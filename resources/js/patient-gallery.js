function setupPatientGallery() {
    const galleryContainer = document.querySelector('[data-patient-gallery]');
    if (!galleryContainer) return;

    const imageLinks = Array.from(galleryContainer.querySelectorAll('img[data-gallery-image]'));
    if (!imageLinks.length) return;

    const modalEl = document.getElementById('imagePreviewModal');
    if (!modalEl) return;

    const modalImg = modalEl.querySelector('#imagePreviewModalImg');
    const modalTitle = modalEl.querySelector('#imagePreviewModalLabel');

    imageLinks.forEach((image) => {
        image.addEventListener('click', () => {
            modalImg.src = image.src;
            modalImg.alt = image.alt || '';
            modalTitle.textContent = image.dataset.galleryTitle || image.alt || '';
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    });
}

if (typeof window !== 'undefined') {
    document.addEventListener('DOMContentLoaded', setupPatientGallery);
}

export {};
