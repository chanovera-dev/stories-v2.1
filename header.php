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
                <?php
                    $menu_html = wp_nav_menu( array(
                        'theme_location'  => 'primary',
                        'container'       => 'nav',
                        'container_class' => 'main-navigation',
                        'echo'            => false,
                        'fallback_cb'     => false,
                    ) );

                    if ( $menu_html ) {
                        // insertar el backdrop justo después de la apertura del <nav ...>
                        $backdrop = '<div class="glass-backdrop glass-bright" aria-hidden="true"></div>';
                        $menu_html = preg_replace(
                            '/(<nav\b[^>]*class=["\\\'][^"\\\']*main-navigation[^"\\\']*["\\\'][^>]*>)/i',
                            '$1' . $backdrop,
                            $menu_html,
                            1
                        );
                        echo $menu_html;
                    }
                ?>
                <button id="search-mobile__button" class="search-mobile__button btn-pagination small-pagination" onclick="toggleCustomSearchform()" aria-label="Open search">
                    <div class="icon--wrapper">
                        <div class="bar"></div>
                    </div>
                </button>
                <?php if (has_nav_menu('primary')) : ?>
                    <button id="menu-mobile__button" class="menu-mobile__button btn-pagination small-pagination" onclick="toggleMenuMobile()">
                        <span class="bar"></span>
                    </button>
                <?php endif; ?>
                <form role="search" method="get" id="custom-searchform" class="" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="section">
                        <label class="screen-reader-text" for="s"><?php esc_html__('Buscar', 'stories'); ?></label>
                        <input class="wp-block-search__input" type="text" value="" name="s" id="s" placeholder="<?php esc_html_e('Buscar', 'stories'); ?>">
                        <div class="buttons-container">
                            <button type="submit" id="searchsubmit" value="Search" aria-label="Activate the search">
                                <?= stories_get_icon('search'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </header>