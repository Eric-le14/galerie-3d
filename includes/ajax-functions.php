add_action('wp_ajax_p3d_toggle_status', 'p3d_handle_toggle_status');

function p3d_handle_toggle_status() {
    if (!current_user_can('manage_options') || !check_ajax_referer('toggle_status_nonce', 'nonce', false)) {
        wp_send_json_error(['message' => 'Non autorisé.']);
    }

    global $wpdb;
    $id = intval($_POST['id'] ?? 0);
    $table = sanitize_key($_POST['table'] ?? '');

    $allowed_tables = ['p3d_galerie3d', 'p3d_materials_g', 'p3d_divers_g'];
    if (!in_array($table, $allowed_tables)) {
        wp_send_json_error(['message' => 'Table non autorisée.']);
    }

    $full_table = $wpdb->prefix . $table;
    $current_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM $full_table WHERE id = %d", $id));
    if ($current_status === null) {
        wp_send_json_error(['message' => 'ID introuvable.']);
    }

    $new_status = $current_status ? 0 : 1;
    $updated = $wpdb->update($full_table, ['status' => $new_status], ['id' => $id]);

    if ($updated !== false) {
        wp_send_json_success(['new_status' => $new_status]);
    } else {
        wp_send_json_error(['message' => 'Erreur lors de la mise à jour.']);
    }
}
