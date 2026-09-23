import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import flatpickr from 'flatpickr';

window.Alpine = Alpine;
window.flatpickr = flatpickr;

// Initialize Lucide icons on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Re-render helper for Alpine/dynamic Blade templates
window.refreshIcons = () => {
    createIcons({ icons });
};

Alpine.start();