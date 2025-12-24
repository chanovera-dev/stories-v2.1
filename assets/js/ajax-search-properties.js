document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.property-filter-form');
    const results = document.querySelector('.properties--list');

    if (!form || !results) return;

    const params = new URLSearchParams(window.location.search);
    const searchValue = params.get('search');

    if (searchValue) {
        const input = form.querySelector('input[name="search"]');
        if (input) input.value = searchValue;
    }
});