<?php
/**
 * Synchronizes property data from the EasyBroker API into the local "property" Custom Post Type.
 *
 * This function connects to one or more EasyBroker API keys and retrieves all available property data, 
 * creating or updating local WordPress posts of type "property" to ensure the website database 
 * stays in sync with EasyBroker listings.
 *
 * The process includes:
 * - Pagination handling to fetch all properties from the remote API.
 * - Creation of new posts when the property (identified by `public_id`) does not yet exist.
 * - Updating existing posts with the latest data (title, price, operation type, currency, etc.).
 * - Fetching detailed property information such as gallery images and description content.
 * - Assigning a featured image (if provided) using the `eb_set_featured_image()` helper function.
 * - Storing both formatted and numeric versions of the price for improved filtering and sorting.
 *
 * Admin feedback:
 * - Displays progress messages within the WordPress Admin area when triggered manually.
 * - Logs API and image download errors to the PHP error log for easier troubleshooting.
 *
 * Automatic synchronization:
 * - This function can also be executed by a daily WP-Cron task (`eb_daily_sync`) to keep listings up to date.
 *
 * Notes:
 * - The "property" post type must exist before synchronization.
 * - All remote connections include a 30-second timeout and basic error handling.
 * - For sites with two EasyBroker accounts, both API keys can be defined (EASYBROKER_API_KEY and EASYBROKER_API_KEY2).
 *
 * @stories
 * @since 2.1.0
 * @return void
 */

if (!defined('ABSPATH')) exit;

/**
 * Helper function to safely get a value from an array
 */
function eb_safe_value($array, $key, $default = '') {
    return isset($array[$key]) && !empty($array[$key]) ? $array[$key] : $default;
}

/**
 * Synchronizes property data from the EasyBroker API with the local WordPress database.
 *
 * This function is responsible for fetching the latest property data from the EasyBroker API 
 * and updating (or creating) corresponding WordPress posts to ensure the local database 
 * reflects the most recent information available in EasyBroker.
 *
 * The synchronization process typically involves:
 * - Retrieving all active properties via the EasyBroker API.
 * - Creating or updating custom post types (e.g., "property") for each entry.
 * - Updating post meta fields such as price, location, bedrooms, and bathrooms.
 * - Optionally removing or marking properties that are no longer active on EasyBroker.
 *
 * Parameters:
 * - None (may rely on constants or configuration defined elsewhere, such as the API key).
 *
 * Returns:
 * - void
 *
 * Example usage:
 *   // This can be called manually or via a scheduled cron job.
 *   eb_sync_properties();
 *
 * Notes:
 * - To avoid performance issues, consider limiting the number of API requests per sync.
 * - It’s recommended to log synchronization results for debugging and monitoring.
 */
