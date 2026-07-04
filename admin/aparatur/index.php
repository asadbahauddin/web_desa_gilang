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
$filter_cari    = trim($_GET['cari']    ?? '');
$filter_jabatan = trim($_GET['jabatan'] ?? '');
$filter_status  = trim($_GET['status']  ?? '');

// ============================================================
//  Data aparatur — query DB asli
// ============================================================
$where  = ['1=1'];
$params = [];
$types  = '';
if ($filter_cari !== '') {
  $where[]  = '(nama LIKE ? OR nip LIKE ?)';
  $like     = '%' . $filter_cari . '%';
  $params[] = $like;
  $params[] = $like;
  $types   .= 'ss';
}
if ($filter_jabatan !== '') {
  $where[]  = 'jabatan = ?';
  $params[] = $filter_jabatan;
  $types   .= 's';
}
if ($filter_status !== '') {
  $where[]  = 'status = ?';
  $params[] = $filter_status;
  $types   .= 's';
}
$sql = "SELECT id, nama, nip, jabatan, foto, status FROM aparatur WHERE " . implode(' AND ', $where) . " ORDER BY jabatan ASC, nama ASC";
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
  mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$aparatur_list = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Daftar jabatan unik untuk dropdown filter
$result_jabatan = mysqli_query($conn, "SELECT DISTINCT jabatan FROM aparatur ORDER BY jabatan ASC");
$semua_jabatan  = mysqli_fetch_all($result_jabatan, MYSQLI_ASSOC);
$semua_jabatan  = array_column($semua_jabatan, 'jabatan');

// ============================================================
//  Notifikasi setelah aksi (redirect with flag)
// ============================================================
$notif = match ($_GET['saved'] ?? '') {
  '1'  => ['type' => 'ok',  'pesan' => 'Data aparatur berhasil disimpan.'],
  default => null,
};
$notif ??= match ($_GET['deleted'] ?? '') {
  '1'  => ['type' => 'off', 'pesan' => 'Data aparatur berhasil dihapus.'],
  default => null,
};

// ============================================================
//  Helper
// ============================================================
function inisial(string $nama): string {
  $kata = array_filter(explode(' ', $nama));
  return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($kata, 0, 2))));
}

