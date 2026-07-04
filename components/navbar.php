<?php
// Deteksi halaman aktif untuk highlight menu
$current = $_SERVER['REQUEST_URI'];
$base = "/web-desa-gilang/";

// Deteksi halaman aktif
$current = $_SERVER['REQUEST_URI'];

$nav_profil = [
  ['label' => 'Sejarah Desa', 'href' => $base . 'pages/profil/sejarah-desa.php'],
  ['label' => 'Visi &amp; Misi', 'href' => $base . 'pages/profil/visi-misi.php'],
  ['label' => 'Struktur Organisasi', 'href' => $base . 'pages/profil/struktur-organisasi.php'],
  ['label' => 'Aparatur Desa', 'href' => $base . 'pages/profil/aparatur-desa.php'],
];

$nav_publikasi = [
  ['label' => 'Berita', 'href' => $base . 'pages/publikasi/berita.php'],
  ['label' => 'Dokumen Publik', 'href' => $base . 'pages/publikasi/dokumen.php'],
];

// Cek apakah group dropdown aktif
$profil_aktif    = str_contains($current, '/profil/');
$publikasi_aktif = str_contains($current, '/publikasi/');
?>

<nav class="navbar" id="navbar">
  <div class="container">

    <!-- Brand / Logo -->
    <a href="<?= $base ?>index.php"  aria-label="Beranda Desa Gilang">
      <img src="<?= $base ?>assets/logo/logo-desa.jpg"
           alt="Logo Desa Gilang"
           class="navbar__logo"
           onerror="this.style.display='none'">
      <span class="navbar__brand-text">
        <span class="navbar__brand-title">Desa Gilang</span>
        <span class="navbar__brand-sub">Kec. Bluto · SUMENEP</span>
      </span>
    </a>

    <!-- Hamburger -->
    <button class="navbar__toggle"
            id="navToggle"
            aria-label="Buka menu navigasi"
            aria-expanded="false"
            aria-controls="navMenu">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <div class="navbar__menu" id="navMenu">

      <!-- Beranda -->
      <a href="<?= $base ?>index.php"
         class="navbar__link <?php echo $current === '/' || str_ends_with($current, 'index.php') ? 'active' : ''; ?>">
        Beranda
      </a>

      <!-- Profil Desa (dropdown) -->
      <div class="navbar__item" data-dropdown>
        <button type="button"
                class="navbar__link <?php echo $profil_aktif ? 'active' : ''; ?>"
                aria-expanded="<?php echo $profil_aktif ? 'true' : 'false'; ?>">
          Profil Desa
          <span class="navbar__chevron"></span>
        </button>

        <div class="navbar__dropdown">
          <?php foreach ($nav_profil as $item) : ?>
          <a href="<?php echo htmlspecialchars($item['href']); ?>"
             class="navbar__dropdown-link <?php echo str_contains($current, $item['href']) ? 'active' : ''; ?>">
            <?php echo $item['label']; ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Publikasi (dropdown) -->
      <div class="navbar__item" data-dropdown>
        <button type="button"
                class="navbar__link <?php echo $publikasi_aktif ? 'active' : ''; ?>"
                aria-expanded="<?php echo $publikasi_aktif ? 'true' : 'false'; ?>">
          Publikasi
          <span class="navbar__chevron"></span>
        </button>

        <div class="navbar__dropdown">
          <?php foreach ($nav_publikasi as $item) : ?>
          <a href="<?php echo htmlspecialchars($item['href']); ?>"
             class="navbar__dropdown-link <?php echo str_contains($current, $item['href']) ? 'active' : ''; ?>">
            <?php echo $item['label']; ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Kontak -->
      <a href="<?= $base ?>pages/kontak/kontak.php"
         class="navbar__link <?php echo str_contains($current, $base . 'pages/kontak/') ? 'active' : ''; ?>">
        Kontak
      </a>

      <a href="<?= $base ?>admin/login.php" class="btn btn--primary navbar__cta">
    Login Admin
</a>

    </div>
  </div>
</nav>