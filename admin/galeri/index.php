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

// ============================================================
//  Filter dari URL
// ============================================================
$filter_cari     = trim($_GET['cari']     ?? '');
$filter_kategori = trim($_GET['kategori'] ?? '');

$kategori_label = [
  'kegiatan'      => 'Kegiatan',
  'alam'          => 'Alam',
  'infrastruktur' => 'Infrastruktur',
];

// ============================================================
//  Data galeri — query DB asli
// ============================================================
$where  = ['1=1'];
$params = [];
$types  = '';
if ($filter_cari !== '') {
  $where[]  = 'keterangan LIKE ?';
  $params[] = '%' . $filter_cari . '%';
  $types   .= 's';
}
if ($filter_kategori !== '') {
  $where[]  = 'kategori = ?';
  $params[] = $filter_kategori;
  $types   .= 's';
}
$sql = "SELECT id, foto, keterangan, kategori, created_at FROM galeri WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
  mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$galeri_list = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// ============================================================
//  Notifikasi setelah redirect
// ============================================================
$notif = match ($_GET['saved'] ?? '') {
  '1' => ['type' => 'ok', 'pesan' => 'Foto berhasil ditambahkan.'],
  default => null,
};
$notif ??= match ($_GET['deleted'] ?? '') {
  '1' => ['type' => 'off', 'pesan' => 'Foto berhasil dihapus.'],
  default => null,
};

// ============================================================
//  Nav sidebar
// ============================================================
require __DIR__ . '/../_nav.php';
$current_page = 'galeri';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Galeri — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .gl-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;padding:20px;}
    .gl-card{border:1px solid var(--ap-line);border-radius:12px;overflow:hidden;background:#fff;}
    .gl-card img{width:100%;height:150px;object-fit:cover;display:block;background:#FAF8F3;}
    .gl-card__body{padding:12px 14px;}
    .gl-card__caption{font-size:13.5px;color:var(--ap-ink);margin:0 0 6px;line-height:1.4;}
    .gl-card__meta{display:flex;align-items:center;justify-content:space-between;gap:8px;}
    .gl-badge{display:inline-flex;font-size:11.5px;font-weight:600;padding:3px 9px;border-radius:999px;background:var(--ap-ok-bg);color:var(--ap-ok-text);}
  </style>
</head>
<body data-admin data-page="galeri">
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
        <h2 class="admin-topbar__title">Galeri</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Galeri</h1>
          <p class="text-muted">Kelola foto yang tampil di halaman Galeri Desa.</p>
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
            <input type="text" name="cari" placeholder="Cari keterangan foto..."
                   value="<?php echo htmlspecialchars($filter_cari); ?>">
          </div>
          <select class="ap-select" name="kategori">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategori_label as $val => $lbl) :
              $sel = ($val === $filter_kategori) ? ' selected' : '';
            ?>
            <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $lbl; ?></option>
            <?php endforeach; ?>
          </select>
          <?php if ($filter_cari || $filter_kategori) : ?>
          <a href="/admin/galeri/index.php" class="btn btn--ghost" style="padding:9px 14px;font-size:13px;">✕ Reset</a>
          <?php endif; ?>
        </div>
        <button type="submit" style="display:none"></button>
        <a href="/admin/galeri/tambah.php" class="btn btn--primary">+ Tambah Foto</a>
      </form>

      <div class="ap-card">
        <?php if (empty($galeri_list)) : ?>
        <div class="ap-empty">
          <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.6"/><circle cx="8.5" cy="8.5" r="1.8" stroke-width="1.6"/><path d="M21 16l-5.5-5.5L9 17" stroke-width="1.6" stroke-linejoin="round"/></svg>
          <h4>Belum ada foto</h4>
          <p>Data yang dicari tidak ditemukan, atau belum ada foto yang ditambahkan.</p>
        </div>
        <?php else : ?>
        <div class="gl-grid">
          <?php foreach ($galeri_list as $g) : ?>
          <div class="gl-card">
            <img src="<?php echo htmlspecialchars($g['foto']); ?>" alt="<?php echo htmlspecialchars($g['keterangan']); ?>">
            <div class="gl-card__body">
              <p class="gl-card__caption"><?php echo htmlspecialchars($g['keterangan']); ?></p>
              <div class="gl-card__meta">
                <span class="gl-badge"><?php echo htmlspecialchars($kategori_label[$g['kategori']] ?? $g['kategori']); ?></span>
                <div class="ap-actions">
                  <a class="ap-icon-btn" href="/admin/galeri/edit.php?id=<?php echo $g['id']; ?>" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M16.5 3.5l4 4L8 20l-5 1 1-5z" stroke-width="1.6" stroke-linejoin="round"/></svg>
                  </a>
                  <a class="ap-icon-btn ap-icon-btn--danger"
                     href="/admin/galeri/hapus.php?id=<?php echo $g['id']; ?>"
                     title="Hapus"
                     onclick="return confirm('Hapus foto ini? Tindakan ini tidak dapat dibatalkan.')">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4.8c0-.4.4-.8.9-.8h4.2c.5 0 .9.4.9.8V7M6 7l1 13.2c0 .9.8 1.8 1.7 1.8h6.6c.9 0 1.7-.9 1.7-1.8L18 7" stroke-width="1.6" stroke-linecap="round"/></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>

<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
