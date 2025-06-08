<?php
class Galerie3D_Admin_Config {

    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function register_settings() {
        $tabs = [
            'admin' => 'Admin Galerie',
            'client' => 'Client Galerie',
            'materiaux_admin' => 'Matériaux Admin',
            'materiaux_client' => 'Matériaux Client',
            'divers_admin' => 'Divers Admin',
            'divers_client' => 'Divers Client',
        ];

        foreach ($tabs as $key => $label) {
            $group   = "galerie3d_{$key}_settings_group";
            $option  = "galerie3d_{$key}_options";
            $section = "galerie3d_{$key}_section";
            $page    = "galerie3d-settings-{$key}";

            // Options de type tableau
            register_setting($group, $option);

            // Options individuelles si nécessaire
            if ($key === 'admin') {
                register_setting($page, 'galerie3d_admin_per_page');
            }
            if ($key === 'client') {
                register_setting($page, 'galerie3d_client_per_page');
            }

            add_settings_section(
                $section,
                "Paramètres de {$label}",
                '__return_null',
                $page
            );

            switch ($key) {
                case 'admin':
                    
                    add_settings_field(
    'per_page',
    'Nombre de photos par page (Admin)',
    function () use ($option) {
        $opts = get_option($option);
        ?>
        <input type="number" name="<?php echo esc_attr($option); ?>[per_page]" value="<?php echo esc_attr($opts['per_page'] ?? 10); ?>" class="small-text" min="1" />
        <?php
    },
    $page,
    $section
);
                    break;

                case 'client':
    // Enregistre l'option en tant que tableau
    register_setting($group, $option);

    // Champ "Nombre de photos par page (Client)" dans le même tableau
    add_settings_field(
        'per_page',
        'Nombre de photos par page (Client)',
        function () use ($option) {
            $opts = get_option($option);
            ?>
            <input type="number" name="<?php echo esc_attr($option); ?>[per_page]" value="<?php echo esc_attr($opts['per_page'] ?? 12); ?>" class="small-text" min="1" />
            <?php
        },
        $page,
        $section
    );
    break;

                case 'materiaux_admin':
                    add_settings_field(
    'per_page',
    'Nombre matériaux par page (admin)',
    function () use ($option) {
        $opts = get_option($option);
        ?>
        <input type="number" name="<?php echo esc_attr($option); ?>[per_page]" value="<?php echo esc_attr($opts['per_page'] ?? 10); ?>" min="1" class="small-text" />
        <?php
    },
    $page,
    $section
);
                    break;

                case 'materiaux_client':
                    register_setting($group, $option);

add_settings_field(
    'per_page',
    'Nombre matériaux par page (client)',
    function () use ($option) {
        $opts = get_option($option);
        ?>
        <input type="number" name="<?php echo esc_attr($option); ?>[per_page]" value="<?php echo esc_attr($opts['per_page'] ?? 10); ?>" min="1" class="small-text" />
        <?php
    },
    $page,
    $section
);

                    break;

                case 'divers_admin':
                    add_settings_field(
    'per_page',
    'Nombre d’éléments divers par page (admin)',
    function () use ($option) {
        $opts = get_option($option);
        ?>
        <input type="number" name="<?php echo esc_attr($option); ?>[per_page]" value="<?php echo esc_attr($opts['per_page'] ?? 10); ?>" min="1" class="small-text" />
        <?php
    },
    $page,
    $section
);
                    break;

                case 'divers_client':
                    register_setting($group, $option);

add_settings_field(
    'per_page',
    'Nombre d’éléments divers par page (client)',
    
    function () use ($option) {
        $opts = get_option($option);
        ?>
        <input type="number" name="<?php echo esc_attr($option); ?>[per_page]" value="<?php echo esc_attr($opts['per_page'] ?? 10); ?>" min="1" class="small-text" />
        <?php
    },
    $page,
    $section
);

                    break;
            }
        }
    }

    public static function render_settings_page() {
        $active_tab = $_GET['tab'] ?? 'admin';
        $base_url = 'admin.php?page=galerie3d-settings';
        ?>
        <div class="wrap">
            <h1>Réglages Galerie 3D</h1>

            <h2 class="nav-tab-wrapper">
                <?php
                $tabs = [
                    'admin' => 'Admin Galerie',
                    'client' => 'Client Galerie',
                    'materiaux_admin' => 'Matériaux Admin',
                    'materiaux_client' => 'Matériaux Client',
                    'divers_admin' => 'Divers Admin',
                    'divers_client' => 'Divers Client',
                ];
                foreach ($tabs as $key => $label) {
                    $url = esc_url(add_query_arg('tab', $key, $base_url));
                    $active = ($active_tab === $key) ? 'nav-tab-active' : '';
                    echo "<a href='{$url}' class='nav-tab {$active}'>{$label}</a>";
                }
                ?>
            </h2>

            <form method="post" action="options.php">
                <?php
                switch ($active_tab) {
                    case 'admin':
                        settings_fields('galerie3d_admin_settings_group');
                        do_settings_sections('galerie3d-settings-admin');
                        break;
                    case 'client':
                        settings_fields('galerie3d_client_settings_group');
                        do_settings_sections('galerie3d-settings-client');
                        break;
                    case 'materiaux_admin':
                        settings_fields('galerie3d_materiaux_admin_settings_group');
                        do_settings_sections('galerie3d-settings-materiaux_admin');
                        break;
                    case 'materiaux_client':
                        settings_fields('galerie3d_materiaux_client_settings_group');
                        do_settings_sections('galerie3d-settings-materiaux_client');
                        break;
                    case 'divers_admin':
                        settings_fields('galerie3d_divers_admin_settings_group');
                        do_settings_sections('galerie3d-settings-divers_admin');
                        break;
                    case 'divers_client':
                        settings_fields('galerie3d_divers_client_settings_group');
                        do_settings_sections('galerie3d-settings-divers_client');
                        break;
                }
                submit_button();
                ?>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.nav-tab-wrapper a.nav-tab').forEach(function(tab) {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        window.location.href = this.href;
                    });
                });
            });
        </script>
        <?php
    }
}

Galerie3D_Admin_Config::init();
?>
