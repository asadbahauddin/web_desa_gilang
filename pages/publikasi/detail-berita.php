<?php
// ============================================================
//  Koneksi DB
// ============================================================
require_once __DIR__ . '/../../config/database.php';

$kategori_label = [
  'kegiatan'     => 'Kegiatan',
  'ekonomi'      => 'Ekonomi',
  'pemerintahan' => 'Pemerintahan',
  'sosial'       => 'Sosial',
];

function format_tanggal_panjang(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}

// ============================================================
//  Ambil id dari URL
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
  header('Location: /pages/publikasi/berita.php');
  exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM berita WHERE id = ? AND status = 'published' LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$berita = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$berita) {
  header('HTTP/1.0 404 Not Found');
  echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Berita Tidak Ditemukan — Desa Gilang</title></head><body style="font-family:sans-serif;text-align:center;padding:80px 20px;">'
     . '<h1>404 — Berita Tidak Ditemukan</h1><p>Berita yang kamu cari tidak ada atau belum dipublikasikan.</p>'
     . '<p><a href="/pages/publikasi/berita.php">← Kembali ke Daftar Berita</a></p></body></html>';
  exit;
}

// Berita terkait: kategori sama, bukan berita ini, hanya yang published
$stmt = mysqli_prepare($conn, "SELECT id, judul, kategori, gambar, tanggal FROM berita WHERE kategori = ? AND id != ? AND status = 'published' ORDER BY tanggal DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 'si', $berita['kategori'], $id);
mysqli_stmt_execute($stmt);
$berita_terkait = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$cover = $berita['gambar'] ?: 'https://picsum.photos/seed/berita-' . $berita['id'] . '/960/540';

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

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
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
              <?php echo htmlspecialchars($kategori_label[$berita['kategori']] ?? $berita['kategori']); ?>
            </span>
            <span class="text-muted font-mono" style="font-size:0.85rem;">
              <?php echo format_tanggal_panjang($berita['tanggal']); ?>
            </span>
          </div>

          <!-- Judul -->
          <h1 class="detail-berita__title">
            <?php echo htmlspecialchars($berita['judul']); ?>
          </h1>

          <!-- Cover -->
          <img src="<?php echo htmlspecialchars($cover); ?>"
               alt="<?php echo htmlspecialchars($berita['judul']); ?>"
               class="detail-berita__cover"
               loading="eager">

          <!-- Isi Konten -->
          <div class="detail-berita__content">
            <?php echo nl2br(htmlspecialchars($berita['isi'])); ?>
          </div>

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
              $href_terkait   = '/pages/publikasi/detail-berita.php?id=' . $terkait['id'];
              $gambar_terkait = $terkait['gambar'] ?: 'https://picsum.photos/seed/berita-' . $terkait['id'] . '/480/300';
              $label_terkait  = $kategori_label[$terkait['kategori']] ?? $terkait['kategori'];
            ?>
            <article class="card-berita">
              <a href="<?php echo htmlspecialchars($href_terkait); ?>" class="card-berita__img-wrap">
                <img src="<?php echo htmlspecialchars($gambar_terkait); ?>"
                     alt="<?php echo htmlspecialchars($terkait['judul']); ?>"
                     loading="lazy">
                <span class="card-berita__category"><?php echo htmlspecialchars($label_terkait); ?></span>
              </a>
              <div class="card-berita__body">
                <span class="card-berita__date font-mono"><?php echo format_tanggal_panjang($terkait['tanggal']); ?></span>
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