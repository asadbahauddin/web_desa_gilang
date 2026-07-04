<?php
require_once '../config/session.php';
require_once '../config/auth.php';
require_once __DIR__ . '/../config/database.php';


checkLogin();

$admin = $_SESSION['admin'];
$db = $conn;

// Inisial nama untuk avatar
$namaParts = explode(' ', trim($admin['nama']));
$inisial   = strtoupper(substr($namaParts[0], 0, 1) . (isset($namaParts[1]) ? substr($namaParts[1], 0, 1) : ''));

// Statistik
function countTable($db, $tabel) {
    $r = $db->query("SELECT COUNT(*) AS total FROM `$tabel`");
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
  <link rel="icon" href="/assets/logo/logo-desa.png">
  <link rel="stylesheet" href="../css/style.css">
  <style>
    :root{
      --sb-bg:#1F2E22; --sb-bg-soft:#25392B; --sb-bg-active:#32492F;
      --sb-accent:#C8893B; --sb-accent-soft:rgba(200,137,59,.16);
      --sb-text:#ECE7DA; --sb-text-muted:#98A691;
      --sb-border:rgba(255,255,255,.08); --sb-danger:#C9583F; --sb-width:264px;
    }

    /* ===== Layout dasar admin ===== */
    body[data-admin]{
      margin:0;
      background:#F5F4EF;
      font-family:'Plus Jakarta Sans',sans-serif;
      color:#26301F;
    }
    .admin-layout{display:flex;align-items:stretch;min-height:100vh;}
    .admin-sidebar{
      width:var(--sb-width);flex-shrink:0;background:var(--sb-bg);color:var(--sb-text);
      display:flex;flex-direction:column;position:sticky;top:0;height:100vh;
      font-family:'Plus Jakarta Sans',sans-serif;border-right:1px solid var(--sb-border);z-index:40;
    }
    .admin-sidebar__brand{display:flex;align-items:center;gap:12px;padding:22px 20px 18px;border-bottom:1px solid var(--sb-border);}
    .admin-sidebar__brand img{width:38px;height:38px;border-radius:10px;object-fit:cover;background:var(--sb-bg-soft);}
    .admin-sidebar__brand-text h1{font-family:'Fraunces',serif;font-size:16px;font-weight:600;line-height:1.15;margin:0;color:var(--sb-text);}
    .admin-sidebar__brand-text span{display:block;font-size:11.5px;letter-spacing:.04em;color:var(--sb-text-muted);margin-top:2px;}
    .admin-sidebar__nav{flex:1;overflow-y:auto;padding:18px 14px;}
    .admin-sidebar__group{margin-bottom:20px;}
    .admin-sidebar__group-label{font-family:'IBM Plex Mono',monospace;font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;color:var(--sb-text-muted);padding:0 10px 8px;}
    .admin-sidebar__link{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;color:var(--sb-text-muted);text-decoration:none;font-size:14px;font-weight:500;margin-bottom:3px;transition:background .15s ease,color .15s ease;position:relative;}
    .admin-sidebar__link svg{width:18px;height:18px;flex-shrink:0;stroke:currentColor;}
    .admin-sidebar__link:hover{background:var(--sb-bg-soft);color:var(--sb-text);}
    .admin-sidebar__link.is-active{background:var(--sb-bg-active);color:#fff;}
    .admin-sidebar__link.is-active::before{content:"";position:absolute;left:-14px;top:50%;transform:translateY(-50%);width:3px;height:18px;border-radius:2px;background:var(--sb-accent);}
    .admin-sidebar__footer{padding:14px;border-top:1px solid var(--sb-border);}
    .admin-sidebar__user{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;margin-bottom:6px;}
    .admin-sidebar__user-avatar{width:32px;height:32px;border-radius:50%;background:var(--sb-accent-soft);color:var(--sb-accent);display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:700;flex-shrink:0;}
    .admin-sidebar__user-name{font-size:13px;font-weight:600;color:var(--sb-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .admin-sidebar__user-role{font-size:11px;color:var(--sb-text-muted);}
    .admin-logout{display:flex;align-items:center;justify-content:space-between;width:100%;gap:10px;padding:10px 12px;border-radius:10px;border:none;background:transparent;color:var(--sb-text-muted);font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s ease,color .15s ease;}
    .admin-logout span.admin-logout__label{display:flex;align-items:center;gap:10px;}
    .admin-logout svg{width:18px;height:18px;stroke:currentColor;flex-shrink:0;}
    .admin-logout:hover{background:rgba(201,88,63,.14);color:#E18672;}
    .admin-logout.is-confirming{background:rgba(201,88,63,.14);color:#fff;}
    .admin-logout__confirm{display:none;gap:8px;}
    .admin-logout.is-confirming .admin-logout__label{display:none;}
    .admin-logout.is-confirming .admin-logout__confirm{display:flex;}
    .admin-logout__confirm button{border:none;border-radius:7px;font-size:12.5px;font-weight:700;padding:5px 10px;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;}
    .admin-logout__confirm-yes{background:var(--sb-danger);color:#fff;}
    .admin-logout__confirm-no{background:rgba(255,255,255,.1);color:var(--sb-text);}
    .admin-sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:35;}
    .admin-main{flex:1;min-width:0;}

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
      .admin-sidebar{position:fixed;left:0;top:0;transform:translateX(-100%);transition:transform .22s ease;box-shadow:8px 0 24px rgba(0,0,0,.25);}
      .admin-sidebar.is-open{transform:translateX(0);}
      .admin-sidebar-backdrop.is-open{display:block;}
      .admin-topbar__toggle{display:flex;}
    }
  </style>
</head>
<body data-admin data-page="dashboard">

<div class="admin-layout">
  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- SIDEBAR -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar__brand">
      <img src="/assets/logo/logo-desa.jpg" alt="Logo Desa Gilang">
      <div class="admin-sidebar__brand-text">
        <h1>Desa Gilang</h1>
        <span>Panel Admin</span>
      </div>
    </div>

    <nav class="admin-sidebar__nav">
      <div class="admin-sidebar__group">
        <a href="/admin/dashboard.php" class="admin-sidebar__link" data-nav="dashboard">
          <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="1.5" stroke-width="1.6"/><rect x="13" y="3" width="8" height="5" rx="1.5" stroke-width="1.6"/><rect x="13" y="11" width="8" height="10" rx="1.5" stroke-width="1.6"/><rect x="3" y="14" width="8" height="7" rx="1.5" stroke-width="1.6"/></svg>
          Dashboard
        </a>
      </div>

      <div class="admin-sidebar__group">
        <p class="admin-sidebar__group-label">Konten</p>
        <a href="/admin/berita/index.php" class="admin-sidebar__link" data-nav="berita">
          <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.6"/><path d="M7 9h10M7 13h10M7 17h6" stroke-width="1.6" stroke-linecap="round"/></svg>
          Berita
        </a>
        <a href="/admin/dokumen/index.php" class="admin-sidebar__link" data-nav="dokumen">
          <svg viewBox="0 0 24 24" fill="none"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Dokumen
        </a>
        <a href="/admin/galeri/index.php" class="admin-sidebar__link" data-nav="galeri">
          <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.6"/><circle cx="8.5" cy="8.5" r="1.8" stroke-width="1.6"/><path d="M21 16l-5.5-5.5L9 17" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Galeri
        </a>
        <a href="/admin/pengumuman/index.php" class="admin-sidebar__link" data-nav="pengumuman">
          <svg viewBox="0 0 24 24" fill="none"><path d="M18 8a6 6 0 00-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Pengumuman
        </a>
      </div>

      <div class="admin-sidebar__group">
        <p class="admin-sidebar__group-label">Profil Desa</p>
        <a href="/admin/aparatur/index.php" class="admin-sidebar__link" data-nav="aparatur">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.4" stroke-width="1.6"/><path d="M4.5 20c1-3.8 4-5.8 7.5-5.8s6.5 2 7.5 5.8" stroke-width="1.6" stroke-linecap="round"/></svg>
          Aparatur Desa
        </a>
      </div>

      <div class="admin-sidebar__group">
        <p class="admin-sidebar__group-label">Pesan</p>
        <a href="/admin/kontak/index.php" class="admin-sidebar__link" data-nav="kontak">
          <svg viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke-width="1.6" stroke-linejoin="round"/></svg>
          Pesan Masuk
        </a>
      </div>
    </nav>

    <div class="admin-sidebar__footer">
      <div class="admin-sidebar__user">
        <span class="admin-sidebar__user-avatar"><?= htmlspecialchars($inisial) ?></span>
        <div>
          <div class="admin-sidebar__user-name"><?= htmlspecialchars($admin['nama']) ?></div>
          <div class="admin-sidebar__user-role"><?= htmlspecialchars($admin['peran'] ?? 'Administrator') ?></div>
        </div>
      </div>
      <button class="admin-logout" id="logoutBtn" type="button">
        <span class="admin-logout__label">
          <svg viewBox="0 0 24 24" fill="none"><path d="M15 17l5-5-5-5M20 12H9M13 4H6a2 2 0 00-2 2v12a2 2 0 002 2h7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Keluar
        </span>
        <span class="admin-logout__confirm">
          Yakin?
          <button type="button" class="admin-logout__confirm-yes" id="logoutConfirmYes">Ya</button>
          <button type="button" class="admin-logout__confirm-no" id="logoutConfirmNo">Batal</button>
        </span>
      </button>
    </div>
  </aside>

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
                <td><?= htmlspecialchars(mb_strimwidth($d['judul'], 0, 38, '…')) ?></td>
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

<script>
  // Tandai menu aktif
  (function(){
    var p = document.body.getAttribute('data-page');
    var a = p && document.querySelector('.admin-sidebar__link[data-nav="'+p+'"]');
    if(a) a.classList.add('is-active');
  })();

  // Toggle sidebar mobile
  (function(){
    var btn      = document.getElementById('sidebarToggle');
    var sidebar  = document.getElementById('adminSidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    if(!btn) return;
    btn.addEventListener('click', function(){
      sidebar.classList.toggle('is-open');
      backdrop.classList.toggle('is-open');
    });
    backdrop.addEventListener('click', function(){
      sidebar.classList.remove('is-open');
      backdrop.classList.remove('is-open');
    });
  })();

  // Logout konfirmasi
  (function(){
    var btn  = document.getElementById('logoutBtn');
    var yes  = document.getElementById('logoutConfirmYes');
    var no   = document.getElementById('logoutConfirmNo');
    if(!btn) return;
    btn.addEventListener('click', function(){
      if(!btn.classList.contains('is-confirming')) btn.classList.add('is-confirming');
    });
    no.addEventListener('click',  function(e){ e.stopPropagation(); btn.classList.remove('is-confirming'); });
    yes.addEventListener('click', function(e){ e.stopPropagation(); window.location.href='/admin/logout.php'; });
  })();
</script>
</body>
</html>