<?php
/**
 * Single Post Template
 *
 * This template is used to display individual blog posts.
 * It loads the header, runs the main WordPress loop to display
 * the post content using the 'template-parts/content-single.php' template part,
 * and then loads the footer.
 *
 * Typically used for standard blog posts, news articles, or custom post types
 * that do not have a dedicated template.
 *
 * @package Stories V2.1
 * @since Stories 2.0.0
 */
get_header();

if (have_posts()) {

    while (have_posts()) {
        the_post();

        $post_format = get_post_format();
        $suffix = 'single';

        if ($post_format) {
            $maybe = 'single-' . $post_format;

            if (locate_template("template-parts/single/content-{$maybe}.php")) {
                $suffix = $maybe;
            }
        }

        get_template_part('template-parts/single/content', $suffix);
    }

}

get_footer();