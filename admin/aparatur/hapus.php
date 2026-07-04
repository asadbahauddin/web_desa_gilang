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
// require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// ============================================================
//  Ambil ID dari URL
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// ============================================================
//  Data aparatur — ganti dengan query DB
//
//  Contoh query PDO:
//  $stmt = $pdo->prepare("SELECT id, nama, nip, jabatan, foto FROM aparatur_desa WHERE id = ? LIMIT 1");
//  $stmt->execute([$id]);
//  $aparatur = $stmt->fetch(PDO::FETCH_ASSOC);
//  if (!$aparatur) { header('Location: /admin/aparatur/index.php'); exit; }
// ============================================================
$semua_aparatur = [
  ['id'=>1,'nama'=>'Ahmad Supriyadi', 'nip'=>'197203012005011001','jabatan'=>'Kepala Desa',        'foto'=>''],
  ['id'=>2,'nama'=>'Siti Maemunah',   'nip'=>'198005152008012002','jabatan'=>'Sekretaris Desa',    'foto'=>''],
  ['id'=>3,'nama'=>'Budi Santoso',    'nip'=>'198501202010011003','jabatan'=>'Kaur Keuangan',      'foto'=>''],
  ['id'=>4,'nama'=>'Rina Wulandari',  'nip'=>'199002102012012004','jabatan'=>'Kaur Umum & TU',    'foto'=>''],
  ['id'=>5,'nama'=>'Joko Prasetyo',   'nip'=>'198808182011011005','jabatan'=>'Kasi Pemerintahan',  'foto'=>''],
  ['id'=>6,'nama'=>'Dewi Anggraini',  'nip'=>'199103252013012006','jabatan'=>'Kasi Kesejahteraan', 'foto'=>''],
  ['id'=>7,'nama'=>'Agus Setiawan',   'nip'=>'198712302009011007','jabatan'=>'Kasi Pelayanan',     'foto'=>''],
  ['id'=>8,'nama'=>'Hendro Wibowo',   'nip'=>'198309142010011008','jabatan'=>'Kepala Dusun I',     'foto'=>''],
  ['id'=>9,'nama'=>'Yusuf Hidayat',   'nip'=>'199105052014011009','jabatan'=>'Kepala Dusun II',    'foto'=>''],
];

$aparatur = null;
foreach ($semua_aparatur as $item) {
  if ($item['id'] === $id) { $aparatur = $item; break; }
}

// Redirect jika ID tidak ditemukan
if (!$aparatur) {
  header('Location: /admin/aparatur/index.php');
  exit;
}

// ============================================================
//  Proses hapus (POST)
//
//  Contoh hapus dari DB (PDO):
//  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
//    if ($_POST['konfirmasi'] === $aparatur['nama']) {
//      // Hapus foto jika ada
//      if ($aparatur['foto']) {
//        $fotoPath = $_SERVER['DOCUMENT_ROOT'] . $aparatur['foto'];
//        if (file_exists($fotoPath)) unlink($fotoPath);
//      }
//      $stmt = $pdo->prepare("DELETE FROM aparatur_desa WHERE id = ?");
//      $stmt->execute([$id]);
//      header('Location: /admin/aparatur/index.php?deleted=1');
//      exit;
//    }
//  }
// ============================================================

// ============================================================
//  Helper: inisial nama (maks 2 kata)
// ============================================================
function inisial(string $nama): string {
  $kata = array_filter(explode(' ', $nama));
  return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($kata, 0, 2))));
}

