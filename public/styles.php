<?php
if (!defined('ABSPATH')) exit;

// Ce fichier sert à ajouter le CSS frontend pour la galerie 3D

function galerie3d_enqueue_styles() {
    wp_enqueue_style('galerie3d-public-style', plugin_dir_url(_FILE_) . 'galerie3d-public.css');
}
add_action('wp_enqueue_scripts', 'galerie3d_enqueue_styles');