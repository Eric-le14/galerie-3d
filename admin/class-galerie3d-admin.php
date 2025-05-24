<?php

if (!class_exists('Galerie3D_Admin')) {

    class Galerie3D_Admin {

        public function enqueue_scripts() {
    wp_enqueue_script(
        'galerie3d-admin', // handle
        GALERIE3D_PLUGIN_URL . 'admin/js/admin.js',
        ['jquery'],
        GALERIE3D_VERSION,
        true
    );

    wp_localize_script('galerie3d-admin', 'galerie3d', [ // ← même handle ici
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('toggle_status_nonce')
    ]);
}


        public function add_admin_pages() {
            add_menu_page(
                'Galerie 3D',
                'Galerie 3D',
                'manage_options',
                'galerie3d',
                [$this, 'display_orders_page'],
                'dashicons-format-gallery'
            );

            add_submenu_page(
                'galerie3d',
                'Commandes',
                'Commandes',
                'manage_options',
                'galerie3d',
                [$this, 'display_orders_page']
            );

            // Sous-menu invisible pour le formulaire
            add_submenu_page(
                null,
                'Ajouter une commande',
                '', // Titre du menu vide pour ne pas l'afficher
                'manage_options',
                'galerie3d-form',
                [$this, 'display_order_form']
            );

            add_submenu_page(
                'galerie3d',
                'Réglages',
                'Réglages',
                'manage_options',
                'galerie3d-settings',
                ['Galerie3D_Admin_Config', 'render_settings_page']
            );
        }

        private function get_sort_icon($column) {
            $current_orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
            $current_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';

            if ($current_orderby === $column) {
                return $current_order === 'asc' ? ' ↑' : ' ↓';
            }

            return '';
        }

        public function display_orders_page() {
            global $wpdb;

            $table_name = $wpdb->prefix . 'p3d_galerie3d';
            $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

            if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete' && current_user_can('manage_options')) {
                check_admin_referer('galerie3d_delete_order');
                $wpdb->delete($table_name, ['id' => intval($_GET['id'])]);
                wp_redirect(admin_url('admin.php?page=galerie3d&paged=' . $paged));
                exit;
            }

            echo '<div class="wrap">';
            echo '<h1 class="wp-heading-inline">Commandes client</h1>';
            echo '<hr class="wp-header-end">';
            echo '<form method="post">';
            wp_editor(get_option('galerie3d_description', ''), 'galerie3d_description', [
                'textarea_name' => 'galerie3d_description',
                'media_buttons' => true,
                'textarea_rows' => 5,
            ]);
            submit_button('Mettre à jour la description');
            echo '</form>';
            echo '<a href="' . admin_url('admin.php?page=galerie3d-form&paged=' . $paged) . '" class="page-title-action" style="margin-top:15px; display:inline-block;">Ajouter une commande</a>';
            echo '<hr>';

            $items_per_page = 5;
            $offset = ($paged - 1) * $items_per_page;
            $orderby = isset($_GET['orderby']) ? sanitize_sql_orderby($_GET['orderby']) : 'print_date';
            $order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';
            $allowed_columns = ['client_name', 'material', 'status', 'print_date', 'image_name'];
            if (!in_array($orderby, $allowed_columns)) {
                $orderby = 'print_date';
            }

            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $total_pages = ceil($total_items / $items_per_page);

            $orders = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $items_per_page, $offset),
                ARRAY_A
            );

            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>';
            echo '<th>Photo</th>';
            $columns = [
                'image_name' => 'Nom de l’image',
                'client_name' => 'Nom client',
                'material' => 'Matière',
                'status' => 'Statut',
                'print_date' => 'Date d\'impression'
            ];

            foreach ($columns as $key => $label) {
                $is_current = (isset($_GET['orderby']) && $_GET['orderby'] === $key);
                $curr_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';
                $next_order = ($is_current && $curr_order === 'asc') ? 'desc' : 'asc';

                $url = add_query_arg([
                    'orderby' => $key,
                    'order' => $next_order,
                    'paged' => $paged
                ]);

                echo '<th><a href="' . esc_url($url) . '">' . esc_html($label) . $this->get_sort_icon($key) . '</a></th>';
            }

            echo '</tr></thead><tbody>';

            if ($orders) {
                foreach ($orders as $order) {
                    $photo = !empty($order['image_url']) ? '<img src="' . esc_url($order['image_url']) . '" width="80" style="border-radius:4px;" />' : '';
                    $edit_url = admin_url('admin.php?page=galerie3d-form&id=' . $order['id'] . '&paged=' . $paged);
                    $delete_url = wp_nonce_url(admin_url('admin.php?page=galerie3d&action=delete&id=' . $order['id'] . '&paged=' . $paged), 'galerie3d_delete_order');
                    $clone_url = wp_nonce_url(admin_url('admin.php?page=galerie3d-form&action=clone&id=' . $order['id'] . '&paged=' . $paged), 'galerie3d_clone_order');

                    echo '<tr>';
                    echo '<td>' . $photo . '</td>';
                    echo '<td>' . esc_html($order['image_name']) . '</td>';
                    echo '<td>
                            <strong>' . esc_html($order['client_name']) . '</strong>
                            <div class="row-actions">
                                <span class="edit"><a href="' . $edit_url . '">✏️ Modifier</a> | </span>
                                <span class="clone"><a href="' . $clone_url . '">📄 Cloner</a> | </span>
                                <span class="delete"><a href="' . $delete_url . '" onclick="return confirm(\'Supprimer cette commande ?\');">🗑️ Supprimer</a></span>
                            </div>
                          </td>';
                    echo '<td>' . esc_html($order['material']) . '</td>';
                    $status_icon = $order['status'] ? '✔️' : '❌';
                    $toggle_icon = $order['status'] ? '❌ déactiver' : '✔️ activer';
                    echo '<td><strong>' . $status_icon . '</strong><div class="row-actions"><span class="status"><a href="#" class="galerie3d-toggle-status" data-id="' . $order['id'] . '">' . $toggle_icon . '</a></span></div></td>';
                    echo '<td>' . esc_html(date('d/m/Y', strtotime($order['print_date']))) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5">Aucune commande trouvée.</td></tr>';
            }

            echo '</tbody></table>';

            echo '<a href="' . admin_url('admin.php?page=galerie3d-form&paged=' . $paged) . '" class="page-title-action" style="margin-top: 20px;">Ajouter une commande</a>';

            if ($total_pages > 1) {
                echo '<div class="tablenav"><div class="tablenav-pages">';
                echo paginate_links([
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '«',
                    'next_text' => '»',
                    'total' => $total_pages,
                    'current' => $paged
                ]);
                echo '</div></div>';
            }

            echo '</div>';
        }

        public function display_order_form() {
            include plugin_dir_path(__FILE__) . 'galerie3d-form.php';
        }
    }

    add_action('admin_init', function () {
        if (isset($_POST['galerie3d_description']) && current_user_can('manage_options')) {
            update_option('galerie3d_description', wp_kses_post($_POST['galerie3d_description']));
        }
    });
}
add_action('wp_ajax_galerie3d_toggle_status', function () {
    check_ajax_referer('toggle_status_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Non autorisé']);
    }

    global $wpdb;
    $id = intval($_POST['order_id']);
    $table = $wpdb->prefix . 'p3d_galerie3d';

    $current = $wpdb->get_var($wpdb->prepare("SELECT status FROM $table WHERE id = %d", $id));
    if ($current === null) {
        wp_send_json_error(['message' => 'Commande introuvable']);
    }

    $new_status = $current ? 0 : 1;
    $wpdb->update($table, ['status' => $new_status], ['id' => $id]);

    wp_send_json_success(['status' => $new_status]);
});