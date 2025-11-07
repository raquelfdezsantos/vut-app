import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// PhotoSwipe (Home) - inicialización segura para producción
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import 'photoswipe/style.css';

document.addEventListener('DOMContentLoaded', () => {
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
