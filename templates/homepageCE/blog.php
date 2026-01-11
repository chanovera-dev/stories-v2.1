<?php
/**
 * Template part for the 'blog' section on the homepage.
 *
 * This section displays the latest news and articles from the standard post type.
 *
 * @package Stories
 * @version 2.1
 */
?>
<section id="blog" class="block posts--body">
    <div class="content heading">
        <span>Nuestras noticias y artículos</span>
        <h2 class="title-section">Inteligencia Reputacional: Lo que necesitas saber antes, durante y después de una
            crisis</h2>
        <h3 class="subtitle-section">Nuestra visión, análisis y metodologías para entender cómo se construye —y se
            destruye— una reputación en la era digital.</h3>
    </div>
    <div class="content">
        <div class="loop">
            <?php
            $args = [
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 6,
                'orderby' => 'date',
                'order' => 'DESC',
            ];

            $latest_posts = new WP_Query($args);

            if ($latest_posts->have_posts()):
                while ($latest_posts->have_posts()):
                    $latest_posts->the_post();
                    $post_format = get_post_format();
                    $part = 'archive';

                    if ($post_format) {
                        if (locate_template("template-parts/loop/content-{$post_format}.php")) {
                            $part = $post_format;
                        }
                    }

                    get_template_part('template-parts/loop/content', $part);
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <div class="content permalink">
        <button onclick="window.location.href='<?php echo get_post_type_archive_link('post'); ?>'"
            class="btn primary go-to-blog" aria-label="Link to go blog page">
            Ver todos los artículos
            <?= stories_get_icon('forward'); ?>
        </button>
    </div>
</section>