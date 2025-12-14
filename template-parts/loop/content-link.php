<?php
/**
 * Template part for displaying link format posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Stories V2.1
 * @since 2.0.0
 */

$content = get_the_content();
preg_match('/https?:\/\/[^\s"]+/', $content, $matches);
$url = $matches[0] ?? '';

$title = get_the_title(); // Default title
$image = '';
$date = ''; // New variable for external date

if ($url && wp_http_validate_url($url)) {

    // Try to retrieve data from cache
    $transient_key = 'stories_link_preview_' . md5($url);
    $cached_data = get_transient($transient_key);

    if (false !== $cached_data) {
        $title = $cached_data['title'];
        $image = $cached_data['image'];
        $date = $cached_data['date'];
    } else {
        // Make secure request with WP HTTP API
        $response = wp_remote_get($url, array(
            'timeout' => 5,
            'user-agent' => 'Mozilla/5.0 (compatible; StoriesTheme/2.0; +' . home_url() . ')',
            'redirection' => 5,
        ));

        if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
            $html = wp_remote_retrieve_body($response);

            if ($html) {

                /**
                 * ===================================
                 *  EXTERNAL TITLE
                 * ===================================
                 */

                // og:title
                if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $og_title)) {
                    $title = $og_title[1];
                }
                // <title>
                elseif (preg_match('/<title>(.*?)<\/title>/i', $html, $fallback_title)) {
                    $title = strip_tags($fallback_title[1]);
                }

                // Optional cleanup
                $title = preg_replace('/\s*-\s*La Voz de la Región$/i', '', $title);


                /**
                 * ===================================
                 *  EXTERNAL IMAGE
                 * ===================================
                 */

                // og:image
                if (preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $og_image)) {
                    $image = $og_image[1];
                } else {
                    // Manually search for images in main content
                    libxml_use_internal_errors(true);
                    $doc = new DOMDocument();
                    // Suppress warnings for malformed HTML
                    $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOWARNING | LIBXML_NOERROR);
                    $xpath = new DOMXPath($doc);

                    $nodes = $xpath->query("//div[contains(@class, 'post-body')]//img");

                    if ($nodes->length > 0) {
                        foreach ($nodes as $img) {
                            $image = $img->getAttribute('src') ?: $img->getAttribute('data-src');
                            if ($image)
                                break;
                        }
                    }

                    libxml_clear_errors();
                }


                /**
                 * ===================================
                 *  EXTERNAL DATE
                 * ===================================
                 */

                // 1. JSON-LD datePublished
                if (preg_match('/"datePublished"\s*:\s*"([^"]+)"/i', $html, $json_date)) {
                    $date = $json_date[1];
                }

                // 2. <meta property="article:published_time">
                elseif (preg_match('/<meta[^>]+property=["\']article:published_time["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $meta_date)) {
                    $date = $meta_date[1];
                }

                // 3. meta name="date"
                elseif (preg_match('/<meta[^>]+name=["\']date["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $meta2_date)) {
                    $date = $meta2_date[1];
                }

                // 4. <time datetime="">
                elseif (preg_match('/<time[^>]+datetime=["\']([^"\']+)["\']/i', $html, $time_date)) {
                    $date = $time_date[1];
                }

                // 5. Simple date YYYY-MM-DD
                elseif (preg_match('/\b(20\d{2}-\d{2}-\d{2})\b/', $html, $simple_date)) {
                    $date = $simple_date[1];
                }

                // Date formatting to "January 1, 2025"
                if ($date) {
                    $timestamp = strtotime($date);
                    if ($timestamp) {
                        $date = date_i18n('F j, Y', $timestamp);
                    }
                }

                // Cache for 24 hours
                set_transient($transient_key, array(
                    'title' => $title,
                    'image' => $image,
                    'date' => $date,
                ), DAY_IN_SECONDS);
            }
        }
    }
}

// If no external image, use post featured image
if (empty($image)) {
    $image = get_the_post_thumbnail_url(get_the_ID(), 'full');
}

// If no external date, use post date
if (empty($date)) {
    $date = get_the_date('F j, Y');
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-id="<?= get_the_ID(); ?>">
    <div class="post-body">
        <header class="post-body__header">
            <div class="category post--tags">
                <?= '<a href="' . esc_url(get_post_format_link('link')) . '" class="post-tag small">' . stories_get_icon('link') . esc_html(__('Enlace', 'stories')) . '</a>'; ?>
            </div>
            <?php if ($image): ?>
                <img class="wp-post-image" src="<?= esc_url($image); ?>" alt="<?= esc_attr($title); ?>" />
            <?php endif; ?>
        </header>
        <div class="post-body__content">
            <a class="post--permalink" href="<?= esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                <h2 class="post--title"><?= esc_html($title); ?></h2>
            </a>
            <div class="post--date" style="display: flex; align-items: center; gap: 0.5rem;">
                <?= stories_get_icon('date'); ?>
                <p><?= esc_html($date); ?></p>
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