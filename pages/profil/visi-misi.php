<?php
// ============================================================
//  Koneksi DB — sesuaikan dengan config proyekmu
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Konfigurasi halaman
// ============================================================
$page = [
  'title'       => 'Visi dan Misi | Desa Gilang',
  'eyebrow'     => 'PROFIL DESA',
  'heading'     => 'Visi & Misi Desa Gilang',
  'description' => 'Komitmen Pemerintah Desa Gilang dalam mewujudkan pelayanan yang maju, mandiri, sejahtera, dan berkelanjutan.',
  'favicon' => '../../assets/logo/logo-desa.jpg',
];

$breadcrumb = [
  ['label' => 'Beranda', 'url' => '../../index.php'],
  ['label' => 'Profil Desa', 'url'    => null],
  ['label' => 'Visi & Misi', 'active' => true],
];

// ============================================================
//  Visi
// ============================================================
$visi = [
  'eyebrow' => 'Visi Desa',
  'heading' => 'Arah Pembangunan Desa',
  'icon'    => '🌿',
  'teks'    => 'Terwujudnya Desa Gilang yang Maju, Mandiri, Sejahtera, Berbudaya, dan Berdaya Saing Melalui Tata Kelola Pemerintahan yang Transparan serta Pelayanan Prima.',
];

// ============================================================
//  Misi
//
//  Contoh query PDO:
//  $stmt = $pdo->query("SELECT nomor, judul, deskripsi FROM misi_desa ORDER BY nomor ASC");
//  $misi['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ============================================================
$misi = [
  'eyebrow' => 'Misi Desa',
  'heading' => 'Langkah Menuju Visi',
  'items'   => [
    ['nomor' => '01', 'judul' => 'Pelayanan Publik',          'deskripsi' => 'Meningkatkan kualitas pelayanan yang cepat, tepat, dan transparan.'],
    ['nomor' => '02', 'judul' => 'Pembangunan Infrastruktur', 'deskripsi' => 'Meningkatkan sarana dan prasarana desa yang berkelanjutan.'],
    ['nomor' => '03', 'judul' => 'Pemberdayaan Ekonomi',      'deskripsi' => 'Mengembangkan potensi UMKM dan ekonomi masyarakat.'],
    ['nomor' => '04', 'judul' => 'SDM Berkualitas',           'deskripsi' => 'Meningkatkan pendidikan dan kesejahteraan masyarakat.'],
    ['nomor' => '05', 'judul' => 'Pelestarian Budaya',        'deskripsi' => 'Menjaga nilai budaya dan semangat gotong royong.'],
    ['nomor' => '06', 'judul' => 'Lingkungan Berkelanjutan',  'deskripsi' => 'Menjaga kelestarian lingkungan dan sumber daya alam.'],
  ],
];

// ============================================================
//  Banner foto
// ============================================================
$banner = [
  'src' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=1600',
  'alt' => 'Panorama Desa',
];

// ============================================================
//  Nilai-nilai
//
//  Contoh query PDO:
//  $stmt = $pdo->query("SELECT ikon, label FROM nilai_desa ORDER BY urutan ASC");
//  $nilai['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ============================================================
$nilai = [
  'eyebrow' => 'Nilai-Nilai',
  'heading' => 'Prinsip Pemerintahan Desa',
  'items'   => [
    ['ikon' => '🤝', 'label' => 'Gotong Royong'],
    ['ikon' => '⚖️', 'label' => 'Transparan'],
    ['ikon' => '🌱', 'label' => 'Berkelanjutan'],
    ['ikon' => '❤️', 'label' => 'Pelayanan Prima'],
  ],
];

// ============================================================
//  Tujuan / Sasaran
//
//  Contoh query PDO:
//  $stmt = $pdo->query("SELECT ikon, label FROM tujuan_desa ORDER BY urutan ASC");
//  $tujuan['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ============================================================
$tujuan = [
  'eyebrow' => 'Tujuan',
  'heading' => 'Sasaran Pembangunan Desa',
  'items'   => [
    ['ikon' => '🏡', 'label' => 'Desa Maju'],
    ['ikon' => '📈', 'label' => 'Desa Mandiri'],
    ['ikon' => '😊', 'label' => 'Desa Sejahtera'],
    ['ikon' => '🌿', 'label' => 'Desa Berkelanjutan'],
  ],
];

