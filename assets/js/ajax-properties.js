// ajax-properties.js
document.addEventListener('DOMContentLoaded', () => {
    const results = document.querySelector('.properties--list');
    const form = document.querySelector('.property-filter-form');

    if (!results) return;

    // Current page stored in JS
    let currentPage = parseInt((new URL(window.location)).searchParams.get('paged')) || 1;

    // Build FormData with optional extras
    const buildFormData = (extra = {}) => {
        const fd = form ? new FormData(form) : new FormData();
        Object.keys(extra).forEach(k => fd.append(k, extra[k]));
        if (!fd.get('action')) fd.append('action', 'filter_properties');
        return fd;
    };

    // Fetch + render
    const fetchProperties = async (paged = 1, pushState = true) => {
        try {
            results.classList.add('is-loading');
            const fd = buildFormData({ paged });

            const res = await fetch(ajax_object.ajaxurl, {
                method: 'POST',
                body: fd
            });

            if (!res.ok) throw new Error('Network response was not ok');

            const html = await res.text();
            results.innerHTML = html;

            // update currentPage from the requested page
            currentPage = parseInt(paged) || 1;

            // reattach handlers
            bindPaginationLinks();

            // update URL
            if (pushState) {
                const url = new URL(window.location);
                if (currentPage > 1) url.searchParams.set('paged', currentPage);
                else url.searchParams.delete('paged');
                window.history.pushState({ paged: currentPage }, '', url);
            }

            // smooth scroll
            window.scrollTo({ top: 0, behavior: 'smooth' });

        } catch (err) {
            console.error('fetchProperties error:', err);
        } finally {
            results.classList.remove('is-loading');
        }
    };

    // Parse page number from link href or from rel attribute
    const parsePageFromLink = (link) => {
        let page = null;
        // 1) Try ?paged=N
        try {
            const url = new URL(link.href, window.location.origin);
            const p = url.searchParams.get('paged');
            if (p) return parseInt(p);
            // 2) Try /page/N/ in pathname
            const match = url.pathname.match(/page\/(\d+)\/?/);
            if (match) return parseInt(match[1]);
        } catch (e) {
            // ignore
        }

        // 3) If it's prev/next and we have currentPage, compute
        const rel = (link.getAttribute('rel') || '').toLowerCase();
        if (rel === 'prev' && currentPage > 1) return currentPage - 1;
        if (rel === 'next') return currentPage + 1;

        // 4) fallback to data-paged attr if provided
        const dp = link.dataset.paged;
        if (dp) return parseInt(dp);

        return 1;
    };

    // Attach listeners to pagination links inside results
    const bindPaginationLinks = () => {
        // selector covers common WP pagination outputs
        const links = results.querySelectorAll('.pagination a, .nav-links a, .page-numbers a');

        links.forEach(link => {
            // remove previous listeners defensivamente by cloning
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
        });

        // re-select after clone
        const freshLinks = results.querySelectorAll('.pagination a, .nav-links a, .page-numbers a');

        freshLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parsePageFromLink(link) || 1;
                fetchProperties(page, true);
            });
        });
    };

    // Form submit triggers fetch (go to page 1)
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            fetchProperties(1, true);
        });
        // Auto submit on change for filters
        form.addEventListener('change', () => fetchProperties(1, true));
    }

    // Handle browser back/forward
    window.addEventListener('popstate', (e) => {
        const paged = (e.state && e.state.paged) ? parseInt(e.state.paged) : (new URL(window.location)).searchParams.get('paged');
        const page = parseInt(paged) || 1;
        fetchProperties(page, false);
    });

    // Initial load: bind listeners if server-side items exist and no filters are present, else fetch.
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = Array.from(urlParams.keys()).some(key => key !== 'paged');

    if (results.children.length > 0 && !hasFilters) {
        bindPaginationLinks();
    } else {
        fetchProperties(currentPage, false);
    }
});

function togglePropertiesSidebar() {
    const form = document.querySelector('.property-filter-form')

    if (form) {
        form.classList.toggle('large')
    }
}