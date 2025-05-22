<?php
if (!current_user_can('manage_options')) {
    wp_die('Accès refusé.');
}

global $wpdb;
$table_name = $wpdb->prefix . 'galerie3d_items';

if (isset($_POST['add_item'])) {
    // Sécuriser les données
    $title = sanitize_text_field($_POST['title']);
    $materiel = sanitize_text_field($_POST['materiel']);
    $image_url = esc_url_raw($_POST['image_url']);
    $stl_url = esc_url_raw($_POST['stl_url']);

    $wpdb->insert($table_name, [
        'title' => $title,
        'materiel' => $materiel,
        'image_url' => $image_url,
        'stl_url' => $stl_url
    ]);

    echo '<div class="updated"><p>Item ajouté avec succès.</p></div>';
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $wpdb->delete($table_name, ['id' => $delete_id]);
    echo '<div class="updated"><p>Item supprimé.</p></div>';
}

$items = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="wrap">
    <h1>Ajouter / Modifier une réalisation 3D</h1>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('save_realisation', 'realisation_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="titre">Titre</label></th>
                <td><input name="titre" type="text" id="titre" class="regular-text" required></td>
            </tr>

            <tr>
                <th scope="row"><label for="matiere">Matière</label></th>
                <td><input name="matiere" type="text" id="matiere" class="regular-text" required></td>
            </tr>

            <tr>
                <th scope="row"><label for="image_url">URL de l’image</label></th>
                <td><input name="image_url" type="text" id="image_url" class="regular-text">
                    <p class="description">ou envoyer une image ci-dessous</p>
                    <input type="file" name="image_file" accept="image/*">
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="stl_url">URL du fichier STL</label></th>
                <td><input name="stl_url" type="text" id="stl_url" class="regular-text">
                    <p class="description">ou envoyer un fichier STL ci-dessous</p>
                    <input type="file" name="stl_file" accept=".stl">
                </td>
            </tr>
        </table>

        <?php submit_button('Enregistrer la réalisation'); ?>
    </form>
</div>