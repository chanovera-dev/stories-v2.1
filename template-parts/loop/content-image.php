<?php
/**
 * Template part for displaying image format posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Stories V2.1
 * @since 2.0.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-id="<?= get_the_ID(); ?>">
    <div class="post-body">
        <header class="post-body__header" style="aspect-ratio: 1 / 1.13949;">
            <div class="category post--tags">
                <?= '<a href="' . esc_url(get_post_format_link('image')) . '" class="post-tag small glass-backdrop">' . stories_get_icon('image') . esc_html(__('Dibujo', 'core')) . '</a>'; ?>
            </div>
            <?php
            if (has_post_thumbnail()) {
                echo get_the_post_thumbnail(null, 'loop-thumbnail', ['class' => 'post-thumbnail', 'alt' => get_the_title(), 'loading' => 'lazy']);
            }
            ?>
            <?php $post_title = get_the_title(); ?>
            <a class="post--permalink btn-pagination small-pagination glass-backdrop" href="<?php the_permalink(); ?>"
                aria-label="Ver la imagen de <?= esc_attr($post_title); ?>">
                <?= stories_get_icon('permalink'); ?>
            </a>
        </header>
        <div class="post-body__content">
            <a class="post--permalink" href="<?php the_permalink(); ?>">
                <?php the_title('<h2 class="post--title">', '</h2>'); ?>
            </a>
            <div class="post--date" style="display: flex; align-items: center; gap: 0.5rem;">
                <?= stories_get_icon('date'); ?>
                <p><?= get_the_date('F j, Y'); ?></p>
            </div>
        </div>
        <footer class="post-body__footer">
            <div class="tags post--tags">
                <?php
                $tags = get_the_tags();
                if ($tags) {
                    foreach ($tags as $tag) {
                        echo '<a class="post-tag small" href="' . esc_url(get_tag_link($tag->term_id)) . '">' . stories_get_icon('tag') . esc_html($tag->name) . '</a>';
                    }
                }
                ?>
            </div>
        </footer>
    </div>
</article>