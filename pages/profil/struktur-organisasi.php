<?php
// ============================================================
//  Koneksi DB — sesuaikan dengan config proyekmu
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Konfigurasi halaman
// ============================================================
$page = [
  'title'       => 'Struktur Organisasi — Desa Gilang',
  'description' => 'Susunan pemerintahan Desa Gilang dalam menjalankan pelayanan dan pembangunan desa.',
  'eyebrow'     => 'PROFIL DESA',
  'heading'     => 'Struktur Organisasi',
  'favicon'     => '/assets/logo/logo-desa.png',
];

$breadcrumb = [
  ['label' => 'Beranda',      'url'  => '/index.php'],
  ['label' => 'Profil Desa',  'url'  => null],
  ['label' => 'Struktur Organisasi', 'url' => null, 'active' => true],
];

$section = [
  'eyebrow' => 'Pemerintahan Desa',
  'heading' => 'Struktur Organisasi Desa Gilang',
];

$cta = [
  'heading' => 'Kenali Aparatur Desa Gilang',
  'desc'    => 'Lihat seluruh perangkat desa yang melayani masyarakat.',
  'label'   => 'Lihat Aparatur',
  'url'     => '/pages/profil/aparatur-desa.php',
];

// ============================================================
//  Data aparatur — ganti dengan query DB nanti
//
//  Contoh query PDO:
//
//  $stmt = $pdo->query("
//    SELECT nama, jabatan, foto
//    FROM aparatur_desa
//    WHERE aktif = 1
//    ORDER BY urutan ASC
//  ");
//  $aparatur = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//  $kades  = array_values(array_filter($aparatur, fn($a) => $a['jabatan'] === 'Kepala Desa'))[0]  ?? null;
//  $sekdes = array_values(array_filter($aparatur, fn($a) => $a['jabatan'] === 'Sekretaris Desa'))[0] ?? null;
//  $staf   = array_values(array_filter($aparatur, fn($a) => !in_array($a['jabatan'], ['Kepala Desa', 'Sekretaris Desa'])));
// ============================================================

$kades = [
  'nama'    => 'Suwarno, S.Sos.',
  'jabatan' => 'Kepala Desa',
  'foto'    => 'https://picsum.photos/seed/kepala/300/300',
];

$sekdes = [
  'nama'    => 'Siti Aminah',
  'jabatan' => 'Sekretaris Desa',
  'foto'    => 'https://picsum.photos/seed/sekdes/300/300',
];

$staf = [
  ['nama' => 'Ahmad Fauzi',   'jabatan' => 'Kaur Umum',          'foto' => 'https://picsum.photos/seed/kaur1/300/300'],
  ['nama' => 'Nur Aisyah',    'jabatan' => 'Kaur Keuangan',      'foto' => 'https://picsum.photos/seed/kaur2/300/300'],
  ['nama' => 'Rudi Hartono',  'jabatan' => 'Kasi Pemerintahan',  'foto' => 'https://picsum.photos/seed/kasi1/300/300'],
  ['nama' => 'Indah Lestari', 'jabatan' => 'Kasi Kesejahteraan', 'foto' => 'https://picsum.photos/seed/kasi2/300/300'],
];

// ============================================================
//  Helper — render satu kartu aparatur
// ============================================================
function render_org_card(array $person, string $extra_class = '', string $loading = 'lazy'): void {
  $nama    = htmlspecialchars($person['nama']);
  $jabatan = htmlspecialchars($person['jabatan']);
  $foto    = htmlspecialchars($person['foto']);
  $class   = trim('org-card ' . $extra_class);
  echo <<<HTML
        <div class="{$class}">
          <img src="{$foto}" alt="Foto {$nama}" loading="{$loading}">
          <h3>{$nama}</h3>
          <p>{$jabatan}</p>
        </div>
  HTML;
}
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
  <link rel="stylesheet" href="../../css/struktur.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ PAGE HERO ============ -->
    <section class="page-hero struktur-hero">
      <div class="container page-hero__content">
        <span class="hero__eyebrow"><?php echo htmlspecialchars($page['eyebrow']); ?></span>
        <h1 class="page-hero__title"><?php echo htmlspecialchars($page['heading']); ?></h1>
        <p class="page-hero__desc"><?php echo htmlspecialchars($page['description']); ?></p>

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <?php foreach ($breadcrumb as $i => $crumb) : ?>
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

    <!-- ============ STRUKTUR ORGANISASI ============ -->
    <section class="section">
      <div class="container">

        <span class="eyebrow"><?php echo htmlspecialchars($section['eyebrow']); ?></span>
        <h2 class="heading-leaf"><?php echo htmlspecialchars($section['heading']); ?></h2>

        <div class="org-tree">

          <!-- Kepala Desa -->
          <?php if ($kades) : ?>
          <div class="org-level">
            <?php render_org_card($kades, 'kepala', 'eager'); ?>
          </div>
          <div class="line" aria-hidden="true"></div>
          <?php endif; ?>

          <!-- Sekretaris Desa -->
          <?php if ($sekdes) : ?>
          <div class="org-level">
            <?php render_org_card($sekdes, 'sekdes'); ?>
          </div>
          <?php endif; ?>

          <!-- Staf (Kaur & Kasi) -->
          <?php if (!empty($staf)) : ?>
          <div class="line" aria-hidden="true"></div>
          <div class="org-row">
            <?php foreach ($staf as $anggota) : ?>
              <?php render_org_card($anggota); ?>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

        </div>
      </div>
    </section>

    <!-- ============ CTA ============ -->
    <section class="cta-section">
      <div class="container">
        <h2><?php echo htmlspecialchars($cta['heading']); ?></h2>
        <p><?php echo htmlspecialchars($cta['desc']); ?></p>
        <a href="<?php echo htmlspecialchars($cta['url']); ?>" class="btn btn--primary">
          <?php echo htmlspecialchars($cta['label']); ?>
        </a>
      </div>
    </section>

  </main>

  <?php require_once __DIR__ . '/../../components/footer.php'; ?>

  <script src="../../js/navbar.js"></script>
  <script src="../../js/script.js"></script>
</body>
</html>