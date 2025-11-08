import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PhotoSwipe (Home) - inicialización segura para producción
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';

document.addEventListener('DOMContentLoaded', () => {
	// Toggle tema claro/oscuro
	const rootHtml = document.documentElement;
	const storedTheme = localStorage.getItem('sn-theme');
	if (storedTheme === 'light' || storedTheme === 'dark') {
		rootHtml.setAttribute('data-theme', storedTheme);
	}
	const themeBtn = document.getElementById('theme-toggle');
	if (themeBtn) {
		themeBtn.addEventListener('click', () => {
			const current = rootHtml.getAttribute('data-theme') || 'dark';
			const next = current === 'dark' ? 'light' : 'dark';
			rootHtml.setAttribute('data-theme', next);
			localStorage.setItem('sn-theme', next);
		});
	}

	// Menú móvil hamburguesa
	const mobileToggle = document.getElementById('mobile-menu-toggle');
	const navMenu = document.querySelector('.nav-menu');
	if (mobileToggle && navMenu) {
		mobileToggle.addEventListener('click', () => {
			navMenu.classList.toggle('is-open');
		});
	}

	const galleryEl = document.querySelector('.sn-home-gallery');
	if (galleryEl) {
		const lightbox = new PhotoSwipeLightbox({
			gallery: '.sn-home-gallery',
			children: 'a',
			showHideAnimationType: 'fade',
			pswpModule: () => import('photoswipe')
		});
		lightbox.init();
	}
});
