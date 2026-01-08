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
                        <h2>"En menos de 48 horas controlaron la narrativa y reencauzaron la conversación.
                            Profesionalismo total.”</h2>
                        <footer>
                            <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt=""
                                width="36" height="36" loading="lazy">
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
                        <h2>"Carolina y su equipo no solo nos sacaron de una crisis de PR internacional, sino que
                            nos dejaron con mejores protocolos para el futuro. Su análisis de datos en tiempo real
                            fue crucial."</h2>
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
                            <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt=""
                                width="36" height="36" loading="lazy">
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
                        <h2>"Su capacidad para anticipar movimientos mediáticos y proteger nuestra reputación fue
                            determinante. Un servicio de élite para situaciones críticas."</h2>
                        <footer>
                            <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt=""
                                width="36" height="36" loading="lazy">
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
                        <h2>"Su intervención fue quirúrgica y efectiva. Lograron contener un desastre mediático
                            inminente con una estrategia de comunicación impecable."</h2>
                        <footer>
                            <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt=""
                                width="36" height="36" loading="lazy">
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
                        <h2>"Más que una consultoría, fueron el escudo estratégico que necesitábamos. Su gestión de
                            la crisis nos permitió salvar años de construcción de marca en cuestión de días."</h2>
                        <footer>
                            <img src="<?= get_template_directory_uri(); ?>/assets/img/testimony-1.webp" alt=""
                                width="36" height="36" loading="lazy">
                            <p>Andrés Q.</p>
                            <span>Director Regional de Operaciones</span>
                        </footer>
                    </div>
                </blockquote>
            </div>
            <div class="navigation">
                <button id="testimonies--backward-button" class="testimonies-prev btn-pagination small-pagination"
                    aria-label="<?= __('Anterior', 'stories'); ?>"><?= stories_get_icon('backward'); ?></button>
                <div class="testimonies-bullets"></div>
                <button id="testimonies--forward-button" class="testimonies-next btn-pagination small-pagination"
                    aria-label="<?= __('Siguiente', 'stories'); ?>"><?= stories_get_icon('forward'); ?></button>
            </div>
        </div>
    </div>
</section>