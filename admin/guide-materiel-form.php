<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'p3d_materials_g';
$action = $_GET['action'] ?? 'add';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Valeurs par défaut
$default = [
    'name' => '',
    'status' => 1,
    'description' => '',
    'advantages' => '',
    'disadvantages' => '',
    'applications' => '',
    'visual_quality' => '',
    'tensile_strength' => '',
    'elongation' => '',
    'impact_resistance' => '',
    'heat_resistance' => '',
    'humidity_resistance' => '',
    'interlayer_adhesion' => '',
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
        echo '<div class="notice notice-error"><p>Matériau introuvable.</p></div>';
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
        echo '<div class="notice notice-success"><p>Matériau modifié avec succès.</p></div>';
    } else {
        $wpdb->insert($table, $fields);
        echo '<div class="notice notice-success"><p>Matériau ajouté avec succès.</p></div>';
    }

    // Rediriger pour éviter resoumission du formulaire
    echo '<script>location.href="' . admin_url('admin.php?page=guide-materiel') . '";</script>';
    exit;
}

$titre = ($action === 'edit') ? 'Modifier un matériau' : (($action === 'clone') ? 'Cloner un matériau' : 'Ajouter un matériau');
$bouton = ($action === 'edit') ? 'Mettre à jour' : 'Ajouter';
?>

<div class="wrap">
    <h1><?php echo esc_html($titre); ?></h1>

    <form method="post">
        <?php wp_nonce_field('save_material', 'p3d_material_nonce'); ?>

        <table class="form-table">
            <tr><th><label for="name">Nom</label></th>
                <td><input name="name" type="text" value="<?php echo esc_attr($default['name']); ?>" class="regular-text" required></td>
            </tr>

            <tr><th><label for="status">Actif</label></th>
                <td><input name="status" type="checkbox" value="1" <?php checked($default['status'], 1); ?>></td>
            </tr>

            <tr><th><label for="description">Description</label></th>
                <td><textarea name="description" rows="4" class="large-text"><?php echo esc_textarea($default['description']); ?></textarea></td>
            </tr>

            <tr><th><label for="advantages">Avantages</label></th>
                <td><textarea name="advantages" rows="3" class="large-text"><?php echo esc_textarea($default['advantages']); ?></textarea></td>
            </tr>

            <tr><th><label for="disadvantages">Inconvénients</label></th>
                <td><textarea name="disadvantages" rows="3" class="large-text"><?php echo esc_textarea($default['disadvantages']); ?></textarea></td>
            </tr>

            <tr><th><label for="applications">Applications</label></th>
                <td><textarea name="applications" rows="3" class="large-text"><?php echo esc_textarea($default['applications']); ?></textarea></td>
            </tr>

            <?php
            $metrics = [
                'visual_quality' => 'Qualité visuelle',
                'tensile_strength' => 'Résistance à la traction',
                'elongation' => 'Élongation',
                'impact_resistance' => 'Résistance aux chocs',
                'heat_resistance' => 'Résistance à la chaleur',
                'humidity_resistance' => 'Résistance à l\'humidité',
                'interlayer_adhesion' => 'Adhésion inter-couches'
            ];

            foreach ($metrics as $key => $label) {
                echo '<tr><th><label for="' . $key . '">' . $label . '</label></th>';
                echo '<td><input name="' . $key . '" type="number" step="any" value="' . esc_attr($default[$key]) . '" class="small-text"></td></tr>';
            }
            ?>
        </table>

        <?php submit_button($bouton); ?>
        <a href="<?php echo admin_url('admin.php?page=guide-materiel'); ?>" class="button-secondary">Retour à la liste</a>
    </form>
</div>
