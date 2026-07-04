<?php
// ============================================================
//  Koneksi DB — sesuaikan dengan config proyekmu
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Parameter filter, pencarian, dan paginasi
// ============================================================
$filter_aktif = isset($_GET['kategori']) ? trim($_GET['kategori']) : 'semua';
$cari         = isset($_GET['q'])        ? trim($_GET['q'])        : '';
$page         = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page     = 9;
$offset       = ($page - 1) * $per_page;

$filter_list = [
  'semua'        => 'Semua',
  'persyaratan'  => 'Persyaratan Pelayanan',
  'kesehatan'    => 'Informasi Kesehatan',
  'pengumuman'   => 'Pengumuman',
  'dokumen-desa' => 'Dokumen Desa',
];

// ============================================================
//  Data dokumen — ganti dengan query DB nanti
//
//  Contoh query PDO:
//
//  $where_parts = ["d.status = 'aktif'"];
//  $params      = [];
//
//  if ($filter_aktif !== 'semua') {
//    $where_parts[] = "k.slug = :slug";
//    $params[':slug'] = $filter_aktif;
//  }
//  if ($cari !== '') {
//    $where_parts[] = "d.nama LIKE :q";
//    $params[':q'] = '%' . $cari . '%';
//  }
//
//  $where = 'WHERE ' . implode(' AND ', $where_parts);
//
//  $stmt = $pdo->prepare("
//    SELECT d.id, d.nama, d.file_path, d.tanggal, k.nama AS kategori, k.slug AS kategori_slug
//    FROM dokumen d
//    JOIN kategori_dokumen k ON k.id = d.kategori_id
//    $where
//    ORDER BY d.tanggal DESC
//    LIMIT :limit OFFSET :offset
//  ");
//  foreach ($params as $k => $v) $stmt->bindValue($k, $v);
//  $stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
//  $stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
//  $stmt->execute();
//  $dokumen_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//  $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM dokumen d
//    JOIN kategori_dokumen k ON k.id = d.kategori_id $where");
//  foreach ($params as $k => $v) $count_stmt->bindValue($k, $v);
//  $count_stmt->execute();
//  $total_dokumen = (int) $count_stmt->fetchColumn();
//  $total_page    = (int) ceil($total_dokumen / $per_page);
// ============================================================

$semua_dokumen = [
  ['no' => '01', 'nama' => 'Syarat Pengajuan KTP Baru',                         'kategori' => 'Persyaratan Pelayanan', 'slug_kat' => 'persyaratan',  'tanggal' => '10 Jan 2026', 'file' => '#'],
  ['no' => '02', 'nama' => 'Syarat Pembuatan Surat Keterangan Tidak Mampu (SKTM)', 'kategori' => 'Persyaratan Pelayanan', 'slug_kat' => 'persyaratan',  'tanggal' => '22 Nov 2025', 'file' => '#'],
  ['no' => '03', 'nama' => 'Jadwal Posyandu Bulan Juni 2026',                   'kategori' => 'Informasi Kesehatan',   'slug_kat' => 'kesehatan',    'tanggal' => '01 Jun 2026', 'file' => '#'],
  ['no' => '04', 'nama' => 'Informasi Imunisasi Anak Tahun 2026',               'kategori' => 'Informasi Kesehatan',   'slug_kat' => 'kesehatan',    'tanggal' => '15 Mei 2026', 'file' => '#'],
  ['no' => '05', 'nama' => 'Pengumuman Penerimaan Bantuan Sosial',               'kategori' => 'Pengumuman',            'slug_kat' => 'pengumuman',   'tanggal' => '10 Jun 2026', 'file' => '#'],
  ['no' => '06', 'nama' => 'Pengumuman Libur Pelayanan Kantor Desa',             'kategori' => 'Pengumuman',            'slug_kat' => 'pengumuman',   'tanggal' => '28 Mei 2026', 'file' => '#'],
  ['no' => '07', 'nama' => 'APBDes Tahun Anggaran 2026',                         'kategori' => 'Dokumen Desa',          'slug_kat' => 'dokumen-desa', 'tanggal' => '15 Jan 2026', 'file' => '#'],
  ['no' => '08', 'nama' => 'Peraturan Desa No. 3 Tahun 2025',                   'kategori' => 'Dokumen Desa',          'slug_kat' => 'dokumen-desa', 'tanggal' => '02 Des 2025', 'file' => '#'],
  ['no' => '09', 'nama' => 'SK Penetapan Perangkat Desa',                        'kategori' => 'Dokumen Desa',          'slug_kat' => 'dokumen-desa', 'tanggal' => '10 Jan 2025', 'file' => '#'],
];

// Filter statis (hapus setelah pakai DB)
$dokumen_list = array_values(array_filter($semua_dokumen, function ($d) use ($filter_aktif, $cari) {
  $cocok_filter = $filter_aktif === 'semua' || $d['slug_kat'] === $filter_aktif;
  $cocok_cari   = $cari === '' || stripos($d['nama'], $cari) !== false;
  return $cocok_filter && $cocok_cari;
}));

$total_dokumen = count($dokumen_list);
$total_page    = max(1, (int) ceil($total_dokumen / $per_page));
$page          = min($page, $total_page);
$dokumen_list  = array_slice($dokumen_list, ($page - 1) * $per_page, $per_page);

// Nomor urut tetap sesuai posisi global
$nomor_awal = ($page - 1) * $per_page + 1;

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
                <td><span class="dokumen-badge"><?php echo htmlspecialchars($dok['kategori']); ?></span></td>
                <td class="font-mono"><?php echo htmlspecialchars($dok['tanggal']); ?></td>
                <td>
                  <a href="<?php echo htmlspecialchars($dok['file']); ?>"
                     class="dokumen-download"
                     <?php if ($dok['file'] !== '#') echo 'download target="_blank" rel="noopener noreferrer"'; ?>>
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