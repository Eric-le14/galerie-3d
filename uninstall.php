<?php
// Empêche l'exécution directe du script
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Supprimer les options (description enregistrée)
delete_option('galerie3d_description');

// Supprimer la table personnalisée
global $wpdb;
$table_name = $wpdb->prefix . 'p3d_galerie3d';
$wpdb->query("DROP TABLE IF EXISTS $table_name");
