<?php
// ============================================================
//  Koneksi DB
// ============================================================
require_once __DIR__ . '/../../config/database.php';

// ============================================================
//  Parameter filter, pencarian, dan paginasi
// ============================================================
$filter_aktif = isset($_GET['kategori']) ? trim($_GET['kategori']) : 'semua';
$cari         = isset($_GET['q'])        ? trim($_GET['q'])        : '';
$page         = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page     = 9;

$filter_list = [
  'semua'        => 'Semua',
  'persyaratan'  => 'Persyaratan Pelayanan',
  'kesehatan'    => 'Informasi Kesehatan',
  'pengumuman'   => 'Pengumuman',
  'dokumen-desa' => 'Dokumen Desa',
];

// ============================================================
//  Data dokumen — query DB asli
// ============================================================
$where  = '1=1';
$params = [];
$types  = '';
if ($filter_aktif !== 'semua') {
  $where   .= ' AND kategori = ?';
  $params[] = $filter_aktif;
  $types   .= 's';
}
if ($cari !== '') {
  $where   .= ' AND nama LIKE ?';
  $params[] = '%' . $cari . '%';
  $types   .= 's';
}

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM dokumen WHERE $where");
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$total_dokumen = (int) (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'] ?? 0);
$total_page    = max(1, (int) ceil($total_dokumen / $per_page));
$page          = min($page, $total_page);
$offset        = ($page - 1) * $per_page;

$stmt = mysqli_prepare($conn, "SELECT id, nama, kategori, tanggal, file FROM dokumen WHERE $where ORDER BY tanggal DESC LIMIT ? OFFSET ?");
$typesLimit  = $types . 'ii';
$paramsLimit = array_merge($params, [$per_page, $offset]);
mysqli_stmt_bind_param($stmt, $typesLimit, ...$paramsLimit);
mysqli_stmt_execute($stmt);
$dokumen_list = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Nomor urut tetap sesuai posisi global
$nomor_awal = ($page - 1) * $per_page + 1;

function format_tanggal_dokumen_publik(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}

// Helper: bangun URL dengan query string yang konsisten
function dokumen_url(string $kategori = 'semua', int $page = 1, string $cari = ''): string {
  $params = [];
  if ($kategori !== 'semua') $params['kategori'] = $kategori;
  if ($cari !== '')          $params['q']        = $cari;
  if ($page > 1)             $params['page']     = $page;
  return 'dokumen.php' . ($params ? '?' . http_build_query($params) : '');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dokumen Publik — Desa Gilang</title>
  <meta name="description" content="Daftar dokumen dan informasi publik Desa Gilang: persyaratan pelayanan, informasi kesehatan, pengumuman, dan dokumen desa.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="../../css/style.css">
  <link rel="stylesheet" href="../../css/navbar.css">
  <link rel="stylesheet" href="../../css/footer.css">
  <link rel="stylesheet" href="../../css/dokumen.css">
  <link rel="stylesheet" href="../../css/publikasi.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ PAGE HERO ============ -->
    <section class="page-hero berita-hero">
      <div class="container page-hero__content">
        <span class="hero__eyebrow">PUBLIKASI DESA</span>
        <h1 class="page-hero__title">Dokumen Desa Gilang</h1>
        <p class="page-hero__desc">
          Kumpulan dokumen publik Desa Gilang yang dapat diakses oleh masyarakat sebagai bentuk transparansi dan keterbukaan informasi.
        </p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="../../index.php">Beranda</a>
          <span aria-hidden="true">/</span>
          <span>Publikasi</span>
          <span aria-hidden="true">/</span>
          <span aria-current="page">Dokumen</span>
        </nav>
      </div>
    </section>

    <!-- ============ DAFTAR DOKUMEN ============ -->
    <section class="section">
      <div class="container">

        <!-- Toolbar: filter + pencarian -->
        <div class="dokumen-toolbar">

          <!-- Filter Kategori -->
          <div class="dokumen-filters" role="group" aria-label="Filter kategori dokumen">
            <?php foreach ($filter_list as $slug => $label) : ?>
            <a href="<?php echo dokumen_url($slug, 1, $cari); ?>"
               class="filter-chip <?php echo $filter_aktif === $slug ? 'is-active' : ''; ?>"
               aria-pressed="<?php echo $filter_aktif === $slug ? 'true' : 'false'; ?>">
              <?php echo htmlspecialchars($label); ?>
            </a>
            <?php endforeach; ?>
          </div>

          <!-- Form Pencarian -->
          <form method="get" action="/pages/publikasi/dokumen.php" class="dokumen-search" role="search">
            <?php if ($filter_aktif !== 'semua') : ?>
            <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($filter_aktif); ?>">
            <?php endif; ?>
            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="7" cy="7" r="5.2" stroke="currentColor" stroke-width="1.5"/>
              <path d="M14 14l-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input type="text"
                   name="q"
                   id="dokumenSearch"
                   placeholder="Cari nama dokumen..."
                   value="<?php echo htmlspecialchars($cari); ?>"
                   autocomplete="off">
          </form>

        </div>

        <!-- Tabel Dokumen -->
        <div class="dokumen-table-wrap">
          <?php if (empty($dokumen_list)) : ?>
          <div class="dokumen-empty">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/>
              <path d="M21 21l-4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <p>Tidak ada dokumen yang cocok dengan filter atau kata kunci yang dimasukkan.</p>
          </div>
          <?php else : ?>
          <table class="dokumen-table">
            <thead>
              <tr>
                <th scope="col">No</th>
                <th scope="col">Nama Dokumen</th>
                <th scope="col">Kategori</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dokumen_list as $i => $dok) : ?>
              <tr>
                <td class="font-mono"><?php echo str_pad($nomor_awal + $i, 2, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($dok['nama']); ?></td>
                <td><span class="dokumen-badge"><?php echo htmlspecialchars($filter_list[$dok['kategori']] ?? $dok['kategori']); ?></span></td>
                <td class="font-mono"><?php echo format_tanggal_dokumen_publik($dok['tanggal']); ?></td>
                <td>
                  <a href="<?php echo htmlspecialchars($dok['file']); ?>"
                     class="dokumen-download"
                     download target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                      <path d="M8 2v8m0 0L5 7m3 3l3-3M3 13h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Unduh
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>

        <!-- Paginasi -->
        <?php if ($total_page > 1) : ?>
        <nav class="pagination" aria-label="Paginasi dokumen">
          <?php if ($page > 1) : ?>
          <a href="<?php echo dokumen_url($filter_aktif, $page - 1, $cari); ?>"
             class="pagination__prev" aria-label="Halaman sebelumnya">&lsaquo;</a>
          <?php endif; ?>

          <?php for ($p = 1; $p <= $total_page; $p++) : ?>
          <a href="<?php echo dokumen_url($filter_aktif, $p, $cari); ?>"
             class="<?php echo $p === $page ? 'is-active' : ''; ?>"
             aria-label="Halaman <?php echo $p; ?>"
             <?php echo $p === $page ? 'aria-current="page"' : ''; ?>>
            <?php echo $p; ?>
          </a>
          <?php endfor; ?>

          <?php if ($page < $total_page) : ?>
          <a href="<?php echo dokumen_url($filter_aktif, $page + 1, $cari); ?>"
             class="pagination__next" aria-label="Halaman berikutnya">&rsaquo;</a>
          <?php endif; ?>
        </nav>
        <?php endif; ?>

        <!-- Info hasil -->
        <?php if (!empty($dokumen_list)) : ?>
        <p class="dokumen-info font-mono">
          Menampilkan <?php echo $nomor_awal; ?>–<?php echo min($nomor_awal + $per_page - 1, $total_dokumen); ?>
          dari <?php echo $total_dokumen; ?> dokumen
          <?php if ($cari !== '') : ?>
            untuk pencarian "<strong><?php echo htmlspecialchars($cari); ?></strong>"
          <?php endif; ?>
        </p>
        <?php endif; ?>

      </div>
    </section>

  </main>

  <?php require_once __DIR__ . '/../../components/footer.php'; ?>

  <script src="../../js/navbar.js"></script>
  <script src="../../js/script.js"></script>
</body>
</html>