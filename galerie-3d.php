<?php
/**
 * Plugin Name: Galerie 3D
 * Plugin URI: https://github.com/Eric-le14/galerie-3d
 * Description: Plugin WordPress pour gérer une galerie 3D avec fiches matériaux, réalisations, et contenus associés.
 * Version: 1.1.0
 * Author: Eric-le14
 * Author URI: https://github.com/Eric-le14
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: galerie-3d
 * Domain Path: /languages
 *
 * GitHub Plugin URI: https://github.com/Eric-le14/galerie-3d
 * GitHub Branch: main
 */



// Sécurité : empêche l'accès direct
if (!defined('ABSPATH')) exit;

// Définir les constantes utiles
define('GALERIE3D_VERSION', '1.0');
define('GALERIE3D_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GALERIE3D_PLUGIN_URL', plugin_dir_url(__FILE__));

// Chargement des fichiers du plugin
require_once GALERIE3D_PLUGIN_DIR . 'includes/activation.php';
register_activation_hook(__FILE__, 'galerie3d_activate');
require_once GALERIE3D_PLUGIN_DIR . 'includes/ajax.php';
require_once GALERIE3D_PLUGIN_DIR . 'includes/shortcodes.php';

require_once GALERIE3D_PLUGIN_DIR . 'admin/class-galerie3d-admin.php';
require_once GALERIE3D_PLUGIN_DIR . 'admin/class-galerie3d-admin-config.php';
require_once GALERIE3D_PLUGIN_DIR . 'admin/guide-materiel-admin.php';
require_once GALERIE3D_PLUGIN_DIR . 'admin/guide-divers-admin.php';

require_once GALERIE3D_PLUGIN_DIR . 'public/galerie3d-shortcode.php';
require_once GALERIE3D_PLUGIN_DIR . 'public/g_material-shortcode.php';
require_once GALERIE3D_PLUGIN_DIR . 'public/divers-shortcode.php';

// Initialiser l'admin
add_action('plugins_loaded', function () {
    if (is_admin()) {
        $admin = new Galerie3D_Admin();
        add_action('admin_enqueue_scripts', [$admin, 'enqueue_scripts']);
        add_action('admin_menu', [$admin, 'add_admin_pages']);
    }
});

// Assets publics
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('galerie3d-public', GALERIE3D_PLUGIN_URL . 'public/css/galerie3d-public.css');
});
