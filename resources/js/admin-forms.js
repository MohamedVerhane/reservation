/**
 * AJAX form submission for admin create pages.
 *
 * Any form with the `data-ajax` attribute submits via fetch():
 *  - Shows a loading spinner on the submit button.
 *  - On 201/2xx JSON response: shows a success toast, then redirects to
 *    the URL provided by the server (`data.redirect`).
 *  - On 422: renders the validation summary + per-field errors inline.
 *  - On 419 / network / server errors: shows a friendly error alert.
 */
document.addEventListener("DOMContentLoaded", () => {
    const SUCCESS_MSG = document.body.dataset.ajaxSuccess || "Saved successfully!";

    const loadingHtml = '<span class="inline-flex items-center gap-2"><i class="bi bi-arrow-repeat animate-spin"></i></span>';

    function escapeCss(value) {
        if (window.CSS && CSS.escape) return CSS.escape(value);
        return String(value).replace(/([^a-zA-Z0-9_-])/g, "\\$1");
    }

    function escapeHtml(value) {
        return String(value).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function getAlertBox(form) {
        let alert = form.querySelector(":scope > .ajax-alert");
        if (!alert) {
            alert = document.createElement("div");
            alert.className = "ajax-alert hidden";
            form.prepend(alert);
        }
        return alert;
    }

    function hideAlert(form) {
        const alert = getAlertBox(form);
        alert.classList.add("hidden");
        alert.classList.remove("ajax-alert-error", "ajax-alert-success");
        alert.innerHTML = "";
    }

    function showAlert(form, type, title, messages) {
        const alert = getAlertBox(form);
        alert.classList.remove("hidden");
        alert.classList.remove("ajax-alert-error", "ajax-alert-success");
        alert.classList.add(type === "success" ? "ajax-alert-success" : "ajax-alert-error");

        const icon = type === "success" ? "bi-check-circle-fill" : "bi-exclamation-triangle-fill";
        const iconColor = type === "success" ? "text-emerald-600 dark:text-emerald-400" : "text-red-600 dark:text-red-400";
        const titleColor = type === "success" ? "text-emerald-700 dark:text-emerald-400" : "text-red-700 dark:text-red-400";
        const itemColor = type === "success" ? "text-emerald-600 dark:text-emerald-400" : "text-red-600 dark:text-red-400";

        let list = "";
        if (messages && messages.length) {
            list = `<ul class="mt-1 list-disc list-inside text-xs ${itemColor} space-y-0.5">` +
                messages.map((m) => `<li>${escapeHtml(m)}</li>`).join("") +
                "</ul>";
        }

        alert.innerHTML = `
            <div class="flex items-start gap-2">
                <i class="bi ${icon} ${iconColor} mt-0.5"></i>
                <div class="min-w-0">
                    <p class="text-sm font-semibold ${titleColor}">${escapeHtml(title)}</p>
                    ${list}
                </div>
                <button type="button" class="ajax-alert-close ms-auto text-[var(--text-muted)] hover:text-[var(--text-primary)]" aria-label="Close">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>`;

        const closeBtn = alert.querySelector(".ajax-alert-close");
        if (closeBtn) {
            closeBtn.addEventListener("click", () => hideAlert(form));
        }

        alert.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function clearFieldErrors(form) {
        form.querySelectorAll(".ajax-field-error").forEach((el) => el.remove());
        form.querySelectorAll("[aria-invalid='true']").forEach((el) => {
            el.removeAttribute("aria-invalid");
            el.classList.remove("border-red-400", "dark:border-red-600", "focus:border-red-400", "focus:ring-red-400/20");
        });
    }

    function renderFieldErrors(form, errors) {
        Object.entries(errors || {}).forEach(([field, messages]) => {
            let input = form.querySelector(`[name="${escapeCss(field)}"]`)
                || form.querySelector(`[name="${escapeCss(field)}[]"]`);

            if (!input) return;

            input.setAttribute("aria-invalid", "true");
            input.classList.add("border-red-400", "dark:border-red-600");

            const list = Array.isArray(messages) ? messages : [messages];
            const err = document.createElement("p");
            err.className = "ajax-field-error mt-1 text-xs text-red-500 dark:text-red-400";
            err.textContent = list[0];
            input.insertAdjacentElement("afterend", err);
        });
    }

    function showToast(message) {
        const container = document.querySelector(".ajax-toast-container");
        if (!container) return;

        const toast = document.createElement("div");
        toast.className = "ajax-toast";
        toast.innerHTML = `
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-500">
                <i class="bi bi-check-lg text-base"></i>
            </span>
            <p class="text-sm font-semibold text-[var(--text-primary)]">${message}</p>`;
        container.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add("show"));

        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    function setButtonLoading(btn, loading) {
        if (!btn) return;
        if (loading) {
            btn.dataset.originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = loadingHtml;
        } else {
            if (btn.dataset.originalHtml) {
                btn.innerHTML = btn.dataset.originalHtml;
                delete btn.dataset.originalHtml;
            }
            btn.disabled = false;
        }
    }

    document.querySelectorAll("form[data-ajax]").forEach((form) => {
        form.addEventListener("submit", (e) => {
            e.preventDefault();

            if (form.dataset.sending === "1") return;
            form.dataset.sending = "1";

            const btn = form.querySelector('button[type="submit"]');
            hideAlert(form);
            clearFieldErrors(form);
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

                    if (data && data.redirect) {
                        showToast(data.success || form.dataset.success || SUCCESS_MSG);
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 500);
                    } else {
                        showAlert(form, "success", data?.success || form.dataset.success || SUCCESS_MSG, []);
                    }
                })
                .catch((err) => {
                    setButtonLoading(btn, false);
                    delete form.dataset.sending;

                    if (err.status === 422 && err.data) {
                        const messages = Object.values(err.data.errors || {}).flat();
                        showAlert(form, "error", err.data.message || "Please fix the errors below.", messages);
                        renderFieldErrors(form, err.data.errors || {});
                    } else if (err.status === 419) {
                        showAlert(form, "error", "Your session has expired. Please refresh and try again.", []);
                    } else {
                        showAlert(form, "error", "Something went wrong. Please try again.", []);
                    }
                });
        });
    });
});
