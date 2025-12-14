<?php
/**
 * Theme functions and definitions
 *
 * This file acts as the central hub for the Stories V2.1 theme's functionality.
 * It is responsible for:
 *  - Defining theme constants (e.g., version).
 *  - Implementing security measures to prevent direct file access.
 *  - Loading modular components from the /inc directory (core setup, custom features, template tags).
 *
 * The modular architecture ensures maintainability by conditionally loading
 * only the necessary files found in the includes directory.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Stories
 * @since Stories 2.0.0
 */

// Prevent direct access to this file for security reasons.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Theme version constant (safe: only define if not already defined).
 */
$theme = wp_get_theme();
$version = $theme && method_exists($theme, 'get') ? $theme->get('Version') : '1.0.0';

if (!defined('STORIES_VERSION')) {
    define('STORIES_VERSION', (string) $version);
}

/**
 * Load optional theme components from the /inc directory.
 * Note: files are included only if they exist.
 */
$inc_files = array(
    'core' => 'inc/core.php',
);

foreach ($inc_files as $key => $relative_path) {
    $path = __DIR__ . '/' . $relative_path;
    if (file_exists($path)) {
        require_once $path;
    }
}