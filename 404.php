<?php
/**
 * 404 Error Page Template
 *
 * This template is used when WordPress cannot find the requested content.
 * It provides a friendly message to users, a link to return to the homepage,
 * and can be styled or enhanced with additional navigation, search, or suggested content.
 *
 * @package stories
 * @since Stories 2.0.0
 */
get_header(); ?>

<main id="main" class="site-main" role="main">
    <section class="block not-found__wrapper">
        <div class="content not-found">
            <?php
            $image_path = '/assets/img/404-alpha.webp';
            $image_url = get_template_directory_uri() . $image_path;
            $image_file = get_template_directory() . $image_path;
            $image_dims = file_exists($image_file) ? getimagesize($image_file)[3] : '';
            ?>
            <img class="not-found__image" src="<?php echo esc_url($image_url); ?>" alt="" <?php echo $image_dims; ?>
                loading="lazy">
            <h2><?php esc_html_e('Página no encontrada.', 'stories'); ?></h2>

            <!-- Back to Homepage Button -->
            <button class="btn primary" onclick="window.location.href='<?= site_url(); ?>'" aria-name="Link to go home">
                <?= stories_get_icon('home'); ?>
                <?php esc_html_e('Ir al inicio', 'stories'); ?>
            </button>
        </div>
    </section>
</main><!-- .site-main -->

<?php get_footer(); ?>