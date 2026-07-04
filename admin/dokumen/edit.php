<?php
// ============================================================
//  Auth guard — pastikan hanya admin yang bisa akses
// ============================================================
session_start();
if (empty($_SESSION['admin'])) {
  header('Location: /admin/login.php');
  exit;
}

// ============================================================
//  Koneksi DB
// ============================================================
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Ambil ID dari URL
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// ============================================================
//  Data dokumen — ganti dengan query DB
//
//  Contoh query PDO:
//  $stmt = $pdo->prepare("SELECT * FROM dokumen WHERE id = ? LIMIT 1");
//  $stmt->execute([$id]);
//  $dokumen = $stmt->fetch(PDO::FETCH_ASSOC);
//  if (!$dokumen) {
//    header('Location: /admin/dokumen/index.php?error=not_found');
//    exit;
//  }
// ============================================================
$semua_dokumen = [
  ['id'=>1,'nama'=>'Syarat Pembuatan KTP',              'kategori'=>'persyaratan', 'tanggal'=>'2025-01-10','file'=>'syarat-ktp.pdf'],
  ['id'=>2,'nama'=>'Syarat Pembuatan Kartu Keluarga',   'kategori'=>'persyaratan', 'tanggal'=>'2025-01-10','file'=>'syarat-kk.pdf'],
  ['id'=>3,'nama'=>'Informasi Posyandu Balita',         'kategori'=>'kesehatan',   'tanggal'=>'2025-02-01','file'=>'posyandu-balita.pdf'],
  ['id'=>4,'nama'=>'Pengumuman Musrenbang Desa 2025',   'kategori'=>'pengumuman',  'tanggal'=>'2025-02-15','file'=>'musrenbang-2025.pdf'],
  ['id'=>5,'nama'=>'APBDes 2025',                       'kategori'=>'dokumen-desa','tanggal'=>'2025-01-05','file'=>'apbdes-2025.pdf'],
  ['id'=>6,'nama'=>'Profil Desa Gilang 2024',           'kategori'=>'dokumen-desa','tanggal'=>'2024-12-31','file'=>'profil-desa-2024.pdf'],
];

$dokumen = null;
foreach ($semua_dokumen as $item) {
  if ($item['id'] === $id) { $dokumen = $item; break; }
}

if (!$dokumen) {
  header('Location: /admin/dokumen/index.php?error=not_found');
  exit;
}

// ============================================================
//  Proses form (POST)
//
//  Contoh simpan ke DB (PDO):
//  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $errors  = [];
//    $nama    = trim($_POST['nama']     ?? '');
//    $kategori= trim($_POST['kategori'] ?? '');
//    $tanggal = trim($_POST['tanggal']  ?? '');
//
//    if (!$nama)     $errors[] = 'Nama dokumen wajib diisi.';
//    if (!$tanggal)  $errors[] = 'Tanggal dokumen wajib diisi.';
//
//    if (empty($errors)) {
//      $filePath = $dokumen['file'];
//      if (!empty($_FILES['fileInput']['tmp_name'])) {
//        // Hapus file lama jika ada
//        $filePathLama = $_SERVER['DOCUMENT_ROOT'] . '/uploads/dokumen/' . $dokumen['file'];
//        if (file_exists($filePathLama)) unlink($filePathLama);
//        // Simpan file baru
//        $namaFile = uniqid('dok_') . '.pdf';
//        $filePath = $namaFile;
//        move_uploaded_file($_FILES['fileInput']['tmp_name'],
//          $_SERVER['DOCUMENT_ROOT'] . '/uploads/dokumen/' . $namaFile);
//      }
//      $stmt = $pdo->prepare("
//        UPDATE dokumen SET nama=?, kategori=?, tanggal=?, file=? WHERE id=?
//      ");
//      $stmt->execute([$nama, $kategori, $tanggal, $filePath, $id]);
//      header('Location: /admin/dokumen/index.php?saved=1');
//      exit;
//    }
//  }
// ============================================================

