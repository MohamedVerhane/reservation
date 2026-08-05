import "./bootstrap";
import Alpine from "alpinejs";
import "./admin-filters";
import "./admin-forms";
import "./room-type-filter";
import "./ajax-actions";
import { createIcons } from "lucide";
import {
    ArrowRight, AtSign, Award, BadgeDollarSign, Bell, Bookmark, Briefcase,
    Building2, CalendarCheck, Camera, ChevronDown, ChevronRight, Clock,
    ConciergeBell, Crown, DoorOpen, Gem, Hash, House, Images, Info, Layers,
    LayoutGrid, LogIn, LogOut, Mail, MapPin, Menu, MessageCircle, Phone, Play,
    Search, ShieldCheck, Sparkles, SprayCan, Star, ThumbsUp, User, Users, X,
} from "lucide";

window.Alpine = Alpine;

// ── 3D tilt card (perspective + glare) ──
Alpine.data("tiltCard", (opts = {}) => ({
    max: opts.max ?? 12,
    glare: opts.glare ?? true,
    px: 0,
    py: 0,
    rx: 0,
    ry: 0,
    sx: 1,
    gx: 50,
    gy: 50,
    onMove(e) {
        const r = this.$el.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width; // 0..1
        const y = (e.clientY - r.top) / r.height; // 0..1
        this.px = x - 0.5;
        this.py = y - 0.5;
        this.ry = this.px * this.max * 2;
        this.rx = -this.py * this.max * 2;
        this.gx = x * 100;
        this.gy = y * 100;
        this.sx = 1.025;
    },
    onLeave() {
        this.rx = 0;
        this.ry = 0;
        this.px = 0;
        this.py = 0;
        this.sx = 1;
    },
    style() {
        return {
            transform: `perspective(1000px) rotateX(${this.rx.toFixed(2)}deg) rotateY(${this.ry.toFixed(2)}deg) scale3d(${this.sx}, ${this.sx}, ${this.sx})`,
            transition: this.sx > 1 ? "transform 80ms linear" : "transform 600ms cubic-bezier(.22,.61,.36,1)",
        };
    },
    glareStyle() {
        return {
            background: `radial-gradient(circle at ${this.gx}% ${this.gy}%, rgba(255,255,255,.35), transparent 55%)`,
            opacity: (this.glare ? this.sx - 1 : 0) * 4,
        };
    },
}));

