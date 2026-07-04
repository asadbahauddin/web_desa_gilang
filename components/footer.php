<?php
// Data footer — bisa diambil dari DB / pengaturan
$footer_links = [
  ['label' => 'Sejarah Desa',   'href' => '/pages/profil/sejarah-desa.php'],
  ['label' => 'Berita Desa',    'href' => '/pages/publikasi/berita.php'],
  ['label' => 'Dokumen Publik', 'href' => '/pages/publikasi/dokumen.php'],
  ['label' => 'Galeri',         'href' => '/pages/galeri/galeri.php'],
];

$footer_sosial = [
  [
    'label' => 'Instagram Desa Gilang',
    'href'  => '#',
    'icon'  => '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><circle cx="17" cy="7" r="1" fill="currentColor"/></svg>',
  ],
  [
    'label' => 'Facebook Desa Gilang',
    'href'  => '#',
    'icon'  => '<svg viewBox="0 0 24 24" fill="none"><path d="M14 9h3V6h-3c-2 0-3.5 1.5-3.5 3.5V11H8v3h2.5v6h3v-6H16l.5-3h-3V9.8c0-.6.3-.8.8-.8z" fill="currentColor"/></svg>',
  ],
  [
    'label' => 'Youtube Desa Gilang',
    'href'  => '#',
    'icon'  => '<svg viewBox="0 0 24 24" fill="none"><rect x="2" y="6" width="20" height="12" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M11 9.5l4 2.5-4 2.5z" fill="currentColor"/></svg>',
  ],
];
?>

<footer class="footer" id="footer">
  <div class="footer__wave">
    <svg viewBox="0 0 1200 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,40 C150,80 350,0 600,30 C850,60 1050,10 1200,35 L1200,0 L0,0 Z" fill="var(--color-cream)"/>
    </svg>
  </div>

  <div class="container footer__top">

    <!-- Brand -->
    <div class="footer__brand">
      <img src="/assets/logo/logo-desa.png" alt="Logo Desa Gilang" class="footer__logo"
           onerror="this.style.display='none'">
      <div>
        <p class="footer__brand-title">Pemerintah Desa Gilang</p>
        <p class="footer__desc">
          Situs resmi Desa Gilang — pusat informasi pelayanan publik, berita, dan dokumen desa untuk warga dan masyarakat umum.
        </p>
      </div>
    </div>

    <!-- Tautan Cepat -->
    <div>
      <p class="footer__heading">Tautan Cepat</p>
      <ul class="footer__links">
        <?php foreach ($footer_links as $link) : ?>
        <li>
          <a href="<?php echo htmlspecialchars($link['href']); ?>">
            <?php echo htmlspecialchars($link['label']); ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Kontak -->
    <div>
      <p class="footer__heading">Kontak</p>
      <ul class="footer__contact">
        <li>
          <svg viewBox="0 0 24 24" fill="none"><path d="M12 22s7-7.4 7-12.6A7 7 0 105 9.4C5 14.6 12 22 12 22z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="1.6"/></svg>
          Jl. Raya Gilang No. 1, Kec. Contoh, Kab. Contoh, Jawa Timur
        </li>
        <li>
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 5.5C3 4 4 3 5.5 3H8l2 5-2.5 1.5a13 13 0 006 6L15 13l5 2v2.5c0 1.5-1 2.5-2.5 2.5C9.5 20 3 13.5 3 5.5z" stroke="currentColor" stroke-width="1.6"/></svg>
          (031) 123-4567
        </li>
        <li>
          <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="1.6"/></svg>
          desagilang@contoh.go.id
        </li>
      </ul>
    </div>

    <!-- Jam Pelayanan & Sosial -->
    <div>
      <p class="footer__heading">Jam Pelayanan</p>
      <ul class="footer__links">
        <li>Senin – Jumat: 08.00 – 15.00</li>
        <li>Sabtu, Minggu &amp; libur: Tutup</li>
      </ul>

      <div class="footer__social">
        <?php foreach ($footer_sosial as $sosial) : ?>
        <a href="<?php echo htmlspecialchars($sosial['href']); ?>"
           aria-label="<?php echo htmlspecialchars($sosial['label']); ?>">
          <?php echo $sosial['icon']; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

  <div class="container footer__bottom">
    <span>&copy; <?php echo date('Y'); ?> Pemerintah Desa Gilang. Hak cipta dilindungi.</span>
    <a href="/admin/login.php">Login Admin</a>
  </div>
</footer>