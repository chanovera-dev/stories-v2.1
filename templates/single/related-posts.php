<?php
/**
 * Related Posts Template Part
 *
 * This template handles the display of related content on single post pages.
 * It queries posts that share the same categories or tags as the current post
 * and presents them in a carousel/slideshow format.
 *
 * @package Stories V2.1
 * @subpackage Templates
 * @since Stories 2.0.0
 */
?>
<section class="block posts--body container--related-posts">
    <?php
    $categories = wp_get_post_categories(get_the_ID());
    $tags = wp_get_post_tags(get_the_ID());
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 8,
        'post__not_in' => array(get_the_ID()),
        'orderby' => 'rand',
        'tax_query' => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $categories,
            ),
            array(
                'taxonomy' => 'post_tag',
                'field' => 'term_id',
                'terms' => wp_list_pluck($tags, 'term_id'),
            ),
        ),
    );

    $related_posts = new WP_Query($args);

    if ($related_posts->have_posts()):
        ?>
        <div class="content related-posts--title">
            <h2 class="title-section"><?php echo esc_html_e('Contenido relacionado', 'stories'); ?></h2>
        </div>
        <div class="content slideshow-wrapper">
            <div class="related-posts--list slideshow">
                <?php
                while ($related_posts->have_posts()):
                    $related_posts->the_post();
                    $post_format = get_post_format();
                    $part = 'archive';

                    if ($post_format) {
                        if (locate_template("template-parts/loop/content-{$post_format}.php")) {
                            $part = $post_format;
                        }
                    }

                    get_template_part('template-parts/loop/content', $part);
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