<?php
function p3d_render_divers_cards($atts) {
    global $wpdb;

    // Récupération des réglages enregistrés
$options = get_option('galerie3d_divers_client_options');
$default_per_page = isset($options['per_page']) ? (int) $options['per_page'] : 2;

// Fusion avec attributs du shortcode
$atts = shortcode_atts([
    'per_page' => $default_per_page,
], $atts, 'p3d_divers');


    $paged = max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
    $offset = ($paged - 1) * $atts['per_page'];

    $table = $wpdb->prefix . 'p3d_divers_g';

    // Total des éléments pour la pagination
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 1");

    // Récupération des éléments avec LIMIT et OFFSET
    $divers = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE status = 1 ORDER BY id ASC LIMIT %d OFFSET %d",
        $atts['per_page'],
        $offset
    ), ARRAY_A);

    if (empty($divers)) {
        return '<p>Aucun élément disponible.</p>';
    }

    ob_start();
    echo '<div class="p3d-divers-wrapper">';
    foreach ($divers as $item) {
        echo '<div class="p3d-divers-card">';
        echo '  <h2 class="p3d-divers-name">' . esc_html($item['name']) . '</h2>';
        echo '  <div class="p3d-divers-content">';
        echo '    <div class="p3d-divers-image">';
        echo '      <img src="' . esc_url($item['image_url']) . '" alt="' . esc_attr($item['name']) . '">';
        echo '    </div>';
        echo '    <div class="p3d-divers-description">';
        echo          wp_kses_post(wpautop($item['description']));
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
    echo '</div>';

    // Pagination
    $pagination_links = paginate_links([
    'base' => get_pagenum_link(1) . '%_%',
    'format' => 'page/%#%/',
    'current' => $paged,
    'total' => ceil($total_items / $atts['per_page']),
    'type' => 'array',
    'prev_text' => '« Précédent',
    'next_text' => 'Suivant »',
]);

if (!empty($pagination_links)) {
    echo '<div class="p3d-divers-pagination">';
    foreach ($pagination_links as $link) {
        echo '<span class="p3d-pagination-button">' . $link . '</span>';
    }
    echo '</div>';
}


    return ob_get_clean();
}

add_shortcode('p3d_divers', 'p3d_render_divers_cards');
function p3d_shortcode_guide_description() {
    $description = get_option('guide_divers_description', '');
    return wpautop(do_shortcode($description));
}
add_shortcode('p3d_guide_description', 'p3d_shortcode_guide_description');
