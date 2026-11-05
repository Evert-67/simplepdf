document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    const search = document.getElementById('smppb-source-search');
    const results = document.getElementById('smppb-source-results');
    const source = document.getElementById('smppb-preview-source');
    const status = document.getElementById('smppb-source-status');
    const preview = document.getElementById('smppb-saved-preview');
    if (!search || !results || !source || !status) return;
    function updatePreview() {
        if (!preview) return;
        const url = new URL(preview.href);
        url.searchParams.set('post_id', String(Math.max(0, parseInt(source.value, 10) || 0)));
        preview.href = url.toString();
    }
    source.addEventListener('input', updatePreview);
    results.addEventListener('change', function () {
        if (!results.value) return;
        source.value = results.value;
        updatePreview();
    });
    let timer, controller, requestNumber = 0;
    search.addEventListener('input', function () {
        clearTimeout(timer);
        if (controller) controller.abort();
        const current = ++requestNumber;
        results.replaceChildren(); results.hidden = true;
        const term = search.value.trim();
        if (term.length < 2) { status.textContent = 'Enter at least two characters.'; return; }
        timer = setTimeout(async function () {
            controller = new AbortController();
            status.textContent = 'Searching…';
            const url = new URL(smppbManager.ajaxUrl);
            url.searchParams.set('action', 'smppb_source_search');
            url.searchParams.set('nonce', smppbManager.nonce);
            url.searchParams.set('search', term);
            try {
                const response = await fetch(url, {credentials:'same-origin', signal:controller.signal});
                const data = await response.json();
                if (current !== requestNumber) return;
                if (!response.ok || !data.success || !Array.isArray(data.data)) throw new Error('Search unavailable');
                results.replaceChildren();
                const placeholder = document.createElement('option');
                placeholder.value = ''; placeholder.textContent = 'Choose preview content'; results.append(placeholder);
                data.data.forEach(function (item) {
                    const option = document.createElement('option'); option.value = String(item.id); option.textContent = item.label; results.append(option);
                });
                results.hidden = data.data.length === 0;
                status.textContent = data.data.length ? 'Choose a result below (maximum 30).' : 'No readable content found.';
            } catch (error) {
                if (error.name !== 'AbortError' && current === requestNumber) status.textContent = 'Search failed. Try again or enter the post ID.';
            }
        }, 250);
    });
});
