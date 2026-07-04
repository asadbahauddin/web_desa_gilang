<?php
session_start();
if (empty($_SESSION['admin'])) {
  header('Location: /admin/login.php');
  exit;
}
require_once __DIR__ . '/../../config/database.php';
require __DIR__ . '/../_nav.php';
$current_page = 'dokumen';

$kategori_label = [
  'persyaratan'  => 'Persyaratan Pelayanan',
  'kesehatan'    => 'Informasi Kesehatan',
  'pengumuman'   => 'Pengumuman',
  'dokumen-desa' => 'Dokumen Desa',
];

$filter_cari = trim($_GET['cari'] ?? '');

if ($filter_cari !== '') {
  $stmt = mysqli_prepare($conn, "SELECT id, nama, kategori, tanggal, file FROM dokumen WHERE nama LIKE ? ORDER BY tanggal DESC");
  $like = '%' . $filter_cari . '%';
  mysqli_stmt_bind_param($stmt, 's', $like);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
} else {
  $result = mysqli_query($conn, "SELECT id, nama, kategori, tanggal, file FROM dokumen ORDER BY tanggal DESC");
}
$dokumen_list = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$notif = match ($_GET['saved'] ?? '') {
  '1' => ['type' => 'ok', 'pesan' => 'Dokumen berhasil disimpan.'],
  default => null,
};
$notif ??= match ($_GET['deleted'] ?? '') {
  '1' => ['type' => 'off', 'pesan' => 'Dokumen berhasil dihapus.'],
  default => null,
};

function format_tanggal_dokumen(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Dokumen — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .notif{padding:12px 18px;border-radius:10px;font-size:13.5px;font-weight:600;margin-bottom:18px;}
    .notif--ok{background:var(--ap-ok-bg,#EAF3E9);color:var(--ap-ok-text,#2F6B3F);}
    .notif--off{background:var(--ap-off-bg,#F3E9E6);color:var(--ap-off-text,#A24A35);}
  </style>
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
          <h2 class="admin-topbar__title">Kelola Dokumen</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail"><?php echo htmlspecialchars($__admin_nama); ?></span>
          <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Daftar Dokumen</h1>
            <p class="text-muted">Kelola dokumen publik yang tampil di halaman Dokumen Publik.</p>
          </div>
          <a href="/admin/dokumen/tambah.php" class="btn btn--primary">+ Tambah Dokumen</a>
        </div>

        <?php if ($notif) : ?>
        <div class="notif notif--<?php echo $notif['type']; ?>" role="alert">
          <?php echo htmlspecialchars($notif['pesan']); ?>
        </div>
        <?php endif; ?>

        <form class="admin-toolbar" method="GET" action="">
          <div class="admin-search">
            <svg viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="5.2" stroke="currentColor" stroke-width="1.5"/><path d="M14 14l-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" name="cari" placeholder="Cari nama dokumen..." value="<?php echo htmlspecialchars($filter_cari); ?>">
          </div>
          <?php if ($filter_cari) : ?>
          <a href="/admin/dokumen/index.php" style="font-size:13px;color:var(--ap-ink-muted);text-decoration:none;">✕ Reset</a>
          <?php endif; ?>
        </form>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Nama Dokumen</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dokumen_list as $d) : ?>
              <tr>
                <td style="font-weight:600; color:var(--color-forest);"><?php echo htmlspecialchars($d['nama']); ?></td>
                <td><span class="dokumen-badge"><?php echo htmlspecialchars($kategori_label[$d['kategori']] ?? $d['kategori']); ?></span></td>
                <td class="font-mono"><?php echo format_tanggal_dokumen($d['tanggal']); ?></td>
                <td>
                  <div class="admin-table__actions">
                    <a href="/admin/dokumen/edit.php?id=<?php echo $d['id']; ?>" class="btn-icon btn-icon--edit" aria-label="Edit">
                      <svg viewBox="0 0 24 24" fill="none"><path d="M4 20h4l11-11-4-4L4 16v4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="/admin/dokumen/hapus.php?id=<?php echo $d['id']; ?>" class="btn-icon btn-icon--delete" aria-label="Hapus"
                       onclick="return confirm('Hapus dokumen &quot;<?php echo addslashes($d['nama']); ?>&quot;? Tindakan tidak dapat dibatalkan.')">
                      <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="admin-empty<?php echo empty($dokumen_list) ? ' is-visible' : ''; ?>">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            <p>Belum ada dokumen yang cocok. Coba kata kunci lain atau tambah dokumen baru.</p>
          </div>
        </div>

      </main>
    </div>
  </div>

  <?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
