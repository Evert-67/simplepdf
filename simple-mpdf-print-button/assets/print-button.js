document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.smppb-control').forEach(function (control) {
        const select = control.querySelector('[data-smppb-select]');
        const description = control.querySelector('[data-smppb-description]');
        if (!select || !description) return;
        function update() {
            const option = select.options[select.selectedIndex];
            const text = option ? (option.getAttribute('data-desc') || '') : '';
            description.textContent = text;
            description.hidden = !text.trim();
        }
        select.addEventListener('change', update);
        update();
    });
});
