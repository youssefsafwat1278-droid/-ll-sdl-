import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global Alpine components
document.addEventListener('alpine:init', () => {
    
    // Toast notification component
    Alpine.data('toast', () => ({
        show: false,
        message: '',
        type: 'success',
        
        showToast(message, type = 'success') {
            this.message = message;
            this.type = type;
            this.show = true;
            
            setTimeout(() => {
                this.show = false;
            }, 3000);
        }
    }));

    // Countdown timer component
    Alpine.data('countdown', (deadline) => ({
        timeLeft: '',
        interval: null,
        
        init() {
            this.updateTime();
            this.interval = setInterval(() => this.updateTime(), 1000);
        },
        
        updateTime() {
            const now = new Date().getTime();
            const target = new Date(deadline).getTime();
            const distance = target - now;

            if (distance < 0) {
                this.timeLeft = 'انتهى الموعد';
                if (this.interval) clearInterval(this.interval);
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            if (days > 0) {
                this.timeLeft = `${days}د ${hours}س ${minutes}د ${seconds}ث`;
            } else {
                this.timeLeft = `${hours}س ${minutes}د ${seconds}ث`;
            }
        },
        
        destroy() {
            if (this.interval) clearInterval(this.interval);
        }
    }));

    // Dark mode toggle
    Alpine.data('darkMode', () => ({
        dark: localStorage.getItem('darkMode') === 'true',
        
        toggle() {
            this.dark = !this.dark;
            localStorage.setItem('darkMode', this.dark);
            document.documentElement.classList.toggle('dark', this.dark);
        },
        
        init() {
            document.documentElement.classList.toggle('dark', this.dark);
        }
    }));

    // Modal component
    Alpine.data('modal', () => ({
        isOpen: false,
        
        open() {
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },
        
        close() {
            this.isOpen = false;
            document.body.style.overflow = 'auto';
        }
    }));
});

Alpine.start();

// Global helper functions
window.formatNumber = (num) => {
    return new Intl.NumberFormat('ar-EG').format(num);
};

window.formatDate = (date) => {
    return new Intl.DateTimeFormat('ar-EG', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
};

// CSRF token setup for axios (configured in bootstrap.js)
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token && window.axios) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else if (!token) {
    console.error('CSRF token not found');
}

// Global UX helpers
document.addEventListener('DOMContentLoaded', () => {
    // Prevent double submit and provide instant feedback.
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            buttons.forEach((button) => {
                if (button.disabled) return;
                button.dataset.originalText = button.textContent || button.value || '';
                if (button.tagName.toLowerCase() === 'button') {
                    button.textContent = 'جارٍ الحفظ...';
                }
                button.disabled = true;
            });
        }, { once: true });
    });

    // Sidebar toggle (no Alpine dependency).
    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');
    if (sidebar && sidebarToggle && sidebarBackdrop) {
        const setSidebarOpen = (open) => {
            sidebar.classList.toggle('translate-x-full', !open);
            sidebar.classList.toggle('translate-x-0', open);
            sidebarBackdrop.classList.toggle('hidden', !open);
            document.body.style.overflow = open ? 'hidden' : '';
        };

        const isMobile = () => window.innerWidth < 1024;

        sidebarToggle.addEventListener('click', () => {
            setSidebarOpen(sidebar.classList.contains('translate-x-full'));
        });

        sidebarBackdrop.addEventListener('click', () => setSidebarOpen(false));

        sidebar.addEventListener('click', (event) => {
            if (isMobile() && event.target.closest('a')) {
                setSidebarOpen(false);
            }
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setSidebarOpen(false);
            }
        });
    }
    // Auto-dismiss flash messages if they opt-in.
    document.querySelectorAll('[data-auto-dismiss]').forEach((el) => {
        const delay = Number(el.dataset.autoDismiss) || 4000;
        setTimeout(() => {
            el.classList.add('opacity-0');
            setTimeout(() => el.remove(), 300);
        }, delay);
    });
});

