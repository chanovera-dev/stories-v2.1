# Stories V2.1 WordPress Theme - Real Estate Edition

Stories V2.1 es un tema de WordPress moderno, minimalista y altamente especializado en el sector inmobiliario (**Real Estate**). Diseñado originalmente para una experiencia de lectura inmersiva, ha evolucionado para convertirse en una solución robusta para la gestión y despliegue de catálogos de propiedades.

Ha sido creado como una base sólida y flexible, permitiendo extender su funcionalidad mediante un sistema modular de archivos en `inc/` y plantillas personalizadas en carpetas como `loop`, `page`, `single`, y `templates`.

## Especialización en Real Estate

El tema incluye un motor dedicado para bienes raíces con capacidades avanzadas:

*   **Integración con API de EasyBroker:** Sincronización completa de propiedades, incluyendo metadatos, descripciones enriquecidas y galerías de imágenes.
*   **CPT Property:** Tipo de contenido personalizado (`property`) optimizado para listados inmobiliarios.
*   **Dashboard Administrativo:** Panel de control personalizado dentro de WordPress que ofrece una vista panorámica del inventario:
    *   Métricas de propiedades (publicadas, borradores, privadas).
    *   Desglose por tipo de operación (Venta/Renta).
    *   Estadísticas de inventario por tipo de propiedad y ubicación.
    *   Estado y tiempo de la última sincronización con la API.
*   **Filtrado AJAX de Alto Rendimiento:** Sistema de búsqueda y filtros en tiempo real que permite a los usuarios navegar por el catálogo por:
    *   Ubicación (Estado y Ciudad).
    *   Tipo de Operación (Venta / Renta).
    *   Tipo de Propiedad (Casa, Departamento, Bodega, Terreno, etc.).
    *   Rangos de Precio.
    *   Dimensiones de Construcción y Terreno.
*   **Categorías Dinámicas e Inteligentes:** El sistema analiza la base de datos y muestra en los filtros únicamente los tipos de propiedad que tienen inventario disponible, manteniendo la interfaz limpia.
*   **Gestión de Galería Híbrida:** Unifica de forma transparente las imágenes sincronizadas desde EasyBroker con imágenes subidas localmente mediante ACF (Advanced Custom Fields), permitiendo personalización total de los visuales.
*   **Botones de Contacto Automáticos:** Escaneo inteligente del contenido para detectar enlaces de WhatsApp, números telefónicos y correos electrónicos, convirtiéndolos automáticamente en botones de acción inmediata ("call-to-action") en las tarjetas de propiedad.
*   **Normalización de Datos:** Motor interno de traducción y normalización que estandariza los datos de la API para garantizar la precisión en las búsquedas y la consistencia visual.

## Características Generales

*   **Diseño "Mobile-First":** Maquetación responsiva premium utilizando CSS moderno (Nesting, Variables) y `theme.json`.
*   **Modo Noche Nativo:** Activado automáticamente según la configuración del sistema operativo del usuario.
*   **Carga Condicional de Recursos:** JavaScript y CSS se cargan solo cuando son necesarios, eliminando el "bloating" y optimizando el Core Web Vitals.
*   **Animaciones de Entrada:** Sistema `animate-in.js` para entradas fluidas de elementos al hacer scroll, optimizado para evitar "glitches" visuales.
*   **Formatos de Post Personalizados:** Soporte para Asides, Galerías, Imágenes, Videos, Citas y Enlaces.
*   **Sistema de Iconos SVG:** Iconos optimizados integrados directamente en el código para minimizar las peticiones HTTP.
*   **Bloques Gutenberg Personalizados:** Incluye renderizado avanzado para galerías tipo slider y bloques de últimas entradas con filtrado por formato.
*   **SEO y Analítica:** Estructura semántica HTML5 y Google Tag Manager configurable desde el personalizador.

## Estructura del Proyecto

```text
stories-next/
├── assets/                     # Recursos estáticos (CSS, JS, Iconos)
├── inc/                        # Lógica modular
│   ├── core.php                # Configuración principal y carga de assets
│   ├── real-estate-tools.php   # Motor inmobiliario, lógica de filtros, panel de control de propiedades
│   ├── easybroker-sync.php     # Integración con la API de EasyBroker
├── template-parts/             # Fragmentos de plantilla reutilizables
│   ├── loop/                   # Vistas para listas (incluye content-property.php)
│   ├── page/                   # Vistas para páginas estáticas
│   └── single/                 # Vistas para entradas individuales
├── functions.php               # Punto de entrada de la lógica
├── theme.json                  # Configuración global de diseño
└── README.md                   # Documentación del tema
```

## Instalación y Configuración

1.  Copia la carpeta del tema a `/wp-content/themes/`.
2.  Activa el tema desde el panel de administración de WordPress.
3.  Configure sus **API Keys de EasyBroker** desde el Personalizador de WordPress (**Apariencia > Personalizar > API Keys de EasyBroker**).
4.  Utilice el menú de sincronización integrado en el escritorio para importar su inventario.
5.  Acceda a **Propiedades > Dashboard** para ver el estado de su catálogo.

## Créditos

*   **Autor:** ChanoDEV (https://chano.dev)
*   **Licencia:** GNU General Public License v2 or later
