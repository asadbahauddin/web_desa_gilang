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
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../_upload.php';

// ============================================================
//  Ambil ID dari URL
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM dokumen WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$dokumen = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$dokumen) {
  header('Location: /admin/dokumen/index.php?error=not_found');
  exit;
}

$post   = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];
$errors = [];

// ============================================================
//  Proses form (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama     = trim($_POST['nama']     ?? '');
  $kategori = trim($_POST['kategori'] ?? '');
  $tanggal  = trim($_POST['tanggal']  ?? '');

  if (!$nama)    $errors[] = 'Nama dokumen wajib diisi.';
  if (!$tanggal) $errors[] = 'Tanggal dokumen wajib diisi.';

  if (empty($errors)) {
    $filePath = $dokumen['file'];
    if (!empty($_FILES['fileInput']['name'])) {
      $fileBaru = upload_pdf('fileInput');
      if ($fileBaru) {
        hapus_file_upload($filePath);
        $filePath = $fileBaru;
      } else {
        $errors[] = 'Berkas harus berformat PDF dan berukuran maksimal 5 MB.';
      }
    }

    if (empty($errors)) {
      $stmt = mysqli_prepare($conn, "UPDATE dokumen SET nama=?, kategori=?, tanggal=?, file=? WHERE id=?");
      mysqli_stmt_bind_param($stmt, 'ssssi', $nama, $kategori, $tanggal, $filePath, $id);
      mysqli_stmt_execute($stmt);
      header('Location: /admin/dokumen/index.php?saved=1');
      exit;
    }
  }
}

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
require __DIR__ . '/../_nav.php';
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

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .form-errors{background:var(--ap-off-bg);border:1px solid #E0BBAF;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13.5px;color:var(--ap-off-text);}
    .form-errors ul{margin:6px 0 0 16px;padding:0;}
    .form-errors li{margin-bottom:3px;}
    .file-existing{font-size:.82rem;color:var(--ap-ink-muted);margin-top:8px;font-family:'IBM Plex Mono',monospace;}
  </style>
</head>
<body data-admin data-page="dokumen">
<div class="admin-layout">

  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
  <?php require __DIR__ . '/../_sidebar.php'; ?>

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
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
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

</script>
<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>