<?php
// Changer le statut d'une réalisation
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
        wp_send_json_error('Introuvable');
    }

    $new_status = $current ? 0 : 1;
    $wpdb->update($table, ['status' => $new_status], ['id' => $id]);

    wp_send_json_success(['status' => $new_status]);
});

// Changer le statut d’un matériau
add_action('wp_ajax_p3d_toggle_material_status', function () {
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
});
