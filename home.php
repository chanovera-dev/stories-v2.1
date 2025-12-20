<?php
/**
 * The template for displaying the blog posts index page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#home-page-display
 *
 * @package Stories V2.1
 * @since Stories 2.0.0
 */
get_header(); ?>

<main id="main" class="site-main" role="main">

    <?php
    if (!is_paged()):
        get_template_part('templates/home/hero', 'section');
    else:
        wp_breadcrumbs();
    endif;
    ?>

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