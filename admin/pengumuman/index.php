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
//  Data pengumuman — query DB asli
// ============================================================
if ($filter_cari !== '') {
  $stmt = mysqli_prepare($conn, "SELECT id, judul, isi, status, tanggal FROM pengumuman WHERE judul LIKE ? ORDER BY tanggal DESC");
  $like = '%' . $filter_cari . '%';
  mysqli_stmt_bind_param($stmt, 's', $like);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
} else {
  $result = mysqli_query($conn, "SELECT id, judul, isi, status, tanggal FROM pengumuman ORDER BY tanggal DESC");
}
$pengumuman_list = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// ============================================================
//  Notifikasi setelah redirect
// ============================================================
$notif = match ($_GET['saved'] ?? '') {
  '1' => ['type' => 'ok', 'pesan' => 'Pengumuman berhasil disimpan.'],
  default => null,
};
$notif ??= match ($_GET['deleted'] ?? '') {
  '1' => ['type' => 'off', 'pesan' => 'Pengumuman berhasil dihapus.'],
  default => null,
};

function format_tanggal_pengumuman(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}

require __DIR__ . '/../_nav.php';
$current_page = 'pengumuman';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengumuman — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    table.ap-table{width:100%;border-collapse:collapse;font-family:'Plus Jakarta Sans',sans-serif;}
    table.ap-table thead th{text-align:left;font-size:11.5px;letter-spacing:.05em;text-transform:uppercase;color:var(--ap-ink-muted);font-weight:600;padding:13px 16px;border-bottom:1px solid var(--ap-line);background:#FAF8F3;font-family:'IBM Plex Mono',monospace;}
    table.ap-table td{padding:12px 16px;font-size:14px;color:var(--ap-ink);border-bottom:1px solid var(--ap-line);vertical-align:middle;}
    table.ap-table tbody tr:last-child td{border-bottom:none;}
    table.ap-table tbody tr:hover{background:#FAF9F5;}
    .pg-judul{font-weight:600;}
    .pg-isi{font-size:12.5px;color:var(--ap-ink-muted);max-width:360px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .ap-badge{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:999px;}
    .ap-badge--aktif{background:var(--ap-ok-bg);color:var(--ap-ok-text);}
    .ap-badge--nonaktif{background:var(--ap-off-bg);color:var(--ap-off-text);}
    .ap-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
  </style>
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
        <h2 class="admin-topbar__title">Pengumuman</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Pengumuman</h1>
          <p class="text-muted">Kelola pengumuman yang tampil untuk warga.</p>
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
            <input type="text" name="cari" placeholder="Cari judul pengumuman..."
                   value="<?php echo htmlspecialchars($filter_cari); ?>">
          </div>
          <?php if ($filter_cari) : ?>
          <a href="/admin/pengumuman/index.php" class="btn btn--ghost" style="padding:9px 14px;font-size:13px;">✕ Reset</a>
          <?php endif; ?>
        </div>
        <button type="submit" style="display:none"></button>
        <a href="/admin/pengumuman/tambah.php" class="btn btn--primary">+ Tambah Pengumuman</a>
      </form>

      <div class="ap-card">
        <?php if (empty($pengumuman_list)) : ?>
        <div class="ap-empty">
          <svg viewBox="0 0 24 24" fill="none"><path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <h4>Belum ada pengumuman</h4>
          <p>Data yang dicari tidak ditemukan, atau belum ada pengumuman yang ditambahkan.</p>
        </div>
        <?php else : ?>
        <table class="ap-table">
          <thead>
            <tr>
              <th>Judul</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th style="text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pengumuman_list as $p) :
              $badge = $p['status'] === 'aktif' ? 'ap-badge--aktif' : 'ap-badge--nonaktif';
              $label = $p['status'] === 'aktif' ? 'Aktif' : 'Nonaktif';
            ?>
            <tr>
              <td>
                <span class="pg-judul"><?php echo htmlspecialchars($p['judul']); ?></span>
                <span class="pg-isi"><?php echo htmlspecialchars($p['isi']); ?></span>
              </td>
              <td><?php echo format_tanggal_pengumuman($p['tanggal']); ?></td>
              <td><span class="ap-badge <?php echo $badge; ?>"><?php echo $label; ?></span></td>
              <td>
                <div class="ap-actions" style="justify-content:flex-end;">
                  <a class="ap-icon-btn" href="/admin/pengumuman/edit.php?id=<?php echo $p['id']; ?>" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M16.5 3.5l4 4L8 20l-5 1 1-5z" stroke-width="1.6" stroke-linejoin="round"/></svg>
                  </a>
                  <a class="ap-icon-btn ap-icon-btn--danger"
                     href="/admin/pengumuman/hapus.php?id=<?php echo $p['id']; ?>"
                     title="Hapus"
                     onclick="return confirm('Hapus pengumuman &quot;<?php echo addslashes($p['judul']); ?>&quot;? Tindakan ini tidak dapat dibatalkan.')">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4.8c0-.4.4-.8.9-.8h4.2c.5 0 .9.4.9.8V7M6 7l1 13.2c0 .9.8 1.8 1.7 1.8h6.6c.9 0 1.7-.9 1.7-1.8L18 7" stroke-width="1.6" stroke-linecap="round"/></svg>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>

<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
