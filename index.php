
<?php
// Konfigurasi umum
$page_title = "Desa Gilang — Situs Resmi Pemerintah Desa";
$page_desc  = "Situs resmi Desa Gilang: profil desa, berita, dokumen publik, galeri, dan informasi layanan masyarakat.";
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
    // Data berita — idealnya diambil dari DB
    $berita_list = [
      [
        'href'     => 'pages/publikasi/detail-berita.php',
        'img_src'  => 'https://picsum.photos/seed/berita-gotong-royong/480/300',
        'img_alt'  => 'Gotong royong bersih desa',
        'category' => 'Kegiatan',
        'date'     => '12 Jun 2026',
        'title'    => 'Gotong Royong Bersih Desa Sambut Musim Tanam',
        'excerpt'  => 'Warga Desa Gilang bersama perangkat desa melaksanakan kerja bakti membersihkan saluran irigasi menjelang musim tanam.',
      ],
      [
        'href'     => 'pages/publikasi/detail-berita.php',
        'img_src'  => 'https://picsum.photos/seed/berita-umkm-desa/480/300',
        'img_alt'  => 'Pelatihan UMKM desa',
        'category' => 'Ekonomi',
        'date'     => '08 Jun 2026',
        'title'    => 'Pelatihan UMKM: Olahan Hasil Panen Jadi Produk Bernilai',
        'excerpt'  => 'Dinas Koperasi bekerja sama dengan pemerintah desa menggelar pelatihan pengolahan hasil panen bagi pelaku UMKM lokal.',
      ],
      [
        'href'     => 'pages/publikasi/detail-berita.php',
        'img_src'  => 'https://picsum.photos/seed/musyawarah-desa/480/300',
        'img_alt'  => 'Musyawarah desa',
        'category' => 'Pemerintahan',
        'date'     => '02 Jun 2026',
        'title'    => 'Musyawarah Desa Bahas Rencana Anggaran 2027',
        'excerpt'  => 'Musyawarah desa digelar untuk membahas rancangan APBDes tahun 2027 bersama BPD dan tokoh masyarakat.',
      ],
    ];
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

        <div class="berita-grid">
          <?php foreach ($berita_list as $berita) : ?>
          <article class="card-berita">
            <a href="<?php echo htmlspecialchars($berita['href']); ?>" class="card-berita__img-wrap">
              <img src="<?php echo htmlspecialchars($berita['img_src']); ?>" alt="<?php echo htmlspecialchars($berita['img_alt']); ?>">
              <span class="card-berita__category"><?php echo htmlspecialchars($berita['category']); ?></span>
            </a>
            <div class="card-berita__body">
              <span class="card-berita__date font-mono"><?php echo htmlspecialchars($berita['date']); ?></span>
              <h3 class="card-berita__title"><?php echo htmlspecialchars($berita['title']); ?></h3>
              <p class="card-berita__excerpt"><?php echo htmlspecialchars($berita['excerpt']); ?></p>
              <a href="<?php echo htmlspecialchars($berita['href']); ?>" class="card-berita__link">
                Baca selengkapnya
                <svg viewBox="0 0 16 16" fill="none"><path d="M2 8h12M9 3l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ============ DOKUMEN & INFORMASI PUBLIK ============ -->
    <?php
    // Data dokumen — idealnya diambil dari DB
    $dokumen_list = [
      ['no' => '01', 'nama' => 'Syarat Pengajuan KTP Baru',             'kategori' => 'Persyaratan Pelayanan', 'tanggal' => '10 Jan 2026', 'file' => '#'],
      ['no' => '02', 'nama' => 'Jadwal Posyandu Bulan Juni 2026',        'kategori' => 'Informasi Kesehatan',   'tanggal' => '01 Jun 2026', 'file' => '#'],
      ['no' => '03', 'nama' => 'Pengumuman Penerimaan Bantuan Sosial',   'kategori' => 'Pengumuman',            'tanggal' => '10 Jun 2026', 'file' => '#'],
      ['no' => '04', 'nama' => 'APBDes Tahun Anggaran 2026',             'kategori' => 'Dokumen Desa',          'tanggal' => '15 Jan 2026', 'file' => '#'],
    ];
    ?>
    <section class="section section--alt" id="dokumen-publik">
      <div class="container">
        <div class="section__header">
          <div class="section__header-text">
            <span class="eyebrow">Transparansi</span>
            <h2 class="heading-leaf">Dokumen &amp; Informasi Publik</h2>
          </div>
        </div>

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
              <?php foreach ($dokumen_list as $dok) : ?>
              <tr>
                <td class="font-mono"><?php echo htmlspecialchars($dok['no']); ?></td>
                <td><?php echo htmlspecialchars($dok['nama']); ?></td>
                <td><span class="dokumen-badge"><?php echo htmlspecialchars($dok['kategori']); ?></span></td>
                <td class="font-mono"><?php echo htmlspecialchars($dok['tanggal']); ?></td>
                <td>
                  <a href="<?php echo htmlspecialchars($dok['file']); ?>" class="dokumen-download">
                    <svg viewBox="0 0 16 16" fill="none"><path d="M8 2v8m0 0L5 7m3 3l3-3M3 13h10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Unduh
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="dokumen-footer">
          <a href="/pages/publikasi/dokumen.php" class="btn btn--ghost">Lihat Semua Dokumen</a>
        </div>
      </div>
    </section>

    <!-- ============ GALERI ============ -->
    <?php
    // Data galeri — bisa diganti dari DB
    $galeri_list = [
      ['seed' => 'galeri-desa-1', 'size' => '600/600', 'alt' => 'Dokumentasi kegiatan desa 1'],
      ['seed' => 'galeri-desa-2', 'size' => '300/300', 'alt' => 'Dokumentasi kegiatan desa 2'],
      ['seed' => 'galeri-desa-3', 'size' => '300/300', 'alt' => 'Dokumentasi kegiatan desa 3'],
      ['seed' => 'galeri-desa-4', 'size' => '300/300', 'alt' => 'Dokumentasi kegiatan desa 4'],
      ['seed' => 'galeri-desa-5', 'size' => '300/300', 'alt' => 'Dokumentasi kegiatan desa 5'],
    ];
    ?>
    <section class="section" id="galeri">
      <div class="container">
        <div class="section__header">
          <div class="section__header-text">
            <span class="eyebrow">Dokumentasi</span>
            <h2 class="heading-leaf">Galeri Desa</h2>
          </div>
        </div>

        <div class="galeri-grid">
          <?php foreach ($galeri_list as $item) : ?>
          <a href="/pages/galeri/galeri.php" class="galeri-item">
            <img
              src="https://picsum.photos/seed/<?php echo htmlspecialchars($item['seed']); ?>/<?php echo $item['size']; ?>"
              alt="<?php echo htmlspecialchars($item['alt']); ?>">
          </a>
          <?php endforeach; ?>
        </div>

        <div class="galeri-footer">
          <a href="/pages/galeri/galeri.php" class="btn btn--ghost">Lihat Semua Galeri</a>
        </div>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <?php require_once __DIR__ . '/components/footer.php'; ?>

  <script src="js/script.js"></script>
</body>
</html>