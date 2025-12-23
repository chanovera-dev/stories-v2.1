<?php

/****************************************************************************************************************
 * E A S Y B R O K E R
 ****************************************************************************************************************/

/**
 * Fetches property data from the EasyBroker API.
 *
 * This function connects to the EasyBroker REST API and retrieves a list of properties 
 * according to the specified filters. It is primarily used to display property listings 
 * on the website (for example, in sales or rental sections). 
 *
 * Parameters:
 * - $operation_type (string|null): Optional filter for operation type ('sale', 'rent', etc.).
 * - $limit (int): Maximum number of properties to fetch (default: 12).
 *
 * Returns:
 * - array: An associative array containing property data from EasyBroker.
 *           Returns an empty array if the API request fails or if no data is available.
 *
 * Example usage:
 *   $properties = eb_get_properties('sale', 10);
 *   foreach ($properties as $property) {
 *       echo $property['title'];
 *   }
 */
function eb_get_properties($operation_type = null, $limit = 12) {
    $keys = function_exists('stories_get_eb_api_keys') ? stories_get_eb_api_keys() : [];
    $api_key = !empty($keys) ? $keys[0] : (defined('EASYBROKER_API_KEY') ? EASYBROKER_API_KEY : '');
    
    if (empty($api_key)) return [];
    $url = 'https://api.easybroker.com/v1/properties?limit=' . intval($limit);

    // Add operation type filter if specified (e.g., 'sale', 'rent')
    if ($operation_type) {
        $url .= '&operation_type=' . urlencode($operation_type);
    }

    $args = array(
        'headers' => array(
            'X-Authorization' => $api_key
        ),
        'timeout' => 15,
    );

    $response = wp_remote_get($url, $args);

    // Return empty array if API request fails
    if (is_wp_error($response)) return [];

    // Decode JSON response and return property content if available
    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['content'] ?? [];
}

/****************************************************************************************************************
 * P R O P E R T I E S
 ****************************************************************************************************************/

/**
 * Retrieves and caches a list of property locations grouped by state and city.
 *
 * This function collects location data (state, city, and neighborhood) from published
 * property posts, organizes them into a structured array, removes duplicates, and
 * sorts them alphabetically. The result is cached using a transient for performance.
 *
 * @return array An associative array of locations grouped by state.
 */
