<?php
// ============================================================
//  Koneksi DB — sesuaikan dengan config proyekmu
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Konfigurasi halaman
// ============================================================
$page = [
  'title'       => 'Kontak — Desa Gilang',
  'description' => 'Hubungi Pemerintah Desa Gilang — alamat, nomor telepon, email, dan lokasi kantor desa.',
  'favicon'     => '/assets/logo/logo-desa.jpg',
  'eyebrow'     => '',
  'heading'     => 'Kontak Kami',
  'desc'        => 'Punya pertanyaan, masukan, atau ingin berkunjung ke kantor desa? Hubungi kami melalui informasi di bawah ini.',
];

$breadcrumb = [
  ['label' => 'Beranda', 'url'    => '/index.php'],
  ['label' => 'Kontak',  'active' => true],
];

// ============================================================
//  Informasi kontak
//
//  Contoh query PDO:
//  $stmt = $pdo->query("SELECT label, nilai, ikon_key FROM kontak_desa ORDER BY urutan ASC");
//  $kontak_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ============================================================
$kontak_info = [
  [
    'label' => 'Alamat Kantor Desa',
    'nilai' => 'Jl. Raya Gilang No. 1, Kec. Contoh, Kab. Contoh, Jawa Timur 61200',
    'ikon'  => '<svg viewBox="0 0 24 24" fill="none"><path d="M12 22s7-7.4 7-12.6A7 7 0 105 9.4C5 14.6 12 22 12 22z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="1.6"/></svg>',
  ],
  [
    'label' => 'Telepon / WhatsApp',
    'nilai' => '(031) 123-4567',
    'ikon'  => '<svg viewBox="0 0 24 24" fill="none"><path d="M3 5.5C3 4 4 3 5.5 3H8l2 5-2.5 1.5a13 13 0 006 6L15 13l5 2v2.5c0 1.5-1 2.5-2.5 2.5C9.5 20 3 13.5 3 5.5z" stroke="currentColor" stroke-width="1.6"/></svg>',
  ],
  [
    'label' => 'Email Resmi',
    'nilai' => 'desagilang@contoh.go.id',
    'ikon'  => '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="1.6"/></svg>',
  ],
];

// ============================================================
//  Jam pelayanan
//
//  Contoh query PDO:
//  $stmt = $pdo->query("SELECT hari, jam FROM jam_pelayanan ORDER BY urutan ASC");
//  $jam_pelayanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ============================================================
$jam_pelayanan = [
  ['hari' => 'Senin – Kamis',        'jam' => '08.00 – 15.00'],
  ['hari' => 'Jumat',                'jam' => '08.00 – 11.30'],
  ['hari' => 'Sabtu, Minggu & Libur','jam' => 'Tutup'],
];

// ============================================================
//  Google Maps embed
// ============================================================
$maps = [
  'src'   => 'https://maps.google.com/maps?q=Balai%20Desa&t=&z=14&ie=UTF8&iwloc=&output=embed',
  'title' => 'Lokasi Kantor Desa Gilang',
];

// ============================================================
//  Form kontak
// ============================================================
$form = [
  'eyebrow' => 'Kirim Pesan',
  'heading' => 'Ada Pertanyaan atau Masukan?',
  'hint'    => 'Isi formulir di bawah ini, tim kami akan merespons melalui email yang kamu cantumkan.',
  'sukses'  => 'Pesan kamu berhasil terkirim. Terima kasih!',
];

// ============================================================
//  Proses form — sambungkan ke backend/email/DB di sini
//
//  Contoh simpan ke DB (PDO):
//  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $stmt = $pdo->prepare("INSERT INTO pesan_masuk (nama, email, pesan) VALUES (?, ?, ?)");
//    $stmt->execute([
//      trim($_POST['nama']  ?? ''),
//      trim($_POST['email'] ?? ''),
//      trim($_POST['pesan'] ?? ''),
//    ]);
//    $form_sukses = true;
//  }
// ============================================================
$form_sukses = false;
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
  <link rel="stylesheet" href="../../css/kontak.css">
</head>
<body>

  <a href="#main-content" class="skip-link">Lompat ke konten utama</a>

  <?php require_once __DIR__ . '/../../components/navbar.php'; ?>

  <main id="main-content">

    <!-- ============ PAGE HERO ============ -->
    <section class="page-hero kontak-hero">
      <div class="container">

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

        <h1 class="page-header__title"><?php echo htmlspecialchars($page['heading']); ?></h1>
        <p class="page-header__desc"><?php echo htmlspecialchars($page['desc']); ?></p>

      </div>
    </section>

    <!-- ============ INFO KONTAK + MAPS ============ -->
    <section class="section">
      <div class="container kontak-grid">

        <!-- Kolom Kiri: Info + Jam Pelayanan -->
        <div>

          <div class="kontak-info">
            <?php foreach ($kontak_info as $item) : ?>
            <div class="kontak-info__item">
              <span class="kontak-info__icon"><?php echo $item['ikon']; ?></span>
              <div>
                <span class="kontak-info__label"><?php echo htmlspecialchars($item['label']); ?></span>
                <p class="kontak-info__value"><?php echo htmlspecialchars($item['nilai']); ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="jam-pelayanan">
            <span class="jam-pelayanan__title">Jam Pelayanan</span>
            <?php foreach ($jam_pelayanan as $jam) : ?>
            <div class="jam-pelayanan__row">
              <span><?php echo htmlspecialchars($jam['hari']); ?></span>
              <span><?php echo htmlspecialchars($jam['jam']); ?></span>
            </div>
            <?php endforeach; ?>
          </div>

        </div>

        <!-- Kolom Kanan: Google Maps -->
        <div class="kontak-map">
          <iframe
            src="<?php echo htmlspecialchars($maps['src']); ?>"
            title="<?php echo htmlspecialchars($maps['title']); ?>"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen>
          </iframe>
        </div>

      </div>
    </section>

    <!-- ============ FORM KONTAK ============ -->
    <section class="section section--alt">
      <div class="container" style="max-width: 720px;">
        <div class="kontak-form">

          <span class="eyebrow"><?php echo htmlspecialchars($form['eyebrow']); ?></span>
          <h3><?php echo htmlspecialchars($form['heading']); ?></h3>
          <p class="kontak-form__hint"><?php echo htmlspecialchars($form['hint']); ?></p>

          <?php if ($form_sukses) : ?>
          <div class="form-success is-visible" role="alert">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php echo htmlspecialchars($form['sukses']); ?>
          </div>
          <?php else : ?>
          <form id="kontakForm" method="POST" action="">
            <div class="form-group">
              <label for="namaPengirim">Nama Lengkap</label>
              <input type="text" id="namaPengirim" name="nama" placeholder="Masukkan nama kamu" required>
            </div>
            <div class="form-group">
              <label for="emailPengirim">Email</label>
              <input type="email" id="emailPengirim" name="email" placeholder="nama@email.com" required>
            </div>
            <div class="form-group">
              <label for="pesanPengirim">Pesan</label>
              <textarea id="pesanPengirim" name="pesan" placeholder="Tulis pertanyaan atau masukan kamu di sini..." required></textarea>
            </div>
            <button type="submit" class="btn btn--primary">
              <?php echo htmlspecialchars($form['eyebrow']); ?>
            </button>
          </form>
          <?php endif; ?>

        </div>
      </div>
    </section>

  </main>

  <?php require_once __DIR__ . '/../../components/footer.php'; ?>

  <script src="../../js/navbar.js"></script>
  <script src="../../js/script.js"></script>
</body>
</html>