<section id="faq" class="block">
    <div class="content heading">
        <span>Dudas comunes</span>
        <h2 class="title-section">Preguntas Frecuentes (FAQs)</h2>
    </div>
    <div class="content faq-container">
        <?php
        $faqs = [
            [
                'q' => '¿Pueden borrar noticias o comentarios negativos de Google e Internet?',
                'a' => 'La verdad cruda: Borrar internet es casi imposible, pero enterrar el contenido negativo sí lo es. Utilizamos técnicas de Reverse SEO y SERP Suppression para desplazar los resultados negativos a la página 2 o 3 de Google, donde nadie mira. En casos de difamación ilegal, activamos protocolos legales para la eliminación directa (Right to be Forgotten).'
            ],
            [
                'q' => '¿Es esto confidencial? Tengo miedo de que se sepa que contraté ayuda.',
                'a' => 'La confidencialidad es nuestra religión. Firmamos un NDA (Acuerdo de Confidencialidad) blindado antes de que nos cuentes el primer detalle. Nuestros casos de éxito son anónimos porque el mejor manejo de crisis es el que nadie nota que fue gestionado.'
            ],
            [
                'q' => '¿Cuánto cuesta una gestión de crisis?',
                'a' => 'Una crisis mal gestionada cuesta millones en acciones, contratos perdidos y reputación. Nuestros honorarios se basan en la complejidad y la urgencia (Retainer de Emergencia + Costos operativos). Es una inversión en la supervivencia de tu marca, no un gasto.'
            ],
            [
                'q' => '¿Qué tan rápido pueden empezar?',
                'a' => 'Inmediatamente. Tenemos equipos de guardia 24/7. Si nos llamas ahora, en menos de 60 minutos estaremos analizando tu situación. El tiempo es el recurso más escaso en una crisis.'
            ],
            [
                'q' => 'Mi abogado dice que no diga nada, ¿debo hacerle caso?',
                'a' => 'Los abogados protegen tu libertad/patrimonio; nosotros protegemos tu reputación. A veces, el silencio legal (“no comment”) se interpreta socialmente como culpabilidad. Trabajamos junto a tu equipo legal para asegurar que lo que digas no te incrimine, pero sí te defienda ante la opinión pública.'
            ],
            [
                'q' => '¿Cómo sé si estoy realmente en una crisis de imagen?',
                'a' => 'Si hay rumores, acusaciones, comentarios negativos, notas en prensa o publicaciones virales que afectan tu reputación o la de tu empresa, ya estás en una crisis o en riesgo de entrar en una. Mientras más rápido actúes, menor será el daño.'
            ],
            [
                'q' => '¿Pueden ayudarme si la crisis ya está muy avanzada?',
                'a' => 'Sí. No importa el nivel de exposición actual: podemos actuar en cualquier etapa, incluso si la conversación ya escaló a medios, redes sociales o entornos legales.'
            ],
            [
                'q' => '¿Atienden a personas y empresas?',
                'a' => 'Sí. Trabajamos con figuras públicas, profesionales, marcas personales, empresas de cualquier tamaño y equipos directivos.'
            ],
            [
                'q' => '¿Qué incluye la gestión de crisis?',
                'a' => 'Análisis y monitoreo 24/7, Estrategias de contención, Preparación de mensajes, comunicados y vocería, Control de narrativa y reputación digital, Manejo de medios y respuesta pública, Plan de reconstrucción y reputación positiva.'
            ],
            [
                'q' => '¿Qué pasa cuando la crisis termina?',
                'a' => 'Trabajamos en tu reconstrucción de imagen, posicionamiento positivo y prevención para que no vuelva a suceder.'
            ],
            [
                'q' => '¿Pueden ayudarme aunque el problema haya sido interno o por una acción propia?',
                'a' => 'Sí. No emitimos juicios; trabajamos en soluciones. Nos enfocamos en reparar el daño y construir un camino reputacional sólido.'
            ]
        ];

        foreach ($faqs as $index => $faq):
            $activeClass = ($index === 0) ? 'active' : '';
            $expanded = ($index === 0) ? 'true' : 'false';
            ?>
            <div class="faq-item <?php echo $activeClass; ?>">
                <button class="faq-question" aria-expanded="<?php echo $expanded; ?>"
                    aria-controls="faq-answer-<?php echo $index; ?>">
                    <?php echo $faq['q']; ?>
                    <?= stories_get_icon('chevron-down'); ?>
                </button>
                <div id="faq-answer-<?php echo $index; ?>" class="faq-answer" role="region">
                    <div class="faq-answer-content">
                        <p>
                            <?php echo $faq['a']; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');

                // If clicking an active item, we might want to close it, 
                // but the requirement "opening only one at a time" often implies 
                // that one must be open if it's an accordion that doesn't collapse fully, 
                // or simply that we close others before opening.

                // Close all items
                faqItems.forEach(i => {
                    i.classList.remove('active');
                    i.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
                });

                // Open clicked item if it wasn't active
                if (!isActive) {
                    item.classList.add('active');
                    question.setAttribute('aria-expanded', 'true');
                }
            });
        });
    });
</script>