function get_property_locations() {
    $locations = get_transient('property_locations');

    // Return cached data if available
    if ($locations !== false) {
        return $locations;
    }

    // Get a limited number of published properties
    $properties = get_posts([
        'post_type' => 'property',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    $locations = [];

    if ($properties) {
        foreach ($properties as $prop) {
            $loc = get_post_meta($prop->ID, 'eb_location', true);
            if ($loc) {
                // Split the location string and trim extra spaces
                $parts = array_map('trim', explode(',', $loc));

                // Expected format: [neighborhood, city, state]
                $neighborhood = $parts[0] ?? '';
                $city         = $parts[1] ?? '';
                $state        = $parts[2] ?? '';

                // Group cities by state
                if ($state && $city) {
                    $locations[$state][] = $city;
                }
            }
        }

        // Remove duplicates and sort alphabetically
        foreach ($locations as $state => $cities) {
            $locations[$state] = array_unique($cities);
            sort($locations[$state]);
        }
        ksort($locations);
    }

    // Cache the results for one day
    set_transient('property_locations', $locations, DAY_IN_SECONDS);

    return $locations;
}

// Clear the cached data when a property is saved or updated
add_action('save_post_property', function() {
    delete_transient('property_locations');
    delete_transient('property_price_range');
    delete_transient('property_construction_range');
    delete_transient('property_land_range');
    delete_transient('existing_property_types');
    delete_transient('existing_operation_types');
});

/**
 * Retrieves the unique property types currently used in the database.
 *
 * @return array List of property type keys.
 */
function get_existing_property_types() {
    $types = get_transient('existing_property_types');
    
    if ($types !== false) {
        return $types;
    }

    global $wpdb;
    $results = $wpdb->get_col(
        "SELECT DISTINCT pm.meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_property_type'
        AND p.post_type = 'property'
        AND p.post_status = 'publish'"
    );

    $raw_results = (array)$results;
    $types = [];
    
    foreach ($raw_results as $raw_type) {
        if (!empty($raw_type)) {
            $normalized = stories_get_normalized_property_type($raw_type);
            if (!in_array($normalized, $types)) {
                $types[] = $normalized;
            }
        }
    }
    
    set_transient('existing_property_types', $types, DAY_IN_SECONDS);
    
    return $types;
}

/**
 * Retrieves the unique operation types currently used in the database.
 *
 * @return array List of operation type keys (e.g., 'sale', 'rental').
 */
function get_existing_operation_types() {
    $ops = get_transient('existing_operation_types');
    
    if ($ops !== false) {
        return $ops;
    }

    global $wpdb;
    $results = $wpdb->get_col(
        "SELECT DISTINCT pm.meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_operation'
        AND p.post_type = 'property'
        AND p.post_status = 'publish'"
    );

    $ops = array_filter((array)$results);
    
    set_transient('existing_operation_types', $ops, DAY_IN_SECONDS);
    
    return $ops;
}

/**
 * Retrieves the minimum and maximum price range from all published properties.
 *
 * Caches the result in a transient for performance optimization.
 *
 * @return array An associative array with 'min' and 'max' keys containing price values.
 */
function get_property_price_range() {
    $cached = get_transient('property_price_range');
    
    if ($cached !== false) {
        return $cached;
    }

    global $wpdb;
    
    $result = $wpdb->get_results(
        "SELECT 
            MIN(CAST(meta_value AS UNSIGNED)) as min_price,
            MAX(CAST(meta_value AS UNSIGNED)) as max_price
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_price_num'
        AND p.post_type = 'property'
        AND p.post_status = 'publish'"
    );

    $range = [
        'min' => isset($result[0]->min_price) && $result[0]->min_price ? (int) $result[0]->min_price : 0,
        'max' => isset($result[0]->max_price) && $result[0]->max_price ? (int) $result[0]->max_price : 0,
    ];

    // Cache for 24 hours
    set_transient('property_price_range', $range, DAY_IN_SECONDS);

    return $range;
}

/**
 * Retrieves the minimum and maximum construction size range from all published properties.
 *
 * Caches the result in a transient for performance optimization.
 *
 * @return array An associative array with 'min' and 'max' keys containing construction size values.
 */
function get_property_construction_range() {
    $cached = get_transient('property_construction_range');
    
    if ($cached !== false) {
        return $cached;
    }

    global $wpdb;
    
    $result = $wpdb->get_results(
        "SELECT 
            MIN(CAST(meta_value AS UNSIGNED)) as min_construction,
            MAX(CAST(meta_value AS UNSIGNED)) as max_construction
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_construction_size'
        AND p.post_type = 'property'
        AND p.post_status = 'publish'"
    );

    $range = [
        'min' => isset($result[0]->min_construction) && $result[0]->min_construction ? (int) $result[0]->min_construction : 0,
        'max' => isset($result[0]->max_construction) && $result[0]->max_construction ? (int) $result[0]->max_construction : 0,
    ];

    // Cache for 24 hours
    set_transient('property_construction_range', $range, DAY_IN_SECONDS);

    return $range;
}

/**
 * Retrieves the minimum and maximum land/lot size range from all published properties.
 *
 * Caches the result in a transient for performance optimization.
 *
 * @return array An associative array with 'min' and 'max' keys containing land size values.
 */
function get_property_land_range() {
    $cached = get_transient('property_land_range');
    
    if ($cached !== false) {
        return $cached;
    }

    global $wpdb;
    
    $result = $wpdb->get_results(
        "SELECT 
            MIN(CAST(meta_value AS UNSIGNED)) as min_land,
            MAX(CAST(meta_value AS UNSIGNED)) as max_land
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_lot_size'
        AND p.post_type = 'property'
        AND p.post_status = 'publish'"
    );

    $range = [
        'min' => isset($result[0]->min_land) && $result[0]->min_land ? (int) $result[0]->min_land : 0,
        'max' => isset($result[0]->max_land) && $result[0]->max_land ? (int) $result[0]->max_land : 0,
    ];

    // Cache for 24 hours
    set_transient('property_land_range', $range, DAY_IN_SECONDS);

    return $range;
}

/**
 * Registers the "Property" custom post type (CPT) for real estate listings.
 * 
 * This function is used as a fallback in case the SCF plugin is not available
 * to register the 'property' post type. It sets up basic labels, supports,
 * archive behavior, REST API availability, and the admin menu icon.
 */
// function eb_register_post_type() {
//     register_post_type('property', [
//         'label' => 'Propiedades',
//         'public' => true,
//         'menu_icon' => 'dashicons-admin-home',
//         'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
//         'has_archive' => true,
//         'rewrite' => ['slug' => 'properties'],
//         'show_in_rest' => true,
//     ]);
// }
// add_action('init', 'eb_register_post_type');

/****************************************************************************************************************
 * A J A X   P R O P E R T I E S
 ****************************************************************************************************************/

function enqueue_property_filter_script() {
    wp_enqueue_script('property-filter', get_template_directory_uri() . '/assets/js/ajax-properties.js', ['jquery'], null, true);
    wp_localize_script('property-filter', 'ajaxurlObj', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_property_filter_script');

/**
 * AJAX property filter handler.
 *
 * Handles AJAX requests to filter property listings based on various criteria:
 * operation type, property type, location, bedrooms, bathrooms, price, 
 * construction size, and lot size. Builds a dynamic WP_Query based on 
 * user-submitted filters and returns matching property templates.
 *
 * @since 1.0.0
 * @return void
 */
add_action('wp_ajax_filter_properties', 'ajax_filter_properties');
add_action('wp_ajax_nopriv_filter_properties', 'ajax_filter_properties');

function ajax_filter_properties() {
    global $wpdb;

    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    // Keyword search
    $search_term = !empty($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    // Initialize args
    $args = [
        'post_type'      => 'property',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'order'          => 'DESC',
        'paged'          => $paged,
    ];

    if ($search_term) {
        $args['s'] = $search_term;
        // Filter search to title only
        add_filter('posts_search', function($search, $wp_query) use ($wpdb) {
            if ($term = $wp_query->get('s')) {
                $search = $wpdb->prepare(
                    " AND {$wpdb->posts}.post_title LIKE %s ",
                    '%' . $wpdb->esc_like($term) . '%'
                );
            }
            return $search;
        }, 10, 2);
    }

    // Build meta_query
    $meta_query = ['relation' => 'AND'];

    // Operation type
    if (!empty($_POST['operation'])) {
        $meta_query[] = [
            'key'     => 'eb_operation',
            'value'   => (array) $_POST['operation'],
            'compare' => 'IN',
        ];
    }

    // Property type
    if (!empty($_POST['type'])) {
        $types = (array) $_POST['type'];
        $search_values = $types;

        // Add common raw variants for backward compatibility
        foreach ($types as $type) {
            if ($type === 'house') {
                $search_values[] = 'Casa';
                $search_values[] = 'Casas';
            } elseif ($type === 'apartment') {
                $search_values[] = 'Departamento';
                $search_values[] = 'Departamentos';
                $search_values[] = 'Depto';
            } elseif ($type === 'commercial') {
                $search_values[] = 'Local comercial';
                $search_values[] = 'Local Comercial';
                $search_values[] = 'Comercial';
            } elseif ($type === 'bedroom') {
                $search_values[] = 'Habitación';
                $search_values[] = 'Habitacion';
            } elseif ($type === 'land') {
                $search_values[] = 'Terreno';
                $search_values[] = 'Terrenos';
            } elseif ($type === 'office') {
                $search_values[] = 'Oficina';
            } elseif ($type === 'warehouse') {
                $search_values[] = 'Bodega';
                $search_values[] = 'Bodega industrial';
            } elseif ($type === 'industrial_warehouse') {
                $search_values[] = 'Nave industrial';
                $search_values[] = 'Nave Industrial';
            }
        }

        $meta_query[] = [
            'key'     => 'eb_property_type',
            'value'   => array_unique($search_values),
            'compare' => 'IN',
        ];
    }

    // Location (state / city)
    $location_meta = [];
    if (!empty($_POST['state'])) {
        foreach ((array) $_POST['state'] as $state) {
            $location_meta[] = [
                'key'     => 'eb_location',
                'value'   => sanitize_text_field($state),
                'compare' => 'LIKE',
            ];
        }
    }
    if (!empty($_POST['city'])) {
        foreach ((array) $_POST['city'] as $city) {
            $location_meta[] = [
                'key'     => 'eb_location',
                'value'   => sanitize_text_field($city),
                'compare' => 'LIKE',
            ];
        }
    }
    if (!empty($location_meta)) {
        $meta_query[] = array_merge(['relation' => 'OR'], $location_meta);
    }

    // Bedrooms
    if (!empty($_POST['bedrooms'])) {
        $meta_query[] = [
            'key'     => 'eb_bedrooms',
            'value'   => intval($_POST['bedrooms']),
            'compare' => '=',
            'type'    => 'NUMERIC',
        ];
    }

    // Bathrooms
    if (!empty($_POST['bathrooms'])) {
        $meta_query[] = [
            'key'     => 'eb_bathrooms',
            'value'   => intval($_POST['bathrooms']),
            'compare' => '=',
            'type'    => 'NUMERIC',
        ];
    }

    // Price range
    $price_min = isset($_POST['price_min']) && $_POST['price_min'] !== '' ? floatval($_POST['price_min']) : 0;
    $price_max = isset($_POST['price_max']) && $_POST['price_max'] !== '' ? floatval($_POST['price_max']) : PHP_INT_MAX;
    if ($price_min > 0 || $price_max < PHP_INT_MAX) {
        $meta_query[] = [
            'key'     => 'eb_price_num',
            'value'   => [$price_min, $price_max],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ];
    }

    // Construction size range
    $construction_min = isset($_POST['construction_min']) && $_POST['construction_min'] !== '' ? floatval($_POST['construction_min']) : 0;
    $construction_max = isset($_POST['construction_max']) && $_POST['construction_max'] !== '' ? floatval($_POST['construction_max']) : PHP_INT_MAX;
    if ($construction_min > 0 || $construction_max < PHP_INT_MAX) {
        $meta_query[] = [
            'key'     => 'eb_construction_size',
            'value'   => [$construction_min, $construction_max],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ];
    }

    // Lot size range
    $lot_min = isset($_POST['land_min']) && $_POST['land_min'] !== '' ? floatval($_POST['land_min']) : 0;
    $lot_max = isset($_POST['land_max']) && $_POST['land_max'] !== '' ? floatval($_POST['land_max']) : PHP_INT_MAX;
    if ($lot_min > 0 || $lot_max < PHP_INT_MAX) {
        $meta_query[] = [
            'key'     => 'eb_lot_size',
            'value'   => [$lot_min, $lot_max],
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ];
    }

    // Assign meta_query if not empty
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    // Execute query
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/loop/content', 'property');
        }

        // Pagination
        echo '<nav class="navigation pagination" aria-label="Posts pagination">';
        echo '<h2 class="screen-reader-text">Posts pagination</h2>';
        echo '<div class="nav-links">';
        echo paginate_links([
            'total'   => $query->max_num_pages,
            'current' => $paged,
            'format'  => '?paged=%#%',
            'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/></svg>',
            'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/></svg>',
        ]);
        echo '</div></nav>';
    } else {
        echo '<p>No se encontraron propiedades.</p>';
    }

    wp_reset_postdata();
    wp_die();
}

/****************************************************************************************************************
 * C H E C K B O X   F E A T U R E D   O N  P R O P E R T I E S
 ****************************************************************************************************************/
// Añadir la columna
add_filter('manage_property_posts_columns', function($columns) {
    $columns['featured'] = __('Featured', 'stories');
    return $columns;
});

// Mostrar la columna con toggle AJAX
add_action('manage_property_posts_custom_column', function($column, $post_id) {
    if ($column === 'featured') {
        $is_featured = get_field('featured', $post_id);
        $checked = $is_featured ? 'checked' : '';
        echo '<input type="checkbox" class="acf-featured-toggle" data-id="' . $post_id . '" ' . $checked . ' />';
    }
}, 10, 2);

add_action('admin_footer-edit.php', function() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'property') return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggles = document.querySelectorAll('.acf-featured-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', () => {
                const postId = toggle.dataset.id;
                const value = toggle.checked ? 1 : 0;
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'toggle_featured',
                        post_id: postId,
                        value: value,
                        _ajax_nonce: '<?php echo wp_create_nonce('toggle_featured_nonce'); ?>'
                    })
                }).then(r => r.json()).then(res => {
                    if (!res.success) alert('Error al actualizar');
                });
            });
        });
    });
    </script>
    <style>
        .acf-featured-toggle { transform: scale(1.2); cursor: pointer; }
    </style>
    <?php
});