// ============================================================
//  Quote
// ============================================================
$quote = [
  'teks'   => 'Membangun desa bukan hanya membangun infrastruktur, tetapi membangun kesejahteraan, kebersamaan, dan masa depan masyarakat.',
  'sumber' => '— Kepala Desa Gilang',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page['title']); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($page['description']); ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="<?php echo htmlspecialchars($page['favicon']); ?>">
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../../css/navbar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="stylesheet" href="../../css/visi.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ PAGE HERO ============ -->
    <section class="page-hero visi-hero">
      <div class="container page-hero__content">

        <span class="hero__eyebrow"><?php echo htmlspecialchars($page['eyebrow']); ?></span>
        <h1 class="page-hero__title"><?php echo htmlspecialchars($page['heading']); ?></h1>
        <p class="page-hero__desc"><?php echo htmlspecialchars($page['description']); ?></p>

        <nav class="breadcrumb" aria-label="Breadcrumb">
          <?php foreach ($breadcrumb as $crumb) : ?>
            <?php if (!empty($crumb['active'])) : ?>
              <span aria-current="page"><?php echo htmlspecialchars($crumb['label']); ?></span>
            <?php elseif (!empty($crumb['url'])) : ?>
              <a href="<?php echo htmlspecialchars($crumb['url']); ?>"><?php echo htmlspecialchars($crumb['label']); ?></a>
              <span aria-hidden="true">/</span>
            <?php else : ?>
              <span><?php echo htmlspecialchars($crumb['label']); ?></span>
              <span aria-hidden="true">/</span>
            <?php endif; ?>
          <?php endforeach; ?>
        </nav>

      </div>
    </section>

    <!-- ============ VISI ============ -->
    <section class="section">
      <div class="container">

        <span class="eyebrow"><?php echo htmlspecialchars($visi['eyebrow']); ?></span>
        <h2 class="heading-leaf"><?php echo htmlspecialchars($visi['heading']); ?></h2>

        <div class="vision-card">
          <div class="vision-icon"><?php echo $visi['icon']; ?></div>
          <h2><?php echo htmlspecialchars($visi['teks']); ?></h2>
        </div>

      </div>
    </section>

    <!-- ============ MISI ============ -->
    <section class="section section--alt">
      <div class="container">

        <span class="eyebrow"><?php echo htmlspecialchars($misi['eyebrow']); ?></span>
        <h2 class="heading-leaf"><?php echo htmlspecialchars($misi['heading']); ?></h2>

        <div class="mission-grid">
          <?php foreach ($misi['items'] as $item) : ?>
          <div class="mission-card">
            <div class="mission-number"><?php echo htmlspecialchars($item['nomor']); ?></div>
            <h3><?php echo htmlspecialchars($item['judul']); ?></h3>
            <p><?php echo htmlspecialchars($item['deskripsi']); ?></p>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>

    <!-- ============ FOTO DESA ============ -->
    <section class="section">
      <div class="container">
        <img
          class="visi-banner"
          src="<?php echo htmlspecialchars($banner['src']); ?>"
          alt="<?php echo htmlspecialchars($banner['alt']); ?>"
          loading="lazy">
      </div>
    </section>

    <!-- ============ NILAI-NILAI ============ -->
    <section class="section">
      <div class="container">

        <span class="eyebrow"><?php echo htmlspecialchars($nilai['eyebrow']); ?></span>
        <h2 class="heading-leaf"><?php echo htmlspecialchars($nilai['heading']); ?></h2>

        <div class="value-grid">
          <?php foreach ($nilai['items'] as $item) : ?>
          <div class="value-card">
            <?php echo $item['ikon']; ?>
            <h3><?php echo htmlspecialchars($item['label']); ?></h3>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>

    <!-- ============ TUJUAN ============ -->
    <section class="section section--alt">
      <div class="container">

        <span class="eyebrow"><?php echo htmlspecialchars($tujuan['eyebrow']); ?></span>
        <h2 class="heading-leaf"><?php echo htmlspecialchars($tujuan['heading']); ?></h2>

        <div class="goal-grid">
          <?php foreach ($tujuan['items'] as $item) : ?>
          <div class="goal-card">
            <?php echo $item['ikon']; ?>
            <h3><?php echo htmlspecialchars($item['label']); ?></h3>
          </div>
          <?php endforeach; ?>
        </div>

      </div>
    </section>

    <!-- ============ QUOTE ============ -->
    <section class="quote-section">
      <div class="container">
        <div class="quote-box">
          <span aria-hidden="true">❝</span>
          <h2><?php echo htmlspecialchars($quote['teks']); ?></h2>
          <p><?php echo htmlspecialchars($quote['sumber']); ?></p>
        </div>
      </div>
    </section>

  </main>

  <?php require_once __DIR__ . '/../../components/footer.php'; ?>

  <script src="../../js/navbar.js"></script>
  <script src="../../js/script.js"></script>
</body>
</html>