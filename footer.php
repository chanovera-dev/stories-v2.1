<?php
/**
 * The template for displaying the footer
 *
 * @package stories
 * @since Stories 2.0.0
 */

?>
<footer id="main-footer">
    <section class="block middle-footer">
        <div class="content">
            <div class="about">
                <h3 class="title-section">Sobre <?php bloginfo('title'); ?></h3>
                <p class="site-bio">
                    <?php
                    echo esc_html(
                        get_theme_mod(
                            'stories_bio',
                            __('Relatos y Cartas es un espacio dedicado a la creatividad y la expresión a través de las palabras. Aquí encontrarás cuentos, microcuentos, poemas e historias que buscan inspirar, emocionar y conectar con los lectores.', 'stories')
                        )
                    );
                    ?>
                </p>
                <?php
                wp_nav_menu(
                    array(
                        'container_id' => 'social',
                        'container_class' => 'social',
                        'theme_location' => 'social',
                    )
                );
                ?>
            </div>
            <div class="other-links">
                <?php
                $footer_menus = ['footer-1', 'footer-2', 'footer-3'];
                $menu_locations = get_nav_menu_locations();

                foreach ($footer_menus as $location):
                    if (isset($menu_locations[$location])):
                        $menu_id = $menu_locations[$location];
                        $menu_obj = wp_get_nav_menu_object($menu_id);
                        $menu_items = wp_get_nav_menu_items($menu_id);

                        if (!empty($menu_items)): ?>
                            <div class="group-links">
                                <h3 class="title-section"><?php echo esc_html($menu_obj->name); ?></h3>
                                <?php
                                wp_nav_menu([
                                    'container' => 'nav',
                                    'container_class' => 'footer',
                                    'theme_location' => $location,
                                ]);
                                ?>
                            </div>
                        <?php endif;
                    endif;
                endforeach;
                ?>
            </div>
        </div>
    </section>
    <section class="block end-footer">
        <div class="content">
            <p>© <?php bloginfo('name');
            echo ' ' . date("Y"); ?> • <?= __('Todos los Derechos Reservados', 'stories') ?>
            </p>
        </div>
    </section>
</footer>
<?php wp_footer(); ?>
</body>

</html>