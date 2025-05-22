<?php
/*
Plugin Name: Galerie 3D Impression
Description: Plugin vitrine photo pour réalisations impression 3D avec gestion STL, zoom et pagination.
Version: 1.0.0
Author: TonNom
*/

if (!defined('ABSPATH')) exit;

// Constantes
define('GALERIE3D_DIR', plugin_dir_path(__FILE__));
define('GALERIE3D_URL', plugin_dir_url(__FILE__));

// Inclure les fichiers admin et public
require_once GALERIE3D_DIR . 'admin/menu.php';
require_once GALERIE3D_DIR . 'public/shortcode.php';
require_once GALERIE3D_DIR . 'public/styles.php';

// Activation - création table base de données
register_activation_hook(__FILE__, 'galerie3d_create_db');
function galerie3d_create_db() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'galerie3d_items';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        materiel varchar(100) NOT NULL,
        image_url varchar(255) NOT NULL,
        stl_url varchar(255) DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Désactivation : rien de spécial ici
register_deactivation_hook(__FILE__, function() {
    // Optionnel: nettoyer options ou cache
});