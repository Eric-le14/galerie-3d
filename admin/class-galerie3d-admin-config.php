<?php

if (!class_exists('Galerie3D_Admin_Config')) {

    class Galerie3D_Admin_Config {

        public static function render_settings_page() {
            ?>
            <div class="wrap">
                <h1>Réglages Galerie 3D</h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('galerie3d_settings_group');
                    do_settings_sections('galerie3d-settings');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }
    }

    add_action('admin_init', function () {
        register_setting('galerie3d_settings_group', 'galerie3d_option_example');

        add_settings_section(
            'galerie3d_main_settings',
            'Paramètres principaux',
            null,
            'galerie3d-settings'
        );

        add_settings_field(
            'galerie3d_option_example',
            'Exemple d’option',
            function () {
                $value = get_option('galerie3d_option_example', '');
                echo '<input type="text" name="galerie3d_option_example" value="' . esc_attr($value) . '" class="regular-text" />';
            },
            'galerie3d-settings',
            'galerie3d_main_settings'
        );
    });
}
