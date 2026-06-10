import './bootstrap';
import './quan-ly-nha-tro';
import './rentry';

const THEME_STORAGE_KEY = 'renty_theme_mode';

function applyThemeMode(mode) {
    const isLight = mode === 'light';
    document.documentElement.classList.toggle('theme-light', isLight);
    document.body?.classList.toggle('theme-light', isLight);

    document.querySelectorAll('[data-theme-icon], #theme-toggle-icon').forEach((icon) => {
        // Skip custom dual-icon switches
        if (!icon.classList.contains('theme-switch-icon')) {
            icon.classList.toggle('fa-sun', isLight);
            icon.classList.toggle('fa-moon', !isLight);
        }
    });

    document.querySelectorAll('[data-theme-label]').forEach((label) => {
        label.textContent = isLight ? 'Sáng' : 'Tối';
    });

    document.querySelectorAll('[data-theme-switch]').forEach((button) => {
        button.classList.toggle('is-light', isLight);
        button.setAttribute('aria-pressed', isLight ? 'true' : 'false');
    });
}

window.applyThemeMode = applyThemeMode;
window.toggleThemeMode = function toggleThemeMode() {
    const nextMode = document.body?.classList.contains('theme-light') ? 'dark' : 'light';
    localStorage.setItem(THEME_STORAGE_KEY, nextMode);

    // Add flipping animation class to body
    document.body?.classList.remove('theme-flipping');
    if (document.body) {
        void document.body.offsetWidth;
        document.body.classList.add('theme-flipping');
    }

    // Add animating class to switches
    document.querySelectorAll('[data-theme-switch]').forEach((button) => {
        button.classList.remove('is-animating');
        void button.offsetWidth;
        button.classList.add('is-animating');
    });

    applyThemeMode(nextMode);
};

document.addEventListener('DOMContentLoaded', () => {
    applyThemeMode(localStorage.getItem(THEME_STORAGE_KEY) || 'dark');
});
