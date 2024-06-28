// Función de búsqueda de certificado en el frontend
document.addEventListener('DOMContentLoaded', function () {
    const searchBtn = document.getElementById('search-folio-btn');
    searchBtn.addEventListener('click', function () {
        const folio = document.getElementById('search-folio').value;
        if (folio) {
            window.location.href = '/?certificado=' + folio;
        }
    });
});