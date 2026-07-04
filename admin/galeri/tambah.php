<?php
// ============================================================
//  Auth guard
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

$errors = [];
$post   = $_POST ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $keterangan = trim($_POST['keterangan'] ?? '');
  $kategori   = $_POST['kategori'] ?? 'kegiatan';

  if ($keterangan === '') $errors[] = 'Keterangan foto wajib diisi.';
  if (empty($_FILES['foto']['name'])) $errors[] = 'Berkas foto wajib diunggah.';

  if (empty($errors)) {
    $foto = upload_gambar('foto');
    if (!$foto) {
      $errors[] = 'Berkas harus berupa gambar (JPG/PNG/WEBP) dan berukuran maksimal 2 MB.';
    } else {
      $stmt = mysqli_prepare($conn, "INSERT INTO galeri (foto, keterangan, kategori) VALUES (?, ?, ?)");
      mysqli_stmt_bind_param($stmt, 'sss', $foto, $keterangan, $kategori);
      mysqli_stmt_execute($stmt);
      header('Location: /admin/galeri/index.php?saved=1');
      exit;
    }
  }
}

require __DIR__ . '/../_nav.php';
$current_page = 'galeri';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Foto — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
</head>
<body data-admin data-page="galeri">
<div class="admin-layout">

  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
  <?php require __DIR__ . '/../_sidebar.php'; ?>

  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <h2 class="admin-topbar__title">Tambah Foto</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Tambah Foto Baru</h1>
          <p class="text-muted">Unggah foto untuk ditampilkan di halaman Galeri Desa.</p>
        </div>
        <a href="/admin/galeri/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
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

      <form method="POST" enctype="multipart/form-data" action="" style="max-width:640px;">
        <div class="admin-panel">
          <h3 class="admin-panel__title">Informasi Foto</h3>

          <div class="form-group">
            <label for="keterangan">Keterangan Foto</label>
            <input type="text" id="keterangan" name="keterangan" value="<?php echo htmlspecialchars($post['keterangan'] ?? ''); ?>" placeholder="Contoh: Kerja bakti membersihkan saluran irigasi" required>
          </div>

          <div class="form-group">
            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori" required>
              <?php foreach (['kegiatan'=>'Kegiatan','alam'=>'Alam','infrastruktur'=>'Infrastruktur'] as $val => $lbl) :
                $sel = ($val === ($post['kategori'] ?? '')) ? ' selected' : '';
              ?>
              <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $lbl; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="foto">Berkas Foto</label>
            <input type="file" id="foto" name="foto" accept="image/*" required>
          </div>
        </div>

        <div class="admin-form__actions">
          <button type="submit" class="btn btn--primary">Simpan Foto</button>
          <a href="/admin/galeri/index.php" class="btn btn--ghost">Batal</a>
        </div>
      </form>

    </main>
  </div>
</div>

<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
