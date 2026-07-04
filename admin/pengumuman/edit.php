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

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT id, judul, isi, status, tanggal FROM pengumuman WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$pengumuman = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$pengumuman) {
  header('Location: /admin/pengumuman/index.php');
  exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul   = trim($_POST['judul'] ?? '');
  $isi     = trim($_POST['isi'] ?? '');
  $status  = $_POST['status'] ?? 'aktif';
  $tanggal = $_POST['tanggal'] ?? '';

  if ($judul === '')   $errors[] = 'Judul pengumuman wajib diisi.';
  if ($isi === '')     $errors[] = 'Isi pengumuman wajib diisi.';
  if ($tanggal === '') $errors[] = 'Tanggal wajib diisi.';

  if (empty($errors)) {
    $stmt = mysqli_prepare($conn, "UPDATE pengumuman SET judul=?, isi=?, status=?, tanggal=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssssi', $judul, $isi, $status, $tanggal, $id);
    mysqli_stmt_execute($stmt);
    header('Location: /admin/pengumuman/index.php?saved=1');
    exit;
  }
  $pengumuman = array_merge($pengumuman, $_POST);
}

require __DIR__ . '/../_nav.php';
$current_page = 'pengumuman';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pengumuman — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
</head>
<body data-admin data-page="pengumuman">
<div class="admin-layout">

  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
  <?php require __DIR__ . '/../_sidebar.php'; ?>

  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <h2 class="admin-topbar__title">Edit Pengumuman</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Edit Pengumuman</h1>
          <p class="text-muted">Perbarui informasi pengumuman yang sudah ada.</p>
        </div>
        <a href="/admin/pengumuman/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
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

      <form method="POST" action="" style="max-width:640px;">
        <div class="admin-panel">
          <h3 class="admin-panel__title">Informasi Pengumuman</h3>

          <div class="form-group">
            <label for="judul">Judul Pengumuman</label>
            <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($pengumuman['judul']); ?>" required>
          </div>

          <div class="form-group">
            <label for="isi">Isi Pengumuman</label>
            <textarea id="isi" name="isi" required><?php echo htmlspecialchars($pengumuman['isi']); ?></textarea>
          </div>

          <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($pengumuman['tanggal']); ?>" required>
          </div>

          <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
              <?php foreach (['aktif'=>'Aktif','nonaktif'=>'Nonaktif'] as $val => $lbl) :
                $sel = ($val === $pengumuman['status']) ? ' selected' : '';
              ?>
              <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $lbl; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="admin-form__actions">
          <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
          <a href="/admin/pengumuman/index.php" class="btn btn--ghost">Batal</a>
        </div>
      </form>

    </main>
  </div>
</div>

<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
