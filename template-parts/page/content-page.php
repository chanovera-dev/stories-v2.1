<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Stories V2.1
 * @since 2.0.0
 */

?>
<div id="main" class="site-main" role="main">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php wp_breadcrumbs(); ?>
        <header class="block">
            <div class="content">
                <?php
                the_title('<h1 class="page-title">', '</h1>');
                if (get_the_modified_time('d/m/Y')) {
                    echo '<p class="latest-modified">' . esc_html__('Este archivo fue modificado por última vez el ', 'stories') . get_the_modified_time('d/m/Y') . '</p>';
                }
                ?>
            </div>
        </header>
        <section class="block">
            <div class="content">
                <div class="is-layout-constrained">
                    <?php
                    if (has_post_thumbnail()) {
                        echo get_the_post_thumbnail(null, 'full', ['alt' => get_the_title(), 'loading' => 'lazy']);
                    }
                    the_content();
                    ?>
                </div>
                <?php
                if (is_active_sidebar('sidebar-3')) {
                    echo '<aside class="sidebar page-body_sidebar">';
                    dynamic_sidebar('sidebar-3');
                    echo '</aside>';
                }
                ?>
            </div>
        </section>
    </article>
</div>