<?php
function p3d_display_material($atts) {
    global $wpdb;
    $table = $wpdb->prefix . 'p3d_materials_g';
    $paged = isset($_GET['p3d_page']) ? max(1, intval($_GET['p3d_page'])) : 1;
       $per_page = get_option('p3d_items_per_page', 2);

    $offset = ($paged - 1) * $per_page;
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 1");
    $materials = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE status = 1 ORDER BY id ASC LIMIT %d OFFSET %d", $per_page, $offset));
  ob_start();

    if (!$materials) {
        return '<p>Aucun matériau trouvé.</p>';
    }

    ob_start();

    foreach ($materials as $material) {
        $uid = uniqid('p3d_');

        // Génère le HTML pour chaque matériau
        ?>
        <style>
        .<?php echo $uid; ?> .p3d-material-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            font-family: sans-serif;
            font-size: 14px;
        }
        .<?php echo $uid; ?> .p3d-description {
            width: 100%;
            margin-bottom: 10px;
        }
        .<?php echo $uid; ?> .p3d-material-content {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }
        .<?php echo $uid; ?> .p3d-material-left {
            flex: 1;
            min-width: 280px;
        }
        .<?php echo $uid; ?> .p3d-material-right {
            flex: 1;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: space-between;
        }
        .<?php echo $uid; ?> .p3d-bar {
            margin-bottom: 10px;
        }
        .<?php echo $uid; ?> .p3d-bar-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .<?php echo $uid; ?> .p3d-bar-track {
            background-color: #eee;
            border-radius: 20px;
            height: 25px;
            overflow: hidden;
        }
        .<?php echo $uid; ?> .p3d-bar-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(to right, #007BFF, #00BFFF);
            width: 0;
            transition: width 1.2s ease-in-out;
            color: white;
            font-weight: bold;
            text-align: right;
            padding-right: 10px;
            line-height: 25px;
        }
        .<?php echo $uid; ?> .p3d-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 100%;
        }
        .<?php echo $uid; ?> .p3d-flex-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .<?php echo $uid; ?> .p3d-half {
            width: 48%;
        }
        .<?php echo $uid; ?> .p3d-applications {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            width: 100%;
            column-count: 2;
            column-gap: 30px;
        }
        .<?php echo $uid; ?> .p3d-applications p {
            break-inside: avoid;
        }
        .<?php echo $uid; ?> .p3d-list p::before {
            content: "\2022 ";
            color: #007BFF;
            font-weight: bold;
            display: inline-block;
            margin-right: 5px;
        }
        .<?php echo $uid; ?> .p3d-list p {
            margin: 0 0 6px;
        }
        .<?php echo $uid; ?> .p3d-list h4 {
            margin-bottom: 10px;
        }
        </style>

        <div class="p3d-material-container <?php echo esc_attr($uid); ?>">
            <h2><?php echo esc_html($material->name); ?></h2>

            <div class="p3d-description">
                <?php if (!empty($material->description)) echo '<p>' . nl2br(esc_html($material->description)) . '</p>'; ?>
            </div>

            <div class="p3d-material-content">
                <div class="p3d-material-left">
                    <?php
                    $properties = array(
                        'Qualité visuelle' => $material->visual_quality,
                        'Résistance à la traction' => $material->tensile_strength,
                        'Élongation à la rupture' => $material->elongation,
                        'Résistance à l’impact' => $material->impact_resistance,
                        'Résistance à la chaleur' => $material->heat_resistance,
                        'Résistance à l’humidité' => $material->humidity_resistance,
                        'Adhérence inter-couches' => $material->interlayer_adhesion
                    );

                    foreach ($properties as $label => $value) {
                        if (is_numeric($value)) {
                            $percent = intval($value);
                            echo '<div class="p3d-bar">';
                            echo '<div class="p3d-bar-label">' . esc_html($label) . '</div>';
                            echo '<div class="p3d-bar-track"><div class="p3d-bar-fill" data-final-width="' . $percent . '%" style="width: ' . $percent . '%">' . $percent . '%</div></div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>

                <div class="p3d-material-right"> 
                    <div class="p3d-flex-row">
                        <div class="p3d-box p3d-half p3d-list">
                            <h4>Avantages</h4>
                            <?php
                            if (!empty($material->advantages)) {
                                foreach (explode("\n", $material->advantages) as $line) {
                                    if (trim($line)) echo '<p>' . esc_html($line) . '</p>';
                                }
                            }
                            ?>
                        </div>
                        <div class="p3d-box p3d-half p3d-list">
                            <h4>Inconvénients</h4>
                            <?php
                            if (!empty($material->disadvantages)) {
                                foreach (explode("\n", $material->disadvantages) as $line) {
                                    if (trim($line)) echo '<p>' . esc_html($line) . '</p>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="p3d-flex-row">
                        <div class="p3d-box p3d-list">
                            <h4>Champs d’application</h4>
                            <div class="p3d-applications">
                                <?php
                                if (!empty($material->applications)) {
                                    foreach (explode("\n", $material->applications) as $line) {
                                        if (trim($line)) echo '<p>' . esc_html($line) . '</p>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
 $total_pages = ceil($total_items / $per_page);
    if ($total_pages > 1) {
        echo '<div class="p3d-pagination">';
        echo paginate_links(array(
            'base' => add_query_arg('p3d_page', '%#%'),
            'format' => '',
            'current' => $paged,
            'total' => $total_pages,
            'prev_text' => '« Précédent',
            'next_text' => 'Suivant »',
        ));
        echo '</div>';
    }


echo '<script>
document.addEventListener("DOMContentLoaded", function() {
    const bars = document.querySelectorAll(".p3d-bar-fill");

    // Initialisation
    bars.forEach(bar => {
        bar.style.width = "0";
        bar.style.transition = "width 1.2s ease-in-out";
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const el = entry.target;
            const finalWidth = el.getAttribute("data-final-width");

            if (entry.isIntersecting) {
                el.style.width = finalWidth;
            } else {
                el.style.width = "0"; // ← pour que ça disparaisse quand on sort du champ
            }
        });
    }, {
        threshold: 0.5
    });

    bars.forEach(el => observer.observe(el));
});
</script>';

    // Ajouter le CSS directement si nécessaire
    echo '<style>
    .p3d-pagination {
        text-align: center;
        margin-top: 30px;
    }

    .p3d-pagination .page-numbers {
        display: inline-block;
        padding: 8px 12px;
        margin: 0 4px;
        border-radius: 4px;
        background-color: #f0f0f0;
        color: #0073aa;
        text-decoration: none;
    }

    .p3d-pagination .page-numbers:hover {
        background-color: #e0e0e0;
    }

    .p3d-pagination .page-numbers.current {
        background-color: #0073aa;
        color: #fff;
        font-weight: bold;
    }
    </style>';


    return ob_get_clean();
}
add_shortcode('p3d_material', 'p3d_display_material');
// Shortcode pour afficher la description des matériaux
function p3d_guide_materiel_description_shortcode() {
    $description = get_option('guide_materiel_description', '');
    return wpautop($description); // ou wp_kses_post($description) si tu veux filtrer le HTML
}
add_shortcode('guide_materiel_description', 'p3d_guide_materiel_description_shortcode');
