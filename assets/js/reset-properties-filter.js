document.addEventListener('DOMContentLoaded', function () {
    const resetButton = document.getElementById('reset-filters');
    const form = document.getElementById('property-filters');

    resetButton.addEventListener('click', function () {
        // Limpia todos los campos del formulario
        form.reset();

        // Elimina parámetros de la URL (recarga limpia)
        const baseUrl = window.location.pathname;
        window.location.href = baseUrl;
    });
});