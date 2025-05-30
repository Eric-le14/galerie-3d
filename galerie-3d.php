<?php
/**
 * Plugin Name: Galerie 3D
 * Description: Gère une galerie de commandes d'impression 3D avec images, fichiers STL, description et statut.
 * Version: 1.0
 * Author: Votre Nom
 * Text Domain: galerie3d
 */

// Sécurité : empêche l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes utiles
define('GALERIE3D_VERSION', '1.0');
define('GALERIE3D_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GALERIE3D_PLUGIN_URL', plugin_dir_url(__FILE__));

// Charger les fichiers nécessaires
require_once GALERIE3D_PLUGIN_DIR . 'admin/class-galerie3d-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/galerie3d-shortcode.php';
require_once GALERIE3D_PLUGIN_DIR . 'admin/class-galerie3d-admin-config.php';
require_once GALERIE3D_PLUGIN_DIR . 'admin/guide-materiel-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/g_material-shortcode.php';
//require_once GALERIE3D_PLUGIN_DIR . 'admin/admin-page.php';
// Hook d'activation
function galerie3d_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'p3d_galerie3d';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        client_name VARCHAR(255) NOT NULL,
        description TEXT,
        material VARCHAR(100),
        image_url TEXT,
        image_name VARCHAR(255),
        stl_file_url TEXT,
        status TINYINT(1) DEFAULT 1,
        display TINYINT(1) DEFAULT 1,
        print_date DATE,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'galerie3d_activate');

// Hook de désactivation (facultatif)
function galerie3d_deactivate() {
    // Pas de suppression automatique
}
register_deactivation_hook(__FILE__, 'galerie3d_deactivate');

// Initialiser l'admin
function galerie3d_init_admin() {
    if (is_admin()) {
        $admin = new Galerie3D_Admin();

        // Charger les scripts
        add_action('admin_enqueue_scripts', [$admin, 'enqueue_scripts']);

        // Ajouter les pages admin
        add_action('admin_menu', [$admin, 'add_admin_pages']);
    }
}
add_action('plugins_loaded', 'galerie3d_init_admin');

// Charger le script JS admin (supprimé car déjà fait via la méthode enqueue_scripts de la classe)

// AJAX pour statut toggle
add_action('wp_ajax_galerie3d_toggle_status', function () {
    check_ajax_referer('toggle_status_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Non autorisé');
    }

    global $wpdb;
    $id = intval($_POST['id']);
    $table = $wpdb->prefix . 'p3d_galerie3d';

    $current = $wpdb->get_var($wpdb->prepare("SELECT status FROM $table WHERE id = %d", $id));
    if ($current === null) {
        wp_send_json_error('Commande introuvable');
    }

    $new_status = $current ? 0 : 1;
    $wpdb->update($table, ['status' => $new_status], ['id' => $id]);

    wp_send_json_success(['status' => $new_status]);
});

// Action admin-post fallback pour compatibilité (optionnel)
add_action('admin_post_toggle_status', function () {
    if (!current_user_can('manage_options')) {
        wp_die('Non autorisé');
    }

    if (!isset($_GET['id'], $_GET['status']) || !check_admin_referer('toggle_status_' . intval($_GET['id']))) {
        wp_die('Paramètres invalides');
    }

    global $wpdb;
    $id = intval($_GET['id']);
    $status = intval($_GET['status']);
    $table = $wpdb->prefix . 'p3d_galerie3d';

    $wpdb->update($table, ['status' => $status], ['id' => $id]);

    $redirect_url = remove_query_arg(['action', 'id', 'status', '_wpnonce'], wp_get_referer());
    wp_safe_redirect($redirect_url);
    exit;
});

function p3d_display_all_materials_paginated($atts) {
    $atts = shortcode_atts(array(
        'per_page' => 5,
    ), $atts);

    ob_start();
    ?>
    <div id="p3d-materials-wrapper" data-page="1" data-per-page="<?php echo intval($atts['per_page']); ?>"></div>
    <div id="p3d-pagination-controls" style="margin-top:20px; text-align:center;">
        <button id="p3d-prev" disabled>← Précédent</button>
        <button id="p3d-next">Suivant →</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('p3d-materials-wrapper');
        const prevBtn = document.getElementById('p3d-prev');
        const nextBtn = document.getElementById('p3d-next');

        function loadPage(page) {
            const perPage = wrapper.getAttribute('data-per-page');

            fetch(`<?php echo admin_url('admin-ajax.php'); ?>?action=p3d_get_materials_page&page=${page}&per_page=${perPage}`)
                .then(res => res.text())
                .then(html => {
                    wrapper.innerHTML = html;
                    wrapper.setAttribute('data-page', page);
                    prevBtn.disabled = page <= 1;
                });
        }

        prevBtn.addEventListener('click', () => {
            let page = parseInt(wrapper.getAttribute('data-page'));
            if (page > 1) loadPage(page - 1);
        });

        nextBtn.addEventListener('click', () => {
            let page = parseInt(wrapper.getAttribute('data-page'));
            loadPage(page + 1);
        });

        loadPage(1);
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('p3d_all_materials_paginated', 'p3d_display_all_materials_paginated');

add_action('admin_enqueue_scripts', function($hook) {
    // Charge ce script uniquement pour la page de ton plugin
    if ($hook !== 'toplevel_page_guide-materiel') return;

    wp_enqueue_script('p3d-material-status', plugin_dir_url(__FILE__) . 'assets/js/admin.js', ['jquery'], null, true);

    wp_localize_script('p3d-material-status', 'p3d_ajax_data', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('p3d_toggle_material_status'),
    ]);
});
add_action('wp_ajax_p3d_toggle_material_status', 'p3d_toggle_material_status_callback');

function p3d_toggle_material_status_callback() {
    check_ajax_referer('p3d_toggle_material_status');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission refusée');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'p3d_materials_g';
    $id = intval($_POST['id']);

    $current_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM $table WHERE id = %d", $id));
    if ($current_status === null) {
        wp_send_json_error('Matériau introuvable');
    }

    $new_status = $current_status ? 0 : 1;

    $wpdb->update($table, ['status' => $new_status], ['id' => $id]);

    wp_send_json_success(['new_status' => $new_status]);
}
