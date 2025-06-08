<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'p3d_divers_g';
$action = $_GET['action'] ?? 'add';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Valeurs par défaut
$default = [
    'name' => '',
    'description' => '',
    'status' => 1,
    'image_url' => '',
];

// Récupération en base si édition ou clonage
if (($action === 'edit' || $action === 'clone') && $id > 0) {
    $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
    if ($data) {
        $default = array_merge($default, $data);
        if ($action === 'clone') {
            unset($default['id']);
        }
    } else {
        echo '<div class="notice notice-error"><p>Divers introuvable.</p></div>';
        return;
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['p3d_material_nonce']) && wp_verify_nonce($_POST['p3d_material_nonce'], 'save_material')) {
    $fields = array_intersect_key($_POST, $default);
    $fields = array_map('sanitize_text_field', $fields);
    $fields['status'] = isset($_POST['status']) ? 1 : 0;

    if ($action === 'edit' && $id > 0) {
        $wpdb->update($table, $fields, ['id' => $id]);
        echo '<div class="notice notice-success"><p>Divers modifié avec succès.</p></div>';
    } else {
        $wpdb->insert($table, $fields);
        echo '<div class="notice notice-success"><p>Divers ajouté avec succès.</p></div>';
    }

    // Rediriger pour éviter resoumission du formulaire
    echo '<script>location.href="' . admin_url('admin.php?page=guide-divers') . '";</script>';
    exit;
}

$titre = ($action === 'edit') ? 'Modifier un Divers' : (($action === 'clone') ? 'Cloner un Divers' : 'Ajouter un Divers');
$bouton = ($action === 'edit') ? 'Mettre à jour' : 'Ajouter';
?>

<div class="wrap">
    <h1><?php echo esc_html($titre); ?></h1>

        <a href="<?php echo admin_url('admin.php?page=guide-divers'); ?>" class="button-secondary">Retour à la liste</a>

    <form method="post">
        <?php wp_nonce_field('save_material', 'p3d_material_nonce'); ?>

        <table class="form-table">
        <tr>
    <th><label for="image_url">Image</label></th>
    <td>
        <input id="image_url" name="image_url" type="text" value="<?php echo esc_attr($default['image_url']); ?>" class="regular-text">
        <input type="button" class="button" id="upload_image_button" value="Choisir une image" />
        <?php if (!empty($default['image_url'])): ?>
            <div><img src="<?php echo esc_url($default['image_url']); ?>" style="max-height:100px;margin-top:10px;"></div>
        <?php endif; ?>
    </td>
</tr>    
        <tr><th><label for="name">Nom</label></th>
                <td><input name="name" type="text" value="<?php echo esc_attr($default['name']); ?>" class="regular-text" required></td>
            </tr>
            <tr><th><label for="description">Description</label></th>
                <td><textarea name="description" rows="4" class="large-text"><?php echo esc_textarea($default['description']); ?></textarea></td>
            </tr>
            <tr><th><label for="status">Actif</label></th>
                <td><input name="status" type="checkbox" value="1" <?php checked($default['status'], 1); ?>></td>
            </tr>
           

           
        </table>

         <?php submit_button($bouton); ?>
    </form>
<?php
// Charger les scripts nécessaires pour la médiathèque WordPress
wp_enqueue_media();
?>
<script>
jQuery(document).ready(function ($) {
    $('#upload_image_button').on('click', function (e) {
        e.preventDefault();
        var image_frame;
        if (image_frame) {
            image_frame.open();
            return;
        }
        image_frame = wp.media({
            title: 'Choisir une image',
            multiple: false,
            library: { type: 'image' },
            button: { text: 'Utiliser cette image' }
        });

        image_frame.on('select', function () {
            var attachment = image_frame.state().get('selection').first().toJSON();
            $('#image_url').val(attachment.url);
        });

        image_frame.open();
    });
});
</script>

<script>
jQuery(document).ready(function($){
    $('#upload_image_button').click(function(e) {
        e.preventDefault();
        var custom_uploader = wp.media({
            title: 'Choisir une image',
            button: {
                text: 'Utiliser cette image'
            },
            multiple: false
        })
        .on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#image_url').val(attachment.url);
        })
        .open();
    });
});
</script>

</div>
