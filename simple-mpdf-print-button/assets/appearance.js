document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    const preview = document.getElementById('smppb-appearance-preview');
    if (!preview) return;
    const enabled = document.getElementById('smppb-custom-enabled');
    const size = document.getElementById('smppb-size');
    const radius = document.getElementById('smppb-radius');
    const icon = document.getElementById('smppb-icon');
    const contrast = document.getElementById('smppb-contrast');
    const colours = document.querySelectorAll('[data-smppb-colour]');
    function luminance(hex) {
        const c = hex.replace('#', '').match(/.{2}/g).map(v => parseInt(v,16)/255).map(v => v <= .04045 ? v/12.92 : Math.pow((v+.055)/1.055,2.4));
        return c[0]*.2126+c[1]*.7152+c[2]*.0722;
    }
    function ratio(a,b) { const x=luminance(a),y=luminance(b);return (Math.max(x,y)+.05)/(Math.min(x,y)+.05); }
    function update() {
        preview.classList.toggle('smppb-custom', enabled.checked);
        ['theme','small','normal','large'].forEach(v => preview.classList.toggle('smppb-size-'+v,size.value===v));
        const values = {};
        colours.forEach(input => {
            if (!/^#[0-9a-f]{6}$/i.test(input.value)) return;
            const key=input.dataset.smppbColour;values[key]=input.value;preview.style.setProperty('--smppb-'+key,input.value);
        });
        preview.style.setProperty('--smppb-radius',Math.max(0,Math.min(40,parseInt(radius.value,10)||0))+'px');
        const button=preview.querySelector('button');
        const old=button.querySelector('.smppb-icon');if(old)old.remove();
        // The icon strings are static SVGs supplied by the plugin, never user HTML.
        if (smppbAppearance.icons[icon.value]) button.insertAdjacentHTML('afterbegin',smppbAppearance.icons[icon.value]);
        if (!enabled.checked) { contrast.textContent='Theme colours active; check contrast on your website.';return; }
        if (!values.background || !values.text || !values['hover-background'] || !values['hover-text']) return;
        const normal=ratio(values.background,values.text),hover=ratio(values['hover-background'],values['hover-text']);
        contrast.textContent='Text contrast: '+normal.toFixed(2)+':1 · hover: '+hover.toFixed(2)+':1. '+(Math.min(normal,hover)>=4.5?'Both meet 4.5:1.':'Choose colours with at least 4.5:1 for readable text.');
    }
    [enabled,size,radius,icon,...colours].forEach(input=>input.addEventListener('input',update));
    update();
});
