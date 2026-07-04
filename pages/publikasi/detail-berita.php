<?php
// ============================================================
//  Koneksi DB — sesuaikan dengan config proyekmu
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Ambil slug dari URL
// ============================================================
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
  header('Location: /pages/publikasi/berita.php');
  exit;
}

// ============================================================
//  Data berita — ganti dengan query DB nanti
//
//  Contoh query PDO:
//
//  $stmt = $pdo->prepare("
//    SELECT b.*, k.nama AS kategori, a.nama AS penulis
//    FROM berita b
//    JOIN kategori_berita k ON k.id = b.kategori_id
//    JOIN admin a ON a.id = b.admin_id
//    WHERE b.slug = :slug AND b.status = 'terbit'
//    LIMIT 1
//  ");
//  $stmt->execute([':slug' => $slug]);
//  $berita = $stmt->fetch(PDO::FETCH_ASSOC);
//
//  if (!$berita) {
//    header('HTTP/1.0 404 Not Found');
//    include $_SERVER['DOCUMENT_ROOT'] . '/404.php';
//    exit;
//  }
//
//  // Update counter dilihat
//  $pdo->prepare("UPDATE berita SET dilihat = dilihat + 1 WHERE slug = :slug")
//      ->execute([':slug' => $slug]);
//
//  // Berita terkait (kategori sama, bukan berita ini)
//  $rel = $pdo->prepare("
//    SELECT b.judul, b.slug, b.thumbnail, b.tanggal_terbit, k.nama AS kategori
//    FROM berita b
//    JOIN kategori_berita k ON k.id = b.kategori_id
//    WHERE b.kategori_id = :kid AND b.slug != :slug AND b.status = 'terbit'
//    ORDER BY b.tanggal_terbit DESC
//    LIMIT 3
//  ");
//  $rel->execute([':kid' => $berita['kategori_id'], ':slug' => $slug]);
//  $berita_terkait = $rel->fetchAll(PDO::FETCH_ASSOC);
// ============================================================

// Data statis sementara — indeks by slug
$semua_berita = [
  'gotong-royong-bersih-desa-sambut-musim-tanam' => [
    'judul'      => 'Gotong Royong Bersih Desa Sambut Musim Tanam',
    'kategori'   => 'Kegiatan',
    'tanggal'    => '12 Juni 2026',
    'penulis'    => 'Admin Desa',
    'cover'      => 'https://picsum.photos/seed/berita-gotong-royong/960/540',
    'cover_alt'  => 'Warga bergotong royong membersihkan saluran irigasi',
    'tags'       => ['Gotong Royong', 'Pertanian', 'Irigasi'],
    'isi'        => '
      <p>Warga Desa Gilang bersama perangkat desa melaksanakan kegiatan kerja bakti membersihkan saluran irigasi
      di seluruh wilayah dusun pada hari Minggu pagi. Kegiatan ini merupakan agenda rutin yang dilaksanakan
      setiap menjelang musim tanam guna memastikan aliran air ke sawah-sawah warga berjalan lancar.</p>

      <p>Kepala Desa Gilang, Bapak Suwarno, turut hadir dan ikut serta dalam kegiatan tersebut. Ia menyampaikan
      bahwa tradisi gotong royong merupakan warisan nilai yang harus terus dijaga sebagai identitas sosial
      masyarakat desa.</p>

      <blockquote>"Gotong royong bukan sekadar kerja bersama, tapi cara kita menjaga kebersamaan dan tanggung jawab terhadap lingkungan tempat kita tinggal."</blockquote>

      <h3>Dampak Bagi Petani</h3>
      <p>Saluran irigasi yang bersih dari sedimen dan tumbuhan liar diharapkan dapat meningkatkan efisiensi
      distribusi air ke lahan persawahan, terutama bagi petani di wilayah Dusun II dan III yang selama ini
      kerap mengalami keterlambatan pasokan air saat awal musim tanam.</p>

      <p>Selain membersihkan saluran irigasi, warga juga memperbaiki beberapa titik tanggul yang mulai rapuh
      serta menanam pohon peneduh di sepanjang jalan menuju area persawahan sebagai upaya pelestarian
      lingkungan jangka panjang.</p>
    ',
  ],
];

