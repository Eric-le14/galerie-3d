<?php
if (!defined('ABSPATH')) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['galerie3d_image']['name'])) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attachment_id = media_handle_upload('galerie3d_image', 0);

    if (is_wp_error($attachment_id)) {
        echo '<div class="error"><p>Erreur lors de l\'upload.</p></div>';
    } else {
        update_option('galerie3d_image_id', $attachment_id);
        echo '<div class="updated"><p>Image uploadée avec succès.</p></div>';
    }
}

$image_id = get_option('galerie3d_image_id');
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';
?>

<div class="wrap">
    <h1>Réglages Galerie 3D</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="galerie3d_image">Uploader une image :</label><br>
        <input type="file" name="galerie3d_image" id="galerie3d_image" accept="image/*" /><br><br>
        <?php if ($image_url): ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="Image actuelle" style="max-width:300px;"><br><br>
        <?php endif; ?>
        <input type="submit" class="button button-primary" value="Uploader Image" />
    </form>
</div>