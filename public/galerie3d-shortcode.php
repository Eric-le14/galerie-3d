<?php
// galerie3d-shortcode.php

add_shortcode('galerie3d', function () {
    global $wpdb;

    $table_name = $wpdb->prefix . 'p3d_galerie3d';
    $images_per_page = 10;

    $paged = max(1, get_query_var('paged') ?: (get_query_var('page') ?: 1));
    $offset = ($paged - 1) * $images_per_page;

    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 1 AND image_url IS NOT NULL AND image_url != ''");

    // 🔧 On récupère aussi le champ image_name
    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT image_url, image_name FROM $table_name 
         WHERE status = 1 AND image_url IS NOT NULL AND image_url != '' 
         LIMIT %d OFFSET %d",
        $images_per_page,
        $offset
    ), ARRAY_A);

    ob_start();

    if ($orders) {
        echo '<div class="galerie3d-grid" id="galerie3d-lightgallery">';
        foreach ($orders as $order) {
            $img = esc_url($order['image_url']);
            $name = esc_attr($order['image_name']);
            echo '<a href="' . $img . '" data-lg-size="1406-1390">';
            echo '<img src="' . $img . '" alt="' . $name . '" title="' . $name . '" width="150" height="150" style="object-fit: cover; border-radius: 8px;" loading="lazy" />';
            echo '</a>';
        }
        echo '</div>';

        $total_pages = ceil($total / $images_per_page);
        echo '<div class="galerie3d-pagination">';
        echo paginate_links([
            'base' => get_pagenum_link(1) . '%_%',
            'format' => 'page/%#%/',
            'current' => $paged,
            'total' => $total_pages,
            'prev_text' => '« Précédent',
            'next_text' => 'Suivant »',
        ]);
        echo '</div>';
    } else {
        echo '<p>Aucune image disponible.</p>';
    }

    return ob_get_clean();
});

add_action('wp_enqueue_scripts', function () {
    // LightGallery CSS & JS
    wp_enqueue_style('lightgallery-css', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css');
    wp_enqueue_script('lightgallery-js', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js', [], null, true);
    wp_enqueue_script('lightgallery-plugins', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.min.js', ['lightgallery-js'], null, true);
    wp_enqueue_script('lightgallery-thumbnail', 'https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/thumbnail/lg-thumbnail.min.js', ['lightgallery-js'], null, true);

    // Initialisation JS
    wp_add_inline_script('lightgallery-thumbnail', "
        document.addEventListener('DOMContentLoaded', function () {
            const lightGalleryContainer = document.getElementById('galerie3d-lightgallery');
            if (lightGalleryContainer) {
                lightGallery(lightGalleryContainer, {
                    selector: 'a',
                    plugins: [lgZoom, lgThumbnail],
                    speed: 500
                });
            }
        });
    ");

    // CSS personnalisé
    wp_add_inline_style('lightgallery-css', '
    .galerie3d-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 15px;
        padding: 15px;
    }

    .galerie3d-grid a {
        display: block;
        position: relative;
        overflow: hidden;
        background: #f1f1f1;
        border-radius: 8px;
    }

    .galerie3d-grid img {
        width: 100%;
        height: 100%;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s;
        object-fit: cover;
    }

    .galerie3d-grid img:hover {
        transform: scale(1.03);
    }

    .galerie3d-pagination {
        text-align: center;
        margin: 30px 0;
    }

    .galerie3d-pagination .page-numbers {
        display: inline-block;
        margin: 0 5px;
        padding: 8px 12px;
        background: #f1f1f1;
        border-radius: 5px;
        text-decoration: none;
        color: #0073aa;
    }

    .galerie3d-pagination .page-numbers.current {
        background: #0073aa;
        color: #fff;
    }
    ');
});
