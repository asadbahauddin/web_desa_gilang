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

$stmt = mysqli_prepare($conn, "SELECT id, nama, kategori, tanggal, file FROM dokumen WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$dokumen = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$dokumen) {
  header('Location: /admin/dokumen/index.php?error=not_found');
  exit;
}

// ============================================================
//  Proses hapus (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
  if ($_POST['konfirmasi'] === $dokumen['nama']) {
    hapus_file_upload($dokumen['file']);
    $stmt = mysqli_prepare($conn, "DELETE FROM dokumen WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    header('Location: /admin/dokumen/index.php?deleted=1');
    exit;
  }
}

// ============================================================
//  Label kategori
// ============================================================
$kategori_label = [
  'persyaratan'  => 'Persyaratan Pelayanan',
  'kesehatan'    => 'Informasi Kesehatan',
  'pengumuman'   => 'Pengumuman',
  'dokumen-desa' => 'Dokumen Desa',
];

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
$current_page = 'dokumen';
$kat_lbl      = htmlspecialchars($kategori_label[$dokumen['kategori']] ?? $dokumen['kategori']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hapus Dokumen — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    /* Breadcrumb */
    .ap-breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--ap-ink-muted);margin-bottom:18px;flex-wrap:wrap;}
    .ap-breadcrumb a{color:var(--ap-ink-muted);text-decoration:none;}
    .ap-breadcrumb a:hover{color:var(--ap-ink);}
    .ap-breadcrumb span{color:var(--ap-ink);font-weight:600;}
    .ap-breadcrumb svg{width:14px;height:14px;stroke:currentColor;}

    /* Danger card */
    .danger-card{background:#fff;border:1px solid #F5C9BF;border-radius:14px;overflow:hidden;max-width:560px;}
    .danger-card__header{background:#FDF2EF;padding:28px 28px 24px;display:flex;align-items:flex-start;gap:16px;border-bottom:1px solid #F5C9BF;}
    .danger-icon{width:48px;height:48px;border-radius:12px;background:var(--ap-off-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .danger-icon svg{width:24px;height:24px;stroke:var(--ap-off-text);}
    .danger-card__header-text h2{font-family:'Fraunces',serif;font-size:20px;font-weight:600;color:var(--ap-ink);margin:0 0 6px;}
    .danger-card__header-text p{font-size:13.5px;color:var(--ap-ink-muted);margin:0;line-height:1.55;}

    /* Info dokumen */
    .danger-doc{display:flex;align-items:center;gap:16px;padding:22px 28px;border-bottom:1px solid var(--ap-line);}
    .danger-doc-icon{width:48px;height:48px;border-radius:10px;background:#FDF2EF;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .danger-doc-icon svg{width:24px;height:24px;stroke:var(--ap-off-text);}
    .danger-doc-name{font-weight:700;font-size:15px;color:var(--ap-ink);margin:0 0 4px;}
    .danger-doc-meta{font-size:12.5px;color:var(--ap-ink-muted);margin:0;font-family:'IBM Plex Mono',monospace;}

    /* Warning */
    .danger-warning{margin:20px 28px 0;padding:14px 16px;background:#FDF2EF;border-left:3px solid var(--ap-off-text);border-radius:0 8px 8px 0;font-size:13px;color:var(--ap-off-text);line-height:1.55;}
    .danger-warning strong{display:block;margin-bottom:3px;font-size:13.5px;}

    .danger-card__body{padding:20px 28px 28px;}

    /* Konfirmasi input */
    .confirm-input-wrap{margin-top:16px;}
    .confirm-input-wrap label{display:block;font-size:12.5px;font-weight:600;color:var(--ap-ink-muted);letter-spacing:.02em;font-family:'IBM Plex Mono',monospace;margin-bottom:7px;}
    .confirm-input-wrap input{width:100%;padding:9px 12px;border-radius:9px;border:1px solid var(--ap-line);font-family:'Plus Jakarta Sans',sans-serif;font-size:13.5px;color:var(--ap-ink);box-sizing:border-box;transition:border-color .15s ease,box-shadow .15s ease;}
    .confirm-input-wrap input:focus{outline:none;border-color:var(--sb-danger);box-shadow:0 0 0 3px rgba(201,88,63,.12);}

    /* Aksi */
    .danger-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:22px;}
    .btn-batal{padding:9px 20px;border-radius:9px;border:1px solid var(--ap-line);background:#fff;color:var(--ap-ink);font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s ease;}
    .btn-batal:hover{background:#F0EDE5;}
    .btn-hapus{padding:9px 22px;border-radius:9px;border:none;background:var(--sb-danger);color:#fff;font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s ease,opacity .15s ease;}
    .btn-hapus:disabled{opacity:.45;cursor:not-allowed;}
    .btn-hapus:not(:disabled):hover{background:#a8402b;}
  </style>
</head>
<body data-admin data-page="dokumen">
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
        <h2 class="admin-topbar__title">Hapus Dokumen</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <!-- Breadcrumb -->
      <nav class="ap-breadcrumb" aria-label="Breadcrumb">
        <a href="/admin/dokumen/index.php">Dokumen</a>
        <svg viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Hapus Dokumen</span>
      </nav>

      <!-- Danger Card -->
      <div class="danger-card">

        <div class="danger-card__header">
          <div class="danger-icon">
            <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4.8c0-.4.4-.8.9-.8h4.2c.5 0 .9.4.9.8V7M6 7l1 13.2c0 .9.8 1.8 1.7 1.8h6.6c.9 0 1.7-.9 1.7-1.8L18 7" stroke-width="1.6" stroke-linecap="round"/></svg>
          </div>
          <div class="danger-card__header-text">
            <h2>Hapus Data Dokumen</h2>
            <p>Tindakan ini akan menghapus dokumen beserta file-nya secara permanen dan tidak dapat dikembalikan.</p>
          </div>
        </div>

        <!-- Info dokumen yang akan dihapus -->
        <div class="danger-doc">
          <div class="danger-doc-icon">
            <svg viewBox="0 0 24 24" fill="none"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 13h8M8 17h5" stroke-width="1.6" stroke-linecap="round"/></svg>
          </div>
          <div>
            <p class="danger-doc-name"><?php echo htmlspecialchars($dokumen['nama']); ?></p>
            <p class="danger-doc-meta">
              <?php echo $kat_lbl; ?>
              &middot; <?php echo format_tanggal($dokumen['tanggal']); ?>
              <?php if ($dokumen['file']) : ?>
              &middot; 📄 <?php echo htmlspecialchars($dokumen['file']); ?>
              <?php endif; ?>
            </p>
          </div>
        </div>

        <div class="danger-warning">
          <strong>⚠ Peringatan</strong>
          File PDF terkait dokumen ini juga akan dihapus dari server secara permanen. Pastikan Anda telah membuat cadangan jika diperlukan.
        </div>

        <!-- Form konfirmasi -->
        <div class="danger-card__body">
          <form method="POST" action="">
            <input type="hidden" name="konfirmasi" id="konfirmasiInput" value="">

            <div class="confirm-input-wrap">
              <label for="confirmInput">KETIK NAMA DOKUMEN UNTUK KONFIRMASI</label>
              <input type="text" id="confirmInput"
                     placeholder="Ketik: <?php echo htmlspecialchars($dokumen['nama']); ?>"
                     oninput="checkKonfirmasi()">
            </div>

            <div class="danger-actions">
              <a href="/admin/dokumen/index.php" class="btn-batal">Batal</a>
              <button type="submit" class="btn-hapus" id="btnHapus" disabled>
                Ya, Hapus Sekarang
              </button>
            </div>
          </form>
        </div>

      </div><!-- /.danger-card -->
    </main>
  </div>
</div>

<script>
  /* Nama dokumen dari PHP — dipakai untuk validasi konfirmasi */
  var namaTarget = <?php echo json_encode($dokumen['nama']); ?>;

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