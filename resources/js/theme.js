/*
 * LazyUI appearance (dark mode, accent, font, sidebar).
 * Inlined in the document head by the lazy::head component, so the page never flashes the wrong theme.
 *
 * Dark mode is stored under Filament's own key (`filament:theme:mode`, values light|dark|system)
 * and applied with the `dark` class, so the front end and every Filament panel stay in sync.
 * Accent, font and sidebar state live under `lazy-settings`.
 *
 * Based on LazyUI by Ilsya (MIT).
 */
(() => {
    if (window.LazySettings) return;

    const config = window.LazyUIConfig || {};
    const MODE_KEY = 'filament:theme:mode';
    const STORE_KEY = config.storageKey || 'lazy-settings';
    const EVENT_KEY = config.eventKey || 'lazy-appearance-change';
    const html = document.documentElement;
    const media = window.matchMedia('(prefers-color-scheme: dark)');

    const read = () => {
        try {
            return JSON.parse(localStorage.getItem(STORE_KEY) || '{}');
        } catch {
            return {};
        }
    };

    const write = (key, value) => {
        localStorage.setItem(STORE_KEY, JSON.stringify({ ...read(), [key]: value }));
    };

    const toFilament = (mode) => (mode === 'auto' ? 'system' : mode);
    const fromFilament = (mode) => (mode === 'system' ? 'auto' : mode);
    const resolve = (mode) => (mode === 'auto' ? (media.matches ? 'dark' : 'light') : mode);

    const withoutTransitions = () => {
        const style = document.createElement('style');
        style.textContent = '*{transition:none!important}';
        document.head.appendChild(style);
        setTimeout(() => style.remove(), 50);
    };

    const LazySettings = {
        defaults: {
            mode: config.mode || 'auto',
            accent: config.accent || 'indigo',
            font: config.font || 'inter',
            aside: config.aside || 'show',
        },

        state: { mode: 'auto', accent: 'indigo', font: 'inter', aside: 'show' },

        init() {
            const saved = read();

            this.state.mode = fromFilament(localStorage.getItem(MODE_KEY)) || this.defaults.mode;
            // Stored values may point at an accent/font that was removed from config/lazyui.php.
            const known = (list, value) => (!Array.isArray(list) || list.includes(value) ? value : null);
            this.state.accent = known(config.accents, saved.themeAccent) || this.defaults.accent;
            this.state.font = known(config.fonts, saved.themeFont) || this.defaults.font;
            this.state.aside = saved.themeAside || this.defaults.aside;

            html.classList.add('lazy');
            this._applyMode(this.state.mode);
            html.setAttribute('theme-accent', this.state.accent);
            html.setAttribute('theme-font', this.state.font);
            html.setAttribute('theme-aside', this.state.aside);
        },

        /* mode: light | dark | auto | toggle */
        setTheme(mode = 'toggle', save = true, dispatch = true) {
            if (mode === 'toggle') {
                mode = resolve(this.state.mode) === 'dark' ? 'light' : 'dark';
            }

            withoutTransitions();
            this.state.mode = mode;
            this._applyMode(mode);

            if (save) localStorage.setItem(MODE_KEY, toFilament(mode));

            // Lets Filament's own theme store follow along.
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: toFilament(mode) }));

            this._emit(dispatch);
        },

        setAccent(accent, save = true, dispatch = true) {
            this.state.accent = accent;
            html.setAttribute('theme-accent', accent);
            if (save) write('themeAccent', accent);
            this._emit(dispatch);
        },

        setFont(font, save = true, dispatch = true) {
            this.state.font = font;
            html.setAttribute('theme-font', font);
            if (save) write('themeFont', font);
            this._emit(dispatch);
        },

        /* aside: show | hide | toggle */
        setAside(aside = 'toggle', save = true, dispatch = true) {
            if (aside === 'toggle') aside = this.state.aside === 'show' ? 'hide' : 'show';
            this.state.aside = aside;
            html.setAttribute('theme-aside', aside);
            if (save) write('themeAside', aside);
            this._emit(dispatch);
        },

        resetAppearance() {
            localStorage.removeItem(STORE_KEY);
            this.setTheme(this.defaults.mode, true, false);
            this.setAccent(this.defaults.accent, false, false);
            this.setFont(this.defaults.font, false, false);
            this.setAside(this.defaults.aside, false, false);
            this._emit(true);
        },

        isDark() {
            return resolve(this.state.mode) === 'dark';
        },

        _applyMode(mode) {
            const resolved = resolve(mode);
            html.classList.toggle('dark', resolved === 'dark');
            html.setAttribute('theme-mode', resolved);
        },

        _emit(dispatch) {
            if (dispatch) {
                window.dispatchEvent(new CustomEvent(EVENT_KEY, { detail: { ...this.state } }));
            }
        },
    };

    LazySettings.init();
    window.LazySettings = LazySettings;

    // Filament's theme switcher (user menu) changed the mode: mirror it.
    window.addEventListener('theme-changed', (event) => {
        const mode = fromFilament(event.detail);
        if (!mode || mode === window.LazySettings.state.mode) return;
        window.LazySettings.state.mode = mode;
        window.LazySettings._applyMode(mode);
        window.LazySettings._emit(true);
    });

    media.addEventListener('change', () => {
        if (window.LazySettings.state.mode === 'auto') {
            window.LazySettings._applyMode('auto');
            window.LazySettings._emit(true);
        }
    });

    document.addEventListener('alpine:init', () => {
        // Re-expose the reactive proxy so bindings like $appearance.state.accent update live.
        window.Alpine.store('lazy-settings', LazySettings);
        window.LazySettings = window.Alpine.store('lazy-settings');
        window.Alpine.magic('appearance', () => window.Alpine.store('lazy-settings'));
    });

    if (config.darkModeShortcut !== false) {
        // Ctrl + D toggles dark mode.
        document.addEventListener('keydown', (event) => {
            if (event.ctrlKey && !event.shiftKey && !event.altKey && event.key === 'd') {
                event.preventDefault();
                window.LazySettings.setTheme('toggle');
            }
        });
    }
})();
