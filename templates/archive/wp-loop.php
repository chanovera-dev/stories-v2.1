<div class="loop">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            $post_format = get_post_format();
            $part = 'archive';

            if ($post_format) {
                if (locate_template("template-parts/loop/content-{$post_format}.php")) {
                    $part = $post_format;
                }
            }

            get_template_part('template-parts/loop/content', $part);
        }

        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => stories_get_icon('backward') . ' Anterior',
            'next_text' => 'Siguiente' . stories_get_icon('forward')
        ));
    } else {
        echo '<p>' . esc_html__('No se han encontrado artículos', 'stories') . '</p>';
    }
    ?>
</div>