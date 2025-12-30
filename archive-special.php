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
                        'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"></path></svg>Anterior',
                        'next_text' => 'Siguiente<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"></path></svg>'
                    ));
                } else {
                    echo '<p>' . esc_html__('No se encontraron artítulos', 'stories') . '</p>';
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

</main><!-- .site-main -->

<?php get_footer(); ?>