add_action('wp_ajax_toggle_featured', function() {
    check_ajax_referer('toggle_featured_nonce');
    $post_id = intval($_POST['post_id']);
    $value = intval($_POST['value']);

    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permiso denegado');
    }

    update_field('featured', $value, $post_id); // ACF actualiza el campo
    wp_send_json_success();
});

/****************************************************************************************************************
 * P R O P E R T Y   M E T A D A T A
 ****************************************************************************************************************/
/**
 * Format numeric values with thousand separators
 * 
 * Formats numbers with ' (apostrophe) for thousands and , (comma) for decimals.
 * Example: 1500000 becomes "1'500'000"
 *
 * @param int|float $number The number to format
 * @return string Formatted number
 */
function format_numeric($number) {
    if (empty($number) || !is_numeric($number)) {
        return $number;
    }
    
    // Convert to number and format with ' for thousands and , for decimals
    return number_format((float) $number, 0, ',', ",");
}

/**
 * Format price with currency symbol
 * 
 * Formats numbers with currency symbol and thousand separators.
 * Example: 1500000 becomes "$1'500'000"
 *
 * @param int|float $number The number to format
 * @param string $currency Currency symbol (default: $)
 * @return string Formatted price
 */
function format_price($number, $currency = '$') {
    if (empty($number) || !is_numeric($number)) {
        return $number;
    }
    
    // Convert to number and format with ' for thousands
    $formatted = number_format((float) $number, 0, '.', ",");
    return $currency . $formatted;
}

/**
 * Translate property type from English to Spanish
 * 
 * Translates property types stored in English to Spanish for display
 * 
 * @param string $type The property type in English (house, apartment, land, commercial, office, other)
 * @return string The translated property type in Spanish
 */
function translate_property_type($type) {
    $translations = [
        'house'                => 'Casa',
        'apartment'            => 'Departamento',
        'bedroom'              => 'Habitación',
        'land'                 => 'Terreno',
        'commercial'           => 'Local Comercial',
        'office'               => 'Oficina',
        'warehouse'            => 'Bodega',
        'industrial_warehouse' => 'Nave Industrial',
        'building'             => 'Edificio',
        'house_in_condo'       => 'Casa en Condominio',
        'penthouse'            => 'Penthouse',
        'loft'                 => 'Loft',
        'villa'                => 'Villa',
        'ranch'                => 'Rancho',
        'doctor_office'        => 'Consultorio',
        'lot'                  => 'Lote',
        'house_with_land_use'  => 'Casa con uso de suelo',
        'other'                => 'Otro',
    ];
    
    return $translations[$type] ?? $type;
}

/****************************************************************************************************************
 * M E T A D A T A   F O R   P R O P E R T I E S
 ****************************************************************************************************************/

/**
 * Property Metadata Helpers
 * 
 * Functions for rendering property metadata items with consistent
 * SVG icons and formatting across templates.
 */

/**
 * Render property metadata item
 * 
 * @param string $type Type of metadata (bedroom, bathroom, construction, lot, parking)
 * @param mixed $value The metadata value
 * @param array $args Additional arguments (unit, class, etc.)
 * @return string HTML <li> element
 */
function stories_render_metadata_item($type, $value, $args = []) {
    // Defaults
    $defaults = [
        'unit' => '',
        'class' => '',
        'format' => true, // Whether to apply format_numeric()
    ];
    $args = wp_parse_args($args, $defaults);

    // Validate value
    if (empty($value) || $value == 0) {
        return '';
    }

    // Format value if needed
    if ($args['format'] && function_exists('format_numeric')) {
        $value = format_numeric($value);
    }

    // Get icon
    $icon = stories_get_icon($type);

    // Build class attribute
    $class = "class=\"{$args['class']}\"";

    // Build unit suffix
    $unit = !empty($args['unit']) ? " {$args['unit']}" : '';

    return "<li {$class}>{$icon}{$value}{$unit}</li>";
}

/**
 * Display property metadata with singular/plural support
 * 
 * Renders all available metadata for a property:
 * - Bedrooms
 * - Bathrooms
 * - Construction size
 * - Lot size
 * - Parking spaces
 * - Property type
 * - Property ID
 * 
 * @param int $post_id Post ID (defaults to current post)
 * @param array $args Additional options (show_id, show_type, show_construction_label, show_lot_label, etc.)
 */
