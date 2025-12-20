<?php
/**
 * Template part for displaying DDE (Detrás del Espejo) posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package stories
 * @since 2.0.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-id="<?php echo get_the_ID(); ?>">
    <div class="post-body">
        <div class="post-body__content">
            <a class="post--permalink" href="<?php the_permalink(); ?>">
                <?php the_title('<h3 class="post--title">', '</h3>'); ?>
            </a>
            <div class="post--excerpt">
                <?php echo get_the_excerpt(); ?>
            </div>
            <div class="post--date" style="display: flex; align-items: center; gap: 0.5rem;">
                <?= stories_get_icon('date'); ?>
                <p><?php echo get_the_date('F j, Y'); ?></p>
            </div>
        </div>
    </div>
</article>