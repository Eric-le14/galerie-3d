<?php
defined('ABSPATH') || exit;
require_once plugin_dir_path(__FILE__) . '../../includes/functions-status-toggle.php';

global $wpdb;
$table_name = $wpdb->prefix . 'p3d_galerie3d';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$order_by = isset($_GET['orderby']) ? sanitize_sql_orderby($_GET['orderby']) : 'id';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';

$query = "SELECT * FROM $table_name";
if (!empty($search)) {
    $query .= $wpdb->prepare(" WHERE nom LIKE %s OR description LIKE %s", "%$search%", "%$search%");
}
$query .= " ORDER BY $order_by $order";
$commandes = $wpdb->get_results($query);
$base_url = admin_url('admin.php?page=galerie3d_commandes');
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Commandes</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=galerie3d_ajouter')); ?>" class="page-title-action">Ajouter</a>
    <form method="get" class="search-form">
        <input type="hidden" name="page" value="galerie3d_commandes">
        <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Rechercher...">
        <input type="submit" class="button" value="Rechercher">
    </form>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <?php
                $columns = [
                    'id' => 'ID',
                    'nom' => 'Nom',
                    'description' => 'Description',
                    'status' => 'Statut'
                ];

                foreach ($columns as $column_key => $column_label) {
                    $sort_order = ($order_by === $column_key && $order === 'ASC') ? 'desc' : 'asc';
                    $arrow = '';
                    if ($order_by === $column_key) {
                        $arrow = $order === 'ASC' ? ' ↑' : ' ↓';
                    }
                    echo '<th><a href="' . esc_url(add_query_arg(['orderby' => $column_key, 'order' => $sort_order], $base_url)) . '">' . esc_html($column_label) . $arrow . '</a></th>';
                }
                ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($commandes): ?>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?php echo esc_html($commande->id); ?></td>
                        <td><?php echo esc_html($commande->nom); ?></td>
                        <td><?php echo esc_html(wp_trim_words($commande->description, 15)); ?></td>
                        <td>
                            <?php echo p3d_render_toggle_status_button($commande->id, $commande->status, 'p3d_galerie3d'); ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=galerie3d_modifier&id=' . $commande->id)); ?>" class="button">Modifier</a>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=galerie3d_cloner&id=' . $commande->id)); ?>" class="button">Cloner</a>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=galerie3d_commandes&action=supprimer&id=' . $commande->id)); ?>" class="button" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Aucune commande trouvée.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