function stories_display_property_metadata($post_id = null, $args = []) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Default options
    $defaults = [
        'show_id' => false,
        'show_type' => false,
        'show_construction_label' => false, // Show "m² de construcción" instead of just "m²"
        'show_lot_label' => false,          // Show "m² de terreno" instead of just "m²"
        'show_plural' => false,              // Show "recámara/recámaras", "baño/baños", etc.
    ];
    $args = wp_parse_args($args, $defaults);

    $metadata = [
        'bedrooms' => [
            'key' => 'eb_bedrooms',
            'type' => 'bedroom',
            'class' => 'bedroom',
            'unit' => '',
            'format' => false,
            'singular' => 'recámara',
            'plural' => 'recámaras',
        ],
        'bathrooms' => [
            'key' => 'eb_bathrooms',
            'type' => 'bathroom',
            'class' => '',
            'unit' => '',
            'format' => false,
            'singular' => 'baño',
            'plural' => 'baños',
        ],
        'construction' => [
            'key' => 'eb_construction_size',
            'type' => 'construction',
            'class' => '',
            'unit' => $args['show_construction_label'] ? 'm² de construcción' : 'm²',
            'format' => true,
        ],
        'lot' => [
            'key' => 'eb_lot_size',
            'type' => 'lot',
            'class' => 'lot',
            'unit' => $args['show_lot_label'] ? 'm² de terreno' : 'm²',
            'format' => true,
        ],
        'parking' => [
            'key' => 'eb_parking',
            'type' => 'parking',
            'class' => '',
            'unit' => '',
            'format' => false,
            'singular' => 'estacionamiento',
            'plural' => 'estacionamientos',
        ],
    ];

    $items = [];

    foreach ($metadata as $key => $meta) {
        $value = get_post_meta($post_id, $meta['key'], true);
        
        if (empty($value) || $value == 0) {
            continue;
        }

        // Format value if needed
        $display_value = $value;
        if ($meta['format'] && function_exists('format_numeric')) {
            $display_value = format_numeric($value);
        }

        // Add singular/plural suffix for bedroom, bathroom, parking
        if ($args['show_plural'] && isset($meta['singular'], $meta['plural'])) {
            $unit = ' ' . ($value < 2 ? $meta['singular'] : $meta['plural']);
        } else {
            $unit = !empty($meta['unit']) ? " {$meta['unit']}" : '';
        }

        // Get icon
        $icon = stories_get_icon($meta['type']);

        // Build class attribute
        $class = !empty($meta['class']) ? "class=\"{$meta['class']}\"" : '';

        $items[] = "<li {$class}>{$icon}{$display_value}{$unit}</li>";
    }

    if (empty($items)) {
        return;
    }

    echo '<div class="post--metadata">';
    echo '<ul class="metadata-list">';
    echo implode("\n", $items);
    echo '</ul>';
    echo '</div>';
}

/**
 * Get property metadata variables for single property template
 * 
 * @param int $post_id Property post ID
 * @return array Array with price, operation, location, gallery keys
 */
function stories_get_property_data($post_id = 0) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $gallery = [];

    // 1. Get synced gallery (EasyBroker URLs)
    $eb_gallery = get_post_meta($post_id, 'eb_gallery', true);
    if (!empty($eb_gallery)) {
        if (is_string($eb_gallery)) {
            $gallery = maybe_unserialize($eb_gallery);
        } elseif (is_array($eb_gallery)) {
            $gallery = $eb_gallery;
        }
    }

    // 2. Get manual gallery from ACF (Attachments)
    if (function_exists('get_field')) {
        $acf_gallery = get_field('eb_gallery_manual', $post_id);
        if (!empty($acf_gallery)) {
            foreach ($acf_gallery as $image) {
                // ACF normally returns array of objects/IDs depending on settings
                $image_url = '';
                if (is_numeric($image)) {
                    $image_url = wp_get_attachment_image_url($image, 'full');
                } elseif (is_array($image)) {
                    $image_url = $image['url'] ?? '';
                }

                if ($image_url) {
                    $gallery[] = ['url' => $image_url];
                }
            }
        }
    }

    return [
        'price'     => get_post_meta($post_id, 'eb_price', true),
        'operation' => get_post_meta($post_id, 'eb_operation', true),
        'location'  => get_post_meta($post_id, 'eb_location', true),
        'gallery'   => is_array($gallery) ? array_values(array_unique($gallery, SORT_REGULAR)) : [],
    ];
}

/**
 * Get all detailed metadata for property details section
 * 
 * @param int $post_id Property post ID
 * @return array Array with all metadata items (id, location, type, operation, price, bedrooms, bathrooms, parking, construction, lot)
 */
function stories_get_full_property_metadata($post_id = 0) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    return [
        'id'            => get_post_meta($post_id, 'eb_public_id', true),
        'location'      => get_post_meta($post_id, 'eb_location', true),
        'type'          => get_post_meta($post_id, 'eb_property_type', true),
        'operation'     => get_post_meta($post_id, 'eb_operation', true),
        'price'         => get_post_meta($post_id, 'eb_price', true),
        'bedrooms'      => get_post_meta($post_id, 'eb_bedrooms', true),
        'bathrooms'     => get_post_meta($post_id, 'eb_bathrooms', true),
        'parking'       => get_post_meta($post_id, 'eb_parking', true),
        'construction'  => get_post_meta($post_id, 'eb_construction_size', true),
        'lot'           => get_post_meta($post_id, 'eb_lot_size', true),
    ];
}

/**
 * Render full property metadata list for details section
 * 
 * @param int $post_id Property post ID
 * @return void Outputs HTML list items
 */
