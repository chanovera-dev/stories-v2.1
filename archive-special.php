<?php
/**
 * Archive 'Special' Template
 * 
 * Template name: Archivo de 'special'
 *
 * This template displays archive-type pages, including categories, tags, authors,
 * and custom post type archives. It includes a header with the archive title,
 * a loop that lists posts using 'content-archive' template parts, and pagination.
 * If no posts are found, a friendly message is displayed.
 *
 * @package stories
 * @since 2.1.0
 */

get_header(); ?>

<main id="main" class="site-main" role="main">

    <?php wp_breadcrumbs(); ?>

    <!-- Archive Posts Section -->
    <section class="block posts--body">
        <div class="content">
            <div class="loop">
                <?php
                $args = array(
                    'post_type' => 'special',
                    'post_status' => 'publish',
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                );
                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $post_format = get_post_format();
                        $part = 'archive';

                        if ($post_format) {
                            if (locate_template("template-parts/loop/content-{$post_format}.php")) {
                                $part = $post_format;
                            }
                        }

                        get_template_part('template-parts/loop/content', $part);
                    }

                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => stories_get_icon('backward') . ' Anterior',
                        'next_text' => 'Siguiente' . stories_get_icon('forward')
                    ));
                } else {
                    echo '<p>' . esc_html__('No se encontraron artículos', 'stories') . '</p>';
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

</main><!-- .site-main -->

<?php get_footer(); ?>