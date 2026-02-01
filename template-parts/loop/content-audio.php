<?php
/**
 * Template part for displaying audio format posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package stories
 * @since 1.0.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-id="<?= get_the_ID(); ?>">
    <div class="post-body">
        <header class="post-body__header" style="aspect-ratio: 1 / 1.13949; display: grid; grid-template-rows: 1fr 54px;">
            <div class="category post--tags">
                <?= '<a href="' . esc_url(get_post_format_link('audio')) . '" class="post-tag small glass-backdrop">' . stories_get_icon('audio') . esc_html(__('Audio', 'stories')) . '</a>'; ?>
            </div>
            
            <?php if (has_post_thumbnail()) : ?>
                <?= get_the_post_thumbnail(null, 'loop-thumbnail', ['class' => 'post-thumbnail audio-thumbnail', 'alt' => get_the_title(), 'loading' => 'lazy']); ?>
            <?php endif; ?>

            <?php $post_title = get_the_title(); ?>
            <a class="post--permalink btn-pagination small-pagination glass-backdrop" href="<?php the_permalink(); ?>"
                aria-label="Ver el audio de <?= esc_attr($post_title); ?>">
                <?= stories_get_icon('permalink'); ?>
            </a>

            <div class="post-audio-wrapper">
                <?php
                // =========================================
                // GET FIRST AUDIO WITHOUT BREAKING THE LOOP
                // =========================================
                $post_obj = get_post();
                $content = $post_obj->post_content;
                $first_audio_html = '';

                // 1) HTML5 audio
                if (!$first_audio_html && preg_match('/<audio.*?<\/audio>/is', $content, $match)) {
                    $first_audio_html = $match[0];
                }

                // 2) [audio] shortcode
                if (!$first_audio_html && has_shortcode($content, 'audio')) {
                    $first_audio_html = do_shortcode('[audio]');
                }

                // 3) Gutenberg blocks
                if (!$first_audio_html) {
                    $blocks = parse_blocks($content);
                    foreach ($blocks as $block) {
                        if ($block['blockName'] === 'core/audio' && !empty($block['attrs']['src'])) {
                            $first_audio_html = '<audio controls src="' . esc_url($block['attrs']['src']) . '"></audio>';
                            break;
                        }
                        if ($block['blockName'] === 'core/embed' && !empty($block['attrs']['url'])) {
                            // Audio embeds (Spotify, Soundcloud)
                            if (strpos($block['attrs']['url'], 'spotify.com') !== false || strpos($block['attrs']['url'], 'soundcloud.com') !== false) {
                                $first_audio_html = wp_oembed_get($block['attrs']['url']);
                                break;
                            }
                        }
                    }
                }

                if ($first_audio_html) {
                    echo $first_audio_html;
                }
                ?>
            </div>
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