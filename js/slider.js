/* ==========================================================================
   SLIDER.JS — Utility carousel/slider generik
   ----------------------------------------------------------------------
   Catatan: Hero di index.html memakai VIDEO background (bukan slider foto),
   jadi file ini belum dipanggil di halaman manapun saat ini. Disiapkan
   sebagai utility siap pakai jika nanti dibutuhkan carousel — misalnya
   slider testimoni warga, atau carousel berita unggulan.

   Cara pakai:
   <div class="carousel" data-autoplay="4000">
     <div class="carousel__track">
       <div class="carousel__slide">...</div>
       <div class="carousel__slide">...</div>
     </div>
     <button class="carousel__prev">‹</button>
     <button class="carousel__next">›</button>
   </div>
   initCarousel(document.querySelector('.carousel'));
   ========================================================================== */

function initCarousel(root) {
  if (!root) return;

  const track = root.querySelector('.carousel__track');
  const slides = Array.from(root.querySelectorAll('.carousel__slide'));
  const btnPrev = root.querySelector('.carousel__prev');
  const btnNext = root.querySelector('.carousel__next');
  if (!track || !slides.length) return;

  let index = 0;
  const autoplayDelay = parseInt(root.getAttribute('data-autoplay'), 10) || 0;
  let timer = null;

  function goTo(newIndex) {
    index = (newIndex + slides.length) % slides.length;
    track.style.transform = `translateX(-${index * 100}%)`;
  }

  function next() { goTo(index + 1); }
  function prev() { goTo(index - 1); }

  function startAutoplay() {
    if (!autoplayDelay) return;
    stopAutoplay();
    timer = setInterval(next, autoplayDelay);
  }

  function stopAutoplay() {
    if (timer) clearInterval(timer);
  }

  btnNext?.addEventListener('click', () => { next(); startAutoplay(); });
  btnPrev?.addEventListener('click', () => { prev(); startAutoplay(); });

  root.addEventListener('mouseenter', stopAutoplay);
  root.addEventListener('mouseleave', startAutoplay);

  track.style.display = 'flex';
  track.style.transition = 'transform 0.4s ease';
  slides.forEach((slide) => { slide.style.flex = '0 0 100%'; });

  goTo(0);
  startAutoplay();

  return { next, prev, goTo, stopAutoplay };
}

/** Auto-init semua elemen .carousel yang ada di halaman saat DOM siap */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.carousel').forEach(initCarousel);
});