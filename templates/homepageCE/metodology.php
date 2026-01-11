<?php
/**
 * Template part for the 'metodology' section on the homepage.
 *
 * This section details the methodology, action pillars, and specialized services.
 *
 * @package Stories
 * @version 2.1
 */
?>
<section id="metodology" class="block">
    <div class="content heading">
        <h2 class="title-section">Metodología de Crisis y Estrategia</h2>
        <h3 class="subtitle-section">Un enfoque estructurado para el análisis y la acción.</h3>
    </div>

    <div class="content metodology">
        <h3 class="title-section">Nuestros pilares de acción</h3>
        <p>Estrategia de "War Room". Soluciones, no excusas.</p>
        <div class="container">
            <div class="card red">
                <div class="icon">
                    <?= stories_get_icon('offline-bolt'); ?>
                </div>
                <h4>Contención inmediata</h4>
                <p><span>[FASE ROJA]</span></p>
                <p>Acción rápida para detener la hemorragia informativa. Tomamos el control de los canales y la
                    narrativa desde el minuto uno.</p>
                <hr>
                <ul>
                    <li>Toma de control de canales de comunicación.</li>
                    <li>Redacción de comunicados de prensa de emergencia.</li>
                    <li>Monitoreo de medios y redes en tiempo real.</li>
                </ul>
                <div class="objetive">
                    <h4>Objetivo</h4>
                    <p>Detener el golpe.</p>
                </div>
            </div>
            <div class="card yellow">
                <div class="icon">
                    <?= stories_get_icon('handshake'); ?>
                </div>
                <h4>Gestión y Respuesta</h4>
                <p><span>[FASE AMBAR]</span></p>
                <p>Preparamos a sus portavoces y alineamos la estrategia legal con la comunicacional para controlar
                    la narrativa.</p>
                <hr>
                <ul>
                    <li>Entrenamiento de portavoces (Media Training Express).</li>
                    <li>Estrategia legal y de comunicación alineada.</li>
                    <li>Negociación con stakeholders y medios.</li>
                </ul>
                <div class="objetive">
                    <h4>Objetivo</h4>
                    <p>Cambiar la narrativa</p>
                </div>
            </div>
            <div class="card green">
                <div class="icon">
                    <?= stories_get_icon('cleaning'); ?>
                </div>
                <h4>Recuperación y Limpieza</h4>
                <p><span>[FASE VERDE]</span></p>
                <p>Restauramos la confianza a largo plazo y limpiamos su huella digital mediante técnicas avanzadas
                    de SEO y contenido.</p>
                <hr>
                <ul>
                    <li>Supresión de contenido negativo en buscadores (SERP Management)</li>
                    <li>Campañas de posicionamiento positivo (Reverse SEO).</li>
                    <li>Reconstrucción de confianza con la audiencia.</li>
                </ul>
                <div class="objetive">
                    <h4>Objetivo</h4>
                    <p>Volver a la normalidad.</p>
                </div>
            </div>
        </div>
    </div>
</section>