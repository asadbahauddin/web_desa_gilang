<?php
// ============================================================
//  Koneksi DB
// ============================================================
require_once __DIR__ . '/../../config/database.php';

// ============================================================
//  Parameter filter & paginasi
// ============================================================
$kategori_aktif = isset($_GET['kategori']) ? trim($_GET['kategori']) : 'semua';
$page           = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page       = 6;
$offset         = ($page - 1) * $per_page;

$kategori_list  = ['Semua', 'Kegiatan', 'Ekonomi', 'Pemerintahan', 'Sosial'];
$kategori_label = [
  'kegiatan'     => 'Kegiatan',
  'ekonomi'      => 'Ekonomi',
  'pemerintahan' => 'Pemerintahan',
  'sosial'       => 'Sosial',
];

// ============================================================
//  Data berita — query DB asli (hanya yang berstatus published)
// ============================================================
$where  = "status = 'published'";
$params = [];
$types  = '';
if ($kategori_aktif !== 'semua') {
  $where   .= " AND kategori = ?";
  $params[] = $kategori_aktif;
  $types   .= 's';
}

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM berita WHERE $where");
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$total_berita = (int) (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'] ?? 0);
$total_page   = max(1, (int) ceil($total_berita / $per_page));
$page         = min($page, $total_page);
$offset       = ($page - 1) * $per_page;

$stmt = mysqli_prepare($conn, "SELECT id, judul, excerpt, kategori, gambar, tanggal FROM berita WHERE $where ORDER BY tanggal DESC LIMIT ? OFFSET ?");
$typesLimit  = $types . 'ii';
$paramsLimit = array_merge($params, [$per_page, $offset]);
mysqli_stmt_bind_param($stmt, $typesLimit, ...$paramsLimit);
mysqli_stmt_execute($stmt);
$berita_list = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Helper: format tanggal Indonesia
function format_tanggal_publik(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}

// Helper: bangun URL filter dengan query string
function filter_url(string $kategori, int $page = 1): string {
  $params = [];
  if (strtolower($kategori) !== 'semua') $params['kategori'] = strtolower($kategori);
  if ($page > 1) $params['page'] = $page;
  return '/pages/publikasi/berita.php' . ($params ? '?' . http_build_query($params) : '');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Berita Desa — Desa Gilang</title>
  <meta name="description" content="Kumpulan berita dan kabar terbaru seputar kegiatan Desa Gilang.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../../css/navbar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="stylesheet" href="../../css/berita.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ PAGE HERO ============ -->
    <section class="page-hero berita-hero">
      <div class="container page-hero__content">
        <span class="hero__eyebrow">PUBLIKASI DESA</span>
        <h1 class="page-hero__title">Berita Desa Gilang</h1>
        <p class="page-hero__desc">
          Kumpulan informasi, kegiatan, pembangunan, dan kabar terbaru Desa Gilang.
        </p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="/index.php">Beranda</a>
          <span aria-hidden="true">/</span>
          <span>Publikasi</span>
          <span aria-hidden="true">/</span>
          <span aria-current="page">Berita</span>
        </nav>
      </div>
    </section>

    <!-- ============ DAFTAR BERITA ============ -->
    <section class="section">
      <div class="container">

        <!-- Filter Kategori -->
        <div class="berita-toolbar" role="group" aria-label="Filter kategori berita">
          <?php foreach ($kategori_list as $kat) : ?>
            <?php
              $slug   = strtolower($kat);
              $aktif  = ($slug === $kategori_aktif) || ($kat === 'Semua' && $kategori_aktif === 'semua');
            ?>
            <a href="<?php echo filter_url($slug); ?>"
               class="filter-chip <?php echo $aktif ? 'is-active' : ''; ?>"
               aria-pressed="<?php echo $aktif ? 'true' : 'false'; ?>">
              <?php echo htmlspecialchars($kat); ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Grid Berita -->
        <?php if (empty($berita_list)) : ?>
        <div class="berita-kosong">
          <p>Belum ada berita untuk kategori ini.</p>
        </div>
        <?php else : ?>
        <div class="berita-grid-full">
          <?php foreach ($berita_list as $berita) :
            $href   = '/pages/publikasi/detail-berita.php?id=' . $berita['id'];
            $gambar = $berita['gambar'] ?: 'https://picsum.photos/seed/berita-' . $berita['id'] . '/480/300';
            $label  = $kategori_label[$berita['kategori']] ?? $berita['kategori'];
          ?>
          <article class="card-berita">
            <a href="<?php echo htmlspecialchars($href); ?>" class="card-berita__img-wrap">
              <img src="<?php echo htmlspecialchars($gambar); ?>"
                   alt="<?php echo htmlspecialchars($berita['judul']); ?>"
                   loading="lazy">
              <span class="card-berita__category"><?php echo htmlspecialchars($label); ?></span>
            </a>
            <div class="card-berita__body">
              <span class="card-berita__date font-mono"><?php echo format_tanggal_publik($berita['tanggal']); ?></span>
              <h3 class="card-berita__title"><?php echo htmlspecialchars($berita['judul']); ?></h3>
              <p class="card-berita__excerpt"><?php echo htmlspecialchars($berita['excerpt']); ?></p>
              <a href="<?php echo htmlspecialchars($href); ?>" class="card-berita__link">
                Baca selengkapnya
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M2 8h12M9 3l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Paginasi -->
        <?php if ($total_page > 1) : ?>
        <nav class="pagination" aria-label="Paginasi berita">
          <?php if ($page > 1) : ?>
          <a href="<?php echo filter_url($kategori_aktif, $page - 1); ?>"
             class="pagination__prev" aria-label="Halaman sebelumnya">
            &lsaquo;
          </a>
          <?php endif; ?>

          <?php for ($p = 1; $p <= $total_page; $p++) : ?>
          <a href="<?php echo filter_url($kategori_aktif, $p); ?>"
             class="<?php echo $p === $page ? 'is-active' : ''; ?>"
             aria-label="Halaman <?php echo $p; ?>"
             <?php echo $p === $page ? 'aria-current="page"' : ''; ?>>
            <?php echo $p; ?>
          </a>
          <?php endfor; ?>

          <?php if ($page < $total_page) : ?>
          <a href="<?php echo filter_url($kategori_aktif, $page + 1); ?>"
             class="pagination__next" aria-label="Halaman berikutnya">
            &rsaquo;
          </a>
          <?php endif; ?>
        </nav>
        <?php endif; ?>

      </div>
    </section>

  </main>

  <?php require_once __DIR__ . '/../../components/footer.php'; ?>

  <script src="../../js/navbar.js"></script>
  <script src="../../js/script.js"></script>
</body>
</html>