function eb_sync_properties() {
    // Prepare available API keys safely from the dynamic settings
    $api_keys = function_exists('stories_get_eb_api_keys') ? stories_get_eb_api_keys() : [];
    
    // Fallback just in case function is missing (shouldn't happen)
    if (empty($api_keys)) {
        if (defined('EASYBROKER_API_KEY')) $api_keys[] = EASYBROKER_API_KEY;
        if (defined('EASYBROKER_API_KEY2')) $api_keys[] = EASYBROKER_API_KEY2;
    }

    $total_imported = 0;
    $errors = [];

    foreach ( $api_keys as $api_key ) {
        $page = 1;
        $limit = 50;

        do {
            $url = "https://api.easybroker.com/v1/properties?page=$page&limit=$limit";
            $args = [
                'headers' => ['X-Authorization' => $api_key],
                'timeout' => 30
            ];

            $response = wp_remote_get($url, $args);
            if ( is_wp_error($response) ) {
                error_log('EB: Connection error with EasyBroker API: ' . $response->get_error_message());
                break;
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            $properties = $body['content'] ?? [];
            if ( empty($properties) ) break;

            foreach ( $properties as $p ) {
                $public_id = sanitize_text_field( eb_safe_value($p, 'public_id') );
                $title     = sanitize_text_field( eb_safe_value($p, 'title', 'Untitled') );

                // error_log( print_r( $p, true ) );

                // Check if the property already exists
                $existing = get_posts([
                    'post_type'      => 'property',
                    'meta_key'       => 'eb_public_id',
                    'meta_value'     => $public_id,
                    'posts_per_page' => 1,
                    'fields'         => 'ids'
                ]);

                if ( $existing ) {
                    $post_id = $existing[0];
                } else {
                    $post_id = wp_insert_post([
                        'post_type'   => 'property',
                        'post_title'  => $title,
                        'post_status' => 'publish',
                    ]);
                    update_post_meta($post_id, 'eb_public_id', $public_id);
                }

                // Basic operation data (price, type, currency, etc.)
                $operation       = $p['operations'][0] ?? [];
                $formatted_price = eb_safe_value($operation, 'formatted_amount', 'Not available');
                $operation_type  = eb_safe_value($operation, 'type', '');
                $currency        = eb_safe_value($operation, 'currency', '');

                // Save main meta fields
                update_post_meta($post_id, 'eb_price', $formatted_price);
                update_post_meta($post_id, 'eb_operation', stories_get_normalized_operation($operation_type));
                update_post_meta($post_id, 'eb_currency', $currency);
                update_post_meta($post_id, 'eb_location', eb_safe_value($p, 'location', 'No location'));
                update_post_meta($post_id, 'eb_property_type', stories_get_normalized_property_type(eb_safe_value($p, 'property_type', 'No type')));
                update_post_meta($post_id, 'eb_bedrooms', intval( eb_safe_value($p, 'bedrooms', 0) ));
                update_post_meta($post_id, 'eb_bathrooms', intval( eb_safe_value($p, 'bathrooms', 0) ));
                update_post_meta($post_id, 'eb_parking', intval( eb_safe_value($p, 'parking_spaces', 0) ));
                update_post_meta($post_id, 'eb_lot_size', intval( eb_safe_value($p, 'lot_size', 0) ));
                update_post_meta($post_id, 'eb_construction_size', intval( eb_safe_value($p, 'construction_size', 0) ));
                
                // Save a numeric version of price for queries and filters
                $raw_price = 0;
                if ( !empty($operation['amount']) ) {
                    $raw_price = floatval( preg_replace('/[^\d.]/', '', $operation['amount']) );
                } elseif ( !empty($operation['formatted_amount']) ) {
                    $raw_price = floatval( preg_replace('/[^\d.]/', '', $operation['formatted_amount']) );
                }
                update_post_meta($post_id, 'eb_price_num', $raw_price);

                // Fetch detailed data for gallery and description
                $detail_url = "https://api.easybroker.com/v1/properties/$public_id";
                $detail_res = wp_remote_get($detail_url, $args);
                if ( !is_wp_error($detail_res) ) {
                    $detail_body = json_decode(wp_remote_retrieve_body($detail_res), true);

                    // Property gallery
                    $gallery = [];
                    if ( !empty($detail_body['property_images']) ) {
                        foreach ( $detail_body['property_images'] as $img ) {
                            if ( !empty($img['url']) ) {
                                $gallery[] = esc_url($img['url']);
                            }
                        }
                        update_post_meta($post_id, 'eb_gallery', $gallery);
                    }

                    // Description (post content)
                    $description = $detail_body['description'] ?? '';
                    if ( $description ) {
                        wp_update_post([
                            'ID'           => $post_id,
                            'post_content' => wp_kses_post($description),
                        ]);
                    }

                    // Featured image
                    $title_image = eb_safe_value($p, 'title_image_full', '');
                    if ( $title_image ) {
                        eb_set_featured_image($post_id, $title_image);
                    }
                } else {
                    $errors[] = "Could not fetch details for {$title} ({$public_id})";
                }

                $total_imported++;
            }

            $page++;
        } while ( !empty($body['pagination']['next_page']) );
    }

    // Final message in admin
    if ( !empty($errors) ) {
        echo '<div class="notice notice-error"><p><strong>Errors found:</strong><br>' . implode('<br>', $errors) . '</p></div>';
    }
    
    // Save last sync time
    update_option('eb_last_sync_time', current_time('mysql'));
    
    echo '<div class="updated"><p><strong>Sync completed:</strong> ' . $total_imported . ' properties updated (both keys).</p></div>';
}

/**
 * Sets or updates the featured image (post thumbnail) for a given WordPress post using a remote image URL.
 *
 * This function downloads an image from a specified URL, saves it to the WordPress media library,
 * and assigns it as the featured image for the specified post. If the image already exists locally,
 * it prevents duplicate downloads by reusing the existing attachment when possible.
 *
 * Typical use cases include:
 * - Importing or synchronizing property images from external APIs such as EasyBroker.
 * - Automatically assigning a main image during post creation or update processes.
 *
 * Parameters:
 * - int    $post_id   The ID of the post to which the featured image will be assigned.
 * - string $image_url The full URL of the remote image to download and attach.
 *
 * Returns:
 * - int|false The attachment ID on success, or false on failure.
 *
 * Notes:
 * - This function uses WordPress core media functions such as `media_sideload_image()` and `set_post_thumbnail()`.
 * - Ensure the URL points to a valid image file (JPG, PNG, etc.) accessible from the server.
 * - Proper error handling is recommended to prevent partial imports when the image download fails.
 *
 * Example usage:
 *   eb_set_featured_image(123, 'https://example.com/images/property-01.jpg');
 */
function eb_set_featured_image($post_id, $image_url) {
    if (empty($image_url)) return;

    $image_name = basename(parse_url($image_url, PHP_URL_PATH));
    $existing_thumbnail = get_post_thumbnail_id($post_id);
    if ($existing_thumbnail) return; // Evita duplicar

    $response = wp_remote_get($image_url, ['timeout' => 30]);
    if (is_wp_error($response)) {
        error_log('EB: Error descargando imagen ' . $image_url);
        return;
    }

    $image_data = wp_remote_retrieve_body($response);
    if (empty($image_data)) {
        error_log('EB: Imagen vacía ' . $image_url);
        return;
    }

    $upload = wp_upload_bits($image_name, null, $image_data);
    if ($upload['error']) {
        error_log('EB: Error subiendo imagen ' . $upload['error']);
        return;
    }

    $wp_filetype = wp_check_filetype($upload['file'], null);
    $attachment = [
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name($image_name),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    set_post_thumbnail($post_id, $attach_id);
}

/**
 * Registers a custom admin submenu page for EasyBroker property synchronization.
 *
 * This function adds a "Sync" submenu item under the "Properties" post type menu 
 * (`edit.php?post_type=property`) in the WordPress admin dashboard. 
 * When accessed, it loads the callback function `eb_admin_sync_page`, 
 * which should handle the synchronization logic between EasyBroker and WordPress.
 *
 * Key details:
 * - The submenu is only visible to users with the `manage_options` capability (typically Administrators).
 * - The page slug is `eb-sync`, which can be used for direct URL access or conditional logic.
 * - The page title and menu label are both set to "Sincronizar".
 *
 * Example usage:
 *   - URL in admin: /wp-admin/edit.php?post_type=property&page=eb-sync
 *   - Access limited to users with sufficient permissions.
 *
 * Hook: `admin_menu`
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=property',
        'Sincronizar',
        'Sincronizar',
        'manage_options',
        'eb-sync',
        'eb_admin_sync_page'
    );
});

/**
 * Renders the EasyBroker synchronization admin page and handles manual sync actions.
 *
 * This function defines the contents and behavior of the "Sincronizar Propiedades" 
 * admin page created under the "Properties" post type menu. It provides a simple 
 * user interface that allows administrators to manually trigger the synchronization 
 * process between EasyBroker and the WordPress site.
 *
 * Behavior details:
 * - When the form is submitted (via the "Iniciar sincronización" button),
 *   it calls the `eb_sync_properties()` function to perform the data sync.
 * - Displays an admin notice indicating that synchronization is in progress.
 * - Wraps the entire UI in standard WordPress admin markup for consistent styling.
 *
 * Intended for: Administrators or users with the `manage_options` capability.
 * Hooked by: The submenu page registered via the `admin_menu` action.
 *
 * Related function: `eb_sync_properties()`
 */
function eb_admin_sync_page() {
    if (isset($_POST['eb_sync_now'])) {
        echo '<div class="updated"><p>Sincronizando propiedades...</p></div>';
        eb_sync_properties();
    }

    echo '<div class="wrap"><h1>Sincronizar Propiedades</h1>';
    echo '<form method="post">';
    submit_button('Iniciar sincronización', 'primary', 'eb_sync_now');
    echo '</form></div>';
}

/**
 * Registers and manages the daily automatic synchronization event with EasyBroker.
 *
 * This block ensures that the EasyBroker property synchronization process runs automatically
 * once per day. If the scheduled event ('eb_daily_sync') does not already exist, it registers
 * a new cron event using WordPress's built-in scheduling system.
 *
 * Behavior details:
 * - Checks if the 'eb_daily_sync' event is already scheduled.
 * - If not, schedules it to run daily starting from the current time.
 * - When triggered, the event calls the `eb_sync_properties()` function, 
 *   which handles the synchronization logic between EasyBroker and WordPress.
 *
 * This automation helps keep property data up-to-date without requiring 
 * manual synchronization by the administrator.
 *
 * Related function: `eb_sync_properties()`
 * Hook: `eb_daily_sync`
 */
if (!wp_next_scheduled('eb_daily_sync')) {
    wp_schedule_event(time(), 'daily', 'eb_daily_sync');
}
add_action('eb_daily_sync', 'eb_sync_properties');