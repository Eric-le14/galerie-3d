<?php
if (!defined('ABSPATH')) exit;

function galerie3d_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_galerie = $wpdb->prefix . 'p3d_galerie3d';
    $table_materials = $wpdb->prefix . 'p3d_materials_g';
    $table_divers = $wpdb->prefix . 'p3d_divers_g';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Table des réalisations
    dbDelta("CREATE TABLE $table_galerie (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        client_name VARCHAR(255) NOT NULL,
        description TEXT,
        material VARCHAR(100),
        image_url TEXT,
        image_name VARCHAR(255),
        stl_file_url TEXT,
        status TINYINT(1) DEFAULT 1,
        display TINYINT(1) DEFAULT 1,
        print_date DATE,
        PRIMARY KEY (id)
    ) $charset_collate;");

    // Table des matériaux
    dbDelta("CREATE TABLE $table_materials (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        advantages TEXT,
        disadvantages TEXT,
        applications TEXT,
        visual_quality FLOAT,
        tensile_strength FLOAT,
        elongation FLOAT,
        impact_resistance FLOAT,
        heat_resistance FLOAT,
        humidity_resistance FLOAT,
        interlayer_adhesion FLOAT,
        status TINYINT(1) DEFAULT 1,
        PRIMARY KEY (id)
    ) $charset_collate;");

    // Table des éléments "divers"
    dbDelta("CREATE TABLE $table_divers (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        image_url TEXT,
        description TEXT,
        status TINYINT(1) DEFAULT 1,
        PRIMARY KEY (id)
    ) $charset_collate;");

    // ➕ Exemple de matériau (si la table est vide)
    $material_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_materials");
    if ($material_count == 0) {
        $wpdb->insert($table_materials, [
    'name' => 'PLA',
    'description' => 'Le PLA est un matériau biodégradable utilisé pour créer des objets légers, esthétiques et colorés. Idéal pour les décorations, maquettes ou prototypes visuels, il offre un très bon rendu et une grande variété de couleurs.',
    'advantages' => 'Très bon rendu visuel, Matériau d’origine végétale, Peu de déformation, Large choix de couleurs.',
    'disadvantages' => 'Peu résistant à la chaleur, Fragile pour les usages mécaniques, Sensibilité à l’humidité, Pas adapté à l’extérieur.',
    'applications' => 'Objets décoratifs, maquettes, prototypes visuels.',
    'visual_quality' => 80,
    'tensile_strength' => 80,
    'elongation' => 20,
    'impact_resistance' => 20,
    'heat_resistance' => 20,
    'humidity_resistance' => 20,
    'interlayer_adhesion' => 80,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PETG',
    'description' => 'Le PETG est un filament solide, légèrement flexible et plus résistant que le PLA. C’est un excellent choix pour créer des objets du quotidien ou des pièces techniques durables. Il combine résistance, souplesse .',
    'advantages' => 'Plus solide que le PLA, Résiste bien à l’eau et à l’humidité, Aux produits chimiques, Légerement flexible, Bonne tenue à la chaleur, Facile à entretenir.',
    'disadvantages' => 'Moins de choix de couleurs que le PLA, Moins précis pour les petits détails fins.',
    'applications' => 'Pièces fonctionnelles, contenants alimentaires (non chauffés), objets durables.',
    'visual_quality' => 70,
    'tensile_strength' => 80,
    'elongation' => 50,
    'impact_resistance' => 60,
    'heat_resistance' => 60,
    'humidity_resistance' => 90,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'TPU',
    'description' => 'Le TPU est un filament flexible parfait pour créer des objets souples, résistants et durables. Il est idéal pour les impressions qui doivent supporter des torsions, des chocs ou des vibrations. Très apprécié pour les pièces techniques ou les protections.',
    'advantages' => 'Haute flexibilité, Résistant aux chocs, A l’usure,Résiste bien à l’humidité, Idéal pour les pièces absorbant les vibrations

.',
    'disadvantages' => 'Moins précis, Pas idéal pour les pièces rigides, Moins esthétique, Peut s’user plus vite.',
    'applications' => 'Coques de téléphone, semelles, joints, amortisseurs.',
    'visual_quality' => 60,
    'tensile_strength' => 50,
    'elongation' => 90,
    'impact_resistance' => 90,
    'heat_resistance' => 40,
    'humidity_resistance' => 70,
    'interlayer_adhesion' => 70,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'ABS',
    'description' => 'L’ABS est un plastique robuste, conçu pour durer. Il résiste bien aux chocs, à la chaleur et à l’usure, ce qui en fait un excellent choix pour fabriquer des objets du quotidien, des pièces fonctionnelles ou même des éléments mécaniques. C’est un matériau fiable, souvent utilisé dans l’industrie pour sa solidité.',
    'advantages' => 'Très résistant aux chocs, Bonne résistance à la chaleur, Durable dans le temps.',
    'disadvantages' => 'Moins bon pour les petits détails, Moins respectueux de l’environnement.',
    'applications' => 'Pièces mécaniques, boîtiers, objets techniques.',
    'visual_quality' => 60,
    'tensile_strength' => 70,
    'elongation' => 20,
    'impact_resistance' => 70,
    'heat_resistance' => 80,
    'humidity_resistance' => 30,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'ASA',
    'description' => 'L’ASA est un matériau idéal pour ceux qui recherchent de la solidité, une bonne tenue dans le temps, et une résistance exceptionnelle aux UV et aux intempéries. Parfait pour des pièces exposées à la lumière du soleil ou à des conditions extérieures.',
    'advantages' => 'Résistant aux UV, Très bonne stabilité extérieure, Solide et durable, Finitions soignées.',
    'disadvantages' => 'Moins adapté aux petits détails, Légèrement plus coûteux que le PLA.',
    'applications' => 'Objets extérieurs, capteurs, coques, panneaux signalétiques.',
    'visual_quality' => 70,
    'tensile_strength' => 70,
    'elongation' => 30,
    'impact_resistance' => 60,
    'heat_resistance' => 80,
    'humidity_resistance' => 50,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PC',
    'description' => 'Le PC est utilisé pour fabriquer des objets solides et durables. C’est un excellent choix pour des pièces techniques, des supports robustes ou des objets soumis à des conditions exigeantes. Il résiste à la chaleur, aux chocs, et garde une très bonne stabilité dans le temps.',
    'advantages' => 'Très solide, Excellente résistance à la chaleur, Haute résistance aux chocs, Durabilité élevée.',
    'disadvantages' => 'Prix élevé, Moins de choix de couleurs, Rigidité importante.',
    'applications' => 'Pièces mécaniques, composants automobiles, boîtiers électroniques, pièces fonctionnelles soumises à de fortes contraintes.',
    'visual_quality' => 60,
    'tensile_strength' => 90,
    'elongation' => 20,
    'impact_resistance' => 90,
    'heat_resistance' => 80,
    'humidity_resistance' => 40,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);

$wpdb->insert($table_materials, [
    'name' => 'PA',
    'description' => 'Le Nylon (PA) est un filament technique utilisé pour imprimer des pièces solides, résistantes et légèrement flexibles. Il est parfait pour les usages mécaniques ou fonctionnels.',
    'advantages' => 'Très solide et résistant, Flexible sans casser, Bonne tenue à l’usure, Résiste à des températures élevées.',
    'disadvantages' => 'Coût élevé, Moins esthétique.',
    'applications' => 'Engrenages, charnières, pièces de friction, prototypes fonctionnels, connecteurs.',
    'visual_quality' => 40,
    'tensile_strength' => 80,
    'elongation' => 80,
    'impact_resistance' => 80,
    'heat_resistance' => 70,
    'humidity_resistance' => 20,
    'interlayer_adhesion' => 50,
    'status' => 1,
]);

$wpdb->insert($table_materials, [
    'name' => 'PA-CF',
    'description' => 'Le PA-CF est un filament technique renforcé avec des fibres de carbone. Il est conçu pour les impressions 3D ultra-résistantes. Idéal pour les pièces mécaniques qui doivent rester solides, légères et rigides.',
    'advantages' => 'Très résistant, Rigide et léger, Bonne tenue à la chaleur, Aspect mat professionnel.',
    'disadvantages' => 'Peu flexible, Coût élevé, Moins esthétique pour les objets décoratifs.',
    'applications' => 'Pièces mécaniques, pièces structurelles, composants de drones, supports de charge.',
    'visual_quality' => 50,
    'tensile_strength' => 90,
    'elongation' => 40,
    'impact_resistance' => 60,
    'heat_resistance' => 80,
    'humidity_resistance' => 20,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PA-GF',
    'description' => 'Le ASA-GF combine la robustesse de l’ASA avec la résistance mécanique des fibres de verre. Ce matériau est conçu pour des pièces techniques et durables, notamment pour un usage en extérieur, là où les conditions sont exigeantes.',
    'advantages' => 'Excellente résistance aux UV et aux intempéries, Très bonne rigidité et stabilité dimensionnelle, Bonne résistance à la chaleur, Durabilité renforcée.',
    'disadvantages' => 'Fragilité en cas de choc, Aspect brut et moins esthétique, Prix plus élevé.',
    'applications' => 'Composants techniques, pièces automobiles, outillage, environnements industriels.',
    'visual_quality' => 40,
    'tensile_strength' => 85,
    'elongation' => 30,
    'impact_resistance' => 50,
    'heat_resistance' => 85,
    'humidity_resistance' => 30,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);

$wpdb->insert($table_materials, [
    'name' => 'PET-CF',
    'description' => 'Le PET-CF est un plastique technique renforcé avec des fibres de carbone. Il combine rigidité, légèreté et excellente stabilité. Idéal pour les pièces techniques ou mécaniques qui doivent rester précises dans le temps..',
    'advantages' => 'Très rigide et léger, Bonne stabilité dimensionnelle, Bonne résistance thermique, Aspect mat et professionnel.',
    'disadvantages' => 'Prix plus élevé, Moins de couleurs, Moins flexible.',
    'applications' => 'Pièces structurelles, composants mécaniques, drones, prototypage fonctionnel.',
    'visual_quality' => 60,
    'tensile_strength' => 90,
    'elongation' => 20,
    'impact_resistance' => 40,
    'heat_resistance' => 85,
    'humidity_resistance' => 60,
    'interlayer_adhesion' => 70,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PET-GF',
    'description' => 'Le PET-GF est une version du PET renforcée avec des fibres de verre. Il offre une excellente rigidité, une bonne stabilité thermique et une grande résistance à l’humidité. C’est un matériau idéal pour des pièces durables et stables dans le temps.',
    'advantages' => 'Très rigide, Bonne tenue à la chaleur, Excellente résistance à l’humidité, Bon comportement mécanique.',
    'disadvantages' => 'Aspect plus brut, Coût plus élevé, Moins adapté aux détails fins.',
    'applications' => 'Pièces techniques, capots de protection, composants automobiles ou industriels.',
    'visual_quality' => 50,
    'tensile_strength' => 85,
    'elongation' => 15,
    'impact_resistance' => 35,
    'heat_resistance' => 80,
    'humidity_resistance' => 70,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PPA-CF',
    'description' => 'Le PPA-CF est un matériau hautes performances renforcé en fibres de carbone. Il combine une excellente rigidité, une grande résistance à la chaleur et une faible absorption d’humidité. Il est conçu pour les pièces techniques qui doivent résister à des environnements exigeants.',
    'advantages' => 'Extrêmement rigide, Haute résistance thermique, Très faible absorption d’humidité, Stabilité dimensionnelle.',
    'disadvantages' => 'Coût élevé, aMoins adapté aux pièces décoratives.',
    'applications' => 'Aéronautique, automobile, pièces structurelles et fonctionnelles dans des environnements sévères.',
    'visual_quality' => 60,
    'tensile_strength' => 90,
    'elongation' => 15,
    'impact_resistance' => 50,
    'heat_resistance' => 90,
    'humidity_resistance' => 80,
    'interlayer_adhesion' => 65,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PPA-GF',
    'description' => 'Le PPA-GF est un matériau technique conçu pour les environnements exigeants. Renforcé avec des fibres de verre, il offre une excellente rigidité, une bonne résistance chimique et thermique, tout en conservant une meilleure stabilité que d autres polymères classiques.',
    'advantages' => 'Très bonne rigidité, Haute résistance à la chaleur, Bonne résistance à l’humidité et aux produits chimiques, Bonne tenue dans le temps.',
    'disadvantages' => 'Prix plus élevé, Aspect brut.',
    'applications' => 'Pièces techniques dans l’automobile, connecteurs électriques, équipements industriels.',
    'visual_quality' => 55,
    'tensile_strength' => 85,
    'elongation' => 12,
    'impact_resistance' => 45,
    'heat_resistance' => 85,
    'humidity_resistance' => 75,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PPS-CF',
    'description' => 'Le PPS-CF est un matériau ultra-technique, reconnu pour sa résistance extrême aux hautes températures, aux produits chimiques et aux environnements agressifs. Il est renforcé en fibres de carbone pour une rigidité et une stabilité accrues. C’est un choix privilégié dans les secteurs de l’automobile, de l’aéronautique ou de l’industrie chimique.',
    'advantages' => 'Excellente résistance à la chaleur, Très grande résistance chimique, Rigidité élevée, Stabilité thermique et dimensionnelle.',
    'disadvantages' => 'Coût élevé, Peu esthétique.',
    'applications' => 'Aéronautique, automobile, électronique, composants exposés à de hautes températures ou à des produits chimiques.',
    'visual_quality' => 50,
    'tensile_strength' => 95,
    'elongation' => 20,
    'impact_resistance' => 50,
    'heat_resistance' => 95,
    'humidity_resistance' => 90,
    'interlayer_adhesion' => 70,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'PVA',
    'description' => 'Le PVA est un filament soluble dans l’eau, utilisé principalement comme support d’impression pour les pièces complexes. Il est combiné à d’autres matériaux (comme le PLA) pour imprimer des formes avec des surplombs impossibles à réaliser autrement. Une fois l’impression terminée, le support en PVA se dissout simplement dans l’eau, laissant la pièce propre et précise.',
    'advantages' => 'Idéal pour les formes complexes, Soluble dans l’eau, Précision améliorée, Compatible avec PLA et d’autres matériaux basse température.',
    'disadvantages' => 'Très sensible à l’humidité, Temps de dissolution, Assez coûteux, Peut laisser des résidus dans l’eau ou sur la pièce, nécessitant un rinçage.',
    'applications' => 'Supports pour pièces techniques complexes, prototypage, pièces avec porte-à-faux difficiles.',
    'visual_quality' => 50,
    'tensile_strength' => 30,
    'elongation' => 10,
    'impact_resistance' => 15,
    'heat_resistance' => 20,
    'humidity_resistance' => 5,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'HIPS',
    'description' => 'Le HIPS est un matériau polyvalent utilisé à la fois pour créer des pièces robustes et comme support soluble (dans le D-Limonène) en impression 3D double extrusion. Il est proche de l’ABS, mais avec une meilleure stabilité et un rendu plus propre. Il convient aussi bien aux pièces finales qu’aux prototypes solides.',
    'advantages' => 'Support parfait pour l’ABS, Bonne résistance aux chocs, Finitions propres et lisses.',
    'disadvantages' => 'Temps de dissolution long, Coût global.',
    'applications' => 'Pièces structurelles légères, maquettes, supports pour l’ABS, objets devant être peints.',
    'visual_quality' => 60,
    'tensile_strength' => 50,
    'elongation' => 30,
    'impact_resistance' => 80,
    'heat_resistance' => 50,
    'humidity_resistance' => 30,
    'interlayer_adhesion' => 60,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'ASA-CF',
    'description' => 'Le ASA-CF combine la robustesse de l’ASA avec la rigidité des fibres de carbone. Il est idéal pour des pièces techniques utilisées en extérieur, là où la stabilité dimensionnelle, la résistance aux UV et la solidité sont cruciales.',
    'advantages' => 'Résistant aux UV et aux intempéries, Très rigide et solide, Bonne tenue à la chaleur.',
    'disadvantages' => 'Fragilité aux chocs, Moins esthétique, Coût plus élevé.',
    'visual_quality' => 70,
    'tensile_strength' => 80,
    'elongation' => 30,
    'impact_resistance' => 50,
    'heat_resistance' => 70,
    'humidity_resistance' => 60,
    'interlayer_adhesion' => 70,
    'status' => 1,
]);
$wpdb->insert($table_materials, [
    'name' => 'ASA-GF',
    'description' => 'ASA renforcé avec des fibres de verre, offrant une excellente stabilité dimensionnelle et une bonne résistance aux intempéries.',
    'advantages' => 'Résistant aux UV, aux intempéries, bonne rigidité, moins abrasif que le carbone.',
    'disadvantages' => 'Peut nécessiter une buse renforcée, adhérence au plateau parfois délicate, finition moins lisse.',
    'applications' => 'Capots, boîtiers, pièces mécaniques en extérieur, prototypes fonctionnels.',
    'visual_quality' => 60,
    'tensile_strength' => 85,
    'elongation' => 25,
    'impact_resistance' => 40,
    'heat_resistance' => 75,
    'humidity_resistance' => 65,
    'interlayer_adhesion' => 65,
    'status' => 1,
]);

    }

    // ➕ Exemple de fiche "divers" (si la table est vide)
    $divers_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_divers");
    if ($divers_count == 0) {
         $wpdb->insert($table_divers, [
        'name' => 'Finesse d’impression',
        'image_url' => '',
        'description' => 'Qu’il s’agisse de fabrication additive (FDM) ou de stéréolithographie (SLA), les objets sont créés par superposition de couches successives. La finesse des détails dépend directement de la hauteur de ces couches : plus la résolution est élevée (c’est-à-dire plus la couche est fine), plus le nombre de couches nécessaires augmente pour atteindre une même hauteur, ce qui permet un rendu plus précis. Il faut toutefois garder à l’esprit que l’impression 3D par dépôt de filament (FDM) n’est pas toujours adaptée à la fabrication d’objets aux détails très fins. D’une part, parce qu’il est difficile d’atteindre des hauteurs de couche inférieures à 0,05 mm (50 microns) sans utiliser des machines très coûteuses ; d’autre part, parce que cette technologie nécessite souvent l’ajout de structures de support, ce qui complique le rendu final. Enfin, la précision mécanique des imprimantes FDM a ses limites, ce qui rend l’impression d’éléments très fins délicate. Malgré cela, l’impression 3D additive reste l’un des moyens les plus accessibles et polyvalents pour produire une grande variété d’objets à un coût raisonnable pour le grand public. Pour les besoins nécessitant une très haute précision ou un niveau de détail élevé, il est conseillé de se tourner vers l’impression 3D SLA. Cette technologie permet d’atteindre des résolutions exceptionnelles, offrant des objets aux surfaces lisses et aux détails nets, proches de ceux obtenus par moulage traditionnel.',
        'status' => 1,
    ]);

    $wpdb->insert($table_divers, [
        'name' => 'Les supports d’impression',
        'image_url' => '',
        'description' => 'Pour imprimer des porte-à-faux, l’ajout de supports est indispensable. Ces structures temporaires permettent de déposer le filament en hauteur ou d’imprimer des parties « dans le vide » en technologie SLA. Bien qu’essentiels dans la plupart des cas, les supports ont pour inconvénient de dégrader la qualité de surface de l’impression. Afin de faciliter leur retrait après impression, les supports ne sont généralement pas remplis à 100 %. Il existe des alternatives comme l\'utilisation d’un filament soluble (par exemple le PVA), combiné à une imprimante équipée de deux têtes d’extrusion. Toutefois, cette solution complexifie le processus d’impression sans toujours offrir un gain qualitatif significatif. Il est donc fortement recommandé de concevoir les pièces en limitant au maximum la nécessité de supports. En impression 3D SLA, ces supports sont également indispensables. Leur retrait laisse souvent de légères marques, qui peuvent cependant disparaître aisément avec un léger ponçage.',
        'status' => 1,
    ]);

    $wpdb->insert($table_divers, [
        'name' => 'Le remplissage',
        'image_url' => '',
        'description' => 'Un paramètre essentiel en impression 3D est le taux de remplissage (ou infill). Un taux plus élevé permet d’obtenir des pièces plus solides, mais augmente la consommation de matière ainsi que le temps d’impression — en particulier avec la technologie FDM. Avec des matériaux flexibles, le taux de remplissage influence directement la souplesse de l’objet : plus il est faible, plus l’objet sera déformable. À l’inverse, un remplissage dense limitera cette flexibilité. En règle générale, un taux de remplissage compris entre 20 % et 30 % convient parfaitement pour les objets décoratifs ou les pièces ne subissant pas de contraintes mécaniques importantes.',
        'status' => 1,
    ]);

    $wpdb->insert($table_divers, [
        'name' => 'La finition des impressions 3D FDM',
        'image_url' => '',
        'description' => 'Pour compenser les limitations visuelles de la fabrication additive, plusieurs techniques de finition permettent d’obtenir un rendu propre et soigné. L’utilisation d’une résine époxy spéciale permet de combler les stries entre les couches, offrant ainsi une surface lisse au toucher et visuellement homogène. Afin d’améliorer encore l’esthétique, une mise en peinture est souvent appliquée, permettant d’obtenir une finition à la fois agréable au toucher et soignée visuellement. Il convient toutefois de noter que certains matériaux, comme les filaments flexibles ou le nylon, se prêtent mal à ce type de traitement de surface. Il est donc important de choisir un matériau adapté au niveau de finition souhaité, ou de réserver ces matériaux à des pièces techniques où l’esthétique est secondaire. Pour plus de détails sur les propriétés et usages des différents matériaux, consultez notre rubrique dédiée : Guides des filaments.',
        'status' => 1,
    ]);
    }
}

register_activation_hook(__FILE__, 'galerie3d_activate');

// 🔻 Désactivation : suppression des tables (facultatif)
function galerie3d_deactivate() {
    global $wpdb;

    $tables = [
        $wpdb->prefix . 'p3d_galerie3d',
        $wpdb->prefix . 'p3d_materials_g',
        $wpdb->prefix . 'p3d_divers_g',
    ];

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}

register_deactivation_hook(__FILE__, 'galerie3d_deactivate');