function stories_render_full_property_metadata($post_id = 0) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $metadata = stories_get_full_property_metadata($post_id);
    
    // ID
    echo '<li>';
    echo '<span>' . stories_get_icon('id') . '</span> ';
    echo 'ID: ' . esc_html($metadata['id']);
    echo '</li>';
    
    // Location
    echo '<li>';
    echo '<span>' . stories_get_icon('location') . '</span> ';
    echo esc_html($metadata['location']);
    echo '</li>';
    
    // Type
    if (!empty($metadata['type'])) {
        echo '<li>';
        echo '<span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.5391 8.67606V15.5524C20.5512 15.8014 20.4327 16.0559 20.1845 16.196L13.0531 20.2197C12.4152 20.5797 11.6357 20.5807 10.9969 20.2223L3.82016 16.1968C3.5659 16.0542 3.44711 15.7917 3.46487 15.5374V8.69449C3.44687 8.44374 3.56156 8.18452 3.80996 8.0397L10.9664 3.86752C11.6207 3.48606 12.4299 3.4871 13.0832 3.87025L20.1945 8.04063C20.4357 8.18211 20.5503 8.43167 20.5391 8.67606Z" stroke="currentColor"/><path d="M3.82019 9.25312C3.3487 8.98865 3.34307 8.31197 3.81009 8.03969L10.9665 3.86751C11.6209 3.48605 12.43 3.48709 13.0834 3.87024L20.1946 8.04062C20.6596 8.31329 20.6539 8.98739 20.1845 9.25227L13.0531 13.276C12.4152 13.636 11.6357 13.637 10.9969 13.2786L3.82019 9.25312Z" stroke="currentColor"/></svg></span> ';
        $type_translated = function_exists('translate_property_type') ? translate_property_type($metadata['type']) : $metadata['type'];
        echo 'Tipo: ' . esc_html($type_translated);
        echo '</li>';
    }
    
    // Operation
    if (!empty($metadata['operation'])) {
        echo '<li>';
        echo '<span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16"><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg></span> ';
        echo ($metadata['operation'] === 'sale' ? 'En venta' : ($metadata['operation'] === 'rental' ? 'En renta' : esc_html($metadata['operation'])));
        echo '</li>';
    }
    
    // Price
    if (!empty($metadata['price'])) {
        echo '<li>';
        echo '<span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16"><path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518z"/><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11m0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12"/></svg></span> ';
        
        // Extract numeric price for formatting
        $price_numeric = preg_replace('/[^\d\.,]/', '', $metadata['price']);
        
        // Handle european format (1.234.567,89) or US format (1,234,567.89)
        if (strpos($price_numeric, ',') !== false && strpos($price_numeric, '.') !== false) {
            // If contains both, assume european: remove dots, replace comma with dot
            $price_numeric = str_replace('.', '', $price_numeric);
            $price_numeric = str_replace(',', '.', $price_numeric);
        } else {
            // Remove commas used as thousands separators
            $price_numeric = str_replace(',', '', $price_numeric);
        }
        
        $price_numeric = preg_replace('/[^\d\.]/', '', $price_numeric);
        
        if (!empty($price_numeric)) {
            echo 'Precio: ' . esc_html(function_exists('format_price') ? format_price($price_numeric) : $metadata['price']);
        } else {
            echo 'Precio: ' . esc_html($metadata['price']);
        }
        
        echo '</li>';
    }
    
    // Bedrooms
    if (!empty($metadata['bedrooms']) && $metadata['bedrooms'] != 0) {
        echo '<li class="bedroom">';
        echo '<span>' . stories_get_icon('bedroom') . '</span> ';
        echo esc_html($metadata['bedrooms']) . ' ' . ($metadata['bedrooms'] < 2 ? 'recámara' : 'recámaras');
        echo '</li>';
    }
    
    // Bathrooms
    if (!empty($metadata['bathrooms']) && $metadata['bathrooms'] != 0) {
        echo '<li>';
        echo '<span>' . stories_get_icon('bathroom') . '</span> ';
        echo esc_html($metadata['bathrooms']) . ' ' . ($metadata['bathrooms'] < 2 ? 'baño' : 'baños');
        echo '</li>';
    }
    
    // Parking
    if (!empty($metadata['parking']) && $metadata['parking'] != 0) {
        echo '<li class="parking">';
        echo '<span>' . stories_get_icon('parking') . '</span> ';
        echo esc_html($metadata['parking']) . ' ' . ($metadata['parking'] < 2 ? 'estacionamiento' : 'estacionamientos');
        echo '</li>';
    }
    
    // Construction size
    if (!empty($metadata['construction']) && $metadata['construction'] != 0) {
        echo '<li>';
        echo '<span>' . stories_get_icon('construction') . '</span> ';
        echo format_numeric($metadata['construction']) . ' m² de construcción';
        echo '</li>';
    }
    
    // Lot size
    if (!empty($metadata['lot']) && $metadata['lot'] != 0) {
        echo '<li class="lot">';
        echo '<span>' . stories_get_icon('lot') . '</span> ';
        echo format_numeric($metadata['lot']) . ' m² de terreno';
        echo '</li>';
    }
}

/****************************************************************************************************************
 * A C F   F I E L D S   F O R   P R O P E R T I E S
 ****************************************************************************************************************/

/**
 * ACF Fields Registration
 * 
 * Registers custom fields for the Property CPT using ACF (Advanced Custom Fields)
 * Allows manual property creation and editing without depending on EasyBroker sync
 * 
 * @package stories-V2
 * @since 1.0.0
 */

if (!function_exists('acf_add_local_field_group')) {
    return;
}

/**
 * Register ACF fields for Property CPT
 */
function stories_register_property_acf_fields() {
    acf_add_local_field_group([
        'key'                   => 'group_property_details',
        'title'                 => 'Detalles de la Propiedad',
        'fields'                => [
            [
                'key'           => 'field_property_id',
                'label'         => 'ID Público',
                'name'          => 'eb_public_id',
                'type'          => 'text',
                'instructions'  => 'Identificador único de la propiedad',
                'required'      => 0,
                'placeholder'   => 'P-12345',
            ],
            [
                'key'           => 'field_property_price',
                'label'         => 'Precio',
                'name'          => 'eb_price',
                'type'          => 'text',
                'instructions'  => 'Formato: $1,500,000 o 1,500,000',
                'required'      => 1,
                'placeholder'   => '$1,500,000',
            ],
            [
                'key'           => 'field_property_operation',
                'label'         => 'Tipo de Operación',
                'name'          => 'eb_operation',
                'type'          => 'select',
                'choices'       => [
                    'sale'      => 'En Venta',
                    'rental'    => 'En Renta',
                ],
                'return_format' => 'value',
                'required'      => 1,
            ],
            [
                'key'           => 'field_property_location',
                'label'         => 'Ubicación',
                'name'          => 'eb_location',
                'type'          => 'text',
                'instructions'  => 'Dirección completa de la propiedad',
                'required'      => 1,
                'placeholder'   => 'Calle Principal 123, Ciudad, Estado',
            ],
            [
                'key'           => 'field_property_type',
                'label'         => 'Tipo de Propiedad',
                'name'          => 'eb_property_type',
                'type'          => 'select',
                'choices'       => [
                    'house'                => 'Casa',
                    'apartment'            => 'Departamento',
                    'house_in_condo'       => 'Casa en Condominio',
                    'bedroom'              => 'Habitación',
                    'land'                 => 'Terreno',
                    'lot'                  => 'Lote',
                    'commercial'           => 'Local Comercial',
                    'office'               => 'Oficina',
                    'doctor_office'        => 'Consultorio',
                    'warehouse'            => 'Bodega',
                    'industrial_warehouse' => 'Nave Industrial',
                    'building'             => 'Edificio',
                    'penthouse'            => 'Penthouse',
                    'loft'                 => 'Loft',
                    'villa'                => 'Villa',
                    'ranch'                => 'Rancho',
                    'other'                => 'Otro',
                ],
                'return_format' => 'value',
                'required'      => 1,
            ],
            [
                'key'           => 'field_property_bedrooms',
                'label'         => 'Recámaras',
                'name'          => 'eb_bedrooms',
                'type'          => 'number',
                'required'      => 0,
                'min'           => 0,
                'placeholder'   => '3',
            ],
            [
                'key'           => 'field_property_bathrooms',
                'label'         => 'Baños',
                'name'          => 'eb_bathrooms',
                'type'          => 'number',
                'required'      => 0,
                'min'           => 0,
                'placeholder'   => '2',
            ],
            [
                'key'           => 'field_property_parking',
                'label'         => 'Estacionamientos',
                'name'          => 'eb_parking',
                'type'          => 'number',
                'required'      => 0,
                'min'           => 0,
                'placeholder'   => '2',
            ],
            [
                'key'           => 'field_property_construction_size',
                'label'         => 'Tamaño de Construcción (m²)',
                'name'          => 'eb_construction_size',
                'type'          => 'number',
                'required'      => 0,
                'min'           => 0,
                'placeholder'   => '150',
            ],
            [
                'key'           => 'field_property_lot_size',
                'label'         => 'Tamaño de Terreno (m²)',
                'name'          => 'eb_lot_size',
                'type'          => 'number',
                'required'      => 0,
                'min'           => 0,
                'placeholder'   => '250',
            ],
            [
                'key'           => 'field_property_gallery',
                'label'         => 'Galería de Imágenes (Manual)',
                'name'          => 'eb_gallery_manual',
                'type'          => 'gallery',
                'instructions'  => 'Agrega imágenes adicionales a la galería. Estas se sumarán a las ya sincronizadas de EasyBroker.',
                'return_format' => 'array',
            ],
        ],
        'location'              => [
            [
                [
                    'param'     => 'post_type',
                    'operator'  => '==',
                    'value'     => 'property',
                ],
            ],
        ],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => [],
    ]);
}

