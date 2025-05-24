jQuery(document).ready(function ($) {
    $('.galerie3d-toggle-status').on('click', function (e) {
        e.preventDefault();
        const link = $(this);
        const orderId = link.data('id');

        $.ajax({
            url: galerie3d.ajaxurl,
            method: 'POST',
            data: {
                action: 'galerie3d_toggle_status', // <- Corrigé ici
                nonce: galerie3d.nonce,
                order_id: orderId
            },
            success: function (response) {
                if (response.success) {
                    location.reload(); // Recharge pour voir les changements
                } else {
                    alert(response.data.message || 'Erreur lors de la mise à jour du statut.');
                }
            }
        });
    });
});
