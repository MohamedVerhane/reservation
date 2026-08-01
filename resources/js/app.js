import "./bootstrap";
import Alpine from "alpinejs";
import "./admin-filters";
import "./admin-forms";
import "./room-type-filter";

window.Alpine = Alpine;
Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
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

    // ── Button loading state on submit (AJAX forms handle their own state) ──
    document.querySelectorAll("form:not([data-ajax])").forEach((form) => {
        form.addEventListener("submit", () => {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.dataset.loading) {
                btn.dataset.loading = "true";
                btn.disabled = true;
                const text = btn.textContent;
                btn.innerHTML = `<span class="btn-spinner"></span>`;
                setTimeout(() => {
                    btn.innerHTML = text;
                    btn.disabled = false;
                    delete btn.dataset.loading;
                }, 5000);
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
