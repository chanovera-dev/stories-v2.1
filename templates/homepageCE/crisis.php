<section id="crisis" class="block">
    <div class="content">
        <video autoplay muted playsinline loop preload="metadata" role="presentation" id="video-on-crisis"
            class="background-video-shortcode started" data-ratio="1.7777777777778" width="640" height="360">
            <source type="video/mp4"
                src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/video/thunder-storm.mp4">
        </video>
        <div class="crisis--text">
            <h2 class="title-section">Una crisis no espera</h2>
            <h3 class="subtitle-section">El silencio tampoco</h3>
            <p>Las crisis de reputación pueden surgir por errores internos, ataques externos, filtraciones,
                controversias o malentendidos. Su alcance puede volverse viral en minutos. Nuestra agencia
                identifica la raíz del problema, controla la narrativa y diseña estrategias de recuperación a corto
                y largo plazo.</p>
            <ul>
                <li><?= stories_get_icon('exclamation-triangle-fill'); ?>¿Estás bajo ataque en redes sociales o
                    prensa?</li>
                <li><?= stories_get_icon('unlock'); ?>¿Se ha filtrado información sensible?</li>
                <li><?= stories_get_icon('search'); ?>¿Tu marca personal o corporativa enfrenta un escrutinio legal
                    o ético?</li>
            </ul>
            <button class="btn hollow"><?= stories_get_icon('phone'); ?>Escribe el final de tu historia</button>
        </div>
    </div>
</section>