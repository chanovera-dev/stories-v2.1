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
    add_theme_support('post-thumbnails', ['post', 'page']);
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

            // real estate
            'single-property' => "$assets_path/css/single-property.css",
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

            // real estate
            'filters'                 => "$assets_path/js/filters.js",
            'filter-listeners'        => "$assets_path/js/filter-listeners.js",
            'reset-properties-filter' => "$assets_path/js/reset-properties-filter.js",
            'ajax-properties'         => "$assets_path/js/ajax-properties.js",
            'ajax-search'             => "$assets_path/js/ajax-search-properties.js",
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
            if (get_post_type() == 'property') {
                echo '<a href="' . $homeLink . '/propiedades/">Propiedades</a>' . $separator;
            } else {
                $slug = $post_type->rewrite;
                echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>' . $separator;
            }
            if ($showCurrent == 1)
                echo $current . get_the_title();
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

        // contact
        'whatsapp' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16"><path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/></svg>',
        'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg>',
        'email' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-at" viewBox="0 0 16 16"><path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z"/><path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648m-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z"/></svg>',

        // real estate
        'sale' => '<svg fill="currentColor" height="16" width="16" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 406.48 406.48" xml:space="preserve"><g><g><path d="M100.672,287.798c-6.25,0-11.334-5.084-11.334-11.334c0-6.25,5.085-11.334,11.334-11.334h2.868 c4.668,0,8.466,3.798,8.466,8.466c0,4.142,3.358,7.5,7.5,7.5c4.142,0,7.5-3.358,7.5-7.5c0-12.939-10.527-23.466-23.466-23.466 h-2.868c-14.521,0-26.334,11.813-26.334,26.334s11.813,26.334,26.334,26.334c6.25,0,11.334,5.084,11.334,11.334 c0,6.25-5.084,11.333-11.334,11.333h-2.868c-4.668,0-8.466-3.797-8.466-8.465c0-4.142-3.358-7.5-7.5-7.5 c-4.142,0-7.5,3.358-7.5,7.5c0,12.939,10.527,23.465,23.466,23.465h2.868c14.521,0,26.334-11.813,26.334-26.333 S115.193,287.798,100.672,287.798z"/></g></g><g><g><path d="M260.22,314.988c-4.142,0-7.5,3.358-7.5,7.5v2.979h-22.667v-67.836c0-4.142-3.358-7.5-7.5-7.5c-4.142,0-7.5,3.358-7.5,7.5 v75.335c0,4.142,3.358,7.5,7.5,7.5h37.667c4.142,0,7.5-3.358,7.5-7.5v-10.479C267.72,318.345,264.362,314.988,260.22,314.988z"/></g></g><g><g><path d="M324.642,277.684c4.142,0,7.5-3.358,7.5-7.5v-12.554c0-4.142-3.358-7.5-7.5-7.5h-37.668c-4.142,0-7.5,3.358-7.5,7.5 v75.335c0,4.142,3.358,7.5,7.5,7.5h37.668c4.142,0,7.5-3.358,7.5-7.5v-10.479c0-4.142-3.358-7.5-7.5-7.5 c-4.142,0-7.5,3.358-7.5,7.5v2.979h-22.668v-22.667h13.749c4.142,0,7.5-3.358,7.5-7.5c0-4.142-3.358-7.5-7.5-7.5h-13.749v-22.668 h22.668v5.054C317.142,274.327,320.5,277.684,324.642,277.684z"/></g></g><g><g><path d="M169.03,250.618c-14.386,0-26.09,11.704-26.09,26.09v55.771c0,4.142,3.358,7.5,7.5,7.5c4.142,0,7.5-3.358,7.5-7.5v-17.607 h22.18v17.607c0,4.142,3.358,7.5,7.5,7.5c4.142,0,7.5-3.358,7.5-7.5v-55.771C195.12,262.322,183.416,250.618,169.03,250.618z M180.12,299.871h-22.18v-23.163c0-6.115,4.975-11.09,11.09-11.09s11.09,4.975,11.09,11.09V299.871z"/></g></g><g><g><path d="M360.811,194.053h-33.864L229.368,65.978c4.985-5.933,7.995-13.579,7.995-21.917c0-18.816-15.308-34.124-34.124-34.124 c-18.816,0-34.124,15.308-34.124,34.124c0,8.34,3.012,15.987,7.999,21.921L79.531,194.053H45.669 C20.487,194.053,0,214.54,0,239.722v111.153c0,25.182,20.487,45.669,45.669,45.669h315.142c25.182,0,45.669-20.487,45.669-45.669 V239.722C406.48,214.54,385.993,194.053,360.811,194.053z M203.24,24.938c10.545,0,19.124,8.579,19.124,19.124 c0,10.545-8.579,19.124-19.124,19.124c-10.545,0-19.124-8.579-19.124-19.124C184.116,33.517,192.695,24.938,203.24,24.938z M189.042,75.079c4.327,1.989,9.133,3.106,14.198,3.106c5.067,0,9.875-1.119,14.203-3.108l90.646,118.977h-209.7L189.042,75.079z M391.48,350.874c0,16.911-13.758,30.669-30.669,30.669H45.669C28.758,381.543,15,367.785,15,350.874V239.722 c0-16.911,13.758-30.669,30.669-30.669h315.142c16.911,0,30.669,13.758,30.669,30.669V350.874z"/></g></g></svg>',
        'rent' => '<svg fill="currentColor" height="18" width="18" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve"><g><g><path d="M211.772,239.087c3.797-2.022,7.023-4.548,9.651-7.578c4.702-5.376,7.074-11.776,7.074-18.987 c0-4.872-1.126-9.327-3.302-13.184c-2.15-3.789-5.103-6.946-8.798-9.438c-3.448-2.321-7.501-4.096-11.998-5.265 c-4.275-1.109-9.003-1.673-13.978-1.673h-27.725v94.865h24.149V245.24l26.402,32.589h30.575L211.772,239.087z M203.375,216.9 c-0.725,1.229-1.749,2.21-3.149,3.063c-1.553,0.947-3.499,1.698-5.803,2.21c-2.372,0.512-4.898,0.785-7.578,0.836v-18.133h3.447 c2.202,0,4.378,0.222,6.4,0.648c1.775,0.384,3.354,0.973,4.651,1.749c0.947,0.546,1.698,1.289,2.253,2.202 c0.503,0.828,0.751,1.937,0.751,3.302C204.348,214.494,204.023,215.825,203.375,216.9z"/></g></g><g><g><polygon points="270.899,255.787 270.899,240.486 303.795,240.486 303.795,218.453 270.899,218.453 270.899,205.005  305.271,205.005 305.271,182.963 246.741,182.963 246.741,277.828 305.647,277.828 305.647,255.787"/></g></g><g><g><polygon points="370.466,182.963 370.466,233.805 334.217,182.963 313.668,182.963 313.668,277.828 337.323,277.828  337.323,226.987 373.572,277.828 394.121,277.828 394.121,182.963"/></g></g><g><g><polygon points="401.041,182.963 401.041,205.005 425.199,205.005 425.199,277.828 449.348,277.828 449.348,205.005  473.498,205.005 473.498,182.963"/></g></g><g><g><path d="M512,64V38.4H64V0H38.4v38.4H0V64h38.4v422.4H12.8c-7.074,0-12.8,5.726-12.8,12.8c0,7.074,5.726,12.8,12.8,12.8h76.8 c7.074,0,12.8-5.726,12.8-12.8c0-7.074-5.726-12.8-12.8-12.8H64V64h89.591v38.4H128c-14.14,0-25.6,11.46-25.6,25.6v204.8 c0,14.14,11.46,25.6,25.6,25.6h358.4c14.14,0,25.6-11.46,25.6-25.6V128c0-14.14-11.46-25.6-25.6-25.6h-25.6V64H512z M179.191,64 H435.2v38.4H179.191V64z M486.4,128v204.8H128V128H486.4z"/></g></g></svg>',
        'bedroom' => '<svg width="19" height="19" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;"><path d="M13,9.5l-10,0l0,-3.25c0.002,-0.685 0.565,-1.248 1.25,-1.25l7.5,0c0.685,0.002 1.248,0.565 1.25,1.25l0,3.25Zm-11.5,5.5l0,-3.5c0.003,-1.096 0.904,-1.997 2,-2l9,0c1.096,0.003 1.997,0.904 2,2l0,3.5" style="fill:none;fill-rule:nonzero;stroke:currentColor;stroke-width:.8px;"/><path d="M1.5,15l0,-0.25c0.001,-0.411 0.339,-0.749 0.75,-0.75l11.5,0c0.411,0.001 0.749,0.339 0.75,0.75l0,0.25m-11,-5.5l0,-0.5c0.002,-0.548 0.452,-0.998 1,-1l2.5,0c0.548,0.002 0.998,0.452 1,1l0,0.5m0,0l0,-0.5c0.002,-0.548 0.452,-0.998 1,-1l2.5,0c0.548,0.002 0.998,0.452 1,1l0,0.5" style="fill:none;fill-rule:nonzero;stroke:currentColor;stroke-width:.8px;"/></svg>',
        'bathroom' => '<svg fill="currentColor" width="16" height="16" viewBox="0 0 512 512" id="Layer_1" enable-background="new 0 0 512 512" xmlns="http://www.w3.org/2000/svg"><g><path d="m496 288c-38.154 0-437.487 0-448 0v-56h32c8.837 0 16-7.164 16-16v-40c0-8.836-7.163-16-16-16s-16 7.164-16 16v24h-16v-138.745c0-25.903 31.562-39.064 49.941-20.686l16.94 16.94c-13.424 23.401-10.164 53.835 9.805 73.805l8 8c6.247 6.248 16.379 6.249 22.627 0l64-64c6.249-6.248 6.249-16.379 0-22.627l-8-8c-20.35-20.351-50.837-23.06-73.817-9.817l-16.928-16.928c-11.57-11.57-26.952-17.942-43.313-17.942-33.776 0-61.255 27.479-61.255 61.255v226.745c-8.837 0-16 7.164-16 16s7.163 16 16 16v32c0 43.889 19.742 83.247 50.806 109.681l-22.338 23.229c-9.803 10.193-2.445 27.09 11.53 27.09 4.199 0 8.394-1.644 11.534-4.91l26.218-27.263c19.844 10.326 42.376 16.173 66.25 16.173h192c23.874 0 46.406-5.847 66.25-16.173l26.218 27.263c6.106 6.35 16.234 6.585 22.623.442 6.369-6.125 6.566-16.254.441-22.623l-22.338-23.229c31.064-26.433 50.806-65.791 50.806-109.68v-32c8.837 0 16-7.164 16-16s-7.163-16-16-16zm-310.89-223.738-40.845 40.845c-8.246-11.427-7.23-27.515 3.048-37.794 10.378-10.377 26.461-11.259 37.797-3.051zm278.89 287.738c0 61.757-50.243 112-112 112h-192c-61.757 0-112-50.243-112-112v-32h416z"/></g></svg>',
        'construction' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings" viewBox="0 0 16 16"><path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/><path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z"/></svg>',
        'condo' => '<svg width="20" height="20" viewBox="0 0 1 1" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g><g><path d="M0.167,0.605l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012Zm-0.012,0.025l0,0.008l-0.038,0l0,-0.008l0.038,0Zm-0.038,0.062l0,-0.029l0.038,0l0,0.029l-0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.236,0.716l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012Zm0.012,-0.025l0,-0.029l0.038,0l0,0.029l-0.038,0Zm0.038,-0.062l0,0.008l-0.038,0l0,-0.008l0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.167,0.738l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012Zm-0.012,0.025l0,0.008l-0.038,0l0,-0.008l0.038,0Zm-0.038,0.062l0,-0.029l0.038,0l0,0.029l-0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.466,0.646l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012Zm-0.012,0.025l0,0.008l-0.038,0l0,-0.008l0.038,0Zm-0.038,0.062l0,-0.029l0.038,0l0,0.029l-0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.597,0.646l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012Zm-0.012,0.025l0,0.008l-0.038,0l0,-0.008l0.038,0Zm-0.038,0.062l0,-0.029l0.038,0l0,0.029l-0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.466,0.779l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012Zm-0.012,0.025l0,0.008l-0.038,0l0,-0.008l0.038,0Zm-0.038,0.062l0,-0.029l0.038,0l0,0.029l-0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.701,0.798l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012Zm0.012,-0.025l0,-0.029l0.038,0l0,0.029l-0.038,0Zm0.038,-0.062l0,0.008l-0.038,0l0,-0.008l0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.833,0.798l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012Zm0.012,-0.025l0,-0.029l0.038,0l0,0.029l-0.038,0Zm0.038,-0.062l0,0.008l-0.038,0l0,-0.008l0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.701,0.931l0.063,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.086c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.086c0,0.007 0.006,0.012 0.012,0.012Zm0.012,-0.025l0,-0.029l0.038,0l0,0.029l-0.038,0Zm0.038,-0.062l0,0.008l-0.038,0l0,-0.008l0.038,0Z" style="fill-rule:nonzero;"/><path d="M0.989,0.975l-0.022,-0.003l0,-0.305l0.016,0c0.007,0 0.012,-0.006 0.012,-0.012l0,-0.133c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.286,0l0,-0.029c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.286,0l0,-0.029c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.37,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.133c0,0.007 0.006,0.012 0.012,0.012l0.016,0l0,0.262l-0.019,-0.002c-0.007,-0.001 -0.013,0.004 -0.014,0.011c-0.001,0.007 0.004,0.013 0.011,0.014l0.975,0.131c0.001,0 0.001,0 0.002,0c0.006,0 0.011,-0.004 0.012,-0.011c0.001,-0.007 -0.004,-0.013 -0.011,-0.014Zm-0.047,-0.006l-0.013,-0.002l0,-0.025c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.009,0l0,-0.097c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.097l-0.009,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.007l-0.13,-0.017l0,-0.265l0.274,0l0,0.302l0,0Zm-0.06,-0.039l-0.038,0l0,-0.085l0.038,0l0,0.085Zm0.022,0.025l0,0.009l-0.069,-0.009l0.069,0Zm-0.32,-0.066l-0.038,0l0,-0.085l0.038,0l0,0.085Zm0.022,0.025l0,0.011l-0.079,-0.011l0.079,0Zm0.012,-0.025l-0.009,0l0,-0.097c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.097l-0.009,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.009l-0.13,-0.017l0,-0.267l0.274,0l0,0.303l-0.013,-0.002l0,-0.026c0,-0.007 -0.006,-0.012 -0.012,-0.012Zm0.079,-0.275l0,-0.079l0.274,0l0,0.108l-0.302,0l0,-0.017l0.016,0c0.007,0 0.012,-0.006 0.012,-0.012Zm-0.298,-0.041l0,-0.08l0.274,0l0,0.108l-0.302,0l0,-0.016l0.016,0c0.007,0 0.012,-0.006 0.012,-0.012Zm-0.37,-0.121l0.346,0l0,0.108l-0.329,0c-0,0 -0,-0 -0,-0c-0,0 -0,0 -0,0l-0.016,0l0,-0.108Zm0.028,0.133l0.289,0l0,0.304l-0.013,-0.002l0,-0.027c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.009,0l0,-0.097c0,-0.007 -0.006,-0.012 -0.012,-0.012l-0.063,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.097l-0.009,0c-0.007,0 -0.012,0.006 -0.012,0.012l0,0.01l-0.145,-0.019l0,-0.266Zm0.229,0.263l-0.038,0l0,-0.085l0.038,0l0,0.085Zm-0.06,0.025l0.081,0l0,0.012l-0.081,-0.011l0,-0.001Z" style="fill-rule:nonzero;"/></g></g></svg>',
        'garden' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9 5.25C7.03323 5.25 5.25 7.15209 5.25 9.75C5.25 12.0121 6.60204 13.7467 8.25001 14.1573V10.9014L6.33398 9.62405L7.16603 8.37597L8.792 9.45995L9.87597 7.83398L11.124 8.66603L9.75001 10.7271V14.1573C11.398 13.7467 12.75 12.0121 12.75 9.75C12.75 7.15209 10.9668 5.25 9 5.25ZM3.75 9.75C3.75 12.6785 5.62993 15.2704 8.25001 15.6906V19.5H3V21H21V19.5H18.75V18L18 17.25H12L11.25 18V19.5H9.75001V15.6906C12.3701 15.2704 14.25 12.6785 14.25 9.75C14.25 6.54892 12.0038 3.75 9 3.75C5.99621 3.75 3.75 6.54892 3.75 9.75ZM12.75 19.5H17.25V18.75H12.75V19.5Z" fill="currentColor"/></svg>',
        'store' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shop-window" viewBox="0 0 16 16"><path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h12V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5m2 .5a.5.5 0 0 1 .5.5V13h8V9.5a.5.5 0 0 1 1 0V13a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a.5.5 0 0 1 .5-.5"/></svg>',
        'warehouse' => '<svg width="1rem" height="1rem" viewBox="0 0 1 1" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;"><g><path d="M0.25,0.601l0.5,0m-0.5,0l0,-0.12c0,-0.028 0,-0.042 0.005,-0.053c0.005,-0.009 0.012,-0.017 0.022,-0.022c0.011,-0.005 0.025,-0.005 0.053,-0.005l0.34,0c0.028,0 0.042,0 0.053,0.005c0.009,0.005 0.017,0.012 0.022,0.022c0.005,0.011 0.005,0.025 0.005,0.053l0,0.12m-0.5,0l0,0.35m0.5,-0.35l0,0.35m0.067,-0.741l-0.21,-0.105c-0.039,-0.02 -0.059,-0.03 -0.08,-0.033c-0.018,-0.003 -0.037,-0.003 -0.055,0c-0.021,0.004 -0.04,0.014 -0.08,0.033l-0.21,0.105c-0.048,0.024 -0.072,0.036 -0.09,0.054c-0.016,0.016 -0.027,0.035 -0.035,0.056c-0.008,0.024 -0.008,0.051 -0.008,0.105l0,0.447c0,0.028 0,0.042 0.005,0.053c0.005,0.009 0.012,0.017 0.022,0.022c0.011,0.005 0.025,0.005 0.053,0.005l0.74,0c0.028,0 0.042,0 0.053,-0.005c0.009,-0.005 0.017,-0.012 0.022,-0.022c0.005,-0.011 0.005,-0.025 0.005,-0.053l0,-0.447c0,-0.054 0,-0.081 -0.008,-0.105c-0.007,-0.021 -0.019,-0.04 -0.035,-0.056c-0.018,-0.018 -0.042,-0.03 -0.09,-0.054Z" style="fill:none;fill-rule:nonzero;stroke:currentColor;stroke-width:0.05px;"/></g></svg>',
        'plus-circle'  => '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/></svg>',
        'minus' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16"><path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/></svg>',
        'plus' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"></path></svg>',
        'location' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>',
        'id' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-heading" viewBox="0 0 16 16"><path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/><path d="M3 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m0-5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5z"/></svg>',
        'location' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>',
        'lot' => '<svg width="20" height="20" fill="currentcolor" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><path d="M6.667,10.333l6.333,0c0.92,0 1.667,0.746 1.667,1.667l0,2c0,0.92 -0.746,1.667 -1.667,1.667l-10,0c-0.92,0 -1.667,-0.746 -1.667,-1.667l0,-10c0,-0.92 0.746,-1.667 1.667,-1.667l2,0c0.92,0 1.667,0.746 1.667,1.667l0,6.333Zm-0.724,4l-2.276,0c-0.184,0 -0.333,-0.149 -0.333,-0.333c0,-0.184 0.149,-0.333 0.333,-0.333l2.333,0l0,-1.333l-1.667,0c-0.184,0 -0.333,-0.149 -0.333,-0.333c0,-0.184 0.149,-0.333 0.333,-0.333l1.667,0l0,-1.333l-2.333,0c-0.184,0 -0.333,-0.149 -0.333,-0.333c0,-0.184 0.149,-0.333 0.333,-0.333l2.333,0l0,-1.333l-1.667,0c-0.184,0 -0.333,-0.149 -0.333,-0.333c0,-0.184 0.149,-0.333 0.333,-0.333l1.667,0l0,-1.333l-2.333,0c-0.184,0 -0.333,-0.149 -0.333,-0.333c0,-0.184 0.149,-0.333 0.333,-0.333l2.333,0l0,-1.333l-1.667,0c-0.184,0 -0.333,-0.149 -0.333,-0.333c0,-0.184 0.149,-0.333 0.333,-0.333l1.61,0c-0.137,-0.388 -0.508,-0.667 -0.943,-0.667l-2,0c-0.552,0 -1,0.448 -1,1l0,10c0,0.552 0.448,1 1,1l2,0c0.435,0 0.806,-0.278 0.943,-0.667Zm0.724,-3.333l0,3c0,0.375 -0.124,0.721 -0.333,1l6.667,0c0.552,0 1,-0.448 1,-1l0,-2c0,-0.552 -0.448,-1 -1,-1l-6.333,0Zm4.667,0.667l1.333,0c0.368,0 0.667,0.298 0.667,0.667l0,1.333c0,0.368 -0.298,0.667 -0.667,0.667l-1.333,0c-0.368,0 -0.667,-0.298 -0.667,-0.667l0,-1.333c0,-0.368 0.298,-0.667 0.667,-0.667Zm0,0.667l0,1.333l1.333,0l0,-1.333l-1.333,0Z" style="fill-rule:nonzero;"/></svg>',
        'parking' => '<svg width="20" height="20" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;"><path d="M14.678,10.331c-0.229,-0.304 -1.08,-0.513 -1.44,-1.061c-0.36,-0.547 -0.655,-1.732 -1.571,-2.187c-0.916,-0.455 -2.668,-0.583 -3.668,-0.583c-1,0 -2.75,0.125 -3.668,0.582c-0.918,0.457 -1.211,1.641 -1.571,2.188c-0.36,0.546 -1.211,0.758 -1.44,1.062c-0.229,0.304 -0.39,2.226 -0.292,3.169c0.098,0.943 0.281,1.5 0.281,1.5l2.688,0c0.44,0 0.583,-0.165 1.483,-0.25c0.987,-0.094 1.956,-0.125 2.519,-0.125c0.562,0 1.562,0.031 2.549,0.125c0.9,0.085 1.048,0.25 1.483,0.25l2.656,0c0,0 0.183,-0.557 0.281,-1.5c0.098,-0.943 -0.064,-2.865 -0.292,-3.169Zm-2.178,4.669l1.75,0l0,0.5l-1.75,0l0,-0.5Zm-10.75,0l1.75,0l0,0.5l-1.75,0l0,-0.5Z" style="fill:none;fill-rule:nonzero;stroke:currentColor;stroke-width:.8px;"/><path d="M11.39,12.661c-0.185,-0.213 -0.787,-0.392 -1.583,-0.511c-0.797,-0.119 -1.088,-0.15 -1.8,-0.15c-0.713,0 -1.037,0.051 -1.8,0.15c-0.764,0.099 -1.337,0.275 -1.583,0.511c-0.369,0.357 0.172,0.759 0.596,0.807c0.411,0.047 1.233,0.03 2.791,0.03c1.557,0 2.38,0.017 2.791,-0.03c0.424,-0.052 0.926,-0.425 0.589,-0.807Zm2.097,-2.066c-0.004,-0.051 -0.046,-0.092 -0.097,-0.094c-0.369,-0.013 -0.744,0.013 -1.408,0.209c-0.339,0.099 -0.658,0.258 -0.94,0.471c-0.071,0.056 -0.046,0.206 0.043,0.222c0.548,0.064 1.099,0.097 1.651,0.097c0.331,0 0.672,-0.094 0.736,-0.389c0.032,-0.17 0.038,-0.344 0.015,-0.516Zm-10.973,-0c0.004,-0.051 0.046,-0.092 0.097,-0.094c0.369,-0.013 0.744,0.013 1.408,0.209c0.339,0.099 0.658,0.258 0.94,0.471c0.071,0.056 0.046,0.206 -0.043,0.222c-0.548,0.064 -1.099,0.097 -1.651,0.097c-0.331,0 -0.672,-0.094 -0.736,-0.389c-0.032,-0.17 -0.038,-0.344 -0.015,-0.516Z" style="fill-rule:nonzero;"/><path d="M13.5,9l0.5,0m-12,0l0.5,0m-0.062,0.594c0,0 1.448,-0.375 5.562,-0.375c4.114,0 5.562,0.375 5.562,0.375" style="fill:none;fill-rule:nonzero;stroke:currentColor;stroke-width:.8px;"/></svg>',
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
    if (is_home() or is_archive() or is_search()) {
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
            require_once get_template_directory() . '/templates/helpers/extract-gallery-images.php';
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
 * Enqueues specific styles and scripts for property-related templates.
 *
 * This function loads custom CSS and JavaScript files for:
 * - Property archive pages (archive-property.php), including filters,
 *   pagination, and AJAX-powered property loading.
 * - Single property pages (single-property.php), including gallery,
 *   related property slideshow, and parallax effects.
 *
 * It uses custom enqueue helpers (stories_enqueue_style/script)
 * and localizes the AJAX script with the admin-ajax URL.
 *
 * @since 1.0.0
 * @package stories
 */
function properties_templates() {
    if ( is_page_template( 'archive-property.php' ) ) {
        $a  = stories_get_assets();

        stories_enqueue_style( 'breadcrumbs', $a['css']['breadcrumbs'] );
        stories_enqueue_style( 'sidebar', $a['css']['sidebar'] );
        stories_enqueue_style( 'posts-styles', $a['css']['posts-styles'] );

        stories_enqueue_script( 'loop-gallery', $a['js']['loop-gallery'] );
        stories_enqueue_style( 'pagination', $a['css']['pagination'] );
        stories_enqueue_script( 'filters', $a['js']['filters'] );
        stories_enqueue_script( 'filter-listeners', $a['js']['filter-listeners'] );
        stories_enqueue_script( 'reset', $a['js']['reset-properties-filter'] );
        stories_enqueue_script( 'ajax-search-from-other-page', $a['js']['ajax-search'] );
        stories_enqueue_script( 'ajax-properties', $a['js']['ajax-properties'] );

        wp_localize_script('ajax-properties', 'ajax_object', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('filter_properties_nonce')
        ]);
    }

    if ( is_singular( 'property' ) ) {
        $a  = stories_get_assets();

        function unload_parts_header() {
            wp_dequeue_style( 'page' );
        }
        add_action( 'wp_enqueue_scripts', 'unload_parts_header', 100 );

        stories_enqueue_style( 'breadcrumbs', $a['css']['breadcrumbs'] );
        stories_enqueue_style( 'post-gallery-styles', $a['css']['post-gallery-styles'] );
        stories_enqueue_style( 'single-property', $a['css']['single-property'] );
        stories_enqueue_style( 'related-styles', $a['css']['related-styles'] );
            
        stories_enqueue_script( 'post-scripts', $a['js']['post-scripts'] );
        stories_enqueue_script( 'animate-in', $a['js']['animate-in'] );
        stories_enqueue_script( 'loop-gallery', $a['js']['loop-gallery'] );
        stories_enqueue_script( 'post-gallery-script', $a['js']['post-gallery-script'] );
        stories_enqueue_script( 'related-script', $a['js']['related-script'] );
    }
}
add_action( 'wp_enqueue_scripts', 'properties_templates' );