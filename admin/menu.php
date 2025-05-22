<?php
if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'galerie3d_admin_menu');

function galerie3d_admin_menu() {
    add_menu_page(
        'Galerie 3D',
        'Galerie 3D',
        'manage_options',
        'galerie3d',
        'galerie3d_page_admin',
        'dashicons-format-gallery',
        26
    );

    add_submenu_page(
        'galerie3d',
        'Réglages',
        'Réglages',
        'manage_options',
        'galerie3d_settings',
        'galerie3d_page_settings'
    );
}

function galerie3d_page_admin() {
    require_once GALERIE3D_DIR . 'admin/admin-view.php';
}

function galerie3d_page_settings() {
    require_once GALERIE3D_DIR . 'admin/settings.php';
}