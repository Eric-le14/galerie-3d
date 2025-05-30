
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

jQuery(document).ready(function ($) {
    $('.nav-tab').on('click', function (e) {
        e.preventDefault();

        // Activer l'onglet
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Afficher le contenu correspondant
        $('.tab-content').hide();
        const tab = $(this).attr('href');
        $(tab).show();
    });
});

jQuery(document).ready(function($) {
    // Cocher / décocher toutes les cases
    $('#galerie3d-select-all').on('change', function() {
        const checked = $(this).is(':checked');
        $('input[name="order_ids[]"]').prop('checked', checked);
    });

    // Mise à jour du "select all" si une case est décochée manuellement
    $('body').on('change', 'input[name="order_ids[]"]', function() {
        const total = $('input[name="order_ids[]"]').length;
        const checked = $('input[name="order_ids[]"]:checked').length;
        $('#galerie3d-select-all').prop('checked', total === checked);
    });
});


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".galerie3d-toggle-status").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            const linkEl = this;

            fetch(p3d_ajax_data.ajaxurl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    action: "p3d_toggle_material_status",
                    id: id,
                    _ajax_nonce: p3d_ajax_data.nonce
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const newStatus = data.data.new_status;
                    const row = linkEl.closest("tr");
                    const statusCell = row.querySelector("td:nth-child(5) strong");
                    statusCell.textContent = newStatus ? "✔️" : "❌";
                    linkEl.textContent = newStatus ? "❌ désactiver" : "✔️ activer";
                } else {
                    alert("Erreur : " + data.data);
                }
            })
            .catch(err => alert("Erreur AJAX : " + err));
        });
    });
});
