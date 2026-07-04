<?php
// ============================================================
//  Koneksi DB — sesuaikan dengan config proyekmu
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Data sejarah — ganti dengan query DB nanti
//
//  Contoh query PDO:
//
//  $profil = $pdo->query("SELECT * FROM profil_desa LIMIT 1")->fetch(PDO::FETCH_ASSOC);
//  $sejarah_teks = $profil['sejarah'];   // kolom LONGTEXT
//
//  $stats = $pdo->query("
//    SELECT label, nilai, satuan FROM statistik_desa ORDER BY urutan ASC
//  ")->fetchAll(PDO::FETCH_ASSOC);
//
//  $timeline = $pdo->query("
//    SELECT tahun, judul, deskripsi FROM timeline_desa ORDER BY tahun ASC
//  ")->fetchAll(PDO::FETCH_ASSOC);
// ============================================================

$sejarah_paragraf = [
  'Desa Gilang merupakan salah satu desa yang memiliki sejarah panjang dalam perkembangan masyarakat, budaya, serta kehidupan pertanian yang diwariskan secara turun-temurun.',
  'Berawal dari sebuah permukiman sederhana, Desa Gilang terus berkembang menjadi desa yang maju dengan tetap menjunjung tinggi nilai gotong royong, kebersamaan, dan kearifan lokal.',
  'Hingga saat ini, pemerintah desa bersama masyarakat terus berupaya meningkatkan kualitas pelayanan publik dan pembangunan berkelanjutan demi terciptanya kesejahteraan seluruh warga.',
];

$timeline = [
  ['tahun' => '1870', 'judul' => 'Awal Berdirinya Desa',        'deskripsi' => 'Desa Gilang mulai terbentuk sebagai kawasan permukiman masyarakat.'],
  ['tahun' => '1950', 'judul' => 'Perkembangan Pertanian',       'deskripsi' => 'Mayoritas masyarakat mulai mengembangkan sektor pertanian dan perkebunan.'],
  ['tahun' => '1995', 'judul' => 'Pembangunan Infrastruktur',    'deskripsi' => 'Pembangunan jalan desa dan fasilitas umum semakin meningkat.'],
  ['tahun' => '2026', 'judul' => 'Transformasi Digital Desa',    'deskripsi' => 'Desa Gilang mulai menerapkan sistem informasi berbasis website.'],
];

$stats = [
  ['nilai' => '2.340',  'label' => 'Jumlah Penduduk'],
  ['nilai' => '4',      'label' => 'Dusun'],
  ['nilai' => '18',     'label' => 'RT/RW'],
  ['nilai' => '530 Ha', 'label' => 'Luas Wilayah'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sejarah Desa — Desa Gilang</title>
  <meta name="description" content="Mengenal perjalanan panjang Desa Gilang dari masa ke masa, menjaga warisan budaya, semangat gotong royong, dan membangun masa depan yang lebih baik.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../../css/navbar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="stylesheet" href="../../css/profil.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ PAGE HERO ============ -->
    <section class="page-hero sejarah-hero">
      <div class="page-hero__overlay"></div>
      <div class="container page-hero__content">
        <span class="hero__eyebrow">Profil Desa</span>
        <h1 class="page-hero__title">Sejarah Desa Gilang</h1>
        <p class="page-hero__desc">
          Mengenal perjalanan panjang Desa Gilang dari masa ke masa, menjaga warisan budaya, semangat gotong royong, dan membangun masa depan yang lebih baik.
        </p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="/index.php">Beranda</a>
          <span aria-hidden="true">/</span>
          <span>Profil Desa</span>
          <span aria-hidden="true">/</span>
          <span aria-current="page">Sejarah Desa</span>
        </nav>
      </div>
    </section>

    <!-- ============ SEJARAH ============ -->
    <section class="section">
      <div class="container">
        <div class="history-grid">

          <div class="history-image">
            <img src="https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=900"
                 alt="Foto sawah dan alam Desa Gilang"
                 loading="eager">
          </div>

          <div class="history-text">
            <span class="eyebrow">Tentang Desa</span>
            <h2 class="heading-leaf">Perjalanan Desa Gilang</h2>

            <?php foreach ($sejarah_paragraf as $paragraf) : ?>
            <p><?php echo htmlspecialchars($paragraf); ?></p>
            <?php endforeach; ?>
          </div>

        </div>
      </div>
    </section>

    <!-- ============ TIMELINE ============ -->
    <section class="section section--alt">
      <div class="container">

        <div class="section__header-text">
          <span class="eyebrow">Perjalanan Waktu</span>
          <h2 class="heading-leaf">Timeline Sejarah</h2>
        </div>

        <div class="timeline">
          <?php foreach ($timeline as $item) : ?>
          <div class="timeline-item">
            <div class="timeline-year">
              <?php echo htmlspecialchars($item['tahun']); ?>
            </div>
            <div class="timeline-content">
              <h3><?php echo htmlspecialchars($item['judul']); ?></h3>
              <p><?php echo htmlspecialchars($item['deskripsi']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>

    <!-- ============ STATISTIK ============ -->
    <section class="section">
      <div class="container">
        <div class="stats-grid">
          <?php foreach ($stats as $stat) : ?>
          <div class="stat-card">
            <h2><?php echo htmlspecialchars($stat['nilai']); ?></h2>
            <p><?php echo htmlspecialchars($stat['label']); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ============ CTA ============ -->
    <section class="cta-section">
      <div class="container">
        <h2>Kenali Pemerintah Desa Gilang</h2>
        <p>Lihat struktur pemerintahan dan aparatur desa yang melayani masyarakat.</p>
        <a href="/pages/profil/aparatur-desa.php" class="btn btn--primary">
          Lihat Aparatur Desa
        </a>
      </div>
    </section>

  </main>

  <?php require_once __DIR__ . '/../../components/footer.php'; ?>

  <script src="/js/script.js"></script>
</body>
</html>