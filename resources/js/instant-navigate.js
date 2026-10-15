/**
 * InstantNavigate: High-Performance Page Preloader, Top Progress Bar & Smooth Transitions
 * Gives Laravel multi-page applications an instant, SPA-like responsiveness without page lag.
 */

class InstantNavigateManager {
    constructor() {
        this.prefetchedUrls = new Set();
        this.progressBar = null;
        this.progressTimer = null;
        this.currentProgress = 0;
        this.hoverTimeout = null;
        this.init();
    }

    init() {
        this.createProgressBar();
        this.attachLinkListeners();
        this.attachFormListeners();
        this.handleBfCache();
    }

    /**
     * Create high-aesthetic top loading bar (Linear/GitHub style)
     */
    createProgressBar() {
        if (document.getElementById('instant-progress-bar')) {
            this.progressBar = document.getElementById('instant-progress-bar');
            return;
        }

        const bar = document.createElement('div');
        bar.id = 'instant-progress-bar';
        bar.setAttribute('aria-hidden', 'true');
        bar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #D48C9A 0%, #E07A5F 50%, #D48C9A 100%);
            background-size: 200% 100%;
            box-shadow: 0 0 10px rgba(224, 122, 95, 0.7), 0 0 5px rgba(212, 140, 154, 0.5);
            z-index: 99999;
            pointer-events: none;
            opacity: 0;
            transition: width 200ms cubic-bezier(0.1, 0.9, 0.2, 1), opacity 200ms ease;
        `;
        document.documentElement.appendChild(bar);
        this.progressBar = bar;
    }

    startProgress() {
        if (!this.progressBar) return;
        clearInterval(this.progressTimer);
        
        this.currentProgress = 15;
        this.progressBar.style.opacity = '1';
        this.progressBar.style.width = '15%';

        this.progressTimer = setInterval(() => {
            if (this.currentProgress < 65) {
                this.currentProgress += Math.random() * 12 + 6;
            } else if (this.currentProgress < 88) {
                this.currentProgress += Math.random() * 4 + 1;
            }
            if (this.currentProgress > 92) {
                this.currentProgress = 92;
                clearInterval(this.progressTimer);
            }
            this.progressBar.style.width = `${this.currentProgress}%`;
        }, 120);
    }

    finishProgress() {
        if (!this.progressBar) return;
        clearInterval(this.progressTimer);
        this.currentProgress = 100;
        this.progressBar.style.width = '100%';

        setTimeout(() => {
            this.progressBar.style.opacity = '0';
            setTimeout(() => {
                this.progressBar.style.width = '0%';
                this.currentProgress = 0;
            }, 250);
        }, 150);
    }

    /**
     * Checks if URL is safe and beneficial to prefetch
     */
    isEligibleForPrefetch(urlStr) {
        if (!urlStr) return false;
        
        // Respect user's data-saver preference
        if (navigator.connection && (navigator.connection.saveData || /2g/.test(navigator.connection.effectiveType))) {
            return false;
        }

        try {
            const targetUrl = new URL(urlStr, window.location.href);
            const currentUrl = new URL(window.location.href);

            // Same origin only
            if (targetUrl.origin !== currentUrl.origin) return false;

            // Don't prefetch current page anchor
            if (targetUrl.pathname === currentUrl.pathname && targetUrl.search === currentUrl.search) {
                return false;
            }

            // Don't prefetch logout, non-GET or mutation paths
            const ignoreKeywords = ['logout', 'delete', 'destroy', 'batal', 'export', 'download', 'toggle'];
            if (ignoreKeywords.some(keyword => targetUrl.pathname.toLowerCase().includes(keyword))) {
                return false;
            }

            // Ignore non-HTML file extensions
            const fileExtRegex = /\.(pdf|zip|rar|jpg|jpeg|png|webp|gif|svg|csv|xlsx|docx)$/i;
            if (fileExtRegex.test(targetUrl.pathname)) {
                return false;
            }

            return true;
        } catch {
            return false;
        }
    }

    prefetchUrl(urlStr) {
        if (!this.isEligibleForPrefetch(urlStr)) return;

        const cleanUrl = new URL(urlStr, window.location.href).href;
        if (this.prefetchedUrls.has(cleanUrl)) return;

        this.prefetchedUrls.add(cleanUrl);

        // Speculative Link Prefetching
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = cleanUrl;
        link.as = 'document';
        document.head.appendChild(link);
    }

    attachLinkListeners() {
        // Hover & Touch Prefetching with 65ms debounce
        document.addEventListener('mouseover', (e) => {
            const anchor = e.target.closest('a');
            if (!anchor || !anchor.href) return;

            clearTimeout(this.hoverTimeout);
            this.hoverTimeout = setTimeout(() => {
                this.prefetchUrl(anchor.href);
            }, 65);
        }, { passive: true });

        // Touchstart Prefetching (Immediate touch on mobile)
        document.addEventListener('touchstart', (e) => {
            const anchor = e.target.closest('a');
            if (!anchor || !anchor.href) return;
            this.prefetchUrl(anchor.href);
        }, { passive: true });

        // Click Progress Bar Feedback
        document.addEventListener('click', (e) => {
            const anchor = e.target.closest('a');
            if (!anchor || !anchor.href) return;

            // Don't trigger for new tabs, modifier keys, or anchor jumps
            if (
                anchor.target === '_blank' ||
                anchor.hasAttribute('download') ||
                e.ctrlKey ||
                e.metaKey ||
                e.shiftKey ||
                e.altKey ||
                anchor.getAttribute('href')?.startsWith('#') ||
                anchor.getAttribute('href')?.startsWith('javascript:')
            ) {
                return;
            }

            if (this.isEligibleForPrefetch(anchor.href)) {
                this.startProgress();
            }
        });
    }

    attachFormListeners() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form || form.hasAttribute('data-no-loading')) return;

            this.startProgress();

            // Optimistic button loading state to prevent double submit and give instant feedback
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                setTimeout(() => {
                    submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                    submitBtn.classList.add('opacity-80', 'cursor-wait');
                    
                    // Add subtle loading spinner if button contains text
                    if (!submitBtn.querySelector('.spinner-loading-icon')) {
                        const originalText = submitBtn.innerText.trim();
                        submitBtn.innerHTML = `
                            <span class="inline-flex items-center gap-2">
                                <svg class="spinner-loading-icon w-4 h-4 animate-spin text-current shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span>${originalText ? 'Memproses...' : ''}</span>
                            </span>
                        `;
                    }
                }, 20);
            }
        });
    }

    handleBfCache() {
        // Back/Forward cache recovery & cleanup
        window.addEventListener('pageshow', (event) => {
            this.finishProgress();

            // If restored from bfcache, restore any disabled buttons
            if (event.persisted) {
                document.querySelectorAll('button[disabled], input[type="submit"][disabled]').forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-80', 'cursor-wait');
                    if (btn.dataset.originalHtml) {
                        btn.innerHTML = btn.dataset.originalHtml;
                    }
                });
            }
        });

        // Window load completion
        window.addEventListener('load', () => {
            this.finishProgress();
        });
    }
}

// Auto-initialize once DOM is ready
if (typeof window !== 'undefined') {
    window.instantNavigate = new InstantNavigateManager();
}

export default InstantNavigateManager;