$berita = $semua_berita[$slug] ?? null;

if (!$berita) {
  header('HTTP/1.0 404 Not Found');
  include $_SERVER['DOCUMENT_ROOT'] . '/404.php';
  exit;
}

// Berita terkait statis sementara
$berita_terkait = [
  [
    'slug'     => 'panen-raya-padi-tandai-musim-tanam-berhasil',
    'judul'    => 'Panen Raya Padi Tandai Musim Tanam yang Berhasil',
    'kategori' => 'Ekonomi',
    'tanggal'  => '20 Mei 2026',
    'img_src'  => 'https://picsum.photos/seed/panen-raya-desa/480/300',
    'img_alt'  => 'Panen raya padi',
  ],
  [
    'slug'     => 'musyawarah-desa-bahas-rencana-anggaran-2027',
    'judul'    => 'Musyawarah Desa Bahas Rencana Anggaran 2027',
    'kategori' => 'Pemerintahan',
    'tanggal'  => '02 Jun 2026',
    'img_src'  => 'https://picsum.photos/seed/musyawarah-desa/480/300',
    'img_alt'  => 'Musyawarah desa',
  ],
  [
    'slug'     => 'posyandu-desa-gelar-pemeriksaan-kesehatan-gratis',
    'judul'    => 'Posyandu Desa Gelar Pemeriksaan Kesehatan Gratis',
    'kategori' => 'Sosial',
    'tanggal'  => '28 Mei 2026',
    'img_src'  => 'https://picsum.photos/seed/posyandu-desa/480/300',
    'img_alt'  => 'Kegiatan Posyandu desa',
  ],
];