// ── 3D mouse parallax for layered sections (hero) ──
document.addEventListener("alpine:init", () => {
    Alpine.data("heroParallax", () => ({
        x: 0,
        y: 0,
        onMove(e) {
            this.x = e.clientX / window.innerWidth - 0.5;
            this.y = e.clientY / window.innerHeight - 0.5;
        },
        onLeave() {
            this.x = 0;
            this.y = 0;
        },
        far() { return `translate3d(${this.x * -22}px, ${this.y * -16}px, 0)`; },
        mid() { return `translate3d(${this.x * 14}px, ${this.y * 10}px, 0)`; },
        near() { return `translate3d(${this.x * -12}px, ${this.y * 18}px, 0)`; },
    }));
});

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    // ── Lucide icons ──
    createIcons({
        icons: {
            ArrowRight, AtSign, Award, BadgeDollarSign, Bell, Bookmark, Briefcase,
            Building2, CalendarCheck, Camera, ChevronDown, ChevronRight, Clock,
            ConciergeBell, Crown, DoorOpen, Gem, Hash, House, Images, Info, Layers,
            LayoutGrid, LogIn, LogOut, Mail, MapPin, Menu, MessageCircle, Phone, Play,
            Search, ShieldCheck, Sparkles, SprayCan, Star, ThumbsUp, User, Users, X,
        },
    });

    // ── Dark mode toggle (initialization only — click handler uses event delegation in layout) ──
    function updateToggleIcons() {
        const isDark = document.documentElement.classList.contains("dark");
        document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
            const sun = btn.querySelector(".icon-sun");
            const moon = btn.querySelector(".icon-moon");
            if (sun && moon) {
                sun.style.display = isDark ? "none" : "inline";
                moon.style.display = isDark ? "inline" : "none";
            }
        });
    }

    // Initialize theme from localStorage or system preference (backup — layout head script runs first)
    if (!document.documentElement.classList.contains("dark")) {
        const stored = localStorage.getItem("theme");
        if (
            stored === "dark" ||
            (!stored &&
                window.matchMedia("(prefers-color-scheme: dark)").matches)
        ) {
            document.documentElement.classList.add("dark");
        }
    }
    updateToggleIcons();

    // ── Password visibility toggle ──
    document.querySelectorAll("[data-toggle-password]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const wrapper = btn.closest(".relative");
            const input = wrapper.querySelector("input");
            const icon = btn.querySelector("i");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        });
    });

    // ── Floating labels ──
    function initFloatingLabels() {
        document.querySelectorAll(".floating-label").forEach((group) => {
            const input = group.querySelector("input");
            if (!input) return;

            if (input.value) group.classList.add("filled");

            input.addEventListener("focus", () => {
                group.classList.add("focused");
                group.classList.remove("filled");
            });

            input.addEventListener("blur", () => {
                group.classList.remove("focused");
                input.value
                    ? group.classList.add("filled")
                    : group.classList.remove("filled");
            });
        });
    }
    initFloatingLabels();

    // ── Entrance animations with stagger ──
    document.querySelectorAll("[data-animate]").forEach((el, i) => {
        el.style.animationDelay = `${i * 0.08}s`;
    });

    // ── Mobile menu toggle ──
    document.querySelectorAll("[data-menu-toggle]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const menu = document.querySelector("[data-mobile-menu]");
            if (!menu) return;
            const isOpen = !menu.classList.contains("hidden");
            menu.classList.toggle("hidden");
            const openIcon = btn.querySelector(".menu-icon-open");
            const closeIcon = btn.querySelector(".menu-icon-close");
            if (openIcon && closeIcon) {
                openIcon.style.display = isOpen ? "inline" : "none";
                closeIcon.style.display = isOpen ? "none" : "inline";
            }
        });
    });

    // ── Admin sidebar (close on ESC only — toggle is handled by layout inline script) ──
    const sidebarToggle = document.querySelector("[data-sidebar-toggle]");
    const sidebarEl = document.querySelector("[data-sidebar]");
    const sidebarOverlay = document.querySelector("[data-sidebar-overlay]");
    document.addEventListener("keydown", (e) => {
        if (
            e.key === "Escape" &&
            sidebarEl &&
            !sidebarEl.classList.contains("-translate-x-full")
        ) {
            sidebarEl.classList.add("-translate-x-full");
            sidebarEl.classList.remove("translate-x-0");
            sidebarOverlay?.classList.add("hidden");
            sidebarOverlay?.classList.remove("block");
        }
    });

    // ── User dropdown ──
    const userDropdown = document.querySelector("[data-user-dropdown]");
    const userToggle = document.querySelector("[data-user-toggle]");
    const userMenu = document.querySelector("[data-user-menu]");

    if (userToggle && userMenu) {
        userToggle.addEventListener("click", (e) => {
            e.stopPropagation();
            userMenu.classList.toggle("show");
        });
        document.addEventListener("click", (e) => {
            if (!userDropdown?.contains(e.target)) {
                userMenu.classList.remove("show");
            }
        });
    }

    // ── Frontend scroll reveal animation ──
    const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -40px 0px" };
    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("revealed");
                scrollObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll(".animate-on-scroll").forEach((el) => {
        scrollObserver.observe(el);
    });

    // ── Counter animation for stats ──
    function animateCounters() {
        document.querySelectorAll("[data-count]").forEach((el) => {
            const target = parseInt(el.dataset.count, 10);
            const duration = 2000;
            const step = Math.ceil(target / (duration / 16));
            let current = 0;
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = current.toLocaleString();
            }, 16);
        });
    }

    const statsObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCounters();
                    statsObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.3 },
    );

    document.querySelectorAll("[data-count]").forEach((el) => {
        statsObserver.observe(el);
    });
});
