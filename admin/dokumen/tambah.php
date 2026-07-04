<?php
session_start();
if (empty($_SESSION['admin'])) {
  header('Location: /admin/login.php');
  exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../_upload.php';

$errors = [];
$post   = $_POST ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama     = trim($_POST['nama'] ?? '');
  $kategori = $_POST['kategori'] ?? '';
  $tanggal  = $_POST['tanggal'] ?? '';

  if ($nama === '')     $errors[] = 'Nama dokumen wajib diisi.';
  if ($tanggal === '')  $errors[] = 'Tanggal dokumen wajib diisi.';
  if (empty($_FILES['file']['name'])) $errors[] = 'Berkas PDF wajib diunggah.';

  if (empty($errors)) {
    $file = upload_pdf('file');
    if (!$file) {
      $errors[] = 'Berkas harus berformat PDF dan berukuran maksimal 5 MB.';
    } else {
      $stmt = mysqli_prepare($conn, "INSERT INTO dokumen (nama, kategori, tanggal, file) VALUES (?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmt, 'ssss', $nama, $kategori, $tanggal, $file);
      mysqli_stmt_execute($stmt);
      header('Location: /admin/dokumen/index.php?saved=1');
      exit;
    }
  }
}

require __DIR__ . '/../_nav.php';
$current_page = 'dokumen';
$kategori_label = [
  'persyaratan'  => 'Persyaratan Pelayanan',
  'kesehatan'    => 'Informasi Kesehatan',
  'pengumuman'   => 'Pengumuman',
  'dokumen-desa' => 'Dokumen Desa',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Dokumen — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
</head>
<body data-admin data-page="dokumen">

  <div class="admin-layout">
    <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
    <?php require __DIR__ . '/../_sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div style="display:flex; align-items:center; gap:14px;">
          <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
            <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          </button>
          <h2 class="admin-topbar__title">Tambah Dokumen</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail"><?php echo htmlspecialchars($__admin_nama); ?></span>
          <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Tambah Dokumen Baru</h1>
            <p class="text-muted">Unggah dokumen resmi untuk ditampilkan di halaman Dokumen Publik.</p>
          </div>
          <a href="/admin/dokumen/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
        </div>

        <?php if (!empty($errors)) : ?>
        <div class="form-errors" role="alert" style="background:#F3E9E6;border:1px solid #E0BBAF;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13.5px;color:#A24A35;">
          <strong>Mohon perbaiki kesalahan berikut:</strong>
          <ul style="margin:6px 0 0 16px;padding:0;">
            <?php foreach ($errors as $err) : ?>
            <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <form id="dokumenForm" method="POST" enctype="multipart/form-data" action="" style="max-width: 640px;">
          <div class="admin-panel">
            <h3 class="admin-panel__title">Informasi Dokumen</h3>

            <div class="form-group">
              <label for="nama">Nama Dokumen</label>
              <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($post['nama'] ?? ''); ?>" placeholder="Contoh: APBDes Tahun Anggaran 2027" required>
            </div>

            <div class="form-group">
              <label for="kategori">Kategori</label>
              <select id="kategori" name="kategori" required>
                <?php foreach ($kategori_label as $val => $lbl) :
                  $sel = ($val === ($post['kategori'] ?? '')) ? ' selected' : '';
                ?>
                <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $lbl; ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="tanggal">Tanggal Dokumen</label>
              <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($post['tanggal'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
              <label>Berkas Dokumen (PDF)</label>
              <div class="upload-dropzone" id="dropzone">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7 9m5-5l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                <p><span>Klik untuk pilih file</span> atau seret PDF ke sini</p>
              </div>
              <input type="file" id="fileInput" name="file" accept="application/pdf" style="display:none;">
              <p class="text-muted" id="fileName" style="font-size:0.82rem; margin-top:8px;"></p>
            </div>
          </div>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Simpan Dokumen</button>
            <a href="/admin/dokumen/index.php" class="btn btn--ghost">Batal</a>
          </div>
        </form>

      </main>
    </div>
  </div>

  <script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const fileNameLabel = document.getElementById('fileName');

    dropzone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
      if (fileInput.files[0]) {
        fileNameLabel.textContent = `📄 ${fileInput.files[0].name}`;
      }
    });

    dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('is-dragover'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('is-dragover'));
    dropzone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropzone.classList.remove('is-dragover');
      const file = e.dataTransfer.files[0];
      if (file && file.type === 'application/pdf') {
        fileInput.files = e.dataTransfer.files;
        fileNameLabel.textContent = `📄 ${file.name}`;
      } else {
        alert('Hanya file PDF yang diizinkan.');
      }
    });
  </script>
  <?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>