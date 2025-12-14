<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Stories V2.1
 * @since Stories 2.0.0
 */
get_header(); ?>

<main id="main" class="site-main" role="main">
    <section class="block">
        <div class="content">
            <h1 class="main-title"><?= esc_html__('Index', 'stories'); ?></h1>
        </div>
    </section>
</main><!-- .site-main -->

<?php get_footer();