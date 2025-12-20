# Stories V2.1 WordPress Theme

Stories V2.1 es un tema de WordPress moderno, minimalista y centrado en el contenido. Diseñado para ofrecer una experiencia de lectura inmersiva, cuenta con una arquitectura modular y optimizada.

Ha sido creado como base para otros proyectos, enlazando otros archivos en functions.php para extenderlo, y de manera similar agregar nuevas plantillas dentro de las carpetas 'loop', 'page', 'single', 'templates', etc.

## Características Principales

*   **Diseño "Mobile-First":** Maquetación responsiva utilizando CSS moderno (Nesting, Variables) y `theme.json`.
*   **Modo noche activado por defecto:** El modo noche viene activado por defecto de manera automática de acuerdo con la configuración del sistema operativo del usuario.
*   **Carga Condicional de Recursos:** JavaScript y CSS se cargan solo cuando son necesarios (lógica en `inc/core.php`), asegurando un rendimiento óptimo.
*   **Formatos de Post Personalizados:** Soporte nativo y estilizado para:
    *   Estándar
    *   Minientradas (Asides)
    *   Galerías (con Slider y Lightbox personalizados)
    *   Imágenes (Dibujos)
    *   Videos
    *   Citas
    *   Enlaces
*   **Sistema de Iconos SVG:** Iconos integrados directamente en PHP para evitar cargas externas y peticiones HTTP adicionales, gestionados mediante `stories_get_icon()`.
*   **Bloques Gutenberg Personalizados:**
    *   **Galería:** Renderizado personalizado que transforma el bloque nativo en un slider interactivo.
    *   **Búsqueda:** Formulario de búsqueda estilizado con iconos SVG.
    *   **Últimas Entradas:** Filtrado avanzado para excluir ciertos formatos de "micro-contenido" (artículos del tipo 'aside', 'quote', 'link', 'image', 'video').
    *   **Listas:** Estilos personalizados para categorías y archivos.
*   **Navegación Avanzada:**
    *   **Menú Compacto:** El menú principal se presenta siempre en modo compacto para dar mayor foco al contenido, pero su comportamiento se puede cambiar fácilmente con CSS.
    *   Migas de pan (Breadcrumbs) dinámicas con lógica profunda para categorías, formatos y fechas.
    *   Paginación numérica y navegación entre posts adyacentes.
*   **SEO y Analítica:** Estructura semántica HTML5, soporte para subida de SVG y Google Tag Manager integrado.
*   **Personalizador:** 
    *   Opción para editar el Google Tag Manager directamente desde el personalizador de WordPress.
    *   Opción para editar la "Biografía del Sitio" directamente desde el personalizador de WordPress.

## Características Específicas de Relatos y Cartas

Esta rama incluye funcionalidades exclusivas diseñadas para la identidad visual de relatosycartas.com:

*   **Efectos Visuales Inmersivos:**
    *   **Nubes en Movimiento:** Efecto atmosférico de fondo (`moving-clouds.js`) que añade profundidad y dinamismo suave.
    *   **Blur Typing:** Efecto de escritura tipo máquina de escribir con desenfoque (`blur-typing.js`) para una introducción dramática de textos.
    *   **Carrusel de Citas:** Rotación interactiva de citas destacadas (`quotes-slideshow.js`).
*   **Plantillas Personalizadas:**
    *   **Detrás del Espejo (DDE):** Plantilla de bucle especializada (`template-parts/loop/content-dde.php`) para la sección "Detrás del Espejo", con un diseño distintivo.
*   **Home Hero:** Estilos dedicados (`home-hero.css`) para la sección principal de la página de inicio, integrando los efectos visuales.
*   **Personalizador Extendido:**
    *   **Datos del Sitio:** Nueva sección para gestionar fácilmente la "Biografía" y el ID de Google Analytics sin tocar código.

## Estructura del Proyecto

```text
stories-next/
├── assets/             # Recursos estáticos (CSS, JS, Iconos, Imágenes)
├── inc/                # Lógica modular del tema
│   └── core.php        # Configuración principal, hooks, assets loader y filtros
├── template-parts/     # Fragmentos de plantilla reutilizables
│   ├── loop/           # Vistas para listas de posts
│   ├── page/           # Vistas para páginas estáticas
│   └── single/         # Vistas para entradas individuales
├── templates/          # Plantillas lógicas (related posts, pagination, tags)
├── functions.php       # Punto de entrada (carga inc/core.php)
├── style.css           # Metadatos del tema y estilos base
└── theme.json          # Configuración global de estilos para Gutenberg
```

## Instalación

1.  Copia la carpeta del tema al directorio `/wp-content/themes/` de tu instalación de WordPress.
2.  Accede al panel de administración > **Apariencia** > **Temas**.
3.  Activa **Stories V2.1**.

## Desarrollo y Personalización

*   **Estilos:** Los estilos están modularizados en `assets/css/` y se cargan condicionalmente dependiendo de la vista (Home, Single, Archive, etc.). `wp-root.css` define las variables globales y tokens de diseño.
*   **Scripts:** La interactividad (galerías, animaciones) se maneja mediante módulos JS en `assets/js/`.
*   **Lógica:** Toda la funcionalidad crítica reside en `inc/core.php` para mantener `functions.php` limpio y ordenado.

## Créditos

*   **Autor:** ChanoDEV (https://chano.dev)
*   **Licencia:** GNU General Public License v2 or later
