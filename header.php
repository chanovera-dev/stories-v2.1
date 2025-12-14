<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Stories V2.1
 * @since Stories 2.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="description" content="<?php echo esc_attr(get_bloginfo('description', 'display')); ?>">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php if (function_exists('wp_body_open')) {
        wp_body_open();
    } ?>
    <header id="main-header" role="banner" aria-label="<?php echo esc_attr__('Main header', 'stories'); ?>">
        <div class="glass-backdrop"></div>
        <div class="block">
            <div class="content">
                <button id="menu-mobile__button" class="menu-mobile__button btn-pagination small-pagination" onclick="toggleMenuMobile()">
                    <span class="bar"></span>
                </button>
                <div class="site-brand">
                    <?php
                    if (!has_custom_logo()) {
                        /* Safe site title output */
                        printf('<a href="%s" aria-label="%s">%s</a>', esc_url(home_url('/')), esc_attr__('Home', 'stories'), esc_html(get_bloginfo('name')));
                    } else {
                        the_custom_logo();
                    }
                    ?>
                </div>
                <button id="search-mobile__button" class="search-mobile__button btn-pagination small-pagination" onclick="openCustomSearchform()" aria-label="Open search">
                    <div class="icon--wrapper">
                        <div class="bar"></div>
                    </div>
                </button>
            </div>
        </div>
    </header>