add_action('acf/init', 'stories_register_property_acf_fields');

/**
 * Convert ACF gallery array to EasyBroker format
 * 
 * Converts ACF's gallery field format to the serialized format used by EasyBroker
 * This ensures compatibility with existing template code and prevents duplicate gallery handling
 * 
 * Handles both:
 * - ACF gallery field (array of image IDs)
 * - ACF image objects (array with id, alt, title, etc.)
 * - Already processed eb_gallery (array of urls)
 * 
 * @param int $post_id Post ID
 */
function stories_convert_acf_gallery_to_eb_format($post_id) {
    // This function is now mostly redundant if we use get_field directly,
    // but we'll keep it to ensure manual images are also indexed if needed.
    // However, we MUST NOT delete 'eb_gallery' if the field is empty, 
    // because 'eb_gallery' is where EasyBroker stores its synced URLs.
    
    $gallery = get_field('eb_gallery_manual', $post_id);
    
    // If we have manual images, we could potentially sync them back to eb_gallery, 
    // but it's better to keep them separate to avoid data loss during the next sync.
    // The stories_get_property_data() function handles the combination.
}

add_action('acf/save_post', 'stories_convert_acf_gallery_to_eb_format', 20);

/**
 * Normalizes property type to a standard set of keys (English)
 * 
 * @param string $type The property type to normalize
 * @return string Normalized type key
 */
function stories_get_normalized_property_type($type) {
    if (empty($type)) return 'other';

    // Work with lowercase for easier matching
    $type_lower = strtolower(trim($type));

    $type_map = [
        'casa'                  => 'house',
        'casas'                 => 'house',
        'house'                 => 'house',
        'departamento'          => 'apartment',
        'departamentos'         => 'apartment',
        'apartment'             => 'apartment',
        'depto'                 => 'apartment',
        'casa en condominio'    => 'house_in_condo',
        'house_in_condo'        => 'house_in_condo',
        'habitación'            => 'bedroom',
        'habitacion'            => 'bedroom',
        'cuarto'                => 'bedroom',
        'casa con uso de suelo' => 'house_with_land_use',
        'bedroom'               => 'bedroom',
        'terreno'               => 'land',
        'terrenos'              => 'land',
        'land'                  => 'land',
        'lote'                  => 'lot',
        'lot'                   => 'lot',
        'comercial'             => 'commercial',
        'commercial'            => 'commercial',
        'local comercial'       => 'commercial',
        'local'                 => 'commercial',
        'oficina'               => 'office',
        'office'                => 'office',
        'consultorio'           => 'doctor_office',
        'doctor_office'         => 'doctor_office',
        'bodega'                => 'warehouse',
        'bodega industrial'     => 'warehouse',
        'warehouse'             => 'warehouse',
        'nave industrial'       => 'industrial_warehouse',
        'nave'                  => 'industrial_warehouse',
        'industrial_warehouse'  => 'industrial_warehouse',
        'edificio'              => 'building',
        'building'              => 'building',
        'penthouse'             => 'penthouse',
        'ph'                    => 'penthouse',
        'loft'                  => 'loft',
        'villa'                 => 'villa',
        'rancho'                => 'ranch',
        'quinta'                => 'ranch',
        'ranch'                 => 'ranch',
        'otro'                  => 'other',
        'other'                 => 'other',
    ];

    return $type_map[$type_lower] ?? $type_lower;
}

/**
 * Normalizes operation type to a standard set of keys (sale, rental)
 * 
 * @param string $operation The operation type to normalize
 * @return string Normalized operation key
 */
function stories_get_normalized_operation($operation) {
    if (empty($operation)) return '';

    $operation_map = [
        'En Venta' => 'sale',
        'sale'     => 'sale',
        'Sale'     => 'sale',
        'En Renta' => 'rental',
        'rental'   => 'rental',
        'Rental'   => 'rental',
        'rent'      => 'rental',
        'Rent'      => 'rental',
    ];

    return $operation_map[$operation] ?? strtolower($operation);
}

/**
 * Fix property type and operation values before ACF saves
 * 
 * ACF may save the label instead of the value for select fields,
 * so we need to intercept and fix this on the save_post hook
 * 
 * @param int $post_id Post ID
 */
function stories_fix_property_select_values($post_id) {
    // Only for property post type
    if (get_post_type($post_id) !== 'property') {
        return;
    }

    // Avoid autosaves and revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    // 1. Normalize eb_property_type
    $property_type = get_post_meta($post_id, 'eb_property_type', true);
    if (!empty($property_type)) {
        $normalized_type = stories_get_normalized_property_type($property_type);
        if ($normalized_type !== $property_type) {
            update_post_meta($post_id, 'eb_property_type', $normalized_type);
        }
    }

    // 2. Normalize eb_operation
    $operation = get_post_meta($post_id, 'eb_operation', true);
    if (!empty($operation)) {
        $normalized_op = stories_get_normalized_operation($operation);
        if ($normalized_op !== $operation) {
            update_post_meta($post_id, 'eb_operation', $normalized_op);
        }
    }
}

// Hook right after ACF saves (priority 15 to run before stories_process_manual_property_save)
add_action('acf/save_post', 'stories_fix_property_select_values', 15);

/**
 * Process property metadata when saved manually via ACF
 * 
 * Ensures that:
 * 1. eb_price_num is created from eb_price for filtering
 * 2. Gallery format is compatible with template display
 * 3. Caches are cleared
 * 
 * @param int $post_id Post ID
 */
