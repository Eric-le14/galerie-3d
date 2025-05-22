<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Sécurité : on bloque l'accès direct
}

// Variables reçues
$paged = isset( $_GET['paged'] ) ? max( 1, intval($_GET['paged']) ) : 1;
$items_per_page = 20;

// Connexion à la base (ici via $wpdb)
global $wpdb;
$table_name = $wpdb->prefix . 'galerie3d_items'; // nom table des réalisations

// Calcul de l'offset pour pagination
$offset = ( $paged - 1 ) * $items_per_page;

// Récupérer les réalisations
$items = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY date_added DESC LIMIT %d OFFSET %d",
        $items_per_page,
        $offset
    )
);

// Nombre total pour pagination
$total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
$total_pages = ceil( $total_items / $items_per_page );

?>

<div class="galerie3d-gallery">
    <?php if ( $items ) : ?>
        <ul class="galerie3d-list">
            <?php foreach ( $items as $item ) : ?>
                <li class="galerie3d-item">
                    <div class="galerie3d-thumb">
                        <img src="<?php echo esc_url( $item->image_url ); ?>" alt="<?php echo esc_attr( $item->title ); ?>" />
                    </div>
                    <h3 class="galerie3d-title"><?php echo esc_html( $item->title ); ?></h3>
                    <p class="galerie3d-matter">Matière : <?php echo esc_html( $item->matter ); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="galerie3d-pagination">
            <?php if ( $paged > 1 ) : ?>
                <a href="<?php echo esc_url( add_query_arg( 'paged', $paged - 1 ) ); ?>" class="prev-page">&laquo; Précédent</a>
            <?php endif; ?>

            <?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
                <?php if ( $i == $paged ) : ?>
                    <span class="current-page"><?php echo $i; ?></span>
                <?php else : ?>
                    <a href="<?php echo esc_url( add_query_arg( 'paged', $i ) ); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ( $paged < $total_pages ) : ?>
                <a href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>" class="next-page">Suivant &raquo;</a>
            <?php endif; ?>
        </div>

    <?php else : ?>
        <p>Aucune réalisation trouvée.</p>
    <?php endif; ?>
</div>