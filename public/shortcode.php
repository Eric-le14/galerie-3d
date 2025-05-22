<?php
if (!defined('ABSPATH')) exit;

function galerie3d_shortcode($atts) {
    global $wpdb;

    // Attributs shortcode avec valeurs par défaut
    $atts = shortcode_atts(array(
        'items_per_page' => 6,
    ), $atts, 'galerie_3d');

    // Récupérer la page actuelle dans l’URL, par défaut 1
    $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    if ($current_page < 1) $current_page = 1;

    $items_per_page = intval($atts['items_per_page']);

    $offset = ($current_page - 1) * $items_per_page;

    // Table personnalisée - adapter selon ta BDD
    $table_name = $wpdb->prefix . 'galerie3d_items';

    // Nombre total d’items
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

    // Récupérer les items pour la page courante
    $items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name LIMIT %d OFFSET %d", $items_per_page, $offset));

    // Début du rendu HTML
    ob_start();

    echo '<div class="galerie3d-container">';
    if ($items) {
        foreach ($items as $item) {
            echo '<div class="galerie3d-item">';
            echo '<img src="' . esc_url($item->image_url) . '" alt="' . esc_attr($item->title) . '" />';
            echo '<div class="galerie3d-title">' . esc_html($item->title) . '</div>';
            echo '<div class="galerie3d-material">' . esc_html($item->material) . '</div>';
            echo '<a href="' . esc_url($item->stl_url) . '" download>Télécharger STL</a>';
            echo '</div>';
        }
    } else {
        echo '<p>Aucun élément trouvé.</p>';
    }
    echo '</div>';

    // Pagination
    $total_pages = ceil($total_items / $items_per_page);
    if ($total_pages > 1) {
        echo '<div class="galerie3d-pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $current_page) {
                echo '<span class="current-page">' . $i . '</span>';
            } else {
                // Garde les autres paramètres de l’URL si besoin
                $url = remove_query_arg('page');
                $url = add_query_arg('page', $i, $url);

                echo '<a href="' . esc_url($url) . '">' . $i . '</a>';
            }
        }
        echo '</div>';
    }

    return ob_get_clean();
}

// Enregistrer le shortcode [galerie_3d]
add_shortcode('galerie_3d', 'galerie3d_shortcode');