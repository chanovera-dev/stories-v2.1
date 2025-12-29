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
    <section id="hero" class="block">
        <div class="content">
            <span>Gestión de crisis</span>
            <h1 class="page-title">Recupera el control <i>de tu narrativa</i></h1>
            <p>Gestionamos crisis de alto perfil, control de daños y protección reputacional. Te acompañamos hasta recuperar el control de la narrativa.</p>
            <div class="cta-buttons">
                <button class="btn"><?= stories_get_icon('phone'); ?>Es una emergencia</button>
                <button class="btn hollow">Diagnóstico confidencial</button>
            </div>
        </div>
    </section>
    <section id="crisis" class="block">
        <div class="content">
            <video autoplay muted playsinline loop preload="metadata" role="presentation" id="video-on-crisis" class="background-video-shortcode started" data-ratio="1.7777777777778" width="640" height="360">
                <source type="video/mp4" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/video/thunder-storm.mp4">
            </video>
            <div class="text">
                <h2 class="title-section">Una crisis no espera</h2>
                <h3 class="subtitle-section">El silencio tampoco</h3>
                <p>Las crisis de reputación pueden surgir por errores internos, ataques externos, filtraciones, controversias o malentendidos. Su alcance puede volverse viral en minutos. Nuestra agencia identifica la raíz del problema, controla la narrativa y diseña estrategias de recuperación a corto y largo plazo.</p>
                <ul>
                    <li><?= stories_get_icon('exclamation-triangle-fill'); ?>¿Estás bajo ataque en redes sociales o prensa?</li>
                    <li><?= stories_get_icon('unlock'); ?>¿Se ha filtrado información sensible?</li>
                    <li><?= stories_get_icon('search'); ?>¿Tu marca personal o corporativa enfrenta un escrutinio legal o ético?</li>
                </ul>
                <button class="btn hollow"><?= stories_get_icon('phone'); ?>Escribe el final de tu historia</button>
            </div>
        </div>
    </section>
    <section id="metodology" class="block">
        <div class="content title-wrapper--metodology">
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
                    <p>Acción rápida para detener la hemorragia informativa. Tomamos el control de los canales y la narrativa desde el minuto uno.</p>
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
                    <p>Preparamos a sus portavoces y alineamos la estrategia legal con la comunicacional para controlar la narrativa.</p>
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
                    <p>Restauramos la confianza a largo plazo y limpiamos su huella digital mediante técnicas avanzadas de SEO y contenido.</p>
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

        <hr>

        <div class="content title-wrapper--services">
            <h2 class="title-section">Servicios especializados</h2>
            <h3 class="subtitle-section">Soluciones diseñadas para proteger tu reputación.</h3>
        </div>

        <div class="content services">
            <div class="card">
                <div class="icon">
                    <?= stories_get_icon('clock'); ?>
                </div>
                <h4>Gestión de crisis en tiempo real</h4>
                <p>Contención inmediata, monitoreo de medios y estrategias de respuesta.</p>
            </div>
            <div class="card">
                <div class="icon">
                    <?= stories_get_icon('control'); ?>
                </div>
                <h4>Control de narrativa y comunicación estratégica</h4>
                <p>Mensajes oficiales, vocería, preparación de respuestas y manejo de prensa.</p>
            </div>
            <div class="card">
                <div class="icon">
                    <?= stories_get_icon('lightning'); ?>
                </div>
                <h4>Reputación digital y eliminación de contenido dañino</h4>
                <p>SEO de emergencia, reducción de impacto y contramedidas digitales.</p>
            </div>
            <div class="card">
                <div class="icon">
                    <?= stories_get_icon('lightning'); ?>
                </div>
                <h4>Reconstrucción de imagen y posicionamiento positivo</h4>
                <p>Análisis reputacional, plan de reintegración mediática y storytelling estratégico.</p>
            </div>
            <div class="card">
                <div class="icon">
                    <?= stories_get_icon('lightning'); ?>
                </div>
                <h4>Simulación y prevención de crisis</h4>
                <p>Auditoría de riesgo, manuales de crisis y capacitación ejecutiva.</p>
            </div>
        </div>
        <hr>
        <div class="content title-wrapper--cta">
            <h2 class="title-section">Toma las riendas antes de que sea tarde.</h2>
            <h3 class="subtitle-section">Si tu credibilidad está en riesgo o enfrentas una situación pública adversa, necesitas una defensa estratégica inmediata. Protegemos tu legado con discreción y firmeza.</h3>
            <div class="cta-buttons">
                <button class="btn"><?= stories_get_icon('exclamation-diamond-fill'); ?>Es una emergencia</button>
                <button class="btn hollow">Diagnóstico confidencial</button>
            </div>
        </div>
    </section>
    <section id="how-works" class="block">
        <div class="content">
            <div class="text">
                <h2 class="title-section">¿Cómo trabajamos?</h2>
                <h3 class="subtitle-section">Nuestro equipo está formado por ex-directores de medios, abogados penalistas y expertos en ciberinteligencia. Hemos gestionado crisis para CEOs de Fortune 500, figuras políticas y celebridades de alto nivel.</h3>
                <div class="container">
                    <div class="card">
                        <h4>Diagnóstico inmediato (0–3 horas)</h4>
                        <p>Revisión del contexto, detección de riesgos y mapeo de conversaciones.</p>
                    </div>
                    <div class="card">
                        <h4>Plan de contención</h4>
                        <p>Acciones rápidas para frenar el daño.</p>
                    </div>
                    <div class="card">
                        <h4>Gestión activa de reputación</h4>
                        <p>Comunicación estratégica y control de versiones.</p>
                    </div> 
                    <div class="card">
                        <h4>Reconstrucción</h4>
                        <p>Restauración de la confianza y posicionamiento positivo.</p>
                    </div> 
                    <div class="card">
                        <h4>Prevención futura</h4>
                        <p>Protocolos personalizados para evitar nuevas crisis.</p>
                    </div>     
                </div>
            </div>
            <div class="data">
                <div class="card">
                    <div class="icon">
                        <?= stories_get_icon('lightning'); ?>
                    </div>
                    <h4>100%</h4>
                    <p>Discreción bajo contrato (NDA blindado).</p>
                </div>
                <div class="card">
                    <div class="icon">
                        <?= stories_get_icon('lightning'); ?>
                    </div>
                    <h4>24 / 7</h4>
                    <p>Disponibilidad real. Tu emergencia es nuestro horario.</p>
                </div>
                <div class="card">
                    <div class="icon">
                        <?= stories_get_icon('lightning'); ?>
                    </div>
                    <h4>+30 Años</h4>
                    <p>Navegando tormentas mediáticas.</p>
                </div>
            </div>
        </div>
    </section>
    <section id="privacy" class="block">
        <div class="content">
            <div class="icon">
                <?= stories_get_icon('shield-check'); ?>
            </div>
            <h2 class="title-section">Tu privacidad es nuestra prioridad número uno</h2>
            <p>Entendemos que lo más valioso en este momento es la discreción. Utilizamos canales de comunicación encriptados de grado militar desde el primer contacto. Nadie sabrá que nos contrataste, solo verán los resultados.</p>
        </div>
    </section>
    <section id="cases" class="block">
        <div class="content title-wrapper--cases">
            <span>Experiencia comprobada</span>
            <h2 class="title-section">Casos reales y escenarios comunes</h2>
            <h3 class="subtitle-section">Sabemos qué hacer porque lo hemos hecho antes. Entendemos la presión, la urgencia y la necesidad de actuar con precisión.</h3>
        </div>
        <div class="content cases">
            <div class="card red">
                <div class="icons">
                    <div class="icon">
                        <?= stories_get_icon('exclamation-triangle-fill'); ?>
                    </div>
                    <div class="icon">
                        <?= stories_get_icon('unlock'); ?>
                    </div>
                </div>
                <h4>Filtración de información sensible</h4>
                <p>Datos clave expuestos (NDA vulnerado).</p>
                <p>Filtraciones internas o hackeos.</p>
            </div>
            <div class="card orangered">
                <div class="icons">
                    <div class="icon">
                        <?= stories_get_icon('broadcast'); ?>
                    </div>
                    <div class="icon">
                        <?= stories_get_icon('fire'); ?>
                    </div>
                </div>
                <h4>Comentarios virales</h4>
                <p>Contenidos sacados de contexto que escalan rápidamente en redes sociales.</p>
            </div>
            <div class="card blue">
                <div class="icons">
                    <div class="icon">
                        <?= stories_get_icon('file'); ?>
                    </div>
                    <div class="icon">
                        <?= stories_get_icon('hammer'); ?>
                    </div>
                </div>
                <h4>Acusaciones públicas</h4>
                <p>Denuncias en medios o redes que afectan la reputación corporativa.</p>
            </div>
            <div class="card yellow">
                <div class="icons">
                    <div class="icon">
                        <?= stories_get_icon('briefcase'); ?>
                    </div>
                    <div class="icon">
                        <?= stories_get_icon('exclamation-circle'); ?>
                    </div>
                </div>
                <h4>Mala praxis operativa</h4>
                <p>Incidentes con impacto mediático directo sobre las operaciones.</p>
            </div>
            <div class="card purple">
                <div class="icons">
                    <div class="icon">
                        <?= stories_get_icon('domain'); ?>
                    </div>
                    <div class="icon">
                        <?= stories_get_icon('people'); ?>
                    </div>
                </div>
                <h4>Crisis internas que escalan</h4>
                <p>conflictos laborales, huelgas o despidos masivos que saltan a la esfera pública. Gestionamos la comunicación interna y externa simultáneamente.</p>
            </div>
        </div>
    </section>
    <section id="why-us" class="block">
        <div class="content slideshow-wrapper">
            <div class="slideshow">
                <div class="post">
                    <div class="icon">
                        <?= stories_get_icon('support'); ?>
                    </div>    
                    <h4>Disponibilidad 24/7</h4>
                    <p>Siempre alerta</p>
                </div>
                <div class="post">
                    <div class="icon">
                        <?= stories_get_icon('shield-check'); ?>
                    </div>    
                    <h4>Confidencialidad</h4>
                    <p>Absoluta (NDA)</p>
                </div>
                <div class="post">
                    <div class="icon">
                        <?= stories_get_icon('graph'); ?>
                    </div>    
                    <h4>Análisis experto</h4>
                    <p>Basado en datos</p>
                </div>
                <div class="post">
                    <div class="icon">
                        <?= stories_get_icon('speed'); ?>
                    </div>    
                    <h4>Acción rápida</h4>
                    <p>Protocolos listos</p>
                </div>
                <div class="post">
                    <div class="icon">
                        <?= stories_get_icon('communication'); ?>
                    </div>    
                    <h4>Analistas expertos</h4>
                    <p>En comunicación y reputación</p>
                </div>
            </div>
            <div class="navigation">
                <button id="related-products--backward-button" class="slide-prev btn-pagination small-pagination"><?= stories_get_icon('backward'); ?></button>
                <div class="related-bullets"></div>
                <button id="related-products--forward-button" class="slide-next btn-pagination small-pagination"><?= stories_get_icon('forward'); ?></button>
            </div>
        </div>
    </section>
    <section id="testimonies" class="block">
        <div class="content">
            <div class="testimonies-wrapper">
                <div class="testimonies">
                    <blockquote class="testimony">
                        <div class="card blue">
                            <div class="stars">
                                <span>⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="quote">
                                <?= stories_get_icon('quote'); ?>
                            </div>
                            <h2>"En menos de 48 horas controlaron la narrativa y reencauzaron la conversación. Profesionalismo total.”</h2>
                            <footer>
                                <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt="" width="36" height="36" loading="lazy">
                                <p>Cliente confidencial</p>
                                <span>Sector Finanzas</span>
                            </footer>
                        </div>
                    </blockquote>
                    <blockquote class="testimony">
                        <div class="card">
                            <div class="stars">
                                <span>⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="quote">
                                <?= stories_get_icon('quote'); ?>
                            </div>
                            <h2>"Carolina y su equipo no solo nos sacaron de una crisis de PR internacional, sino que nos dejaron con mejores protocolos para el futuro. Su análisis de datos en tiempo real fue crucial."</h2>
                            <div class="details">
                                <h3>Impacto generado</h3>
                                <ul>
                                    <li>
                                        <span>Recuperación Share Value</span>
                                        <span>+12%</span>
                                    </li>
                                    <li>
                                        <span>Menciones Negativas</span>
                                        <span>-92%</span>
                                    </li>
                                    <li>
                                        <span>Tiempo de Resolución</span>
                                        <span>48 Horas</span>
                                    </li>
                                </ul>
                            </div>
                            <footer>
                                <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt="" width="36" height="36" loading="lazy">
                                <p>Maria R.</p>
                                <span>Dir. de Comunicación, Retail Multinacional</span>
                            </footer>
                        </div>
                    </blockquote>
                    <blockquote class="testimony">
                        <div class="card">
                            <div class="stars">
                                <span>⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="quote">
                                <?= stories_get_icon('quote'); ?>
                            </div>
                            <h2>"Su capacidad para anticipar movimientos mediáticos y proteger nuestra reputación fue determinante. Un servicio de élite para situaciones críticas."</h2>
                            <footer>
                                <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt="" width="36" height="36" loading="lazy">
                                <p>CEO de Multinacional</p>
                                <span>Sector Logística</span>
                            </footer>
                        </div>
                    </blockquote>
                    <blockquote class="testimony">
                        <div class="card blue">
                            <div class="stars">
                                <span>⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="quote">
                                <?= stories_get_icon('quote'); ?>
                            </div>
                            <h2>"Su intervención fue quirúrgica y efectiva. Lograron contener un desastre mediático inminente con una estrategia de comunicación impecable."</h2>
                            <footer>
                                <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt="" width="36" height="36" loading="lazy">
                                <p>VP Legal</p>
                                <span>Sector Corporativo</span>
                            </footer>
                        </div>
                    </blockquote>
                    <blockquote class="testimony">
                        <div class="card">
                            <div class="stars">
                                <span>⭐⭐⭐⭐⭐</span>
                            </div>
                            <div class="quote">
                                <?= stories_get_icon('quote'); ?>
                            </div>
                            <h2>"Más que una consultoría, fueron el escudo estratégico que necesitábamos. Su gestión de la crisis nos permitió salvar años de construcción de marca en cuestión de días."</h2>
                            <footer>
                                <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt="" width="36" height="36" loading="lazy">
                                <p>Andrés Q.</p>
                                <span>Director Regional de Operaciones</span>
                            </footer>
                        </div>
                    </blockquote>
                </div>
                <div class="navigation">
                    <button id="testimonies--backward-button" class="testimonies-prev btn-pagination small-pagination"><?= stories_get_icon('backward'); ?></button>
                    <div class="testimonies-bullets"></div>
                    <button id="testimonies--forward-button" class="testimonies-next btn-pagination small-pagination"><?= stories_get_icon('forward'); ?></button>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" class="block">
        <div class="content">
            <div>
                <span>Contacto directo</span>
                <h2 class="title-section">No pierdas más tiempo.</h2>
                <h3 class="subtitle-section">Cada segundo cuenta.</h3>
                <p>El primer paso para resolver la crisis es admitir que necesitas ayuda profesional. Hablemos ahora.</p>
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
                        <p>"Su capacidad para anticipar movimientos mediáticos y proteger nuestra reputación fue determinante. Un servicio de élite para situaciones críticas."</p>
                        <footer>
                            <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt="" width="36" height="36" loading="lazy">
                            <p>CEO de Multinacional</p>
                            <span>Sector Logística</span>
                        </footer>
                    </div>
                </blockquote>
            </div>
            <div>
                <?php echo do_shortcode('[contact-form-7 id="58b5fc6" title="Formulario de contacto 1"]'); ?>
            </div>
        </div>
    </section>
</main>

<?php get_footer();