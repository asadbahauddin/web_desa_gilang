/* ==========================================================================
   SCRIPT.JS — Logic global: include komponen, navbar, dropdown, mobile menu
   ========================================================================== */

/**
 * Memuat partial HTML (navbar/footer) ke dalam elemen target.
 * Catatan: fetch() butuh halaman diakses lewat server (http://...),
 * bukan dibuka langsung dari file:// — jalankan lewat Live Server / 
 * `firebase serve` / `python -m http.server` saat development.
 */
async function includeComponent(targetSelector, url) {
  const target = document.querySelector(targetSelector);
  if (!target) return;

  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`Gagal memuat ${url} (status ${res.status})`);

    let html = await res.text();

    // Buang script auto-reload yang disisipkan otomatis oleh live-server.
    // Tanpa ini, script tersebut bisa merusak struktur HTML partial
    // (navbar.html / footer.html) karena nggak punya tag <body>.
    html = html.replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '');

    target.innerHTML = html;
  } catch (err) {
    console.error('[includeComponent]', err);
    target.innerHTML = `<p style="padding:16px;color:#b00;">Komponen gagal dimuat: ${url}</p>`;
  }
}

/** Inisialisasi efek navbar: solid saat discroll */
function initNavbarScroll() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const onScroll = () => {
    if (window.scrollY > 24) {
      navbar.classList.add('is-scrolled');
    } else {
      navbar.classList.remove('is-scrolled');
    }
  };

  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
}

/** Inisialisasi dropdown navbar (klik untuk desktop & mobile) */
function initNavbarDropdown() {
  const items = document.querySelectorAll('.navbar__item[data-dropdown]');

  items.forEach((item) => {

    // AMBIL BUTTON SAJA
    const trigger = item.querySelector('button.navbar__link');
    const dropdown = item.querySelector('.navbar__dropdown');

    if (!trigger || !dropdown) return;

    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      // tutup dropdown lain
      items.forEach(other => {
        if (other !== item) {
          other.classList.remove('is-open');

          const btn = other.querySelector('button.navbar__link');
          if (btn) btn.setAttribute('aria-expanded', 'false');
        }
      });

      item.classList.toggle('is-open');

      trigger.setAttribute(
        'aria-expanded',
        item.classList.contains('is-open')
      );
    });
  });

  // klik luar navbar
  document.addEventListener('click', () => {
    items.forEach(item => {
      item.classList.remove('is-open');

      const btn = item.querySelector('button.navbar__link');
      if (btn) btn.setAttribute('aria-expanded', 'false');
    });
  });
}

/** Inisialisasi toggle menu mobile (hamburger) */
function initMobileMenu() {
  const navbar = document.getElementById('navbar');
  const toggle = document.getElementById('navToggle');
  if (!navbar || !toggle) return;

  toggle.addEventListener('click', () => {
    const isOpen = navbar.classList.toggle('is-menu-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });
}

/** Set tahun berjalan di footer */
function initFooterYear() {
  const el = document.getElementById('footerYear');
  if (el) el.textContent = new Date().getFullYear();
}

/**
 * Entry point: include navbar & footer lebih dulu,
 * baru jalankan semua interaksi yang butuh elemen tersebut sudah ada di DOM.
 */
async function initLayout() {
  await Promise.all([
    includeComponent('#navbar-placeholder', '/components/navbar.html'),
    includeComponent('#footer-placeholder', '/components/footer.html'),
  ]);

  initNavbarScroll();
  initNavbarDropdown();
  initMobileMenu();
  initFooterYear();
}

document.addEventListener('DOMContentLoaded', initLayout);

