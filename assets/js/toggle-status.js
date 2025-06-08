jQuery(document).ready(function($) {
    $('.p3d-toggle-status').on('click', function(e) {
        e.preventDefault();

        const id = $(this).data('id');
        const type = $(this).data('type');
        const span = $(this);

        $.post(ajaxurl, {
            action: 'p3d_toggle_status',
            id: id,
            type: type,
            nonce: p3d_toggle_status.nonce,
        }, function(response) {
            if (response.success) {
                const status = response.data.status;
                span.text(status ? '✔️' : '❌');
            } else {
                alert('Erreur : ' + response.data);
            }
        });
    });
});
