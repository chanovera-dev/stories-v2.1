<?php
/**
 * Single Property Template
 *
 * Template for displaying a single property post (Custom Post Type: 'property').
 * This file includes the full property details such as metadata, gallery, location, and related listings.
 *
 * @package stories
 * @since 1.0.0
 */

get_header(); 

while ( have_posts() ) : the_post();
    $property_data = stories_get_property_data();
    
    extract([
        'price'     => $property_data['price'],
        'operation' => $property_data['operation'],
        'location'  => $property_data['location'],
        'gallery'   => $property_data['gallery'],
    ]);
    
    $location_js = esc_js($location);
    
    // Get all metadata for details section
    $full_metadata = stories_get_full_property_metadata();
?>

<main id="main" class="site-main" role="main">

    <?php wp_breadcrumbs(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="block property--heading">
            <div class="content">
                <div class="property-data--wrapper">
                    <?php the_title( '<h1 class="property-title">', '</h1>' ); ?>
                    <p class="property--operation post-tag small"><?php echo $operation === 'sale' ? 'En venta' : ( $operation === 'rental' ? 'En renta' : '' ); ?></p>
                    <h2 class="property--price">
                        <?php 
                            // Extract numeric price for formatting
                            $price_numeric = preg_replace('/[^\d\.,]/', '', $price);
                            
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
                                echo function_exists('format_price') ? esc_html(format_price($price_numeric)) : esc_html($price);
                            } else {
                                echo esc_html($price);
                            }
                        ?>
                    </h2>

                    <div class="property--metadata">
                        <div class="property--map" id="property-map"></div>

                        <ul class="property---metadata--list">
                            <?php
                                $post_id = get_the_ID();
                                $id = get_post_meta($post_id, 'eb_public_id', true);
                                $construction = get_post_meta($post_id, 'eb_construction_size', true);
                                $lot = get_post_meta($post_id, 'eb_lot_size', true);
                                
                                // Display basic metadata items
                                echo '<li>' . stories_get_icon('id') . 'ID: ' . esc_html($id) . '</li>';
                                echo '<li>' . stories_get_icon('location') . esc_html($location) . '</li>';
                                
                                // Display construction size if available
                                if (!empty($construction) && $construction != 0) {
                                    echo '<li>' . stories_get_icon('construction') . format_numeric($construction) . ' m² de construcción</li>';
                                }
                                
                                // Display lot size if available
                                if (!empty($lot) && $lot != 0) {
                                    echo '<li class="lot">' . stories_get_icon('lot') . format_numeric($lot) . ' m² de terreno</li>';
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="is-layout-constrained">
                    <div class="post-gallery-wrapper">
                        <div class="total-images post-tag glass-backdrop glass-bright"></div>
                        <div class="post-gallery">
                            <?php foreach ( $gallery as $img ) :
                                $img_url = is_array($img) ? $img['url'] : $img; ?>
                                <div class="post-gallery-slide">
                                    <img src="<?php echo esc_url( $img_url ); ?>" alt="" loading="lazy">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="post-gallery-thumbs-container">
                            <button class="btn-pagination" aria-label="Anterior"><?= stories_get_icon('backward'); ?></button>
                            <div class="post-gallery-thumbs"></div>
                            <button class="btn-pagination" aria-label="Siguiente"><?= stories_get_icon('forward'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <section class="block property--details">
            <div class="content">
                <h2 class="title-section"><?php esc_html_e( 'Detalles de la propiedad', 'stories' ); ?></h2>
            </div>
            <div class="content details">
                <div class="property--metadata">
                    <ul class="property--metadata--list">
                        <?php stories_render_full_property_metadata(); ?>
                    </ul>                
                </div>
                <div class="is-layout-constrained">
                    <?php the_content(); ?>
                </div>
            </div>
        </section>
        <?php endwhile; ?>
        <?php
            // --- Extraer ciudad y estado desde $location ---
            $city  = '';
            $state = '';

            if ($location) {
                $parts = array_map('trim', explode(',', $location));

                // Ejemplo: Calle, Ciudad, Estado, País
                if (count($parts) >= 2) {
                    $city  = $parts[count($parts) - 3] ?? '';
                    $state = $parts[count($parts) - 2] ?? '';
                }
            }

            // --- WP_Query de propiedades relacionadas ---
            $args = array(
                'post_type'      => 'property',
                'post_status'    => 'publish',
                'posts_per_page' => 8,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'no_found_rows'  => true,
                'post__not_in'   => array( get_the_ID() ), // excluir la actual
                'meta_query'     => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'eb_location',
                        'value'   => $city,
                        'compare' => 'LIKE',
                    ),
                    array(
                        'key'     => 'eb_location',
                        'value'   => $state,
                        'compare' => 'LIKE',
                    ),
                ),
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) :
            ?>
        <section class="block posts--body container--related-posts">
            <div class="content related-posts--title">
                <h2 class="title-section"><?php echo esc_html_e( 'Propiedades cercanas', 'stories' ); ?></h2>
            </div>
            <div class="content slideshow-wrapper">
                <div class="related-posts--list slideshow">
                    <?php 
                        while ($query->have_posts()) : $query->the_post();
                            get_template_part('template-parts/loop/content', 'property');
                        endwhile;
                    ?>
                </div>
                <div class="navigation">
                    <button id="related-products--backward-button" class="slide-prev btn-pagination small-pagination">
                        <?= stories_get_icon('backward'); ?>
                    </button>
                    <div class="related-bullets"></div>
                    <button id="related-products--forward-button" class="slide-next btn-pagination small-pagination">
                        <?= stories_get_icon('forward'); ?>
                    </button>
                </div>
            </div>
            <?php
                wp_reset_postdata();
                endif;
            ?>
        </section>
    </article>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mapContainer = document.getElementById('property-map');
    const propertyLocation = "<?php echo esc_js($location); ?>";

    if (!propertyLocation) return;

    // Extraer partes (ciudad y estado si están en la dirección)
    const parts = propertyLocation.split(',').map(p => p.trim());
    const city = parts.length >= 2 ? parts[parts.length - 2] : null;
    const state = parts.length >= 1 ? parts[parts.length - 1] : null;

    // Función para mostrar un mapa embebido con coordenadas
    const renderMap = (lat, lon, label = '') => {
        mapContainer.innerHTML = `
            <iframe
                width="100%"
                height="300"
                style="border:0"
                loading="lazy"
                allowfullscreen
                src="https://www.google.com/maps?q=${lat},${lon}&hl=es;z=12&output=embed"
                title="Mapa ${label}">
            </iframe>
        `;
    };

    // Función genérica para buscar con Nominatim
    const fetchCoords = async (query) => {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
        const data = await response.json();
        return (data && data.length > 0) ? { lat: data[0].lat, lon: data[0].lon } : null;
    };

    // Secuencia de búsqueda: dirección → ciudad → estado → México
    (async () => {
        let coords = await fetchCoords(propertyLocation);

        if (!coords && city) {
            console.warn(`Ubicación no encontrada, intentando ciudad: ${city}`);
            coords = await fetchCoords(city);
        }

        if (!coords && state) {
            console.warn(`Ciudad no encontrada, intentando estado: ${state}`);
            coords = await fetchCoords(state);
        }

        if (!coords) {
            console.warn("Usando mapa general de México.");
            coords = { lat: 23.6345, lon: -102.5528 }; // Centro de México
        }

        renderMap(coords.lat, coords.lon, propertyLocation);
    })().catch(err => {
        console.error("Error cargando mapa:", err);
        mapContainer.innerHTML = "<p>Error al cargar el mapa.</p>";
    });
});
</script>
<?php get_footer(); ?>