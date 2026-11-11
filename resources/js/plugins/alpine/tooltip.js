import  tippy, { followCursor } from 'tippy.js';

export default function (Alpine) {
    // Filament ships its own x-tooltip directive; keep it on Filament pages (<html class="fi">).
    if (document.documentElement.classList.contains('fi')) return;

    Alpine.directive('tooltip', (el, { expression }, { evaluate }) => {
        let options = evaluate(expression);
        Object.keys(options).forEach((key) => {
            if (key === 'text') {
                options.content = options.text;
                delete options[key];
            }

            if (key === 'followCursor') {
                options.plugins = [followCursor];
            }
        });
        tippy(el, options);
    });

    tippy.setDefaultProps({
        theme: 'custom',
    });
}
