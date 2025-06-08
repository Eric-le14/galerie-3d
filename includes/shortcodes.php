<?php
function p3d_display_all_materials_paginated($atts) {
    $atts = shortcode_atts(['per_page' => 5], $atts);

    ob_start();
    ?>
    <div id="p3d-materials-wrapper" data-page="1" data-per-page="<?php echo intval($atts['per_page']); ?>"></div>
    <div id="p3d-pagination-controls" style="margin-top:20px; text-align:center;">
        <button id="p3d-prev" disabled>← Précédent</button>
        <button id="p3d-next">Suivant →</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrapper = document.getElementById('p3d-materials-wrapper');
        const prevBtn = document.getElementById('p3d-prev');
        const nextBtn = document.getElementById('p3d-next');

        function loadPage(page) {
            const perPage = wrapper.getAttribute('data-per-page');

            fetch(`<?php echo admin_url('admin-ajax.php'); ?>?action=p3d_get_materials_page&page=${page}&per_page=${perPage}`)
                .then(res => res.text())
                .then(html => {
                    wrapper.innerHTML = html;
                    wrapper.setAttribute('data-page', page);
                    prevBtn.disabled = page <= 1;
                });
        }

        prevBtn.addEventListener('click', () => {
            let page = parseInt(wrapper.getAttribute('data-page'));
            if (page > 1) loadPage(page - 1);
        });

        nextBtn.addEventListener('click', () => {
            let page = parseInt(wrapper.getAttribute('data-page'));
            loadPage(page + 1);
        });

        loadPage(1);
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('p3d_all_materials_paginated', 'p3d_display_all_materials_paginated');
