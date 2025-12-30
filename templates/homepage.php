<?php
/**
 * Template Name: Homepage
 *
 * Página principal enfocada en servicios de Gestión de Crisis, Reputación Digital y Defensa Estratégica.
 * Incluye secciones de metodología, servicios especializados, casos de estudio y protocolos de privacidad.
 *
 * @package Stories
 * @version 2.1
 */

get_header(); ?>

<main id="main" class="site-main" role="main">
    <?php
    $directory = get_template_directory() . '/templates/homepage';

    $sections = [
        'hero',
        'crisis',
        'metodology',
        'services',
        'how-works',
        'privacy',
        'cases',
        'why-us',
        'testimonies',
        'contact',
        'blog' => !empty(get_posts(['post_type' => 'post', 'posts_per_page' => 1])),
        'special' => !empty(get_posts(['post_type' => 'special', 'posts_per_page' => 1])),
    ];

    foreach ($sections as $section => $condition) {
        if (is_int($section)) {
            $section = $condition;
            $condition = true;
        }

        if ($condition && file_exists("$directory/$section.php")) {
            include "$directory/$section.php";
        }
    }
    ?>
</main>

<?php get_footer();