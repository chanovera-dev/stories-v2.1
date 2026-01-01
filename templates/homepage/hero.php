<?php
/**
 * Template part for the 'hero' section on the homepage.
 *
 * This section displays the hero section.
 *
 * @package Stories
 * @version 2.1
 */

$span = get_field('hero_span');
$title = get_field('hero_title');
$subtitle = get_field('hero_subtitle');
$phone = get_field('hero_phone');
$phone_label = get_field('hero_phone_label');
$contact = get_field('hero_section_contact_link');
$contact_label = get_field('hero_section_contact_link_label');
?>
<section id="hero" class="block">
    <div class="content hero--content heading">
        <span><?php echo $span; ?></span>
        <?php echo $title; ?>
        <?php echo $subtitle; ?>
        <div class="cta-buttons">
            <button class="btn" onclick="window.location.href='<?= 'tel:' . $phone ?>'">
                <?=
                    stories_get_icon('phone') .
                    $phone_label;
                ?>
            </button>
            <button class="btn hollow" onclick="window.location.href='<?= $contact ?>'">
                <?= $contact_label; ?>
            </button>
        </div>
    </div>
</section>