document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.p3d-toggle-status').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const type = this.dataset.type; // commande, materiau, ou divers

            fetch(ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'p3d_toggle_status',
                    nonce: p3d_toggle_status.nonce,
                    id: id,
                    type: type
                })
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    const icon = this.querySelector('span');
                    icon.textContent = response.data.status ? '✔️' : '❌';
                } else {
                    alert('Erreur : ' + response.data);
                }
            });
        });
    });
});
