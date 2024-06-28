document.addEventListener('DOMContentLoaded', function () {
    const modal = document.createElement('div');
    modal.classList.add('modal');
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="modal-img" src="" alt="QR Code" style="width: 100%;">
        </div>
    `;
    document.body.appendChild(modal);
    const modalImg = document.getElementById('modal-img');
    const closeModal = document.querySelector('.modal .close');
    document.querySelectorAll('.open-modal').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            modalImg.src = this.dataset.url;
            modal.style.display = 'block';
        });
    });
    closeModal.addEventListener('click', function () {
        modal.style.display = 'none';
    });
    window.addEventListener('click', function (e) {
        if (e.target == modal) {
            modal.style.display = 'none';
        }
    });
});
