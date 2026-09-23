import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import { createIcons, icons } from 'lucide';
import flatpickr from 'flatpickr';
import Lenis from 'lenis';

// Register Alpine plugins
Alpine.plugin(intersect);

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

// Initialize Lenis Smooth Scrolling (Performance & Smoothness)
const lenis = new Lenis({
    duration: 1.1,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    smoothWheel: true,
    touchMultiplier: 1.5,
});

window.lenis = lenis;

function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}
requestAnimationFrame(raf);

Alpine.start();