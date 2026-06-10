import './bootstrap';
import './quan-ly-nha-tro';
import './rentry';

const THEME_STORAGE_KEY = 'renty_theme_mode';

function applyThemeMode(mode) {
    const isLight = mode === 'light';
    document.documentElement.classList.toggle('theme-light', isLight);
    document.body?.classList.toggle('theme-light', isLight);

    document.querySelectorAll('[data-theme-icon], #theme-toggle-icon').forEach((icon) => {
        icon.classList.toggle('fa-sun', isLight);
        icon.classList.toggle('fa-moon', !isLight);
    });

    document.querySelectorAll('[data-theme-label]').forEach((label) => {
        label.textContent = isLight ? 'Sáng' : 'Tối';
    });
}

window.applyThemeMode = applyThemeMode;
window.toggleThemeMode = function toggleThemeMode() {
    const nextMode = document.body?.classList.contains('theme-light') ? 'dark' : 'light';
    localStorage.setItem(THEME_STORAGE_KEY, nextMode);
    applyThemeMode(nextMode);
};

document.addEventListener('DOMContentLoaded', () => {
    applyThemeMode(localStorage.getItem(THEME_STORAGE_KEY) || 'dark');
});
