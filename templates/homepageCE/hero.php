<?php
/**
 * Template part for displaying the hero section on the homepage.
 *
 * This section features a main heading, subheading, and call-to-action buttons.
 * All content is managed through Advanced Custom Fields (ACF).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Stories
 * @subpackage Templates/Homepage
 * @since 1.0.0
 * @version 2.1
 */
$hero_span = get_field('hero_span');
$hero_title = get_field('hero_title');
$hero_subtitle = get_field('hero_subtitle');
$hero_phone = get_field('hero_phone');
$hero_phone_label = get_field('hero_phone_label');
$hero_contact = get_field('hero_section_contact_link');
$hero_contact_label = get_field('hero_section_contact_link_label');

if (empty($hero_span) && empty($hero_title) && empty($hero_subtitle) && empty($hero_phone) && empty($hero_contact)) {
    return;
}
?>
<section id="hero" class="block">
    <div class="content hero--content heading">

        <?php if ($hero_span): ?>
            <span><?php echo esc_html($hero_span); ?></span>
        <?php endif; ?>

        <?php
        if ($hero_title) {
            echo wp_kses_post($hero_title);
        }
        if ($hero_subtitle) {
            echo wp_kses_post($hero_subtitle);
        }
        ?>

        <?php if (($hero_phone && $hero_phone_label) || ($hero_contact && $hero_contact_label)): ?>
            <div class="cta-buttons">

                <?php if ($hero_phone && $hero_phone_label): ?>
                    <?php $tel_link = preg_replace('/[^0-9+]/', '', $hero_phone); ?>
                    <a href="tel:<?php echo esc_attr($tel_link); ?>" class=" btn">
                        <?php
                        echo stories_get_icon('phone');
                        echo esc_html($hero_phone_label);
                        ?>
                    </a>
                <?php endif; ?>

                <?php if ($hero_contact && $hero_contact_label): ?>
                    <a href="<?php echo esc_url($hero_contact); ?>" class=" btn hollow">
                        <?php echo esc_html($hero_contact_label); ?>
                    </a>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </div>
</section>