import './bootstrap';
import './instant-navigate';
import Alpine from 'alpinejs';

// Global Alpine Store for UI Effects and Settings
Alpine.store('effects', {
    petalsEnabled: localStorage.getItem('rp_petals_enabled') !== 'false',
    togglePetals() {
        this.petalsEnabled = !this.petalsEnabled;
        localStorage.setItem('rp_petals_enabled', this.petalsEnabled);
    }
});

// Global Lightbox Modal Store
Alpine.store('lightbox', {
    isOpen: false,
    currentImage: '',
    title: '',
    images: [],
    currentIndex: 0,
    open(imgUrl, title = '', allImages = []) {
        this.currentImage = imgUrl;
        this.title = title;
        this.images = allImages.length > 0 ? allImages : [imgUrl];
        this.currentIndex = this.images.indexOf(imgUrl);
        if (this.currentIndex === -1) this.currentIndex = 0;
        this.isOpen = true;
        document.body.classList.add('overflow-hidden');
    },
    close() {
        this.isOpen = false;
        document.body.classList.remove('overflow-hidden');
    },
    next() {
        if (this.images.length > 1) {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
            this.currentImage = this.images[this.currentIndex];
        }
    },
    prev() {
        if (this.images.length > 1) {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            this.currentImage = this.images[this.currentIndex];
        }
    }
});

// Keyboard controls for Lightbox
window.addEventListener('keydown', (e) => {
    const lightbox = Alpine.store('lightbox');
    if (!lightbox || !lightbox.isOpen) return;

    if (e.key === 'Escape') {
        lightbox.close();
    } else if (e.key === 'ArrowRight') {
        lightbox.next();
    } else if (e.key === 'ArrowLeft') {
        lightbox.prev();
    }
});

// High-Performance IntersectionObserver for Scroll Reveal
document.addEventListener('DOMContentLoaded', () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealElements = document.querySelectorAll('[data-reveal], .reveal-init');

    if (prefersReducedMotion) {
        revealElements.forEach(el => el.classList.add('reveal-active'));
        return;
    }

    if ('IntersectionObserver' in window) {
        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -60px 0px',
            threshold: 0.12
        };

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-active');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        revealElements.forEach(el => {
            revealObserver.observe(el);
        });
    } else {
        // Fallback for older browsers
        revealElements.forEach(el => el.classList.add('reveal-active'));
    }
});

window.Alpine = Alpine;
Alpine.start();
