<header class="block">
    <div class="content">
        <div class="stars"></div>
        <div class="clouds--wrapper">
            <div class="clouds">
                <div class="c1 one"></div>
                <div class="c1 two"></div>
                <div class="c1 three"></div>
                <div class="c1 four"></div>
                <div class="c2 one"></div>
                <div class="c2 two"></div>
                <div class="c2 three"></div>
                <div class="c2 four"></div>
            </div>
        </div>

        <div class="tree--wrapper">
            <img class="tree tree-0" src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/tree-min-0.webp"
                alt="tree" width="600" height="984" loading="lazy">
            <img class="tree tree-1" src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/tree-min-1.webp"
                alt="tree" width="600" height="984" loading="lazy">
            <img class="tree tree-2" src="<?= esc_url(get_template_directory_uri()); ?>/assets/img/tree-min-2.webp"
                alt="tree" width="600" height="984" loading="lazy">
        </div>
        <div class="container">
            <div class="slideshow--wrapper">
                <div class="slideshow">
                    <?php
                    $args = array(
                        'post_type' => 'quote',
                        'posts_per_page' => 7,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC',
                    );

                    $quotes_query = new WP_Query($args);

                    if ($quotes_query->have_posts()):
                        while ($quotes_query->have_posts()):
                            $quotes_query->the_post(); ?>

                            <article id="post-<?php the_ID(); ?>" <?php post_class('quote-item'); ?>>
                                <div class="quote-content">
                                    <?php the_content(); ?>
                                </div>
                            </article>

                        <?php endwhile;
                        wp_reset_postdata();
                    else:
                        echo '<p>No se encontraron citas recientes.</p>';
                    endif;
                    ?>
                </div>
            </div>
            <div class="slideshow-bullets-wrapper">
                <button class="slideshow-prev btn-pagination small-pagination" aria-label="siguiente diapositiva">
                    <?= stories_get_icon('backward'); ?>
                </button>
                <div class="slideshow-bullets bullets"></div>
                <button class="slideshow-next btn-pagination small-pagination" aria-label="anterior diapositiva">
                    <?= stories_get_icon('forward'); ?>
                </button>
            </div>
        </div>
    </div>
</header>