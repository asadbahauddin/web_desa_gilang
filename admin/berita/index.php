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
$filter_cari = trim($_GET['cari'] ?? '');

// ============================================================
//  Kategori label
// ============================================================
$kategori_label = [
  'kegiatan'     => 'Kegiatan',
  'ekonomi'      => 'Ekonomi',
  'pemerintahan' => 'Pemerintahan',
  'sosial'       => 'Sosial',
];

// ============================================================
//  Data berita — query DB asli
// ============================================================
if ($filter_cari !== '') {
  $stmt = mysqli_prepare($conn, "SELECT id, judul, kategori, gambar, tanggal, status FROM berita WHERE judul LIKE ? ORDER BY tanggal DESC");
  $like = '%' . $filter_cari . '%';
  mysqli_stmt_bind_param($stmt, 's', $like);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
} else {
  $result = mysqli_query($conn, "SELECT id, judul, kategori, gambar, tanggal, status FROM berita ORDER BY tanggal DESC");
}
$berita_list = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// ============================================================
//  Notifikasi setelah redirect
// ============================================================
$notif = match ($_GET['saved'] ?? '') {
  '1' => ['type' => 'ok',  'pesan' => 'Berita berhasil disimpan.'],
  default => null,
};
$notif ??= match ($_GET['deleted'] ?? '') {
  '1' => ['type' => 'off', 'pesan' => 'Berita berhasil dihapus.'],
  default => null,
};

// ============================================================
//  Helper: format tanggal Indonesia
// ============================================================
function format_tanggal(string $iso): string {
  if (!$iso) return '-';
  $bulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  [$y, $m, $d] = explode('-', $iso);
  return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}

// ============================================================
//  Nav sidebar
// ============================================================
require __DIR__ . '/../_nav.php';
$current_page = 'berita';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Berita — Panel Admin Desa Gilang</title>
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
    .notif--ok{background:var(--ap-ok-bg);color:var(--ap-ok-text);}
    .notif--off{background:var(--ap-off-bg);color:var(--ap-off-text);}
    .status-pill{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:999px;}
    .status-pill--published{background:var(--ap-ok-bg);color:var(--ap-ok-text);}
    .status-pill--draft{background:#F3F0E8;color:#7A6F55;}
    .status-pill::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
  </style>
</head>
<body data-admin data-page="berita">
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
        <h2 class="admin-topbar__title">Kelola Berita</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Daftar Berita</h1>
          <p class="text-muted">Kelola semua berita yang tampil di situs publik.</p>
        </div>
        <a href="/admin/berita/tambah.php" class="btn btn--primary">+ Tambah Berita</a>
      </div>

      <!-- Notifikasi setelah redirect -->
      <?php if ($notif) : ?>
      <div class="notif notif--<?php echo $notif['type']; ?>" role="alert">
        <?php echo htmlspecialchars($notif['pesan']); ?>
      </div>
      <?php endif; ?>

      <!-- Toolbar filter -->
      <form class="admin-toolbar" method="GET" action="">
        <div class="admin-search">
          <svg viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="5.2" stroke="currentColor" stroke-width="1.5"/><path d="M14 14l-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          <input type="text" name="cari" placeholder="Cari judul berita..."
                 value="<?php echo htmlspecialchars($filter_cari); ?>"
                 oninput="this.form.submit()">
        </div>
        <?php if ($filter_cari) : ?>
          <a href="/admin/berita/index.php" style="font-size:13px;color:var(--ap-ink-muted);text-decoration:none;">✕ Reset</a>
        <?php endif; ?>
      </form>

      <!-- Tabel berita -->
      <div class="admin-table-wrap">
        <?php if (empty($berita_list)) : ?>
        <div class="admin-empty is-visible">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
          <p>Belum ada berita yang cocok. Coba kata kunci lain atau tambah berita baru.</p>
        </div>
        <?php else : ?>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Berita</th>
              <th>Kategori</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($berita_list as $b) :
              $judul   = htmlspecialchars($b['judul']);
              $gambar  = htmlspecialchars($b['gambar'] ?? '');
              $kat_lbl = htmlspecialchars($kategori_label[$b['kategori']] ?? $b['kategori']);
              $tgl     = format_tanggal($b['tanggal']);
              $pill    = $b['status'] === 'published' ? 'status-pill--published' : 'status-pill--draft';
              $lbl_st  = $b['status'] === 'published' ? 'Tayang' : 'Draft';
            ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:12px;">
                  <img src="<?php echo $gambar; ?>" alt="" class="admin-table__thumb"
                       width="80" height="60" loading="lazy">
                  <span style="font-weight:600;color:var(--color-forest);"><?php echo $judul; ?></span>
                </div>
              </td>
              <td><?php echo $kat_lbl; ?></td>
              <td class="font-mono"><?php echo $tgl; ?></td>
              <td><span class="status-pill <?php echo $pill; ?>"><?php echo $lbl_st; ?></span></td>
              <td>
                <div class="admin-table__actions">
                  <a href="/admin/berita/edit.php?id=<?php echo $b['id']; ?>"
                     class="btn-icon btn-icon--edit" aria-label="Edit">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 20h4l11-11-4-4L4 16v4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                  </a>
                  <a href="/admin/berita/hapus.php?id=<?php echo $b['id']; ?>"
                     class="btn-icon btn-icon--delete" aria-label="Hapus"
                     onclick="return confirm('Hapus berita &quot;<?php echo addslashes($b['judul']); ?>&quot;? Tindakan tidak dapat dibatalkan.')">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
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