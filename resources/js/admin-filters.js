function initAdminFilters() {
    const form = document.querySelector('[data-ajax-filter]');
    if (!form || form.__adminFiltersBound) return;
    form.__adminFiltersBound = true;

    const containerId = form.getAttribute('data-ajax-filter');
    const container = document.getElementById(containerId);
    if (!container) return;

    let debounceTimer;

    function fetchResults(url) {
        if (typeof url !== 'string') {
            const params = new URLSearchParams(new FormData(form));
            url = form.action + '?' + params.toString();
        }

        history.replaceState({}, '', url);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.text(); })
            .then(function (html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newContent = doc.getElementById(containerId);
                if (newContent) {
                    container.innerHTML = newContent.innerHTML;
                    if (window.Alpine && typeof Alpine.initTree === 'function') {
                        Alpine.initTree(container);
                    }
                }
                flashHandler();
            })
            .catch(function () {});
    }

    function flashHandler() {
        container.querySelectorAll('[data-auto-hide]').forEach(function (el) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 500);
            }, parseInt(el.getAttribute('data-auto-hide'), 10) || 4000);
        });
    }

    var inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(function (input) {
        var tag = input.tagName.toLowerCase();

        if (input.type === 'text' || input.type === 'date' || input.type === 'number' || tag === 'textarea') {
            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchResults, 400);
            });
        } else if (input.type === 'checkbox' || input.type === 'radio') {
            input.addEventListener('change', fetchResults);
        } else if (tag === 'select') {
            input.addEventListener('change', fetchResults);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchResults();
    });

    container.addEventListener('click', function (e) {
        var link = e.target.closest('a');
        if (!link) return;

        var wrapper = link.closest('[data-ajax-pagination]') || link.closest('.pagination') || link.closest('nav');
        if (!wrapper) return;

        var href = link.getAttribute('href');
        if (!href || href === '#') return;

        e.preventDefault();
        fetchResults(href);
    });

    container.addEventListener('submit', function (e) {
        var target = e.target;
        if (target.matches('form') && !target.hasAttribute('data-no-ajax')) {
            var method = (target.getAttribute('method') || 'GET').toUpperCase();
            var formData = new FormData(target);

            if (method === 'GET') {
                var params = new URLSearchParams(formData);
                e.preventDefault();
                fetchResults(target.action + '?' + params.toString());
            }
        }
    });

    flashHandler();
}

window.initAdminFilters = initAdminFilters;
document.addEventListener('DOMContentLoaded', initAdminFilters);
