<?php
/**
 * Template part for displaying post author
 *
 * @package stories
 * @since Stories 2.0.0
 */
?>
<div class="post--author">
    <?php
    echo get_avatar(get_the_author_meta('email'), '70') . '<h3 class="author-name">';
    the_author();
    echo '</h3><span class="author-description">';
    the_author_meta('description');
    echo '</span>';
    ?>
</div>