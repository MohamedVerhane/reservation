/**
 * AJAX quick actions (no full-page reload).
 *
 * Any form with the `data-ajax-action` attribute is submitted via fetch():
 *  - On success (JSON from `orJson()`): shows a toast, then either
 *      a) navigates to the server-provided `data.redirect` when it points to a
 *         different page, or
 *      b) re-renders the current page region in place:
 *           - the active filter container (`[data-ajax-filter]`) on index pages, or
 *           - the page content region (`[data-ajax-page]`) on detail pages.
 *  - Notification actions additionally refresh the notification dropdowns and
 *    unread-count badges via `refreshNotificationWidgets()`.
 *  - Pagination links on the current page (`?page=N`) are fetched via AJAX and
 *    swap the `[data-ajax-page]` region, so no full browser reload happens.
 *  - On 422/419/network/server errors: shows a friendly error toast.
 * No loading spinner or dimming is shown during any of these operations.
 */
document.addEventListener("DOMContentLoaded", () => {
    const DEFAULT_SUCCESS = "Done!";

    function showToast(message, type = "success") {
        const container = document.querySelector(".ajax-toast-container");
        if (!container) return;

        const toast = document.createElement("div");
        toast.className = `ajax-toast ${type === "error" ? "ajax-toast-error" : ""}`;
        const icon = type === "error" ? "bi-x-lg" : "bi-check-lg";
        const iconColor = type === "error" ? "text-red-500" : "text-emerald-500";
        toast.innerHTML = `
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full ${iconColor}/15 text-[var(--text-primary)]">
                <i class="bi ${icon} text-base"></i>
            </span>
            <p class="text-sm font-semibold text-[var(--text-primary)]">${message}</p>`;
        container.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add("show"));

        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function setButtonLoading(btn, loading) {
        if (!btn) return;
        btn.disabled = loading;
    }

    function fetchDocument(url) {
        return fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
            .then((res) => res.text())
            .then((html) => new DOMParser().parseFromString(html, "text/html"));
    }

    function findRefreshableRegion() {
        const filterForm = document.querySelector("[data-ajax-filter]");
        if (filterForm) {
            const id = filterForm.getAttribute("data-ajax-filter");
            const el = document.getElementById(id);
            if (el) return { el, selector: `#${CSS.escape(id)}` };
        }

        const page = document.querySelector("[data-ajax-page]");
        if (page) return { el: page, selector: "[data-ajax-page]" };

        return null;
    }

    function refreshRegion() {
        const region = findRefreshableRegion();
        if (!region) return Promise.resolve(false);

        const { el, selector } = region;

        return fetchDocument(window.location.href)
            .then((doc) => {
                const fresh = doc.querySelector(selector);
                if (!fresh) return false;
                el.innerHTML = fresh.innerHTML;
                if (window.Alpine && typeof Alpine.initTree === "function") {
                    Alpine.initTree(el);
                }
                return true;
            })
            .catch(() => false);
    }

    function refreshNotificationWidgets() {
        const hasDropdown = document.querySelector("[data-notifications-dropdown]");
        const badges = document.querySelectorAll("[data-notif-badge]");
        if (!hasDropdown && badges.length === 0) return;

        fetchDocument(window.location.href)
            .then((doc) => {
                if (hasDropdown) {
                    const fresh = doc.querySelector("[data-notifications-dropdown]");
                    if (fresh) {
                        hasDropdown.innerHTML = fresh.innerHTML;
                        if (window.Alpine && typeof Alpine.initTree === "function") {
                            Alpine.initTree(hasDropdown);
                        }
                    }
                }
                badges.forEach((badge, i) => {
                    const fresh = doc.querySelectorAll("[data-notif-badge]")[i];
                    if (fresh) badge.outerHTML = fresh.outerHTML;
                });
            })
            .catch(() => {});
    }

    function swapPageContent(doc, url) {
        const target = document.querySelector("[data-ajax-page]");
        const fresh = doc.querySelector("[data-ajax-page]");
        if (!target || !fresh) return false;

        target.innerHTML = fresh.innerHTML;
        document.title = doc.title || document.title;
        if (window.Alpine && typeof Alpine.initTree === "function") {
            Alpine.initTree(target);
        }
        if (window.initAdminFilters) window.initAdminFilters();
        if (url) window.history.pushState({}, "", url);
        return true;
    }

    window.navigateAjax = function (url) {
        return fetchDocument(url)
            .then((doc) => {
                if (!swapPageContent(doc, url)) window.location.href = url;
            })
            .catch(() => {
                window.location.href = url;
            });
    };

    document.addEventListener("submit", (e) => {
        const form = e.target && e.target.closest ? e.target.closest("form[data-ajax-action]") : null;
        if (!form) return;

        if (e.defaultPrevented) return;

        e.preventDefault();
        if (form.dataset.sending === "1") return;
        form.dataset.sending = "1";

        const btn = form.querySelector('button[type="submit"]');
        setButtonLoading(btn, true);

        const token = form.querySelector('input[name="_token"]')?.value
            || document.querySelector('meta[name="csrf-token"]')?.content
            || "";

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json",
            },
            body: new FormData(form),
        })
            .then(async (res) => {
                const contentType = res.headers.get("content-type") || "";
                let data = null;
                if (contentType.includes("application/json")) {
                    try {
                        data = await res.json();
                    } catch (_) {
                        data = null;
                    }
                }

                if (!res.ok) {
                    const err = new Error(res.statusText);
                    err.status = res.status;
                    err.data = data;
                    throw err;
                }
                return data;
            })
            .then((data) => {
                setButtonLoading(btn, false);
                delete form.dataset.sending;

                if (data && data.error) {
                    showToast(data.error, "error");
                } else {
                    showToast(data?.success || form.dataset.success || DEFAULT_SUCCESS);
                }

                const isNotificationAction = form.action.includes("/notifications/");
                const redirect = data && data.redirect;
                const samePage = redirect && new URL(redirect, window.location.origin).pathname === window.location.pathname;

                const settle = () => {
                    if (redirect && !samePage) {
                        window.navigateAjax(redirect);
                        return;
                    }
                    if (!form.hasAttribute("data-no-refresh")) {
                        refreshRegion();
                    }
                    if (isNotificationAction) refreshNotificationWidgets();
                };

                setTimeout(settle, 350);
            })
            .catch((err) => {
                setButtonLoading(btn, false);
                delete form.dataset.sending;

                if (err.status === 419) {
                    showToast("Your session has expired. Please refresh and try again.", "error");
                } else if (err.status === 403) {
                    showToast("You are not allowed to perform this action.", "error");
                } else if (err.status === 422 && err.data) {
                    const messages = Object.values(err.data.errors || {}).flat();
                    showToast(err.data.message || messages[0] || "Please check your input.", "error");
                } else {
                    showToast("Something went wrong. Please try again.", "error");
                }
            });
    });

    document.addEventListener("click", (e) => {
        if (e.defaultPrevented) return;
        if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const link = e.target && e.target.closest ? e.target.closest("a[href]") : null;
        if (!link) return;
        if (link.hasAttribute("data-no-ajax")) return;
        if (link.closest("[data-ajax-filter]")) return;

        const url = new URL(link.href, window.location.origin);
        if (url.origin !== window.location.origin) return;
        if (url.pathname !== window.location.pathname) return;
        if (!/(^|[?&])page=\d+/.test(url.search)) return;

        const page = document.querySelector("[data-ajax-page]");
        if (!page) return;

        e.preventDefault();
        fetchDocument(url.href).then((doc) => {
            swapPageContent(doc, url.href);
        });
    });
});
