<?php  
if (!defined('ABSPATH')) exit;
function p3d_render_guide_divers_form_page() {
    include plugin_dir_path(__FILE__) . 'guide-divers-form.php';
}
 


function p3d_render_guide_divers_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'p3d_divers_g';
 if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = intval($_GET['id']);
    if (wp_verify_nonce($_GET['_wpnonce'], 'p3d_delete_material_' . $id)) {
        $wpdb->delete($table, ['id' => $id]);
        echo '<div class="notice notice-success"><p>Matériau supprimé.</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>Échec de la vérification de sécurité.</p></div>';
    }
}
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guide_divers_description'])) {
        update_option('guide_divers_description', wp_kses_post($_POST['guide_divers_description']));
        echo '<div class="notice notice-success"><p>Description mise à jour.</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1>Bon à savoir</h1>';
echo '<style>
    .widefat tr.has-row-actions:hover td > .row-actions {
        display: block;
    }
    .row-actions {
        font-size: 13px;
        color: #a0a5aa;
        display: none;
    }
    .row-actions a {
        color: #0073aa;
        text-decoration: none;
    }
    .row-actions a:hover {
        text-decoration: underline;
    }
</style>';

    echo '<form method="post">';
    wp_editor(get_option('guide_divers_description', ''), 'guide_divers_description', [
        'textarea_name' => 'guide_divers_description',
        'media_buttons' => true,
        'textarea_rows' => 5,
    ]);
    submit_button('Enregistrer la description');
    echo '</form><hr>';

    echo '<form method="get">';
    echo '<input type="hidden" name="page" value="guide-divers">';
    echo '<input type="text" name="search" placeholder="Rechercher par nom ou description..." value="' . esc_attr($_GET['search'] ?? '') . '" />';
    submit_button('Rechercher', 'secondary', '', false);
    if (!empty($_GET['search'])) {
        echo ' <a href="' . admin_url('admin.php?page=guide-divers') . '">Réinitialiser</a>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['bulk_action'], $_POST['divers_ids'], $_POST['galerie3d_bulk_nonce'])
    && wp_verify_nonce($_POST['galerie3d_bulk_nonce'], 'galerie3d_bulk_action')) {

    $action = sanitize_text_field($_POST['bulk_action']);
    $ids = array_map('intval', $_POST['divers_ids']);

    if (!empty($ids)) {
        switch ($action) {
            case 'enable':
                $wpdb->query("UPDATE $table SET status = 1 WHERE id IN (" . implode(',', $ids) . ")");
                echo '<div class="notice notice-success"><p>Divers activés.</p></div>';
                break;

            case 'disable':
                $wpdb->query("UPDATE $table SET status = 0 WHERE id IN (" . implode(',', $ids) . ")");
                echo '<div class="notice notice-success"><p>Divers désactivés.</p></div>';
                break;

            case 'delete':
                $wpdb->query("DELETE FROM $table WHERE id IN (" . implode(',', $ids) . ")");
                echo '<div class="notice notice-success"><p>Divers supprimés.</p></div>';
                break;
        }
    }
}
$results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
    echo '</form><br>';
    echo '<a href="' . admin_url('admin.php?page=guide-divers-form&action=add') . '" class="page-title-action" style="margin-top:15px;">Ajouter un Divers</a>';

   // echo '<a href="' . admin_url('admin.php?page=galerie3d-form&action=add') . '" class="page-title-action" style="margin-top:15px;">Ajouter un matériau</a>';
    echo '<hr>';

    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $opts = get_option('galerie3d_divers_admin_options');
    $items_per_page = $opts['per_page'] ?? 10;
    
    //$items_per_page = 10;
    $offset = ($paged - 1) * $items_per_page;

    $valid_columns = ['id', 'name', 'description', 'status'];
    $orderby = in_array($_GET['orderby'] ?? '', $valid_columns) ? $_GET['orderby'] : 'id';
    $order = ($_GET['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $where_sql = '';
    if (!empty($search)) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where_sql = $wpdb->prepare("WHERE name LIKE %s OR description LIKE %s", $like, $like);
    }

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table $where_sql");
    $total_pages = ceil($total_items / $items_per_page);

    $query = "SELECT * FROM $table $where_sql ORDER BY $orderby $order LIMIT $items_per_page OFFSET $offset";
    $divers = $wpdb->get_results($query);

    echo '<form method="post">';
wp_nonce_field('galerie3d_bulk_action', 'galerie3d_bulk_nonce');

echo '<select name="bulk_action">';
echo '<option value="">— Action groupée —</option>';
echo '<option value="enable">✔️ Activer</option>';
echo '<option value="disable">❌ Désactiver</option>';
echo '<option value="delete">🗑️ Supprimer</option>';
echo '</select>';
echo '<input type="submit" class="button action" value="Appliquer">';


    function sort_link($column, $label, $current_by, $current_order) {
        $base_url = admin_url('admin.php?page=guide-divers');
        $asc_url = add_query_arg(['orderby' => $column, 'order' => 'asc'], $base_url);
        $desc_url = add_query_arg(['orderby' => $column, 'order' => 'desc'], $base_url);

        $asc_active = ($column === $current_by && $current_order === 'asc');
        $desc_active = ($column === $current_by && $current_order === 'desc');

        $asc_style = $asc_active ? 'font-weight:bold;color:black;' : 'color:#999;';
        $desc_style = $desc_active ? 'font-weight:bold;color:black;' : 'color:#999;';

        return '<span style="display:flex; align-items:center; gap:4px;">' . esc_html($label) .
            '<a href="' . esc_url($asc_url) . '" style="' . $asc_style . ' text-decoration:none;" title="Trier croissant">▲</a>' .
            '<a href="' . esc_url($desc_url) . '" style="' . $desc_style . ' text-decoration:none;" title="Trier décroissant">▼</a>' .
            '</span>';
    }

    echo '<table class="widefat fixed striped"><thead><tr>';
    echo '<th><input type="checkbox" id="p3d-select-all" /></th>';
    echo '<th>' . sort_link('id', 'ID', $orderby, $order) . '</th>';
    echo '<th>' . sort_link('name', 'Nom', $orderby, $order) . '</th>';
    echo '<th>' . sort_link('description', 'Description', $orderby, $order) . '</th>';
    echo '<th>' . sort_link('status', 'Statut', $orderby, $order) . '</th>';
    echo '</tr></thead><tbody>';

    foreach ($divers as $mat) {
    $id = intval($mat->id);
    $name = esc_html($mat->name);
    $edit_url = admin_url("admin.php?page=guide-divers-form&action=edit&id=$id");
    $clone_url = admin_url("admin.php?page=guide-divers-form&action=clone&id=$id");
    $delete_url = wp_nonce_url(admin_url("admin.php?page=guide-divers&action=delete&id=$id"), 'p3d_delete_material_' . $id);

    echo '<tr class="has-row-actions">';
    echo '<td><input type="checkbox" class="p3d-select-item" name="divers_ids[]" value="' . esc_attr($id) . '" /></td>';
    echo '<td>' . esc_html($id) . '</td>';
    echo '<td>';
    echo '<strong>' . $name . '</strong>';
    echo '<div class="row-actions">';
    echo '<span class="edit"><a href="' . esc_url($edit_url) . '">Modifier</a> | </span>';
    echo '<span class="clone"><a href="' . esc_url($clone_url) . '">Cloner</a> | </span>';
    echo '<span class="delete"><a href="' . esc_url($delete_url) . '" onclick="return confirm(\'Confirmer la suppression ?\')">Supprimer</a></span>';
    echo '</div>';
    echo '</td>';
    echo '<td>' . esc_html(wp_trim_words($mat->description, 15)) . '</td>';
    $status_icon = $mat->status ? '✔️' : '❌';
    $toggle_icon = $mat->status ? '❌ désactiver' : '✔️ activer';
echo '<td>';
echo '<strong>' . $status_icon . '</strong>';
echo '<div class="row-actions">';
echo '<span class="status">';
echo '<a href="#" class="galerie3d-toggle-status" data-id="' . esc_attr($id) . '">' . $toggle_icon . '</a>';
echo '</span>';
echo '</div>';
echo '</td>';
    echo '</tr>';
}

    echo '</tbody></table>';
    echo '</form>';
    
 echo '<a href="' . admin_url('admin.php?page=guide-divers-form&action=add') . '" class="page-title-action" style="margin-top:15px;">Ajouter un Divers</a>';

    echo '<div class="tablenav"><div class="tablenav-pages">';
    $page_links = paginate_links([
        'base' => add_query_arg('paged', '%#%'),
        'format' => '',
        'prev_text' => '«',
        'next_text' => '»',
        'total' => $total_pages,
        'current' => $paged,
        'type' => 'array',
    ]);

    if (is_array($page_links)) {
        echo '<span class="pagination-links">';
        foreach ($page_links as $link) {
            echo $link . ' ';
        }
        echo '</span>';
    }
    
    echo '</div></div>';
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

