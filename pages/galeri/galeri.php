<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Galeri Desa — Desa Gilang</title>
  <meta name="description" content="Dokumentasi foto kegiatan, alam, dan infrastruktur Desa Gilang.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.png">

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/navbar.css">
  <link rel="stylesheet" href="/css/footer.css">
  <link rel="stylesheet" href="/css/galeri.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/partials/navbar.php'; ?>

  <main id="main-content">

    <header class="page-header">
      <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="/index.php">Beranda</a>
          <span>/</span>
          <span aria-current="page">Galeri</span>
        </nav>
        <h1 class="page-header__title">Galeri Desa</h1>
        <p class="page-header__desc">Dokumentasi visual kegiatan, keindahan alam, dan pembangunan infrastruktur Desa Gilang.</p>
      </div>
    </header>

    <section class="section">
      <div class="container">

        <div class="galeri-toolbar">
          <button class="filter-chip is-active" data-filter="semua">Semua</button>
          <button class="filter-chip" data-filter="kegiatan">Kegiatan</button>
          <button class="filter-chip" data-filter="alam">Alam</button>
          <button class="filter-chip" data-filter="infrastruktur">Infrastruktur</button>
        </div>

        <?php
        // Data galeri — bisa dipindahkan ke database / file terpisah jika diperlukan
        $galeriItems = [
          ['kategori' => 'alam',          'seed' => 'galeri-full-1',  'lebar' => 700, 'tinggi' => 900, 'caption' => 'Pemandangan sawah Desa Gilang saat pagi hari'],
          ['kategori' => 'kegiatan',      'seed' => 'galeri-full-2',  'lebar' => 500, 'tinggi' => 500, 'caption' => 'Kerja bakti membersihkan saluran irigasi'],
          ['kategori' => 'infrastruktur', 'seed' => 'galeri-full-3',  'lebar' => 500, 'tinggi' => 500, 'caption' => 'Pembangunan jalan desa'],
          ['kategori' => 'kegiatan',      'seed' => 'galeri-full-4',  'lebar' => 500, 'tinggi' => 500, 'caption' => 'Perayaan HUT Kemerdekaan di balai desa'],
          ['kategori' => 'alam',          'seed' => 'galeri-full-5',  'lebar' => 700, 'tinggi' => 900, 'caption' => 'Sungai yang mengalir di sisi utara desa'],
          ['kategori' => 'kegiatan',      'seed' => 'galeri-full-6',  'lebar' => 500, 'tinggi' => 500, 'caption' => 'Pelatihan UMKM bagi warga'],
          ['kategori' => 'infrastruktur', 'seed' => 'galeri-full-7',  'lebar' => 500, 'tinggi' => 500, 'caption' => 'Balai pertemuan Desa Gilang'],
          ['kategori' => 'alam',          'seed' => 'galeri-full-8',  'lebar' => 500, 'tinggi' => 500, 'caption' => 'Pepohonan rindang di jalan masuk desa'],
          ['kategori' => 'kegiatan',      'seed' => 'galeri-full-9',  'lebar' => 500, 'tinggi' => 500, 'caption' => 'Posyandu pemeriksaan kesehatan warga'],
          ['kategori' => 'infrastruktur', 'seed' => 'galeri-full-10', 'lebar' => 500, 'tinggi' => 500, 'caption' => 'Penerangan jalan umum yang baru terpasang'],
        ];
        ?>

        <div class="galeri-grid-full">
          <?php foreach ($galeriItems as $item): ?>
          <div class="galeri-item-full" data-category="<?php echo htmlspecialchars($item['kategori']); ?>">
            <img src="https://picsum.photos/seed/<?php echo htmlspecialchars($item['seed']); ?>/<?php echo (int)$item['lebar']; ?>/<?php echo (int)$item['tinggi']; ?>" alt="<?php echo htmlspecialchars($item['caption']); ?>">
            <span class="galeri-item-full__zoom"><svg viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.5"/><path d="M14 14l-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span>
            <span class="galeri-item-full__caption"><?php echo htmlspecialchars($item['caption']); ?></span>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>

  </main>

  <!-- Lightbox modal -->
  <div class="lightbox" id="lightbox">
    <button class="lightbox__close" aria-label="Tutup">
      <svg viewBox="0 0 16 16" fill="none"><path d="M2 2l12 12M14 2L2 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
    </button>
    <button class="lightbox__nav lightbox__nav--prev" aria-label="Foto sebelumnya">
      <svg viewBox="0 0 16 16" fill="none"><path d="M10 2L4 8l6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <button class="lightbox__nav lightbox__nav--next" aria-label="Foto berikutnya">
      <svg viewBox="0 0 16 16" fill="none"><path d="M6 2l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <figure class="lightbox__figure">
      <img src="" alt="">
      <figcaption class="lightbox__caption"></figcaption>
    </figure>
  </div>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/partials/footer.php'; ?>
  <script src="/js/script.js"></script>
  <script src="/js/galeri.js"></script>
</body>
</html>