function stories_process_manual_property_save($post_id) {
    // Only for property post type
    if (get_post_type($post_id) !== 'property') {
        return;
    }

    // Avoid autosaves and revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    // 0. Auto-generate ID if empty (for local properties)
    $existing_id = get_post_meta($post_id, 'eb_public_id', true);
    if (empty($existing_id)) {
        $new_id = 'LOC-' . $post_id;
        update_post_meta($post_id, 'eb_public_id', $new_id);
    }

    // 1. Convert and save numeric price (eb_price_num) for filtering
    $price_raw = get_post_meta($post_id, 'eb_price', true);
    if (!empty($price_raw)) {
        // Extract only digits and decimals from price string
        $price_normalized = preg_replace('/[^\d\.,]/', '', $price_raw);
        
        // Handle european format (1.234.567,89) or US format (1,234,567.89)
        if (strpos($price_normalized, ',') !== false && strpos($price_normalized, '.') !== false) {
            // If contains both, assume european: remove dots, replace comma with dot
            $price_normalized = str_replace('.', '', $price_normalized);
            $price_normalized = str_replace(',', '.', $price_normalized);
        } else {
            // Remove commas used as thousands separators
            $price_normalized = str_replace(',', '', $price_normalized);
        }
        
        // Final cleanup: allow only digits and dot
        $price_normalized = preg_replace('/[^\d\.]/', '', $price_normalized);

        if (!empty($price_normalized)) {
            // Determine integer vs float
            $price_value = (strpos($price_normalized, '.') !== false) ? floatval($price_normalized) : intval($price_normalized);
            update_post_meta($post_id, 'eb_price_num', $price_value);
        }
    }

    // 2. Clear transient caches when property is updated
    delete_transient('property_locations');
    delete_transient('property_price_range');
    delete_transient('property_construction_range');
    delete_transient('property_land_range');
}

// Hook at priority 25 (after ACF saves the fields)
add_action('acf/save_post', 'stories_process_manual_property_save', 25);

// Also hook into save_post to catch any direct saves
add_action('save_post_property', function() {
    delete_transient('property_locations');
    delete_transient('property_price_range');
    delete_transient('property_construction_range');
    delete_transient('property_land_range');
});

/****************************************************************************************************************
 * P R O P E R T I E S   D A S H B O A R D
 ****************************************************************************************************************/

/**
 * Property Dashboard for WordPress Admin
 *
 * Provides a high-level overview of property listings, synchronization status,
 * and key metrics directly in the WordPress dashboard.
 *
 * @package Stories
 * @subpackage Admin
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Helper to get all registered EasyBroker API keys.
 *
 * @return array
 */
function stories_get_eb_api_keys() {
    $keys = get_option('stories_eb_api_keys');
    
    // If the new dynamic option doesn't exist yet, return empty array
    if ($keys === false) {
        return [];
    }

    return is_array($keys) ? array_filter($keys, 'strlen') : [];
}

/**
 * Register Property-related sub-menu pages.
 */
function stories_register_property_admin_pages() {
    // Dashboard
    add_submenu_page(
        'edit.php?post_type=property',
        __('Dashboard de Propiedades', 'stories'),
        __('Dashboard', 'stories'),
        'manage_options',
        'property-dashboard',
        'stories_property_dashboard_render'
    );

    // Configuration
    add_submenu_page(
        'edit.php?post_type=property',
        __('Configuración de EasyBroker', 'stories'),
        __('Configuración', 'stories'),
        'manage_options',
        'property-settings',
        'stories_property_settings_render'
    );
}
add_action('admin_menu', 'stories_register_property_admin_pages');

/**
 * Enqueue styles for the Property Dashboard and Settings.
 */
function stories_property_dashboard_assets($hook) {
    if (!in_array($hook, ['property_page_property-dashboard', 'property_page_property-settings'])) {
        return;
    }

    wp_enqueue_style('stories-admin-dashboard', get_template_directory_uri() . '/assets/css/admin-dashboard.css', [], STORIES_VERSION);
}
// Remove any previous versions of this hook to avoid duplicates
remove_action('admin_enqueue_scripts', 'stories_property_dashboard_assets');
add_action('admin_enqueue_scripts', 'stories_property_dashboard_assets');

/**
 * Migration: Move API keys from individual options/theme_mods to the new dynamic array format.
 */
add_action('admin_init', function() {
    // Check if migration has already been performed
    if (get_option('stories_eb_keys_migrated')) {
        return;
    }

    // Migrate existing options if they contain non-default values
    $k1 = get_option('stories_eb_api_key');
    $k2 = get_option('stories_eb_api_key_2');
    
    $initial_keys = [];
    if (!empty($k1)) $initial_keys[] = $k1;
    if (!empty($k2)) $initial_keys[] = $k2;

    if (!empty($initial_keys)) {
        update_option('stories_eb_api_keys', array_values(array_unique($initial_keys)));
    }
    
    // Mark as migrated
    update_option('stories_eb_keys_migrated', true);
});

/**
 * Render the Property Settings page.
 */
function stories_property_settings_render() {
    // Handle form submission
    if (isset($_POST['stories_save_settings']) && check_admin_referer('stories_eb_settings_nonce')) {
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos suficientes para realizar esta acción.', 'stories'));
        }

        $submitted_keys = isset($_POST['eb_api_keys']) ? (array) $_POST['eb_api_keys'] : [];
        
        // Trim each key first, then filter out empty ones, then ensure uniqueness
        $trimmed_keys = array_map('trim', $submitted_keys);
        $cleaned_keys = array_values(array_unique(array_filter($trimmed_keys, 'strlen')));
        
        update_option('stories_eb_api_keys', $cleaned_keys);
        
        // Ensure migration flag is set so migration doesn't overwrite new data
        update_option('stories_eb_keys_migrated', true);
        
        // Update individual options for backward compatibility with constants/older functions
        // We sync the first two for legacy support
        update_option('stories_eb_api_key', $cleaned_keys[0] ?? '');
        update_option('stories_eb_api_key_2', $cleaned_keys[1] ?? '');
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Configuración guardada correctamente.', 'stories') . '</p></div>';
    }

    $keys = stories_get_eb_api_keys();
    // Ensure at least one empty field if no keys exist
    if (empty($keys)) $keys = [''];
    ?>
    <div class="wrap stories-admin-wrap">
        <h1 class="wp-heading-inline"><?php _e('Configuración de EasyBroker', 'stories'); ?></h1>
        <hr class="wp-header-end">

        <div class="stories-dashboard-grid secondary">
            <div class="stories-card full-height">
                <h3><?php _e('API Keys de EasyBroker', 'stories'); ?></h3>
                <p><?php _e('Gestiona tus claves API para la sincronización de propiedades. Puedes agregar múltiples cuentas si lo necesitas.', 'stories'); ?></p>
                
                <form method="post" action="">
                    <?php wp_nonce_field('stories_eb_settings_nonce'); ?>
                    
                    <div id="eb-keys-container">
                        <?php foreach ($keys as $index => $key_value): ?>
                            <div class="eb-key-row" style="display: flex; gap: 10px; align-items: center; margin-bottom: 15px;">
                                <input type="text" name="eb_api_keys[]" value="<?php echo esc_attr($key_value); ?>" class="regular-text" style="flex: 1; max-width: 500px;" placeholder="<?php esc_attr_e('Ingrese su API Key', 'stories'); ?>">
                                <button type="button" class="button remove-key" style="color: #d63638; border-color: #d63638;"><?php _e('Eliminar', 'stories'); ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" id="add-eb-key" class="button button-secondary" style="margin-bottom: 30px; margin-top: 10px;">
                        <span class="dashicons dashicons-plus" style="margin-top: 4px;"></span> <?php _e('Agregar otra clave API', 'stories'); ?>
                    </button>

                    <?php submit_button(__('Guardar Configuración', 'stories'), 'primary', 'stories_save_settings'); ?>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('eb-keys-container');
        const addButton = document.getElementById('add-eb-key');

        // Add new key row
        addButton.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'eb-key-row';
            newRow.style.cssText = 'display: flex; gap: 10px; align-items: center; margin-bottom: 15px;';
            newRow.innerHTML = `
                <input type="text" name="eb_api_keys[]" value="" class="regular-text" style="flex: 1; max-width: 500px;" placeholder="Nueva API Key">
                <button type="button" class="button remove-key" style="color: #d63638; border-color: #d63638;">Eliminar</button>
            `;
            container.appendChild(newRow);
        });

        // Remove key row (delegate event)
        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-key')) {
                const rows = container.querySelectorAll('.eb-key-row');
                if (rows.length > 1) {
                    e.target.closest('.eb-key-row').remove();
                } else {
                    e.target.closest('.eb-key-row').querySelector('input').value = '';
                }
            }
        });
    });
    </script>
    <?php
}