// URL halaman ini untuk tombol share
$url_halaman = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$judul_encode = urlencode($berita['judul']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($berita['judul']); ?> — Desa Gilang</title>
  <meta name="description" content="<?php echo htmlspecialchars(strip_tags(substr($berita['isi'], 0, 160))); ?>">

  <!-- Open Graph -->
  <meta property="og:title"       content="<?php echo htmlspecialchars($berita['judul']); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars(strip_tags(substr($berita['isi'], 0, 160))); ?>">
  <meta property="og:image"       content="<?php echo htmlspecialchars($berita['cover']); ?>">
  <meta property="og:url"         content="<?php echo htmlspecialchars($url_halaman); ?>">
  <meta property="og:type"        content="article">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.png">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/navbar.css">
  <link rel="stylesheet" href="/css/footer.css">
  <link rel="stylesheet" href="/css/home.css">
  <link rel="stylesheet" href="/css/berita.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ BREADCRUMB ============ -->
    <header class="page-header">
      <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="/index.php">Beranda</a>
          <span aria-hidden="true">/</span>
          <a href="/pages/publikasi/berita.php">Berita</a>
          <span aria-hidden="true">/</span>
          <span aria-current="page"><?php echo htmlspecialchars($berita['judul']); ?></span>
        </nav>
      </div>
    </header>

    <!-- ============ DETAIL BERITA ============ -->
    <section class="section">
      <div class="container">

        <article class="detail-berita">

          <!-- Meta -->
          <div class="detail-berita__meta">
            <span class="card-berita__category" style="position:static;">
              <?php echo htmlspecialchars($berita['kategori']); ?>
            </span>
            <span class="text-muted font-mono" style="font-size:0.85rem;">
              <?php echo htmlspecialchars($berita['tanggal']); ?>
              &middot; oleh <?php echo htmlspecialchars($berita['penulis']); ?>
            </span>
          </div>

          <!-- Judul -->
          <h1 class="detail-berita__title">
            <?php echo htmlspecialchars($berita['judul']); ?>
          </h1>

          <!-- Cover -->
          <img src="<?php echo htmlspecialchars($berita['cover']); ?>"
               alt="<?php echo htmlspecialchars($berita['cover_alt']); ?>"
               class="detail-berita__cover"
               loading="eager">

          <!-- Isi Konten -->
          <div class="detail-berita__content">
            <?php
            // Konten dari DB sudah HTML — gunakan strip_tags untuk keamanan jika dari input user
            // Jika konten diinput via editor (TinyMCE/Quill), pastikan sanitasi sisi server sebelum disimpan
            echo $berita['isi'];
            ?>
          </div>

          <!-- Tags -->
          <?php if (!empty($berita['tags'])) : ?>
          <div class="detail-berita__tags">
            <?php foreach ($berita['tags'] as $tag) : ?>
            <a href="/pages/publikasi/berita.php?tag=<?php echo urlencode($tag); ?>"
               class="detail-berita__tag">
              <?php echo htmlspecialchars($tag); ?>
            </a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <!-- Share -->
          <div class="detail-berita__share">
            <span>Bagikan:</span>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url_halaman); ?>"
               target="_blank" rel="noopener noreferrer"
               aria-label="Bagikan ke Facebook">
              <svg viewBox="0 0 24 24" fill="none"><path d="M14 9h3V6h-3c-2 0-3.5 1.5-3.5 3.5V11H8v3h2.5v6h3v-6H16l.5-3h-3V9.8c0-.6.3-.8.8-.8z" fill="currentColor"/></svg>
            </a>
            <a href="https://api.whatsapp.com/send?text=<?php echo $judul_encode; ?>%20<?php echo urlencode($url_halaman); ?>"
               target="_blank" rel="noopener noreferrer"
               aria-label="Bagikan ke WhatsApp">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 3a9 9 0 00-7.8 13.5L3 21l4.7-1.2A9 9 0 1012 3z" stroke="currentColor" stroke-width="1.6"/><path d="M8.5 9.5c0 3.5 3 6.5 6.5 6.5l1-2-2.5-1-1 1c-1-.5-2-1.5-2.5-2.5l1-1-1-2.5z" fill="currentColor"/></svg>
            </a>
            <button onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($url_halaman); ?>').then(()=>alert('Tautan disalin!'))"
                    aria-label="Salin tautan">
              <svg viewBox="0 0 24 24" fill="none"><path d="M9 12a3 3 0 003 3h2a3 3 0 100-6h-1m-4 6H8a3 3 0 110-6h1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            </button>
          </div>

        </article>

        <!-- ============ BERITA TERKAIT ============ -->
        <?php if (!empty($berita_terkait)) : ?>
        <div class="berita-terkait">
          <h2 class="heading-leaf" style="margin-bottom:28px;">Berita Terkait</h2>
          <div class="berita-terkait__grid">
            <?php foreach ($berita_terkait as $terkait) :
              $href_terkait = '/pages/publikasi/detail-berita.php?slug=' . urlencode($terkait['slug']);
            ?>
            <article class="card-berita">
              <a href="<?php echo htmlspecialchars($href_terkait); ?>" class="card-berita__img-wrap">
                <img src="<?php echo htmlspecialchars($terkait['img_src']); ?>"
                     alt="<?php echo htmlspecialchars($terkait['img_alt']); ?>"
                     loading="lazy">
                <span class="card-berita__category"><?php echo htmlspecialchars($terkait['kategori']); ?></span>
              </a>
              <div class="card-berita__body">
                <span class="card-berita__date font-mono"><?php echo htmlspecialchars($terkait['tanggal']); ?></span>
                <h3 class="card-berita__title"><?php echo htmlspecialchars($terkait['judul']); ?></h3>
                <a href="<?php echo htmlspecialchars($href_terkait); ?>" class="card-berita__link">Baca selengkapnya</a>
              </div>
            </article>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </section>

  </main>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/footer.php'; ?>

  <script src="/js/script.js"></script>
</body>
</html>