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

$stmt = mysqli_prepare($conn, "SELECT id, nama FROM pesan_masuk WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$pesan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$pesan) {
  header('Location: /admin/kontak/index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = mysqli_prepare($conn, "DELETE FROM pesan_masuk WHERE id = ?");
  mysqli_stmt_bind_param($stmt, 'i', $id);
  mysqli_stmt_execute($stmt);
  header('Location: /admin/kontak/index.php?deleted=1');
  exit;
}

require __DIR__ . '/../_nav.php';
$current_page = 'kontak';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hapus Pesan — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .danger-card{background:#fff;border:1px solid #F5C9BF;border-radius:14px;overflow:hidden;max-width:560px;}
    .danger-card__header{background:#FDF2EF;padding:24px 28px;display:flex;align-items:flex-start;gap:16px;border-bottom:1px solid #F5C9BF;}
    .danger-icon{width:44px;height:44px;border-radius:12px;background:var(--ap-off-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .danger-icon svg{width:22px;height:22px;stroke:var(--ap-off-text);}
    .danger-card__header-text h2{font-family:'Fraunces',serif;font-size:19px;font-weight:600;color:var(--ap-ink);margin:0 0 6px;}
    .danger-card__header-text p{font-size:13.5px;color:var(--ap-ink-muted);margin:0;line-height:1.55;}
    .danger-preview{padding:20px 28px;border-bottom:1px solid var(--ap-line);font-weight:600;color:var(--ap-ink);}
    .danger-card__body{padding:20px 28px 28px;}
    .danger-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:8px;}
    .btn-batal{padding:9px 20px;border-radius:9px;border:1px solid var(--ap-line);background:#fff;color:var(--ap-ink);font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s ease;}
    .btn-batal:hover{background:#F0EDE5;}
    .btn-hapus{padding:9px 22px;border-radius:9px;border:none;background:var(--sb-danger);color:#fff;font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s ease;}
    .btn-hapus:hover{background:#a8402b;}
  </style>
</head>
<body data-admin data-page="kontak">
<div class="admin-layout">

  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
  <?php require __DIR__ . '/../_sidebar.php'; ?>

  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <h2 class="admin-topbar__title">Hapus Pesan</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="danger-card">
        <div class="danger-card__header">
          <div class="danger-icon">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4.8c0-.4.4-.8.9-.8h4.2c.5 0 .9.4.9.8V7M6 7l1 13.2c0 .9.8 1.8 1.7 1.8h6.6c.9 0 1.7-.9 1.7-1.8L18 7" stroke-width="1.6" stroke-linecap="round"/></svg>
          </div>
          <div class="danger-card__header-text">
            <h2>Hapus Pesan</h2>
            <p>Tindakan ini akan menghapus pesan secara permanen dan tidak dapat dikembalikan.</p>
          </div>
        </div>

        <div class="danger-preview">Pesan dari <?php echo htmlspecialchars($pesan['nama']); ?></div>

        <div class="danger-card__body">
          <form method="POST" action="">
            <div class="danger-actions">
              <a href="/admin/kontak/index.php" class="btn-batal">Batal</a>
              <button type="submit" class="btn-hapus">Ya, Hapus Pesan</button>
            </div>
          </form>
        </div>
      </div>

    </main>
  </div>
</div>

<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
