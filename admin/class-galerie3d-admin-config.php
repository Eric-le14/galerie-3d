<?php

if (!class_exists('Galerie3D_Admin_Config')) {

    class Galerie3D_Admin_Config {

        public static function render_settings_page() {
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'admin';
            $base_url = admin_url('admin.php?page=galerie3d-settings');
            ?>
            <div class="wrap">
                <h1>Réglages Galerie 3D</h1>
                <h2 class="nav-tab-wrapper">
                    <a href="<?php echo esc_url(add_query_arg('tab', 'admin', $base_url)); ?>" class="nav-tab <?php echo $active_tab === 'admin' ? 'nav-tab-active' : ''; ?>">Admin Galerie</a>
                    <a href="<?php echo esc_url(add_query_arg('tab', 'client', $base_url)); ?>" class="nav-tab <?php echo $active_tab === 'client' ? 'nav-tab-active' : ''; ?>">Client Galerie</a>
                </h2>
                <form method="post" action="options.php">
                    <?php
                    if ($active_tab === 'admin') {
                        settings_fields('galerie3d_admin_settings_group');
                        do_settings_sections('galerie3d-settings-admin');
                    } else {
                        settings_fields('galerie3d_client_settings_group');
                        do_settings_sections('galerie3d-settings-client');
                    }
                    submit_button();
                    ?>
                </form>
                <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.nav-tab-wrapper a').forEach(function (tabLink) {
        tabLink.addEventListener('click', function (e) {
            window.location.href = this.href; // Force la redirection
        });
    });
});
</script>

            </div>
            <?php
        }
    }

    add_action('admin_init', function () {
        // Admin Galerie
        register_setting('galerie3d_admin_settings_group', 'galerie3d_admin_per_page');

        add_settings_section(
            'galerie3d_admin_section',
            'Réglages Admin Galerie',
            null,
            'galerie3d-settings-admin'
        );

        add_settings_field(
            'galerie3d_admin_per_page',
            'Nombre de photos par page (Admin)',
            function () {
                $value = get_option('galerie3d_admin_per_page', 10);
                echo '<input type="number" name="galerie3d_admin_per_page" value="' . esc_attr($value) . '" class="small-text" min="1" />';
            },
            'galerie3d-settings-admin',
            'galerie3d_admin_section'
        );

        // Client Galerie
        register_setting('galerie3d_client_settings_group', 'galerie3d_client_per_page');

        add_settings_section(
            'galerie3d_client_section',
            'Réglages Client Galerie',
            null,
            'galerie3d-settings-client'
        );

        add_settings_field(
            'galerie3d_client_per_page',
            'Nombre de photos par page (Client)',
            function () {
                $value = get_option('galerie3d_client_per_page', 12);
                echo '<input type="number" name="galerie3d_client_per_page" value="' . esc_attr($value) . '" class="small-text" min="1" />';
            },
            'galerie3d-settings-client',
            'galerie3d_client_section'
        );
    });
}
