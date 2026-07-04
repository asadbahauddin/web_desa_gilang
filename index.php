
<?php
require_once __DIR__ . '/config/database.php';

// Konfigurasi umum
$page_title = "Desa Gilang — Situs Resmi Pemerintah Desa";
$page_desc  = "Situs resmi Desa Gilang: profil desa, berita, dokumen publik, dan informasi layanan masyarakat.";

$kategori_berita_label = [
  'kegiatan'     => 'Kegiatan',
  'ekonomi'      => 'Ekonomi',
  'pemerintahan' => 'Pemerintahan',
  'sosial'       => 'Sosial',
];
$kategori_dokumen_label = [
  'persyaratan'  => 'Persyaratan Pelayanan',
  'kesehatan'    => 'Informasi Kesehatan',
  'pengumuman'   => 'Pengumuman',
  'dokumen-desa' => 'Dokumen Desa',
];

function format_tanggal_id(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($page_desc); ?>">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="assets/logo/logo-desa.jpg">

  <!-- CSS -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/navbar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/home.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <!-- NAVBAR -->
  <?php require_once __DIR__ . '/components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ HERO ============ -->
    <section class="hero">
      <img class="hero__poster"
           src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=1600&q=80&auto=format&fit=crop"
           alt="Sawah dan pemandangan pedesaan">
      <div class="hero__overlay"></div>

      <div class="container hero__content">
        <span class="hero__eyebrow">Selamat Datang di</span>
        <h1 class="hero__title">Desa Gilang, <em>Tumbuh</em> Bersama Alam</h1>
        <p class="hero__desc">
          Desa yang menjaga keseimbangan antara tradisi, gotong royong, dan kelestarian alam — menyediakan layanan publik yang terbuka dan mudah diakses oleh seluruh warga.
        </p>
        <div class="hero__actions">
          <a href="pages/profil/sejarah-desa.php" class="btn btn--primary">Lihat Profil Desa</a>
          <a href="pages/publikasi/dokumen.php" class="btn btn--outline">Dokumen Publik</a>
        </div>
      </div>

      <div class="hero__scroll-cue">
        <span>GULIR KE BAWAH</span>
        <svg viewBox="0 0 16 16" fill="none"><path d="M2 5l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
    </section>

    <!-- ============ SAMBUTAN KEPALA DESA ============ -->
    <?php
    // Data kepala desa — bisa diganti dari DB nanti
    $kades = [
      'nama'          => 'Bapak Syamsul Arifin.',
      'jabatan'       => 'Kepala Desa Gilang',
      'tahun_mengabdi'=> 9,
      'foto'          => 'logo/kades-gilang.png',
      'sambutan'      => 'Selamat datang di rumah digital Desa Gilang. Situs ini kami bangun agar warga dapat mengakses informasi, dokumen, dan berita desa secara terbuka — sejalan dengan komitmen kami untuk pemerintahan yang transparan dan dekat dengan masyarakat.',
    ];
    ?>
    <section class="section sambutan">
      <div class="container sambutan__grid">
        <div class="sambutan__photo-wrap">
          <img
            src="<?php echo htmlspecialchars($kades['foto']); ?>"
            alt="Foto Kepala Desa Gilang"
            class="sambutan__photo">
          <div class="sambutan__badge">
            <strong><?php echo (int) $kades['tahun_mengabdi']; ?></strong>
            <span>Tahun Mengabdi Untuk Desa</span>
          </div>
        </div>

        <div>
          <span class="eyebrow">Sambutan</span>
          <h2 class="heading-leaf" style="margin-bottom: 24px;">Kata Sambutan<br>Kepala Desa</h2>

          <svg class="sambutan__quote-icon" viewBox="0 0 38 30" fill="none">
            <path d="M16 0C7 2 0 10 0 19c0 6 4 11 10 11 5 0 9-4 9-9s-4-8-8-8c-1 0-2 0-3 1C9 9 13 4 19 2L16 0z" fill="currentColor"/>
            <path d="M35 0c-9 2-16 10-16 19 0 6 4 11 10 11 5 0 9-4 9-9s-4-8-8-8c-1 0-2 0-3 1C28 9 32 4 38 2L35 0z" fill="currentColor"/>
          </svg>

          <p class="sambutan__text">
            "<?php echo htmlspecialchars($kades['sambutan']); ?>"
          </p>

          <div class="sambutan__signature">
            <div class="sambutan__signature-line"></div>
            <div>
              <p class="sambutan__name"><?php echo htmlspecialchars($kades['nama']); ?></p>
              <p class="sambutan__role"><?php echo htmlspecialchars($kades['jabatan']); ?></p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ============ BERITA TERBARU ============ -->
    <?php
    $result      = mysqli_query($conn, "SELECT id, judul, excerpt, kategori, gambar, tanggal FROM berita WHERE status = 'published' ORDER BY tanggal DESC LIMIT 3");
    $berita_list = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    ?>
    <section class="section" id="berita-terbaru">
      <div class="container">
        <div class="section__header">
          <div class="section__header-text">
            <span class="eyebrow">Publikasi</span>
            <h2 class="heading-leaf">Berita Terbaru</h2>
          </div>
          <a href="pages/publikasi/berita.php" class="btn btn--ghost">Semua Berita</a>
        </div>

        <?php if (empty($berita_list)) : ?>
        <p class="text-muted">Belum ada berita yang dipublikasikan.</p>
        <?php else : ?>
        <div class="berita-grid">
          <?php foreach ($berita_list as $berita) :
            $href   = 'pages/publikasi/detail-berita.php?id=' . $berita['id'];
            $gambar = $berita['gambar'] ?: 'https://picsum.photos/seed/berita-' . $berita['id'] . '/480/300';
            $label  = $kategori_berita_label[$berita['kategori']] ?? $berita['kategori'];
          ?>
          <article class="card-berita">
            <a href="<?php echo htmlspecialchars($href); ?>" class="card-berita__img-wrap">
              <img src="<?php echo htmlspecialchars($gambar); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
              <span class="card-berita__category"><?php echo htmlspecialchars($label); ?></span>
            </a>
            <div class="card-berita__body">
              <span class="card-berita__date font-mono"><?php echo format_tanggal_id($berita['tanggal']); ?></span>
              <h3 class="card-berita__title"><?php echo htmlspecialchars($berita['judul']); ?></h3>
              <p class="card-berita__excerpt"><?php echo htmlspecialchars($berita['excerpt']); ?></p>
              <a href="<?php echo htmlspecialchars($href); ?>" class="card-berita__link">
                Baca selengkapnya
                <svg viewBox="0 0 16 16" fill="none"><path d="M2 8h12M9 3l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- ============ DOKUMEN & INFORMASI PUBLIK ============ -->
    <?php
    $result       = mysqli_query($conn, "SELECT id, nama, kategori, tanggal, file FROM dokumen ORDER BY tanggal DESC LIMIT 4");
    $dokumen_list = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    ?>
    <section class="section section--alt" id="dokumen-publik">
      <div class="container">
        <div class="section__header">
          <div class="section__header-text">
            <span class="eyebrow">Transparansi</span>
            <h2 class="heading-leaf">Dokumen &amp; Informasi Publik</h2>
          </div>
        </div>

        <?php if (empty($dokumen_list)) : ?>
        <p class="text-muted">Belum ada dokumen yang tersedia.</p>
        <?php else : ?>
        <div class="dokumen-table-wrap">
          <table class="dokumen-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dokumen_list as $i => $dok) : ?>
              <tr>
                <td class="font-mono"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($dok['nama']); ?></td>
                <td><span class="dokumen-badge"><?php echo htmlspecialchars($kategori_dokumen_label[$dok['kategori']] ?? $dok['kategori']); ?></span></td>
                <td class="font-mono"><?php echo format_tanggal_id($dok['tanggal']); ?></td>
                <td>
                  <a href="<?php echo htmlspecialchars($dok['file']); ?>" class="dokumen-download" download target="_blank" rel="noopener noreferrer">
                    <svg viewBox="0 0 16 16" fill="none"><path d="M8 2v8m0 0L5 7m3 3l3-3M3 13h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Unduh
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>

        <div class="dokumen-footer">
          <a href="/pages/publikasi/dokumen.php" class="btn btn--ghost">Lihat Semua Dokumen</a>
        </div>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <?php require_once __DIR__ . '/components/footer.php'; ?>

  <script src="js/script.js"></script>
</body>
</html>