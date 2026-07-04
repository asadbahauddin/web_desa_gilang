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
//  Filter dari URL
// ============================================================
$filter_cari    = trim($_GET['cari']    ?? '');
$filter_jabatan = trim($_GET['jabatan'] ?? '');
$filter_status  = trim($_GET['status']  ?? '');

// ============================================================
//  Data aparatur — ganti dengan query DB
//
//  Contoh query PDO dengan filter:
//  $where  = ['1=1'];
//  $params = [];
//  if ($filter_cari) {
//    $where[]  = '(nama LIKE ? OR nip LIKE ?)';
//    $params[] = '%' . $filter_cari . '%';
//    $params[] = '%' . $filter_cari . '%';
//  }
//  if ($filter_jabatan) { $where[] = 'jabatan = ?'; $params[] = $filter_jabatan; }
//  if ($filter_status)  { $where[] = 'status  = ?'; $params[] = $filter_status;  }
//
//  $stmt = $pdo->prepare("
//    SELECT id, nama, nip, jabatan, foto, status
//    FROM aparatur_desa
//    WHERE " . implode(' AND ', $where) . "
//    ORDER BY urutan ASC
//  ");
//  $stmt->execute($params);
//  $aparatur_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//  $semua_jabatan = $pdo->query("SELECT DISTINCT jabatan FROM aparatur_desa ORDER BY jabatan ASC")
//                       ->fetchAll(PDO::FETCH_COLUMN);
// ============================================================
$semua_aparatur = [
  ['id'=>1,'nama'=>'Ahmad Supriyadi', 'nip'=>'197203012005011001','jabatan'=>'Kepala Desa',        'foto'=>'','status'=>'aktif'],
  ['id'=>2,'nama'=>'Siti Maemunah',   'nip'=>'198005152008012002','jabatan'=>'Sekretaris Desa',    'foto'=>'','status'=>'aktif'],
  ['id'=>3,'nama'=>'Budi Santoso',    'nip'=>'198501202010011003','jabatan'=>'Kaur Keuangan',      'foto'=>'','status'=>'aktif'],
  ['id'=>4,'nama'=>'Rina Wulandari',  'nip'=>'199002102012012004','jabatan'=>'Kaur Umum & TU',    'foto'=>'','status'=>'aktif'],
  ['id'=>5,'nama'=>'Joko Prasetyo',   'nip'=>'198808182011011005','jabatan'=>'Kasi Pemerintahan',  'foto'=>'','status'=>'aktif'],
  ['id'=>6,'nama'=>'Dewi Anggraini',  'nip'=>'199103252013012006','jabatan'=>'Kasi Kesejahteraan', 'foto'=>'','status'=>'nonaktif'],
  ['id'=>7,'nama'=>'Agus Setiawan',   'nip'=>'198712302009011007','jabatan'=>'Kasi Pelayanan',     'foto'=>'','status'=>'aktif'],
  ['id'=>8,'nama'=>'Hendro Wibowo',   'nip'=>'198309142010011008','jabatan'=>'Kepala Dusun I',     'foto'=>'','status'=>'aktif'],
  ['id'=>9,'nama'=>'Yusuf Hidayat',   'nip'=>'199105052014011009','jabatan'=>'Kepala Dusun II',    'foto'=>'','status'=>'aktif'],
];

// Terapkan filter di PHP (hapus blok ini saat pakai query DB)
$aparatur_list = array_filter($semua_aparatur, function ($a) use ($filter_cari, $filter_jabatan, $filter_status) {
  if ($filter_cari && stripos($a['nama'], $filter_cari) === false && stripos($a['nip'], $filter_cari) === false) return false;
  if ($filter_jabatan && $a['jabatan'] !== $filter_jabatan) return false;
  if ($filter_status  && $a['status']  !== $filter_status)  return false;
  return true;
});
$aparatur_list = array_values($aparatur_list);

// Daftar jabatan unik untuk dropdown filter
$semua_jabatan = array_values(array_unique(array_column($semua_aparatur, 'jabatan')));

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
        <h2 class="admin-topbar__title">Aparatur Desa</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name">admin@desagilang.go.id</span>
        <span class="admin-topbar__avatar">AD</span>
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

<script src="/js/auth.js"></script>
<script src="/js/aparatur.js"></script>
<script src="/js/dashboard.js"></script>
<script>
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

  /* Auto-submit form filter saat select berubah */
  document.querySelectorAll('.ap-select').forEach(function (sel) {
    sel.addEventListener('change', function () { sel.closest('form').submit(); });
  });
</script>
</body>
</html>