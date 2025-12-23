<?php
/**
 * Property Archive Template
 *
 * Template for displaying the property archive page (Custom Post Type: 'property').
 * This file handles the property listing loop, filters, pagination, and AJAX-based dynamic loading.
 *
 * @package stories
 * @since 1.0.0
 *
 * Template Name: Propiedades */
$locations = get_property_locations();
$price_range = get_property_price_range();
$construction_range = get_property_construction_range();
$land_range = get_property_land_range();
$property_types = get_existing_property_types();
$operation_types = get_existing_operation_types();

set_query_var('locations', $locations);
set_query_var('price_range', $price_range);
set_query_var('construction_range', $construction_range);
set_query_var('land_range', $land_range);
set_query_var('property_types', $property_types);
set_query_var('operation_types', $operation_types);

get_header(); ?>

<main id="main" class="site-main" role="main">

    <?php wp_breadcrumbs(); ?>

    <!-- Archive Posts Section -->
    <section class="block posts--body">
        <div class="content">
            <?php     
                get_template_part('templates/archive-property/properties-list');
                get_template_part('templates/archive-property/filter-properties');
            ?>
        </div>
    </section>

</main><!-- .site-main -->

<?php get_footer(); ?>