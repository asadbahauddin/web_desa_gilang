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

// ============================================================
//  Filter dari URL
// ============================================================
$filter_cari = trim($_GET['cari'] ?? '');

// ============================================================
//  Data pesan masuk — query DB asli
//
//  Pesan ini berasal dari formulir kontak di halaman publik
//  (pages/kontak/kontak.php).
// ============================================================
if ($filter_cari !== '') {
  $stmt = mysqli_prepare($conn, "SELECT id, nama, email, pesan, DATE(created_at) AS created_at FROM pesan_masuk WHERE nama LIKE ? OR email LIKE ? ORDER BY created_at DESC");
  $like = '%' . $filter_cari . '%';
  mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
} else {
  $result = mysqli_query($conn, "SELECT id, nama, email, pesan, DATE(created_at) AS created_at FROM pesan_masuk ORDER BY created_at DESC");
}
$pesan_list = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$notif = match ($_GET['deleted'] ?? '') {
  '1' => ['type' => 'off', 'pesan' => 'Pesan berhasil dihapus.'],
  default => null,
};

function format_tanggal_pesan(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}

require __DIR__ . '/../_nav.php';
$current_page = 'kontak';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesan Masuk — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .pesan-card{border:1px solid var(--ap-line);border-radius:12px;padding:16px 18px;background:#fff;margin-bottom:12px;}
    .pesan-card__head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px;}
    .pesan-nama{font-weight:600;color:var(--ap-ink);}
    .pesan-email{font-size:12.5px;color:var(--ap-ink-muted);font-family:'IBM Plex Mono',monospace;}
    .pesan-tanggal{font-size:12px;color:var(--ap-ink-muted);white-space:nowrap;}
    .pesan-isi{font-size:13.5px;color:var(--ap-ink);line-height:1.55;margin:0 0 10px;}
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
        <h2 class="admin-topbar__title">Pesan Masuk</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Pesan Masuk</h1>
          <p class="text-muted">Pesan yang dikirim warga lewat formulir kontak di halaman publik.</p>
        </div>
      </div>

      <?php if ($notif) : ?>
      <div class="ap-notif ap-notif--<?php echo $notif['type']; ?>">
        <?php echo htmlspecialchars($notif['pesan']); ?>
      </div>
      <?php endif; ?>

      <form class="ap-toolbar" method="GET" action="">
        <div class="ap-filters">
          <div class="ap-search">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke-width="1.6"/><path d="M21 21l-4.3-4.3" stroke-width="1.6" stroke-linecap="round"/></svg>
            <input type="text" name="cari" placeholder="Cari nama atau email pengirim..."
                   value="<?php echo htmlspecialchars($filter_cari); ?>">
          </div>
          <?php if ($filter_cari) : ?>
          <a href="/admin/kontak/index.php" class="btn btn--ghost" style="padding:9px 14px;font-size:13px;">✕ Reset</a>
          <?php endif; ?>
        </div>
        <button type="submit" style="display:none"></button>
      </form>

      <?php if (empty($pesan_list)) : ?>
      <div class="ap-card">
        <div class="ap-empty">
          <svg viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-width="1.6" stroke-linejoin="round"/></svg>
          <h4>Belum ada pesan masuk</h4>
          <p>Data yang dicari tidak ditemukan, atau belum ada pesan yang masuk.</p>
        </div>
      </div>
      <?php else : ?>
        <?php foreach ($pesan_list as $p) : ?>
        <div class="pesan-card">
          <div class="pesan-card__head">
            <div>
              <span class="pesan-nama"><?php echo htmlspecialchars($p['nama']); ?></span>
              — <span class="pesan-email"><?php echo htmlspecialchars($p['email']); ?></span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <span class="pesan-tanggal"><?php echo format_tanggal_pesan($p['created_at']); ?></span>
              <a class="ap-icon-btn ap-icon-btn--danger"
                 href="/admin/kontak/hapus.php?id=<?php echo $p['id']; ?>"
                 title="Hapus"
                 onclick="return confirm('Hapus pesan dari &quot;<?php echo addslashes($p['nama']); ?>&quot;?')">
                <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4.8c0-.4.4-.8.9-.8h4.2c.5 0 .9.4.9.8V7M6 7l1 13.2c0 .9.8 1.8 1.7 1.8h6.6c.9 0 1.7-.9 1.7-1.8L18 7" stroke-width="1.6" stroke-linecap="round"/></svg>
              </a>
            </div>
          </div>
          <p class="pesan-isi"><?php echo nl2br(htmlspecialchars($p['pesan'])); ?></p>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </main>
  </div>
</div>

<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