$post   = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];
$errors = [];

// Nilai aktif: POST jika ada (repopulate), kalau tidak pakai data DB
$val = fn(string $key) => htmlspecialchars($post[$key] ?? $dokumen[$key] ?? '');

// ============================================================
//  Opsi dropdown kategori
// ============================================================
$opsi_kategori = [
  'persyaratan'  => 'Persyaratan Pelayanan',
  'kesehatan'    => 'Informasi Kesehatan',
  'pengumuman'   => 'Pengumuman',
  'dokumen-desa' => 'Dokumen Desa',
];

// ============================================================
//  Nav sidebar
// ============================================================
$nav_groups = [
  [
    'label' => null,
    'links' => [
      ['href'=>'/admin/dashboard.php','nav'=>'dashboard','label'=>'Dashboard',
       'icon'=>'<rect x="3" y="3" width="8" height="8" rx="1.5" stroke-width="1.6"/><rect x="13" y="3" width="8" height="5" rx="1.5" stroke-width="1.6"/><rect x="13" y="11" width="8" height="10" rx="1.5" stroke-width="1.6"/><rect x="3" y="14" width="8" height="7" rx="1.5" stroke-width="1.6"/>'],
    ],
  ],
  [
    'label' => 'Konten',
    'links' => [
      ['href'=>'/admin/berita/index.php','nav'=>'berita','label'=>'Berita',
       'icon'=>'<rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.6"/><path d="M7 9h10M7 13h10M7 17h6" stroke-width="1.6" stroke-linecap="round"/>'],
      ['href'=>'/admin/dokumen/index.php','nav'=>'dokumen','label'=>'Dokumen',
       'icon'=>'<path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke-width="1.6" stroke-linejoin="round"/>'],
    ],
  ],
  [
    'label' => 'Profil Desa',
    'links' => [
      ['href'=>'/admin/struktur-organisasi/index.php','nav'=>'struktur','label'=>'Struktur Organisasi',
       'icon'=>'<circle cx="12" cy="5" r="2.2" stroke-width="1.6"/><circle cx="6" cy="18" r="2.2" stroke-width="1.6"/><circle cx="18" cy="18" r="2.2" stroke-width="1.6"/><path d="M12 7.2V12M12 12L6 15.8M12 12l6 3.8" stroke-width="1.6" stroke-linecap="round"/>'],
      ['href'=>'/admin/aparatur/index.php','nav'=>'aparatur','label'=>'Aparatur Desa',
       'icon'=>'<circle cx="12" cy="8" r="3.4" stroke-width="1.6"/><path d="M4.5 20c1-3.8 4-5.8 7.5-5.8s6.5 2 7.5 5.8" stroke-width="1.6" stroke-linecap="round"/>'],
    ],
  ],
];

