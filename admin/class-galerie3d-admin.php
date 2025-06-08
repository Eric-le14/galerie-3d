<?php
// Vérifie que la classe Galerie3D_Admin n'existe pas déjà
if (!class_exists('Galerie3D_Admin')) {

    class Galerie3D_Admin {

// Affiche le formulaire d’ajout ou édition de matériau (guide-materiel-form.php)
function p3d_render_guide_materiel_form_page() {
    include plugin_dir_path(__FILE__) . 'guide-materiel-form.php';
}

// Affiche le formulaire d’ajout ou édition de matériau (guide-divers-form.php)
function p3d_render_guide_divers_form_page() {
    include plugin_dir_path(__FILE__) . 'guide-divers-form.php';
}

// Affiche le formulaire d’ajout ou édition de réalisation (galerie3d-form.php)
        public function display_realisation_form() {
    include plugin_dir_path(__FILE__) . 'galerie3d-form.php';
}
  
// Charge les scripts et styles du back-office
public function enqueue_scripts() {
    wp_enqueue_script(
        'galerie3d-admin',
        GALERIE3D_PLUGIN_URL . 'admin/js/admin.js',
        ['jquery'],
        GALERIE3D_VERSION,
        true
    );
    wp_enqueue_style(
        'galerie3d-admin-style',
         GALERIE3D_PLUGIN_URL . 'admin/css/admin.css',
         [],
         GALERIE3D_VERSION
    );
   
    
}

 // Enregistre les pages du menu admin WordPress
        public function add_admin_pages() {
            add_menu_page(
                'Galerie 3D',
                'Galerie 3D',
                'manage_options',
                'galerie3d',
                [$this, 'display_realisations_page'],
                'dashicons-format-gallery'
            );

            // Sous-menu : Liste des réalisations
            add_submenu_page(
                'galerie3d',
                'Réalisation',
                'Réalisation',
                'manage_options',
                'galerie3d',
                [$this, 'display_realisations_page']
            );

            // Sous-menu masqué pour le formulaire d'ajout/édition
            add_submenu_page(
                null,
                'Ajouter une Réalisation',
                '', // Titre du menu vide pour ne pas l'afficher
                'manage_options',
                'galerie3d-form',
                [$this, 'display_realisation_form']
            );

            // Sous-menu : Liste des matériaux du guide
            add_submenu_page(
        'galerie3d',
        'Guide Matériel',
        'Guide Matériel',
        'manage_options',
        'guide-materiel',
        'p3d_render_guide_materiel_page'
    );

    // Sous-menu : Liste des divers du guide
            add_submenu_page(
        'galerie3d',
        'Guide divers',
        'Guide divers',
        'manage_options',
        'guide-divers',
        'p3d_render_guide_divers_page'
    );

    // Sous-menu masqué pour le formulaire de matériau
            add_submenu_page(
                null,
                'Formulaire Matériau', 
                '', // Titre du menu vide pour ne pas l'afficher
                'manage_options',
                'guide-materiel-form',
                'p3d_render_guide_materiel_form_page' 
            );

            // Sous-menu masqué pour le formulaire de matériau
            add_submenu_page(
                null,
                'Formulaire divers', 
                '', // Titre du menu vide pour ne pas l'afficher
                'manage_options',
                'guide-divers-form',
                'p3d_render_guide_divers_form_page' 
            );

// Sous-menu : Réglages du plugin
            add_submenu_page(
                'galerie3d',
                'Réglages',
                'Réglages',
                'manage_options',
                'galerie3d-settings',
                ['Galerie3D_Admin_Config', 'render_settings_page']
            );
             // Ajoute les pages au menu admin
           add_action('admin_menu', function() {
           // Cette fonction est redondante ici, mais incluse au cas où tu y ajoutes d'autres actions
            
    
});
        }

// Génère l’icône de tri dans les colonnes du tableau admin
        private function get_sort_icon($column) {
            $current_orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';
            $current_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';

            if ($current_orderby === $column) {
                return $current_order === 'asc' ? ' ↑' : ' ↓';
            }

            return '';
        }


// Affiche la page admin avec la liste des réalisations
        public function display_realisations_page() {    
        global $wpdb;

    $table_name = $wpdb->prefix . 'p3d_galerie3d';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            if (
    isset($_GET['action'], $_GET['id']) &&
    $_GET['action'] === 'toggle_status' &&
    current_user_can('manage_options') &&
    check_admin_referer('galerie3d_toggle_status')
) {
    $id = intval($_GET['id']);
    $current_status = $wpdb->get_var($wpdb->prepare("SELECT status FROM $table_name WHERE id = %d", $id));
    //$new_status = ($current_status == 1) ? 0 : 1;
   // $wpdb->update($table_name, ['status' => $new_status], ['id' => $id]);
    wp_redirect(admin_url('admin.php?page=galerie3d&paged=' . $paged));
    exit;
}
            

 // Suppression d'une réalisation (via lien avec nonce)
    if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete' && current_user_can('manage_options')) {
        check_admin_referer('galerie3d_delete_order');
        $wpdb->delete($table_name, ['id' => intval($_GET['id'])]);
        wp_redirect(admin_url('admin.php?page=galerie3d&paged=' . $paged));
        exit;
    }

// Traitement des actions groupées : activer, désactiver, supprimer
if (isset($_POST['bulk_action'], $_POST['order_ids'], $_POST['galerie3d_bulk_nonce']) &&
    wp_verify_nonce($_POST['galerie3d_bulk_nonce'], 'galerie3d_bulk_action') &&
    current_user_can('manage_options')) {

    $ids = array_map('intval', $_POST['order_ids']);
    if (!empty($ids)) {
        $in_ids = implode(',', $ids);
        switch ($_POST['bulk_action']) {
            case 'enable':
                $wpdb->query("UPDATE $table_name SET status = 1 WHERE id IN ($in_ids)");
                break;
            case 'disable':
                $wpdb->query("UPDATE $table_name SET status = 0 WHERE id IN ($in_ids)");
                break;
            case 'delete':
                $wpdb->query("DELETE FROM $table_name WHERE id IN ($in_ids)");
                break;
        }
        wp_redirect(admin_url('admin.php?page=galerie3d&paged=' . $paged));
        exit;
    }
}









add_action('wp_ajax_p3d_toggle_status', 'p3d_toggle_status_callback');











// --- AFFICHAGE DE LA PAGE ---
    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Réalisation client</h1>';
    echo '<hr class="wp-header-end">';

    // 📝 Éditeur WYSIWYG pour la description
    echo '<form method="post">';
    wp_editor(get_option('galerie3d_description', ''), 'galerie3d_description', [
        'textarea_name' => 'galerie3d_description',
        'media_buttons' => true,
        'textarea_rows' => 5,
    ]);
    submit_button('description page réalisation');
    echo '</form>';

// 🔍 FORMULAIRE DE RECHERCHE
    echo '<form method="get">';
    echo '<input type="hidden" name="page" value="galerie3d">';
    echo '<input type="text" name="search" placeholder="Rechercher par client, date ou image..." value="' . esc_attr($_GET['search'] ?? '') . '" />';
    submit_button('Rechercher', 'secondary', '', false);
    if (!empty($_GET['search'])) {
        echo ' <a href="' . admin_url('admin.php?page=galerie3d') . '">Réinitialiser</a>';
    }
    echo '</form>';
    echo '<br>';

// Lien pour ajouter une réalisation
    echo '<a href="' . admin_url('admin.php?page=galerie3d-form&paged=' . $paged) . '" class="page-title-action" style="margin-top:15px; display:inline-block;">Ajouter une Réalisation</a>';
    echo '<hr>';
 
 // Préparation de la requête : pagination, tri, recherche
    $opts = get_option('galerie3d_admin_options');
$items_per_page = $opts['per_page'] ?? 10;
 //$items_per_page = get_option('galerie3d_admin_per_page', 10);
    $offset = ($paged - 1) * $items_per_page;
    $orderby = isset($_GET['orderby']) ? sanitize_sql_orderby($_GET['orderby']) : 'print_date';
    $order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';
    $allowed_columns = ['id','client_name', 'material', 'status', 'print_date', 'image_name'];
    if (!in_array($orderby, $allowed_columns)) {
        $orderby = 'print_date';
    }

    // 🔍 GESTION DE LA RECHERCHE
    $search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $where_sql = '';
    if (!empty($search_term)) {
        $like = '%' . $wpdb->esc_like($search_term) . '%';
        $where_sql = $wpdb->prepare("WHERE client_name LIKE %s OR print_date LIKE %s OR image_name LIKE %s", $like, $like, $like);
    }

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where_sql");
    $total_pages = ceil($total_items / $items_per_page);

    $orders = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_name $where_sql ORDER BY $orderby $order LIMIT %d OFFSET %d", $items_per_page, $offset),
        ARRAY_A
    );

// Tableau HTML avec toutes les réalisations
    echo '<form method="post">';
wp_nonce_field('galerie3d_bulk_action', 'galerie3d_bulk_nonce');

echo '<select name="bulk_action">';
echo '<option value="">— Action groupée —</option>';
echo '<option value="enable">✔️ Activer</option>';
echo '<option value="disable">❌ Désactiver</option>';
echo '<option value="delete">🗑️ Supprimer</option>';
echo '</select>';
echo '<input type="submit" class="button action" value="Appliquer">';

    // 🧾 TABLEAU
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th class="checkbox-column"><input type="checkbox" id="galerie3d-select-all"></th>';
    echo '<th class="image-column">Photo</th>';
    
    // En-têtes avec tri
    $columns = [
    'id' => 'ID',
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
            'paged' => $paged,
            'search' => $search_term // 👈 Conserve la recherche
        ]);

        echo '<th><a href="' . esc_url($url) . '">' . esc_html($label) . $this->get_sort_icon($key) . '</a></th>';
    }

    echo '</tr></thead><tbody>';

 // Lignes du tableau
    if ($orders) {
        foreach ($orders as $mat) {
            $photo = !empty($mat['image_url']) ? '<img src="' . esc_url($mat['image_url']) . '" width="80" style="border-radius:4px;" />' : '';
            $edit_url = admin_url('admin.php?page=galerie3d-form&id=' . $mat['id'] . '&paged=' . $paged);
            $delete_url = wp_nonce_url(admin_url('admin.php?page=galerie3d&action=delete&id=' . $mat['id'] . '&paged=' . $paged), 'galerie3d_delete_order');
            $clone_url = wp_nonce_url(admin_url('admin.php?page=galerie3d-form&action=clone&id=' . $mat['id'] . '&paged=' . $paged), 'galerie3d_clone_order');

            echo '<tr>';
            echo '<td class="checkbox-column"><input type="checkbox" name="order_ids[]" value="' . intval($mat['id']) . '"></td>';
            echo '<td class="image-column">' . $photo . '</td>';
            echo '<td>' . intval($mat['id']) . '</td>';
            echo '<td>' . esc_html($mat['image_name']) . '</td>';
            echo '<td>
                    <strong>' . esc_html($mat['client_name']) . '</strong>
                    <div class="row-actions">
                        <span class="edit"><a href="' . $edit_url . '">✏️ Modifier</a> | </span>
                        <span class="clone"><a href="' . $clone_url . '">📄 Cloner</a> | </span>
                        <span class="delete"><a href="' . $delete_url . '" onclick="return confirm(\'Supprimer cette Réalisation ?\');">🗑️ Supprimer</a></span>
                    </div>
                  </td>';
            echo '<td>' . esc_html($mat['material']) . '</td>';
    $status_icon = $mat['status'] ? '✔️' : '❌';
$toggle_label = $mat['status'] ? '❌ désactiver' : '✔️ activer';
$id = $mat['id'];

echo '<td>';
echo '<strong>' . $status_icon . '</strong>';
echo '<div class="row-actions">';
echo '<span class="status"><a href="#" class="toggle-status" data-id="' . esc_attr($id) . '">' . esc_html($toggle_label) . '</a></span>';
echo '</div>';
echo '</td>';

    
echo '<td>' . esc_html(date('d/m/Y', strtotime($mat['print_date']))) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6">Aucune Réalisation trouvée.</td></tr>';
    }

    echo '</tbody></table>';
 
    echo '</form>';
    
    echo '<a href="' . admin_url('admin.php?page=galerie3d-form&paged=' . $paged) . '" class="page-title-action" style="margin-top: 20px;">Ajouter une Réalisation</a>';

    // Pagination
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
 ?><script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectAll = document.getElementById("p3d-select-all");
        const checkboxes = document.querySelectorAll(".p3d-select-item");

        if (selectAll) {
            selectAll.addEventListener("change", function () {
                checkboxes.forEach(cb => cb.checked = this.checked);
            });
        }
    });
    </script>
    <script>

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".galerie3d-toggle-status").forEach(link => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                const id = this.dataset.id;
                const linkEl = this;

                fetch(ajaxurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        action: "p3d_toggle_material_status",
                        id: id,
                        _ajax_nonce: "<?php echo wp_create_nonce('p3d_toggle_material_status'); ?>"
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const newStatus = data.data.new_status;
                        const row = linkEl.closest("tr");
                        const statusCell = row.querySelector("td:nth-child(5) strong");
                        statusCell.textContent = newStatus ? "✔️" : "❌";
                        linkEl.textContent = newStatus ? "❌ désactiver" : "✔️ activer";
                    } else {
                        alert("Erreur : " + data.data);
                    }
                })
                .catch(err => alert("Erreur AJAX : " + err));
            });
        });
    });
    </script>
    <?php

}


        
    }

// Sauvegarde la description de la page "Réalisation client"
    add_action('admin_init', function () {
        if (isset($_POST['galerie3d_description']) && current_user_can('manage_options')) {
            update_option('galerie3d_description', wp_kses_post($_POST['galerie3d_description']));
        }
    });
}

