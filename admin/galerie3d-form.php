<?php
global $wpdb;
$table_name = $wpdb->prefix . 'p3d_galerie3d';

// Récupération du numéro de page pour le retour
$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

// Détection du mode
$is_edit  = !empty($_GET['id']) && (!isset($_GET['action']) || $_GET['action'] !== 'clone');
$is_clone = !empty($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'clone';

// Pré-remplissage si édition ou clonage
$commande = null;
if (!empty($_GET['id'])) {
    $commande = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", intval($_GET['id'])),
        ARRAY_A
    );
    if ($is_clone) {
        unset($commande['id']);
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('manage_options')) {
    $data = [
        'client_name' => sanitize_text_field($_POST['client_name']),
        'image_name'  => sanitize_text_field($_POST['image_name']),
        'description' => sanitize_textarea_field($_POST['description']),
        'material'    => sanitize_text_field($_POST['material']),
        'print_date'  => sanitize_text_field($_POST['print_date']),
        'status'      => isset($_POST['status']) ? 1 : 0,
        'display'     => 1,
    ];

    // Upload image
    if (!empty($_FILES['image']['name'])) {
        $upload = wp_handle_upload($_FILES['image'], ['test_form' => false]);
        if (!isset($upload['error'])) {
            $data['image_url'] = esc_url_raw($upload['url']);
        }
    } elseif (!empty($commande['image_url'])) {
        $data['image_url'] = $commande['image_url'];
    }

    // Upload STL
    if (!empty($_FILES['stl_file']['name'])) {
        $upload = wp_handle_upload($_FILES['stl_file'], ['test_form' => false]);
        if (!isset($upload['error'])) {
            $data['stl_file_url'] = esc_url_raw($upload['url']);
        }
    } elseif (!empty($commande['stl_file_url'])) {
        $data['stl_file_url'] = $commande['stl_file_url'];
    }

    if ($is_edit) {
        $wpdb->update($table_name, $data, ['id' => intval($_GET['id'])]);
    } else {
        $wpdb->insert($table_name, $data);
    }

    wp_redirect(admin_url('admin.php?page=galerie3d&paged=' . $paged));
    exit;
}
?>

<div class="wrap">
  <div class="galerie3d-form-container">
    <h1 class="wp-heading-inline">
      <?php echo $is_clone ? 'Cloner' : ($is_edit ? 'Modifier' : 'Ajouter'); ?> une photo
    </h1>

<p>
    <a href="<?php echo admin_url('admin.php?page=galerie3d&paged=' . $paged); ?>" class="page-title-action">← Retour à la liste</a>
</p>

<hr class="wp-header-end">

<form method="post" enctype="multipart/form-data">
    <table class="form-table">
        <tr>
            <th><label for="client_name">Nom du client</label></th>
            <td><input type="text" name="client_name" value="<?php echo esc_attr($commande['client_name'] ?? ''); ?>" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="description">Description</label></th>
            <td><textarea name="description" rows="4" class="large-text"><?php echo esc_textarea($commande['description'] ?? ''); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="material">Matière</label></th>
            <td><input type="text" name="material" value="<?php echo esc_attr($commande['material'] ?? ''); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="print_date">Date d'impression</label></th>
            <td><input type="date" name="print_date" value="<?php echo esc_attr($commande['print_date'] ?? ''); ?>"></td>
        </tr>
        <tr>
            <th><label for="image">Image</label></th>
            <td>
                <input type="file" name="image">
                <?php if (!empty($commande['image_url'])): ?>
                    <p style="margin-top:10px;">
                        <img src="<?php echo esc_url($commande['image_url']); ?>" style="max-width:200px; border-radius:6px;" />
                    </p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label for="image_name">Nom de l’image</label></th>
            <td>
                <input type="text" name="image_name" value="<?php echo esc_attr($commande['image_name'] ?? ''); ?>" class="regular-text">
                <p class="description">Nom affiché dans la liste des commandes.</p>
            </td>
        </tr>
        <tr>
            <th><label for="stl_file">Fichier STL</label></th>
            <td>
                <input type="file" name="stl_file" accept=".stl">
                <?php if (!empty($commande['stl_file_url'])): ?>
                    <p><a href="<?php echo esc_url($commande['stl_file_url']); ?>" target="_blank">Télécharger le fichier STL existant</a></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label for="status">Actif</label></th>
            <td><input type="checkbox" name="status" value="1" <?php checked($commande['status'] ?? 0, 1); ?>></td>
        </tr>
    </table>

    <?php
        if ($is_clone) {
            submit_button('Copier');
        } elseif ($is_edit) {
            submit_button('Mettre à jour');
        } else {
            submit_button('Enregistrer');
        }
    ?>
</form>
</div>
</div>