$current_page = 'dokumen';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Dokumen — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.png">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <style>
    :root{
      --sb-bg:#1F2E22; --sb-bg-soft:#25392B; --sb-bg-active:#32492F;
      --sb-accent:#C8893B; --sb-accent-soft:rgba(200,137,59,.16);
      --sb-text:#ECE7DA; --sb-text-muted:#98A691; --sb-border:rgba(255,255,255,.08);
      --sb-danger:#C9583F; --sb-width:264px;
      --ap-ink:#1F2E22; --ap-ink-muted:#6B7768; --ap-line:#E7E2D6;
      --ap-off-bg:#F3E9E6; --ap-off-text:#A24A35;
    }
    .admin-layout{display:flex;align-items:stretch;min-height:100vh;}
    .admin-sidebar{width:var(--sb-width);flex-shrink:0;background:var(--sb-bg);color:var(--sb-text);display:flex;flex-direction:column;position:sticky;top:0;height:100vh;font-family:'Plus Jakarta Sans',sans-serif;border-right:1px solid var(--sb-border);z-index:40;}
    .admin-sidebar__brand{display:flex;align-items:center;gap:12px;padding:22px 20px 18px;border-bottom:1px solid var(--sb-border);}
    .admin-sidebar__brand img{width:38px;height:38px;border-radius:10px;object-fit:cover;background:var(--sb-bg-soft);}
    .admin-sidebar__brand-text h1{font-family:'Fraunces',serif;font-size:16px;font-weight:600;line-height:1.15;margin:0;color:var(--sb-text);}
    .admin-sidebar__brand-text span{display:block;font-size:11.5px;letter-spacing:.04em;color:var(--sb-text-muted);margin-top:2px;}
    .admin-sidebar__nav{flex:1;overflow-y:auto;padding:18px 14px;}
    .admin-sidebar__group{margin-bottom:20px;}
    .admin-sidebar__group-label{font-family:'IBM Plex Mono',monospace;font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;color:var(--sb-text-muted);padding:0 10px 8px;}
    .admin-sidebar__link{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;color:var(--sb-text-muted);text-decoration:none;font-size:14px;font-weight:500;margin-bottom:3px;transition:background .15s ease,color .15s ease;position:relative;}
    .admin-sidebar__link svg{width:18px;height:18px;flex-shrink:0;stroke:currentColor;}
    .admin-sidebar__link:hover{background:var(--sb-bg-soft);color:var(--sb-text);}
    .admin-sidebar__link.is-active{background:var(--sb-bg-active);color:#fff;}
    .admin-sidebar__link.is-active::before{content:"";position:absolute;left:-14px;top:50%;transform:translateY(-50%);width:3px;height:18px;border-radius:2px;background:var(--sb-accent);}
    .admin-sidebar__footer{padding:14px;border-top:1px solid var(--sb-border);}
    .admin-sidebar__user{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;margin-bottom:6px;}
    .admin-sidebar__user-avatar{width:32px;height:32px;border-radius:50%;background:var(--sb-accent-soft);color:var(--sb-accent);display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:700;flex-shrink:0;}
    .admin-sidebar__user-email{font-size:12.5px;color:var(--sb-text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .admin-logout{display:flex;align-items:center;justify-content:space-between;width:100%;gap:10px;padding:10px 12px;border-radius:10px;border:none;background:transparent;color:var(--sb-text-muted);font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s ease,color .15s ease;}
    .admin-logout span.admin-logout__label{display:flex;align-items:center;gap:10px;}
    .admin-logout svg{width:18px;height:18px;stroke:currentColor;flex-shrink:0;}
    .admin-logout:hover{background:rgba(201,88,63,.14);color:#E18672;}
    .admin-logout.is-confirming{background:rgba(201,88,63,.14);color:#fff;}
    .admin-logout__confirm{display:none;gap:8px;}
    .admin-logout.is-confirming .admin-logout__label{display:none;}
    .admin-logout.is-confirming .admin-logout__confirm{display:flex;}
    .admin-logout__confirm button{border:none;border-radius:7px;font-size:12.5px;font-weight:700;padding:5px 10px;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;}
    .admin-logout__confirm-yes{background:var(--sb-danger);color:#fff;}
    .admin-logout__confirm-no{background:rgba(255,255,255,.1);color:var(--sb-text);}
    .admin-sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:35;}
    .admin-main{flex:1;min-width:0;}
    @media(max-width:900px){
      .admin-sidebar{position:fixed;left:0;top:0;transform:translateX(-100%);transition:transform .22s ease;box-shadow:8px 0 24px rgba(0,0,0,.25);}
      .admin-sidebar.is-open{transform:translateX(0);}
      .admin-sidebar-backdrop.is-open{display:block;}
    }
    .form-errors{background:var(--ap-off-bg);border:1px solid #E0BBAF;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13.5px;color:var(--ap-off-text);}
    .form-errors ul{margin:6px 0 0 16px;padding:0;}
    .form-errors li{margin-bottom:3px;}
    .file-existing{font-size:.82rem;color:var(--ap-ink-muted);margin-top:8px;font-family:'IBM Plex Mono',monospace;}
  </style>
</head>
<body data-admin data-page="dokumen">
<div class="admin-layout">

  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- ============ SIDEBAR ============ -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar__brand">
      <img src="/assets/logo/logo-desa.png" alt="Logo Desa Gilang">
      <div class="admin-sidebar__brand-text">
        <h1>Desa Gilang</h1><span>Panel Admin</span>
      </div>
    </div>

    <nav class="admin-sidebar__nav">
      <?php foreach ($nav_groups as $group) : ?>
      <div class="admin-sidebar__group">
        <?php if ($group['label']) : ?>
          <p class="admin-sidebar__group-label"><?php echo htmlspecialchars($group['label']); ?></p>
        <?php endif; ?>
        <?php foreach ($group['links'] as $link) :
          $active = ($link['nav'] === $current_page) ? ' is-active' : '';
        ?>
        <a href="<?php echo $link['href']; ?>" class="admin-sidebar__link<?php echo $active; ?>" data-nav="<?php echo $link['nav']; ?>">
          <svg viewBox="0 0 24 24" fill="none"><?php echo $link['icon']; ?></svg>
          <?php echo htmlspecialchars($link['label']); ?>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </nav>

    <div class="admin-sidebar__footer">
      <div class="admin-sidebar__user">
        <span class="admin-sidebar__user-avatar">AD</span>
        <span class="admin-sidebar__user-email">admin@desagilang.go.id</span>
      </div>
      <button class="admin-logout" id="logoutBtn" type="button">
        <span class="admin-logout__label">
          <svg viewBox="0 0 24 24" fill="none"><path d="M15 17l5-5-5-5M20 12H9M13 4H6a2 2 0 00-2 2v12a2 2 0 002 2h7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Keluar
        </span>
        <span class="admin-logout__confirm">
          Yakin?
          <button type="button" class="admin-logout__confirm-yes" id="logoutConfirmYes">Ya</button>
          <button type="button" class="admin-logout__confirm-no" id="logoutConfirmNo">Batal</button>
        </span>
      </button>
    </div>
  </aside>

  <!-- ============ MAIN CONTENT ============ -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <h2 class="admin-topbar__title">Edit Dokumen</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name">admin@desagilang.go.id</span>
        <span class="admin-topbar__avatar">AD</span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Edit Dokumen</h1>
          <p class="text-muted">Perbarui informasi dokumen: <strong><?php echo htmlspecialchars($dokumen['nama']); ?></strong></p>
        </div>
        <a href="/admin/dokumen/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
      </div>

      <!-- Error list -->
      <?php if (!empty($errors)) : ?>
      <div class="form-errors" role="alert">
        <strong>Mohon perbaiki kesalahan berikut:</strong>
        <ul>
          <?php foreach ($errors as $err) : ?>
          <li><?php echo htmlspecialchars($err); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- ============ FORM ============ -->
      <form id="dokumenForm" method="POST" enctype="multipart/form-data" action="" style="max-width:640px;">

        <div class="admin-panel">
          <h3 class="admin-panel__title">Informasi Dokumen</h3>

          <div class="form-group">
            <label for="nama">Nama Dokumen</label>
            <input type="text" id="nama" name="nama"
                   value="<?php echo $val('nama'); ?>" required>
          </div>

          <div class="form-group">
            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori" required>
              <?php
              $kat_aktif = $post['kategori'] ?? $dokumen['kategori'] ?? '';
              foreach ($opsi_kategori as $k => $l) :
                $sel = ($k === $kat_aktif) ? ' selected' : '';
              ?>
              <option value="<?php echo htmlspecialchars($k); ?>"<?php echo $sel; ?>>
                <?php echo htmlspecialchars($l); ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="tanggal">Tanggal Dokumen</label>
            <input type="date" id="tanggal" name="tanggal"
                   value="<?php echo $val('tanggal'); ?>" required>
          </div>

          <div class="form-group">
            <label>Berkas Dokumen (PDF)</label>

            <!-- File saat ini -->
            <?php if ($dokumen['file']) : ?>
            <p class="file-existing">
              📄 File saat ini: <strong><?php echo htmlspecialchars($dokumen['file']); ?></strong>
              — biarkan kosong jika tidak ingin mengganti.
            </p>
            <?php endif; ?>

            <div class="upload-dropzone" id="dropzone">
              <svg viewBox="0 0 24 24" fill="none">
                <path d="M12 16V4m0 0L7 9m5-5l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
              </svg>
              <p><span>Klik untuk pilih file baru</span> atau seret PDF ke sini</p>
            </div>
            <input type="file" id="fileInput" name="fileInput" accept="application/pdf" style="display:none;">
            <p class="text-muted" id="fileNameLabel" style="font-size:.82rem;margin-top:8px;"></p>
            <p class="text-muted" style="font-size:.78rem;margin-top:6px;">
              Pilih file PDF baru untuk mengganti berkas yang sudah ada.
            </p>
          </div>
        </div>

        <div class="admin-form__actions">
          <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
          <a href="/admin/dokumen/index.php" class="btn btn--ghost">Batal</a>
        </div>

      </form>
    </main>
  </div>
</div>

<script src="/js/auth.js"></script>
<script src="/js/dashboard.js"></script>
<script>
  /* Dropzone → buka file picker */
  var dropzone       = document.getElementById('dropzone');
  var fileInput      = document.getElementById('fileInput');
  var fileNameLabel  = document.getElementById('fileNameLabel');

  dropzone.addEventListener('click', function () { fileInput.click(); });

  /* Drag & drop support */
  dropzone.addEventListener('dragover', function (e) {
    e.preventDefault();
    dropzone.classList.add('is-dragover');
  });
  dropzone.addEventListener('dragleave', function () {
    dropzone.classList.remove('is-dragover');
  });
  dropzone.addEventListener('drop', function (e) {
    e.preventDefault();
    dropzone.classList.remove('is-dragover');
    var file = e.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
      fileInput.files = e.dataTransfer.files;
      fileNameLabel.textContent = '📄 ' + file.name;
    } else {
      alert('Hanya file PDF yang diizinkan.');
    }
  });

  fileInput.addEventListener('change', function () {
    if (fileInput.files[0]) {
      fileNameLabel.textContent = '📄 ' + fileInput.files[0].name;
    }
  });

  /* Sidebar toggle */
  (function () {
    var t = document.getElementById('sidebarToggle'),
        s = document.getElementById('adminSidebar'),
        b = document.getElementById('sidebarBackdrop');
    function close() { s.classList.remove('is-open'); b.classList.remove('is-open'); }
    if (t && s && b) {
      t.addEventListener('click', function () { s.classList.toggle('is-open'); b.classList.toggle('is-open'); });
      b.addEventListener('click', close);
    }
  })();

  /* Logout konfirmasi */
  (function () {
    var lb = document.getElementById('logoutBtn'),
        yb = document.getElementById('logoutConfirmYes'),
        nb = document.getElementById('logoutConfirmNo');
    if (!lb) return;
    lb.addEventListener('click', function () { lb.classList.add('is-confirming'); });
    nb.addEventListener('click', function (e) { e.stopPropagation(); lb.classList.remove('is-confirming'); });
    yb.addEventListener('click', function (e) {
      e.stopPropagation();
      try {
        localStorage.removeItem('token'); localStorage.removeItem('adminToken');
        sessionStorage.removeItem('token'); sessionStorage.removeItem('adminToken');
      } catch (err) {}
      window.location.href = '/admin/login.php';
    });
  })();
</script>
</body>
</html>