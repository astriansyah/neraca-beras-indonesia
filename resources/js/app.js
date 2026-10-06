import Alpine from 'alpinejs';

// Setup store UI global untuk Alpine
Alpine.store('ui', {
    dark: document.documentElement.classList.contains('dark'),
    collapsed: document.documentElement.getAttribute('data-sb') === 'collapsed',

    toggleTheme() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        try {
            localStorage.setItem('nb-theme', this.dark ? 'dark' : 'light');
        } catch (e) {}
        window.dispatchEvent(new CustomEvent('nb-theme-change', { detail: { dark: this.dark } }));
    },

    toggleSidebar() {
        this.collapsed = !this.collapsed;
        if (this.collapsed) {
            document.documentElement.setAttribute('data-sb', 'collapsed');
            try {
                localStorage.setItem('nb-sidebar', 'collapsed');
            } catch (e) {}
        } else {
            document.documentElement.removeAttribute('data-sb');
            try {
                localStorage.setItem('nb-sidebar', 'expanded');
            } catch (e) {}
        }
        window.dispatchEvent(new Event('resize'));
    }
});

window.Alpine = Alpine;
Alpine.start();
