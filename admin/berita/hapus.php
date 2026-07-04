<?php
session_start();
if (empty($_SESSION['admin'])) {
  header('Location: /admin/login.php');
  exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../_upload.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM berita WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$berita = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$berita) {
  header('Location: /admin/berita/index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = mysqli_prepare($conn, "DELETE FROM berita WHERE id = ?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  hapus_file_upload($berita['gambar']);
  header('Location: /admin/berita/index.php?deleted=1');
  exit;
}

require __DIR__ . '/../_nav.php';
$current_page = 'berita';
$kategori_label = [
  'kegiatan'     => 'Kegiatan',
  'ekonomi'      => 'Ekonomi',
  'pemerintahan' => 'Pemerintahan',
  'sosial'       => 'Sosial',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hapus Berita — Panel Admin Desa Gilang</title>
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
          <h2 class="admin-topbar__title">Hapus Berita</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail"><?php echo htmlspecialchars($__admin_nama); ?></span>
          <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Hapus Berita</h1>
            <p class="text-muted">Periksa kembali sebelum menghapus berita secara permanen.</p>
          </div>
          <a href="/admin/berita/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
        </div>

        <form method="POST" action="">
          <div class="admin-form">
            <div class="admin-panel">
              <h3 class="admin-panel__title">Detail Berita</h3>

              <?php if ($berita['gambar']) : ?>
              <div class="upload-preview is-visible" style="margin-bottom:16px;">
                <img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="Pratinjau gambar">
              </div>
              <?php endif; ?>

              <div class="form-group">
                <label>Judul Berita</label>
                <p style="font-weight:600;"><?php echo htmlspecialchars($berita['judul']); ?></p>
              </div>

              <div class="form-group">
                <label>Ringkasan Singkat</label>
                <p><?php echo htmlspecialchars($berita['excerpt']); ?></p>
              </div>

              <div class="form-group">
                <label>Kategori</label>
                <p><?php echo htmlspecialchars($kategori_label[$berita['kategori']] ?? $berita['kategori']); ?></p>
              </div>

              <div class="form-group">
                <label>Tanggal Terbit</label>
                <p><?php echo htmlspecialchars($berita['tanggal']); ?></p>
              </div>

              <div class="form-group">
                <label>Status</label>
                <p><?php echo $berita['status'] === 'published' ? 'Tayang' : 'Draft'; ?></p>
              </div>

              <div class="admin-alert admin-alert--danger" style="margin-top:8px;">
                ⚠️ Tindakan ini tidak dapat dibatalkan. Berita yang sudah dihapus tidak bisa dikembalikan.
              </div>
            </div>
          </div>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--danger">Ya, Hapus Berita</button>
            <a href="/admin/berita/index.php" class="btn btn--ghost">Batal</a>
          </div>
        </form>

      </main>
    </div>
  </div>

  <?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
