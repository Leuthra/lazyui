/*
 * Shared LazyUI runtime: Alpine plugins (dropdown, dialog, sheet, select, accordion),
 * the toast and alert helpers, and the front-end sidebar store.
 * Alpine comes from Livewire 4 (and therefore Filament); plugins register on `alpine:init`.
 *
 * Based on LazyUI by Ilsya (MIT).
 */
import './plugins/vanilla/toast.js';
import './plugins/vanilla/alert.js';

import accordion from './plugins/alpine/accordion.js';
import dialog from './plugins/alpine/dialog.js';
import dropdown from './plugins/alpine/dropdown.js';
import select from './plugins/alpine/select.js';
import sheet from './plugins/alpine/sheet.js';

export function boot(extraPlugins = []) {
    const plugins = [accordion, dialog, dropdown, select, sheet, ...extraPlugins];

    const register = (Alpine) => {
        if (Alpine.__lazyuiRegistered) return;
        Alpine.__lazyuiRegistered = true;

        plugins.forEach((plugin) => Alpine.plugin(plugin));

        Alpine.store('lazySidebar', {
            isOpen: false,
            toggle() {
                this.isOpen = !this.isOpen;
            },
            close() {
                this.isOpen = false;
            },
            open() {
                this.isOpen = true;
            },
        });
    };

    if (window.Alpine) {
        register(window.Alpine);
    } else {
        document.addEventListener('alpine:init', () => register(window.Alpine), { once: true });
    }

    window.LazyUI = {
        version: '2.0.0',
        name: 'LazyUI',
        license: 'MIT',
        credits: 'Based on LazyUI by Ilsya (https://github.com/ilsyaa/lazyui)',
    };
}
