import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PhotoSwipe (Home) - inicialización segura para producción
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';
// Iconos Phosphor (web) - carga de clases CSS (opcional)
// Nota: usamos SVGs locales de Phosphor mediante <x-icon>, no es necesario importar el paquete web.

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
			// Hint: podríamos añadir una animación de scale/fade si se quisiera más adelante
		});
	}

	const galleryEl = document.querySelector('.sn-gallery-compact');
	if (galleryEl) {
		// Calcula padding vertical para dejar el área visible en ~66vh (2/3 de la pantalla)
		const padV = Math.round(window.innerHeight * 0.17); // 17% arriba y abajo => 34% total
		const lightbox = new PhotoSwipeLightbox({
			gallery: '.sn-gallery-compact',
			children: 'a',
			showHideAnimationType: 'fade',
			// Asegura que la imagen no ocupe el 100% de alto: reservamos padding arriba y abajo
			padding: { top: padV, bottom: padV, left: 0, right: 0 },
			// Mantener encuadre "fit" para centrar y ajustar dentro del área útil
			initialZoomLevel: 'fit',
			pswpModule: () => import('photoswipe')
		});
		lightbox.init();
	}
});
