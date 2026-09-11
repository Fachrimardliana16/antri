import './echo';
import './audio';
import './printer';
import './theme';

// Dynamic Theme Listener: when theme updates via WebSocket, update CSS variables instantly
document.addEventListener('DOMContentLoaded', () => {
    if (window.Echo) {
        window.Echo.channel('theme')
            .listen('.theme.updated', (e) => {
                if (e.settings) {
                    if (e.settings.primary_color) {
                        document.documentElement.style.setProperty('--primary', e.settings.primary_color);
                    }
                    if (e.settings.secondary_color) {
                        document.documentElement.style.setProperty('--secondary', e.settings.secondary_color);
                    }
                }
            });
    }
});
