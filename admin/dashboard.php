<?php
require_once '../config/session.php';
require_once '../config/auth.php';
require_once __DIR__ . '/../config/database.php';
require __DIR__ . '/_nav.php';

checkLogin();

$admin = $_SESSION['admin'];
$db = $conn;

// Inisial nama untuk avatar
$namaParts = explode(' ', trim($admin['nama']));
$inisial   = strtoupper(substr($namaParts[0], 0, 1) . (isset($namaParts[1]) ? substr($namaParts[1], 0, 1) : ''));

// Statistik
function countTable($db, $tabel) {
    try {
        $r = $db->query("SELECT COUNT(*) AS total FROM `$tabel`");
    } catch (mysqli_sql_exception $e) {
        return 0;
    }
    return $r ? (int)$r->fetch_assoc()['total'] : 0;
}

$statBerita   = countTable($db, 'berita');
$statDokumen  = countTable($db, 'dokumen');
$statGaleri   = countTable($db, 'galeri');
$statAparatur = countTable($db, 'aparatur');

$beritaTerbaru = $db->query("SELECT judul, status, created_at FROM berita ORDER BY created_at DESC LIMIT 5");

$dokumenTerbaru = $db->query("SELECT nama, kategori, created_at FROM dokumen ORDER BY created_at DESC LIMIT 5");
if (!$dokumenTerbaru) {
    die($db->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="../css/style.css">
  <?php require __DIR__ . '/_sidebar-style.php'; ?>
  <style>
    /* ===== Layout dasar admin ===== */
    body[data-admin]{
      margin:0;
      background:#F5F4EF;
      font-family:'Plus Jakarta Sans',sans-serif;
      color:#26301F;
    }

    /* ===== Topbar ===== */
    .admin-topbar{
      position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;
      background:#fff;border-bottom:1px solid #ECEAE2;padding:14px 28px;
    }
    .admin-topbar__toggle{
      display:none;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;
      border:1px solid #ECEAE2;background:#fff;color:#26301F;cursor:pointer;
    }
    .admin-topbar__title{font-family:'Fraunces',serif;font-size:19px;font-weight:600;margin:0;color:#1F2E22;}
    .admin-topbar__user{display:flex;align-items:center;gap:10px;}
    .admin-topbar__name{font-size:13.5px;font-weight:600;color:#333;}
    .admin-topbar__avatar{
      width:34px;height:34px;border-radius:50%;background:var(--sb-accent-soft);color:var(--sb-accent);
      display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;
    }

    /* ===== Konten ===== */
    .admin-content{padding:28px;max-width:1280px;margin:0 auto;}
    .admin-page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;}
    .admin-page-header h1{font-family:'Fraunces',serif;font-size:26px;font-weight:600;margin:0 0 4px;color:#1F2E22;}
    .text-muted{color:#8A8C7F;font-size:14px;margin:0;}

    /* ===== Stat cards ===== */
    .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;}
    @media(max-width:1000px){.stat-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:520px){.stat-grid{grid-template-columns:1fr;}}
    .stat-card{
      display:flex;align-items:center;gap:14px;background:#fff;border:1px solid #ECEAE2;border-radius:16px;
      padding:18px 20px;--card-color:#C8893B;
    }
    .stat-card__icon{
      width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;
      background:rgba(200,137,59,.14);color:var(--card-color);flex-shrink:0;
    }
    .stat-card__icon svg{width:22px;height:22px;}
    .stat-card__number{font-family:'Fraunces',serif;font-size:24px;font-weight:700;margin:0;color:#1F2E22;line-height:1.1;}
    .stat-card__label{font-size:12.5px;color:#8A8C7F;margin:2px 0 0;}
    .stat-card--berita{--card-color:#C8893B;}
    .stat-card--berita .stat-card__icon{background:rgba(200,137,59,.14);color:#C8893B;}
    .stat-card--dokumen{--card-color:#2F6F5E;}
    .stat-card--dokumen .stat-card__icon{background:rgba(47,111,94,.14);color:#2F6F5E;}
    .stat-card--galeri{--card-color:#3468B0;}
    .stat-card--galeri .stat-card__icon{background:rgba(52,104,176,.14);color:#3468B0;}
    .stat-card--aparatur{--card-color:#7C5CBF;}
    .stat-card--aparatur .stat-card__icon{background:rgba(124,92,191,.14);color:#7C5CBF;}

    /* ===== Panel & tombol akses cepat ===== */
    .admin-panel{background:#fff;border:1px solid #ECEAE2;border-radius:16px;padding:20px 22px;}
    .admin-panel__title{font-size:14px;font-weight:700;margin:0 0 14px;color:#222;font-family:'Plus Jakarta Sans',sans-serif;}
    .btn{
      display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:10px;font-size:13.5px;
      font-weight:600;text-decoration:none;border:1px solid transparent;transition:opacity .15s ease,background .15s ease;
    }
    .btn--primary{background:var(--sb-accent);color:#fff;}
    .btn--primary:hover{opacity:.9;}
    .btn--ghost{background:#F5F4EF;color:#333;border-color:#ECEAE2;}
    .btn--ghost:hover{background:#ECEAE2;}

    /* stat tambahan */
    /* tabel ringkasan */
    .admin-summary-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:24px;}
    @media(max-width:800px){.admin-summary-grid{grid-template-columns:1fr;}}
    .summary-table-wrap{background:#fff;border-radius:14px;padding:20px;border:1px solid #eee;}
    .summary-table-wrap h3{font-size:14px;font-weight:700;margin:0 0 14px;color:#222;font-family:'Plus Jakarta Sans',sans-serif;}
    .summary-table{width:100%;border-collapse:collapse;font-size:13px;}
    .summary-table th{text-align:left;padding:7px 10px;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#999;border-bottom:2px solid #f3f3f3;}
    .summary-table td{padding:9px 10px;border-bottom:1px solid #f5f5f5;color:#333;vertical-align:middle;}
    .summary-table tr:last-child td{border-bottom:none;}
    .summary-table .badge{display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;}
    .badge--published{background:#d1fae5;color:#065f46;}
    .badge--draft{background:#f3f4f6;color:#6b7280;}
    .empty-row{color:#aaa;font-style:italic;}
    @media(max-width:900px){
      .admin-topbar__toggle{display:flex;}
    }
  </style>
</head>
<body data-admin data-page="dashboard">

<?php $current_page = 'dashboard'; ?>
<div class="admin-layout">
  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>

  <?php require __DIR__ . '/_sidebar.php'; ?>

  <!-- MAIN -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <h2 class="admin-topbar__title">Dashboard</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?= htmlspecialchars($admin['nama']) ?></span>
        <span class="admin-topbar__avatar"><?= htmlspecialchars($inisial) ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Selamat Datang, <?= htmlspecialchars(explode(' ', $admin['nama'])[0]) ?> 👋</h1>
          <p class="text-muted">Ringkasan konten situs Desa Gilang saat ini.</p>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="stat-grid">
        <div class="stat-card stat-card--berita">
          <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M7 9h10M7 13h10M7 17h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
          </span>
          <div>
            <p class="stat-card__number"><?= $statBerita ?></p>
            <p class="stat-card__label">Total Berita</p>
          </div>
        </div>

        <div class="stat-card stat-card--dokumen">
          <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" fill="none"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
          </span>
          <div>
            <p class="stat-card__number"><?= $statDokumen ?></p>
            <p class="stat-card__label">Total Dokumen</p>
          </div>
        </div>

        <div class="stat-card stat-card--galeri">
          <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.6"/><circle cx="8.5" cy="8.5" r="1.8" stroke="currentColor" stroke-width="1.6"/><path d="M21 16l-5.5-5.5L9 17" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
          </span>
          <div>
            <p class="stat-card__number"><?= $statGaleri ?></p>
            <p class="stat-card__label">Total Foto Galeri</p>
          </div>
        </div>

        <div class="stat-card stat-card--aparatur">
          <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.4" stroke="currentColor" stroke-width="1.6"/><path d="M4.5 20c1-3.8 4-5.8 7.5-5.8s6.5 2 7.5 5.8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
          </span>
          <div>
            <p class="stat-card__number"><?= $statAparatur ?></p>
            <p class="stat-card__label">Aparatur Desa</p>
          </div>
        </div>
      </div>

      <!-- Akses Cepat -->
      <div class="admin-panel" style="margin-top:24px;">
        <h3 class="admin-panel__title">Akses Cepat</h3>
        <div style="display:flex;gap:14px;flex-wrap:wrap;">
          <a href="/admin/berita/tambah.php"   class="btn btn--primary">+ Tambah Berita</a>
          <a href="/admin/dokumen/tambah.php"  class="btn btn--ghost">+ Upload Dokumen</a>
          <a href="/admin/aparatur/tambah.php" class="btn btn--ghost">+ Tambah Aparatur</a>
          <a href="/admin/galeri/tambah.php"   class="btn btn--ghost">+ Tambah Foto</a>
        </div>
      </div>

      <!-- Tabel Ringkasan -->
      <div class="admin-summary-grid">
        <div class="summary-table-wrap">
          <h3>📰 Berita Terbaru</h3>
          <table class="summary-table">
            <thead><tr><th>Judul</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
              <?php while ($b = $beritaTerbaru->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars(mb_strimwidth($b['judul'], 0, 38, '…')) ?></td>
  <td>
    <span class="badge badge--<?= $b['status'] ?>">
      <?= $b['status'] === 'published' ? 'Tayang' : 'Draft' ?>
    </span>
  </td>
  <td><?= date('d/m/Y', strtotime($b['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
            </tbody>
          </table>
          <div style="margin-top:12px;">
            <a href="/admin/berita/index.php" style="font-size:13px;color:#2563eb;font-weight:600;text-decoration:none;">Lihat semua berita →</a>
          </div>
        </div>

        <div class="summary-table-wrap">
          <h3>📁 Dokumen Terbaru</h3>
          <table class="summary-table">
            <thead><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th></tr></thead>
            <tbody>
              <?php if ($dokumenTerbaru->num_rows === 0): ?>
                <tr><td colspan="3" class="empty-row">Belum ada dokumen.</td></tr>
              <?php else: while ($d = $dokumenTerbaru->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars(mb_strimwidth($d['nama'], 0, 38, '…')) ?></td>
                <td><?= htmlspecialchars($d['kategori']) ?></td>
                <td><?= date('d/m/Y', strtotime($d['created_at'])) ?></td>
              </tr>
              <?php endwhile; endif; ?>
            </tbody>
          </table>
          <div style="margin-top:12px;">
            <a href="/admin/dokumen/index.php" style="font-size:13px;color:#2563eb;font-weight:600;text-decoration:none;">Lihat semua dokumen →</a>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<?php require __DIR__ . '/_sidebar-script.php'; ?>
</body>
</html>