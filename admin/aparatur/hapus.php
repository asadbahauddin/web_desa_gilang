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
require_once __DIR__ . '/../_upload.php';

// ============================================================
//  Ambil ID dari URL
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT id, nama, nip, jabatan, foto FROM aparatur WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$aparatur = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Redirect jika ID tidak ditemukan
if (!$aparatur) {
  header('Location: /admin/aparatur/index.php');
  exit;
}

// ============================================================
//  Proses hapus (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
  if ($_POST['konfirmasi'] === $aparatur['nama']) {
    hapus_file_upload($aparatur['foto']);
    $stmt = mysqli_prepare($conn, "DELETE FROM aparatur WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: /admin/aparatur/index.php?deleted=1');
    exit;
  }
}

// ============================================================
//  Helper: inisial nama (maks 2 kata)
// ============================================================
function inisial(string $nama): string {
  $kata = array_filter(explode(' ', $nama));
  return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($kata, 0, 2))));
}

require __DIR__ . '/../_nav.php';
$current_page = 'aparatur';
$h = fn(string $key) => htmlspecialchars($aparatur[$key] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hapus Aparatur — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .ap-breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--ap-ink-muted);margin-bottom:18px;flex-wrap:wrap;}
    .ap-breadcrumb a{color:var(--ap-ink-muted);text-decoration:none;}
    .ap-breadcrumb a:hover{color:var(--ap-ink);}
    .ap-breadcrumb span{color:var(--ap-ink);font-weight:600;}
    .ap-breadcrumb svg{width:14px;height:14px;stroke:currentColor;}
    .danger-card{background:#fff;border:1px solid #F5C9BF;border-radius:14px;overflow:hidden;max-width:560px;}
    .danger-card__header{background:#FDF2EF;padding:28px 28px 24px;display:flex;align-items:flex-start;gap:16px;border-bottom:1px solid #F5C9BF;}
    .danger-icon{width:48px;height:48px;border-radius:12px;background:var(--ap-off-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .danger-icon svg{width:24px;height:24px;stroke:var(--ap-off-text);}
    .danger-card__header-text h2{font-family:'Fraunces',serif;font-size:20px;font-weight:600;color:var(--ap-ink);margin:0 0 6px;}
    .danger-card__header-text p{font-size:13.5px;color:var(--ap-ink-muted);margin:0;line-height:1.55;}
    .danger-person{display:flex;align-items:center;gap:16px;padding:22px 28px;border-bottom:1px solid var(--ap-line);}
    .danger-avatar{width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid var(--ap-line);background:#FAF8F3;flex-shrink:0;}
    .danger-avatar-placeholder{width:56px;height:56px;border-radius:50%;background:var(--sb-accent-soft);display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;color:#9A5A1F;flex-shrink:0;}
    .danger-person-name{font-weight:700;font-size:15px;color:var(--ap-ink);margin:0 0 3px;}
    .danger-person-sub{font-size:13px;color:var(--ap-ink-muted);margin:0;}
    .danger-warning{margin:20px 28px 0;padding:14px 16px;background:#FDF2EF;border-left:3px solid var(--ap-off-text);border-radius:0 8px 8px 0;font-size:13px;color:var(--ap-off-text);line-height:1.55;}
    .danger-warning strong{display:block;margin-bottom:3px;font-size:13.5px;}
    .danger-card__body{padding:20px 28px 28px;}
    .confirm-input-wrap{margin-top:16px;}
    .confirm-input-wrap label{display:block;font-size:12.5px;font-weight:600;color:var(--ap-ink-muted);letter-spacing:.02em;font-family:'IBM Plex Mono',monospace;margin-bottom:7px;}
    .confirm-input-wrap input{width:100%;padding:9px 12px;border-radius:9px;border:1px solid var(--ap-line);font-family:'Plus Jakarta Sans',sans-serif;font-size:13.5px;color:var(--ap-ink);box-sizing:border-box;transition:border-color .15s ease,box-shadow .15s ease;}
    .confirm-input-wrap input:focus{outline:none;border-color:var(--sb-danger);box-shadow:0 0 0 3px rgba(201,88,63,.12);}
    .danger-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:22px;}
    .btn-batal{padding:9px 20px;border-radius:9px;border:1px solid var(--ap-line);background:#fff;color:var(--ap-ink);font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s ease;}
    .btn-batal:hover{background:#F0EDE5;}
    .btn-hapus{padding:9px 22px;border-radius:9px;border:none;background:var(--sb-danger);color:#fff;font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s ease,opacity .15s ease;}
    .btn-hapus:disabled{opacity:.45;cursor:not-allowed;}
    .btn-hapus:not(:disabled):hover{background:#a8402b;}
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
        <h2 class="admin-topbar__title">Hapus Aparatur</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <!-- Breadcrumb -->
      <nav class="ap-breadcrumb" aria-label="Breadcrumb">
        <a href="/admin/aparatur/index.php">Aparatur Desa</a>
        <svg viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Hapus Aparatur</span>
      </nav>

      <!-- Danger Card -->
      <div class="danger-card">
        <div class="danger-card__header">
          <div class="danger-icon">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4.8c0-.4.4-.8.9-.8h4.2c.5 0 .9.4.9.8V7M6 7l1 13.2c0 .9.8 1.8 1.7 1.8h6.6c.9 0 1.7-.9 1.7-1.8L18 7" stroke-width="1.6" stroke-linecap="round"/></svg>
          </div>
          <div class="danger-card__header-text">
            <h2>Hapus Data Aparatur</h2>
            <p>Tindakan ini akan menghapus data aparatur secara permanen dari sistem dan tidak dapat dikembalikan.</p>
          </div>
        </div>

        <!-- Profil aparatur yang akan dihapus -->
        <div class="danger-person">
          <?php if ($aparatur['foto']) : ?>
            <img class="danger-avatar"
                 src="<?php echo $h('foto'); ?>"
                 alt="<?php echo $h('nama'); ?>">
          <?php else : ?>
            <div class="danger-avatar-placeholder">
              <?php echo htmlspecialchars(inisial($aparatur['nama'])); ?>
            </div>
          <?php endif; ?>
          <div>
            <p class="danger-person-name"><?php echo $h('nama'); ?></p>
            <p class="danger-person-sub">
              <?php echo $h('jabatan'); ?> &middot; NIP <?php echo $h('nip'); ?>
            </p>
          </div>
        </div>

        <div class="danger-warning">
          <strong>⚠ Peringatan</strong>
          Semua data terkait aparatur ini, termasuk riwayat jabatan dan informasi kontak, akan dihapus secara permanen. Pastikan Anda telah membuat cadangan data jika diperlukan.
        </div>

        <!-- Form konfirmasi -->
        <div class="danger-card__body">
          <form method="POST" action="" id="formHapus">
            <input type="hidden" name="konfirmasi" id="konfirmasiInput" value="">

            <div class="confirm-input-wrap">
              <label for="confirmInput">KETIK NAMA APARATUR UNTUK KONFIRMASI</label>
              <input type="text" id="confirmInput"
                     placeholder="Ketik: <?php echo $h('nama'); ?>"
                     oninput="checkKonfirmasi()">
            </div>

            <div class="danger-actions">
              <a href="/admin/aparatur/index.php" class="btn-batal">Batal</a>
              <button type="submit" class="btn-hapus" id="btnHapus" disabled>
                Ya, Hapus Sekarang
              </button>
            </div>
          </form>
        </div>
      </div>

    </main>
  </div>
</div>

<script src="/js/aparatur.js"></script>
<script>
  /* Nama aparatur dari PHP — dipakai untuk validasi konfirmasi */
  var namaTarget = <?php echo json_encode($aparatur['nama']); ?>;

  function checkKonfirmasi() {
    var val    = document.getElementById('confirmInput').value.trim();
    var tombol = document.getElementById('btnHapus');
    var cocok  = (val === namaTarget);
    tombol.disabled = !cocok;
    /* Sinkron ke hidden input agar ikut terkirim ke POST */
    document.getElementById('konfirmasiInput').value = cocok ? val : '';
  }
</script>
<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>