/**
 * Theme Manager - Sistem Antrian Terpadu
 * Manages light/dark mode across all pages
 */

const THEME_KEY = 'antrian-theme';

function getStoredTheme() {
    try {
        return localStorage.getItem(THEME_KEY) || 'light';
    } catch {
        return 'light';
    }
}

function setStoredTheme(theme) {
    try {
        localStorage.setItem(THEME_KEY, theme);
    } catch {}
}

function applyTheme(theme) {
    const root = document.documentElement;
    root.setAttribute('data-theme', theme);
    // Dispatch event so Alpine components can react
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
}

function toggleTheme() {
    const current = getStoredTheme();
    const next = current === 'light' ? 'dark' : 'light';
    setStoredTheme(next);
    applyTheme(next);
    return next;
}

function initTheme() {
    const theme = getStoredTheme();
    applyTheme(theme);
}

// Run immediately before paint to avoid flicker
initTheme();

// Export for use in Alpine / inline scripts
window.ThemeManager = { getStoredTheme, toggleTheme, initTheme };