// ============================================================
//  Nav sidebar
// ============================================================
$nav_groups = [
  [
    'label' => null,
    'links' => [
      ['href'=>'/admin/dashboard.php','nav'=>'dashboard','label'=>'Dashboard',
       'icon'=>'<rect x="3" y="3" width="8" height="8" rx="1.5" stroke-width="1.6"/><rect x="13" y="3" width="8" height="5" rx="1.5" stroke-width="1.6"/><rect x="13" y="11" width="8" height="10" rx="1.5" stroke-width="1.6"/><rect x="3" y="14" width="8" height="7" rx="1.5" stroke-width="1.6"/>'],
    ],
  ],
  [
    'label' => 'Konten',
    'links' => [
      ['href'=>'/admin/berita/index.php','nav'=>'berita','label'=>'Berita',
       'icon'=>'<rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.6"/><path d="M7 9h10M7 13h10M7 17h6" stroke-width="1.6" stroke-linecap="round"/>'],
      ['href'=>'/admin/dokumen/index.php','nav'=>'dokumen','label'=>'Dokumen',
       'icon'=>'<path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke-width="1.6" stroke-linejoin="round"/>'],
    ],
  ],
  [
    'label' => 'Profil Desa',
    'links' => [
      ['href'=>'/admin/struktur-organisasi/index.php','nav'=>'struktur','label'=>'Struktur Organisasi',
       'icon'=>'<circle cx="12" cy="5" r="2.2" stroke-width="1.6"/><circle cx="6" cy="18" r="2.2" stroke-width="1.6"/><circle cx="18" cy="18" r="2.2" stroke-width="1.6"/><path d="M12 7.2V12M12 12L6 15.8M12 12l6 3.8" stroke-width="1.6" stroke-linecap="round"/>'],
      ['href'=>'/admin/aparatur/index.php','nav'=>'aparatur','label'=>'Aparatur Desa',
       'icon'=>'<circle cx="12" cy="8" r="3.4" stroke-width="1.6"/><path d="M4.5 20c1-3.8 4-5.8 7.5-5.8s6.5 2 7.5 5.8" stroke-width="1.6" stroke-linecap="round"/>'],
    ],
  ],
];

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

  <link rel="icon" href="/assets/logo/logo-desa.png">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <style>
    :root{
      --sb-bg:#1F2E22; --sb-bg-soft:#25392B; --sb-bg-active:#32492F;
      --sb-accent:#C8893B; --sb-accent-soft:rgba(200,137,59,.16);
      --sb-text:#ECE7DA; --sb-text-muted:#98A691; --sb-border:rgba(255,255,255,.08);
      --sb-danger:#C9583F; --sb-width:264px;
      --ap-ink:#1F2E22; --ap-ink-muted:#6B7768; --ap-line:#E7E2D6;
      --ap-card:#ffffff; --ap-ok-bg:#EAF3E9; --ap-ok-text:#2F6B3F;
      --ap-off-bg:#F3E9E6; --ap-off-text:#A24A35;
    }
    .admin-layout{display:flex;align-items:stretch;min-height:100vh;}
    .admin-sidebar{width:var(--sb-width);flex-shrink:0;background:var(--sb-bg);color:var(--sb-text);display:flex;flex-direction:column;position:sticky;top:0;height:100vh;font-family:'Plus Jakarta Sans',sans-serif;border-right:1px solid var(--sb-border);z-index:40;}
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
    .admin-sidebar__user-email{font-size:12.5px;color:var(--sb-text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
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
    @media(max-width:900px){
      .admin-sidebar{position:fixed;left:0;top:0;transform:translateX(-100%);transition:transform .22s ease;box-shadow:8px 0 24px rgba(0,0,0,.25);}
      .admin-sidebar.is-open{transform:translateX(0);}
      .admin-sidebar-backdrop.is-open{display:block;}
    }
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

  <!-- ============ SIDEBAR ============ -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar__brand">
      <img src="/assets/logo/logo-desa.png" alt="Logo Desa Gilang">
      <div class="admin-sidebar__brand-text">
        <h1>Desa Gilang</h1><span>Panel Admin</span>
      </div>
    </div>

    <nav class="admin-sidebar__nav">
      <?php foreach ($nav_groups as $group) : ?>
      <div class="admin-sidebar__group">
        <?php if ($group['label']) : ?>
          <p class="admin-sidebar__group-label"><?php echo htmlspecialchars($group['label']); ?></p>
        <?php endif; ?>
        <?php foreach ($group['links'] as $link) :
          $active = ($link['nav'] === $current_page) ? ' is-active' : '';
        ?>
        <a href="<?php echo $link['href']; ?>" class="admin-sidebar__link<?php echo $active; ?>" data-nav="<?php echo $link['nav']; ?>">
          <svg viewBox="0 0 24 24" fill="none"><?php echo $link['icon']; ?></svg>
          <?php echo htmlspecialchars($link['label']); ?>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </nav>

    <div class="admin-sidebar__footer">
      <div class="admin-sidebar__user">
        <span class="admin-sidebar__user-avatar">AD</span>
        <span class="admin-sidebar__user-email">admin@desagilang.go.id</span>
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
        <span class="admin-topbar__name">admin@desagilang.go.id</span>
        <span class="admin-topbar__avatar">AD</span>
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

<script src="/js/auth.js"></script>
<script src="/js/aparatur.js"></script>
<script src="/js/dashboard.js"></script>
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

  /* Sidebar toggle */
  (function () {
    var t = document.getElementById('sidebarToggle'),
        s = document.getElementById('adminSidebar'),
        b = document.getElementById('sidebarBackdrop');
    function close() { s.classList.remove('is-open'); b.classList.remove('is-open'); }
    if (t && s && b) {
      t.addEventListener('click', function () { s.classList.toggle('is-open'); b.classList.toggle('is-open'); });
      b.addEventListener('click', close);
    }
  })();

  /* Logout konfirmasi */
  (function () {
    var lb = document.getElementById('logoutBtn'),
        yb = document.getElementById('logoutConfirmYes'),
        nb = document.getElementById('logoutConfirmNo');
    if (!lb) return;
    lb.addEventListener('click', function () { lb.classList.add('is-confirming'); });
    nb.addEventListener('click', function (e) { e.stopPropagation(); lb.classList.remove('is-confirming'); });
    yb.addEventListener('click', function (e) {
      e.stopPropagation();
      try {
        localStorage.removeItem('token'); localStorage.removeItem('adminToken');
        sessionStorage.removeItem('token'); sessionStorage.removeItem('adminToken');
      } catch (err) {}
      window.location.href = '/admin/login.php';
    });
  })();
</script>
</body>
</html>