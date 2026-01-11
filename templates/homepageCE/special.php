<?php
/**
 * Template part for the 'special' section on the homepage.
 *
 * This section displays latest posts from the 'special' post type.
 *
 * @package Stories
 * @version 2.1
 */
?>
<section id="special" class="block posts--body">
    <div class="content heading">
        <span>Sección especial</span>
        <h2 class="title-section">Manejo de Crisis: Nuestro ADN Operativo</h2>
        <h3 class="subtitle-section">Más allá de los servicios y las fases, nuestra agencia cuenta con un método
            propio. No improvisamos. Actuamos con protocolos diseñados después de haber gestionado crisis
            mediáticas, corporativas, políticas y personales durante décadas.</h3>
    </div>
    <div class="content special-content">
        <div class="loop">
            <?php
            $args = [
                'post_type' => 'special',
                'post_status' => 'publish',
                'posts_per_page' => 4,
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
        <a href="<?php echo esc_url(get_post_type_archive_link('special')); ?>" class="btn primary go-to-blog"
            aria-label="Link to go special posts page">
            Ver todos los artículos
            <?= stories_get_icon('forward'); ?>
        </a>
    </div>
</section>