require __DIR__ . '/../_nav.php';
$current_page = 'aparatur';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aparatur Desa — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .ap-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px;}
    .ap-filters{display:flex;gap:10px;flex-wrap:wrap;flex:1;}
    .ap-search{position:relative;max-width:280px;flex:1;min-width:200px;}
    .ap-search input{width:100%;padding:9px 12px 9px 36px;border-radius:9px;border:1px solid var(--ap-line);font-family:'Plus Jakarta Sans',sans-serif;font-size:13.5px;color:var(--ap-ink);background:#fff;}
    .ap-search svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:16px;height:16px;stroke:var(--ap-ink-muted);}
    .ap-select{padding:9px 12px;border-radius:9px;border:1px solid var(--ap-line);font-family:'Plus Jakarta Sans',sans-serif;font-size:13.5px;color:var(--ap-ink);background:#fff;}
    .ap-card{background:var(--ap-card);border:1px solid var(--ap-line);border-radius:14px;overflow:hidden;}
    table.ap-table{width:100%;border-collapse:collapse;font-family:'Plus Jakarta Sans',sans-serif;}
    table.ap-table thead th{text-align:left;font-size:11.5px;letter-spacing:.05em;text-transform:uppercase;color:var(--ap-ink-muted);font-weight:600;padding:13px 16px;border-bottom:1px solid var(--ap-line);background:#FAF8F3;font-family:'IBM Plex Mono',monospace;}
    table.ap-table td{padding:12px 16px;font-size:14px;color:var(--ap-ink);border-bottom:1px solid var(--ap-line);vertical-align:middle;}
    table.ap-table tbody tr:last-child td{border-bottom:none;}
    table.ap-table tbody tr:hover{background:#FAF9F5;}
    .ap-foto{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--ap-line);background:#FAF8F3;display:block;}
    .ap-foto-placeholder{width:44px;height:44px;border-radius:50%;background:var(--sb-accent-soft);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#9A5A1F;flex-shrink:0;}
    .ap-person{display:flex;align-items:center;gap:10px;}
    .ap-person-name{font-weight:600;}
    .ap-person-nip{font-size:12px;color:var(--ap-ink-muted);font-family:'IBM Plex Mono',monospace;}
    .ap-badge{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:999px;}
    .ap-badge--aktif{background:var(--ap-ok-bg);color:var(--ap-ok-text);}
    .ap-badge--nonaktif{background:var(--ap-off-bg);color:var(--ap-off-text);}
    .ap-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
    .ap-actions{display:flex;gap:6px;}
    .ap-icon-btn{width:32px;height:32px;border-radius:8px;border:1px solid var(--ap-line);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s ease,border-color .15s ease;text-decoration:none;}
    .ap-icon-btn svg{width:15px;height:15px;stroke:var(--ap-ink-muted);}
    .ap-icon-btn:hover{background:#FAF8F3;border-color:#D8D2C2;}
    .ap-icon-btn--danger:hover{background:var(--ap-off-bg);border-color:#E0BBAF;}
    .ap-icon-btn--danger:hover svg{stroke:var(--ap-off-text);}
    .ap-empty{padding:56px 24px;text-align:center;color:var(--ap-ink-muted);}
    .ap-empty svg{width:40px;height:40px;stroke:#C8C0A8;margin-bottom:14px;display:block;margin-inline:auto;}
    .ap-empty h4{font-family:'Fraunces',serif;font-size:16px;color:var(--ap-ink);margin:0 0 6px;}
    .ap-empty p{font-size:13.5px;margin:0;}
    .ap-notif{padding:12px 18px;border-radius:10px;font-size:13.5px;font-weight:600;margin-bottom:18px;font-family:'Plus Jakarta Sans',sans-serif;}
    .ap-notif--ok{background:var(--ap-ok-bg);color:var(--ap-ok-text);}
    .ap-notif--off{background:var(--ap-off-bg);color:var(--ap-off-text);}
  </style>
</head>
<body data-admin data-page="aparatur">
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
        <h2 class="admin-topbar__title">Aparatur Desa</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Aparatur Desa</h1>
          <p class="text-muted">Kelola data perangkat dan pejabat Desa Gilang.</p>
        </div>
      </div>

      <!-- Notifikasi setelah redirect -->
      <?php if ($notif) : ?>
      <div class="ap-notif ap-notif--<?php echo $notif['type']; ?>">
        <?php echo htmlspecialchars($notif['pesan']); ?>
      </div>
      <?php endif; ?>

      <!-- Toolbar: filter + tombol tambah -->
      <form class="ap-toolbar" method="GET" action="">
        <div class="ap-filters">
          <div class="ap-search">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke-width="1.6"/><path d="M21 21l-4.3-4.3" stroke-width="1.6" stroke-linecap="round"/></svg>
            <input type="text" name="cari" placeholder="Cari nama aparatur..."
                   value="<?php echo htmlspecialchars($filter_cari); ?>">
          </div>

          <select class="ap-select" name="jabatan">
            <option value="">Semua Jabatan</option>
            <?php foreach ($semua_jabatan as $jab) :
              $sel = ($jab === $filter_jabatan) ? ' selected' : '';
            ?>
            <option value="<?php echo htmlspecialchars($jab); ?>"<?php echo $sel; ?>>
              <?php echo htmlspecialchars($jab); ?>
            </option>
            <?php endforeach; ?>
          </select>

          <select class="ap-select" name="status">
            <option value="">Semua Status</option>
            <?php foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $val => $lbl) :
              $sel = ($val === $filter_status) ? ' selected' : '';
            ?>
            <option value="<?php echo $val; ?>"<?php echo $sel; ?>><?php echo $lbl; ?></option>
            <?php endforeach; ?>
          </select>

          <?php if ($filter_cari || $filter_jabatan || $filter_status) : ?>
          <a href="/admin/aparatur/index.php" class="btn btn--ghost" style="padding:9px 14px;font-size:13px;">
            ✕ Reset
          </a>
          <?php endif; ?>
        </div>
        <button type="submit" style="display:none"></button>
        <a href="/admin/aparatur/tambah.php" class="btn btn--primary">+ Tambah Aparatur</a>
      </form>

      <!-- Tabel aparatur -->
      <div class="ap-card">
        <?php if (empty($aparatur_list)) : ?>
        <div class="ap-empty">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.4" stroke-width="1.6"/><path d="M4.5 20c1-3.8 4-5.8 7.5-5.8s6.5 2 7.5 5.8" stroke-width="1.6" stroke-linecap="round"/></svg>
          <h4>Belum ada data aparatur</h4>
          <p>Data yang dicari tidak ditemukan, atau belum ada aparatur yang ditambahkan.</p>
        </div>
        <?php else : ?>
        <table class="ap-table">
          <thead>
            <tr>
              <th>Foto</th>
              <th>Nama &amp; NIP</th>
              <th>Jabatan</th>
              <th>Status</th>
              <th style="text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($aparatur_list as $a) :
              $nama    = htmlspecialchars($a['nama']);
              $nip     = htmlspecialchars($a['nip']);
              $jabatan = htmlspecialchars($a['jabatan']);
              $badge   = $a['status'] === 'aktif' ? 'ap-badge--aktif' : 'ap-badge--nonaktif';
              $label   = $a['status'] === 'aktif' ? 'Aktif' : 'Nonaktif';
            ?>
            <tr>
              <td>
                <?php if ($a['foto']) : ?>
                  <img class="ap-foto" src="<?php echo htmlspecialchars($a['foto']); ?>" alt="<?php echo $nama; ?>">
                <?php else : ?>
                  <div class="ap-foto-placeholder"><?php echo htmlspecialchars(inisial($a['nama'])); ?></div>
                <?php endif; ?>
              </td>
              <td>
                <div class="ap-person">
                  <div>
                    <div class="ap-person-name"><?php echo $nama; ?></div>
                    <div class="ap-person-nip">NIP <?php echo $nip; ?></div>
                  </div>
                </div>
              </td>
              <td><?php echo $jabatan; ?></td>
              <td><span class="ap-badge <?php echo $badge; ?>"><?php echo $label; ?></span></td>
              <td>
                <div class="ap-actions" style="justify-content:flex-end;">
                  <a class="ap-icon-btn" href="/admin/aparatur/edit.php?id=<?php echo $a['id']; ?>" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M16.5 3.5l4 4L8 20l-5 1 1-5z" stroke-width="1.6" stroke-linejoin="round"/></svg>
                  </a>
                  <a class="ap-icon-btn ap-icon-btn--danger"
                     href="/admin/aparatur/hapus.php?id=<?php echo $a['id']; ?>"
                     title="Hapus"
                     onclick="return confirm('Hapus data aparatur &quot;<?php echo addslashes($a['nama']); ?>&quot;? Tindakan ini tidak dapat dibatalkan.')">
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

<script src="/js/aparatur.js"></script>
<?php require __DIR__ . '/../_sidebar-script.php'; ?>
<script>
  /* Auto-submit form filter saat select berubah */
  document.querySelectorAll('.ap-select').forEach(function (sel) {
    sel.addEventListener('change', function () { sel.closest('form').submit(); });
  });
</script>
</body>
</html>