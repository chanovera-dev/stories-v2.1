<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package stories
 * @since Stories 2.0.0
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <?php wp_breadcrumbs(); ?>

    <section class="block posts--body">
        <div class="content">
            <?php
            get_template_part('templates/archive/wp', 'loop');

            if (is_active_sidebar('sidebar-1')) {
                echo '
                    <aside class="sidebar posts-body_sidebar">';
                dynamic_sidebar('sidebar-1');
                echo '
                    </aside>';
            }
            ?>
        </div>
    </section>

</main><!-- .site-main -->

<?php get_footer(); ?>