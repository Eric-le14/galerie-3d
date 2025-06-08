<?php
defined('ABSPATH') || exit;

$is_edit = isset($commande);
$action_label = $is_edit ? (isset($_GET['clone']) ? 'Cloner' : 'Modifier') : 'Ajouter';
?>

<div class="wrap">
    <h1><?php echo esc_html($action_label); ?> une commande</h1>
    <form method="post">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="nom">Nom</label></th>
                <td><input name="nom" type="text" id="nom" value="<?php echo esc_attr($commande->nom ?? ''); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="description">Description</label></th>
                <td><textarea name="description" id="description" rows="5" class="large-text"><?php echo esc_textarea($commande->description ?? ''); ?></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="status">Statut</label></th>
                <td><input name="status" type="checkbox" id="status" value="1" <?php checked($commande->status ?? false); ?>> Actif</td>
            </tr>
        </table>
        <?php submit_button($action_label); ?>
    </form>
</div>
