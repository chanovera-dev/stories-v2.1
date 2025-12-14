<?php
/**
 * Template part for displaying single post content
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
                <div class="category post--tags">
                    <?php
                    // $post_format = get_post_format();
                    // if ($post_format) {
                    //     $format_label = '';
                    //     switch ($post_format) {
                    //         case 'aside':
                    //             $format_label = __('Minientrada', 'stories');
                    //             break;
                    //         case 'gallery':
                    //             $format_label = __('Galería', 'stories');
                    //             break;
                    //         case 'image':
                    //             $format_label = __('Dibujo', 'stories');
                    //             break;
                    //         case 'video':
                    //             $format_label = __('Video', 'stories');
                    //             break;
                    //         case 'quote':
                    //             $format_label = __('Cita', 'stories');
                    //             break;
                    //         case 'link':
                    //             $format_label = __('Artículo externo', 'stories');
                    //             break;
                    //     }
                    
                    //     if ($format_label) {
                    //         echo '<a href="' . esc_url(get_post_format_link($post_format)) . '" class="post-tag small">' . stories_get_icon($post_format) . esc_html($format_label) . '</a>';
                    //     }
                    // }
                    
                    // $categories = get_the_category();
                    // if (!empty($categories)) {
                    //     foreach ($categories as $category) {
                    //         $cat_name = esc_html($category->name);
                    //         $cat_link = esc_url(get_category_link($category->term_id));
                    //         $cat_icon = stories_get_icon('category');
                    //         echo "<a href='{$cat_link}' class='post-tag'>{$cat_icon}<span>{$cat_name}</span></a> ";
                    //     }
                    // }
                    ?>
                </div>
                <?php
                the_title('<h1 class="page-title">', '</h1>');
                echo '<div class="metadata"><div class="date">' . stories_get_icon('date') . get_the_date() . '</div>';
                if (get_comments_number() > 0):
                    echo '<div class="comments">';
                    if (get_comments_number() == 1) {
                        echo stories_get_icon('comment') . get_comments_number() . '<span></span>' . esc_html__('Comentario', 'stories');
                    } else {
                        echo stories_get_icon('comment') . get_comments_number() . '<span></span>' . esc_html__('Comentarios', 'stories');
                    }
                    echo '</div>';
                endif;
                ?>
            </div>
        </header>
        <section class="block">
            <div class="content">
                <div class="is-layout-constrained">
                    <?php
                    foreach (['content', 'tags', 'author'] as $part) {
                        get_template_part('templates/single/' . $part);
                    }
                    ?>
                </div>
                <?php
                if (is_active_sidebar('sidebar-2')) {
                    echo '<aside class="sidebar page-body_sidebar">';
                    dynamic_sidebar('sidebar-2');
                    echo '</aside>';
                }
                ?>
            </div>
        </section>
        <section class="block">
            <?php get_template_part('templates/single/single-post-pagination'); ?>
        </section>
        <?php
        get_template_part('templates/single/related', 'posts');
        if (comments_open()):
            ?>
            <section class="block">
                <div class="content content-comments">
                    <?php comments_template(); ?>
                </div>
            </section>
        <?php endif; ?>
    </article>
</div>