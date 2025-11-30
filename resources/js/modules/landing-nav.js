/**
 * Landing Page Navigation
 * Handles mobile menu toggle and active link highlighting
 */

export class LandingNav {
    constructor() {
        this.toggle = document.getElementById('menu-toggle');
        this.menu = document.getElementById('mobile-menu');
        this.navLinks = document.querySelectorAll('.nav-link');
        this.sections = document.querySelectorAll('section[id]');
        
        this.init();
    }

    init() {
        if (!this.toggle || !this.menu) return;

        // Mobile menu toggle
        this.toggle.addEventListener('click', () => {
            this.menu.classList.toggle('hidden');
        });

        // Close menu when clicking a link
        this.menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                this.menu.classList.add('hidden');
            });
        });

        // Active link highlighting on click
        this.navLinks.forEach(link => {
            link.addEventListener('click', () => {
                this.setActiveLink(link);
            });
        });

        // Active link highlighting on scroll
        this.setupScrollObserver();
    }

    setActiveLink(activeLink) {
        this.navLinks.forEach(link => {
            link.classList.remove('text-blue-600', 'font-bold');
        });
        activeLink.classList.add('text-blue-600', 'font-bold');
    }

    setupScrollObserver() {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.navLinks.forEach(link => {
                        link.classList.remove('text-blue-600', 'font-bold');
                    });
                    
                    const activeLink = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
                    if (activeLink) {
                        activeLink.classList.add('text-blue-600', 'font-bold');
                    }
                }
            });
        }, { threshold: 0.6 });

        this.sections.forEach(section => observer.observe(section));
    }
}
