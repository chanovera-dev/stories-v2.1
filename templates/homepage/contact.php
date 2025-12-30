<?php
/**
 * Template part for the 'contact' section on the homepage.
 *
 * This section provides contact information and a lead generation form.
 *
 * @package Stories
 * @version 2.1
 */
?>
<section id="contact" class="block">
    <div class="content">
        <div class="direct">
            <span>Contacto directo</span>
            <h2 class="title-section">No pierdas más tiempo.</h2>
            <h3 class="subtitle-section">Cada segundo cuenta.</h3>
            <p>El primer paso para resolver la crisis es admitir que necesitas ayuda profesional. Hablemos ahora.
            </p>
            <div class="links">
                <div class="link">
                    <div class="icon">
                        <?= stories_get_icon('phone'); ?>
                    </div>
                    <p>LÍNEA ROJA (URGENCIAS)</p>
                    <a href="tel:9211234560">+52 (921) 123-4560</a>
                </div>
                <div class="link">
                    <div class="icon">
                        <?= stories_get_icon('mail-secure'); ?>
                    </div>
                    <p>EMAIL SEGURO</p>
                    <a href="mailto:crisis@carolinaeslava.com">crisis@carolinaeslava.com</a>
                </div>
            </div>
            <blockquote class="testimony">
                <div class="card">
                    <p>"Su capacidad para anticipar movimientos mediáticos y proteger nuestra reputación fue
                        determinante. Un servicio de élite para situaciones críticas."</p>
                    <footer>
                        <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt="" width="36"
                            height="36" loading="lazy">
                        <p>CEO de Multinacional</p>
                        <span>Sector Logística</span>
                    </footer>
                </div>
            </blockquote>
        </div>
        <div class="form">
            <?php echo do_shortcode('[contact-form-7 id="a914ef3" title="Formulario de contacto 1"]'); ?>
        </div>
    </div>
</section>