<?php
/**
 * Core Theme Functions and Definitions
 *
 * This file serves as the main engine for the stories V2 theme, encapsulating
 * essential functionality such as:
 *  - Theme setup (menus, theme support, image sizes).
 *  - Asset management (enqueuing scripts and styles with versioning).
 *  - Custom Gutenberg block rendering and filters.
 *  - Theme Customizer settings (e.g., bio, social links).
 *  - Template tags and helper functions (breadcrumbs, icons).
 *
 * It is included by functions.php and acts as the backbone for
 * the theme's features and global operations.
 *
 * @package Stories V2
 * @subpackage Core
 * @since 2.0.0
 */

/*
 * =========================================================================
 * THEME SETUP
 * =========================================================================
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since 2.0.0
 */
function setup_stories()
{

    register_nav_menus(
        array(
            'primary' => __('Main Menu', 'stories'),
            'social' => __('Social Menu', 'stories'),
            'footer-1' => __('Footer 1', 'stories'),
            'footer-2' => __('Footer 2', 'stories'),
            'footer-3' => __('Footer 3', 'stories'),
        )
    );

    add_theme_support('title-tag');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', array('height' => 32, 'width' => 172, 'flex-height' => true, 'flex-width' => true, ));
    add_theme_support('html5', apply_filters('chanovera_html5_args', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'widgets', 'style', 'script', )));
    add_theme_support('post-formats', array('aside', 'image', 'video', 'quote', 'link', 'gallery', ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('post-thumbnails', ['post', 'page', 'special']);
    set_post_thumbnail_size(350, 200, true);
    add_image_size('loop-thumbnail', 400, 400, true);
}
add_action('after_setup_theme', 'setup_stories');

/**
 * Gets the version of a file based on its modification time.
 *
 * @param string $file_path Relative path to the file.
 * @return int|string File modification time or current time if file doesn't exist.
 */
function get_asset_version($file_path)
{
    $full_path = get_template_directory() . $file_path;
    return file_exists($full_path) ? filemtime($full_path) : time();
}

/**
 * Enqueues the main stylesheet in the header.
 */
function load_parts_header()
{
    wp_register_style('global', get_template_directory_uri() . '/style.css', array(), get_asset_version('/style.css'), 'all');
    wp_enqueue_style('global');
}
add_action('wp_enqueue_scripts', 'load_parts_header');

/**
 * Helper function to enqueue a stylesheet with versioning.
 *
 * @param string $handle Name of the stylesheet.
 * @param string $path   Relative path to the stylesheet.
 * @param string $media  Media type.
 */
function stories_enqueue_style($handle, $path, $media = 'all')
{
    $uri = get_template_directory_uri();
    wp_enqueue_style($handle, $uri . $path, [], get_asset_version($path), $media);
}

/**
 * Helper function to enqueue a script with versioning.
 *
 * @param string $handle Name of the script.
 * @param string $path   Relative path to the script.
 */
function stories_enqueue_script($handle, $path)
{
    $uri = get_template_directory_uri();
    wp_enqueue_script($handle, $uri . $path, [], get_asset_version($path), true);
}

/**
 * Returns an array of asset paths for the theme.
 *
 * @return array List of CSS and JS file paths.
 */
function stories_get_assets()
{
    $assets_path = '/assets';

    return [
        'css' => [
            // All pages
            'wp-root' => "$assets_path/css/wp-root.css",
            'custom-forms' => "$assets_path/css/custom-forms.css",
            'shapes' => "$assets_path/css/shapes.css",
            'wp-logged-in' => "$assets_path/css/wp-logged-in.css",
            'normalize' => "$assets_path/css/normalize.css",

            // someone pages
            'breadcrumbs' => "$assets_path/css/breadcrumbs.css",
            'posts-styles' => "$assets_path/css/posts.css",
            'pagination' => "$assets_path/css/pagination.css",
            'sidebar' => "$assets_path/css/sidebar.css",
            'page' => "$assets_path/css/page.css",
            'single' => "$assets_path/css/single.css",
            'post-gallery-styles' => "$assets_path/css/post-gallery.css",
            'related-styles' => "$assets_path/css/related.css",
            'comments' => "$assets_path/css/comments.css",
            'error404' => "$assets_path/css/error404.css",

            // homepage
            'homepage-styles' => "$assets_path/css/homepage.css",
        ],
        'js' => [
            // All pages
            'global-script' => "$assets_path/js/global.js",

            // someone pages
            'loop-gallery' => "$assets_path/js/loop-gallery.js",
            'animate-in' => "$assets_path/js/animate-in.js",
            'posts-scripts' => "$assets_path/js/posts.js",
            'post-gallery-script' => "$assets_path/js/post-gallery.js",
            'related-script' => "$assets_path/js/related.js",
            'post-scripts' => "$assets_path/js/post.js",

            // homepage
            'homepage-scripts' => "$assets_path/js/homepage.js",
            'testimonies-script' => "$assets_path/js/testimonies.js",
        ]
    ];
}

/**
 * Enqueues scripts and styles for the footer.
 */
function footer_components()
{
    $a = stories_get_assets();

    stories_enqueue_style('wp-root', $a['css']['wp-root']);
    stories_enqueue_style('custom-forms', $a['css']['custom-forms']);
    stories_enqueue_style('shapes', $a['css']['shapes']);
    stories_enqueue_style('normalize', $a['css']['normalize']);
    stories_enqueue_script('global-script', $a['js']['global-script']);

    if (is_user_logged_in()) {
        stories_enqueue_style('wp-logged-in', $a['css']['wp-logged-in']);
    }
}
add_action('wp_enqueue_scripts', 'footer_components');

/*
 * =========================================================================
 * WIDGETS & SIDEBARS
 * =========================================================================
 */

/**
 * Registers the widget areas (sidebars) for the theme.
 */
function widgets_areas()
{

    register_sidebar(
        array(
            'name' => __('Posts sidebar', 'stories'),
            'id' => 'sidebar-1',
            'before_widget' => '',
            'after_widget' => '',
        )
    );

    register_sidebar(
        array(
            'name' => __('Post sidebar', 'stories'),
            'id' => 'sidebar-2',
            'before_widget' => '',
            'after_widget' => '',
        )
    );

    register_sidebar(
        array(
            'name' => __('Page sidebar', 'stories'),
            'id' => 'sidebar-3',
            'before_widget' => '',
            'after_widget' => '',
        )
    );

}
add_action('widgets_init', 'widgets_areas');

/*
 * =========================================================================
 * FILTERS & UPLOADS
 * =========================================================================
 */

/**
 * Adds SVG support to the allowed MIME types.
 *
 * @param array $mimes Current list of allowed MIME types.
 * @return array Updated list of allowed MIME types.
 */
function mime_types($mimes)
{
    if (current_user_can('manage_options')) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter('upload_mimes', 'mime_types');

/**
 * Customizes the length of the post excerpt.
 *
 * @param int $limit Default excerpt length.
 * @return int New excerpt length.
 */
function reduce_excerpt_length($limit)
{
    return 21;
}
add_filter('excerpt_length', 'reduce_excerpt_length', 999);

/*
 * =========================================================================
 * HEAD INJECTIONS & STYLES
 * =========================================================================
 */

/**
 * Adds custom social media icons styles to the head.
 *
 * @since 2.0.0
 */
function theme_custom_icons()
{
    ?>
    <style>
        /* iconos de redes sociales */
        .menu li a[href*="facebook"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/facebook.svg');
        }

        .menu li a[href*="wa.me"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/whatsapp.svg');
        }

        .menu li a[href*="x.com"]:before,
        .menu li a[href*="twitter"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/twitter.svg');
        }

        .menu li a[href*="youtube"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/youtube.svg');
        }

        .menu li a[href*="instagram"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/instagram.svg');
        }

        .menu li a[href*="google"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/google.svg');
        }

        .menu li a[href*="tiktok"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/tiktok.svg');
        }

        .menu li a[href*="linkedin"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/linkedin.svg');
        }

        .menu li a[href*="flickr"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/flickr.svg');
        }

        .menu li a[href*="tel"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/support-phone.svg');
        }

        .menu li a[href*="mailto"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/mailto.svg');
        }

        .menu li a[href*="maps"]:before {
            mask-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/map.svg');
        }
    </style>
    <?php
}
add_action('wp_head', 'theme_custom_icons');

/**
 * Adds Google Tag Manager scripts to the head.
 *
 * @since 2.0.0
 */
function add_gtm_header()
{
    $ga_id = get_theme_mod('stories_ga_id', 'G-7XNN23WGQT');

    if (!$ga_id) {
        return;
    }
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', '<?php echo esc_js($ga_id); ?>', { 'transport_type': 'beacon', 'send_page_view': false });
    </script>
    <?php
}
add_action('wp_head', 'add_gtm_header');

/*
 * =========================================================================
 * TEMPLATE TAGS & HELPERS
 * =========================================================================
 */

/**
 * Displays breadcrumb navigation for the site.
 *
 * @since 2.0.0
 */
function wp_breadcrumbs()
{
    $separator = stories_get_icon('separator');
    $icon_home = stories_get_icon('home');
    $home = 'Inicio';
    $showCurrent = 1;
    $showOnHome = 0;
    $current = '';
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $cat_separator = ' <span class="separator">●</span> ';

    global $post;
    $homeLink = get_bloginfo('url');
    echo '<section class="block breadcrumbs--wrapper"><div class="content"><div class="breadcrumbs">';
    echo '<a class="go-home" href="' . $homeLink . '">' . $icon_home . $home . '</a>' . $separator;

    // ARCHIVO FORMATO IMAGE
    if (is_tax('post_format', 'post-format-image')) {
        echo $current . 'Dibujos más recientes';
    }

    // ARCHIVO FORMATO VIDEO
    elseif (is_tax('post_format', 'post-format-video')) {
        echo $current . 'Videos más recientes';
    }

    // ARCHIVO FORMATO GALERÍA
    elseif (is_tax('post_format', 'post-format-gallery')) {
        echo $current . 'Galerías más recientes';
    }

    // ARCHIVO FORMATO ENLACES
    elseif (is_tax('post_format', 'post-format-link')) {
        echo $current . 'Artículos externos más recientes';
    }

    // ARCHIVO FORMATO CITAS
    elseif (is_tax('post_format', 'post-format-quote')) {
        echo $current . 'Citas más recientes';
    }

    // ARCHIVO FORMATO MINIENTRADA
    elseif (is_tax('post_format', 'post-format-aside')) {
        echo $current . 'Minientradas más recientes';
    }

    // 2. CATEGORÍA
    elseif (is_category()) {
        if ($paged === 1) {
            echo '<span>Últimos artículos de la</span>';
            the_archive_title('<h1 class="page-title">', '</h1>');
        } else {
            echo esc_html('Página ' . $paged . ' de ');
            the_archive_title('<h1 class="page-title">', '</h1>');
        }
    }
    // 3. OTROS ARCHIVOS GENÉRICOS
    elseif (is_archive()) {
        if ($paged === 1) {
            $archive_text = is_date() ? 'Últimos artículos del' : 'Últimos artículos de la';
            echo '<span>' . $archive_text . '</span>';
            the_archive_title('<h1 class="page-title">', '</h1>');
        } else {
            echo esc_html('Página ' . $paged . ' de ');
            the_archive_title('<h1 class="page-title">', '</h1>');
        }
    } elseif (is_home()) {
        if ($paged === 1) {
            echo '<h1 class="page-title">' . esc_html__('Contenido más reciente', 'stories') . '</h1>';
        } else {
            echo '<span>' . esc_html('Página ' . $paged . ' de ') . '</span>' . '<h1 class="page-title">todo el contenido</h1>';
        }
    } elseif (is_page()) {
        if ($post->post_parent) {
            $ancestors = get_post_ancestors($post->ID);
            foreach ($ancestors as $ancestor) {
                $output = '<a href="' . get_permalink($ancestor) . '">' . get_the_title($ancestor) . '</a>' . $separator;
            }
            echo $output;
            echo $current . ' ' . get_the_title();
        } else {
            if ($showCurrent == 1)
                echo $current . ' ' . get_the_title();
        }
    } elseif (is_search()) {
        if ($paged === 1) {
            echo '<h1 class="page-title">';
            esc_html_e('Resultados de búsqueda de "', 'stories');
            echo get_search_query();
            esc_html_e('"', 'stories');
            echo '</h1>';
        } else {
            echo '<h1 class="page-title">' . esc_html('Página ' . $paged) . '</h1>';
        }
    } elseif (is_day()) {
        echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $separator;
        echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a>' . $separator;
        echo get_the_time('d') . $separator;
        echo $current . ' ' . get_the_time('l');
    } elseif (is_month()) {
        echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $separator;
        echo $current . ' ' . get_the_time('F');
    } elseif (is_year()) {
        echo $current . ' ' . get_the_time('Y');
    } elseif (is_single() && !is_attachment()) {
        if (get_post_type() != 'post') {
            $post_type = get_post_type_object(get_post_type());
            $slug = $post_type->rewrite;
            echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>' . $separator;
            if ($showCurrent == 1)
                echo $current . ' ';
        } else {
            $categories = get_the_category();
            if ($categories) {
                $links = [];
                foreach ($categories as $category) {
                    $ancestors = get_ancestors($category->term_id, 'category');
                    $ancestors = array_reverse($ancestors);

                    foreach ($ancestors as $ancestor_id) {
                        $ancestor = get_category($ancestor_id);
                        if ($ancestor) {
                            $links[] = '<a class="post-tag" href="' . esc_url(get_category_link($ancestor->term_id)) . '">' . stories_get_icon('category') . esc_html($ancestor->name) . '</a>';
                        }
                    }

                    $links[] = '<a class="post-tag" href="' . esc_url(get_category_link($category->term_id)) . '">' . stories_get_icon('category') . esc_html($category->name) . '</a>';
                }
                echo implode($cat_separator, array_unique($links));
            }

            $post_format = get_post_format();
            if ($post_format) {
                echo $separator;
                $format_label = '';
                switch ($post_format) {
                    case 'aside':
                        $format_label = __('Minientrada', 'stories');
                        break;
                    case 'gallery':
                        $format_label = __('Galería', 'stories');
                        break;
                    case 'image':
                        $format_label = __('Dibujo', 'stories');
                        break;
                    case 'video':
                        $format_label = __('Video', 'stories');
                        break;
                    case 'quote':
                        $format_label = __('Cita', 'stories');
                        break;
                    case 'link':
                        $format_label = __('Artículo externo', 'stories');
                        break;
                }

                if ($format_label) {
                    echo '<a class="post-tag" href="' . esc_url(get_post_format_link($post_format)) . '">' . stories_get_icon($post_format) . esc_html($format_label) . '</a>';
                }
            }

            echo $current . ' ';
        }
    } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
        // Aquí podrías manejar archivos de CPT si quisieras
    }

    echo '</div></div></section>';
}

/**
 * Retrieves an SVG icon based on the provided type.
 *
 * @param string $type The type of icon to retrieve.
 * @return string The SVG markup for the icon.
 */
function stories_get_icon($type)
{
    $icons = [
        'separator' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/></svg>',
        'home' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22 22L2 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M2 11L10.1259 4.49931C11.2216 3.62279 12.7784 3.62279 13.8741 4.49931L22 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M15.5 5.5V3.5C15.5 3.22386 15.7239 3 16 3H18.5C18.7761 3 19 3.22386 19 3.5V8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M4 22V9.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M20 22V9.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M15 22V17C15 15.5858 15 14.8787 14.5607 14.4393C14.1213 14 13.4142 14 12 14C10.5858 14 9.87868 14 9.43934 14.4393C9 14.8787 9 15.5858 9 17V22" stroke="currentColor" stroke-width="1.5"/><path d="M14 9.5C14 10.6046 13.1046 11.5 12 11.5C10.8954 11.5 10 10.6046 10 9.5C10 8.39543 10.8954 7.5 12 7.5C13.1046 7.5 14 8.39543 14 9.5Z" stroke="currentColor" stroke-width="1.5"/></svg>',
        'close' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>',
        'search' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg>',
        'date' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-week" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/><path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5zM11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/></svg>',
        'backward' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"></path></svg>',
        'forward' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"></path></svg>',
        'permalink' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right-circle" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.854 10.803a.5.5 0 1 1-.708-.707L9.243 6H6.475a.5.5 0 1 1 0-1h3.975a.5.5 0 0 1 .5.5v3.975a.5.5 0 1 1-1 0V6.707z"/></svg>',
        'gallery' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-images" viewBox="0 0 16 16"><path d="M4.502 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/><path d="M14.002 13a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2V5A2 2 0 0 1 2 3a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v8a2 2 0 0 1-1.998 2M14 2H4a1 1 0 0 0-1 1h9.002a2 2 0 0 1 2 2v7A1 1 0 0 0 15 11V3a1 1 0 0 0-1-1M2.002 4a1 1 0 0 0-1 1v8l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71a.5.5 0 0 1 .577-.094l1.777 1.947V5a1 1 0 0 0-1-1z"/></svg>',
        'image' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z"/></svg>',
        'link' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16"><path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/><path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/></svg>',
        'quote' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-quote" viewBox="0 0 16 16"><path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/></svg>',
        'video' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-film" viewBox="0 0 16 16"><path d="M0 1a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm4 0v6h8V1zm8 8H4v6h8zM1 1v2h2V1zm2 3H1v2h2zM1 7v2h2V7zm2 3H1v2h2zm-2 3v2h2v-2zM15 1h-2v2h2zm-2 3v2h2V4zm2 3h-2v2h2zm-2 3v2h2v-2zm2 3h-2v2h2z"/></svg>',
        'aside' => '<svg width="16" height="16" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.2895 2.75C11.4964 2.74979 11.6821 2.87701 11.7565 3.07003L14.9664 11.39C15.0657 11.6477 14.9375 11.9371 14.6798 12.0365C14.4222 12.1359 14.1328 12.0076 14.0334 11.75L12.9822 9.02537H9.61106L8.56672 11.749C8.46786 12.0068 8.1787 12.1357 7.92086 12.0369C7.66302 11.938 7.53414 11.6488 7.63301 11.391L10.8232 3.07099C10.8972 2.87782 11.0826 2.75021 11.2895 2.75ZM11.2915 4.64284L12.6543 8.17537H9.93698L11.2915 4.64284ZM2.89895 5.20703C1.25818 5.20703 0.00915527 6.68569 0.00915527 8.60972C0.00915527 10.6337 1.35818 12.0124 2.89895 12.0124C3.72141 12.0124 4.57438 11.6692 5.15427 11.0219V11.53C5.15427 11.7785 5.35574 11.98 5.60427 11.98C5.8528 11.98 6.05427 11.7785 6.05427 11.53V5.72C6.05427 5.47147 5.8528 5.27 5.60427 5.27C5.35574 5.27 5.15427 5.47147 5.15427 5.72V6.22317C4.60543 5.60095 3.79236 5.20703 2.89895 5.20703ZM5.15427 9.79823V7.30195C4.76393 6.58101 3.94144 6.05757 3.08675 6.05757C2.10885 6.05757 1.03503 6.96581 1.03503 8.60955C1.03503 10.1533 2.00885 11.1615 3.08675 11.1615C3.97011 11.1615 4.77195 10.4952 5.15427 9.79823Z" fill="currentColor"/></svg>',
        'category' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-bookmark" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M6 8V1h1v6.117L8.743 6.07a.5.5 0 0 1 .514 0L11 7.117V1h1v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8"></path><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"></path><path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"></path></svg>',
        'archive' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar4-week" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/><path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-2 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/></svg>',
        'tag' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tag" viewBox="0 0 16 16"><path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0"/><path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1m0 5.586 7 7L13.586 9l-7-7H2z"/></svg>',
        'comment' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-right-text" viewBox="0 0 16 16"><path d="M2 1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h9.586a2 2 0 0 1 1.414.586l2 2V2a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/><path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/></svg>',
        'chevron-down' => '<svg width="13" height="13" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"></path></svg>',

        // carolina eslava icons
        'exclamation-diamond-fill' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-diamond-fill" viewBox="0 0 16 16"><path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg>',
        'exclamation-triangle-fill' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg>',
        'exclamation-circle' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>',
        'unlock' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 0c1.07 0 2.041.42 2.759 1.104l.14.14.062.08a.5.5 0 0 1-.71.675l-.076-.066-.216-.205A3 3 0 0 0 5 4v2h6.5A2.5 2.5 0 0 1 14 8.5v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4M4.5 7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7z"/></svg>',
        'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-outbound" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5"/></svg>',
        'lightning' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightning" viewBox="0 0 16 16"><path d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641zM6.374 1 4.168 8.5H7.5a.5.5 0 0 1 .478.647L6.78 13.04 11.478 7H8a.5.5 0 0 1-.474-.658L9.306 1z"/></svg>',
        'shield-check' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16"><path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/><path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/></svg>',
        'fire' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fire" viewBox="0 0 16 16"><path d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16m0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15"/></svg>',
        'broadcast' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-broadcast" viewBox="0 0 16 16"><path d="M3.05 3.05a7 7 0 0 0 0 9.9.5.5 0 0 1-.707.707 8 8 0 0 1 0-11.314.5.5 0 0 1 .707.707m2.122 2.122a4 4 0 0 0 0 5.656.5.5 0 1 1-.708.708 5 5 0 0 1 0-7.072.5.5 0 0 1 .708.708m5.656-.708a.5.5 0 0 1 .708 0 5 5 0 0 1 0 7.072.5.5 0 1 1-.708-.708 4 4 0 0 0 0-5.656.5.5 0 0 1 0-.708m2.122-2.12a.5.5 0 0 1 .707 0 8 8 0 0 1 0 11.313.5.5 0 0 1-.707-.707 7 7 0 0 0 0-9.9.5.5 0 0 1 0-.707zM10 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/></svg>',
        'hammer' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M160-120v-80h480v80H160Zm226-194L160-540l84-86 228 226-86 86Zm254-254L414-796l86-84 226 226-86 86Zm184 408L302-682l56-56 522 522-56 56Z"/></svg>',
        'file' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16"><path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/><path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/></svg>',
        'briefcase' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16"><path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5m1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0M1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5"/></svg>',
        'people' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M40-160v-160q0-34 23.5-57t56.5-23h131q20 0 38 10t29 27q29 39 71.5 61t90.5 22q49 0 91.5-22t70.5-61q13-17 30.5-27t36.5-10h131q34 0 57 23t23 57v160H640v-91q-35 25-75.5 38T480-200q-43 0-84-13.5T320-252v92H40Zm440-160q-38 0-72-17.5T351-386q-17-25-42.5-39.5T253-440q22-37 93-58.5T480-520q63 0 134 21.5t93 58.5q-29 0-55 14.5T609-386q-22 32-56 49t-73 17ZM160-440q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T280-560q0 50-34.5 85T160-440Zm640 0q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T920-560q0 50-34.5 85T800-440ZM480-560q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-680q0 50-34.5 85T480-560Z"/></svg>',
        'domain' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M80-120v-720h400v160h400v560H80Zm80-80h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 480h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 480h320v-400H480v80h80v80h-80v80h80v80h-80v80Zm160-240v-80h80v80h-80Zm0 160v-80h80v80h-80Z"/></svg>',
        'support' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M440-120v-80h320v-284q0-117-81.5-198.5T480-764q-117 0-198.5 81.5T200-484v244h-40q-33 0-56.5-23.5T80-320v-80q0-21 10.5-39.5T120-469l3-53q8-68 39.5-126t79-101q47.5-43 109-67T480-840q68 0 129 24t109 66.5Q766-707 797-649t40 126l3 52q19 9 29.5 27t10.5 38v92q0 20-10.5 38T840-249v49q0 33-23.5 56.5T760-120H440Zm-80-280q-17 0-28.5-11.5T320-440q0-17 11.5-28.5T360-480q17 0 28.5 11.5T400-440q0 17-11.5 28.5T360-400Zm240 0q-17 0-28.5-11.5T560-440q0-17 11.5-28.5T600-480q17 0 28.5 11.5T640-440q0 17-11.5 28.5T600-400Zm-359-62q-7-106 64-182t177-76q89 0 156.5 56.5T720-519q-91-1-167.5-49T435-698q-16 80-67.5 142.5T241-462Z"/></svg>',
        'graph' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-graph-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07"/></svg>',
        'speed' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M418-340q24 24 62 23.5t56-27.5l224-336-336 224q-27 18-28.5 55t22.5 61Zm62-460q59 0 113.5 16.5T696-734l-76 48q-33-17-68.5-25.5T480-720q-133 0-226.5 93.5T160-400q0 42 11.5 83t32.5 77h552q23-38 33.5-79t10.5-85q0-36-8.5-70T766-540l48-76q30 47 47.5 100T880-406q1 57-13 109t-41 99q-11 18-30 28t-40 10H204q-21 0-40-10t-30-28q-26-45-40-95.5T80-400q0-83 31.5-155.5t86-127Q252-737 325-768.5T480-800Zm7 313Z"/></svg>',
        'offline-bolt' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="m456-200 174-340H510v-220L330-420h126v220Zm24 120q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>',
        'handshake' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M475-160q4 0 8-2t6-4l328-328q12-12 17.5-27t5.5-30q0-16-5.5-30.5T817-607L647-777q-11-12-25.5-17.5T591-800q-15 0-30 5.5T534-777l-11 11 74 75q15 14 22 32t7 38q0 42-28.5 70.5T527-522q-20 0-38.5-7T456-550l-75-74-175 175q-3 3-4.5 6.5T200-435q0 8 6 14.5t14 6.5q4 0 8-2t6-4l136-136 56 56-135 136q-3 3-4.5 6.5T285-350q0 8 6 14t14 6q4 0 8-2t6-4l136-135 56 56-135 136q-3 2-4.5 6t-1.5 8q0 8 6 14t14 6q4 0 7.5-1.5t6.5-4.5l136-135 56 56-136 136q-3 3-4.5 6.5T454-180q0 8 6.5 14t14.5 6Zm-1 80q-37 0-65.5-24.5T375-166q-34-5-57-28t-28-57q-34-5-56.5-28.5T206-336q-38-5-62-33t-24-66q0-20 7.5-38.5T149-506l232-231 131 131q2 3 6 4.5t8 1.5q9 0 15-5.5t6-14.5q0-4-1.5-8t-4.5-6L398-777q-11-12-25.5-17.5T342-800q-15 0-30 5.5T285-777L144-635q-9 9-15 21t-8 24q-2 12 0 24.5t8 23.5l-58 58q-17-23-25-50.5T40-590q2-28 14-54.5T87-692l141-141q24-23 53.5-35t60.5-12q31 0 60.5 12t52.5 35l11 11 11-11q24-23 53.5-35t60.5-12q31 0 60.5 12t52.5 35l169 169q23 23 35 53t12 61q0 31-12 60.5T873-437L545-110q-14 14-32.5 22T474-80Zm-99-560Z"/></svg>',
        'cleaning' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M120-40v-280q0-83 58.5-141.5T320-520h40v-320q0-33 23.5-56.5T440-920h80q33 0 56.5 23.5T600-840v320h40q83 0 141.5 58.5T840-320v280H120Zm80-80h80v-120q0-17 11.5-28.5T320-280q17 0 28.5 11.5T360-240v120h80v-120q0-17 11.5-28.5T480-280q17 0 28.5 11.5T520-240v120h80v-120q0-17 11.5-28.5T640-280q17 0 28.5 11.5T680-240v120h80v-200q0-50-35-85t-85-35H320q-50 0-85 35t-35 85v200Zm320-400v-320h-80v320h80Zm0 0h-80 80Z"/></svg>',
        'clock' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>',
        'ban' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ban" viewBox="0 0 16 16"><path d="M15 8a6.97 6.97 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/></svg>',
        'boxes' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-boxes" viewBox="0 0 16 16"><path d="M7.752.066a.5.5 0 0 1 .496 0l3.75 2.143a.5.5 0 0 1 .252.434v3.995l3.498 2A.5.5 0 0 1 16 9.07v4.286a.5.5 0 0 1-.252.434l-3.75 2.143a.5.5 0 0 1-.496 0l-3.502-2-3.502 2.001a.5.5 0 0 1-.496 0l-3.75-2.143A.5.5 0 0 1 0 13.357V9.071a.5.5 0 0 1 .252-.434L3.75 6.638V2.643a.5.5 0 0 1 .252-.434zM4.25 7.504 1.508 9.071l2.742 1.567 2.742-1.567zM7.5 9.933l-2.75 1.571v3.134l2.75-1.571zm1 3.134 2.75 1.571v-3.134L8.5 9.933zm.508-3.996 2.742 1.567 2.742-1.567-2.742-1.567zm2.242-2.433V3.504L8.5 5.076V8.21zM7.5 8.21V5.076L4.75 3.504v3.134zM5.258 2.643 8 4.21l2.742-1.567L8 1.076zM15 9.933l-2.75 1.571v3.134L15 13.067zM3.75 14.638v-3.134L1 9.933v3.134z"/></svg>',
        'security' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M480-80q-139-35-229.5-159.5T160-516v-244l320-120 320 120v244q0 152-90.5 276.5T480-80Zm0-84q97-30 162-118.5T718-480H480v-315l-240 90v207q0 7 2 18h238v316Z"/></svg>',
        'discrete' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Zm0-80q88 0 158-48.5T740-375q-20 5-40 8t-40 3q-123 0-209.5-86.5T364-660q0-20 3-40t8-40q-78 32-126.5 102T200-480q0 116 82 198t198 82Zm-10-270Z"/></svg>',
        'communication' => '<svg xmlns="http://www.w3.org/2000/svg" height="16" viewBox="0 -960 960 960" width="16" fill="currentColor"><path d="m408-432-42-42q8-11 11-22.5t3-23.5q0-12-3-23.5T366-565l42-43q16 19 24 42t8 46q0 23-8 45.5T408-432Zm85 86-43-43q25-28 37.5-62t12.5-69q0-35-12.5-68.5T450-650l43-43q34 37 50.5 81.5T560-520q0 47-16.5 92T493-346ZM200-480q-33 0-56.5-23.5T120-560q0-33 23.5-56.5T200-640q33 0 56.5 23.5T280-560q0 33-23.5 56.5T200-480ZM40-320v-23q0-24 13-44t36-30q26-11 53.5-17t57.5-6q30 0 57.5 6t53.5 17q23 10 36 30t13 44v23H40Zm720-160q-33 0-56.5-23.5T680-560q0-33 23.5-56.5T760-640q33 0 56.5 23.5T840-560q0 33-23.5 56.5T760-480ZM600-320v-23q0-24 13-44t36-30q26-11 53.5-17t57.5-6q30 0 57.5 6t53.5 17q23 10 36 30t13 44v23H600Z"/></svg>',
        'mail-secure' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-check" viewBox="0 0 16 16"><path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z"/><path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686"/></svg>',
    ];

    return $icons[$type] ?? '';
}

/*
 * =========================================================================
 * CUSTOMIZER SETTINGS
 * =========================================================================
 */

/**
 * Theme Customizer Settings for stories
 *
 * This file adds a custom section to the WordPress Customizer
 * that allows administrators to set site-specific data such as
 * a short bio or description. All fields include sanitization
 * and translation support for better security and flexibility.
 */
function stories_customize_register($wp_customize)
{

    // ====== SECTION: Site Data ======
    $wp_customize->add_section('stories_site_data', array(
        'title' => __('Site Data', 'stories'),
        'description' => __('Define basic information about your website.', 'stories'),
        'priority' => 11,
    ));

    // ====== SETTING: Google Analytics ID ======
    $wp_customize->add_setting('stories_ga_id', array(
        'default' => 'G-7XNN23WGQT',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    // ====== CONTROL: Google Analytics ID ======
    $wp_customize->add_control('stories_ga_id', array(
        'label' => __('Google Analytics ID', 'stories'),
        'description' => __('Enter your Google Analytics G-ID (e.g., G-XXXXXXXXXX).', 'stories'),
        'section' => 'stories_site_data',
        'type' => 'text',
    ));

    // ====== SETTING: Short Bio ======
    $wp_customize->add_setting('stories_bio', array(
        'default' => __('Relatos y Cartas es un espacio dedicado a la creatividad y la expresión a través de las palabras. Aquí encontrarás cuentos, microcuentos, poemas e historias que buscan inspirar, emocionar y conectar con los lectores.', 'stories'),
        'sanitize_callback' => 'wp_kses_post', // Allows safe HTML
    ));

    // ====== CONTROL: Short Bio ======
    $wp_customize->add_control('stories_bio', array(
        'label' => __('Short Bio', 'stories'),
        'section' => 'stories_site_data',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'stories_customize_register');

/*
 * =========================================================================
 * CUSTOM POST TYPE ARCHIVE
 * =========================================================================
 */

/**
 * Filter to ensure the 'special' custom post type has an archive enabled.
 * This makes get_post_type_archive_link('special') work correctly.
 */
add_filter('register_post_type_args', function ($args, $post_type) {
    if ($post_type === 'special') {
        $args['has_archive'] = true;
    }
    return $args;
}, 10, 2);

/*
 * =========================================================================
 * BLOCK FILTERS & CUSTOM RENDER
 * =========================================================================
 */

/**
 * Enhances menu structure with custom elements
 * 
 * Adds submenu indicators and custom markup for mobile and primary
 * navigation menus. Includes SVG icons for visual hierarchy.
 *
 * @param string $item_output The menu item's HTML
 * @param object $item Menu item data object
 * @param int $depth Depth of menu item
 * @param object $args Menu arguments
 * @return string Modified menu item HTML
 */
function custom_menu($item_output, $item, $depth, $args)
{
    $allowed_locations = ['primary'];

    if (!isset($args->theme_location) || !in_array($args->theme_location, $allowed_locations)) {
        return $item_output;
    }

    global $submenu_items_by_parent;
    static $checked_menus = [];

    if (!empty($args->menu) && !in_array($args->menu->term_id, $checked_menus)) {
        $menu_items = wp_get_nav_menu_items($args->menu->term_id);
        foreach ($menu_items as $menu_item) {
            $submenu_items_by_parent[$menu_item->menu_item_parent][] = $menu_item;
        }
        $checked_menus[] = $args->menu->term_id;
    }

    $has_children = !empty($submenu_items_by_parent[$item->ID]);

    if ($has_children) {
        $text = '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        $svg_icon = stories_get_icon('chevron-down');

        return '<div class="wrapper-for-title">' . $text . '<button class="button-for-submenu">' . $svg_icon . '</button></div>';
    }

    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'custom_menu', 10, 4);

/**
 * Renders a custom gallery block with slider functionality.
 *
 * @param string $block_content The original block content.
 * @param array  $block         The block attributes and structure.
 * @return string Modified block content.
 */
function custom_render_gallery_block($block_content, $block)
{
    if ($block['blockName'] !== 'core/gallery') {
        return $block_content;
    }

    if (empty($block['innerBlocks'])) {
        return $block_content;
    }

    $images = [];

    foreach ($block['innerBlocks'] as $inner) {
        if ($inner['blockName'] === 'core/image' && !empty($inner['attrs']['id'])) {

            $img_id = $inner['attrs']['id'];
            $src = wp_get_attachment_image_src($img_id, 'large')[0] ?? '';
            $alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);

            if ($src) {
                $images[] = [
                    'src' => $src,
                    'alt' => $alt
                ];
            }
        }
    }

    if (empty($images)) {
        return $block_content;
    }

    $total_slides = count($images);
    $slide_width = 100 / $total_slides;

    ob_start();
    ?>

    <div class="post-gallery-wrapper">
        <div class="total-images post-tag glass-backdrop glass-bright"></div>
        <div class="post-gallery" style="display:flex;width:<?php echo $total_slides * 100; ?>%;">
            <?php foreach ($images as $i => $img): ?>
                <div class="post-gallery-slide<?php echo $i === 0 ? ' active' : ''; ?>"
                    style="width:<?php echo $slide_width; ?>%;">
                    <img src="<?php echo esc_url($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="post-gallery-thumbs-container">
            <button class="btn-pagination" aria-label="Anterior"><?= stories_get_icon('backward'); ?></button>
            <div class="post-gallery-thumbs"></div>
            <button class="btn-pagination" aria-label="Siguiente"><?= stories_get_icon('forward'); ?></button>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_filter('render_block', 'custom_render_gallery_block', 10, 3);

/**
 * Customizes the rendering of the core search block.
 *
 * @param string $block_content The original block content.
 * @param array  $block         The block attributes and structure.
 * @return string Modified block content.
 */
function custom_wp_block_search($block_content, $block)
{
    if ($block['blockName'] === 'core/search') {
        ob_start();
        ?>
        <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="stories-block-search">
            <div class="stories-block-search__inside-wrapper ">
                <input class="stories-block-search__input"
                    id="<?php echo esc_attr(wp_unique_id('stories-block-search__input-')); ?>"
                    placeholder="<?php esc_html_e('Buscar', 'stories'); ?>" value="" type="search" name="s" required="">
                <button aria-label="<?php esc_html_e('Buscar', 'stories'); ?>"
                    class="stories-block-search__button stories-element-button" type="submit">
                    <?= stories_get_icon('search'); ?>
                </button>
            </div>
        </form>
        <?php
        return ob_get_clean();
    }
    return $block_content;
}
add_filter('render_block', 'custom_wp_block_search', 10, 2);

/**
 * Customizes the HTML output of the category list.
 *
 * @param string $output The original category list HTML.
 * @param array  $args   Arguments used for retrieving categories.
 * @return string Modified category list HTML.
 */
function custom_category_list($output, $args)
{
    $categories = get_categories($args);

    $output = '';
    foreach ($categories as $category) {
        $svg_icon = stories_get_icon('category');

        $output .= '<li>';
        $output .= '<a href="' . esc_url(get_category_link($category->term_id)) . '">';
        $output .= $svg_icon . '<span>' . esc_html($category->name);
        $output .= '</span></a>';
        $output .= '</li>';
    }
    $output .= '';

    return $output;
}
add_filter('wp_list_categories', 'custom_category_list', 10, 2);

/**
 * Customizes the HTML output of archive links.
 *
 * @param string $link_html The original link HTML.
 * @param string $url       The archive URL.
 * @param string $text      The archive link text.
 * @param string $format    The archive format.
 * @param string $before    Content before the link.
 * @param string $after     Content after the link.
 * @return string Modified archive link HTML.
 */
function custom_archives_link($link_html, $url, $text, $format, $before, $after)
{
    $custom_link = '<li><a href="' . esc_url($url) . '">' .
        stories_get_icon('archive') .
        '<span>' . $text . '</span></a></li>';
    // Return the modified link HTML
    return $before . $custom_link . $after;
}
add_filter('get_archives_link', 'custom_archives_link', 10, 6);

/**
 * Modifies the latest posts block to exclude specific post formats and customize the layout.
 *
 * @param string $block_content The original block content.
 * @param array  $block         The block attributes and structure.
 * @return string Modified block content.
 */
function custom_modify_latest_posts_block($block_content, $block)
{
    if ($block['blockName'] !== 'core/latest-posts') {
        return $block_content;
    }

    $args = [
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'tax_query' => [
            [
                'taxonomy' => 'post_format',
                'field' => 'slug',
                'terms' => ['post-format-aside', 'post-format-image', 'post-format-video', 'post-format-gallery', 'post-format-link', 'post-format-quote'],
                'operator' => 'NOT IN'
            ]
        ]
    ];
    $recent_posts = get_posts($args);

    if (empty($recent_posts)) {
        return $block_content;
    }

    $output = '<ul class="wp-block-latest-posts__list wp-block-latest-posts">';

    foreach ($recent_posts as $post) {
        $post_id = $post->ID;
        $post_title = esc_html(get_the_title($post_id));
        $post_link = esc_url(get_permalink($post_id));
        $post_date = get_the_date('j \d\e F \d\e Y', $post_id);
        $post_thumbnail = get_the_post_thumbnail($post_id, 'thumbnail', ['class' => 'latest-post-thumbnail']);

        $output .= '<li><a class="latest-post__body" href="' . $post_link . '">';
        if ($post_thumbnail) {
            $output .= '<div class="latest-post-thumbnail-wrapper">' . $post_thumbnail . '</div>';
        }
        $output .= '<h4 class="wp-block-latest-posts__post-title">' . $post_title . '</h4>';
        $output .= '<div class="latest-post-date">' . $post_date . '</div>';
        $output .= '</li></a>';
    }

    $output .= '</ul>';

    return $output;
}
add_filter('render_block', 'custom_modify_latest_posts_block', 10, 2);

/**
 * Customizes the avatar size for comments.
 *
 * @param string $avatar The avatar HTML.
 * @return string Modified avatar HTML with fixed width and height.
 */
function custom_comment_avatar_size($avatar)
{
    // Remove existing width, height, and style attributes from the avatar
    $avatar = preg_replace('/(width|height)="\d*"\s/', '', $avatar);
    $avatar = preg_replace('/style=["\'](.*?)["\']/', '', $avatar);

    // Set a fixed width and height of 70 pixels for the avatar
    $avatar = preg_replace('/src=([\'"])((?:(?!\1).)*?)\1/', 'src=$1$2$1 width="70" height="70"', $avatar);

    return $avatar;
}
add_filter('get_avatar', 'custom_comment_avatar_size', 10, 1);

/*
 * =========================================================================
 * TEMPLATES
 * =========================================================================
 */

/**
 * Checks if the current post has related posts.
 *
 * @param int|null $post_id Optional. Post ID. Defaults to current post.
 * @return bool True if related posts exist, false otherwise.
 */
function stories_has_related_posts($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $categories = wp_get_post_categories($post_id);
    $tags = wp_get_post_tags($post_id);

    if (empty($categories) && empty($tags)) {
        return false;
    }

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'post__not_in' => array($post_id),
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
        'fields' => 'ids',
        'no_found_rows' => true,
    );

    $query = new WP_Query($args);

    return $query->have_posts();
}

/**
 * Enqueues styles and scripts for posts, archives, and search results.
 *
 * This function checks for galleries in the loop and enqueues the necessary assets.
 * It also enqueues the sidebar styles if the sidebar is active.
 *
 * @since 2.0.0
 */
function posts_styles()
{
    if (is_home() or is_archive() or is_search() || is_page_template('archive-special.php')) {
        $a = stories_get_assets();

        global $wp_query;

        $has_gallery = false;

        foreach ($wp_query->posts as $post) {
            if (has_block('core/gallery', $post) || has_shortcode($post->post_content, 'gallery')) {
                $has_gallery = true;
                break;
            }
        }

        if ($has_gallery) {
            stories_enqueue_script('loop-gallery', $a['js']['loop-gallery']);
        }

        stories_enqueue_style('breadcrumbs', $a['css']['breadcrumbs']);
        stories_enqueue_style('posts-styles', $a['css']['posts-styles']);
        stories_enqueue_style('pagination', $a['css']['pagination']);
        stories_enqueue_script('animate-in', $a['js']['animate-in']);
        stories_enqueue_script('posts-scripts', $a['js']['posts-scripts']);

        if (is_active_sidebar('sidebar-1')) {
            stories_enqueue_style('sidebar', $a['css']['sidebar']);
        }
    }
}
add_action('wp_enqueue_scripts', 'posts_styles');

/**
 * Enqueues styles and scripts for single pages and posts.
 *
 * Checks for specific conditions (galleries, sidebars) and enqueues relevant assets.
 *
 * @since 2.0.0
 */
function page_template()
{
    $assets_path = '/assets';

    if (is_page() or is_single()) {
        $a = stories_get_assets();

        stories_enqueue_style('page', $a['css']['page']);
        stories_enqueue_style('breadcrumbs', $a['css']['breadcrumbs']);

        if (is_page() && is_active_sidebar('sidebar-3')) {
            stories_enqueue_style('sidebar', $a['css']['sidebar']);
        }

        if (is_single() && is_active_sidebar('sidebar-2')) {
            stories_enqueue_style('sidebar', $a['css']['sidebar']);
        }

        $post_id = get_queried_object_id();

        $post = get_post($post_id);
        if ($post && (has_block('core/gallery', $post) || has_shortcode($post->post_content, 'gallery'))) {
            stories_enqueue_style('post-gallery-styles', $a['css']['post-gallery-styles']);
            stories_enqueue_script('post-gallery-script', $a['js']['post-gallery-script']);
        }

        if (is_single()) {
            stories_enqueue_style('single', $a['css']['single']);
            if (stories_has_related_posts()) {
                stories_enqueue_style('related-styles', $a['css']['related-styles']);
                stories_enqueue_script('related-script', $a['js']['related-script']);
                stories_enqueue_script('animate-in', $a['js']['animate-in']);
                stories_enqueue_script('post-scripts', $a['js']['post-scripts']);
            }

            if (comments_open()) {
                stories_enqueue_style('custom-comments', $a['css']['comments']);
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'page_template');

/**
 * Enqueues styles specifically for 404 error page
 * 
 * Loads custom CSS file only when viewing 404 page
 * to optimize performance and reduce unnecessary loading
 *
 * @since 2.0.0
 * @return void
 */
function page404_styles()
{
    if (is_404()) {
        $a = stories_get_assets();
        stories_enqueue_style('error404', $a['css']['error404']);
    }
}
add_action('wp_enqueue_scripts', 'page404_styles');

/**
 * Enqueues styles specifically for homepage
 * 
 * Loads custom CSS file only when viewing homepage
 * to optimize performance and reduce unnecessary loading
 *
 * @since 2.0.0
 * @return void
 */
function homepage_styles()
{
    if (is_page_template('templates/homepage.php')) {
        $a = stories_get_assets();

        function unload_parts_header()
        {
            wp_dequeue_style('page');
        }
        add_action('wp_enqueue_scripts', 'unload_parts_header', 100);

        global $wp_query;

        $has_gallery = false;

        foreach ($wp_query->posts as $post) {
            if (has_block('core/gallery', $post) || has_shortcode($post->post_content, 'gallery')) {
                $has_gallery = true;
                break;
            }
        }

        stories_enqueue_style('homepage-styles', $a['css']['homepage-styles']);
        stories_enqueue_style('posts-styles', $a['css']['posts-styles']);

        stories_enqueue_script('loop-gallery', $a['js']['loop-gallery']);
        stories_enqueue_script('animate-in', $a['js']['animate-in']);
        stories_enqueue_script('homepage-scripts', $a['js']['homepage-scripts']);
        stories_enqueue_script('related-script', $a['js']['related-script']);
        stories_enqueue_script('testimonies-script', $a['js']['testimonies-script']);
    }
}
add_action('wp_enqueue_scripts', 'homepage_styles');