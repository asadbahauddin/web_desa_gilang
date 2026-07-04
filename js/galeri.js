/* ==========================================================================
   GALERI.JS — Lightbox untuk halaman Galeri
   ========================================================================== */

function initGaleriLightbox() {
  const items = Array.from(document.querySelectorAll('.galeri-item-full'));
  const lightbox = document.getElementById('lightbox');
  if (!items.length || !lightbox) return;

  const img = lightbox.querySelector('.lightbox__figure img');
  const caption = lightbox.querySelector('.lightbox__caption');
  const btnClose = lightbox.querySelector('.lightbox__close');
  const btnPrev = lightbox.querySelector('.lightbox__nav--prev');
  const btnNext = lightbox.querySelector('.lightbox__nav--next');

  let currentIndex = 0;

  function renderSlide(index) {
    currentIndex = (index + items.length) % items.length;
    const item = items[currentIndex];
    const itemImg = item.querySelector('img');
    img.src = itemImg.src;
    img.alt = itemImg.alt;
    caption.textContent = itemImg.alt;
  }

  function openLightbox(index) {
    renderSlide(index);
    lightbox.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lightbox.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  items.forEach((item, index) => {
    item.addEventListener('click', () => openLightbox(index));
  });

  btnClose.addEventListener('click', closeLightbox);
  btnPrev.addEventListener('click', () => renderSlide(currentIndex - 1));
  btnNext.addEventListener('click', () => renderSlide(currentIndex + 1));

  // Klik di luar gambar (area gelap) menutup lightbox
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  // Navigasi via keyboard
  document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('is-open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') renderSlide(currentIndex - 1);
    if (e.key === 'ArrowRight') renderSlide(currentIndex + 1);
  });
}

/** Filter galeri berdasarkan kategori (chip) */
function initGaleriFilter() {
  const chips = document.querySelectorAll('.galeri-toolbar .filter-chip');
  const items = document.querySelectorAll('.galeri-item-full');
  if (!chips.length) return;

  chips.forEach((chip) => {
    chip.addEventListener('click', () => {
      chips.forEach((c) => c.classList.remove('is-active'));
      chip.classList.add('is-active');
      const category = chip.getAttribute('data-filter') || 'semua';

      items.forEach((item) => {
        const itemCategory = item.getAttribute('data-category') || '';
        const show = category === 'semua' || itemCategory === category;
        item.style.display = show ? '' : 'none';
      });
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initGaleriLightbox();
  initGaleriFilter();
});