/**
 * Calculate property statistics.
 */
function stories_get_property_stats() {
    $counts = wp_count_posts('property');
    $stats = [
        'total' => $counts->publish + $counts->draft + $counts->private,
        'published' => $counts->publish,
        'draft' => $counts->draft,
        'operations' => [
            'sale' => 0,
            'rental' => 0
        ],
        'types' => [],
        'locations' => []
    ];

    // Get all properties to calculate breakdown (Optimized for internal use)
    global $wpdb;
    
    // Operation stats
    $ops = $wpdb->get_results("
        SELECT pm.meta_value, COUNT(*) as count 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_operation' 
        AND p.post_type = 'property' 
        AND p.post_status IN ('publish', 'draft', 'private')
        GROUP BY pm.meta_value
    ");
    foreach ($ops as $op) {
        if ($op->meta_value === 'sale') $stats['operations']['sale'] = $op->count;
        if ($op->meta_value === 'rental') $stats['operations']['rental'] = $op->count;
    }

    // Type stats - Normalizing on the fly to avoid duplicates and including all statuses
    $raw_types = $wpdb->get_results("
        SELECT pm.meta_value, COUNT(*) as count 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_property_type' 
        AND pm.meta_value != ''
        AND p.post_type = 'property' 
        AND p.post_status IN ('publish', 'draft', 'private')
        GROUP BY pm.meta_value
    ");

    $normalized_counts = [];
    foreach ($raw_types as $rt) {
        $normalized = function_exists('stories_get_normalized_property_type') ? stories_get_normalized_property_type($rt->meta_value) : $rt->meta_value;
        if (!isset($normalized_counts[$normalized])) $normalized_counts[$normalized] = 0;
        $normalized_counts[$normalized] += intval($rt->count);
    }
    arsort($normalized_counts);
    $stats['types'] = array_filter($normalized_counts); // Ensure no zero counts (though SQL handles it)

    // Location stats - Including all statuses
    $locs = $wpdb->get_col("
        SELECT pm.meta_value 
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
        WHERE pm.meta_key = 'eb_location'
        AND pm.meta_value != ''
        AND p.post_type = 'property' 
        AND p.post_status IN ('publish', 'draft', 'private')
    ");
    $cities = [];
    foreach ($locs as $loc) {
        $parts = array_map('trim', explode(',', $loc));
        $city = $parts[1] ?? 'Desconocido';
        if (!isset($cities[$city])) $cities[$city] = 0;
        $cities[$city]++;
    }
    arsort($cities);
    $stats['locations'] = array_slice($cities, 0, 5);

    return $stats;
}

/**
 * Render the Property Dashboard page.
 */
function stories_property_dashboard_render() {
    $stats = stories_get_property_stats();
    $last_sync = get_option('eb_last_sync_time', __('Nunca', 'stories'));
    ?>
    <div class="wrap stories-admin-wrap">
        <h1 class="wp-heading-inline"><?php _e('Dashboard de Propiedades', 'stories'); ?></h1>
        <hr class="wp-header-end">

        <div class="stories-dashboard-grid">
            
            <!-- Summary Cards -->
            <div class="stories-card summary-card">
                <div class="card-icon dashicons dashicons-admin-home"></div>
                <div class="card-content">
                    <h3><?php _e('Total Propiedades', 'stories'); ?></h3>
                    <span class="card-value"><?php echo esc_html($stats['total']); ?></span>
                    <p class="card-subtitle"><?php printf(__('%d publicadas, %d borradores', 'stories'), $stats['published'], $stats['draft']); ?></p>
                </div>
            </div>

            <div class="stories-card operation-card">
                <div class="card-icon dashicons dashicons-tag"></div>
                <div class="card-content">
                    <h3><?php _e('En Venta', 'stories'); ?></h3>
                    <span class="card-value sale"><?php echo esc_html($stats['operations']['sale']); ?></span>
                </div>
            </div>

            <div class="stories-card operation-card">
                <div class="card-icon dashicons dashicons-cart"></div>
                <div class="card-content">
                    <h3><?php _e('En Renta', 'stories'); ?></h3>
                    <span class="card-value rent"><?php echo esc_html($stats['operations']['rental']); ?></span>
                </div>
            </div>

            <div class="stories-card sync-card">
                <div class="card-icon dashicons dashicons-update"></div>
                <div class="card-content">
                    <h3><?php _e('Sincronización', 'stories'); ?></h3>
                    <span class="card-value"><?php echo esc_html($last_sync); ?></span>
                    <a href="<?php echo admin_url('edit.php?post_type=property&page=eb-sync'); ?>" class="button button-small"><?php _e('Sincronizar ahora', 'stories'); ?></a>
                </div>
            </div>

        </div>

        <div class="stories-dashboard-grid secondary">
            
            <!-- Types Table -->
            <div class="stories-card full-height">
                <h3><?php _e('Propiedades por Tipo', 'stories'); ?></h3>
                <ul class="stats-list">
                    <?php foreach ($stats['types'] as $type => $count): ?>
                        <li>
                            <?php 
                                $label = function_exists('translate_property_type') ? translate_property_type($type) : $type;
                            ?>
                            <span class="label"><?php echo esc_html(ucfirst($label)); ?></span>
                            <div class="bar-wrapper">
                                <div class="bar" style="width: <?php echo ($stats['total'] > 0) ? ($count / $stats['total'] * 100) : 0; ?>%"></div>
                            </div>
                            <span class="count"><?php echo esc_html($count); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Locations Table -->
            <div class="stories-card full-height">
                <h3><?php _e('Principales Ciudades', 'stories'); ?></h3>
                <ul class="stats-list dots">
                    <?php foreach ($stats['locations'] as $city => $count): ?>
                        <li>
                            <span class="label"><?php echo esc_html($city); ?></span>
                            <span class="count"><?php echo esc_html($count); ?> <?php _e('propiedades', 'stories'); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    </div>
    <?php
}