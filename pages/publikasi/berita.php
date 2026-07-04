<?php
// ============================================================
//  Koneksi DB — sesuaikan dengan config proyekmu
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Parameter filter & paginasi
// ============================================================
$kategori_aktif = isset($_GET['kategori']) ? trim($_GET['kategori']) : 'semua';
$page           = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page       = 6;
$offset         = ($page - 1) * $per_page;

$kategori_list = ['Semua', 'Kegiatan', 'Ekonomi', 'Pemerintahan', 'Sosial'];

// ============================================================
//  Data berita — ganti blok ini dengan query DB nanti
//  Contoh query PDO:
//
//  $where  = $kategori_aktif !== 'semua'
//            ? "WHERE k.slug = :slug AND b.status = 'terbit'"
//            : "WHERE b.status = 'terbit'";
//  $stmt   = $pdo->prepare("
//    SELECT b.id, b.judul, b.slug, b.ringkasan, b.thumbnail,
//           b.tanggal_terbit, k.nama AS kategori
//    FROM berita b
//    JOIN kategori_berita k ON k.id = b.kategori_id
//    $where
//    ORDER BY b.tanggal_terbit DESC
//    LIMIT :limit OFFSET :offset
//  ");
//  if ($kategori_aktif !== 'semua') $stmt->bindValue(':slug', $kategori_aktif);
//  $stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
//  $stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
//  $stmt->execute();
//  $berita_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//  $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM berita b
//    JOIN kategori_berita k ON k.id = b.kategori_id $where");
//  if ($kategori_aktif !== 'semua') $total_stmt->bindValue(':slug', $kategori_aktif);
//  $total_stmt->execute();
//  $total_berita = (int) $total_stmt->fetchColumn();
//  $total_page   = (int) ceil($total_berita / $per_page);
// ============================================================

$berita_list = [
  [
    'slug'      => 'gotong-royong-bersih-desa-sambut-musim-tanam',
    'img_src'   => 'https://picsum.photos/seed/berita-gotong-royong/480/300',
    'img_alt'   => 'Gotong royong bersih desa',
    'kategori'  => 'Kegiatan',
    'tanggal'   => '12 Jun 2026',
    'judul'     => 'Gotong Royong Bersih Desa Sambut Musim Tanam',
    'ringkasan' => 'Warga Desa Gilang bersama perangkat desa melaksanakan kerja bakti membersihkan saluran irigasi.',
  ],
  [
    'slug'      => 'pelatihan-umkm-olahan-hasil-panen',
    'img_src'   => 'https://picsum.photos/seed/berita-umkm-desa/480/300',
    'img_alt'   => 'Pelatihan UMKM desa',
    'kategori'  => 'Ekonomi',
    'tanggal'   => '08 Jun 2026',
    'judul'     => 'Pelatihan UMKM: Olahan Hasil Panen Jadi Produk Bernilai',
    'ringkasan' => 'Dinas Koperasi bekerja sama dengan pemerintah desa menggelar pelatihan pengolahan hasil panen.',
  ],
  [
    'slug'      => 'musyawarah-desa-bahas-rencana-anggaran-2027',
    'img_src'   => 'https://picsum.photos/seed/musyawarah-desa/480/300',
    'img_alt'   => 'Musyawarah desa',
    'kategori'  => 'Pemerintahan',
    'tanggal'   => '02 Jun 2026',
    'judul'     => 'Musyawarah Desa Bahas Rencana Anggaran 2027',
    'ringkasan' => 'Musyawarah desa digelar untuk membahas rancangan APBDes tahun 2027 bersama BPD dan tokoh masyarakat.',
  ],
  [
    'slug'      => 'posyandu-desa-gelar-pemeriksaan-kesehatan-gratis',
    'img_src'   => 'https://picsum.photos/seed/posyandu-desa/480/300',
    'img_alt'   => 'Kegiatan Posyandu desa',
    'kategori'  => 'Sosial',
    'tanggal'   => '28 Mei 2026',
    'judul'     => 'Posyandu Desa Gelar Pemeriksaan Kesehatan Gratis',
    'ringkasan' => 'Kegiatan rutin bulanan Posyandu menyasar ibu hamil, balita, dan lansia di seluruh dusun.',
  ],
  [
    'slug'      => 'panen-raya-padi-tandai-musim-tanam-berhasil',
    'img_src'   => 'https://picsum.photos/seed/panen-raya-desa/480/300',
    'img_alt'   => 'Panen raya padi',
    'kategori'  => 'Ekonomi',
    'tanggal'   => '20 Mei 2026',
    'judul'     => 'Panen Raya Padi Tandai Musim Tanam yang Berhasil',
    'ringkasan' => 'Hasil panen tahun ini meningkat dibanding tahun sebelumnya berkat perbaikan sistem irigasi.',
  ],
  [
    'slug'      => 'karang-taruna-pelatihan-kepemimpinan-pemuda',
    'img_src'   => 'https://picsum.photos/seed/pelatihan-pemuda-desa/480/300',
    'img_alt'   => 'Pelatihan pemuda desa',
    'kategori'  => 'Kegiatan',
    'tanggal'   => '15 Mei 2026',
    'judul'     => 'Karang Taruna Adakan Pelatihan Kepemimpinan Pemuda',
    'ringkasan' => 'Puluhan pemuda desa mengikuti pelatihan soft skill dan kepemimpinan selama dua hari.',
  ],
];

// Filter statis (nanti dihapus kalau sudah pakai DB)
if ($kategori_aktif !== 'semua') {
  $berita_list = array_values(array_filter($berita_list, function ($b) use ($kategori_aktif) {
    return strtolower($b['kategori']) === strtolower($kategori_aktif);
  }));
}

$total_berita = count($berita_list);
$total_page   = (int) ceil($total_berita / $per_page);
$berita_list  = array_slice($berita_list, $offset, $per_page);

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

  <link rel="icon" href="/assets/logo/logo-desa.png">
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
            $href = '/pages/publikasi/detail-berita.php?slug=' . urlencode($berita['slug']);
          ?>
          <article class="card-berita">
            <a href="<?php echo htmlspecialchars($href); ?>" class="card-berita__img-wrap">
              <img src="<?php echo htmlspecialchars($berita['img_src']); ?>"
                   alt="<?php echo htmlspecialchars($berita['img_alt']); ?>"
                   loading="lazy">
              <span class="card-berita__category"><?php echo htmlspecialchars($berita['kategori']); ?></span>
            </a>
            <div class="card-berita__body">
              <span class="card-berita__date font-mono"><?php echo htmlspecialchars($berita['tanggal']); ?></span>
              <h3 class="card-berita__title"><?php echo htmlspecialchars($berita['judul']); ?></h3>
              <p class="card-berita__excerpt"><?php echo htmlspecialchars($berita['ringkasan']); ?></p>
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