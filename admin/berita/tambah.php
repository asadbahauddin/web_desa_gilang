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
  $judul    = trim($_POST['judul'] ?? '');
  $excerpt  = trim($_POST['excerpt'] ?? '');
  $konten   = trim($_POST['konten'] ?? '');
  $kategori = $_POST['kategori'] ?? '';
  $tanggal  = $_POST['tanggal'] ?? '';
  $status   = $_POST['status'] ?? 'draft';

  if ($judul === '')   $errors[] = 'Judul berita wajib diisi.';
  if ($konten === '')  $errors[] = 'Isi berita wajib diisi.';
  if ($tanggal === '') $errors[] = 'Tanggal terbit wajib diisi.';

  if (empty($errors)) {
    $gambar = upload_gambar('gambar') ?? '';
    $stmt = mysqli_prepare($conn, "INSERT INTO berita (judul, excerpt, isi, kategori, gambar, tanggal, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sssssss', $judul, $excerpt, $konten, $kategori, $gambar, $tanggal, $status);
    mysqli_stmt_execute($stmt);
    header('Location: /admin/berita/index.php?saved=1');
    exit;
  }
}

require __DIR__ . '/../_nav.php';
$current_page = 'berita';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Berita — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
</head>
<body data-admin data-page="berita">

  <div class="admin-layout">
    <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
    <?php require __DIR__ . '/../_sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div style="display:flex; align-items:center; gap:14px;">
          <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
            <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          </button>
          <h2 class="admin-topbar__title">Tambah Berita</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail"><?php echo htmlspecialchars($__admin_nama); ?></span>
          <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Tambah Berita</h1>
            <p class="text-muted">Buat dan publikasikan berita baru untuk situs desa.</p>
          </div>
          <a href="/admin/berita/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
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

        <form id="beritaForm" method="POST" enctype="multipart/form-data" action="">
          <div class="admin-form">

            <div class="admin-panel">
              <h3 class="admin-panel__title">Informasi Berita</h3>

              <div class="form-group">
                <label for="judul">Judul Berita</label>
                <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($post['judul'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="excerpt">Ringkasan Singkat</label>
                <input type="text" id="excerpt" name="excerpt" value="<?php echo htmlspecialchars($post['excerpt'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="konten">Isi Berita</label>
                <textarea id="konten" name="konten" required><?php echo htmlspecialchars($post['konten'] ?? ''); ?></textarea>
              </div>
            </div>

            <div>
              <div class="admin-panel">
                <h3 class="admin-panel__title">Publikasi</h3>

                <div class="form-group">
                  <label for="kategori">Kategori</label>
                  <select id="kategori" name="kategori" required>
                    <?php foreach (['kegiatan'=>'Kegiatan','ekonomi'=>'Ekonomi','pemerintahan'=>'Pemerintahan','sosial'=>'Sosial'] as $val => $lbl) :
                      $sel = ($val === ($post['kategori'] ?? '')) ? ' selected' : '';
                    ?>
                    <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $lbl; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="tanggal">Tanggal Terbit</label>
                  <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($post['tanggal'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                  <label for="status">Status</label>
                  <select id="status" name="status" required>
                    <?php foreach (['published'=>'Tayang','draft'=>'Draft'] as $val => $lbl) :
                      $sel = ($val === ($post['status'] ?? '')) ? ' selected' : '';
                    ?>
                    <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $lbl; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="admin-panel">
                <h3 class="admin-panel__title">Gambar Sampul</h3>
                <div class="form-group">
                  <label>Berkas Gambar</label>
                  <div class="upload-dropzone" id="dropzoneGambar">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7 9m5-5l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <p><span>Klik untuk pilih gambar</span> atau seret ke sini</p>
                  </div>
                  <input type="file" id="gambar" name="gambar" accept="image/*" style="display:none;">
                  <p class="text-muted" id="gambarFileName" style="font-size:0.82rem; margin-top:8px;"></p>
                </div>
                <div class="upload-preview" id="imgPreview">
                  <img src="" alt="Pratinjau gambar">
                </div>
              </div>
            </div>

          </div>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Simpan Berita</button>
            <a href="/admin/berita/index.php" class="btn btn--ghost">Batal</a>
          </div>
        </form>

      </main>
    </div>
  </div>

  <script>
    const gambarInput = document.getElementById('gambar');
    const dropzoneGambar = document.getElementById('dropzoneGambar');
    const gambarFileName = document.getElementById('gambarFileName');
    const preview = document.getElementById('imgPreview');
    const previewImg = preview.querySelector('img');

    function tampilkanGambar(file) {
      if (!file || !file.type.startsWith('image/')) {
        alert('Hanya file gambar yang diizinkan.');
        return;
      }
      gambarFileName.textContent = '🖼 ' + file.name;
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        preview.classList.add('is-visible');
      };
      reader.readAsDataURL(file);
    }

    dropzoneGambar.addEventListener('click', () => gambarInput.click());
    gambarInput.addEventListener('change', () => tampilkanGambar(gambarInput.files[0]));

    dropzoneGambar.addEventListener('dragover', (e) => { e.preventDefault(); dropzoneGambar.classList.add('is-dragover'); });
    dropzoneGambar.addEventListener('dragleave', () => dropzoneGambar.classList.remove('is-dragover'));
    dropzoneGambar.addEventListener('drop', (e) => {
      e.preventDefault();
      dropzoneGambar.classList.remove('is-dragover');
      const file = e.dataTransfer.files[0];
      if (file) { gambarInput.files = e.dataTransfer.files; tampilkanGambar(file); }
    });
  </script>
  <?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
