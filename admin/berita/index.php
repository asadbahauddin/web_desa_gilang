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
//  Data berita — ganti dengan query DB
//
//  Contoh query PDO dengan filter:
//  $where  = ['1=1'];
//  $params = [];
//  if ($filter_cari) {
//    $where[]  = 'judul LIKE ?';
//    $params[] = '%' . $filter_cari . '%';
//  }
//  $stmt = $pdo->prepare("
//    SELECT id, judul, kategori, gambar, tanggal, status
//    FROM berita
//    WHERE " . implode(' AND ', $where) . "
//    ORDER BY tanggal DESC
//  ");
//  $stmt->execute($params);
//  $berita_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ============================================================
$semua_berita = [
  ['id'=>1,'judul'=>'Musyawarah Desa Bahas Rencana Pembangunan 2025',  'kategori'=>'pemerintahan','gambar'=>'https://picsum.photos/seed/b1/80/60','tanggal'=>'2025-03-10','status'=>'published'],
  ['id'=>2,'judul'=>'Pelatihan UMKM Bersama Dinas Koperasi',           'kategori'=>'ekonomi',     'gambar'=>'https://picsum.photos/seed/b2/80/60','tanggal'=>'2025-03-05','status'=>'published'],
  ['id'=>3,'judul'=>'Gotong Royong Bersih Desa Sambut HUT RI',         'kategori'=>'kegiatan',    'gambar'=>'https://picsum.photos/seed/b3/80/60','tanggal'=>'2025-02-28','status'=>'published'],
  ['id'=>4,'judul'=>'Bantuan Sosial untuk Warga Kurang Mampu Disalurkan','kategori'=>'sosial',    'gambar'=>'https://picsum.photos/seed/b4/80/60','tanggal'=>'2025-02-20','status'=>'published'],
  ['id'=>5,'judul'=>'Rencana Pembangunan Jalan Desa Tahap II',          'kategori'=>'pemerintahan','gambar'=>'https://picsum.photos/seed/b5/80/60','tanggal'=>'2025-02-15','status'=>'draft'],
  ['id'=>6,'judul'=>'Festival Budaya Desa Gilang 2025',                 'kategori'=>'kegiatan',   'gambar'=>'https://picsum.photos/seed/b6/80/60','tanggal'=>'2025-02-01','status'=>'draft'],
];

// Terapkan filter di PHP (hapus saat pakai query DB)
$berita_list = array_values(array_filter($semua_berita, function ($b) use ($filter_cari) {
  if ($filter_cari && stripos($b['judul'], $filter_cari) === false) return false;
  return true;
}));

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
$nav_groups = [
  [
    'label' => null,
    'links' => [
      ['href'=>'/web-desa-gilang/admin/dashboard.php','nav'=>'dashboard','label'=>'Dashboard',
       'icon'=>'<rect x="3" y="3" width="8" height="8" rx="1.5" stroke-width="1.6"/><rect x="13" y="3" width="8" height="5" rx="1.5" stroke-width="1.6"/><rect x="13" y="11" width="8" height="10" rx="1.5" stroke-width="1.6"/><rect x="3" y="14" width="8" height="7" rx="1.5" stroke-width="1.6"/>'],
    ],
  ],
  [
    'label' => 'Konten',
    'links' => [
      ['href'=>'/web-desa-gilang/admin/berita/index.php','nav'=>'berita','label'=>'Berita',
       'icon'=>'<rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.6"/><path d="M7 9h10M7 13h10M7 17h6" stroke-width="1.6" stroke-linecap="round"/>'],
      ['href'=>'/web-desa-gilang/admin/dokumen/index.php','nav'=>'dokumen','label'=>'Dokumen',
       'icon'=>'<path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke-width="1.6" stroke-linejoin="round"/>'],
    ],
  ],
  [
    'label' => 'Profil Desa',
    'links' => [
      ['href'=>'/web-desa-gilang/admin/struktur-organisasi/index.php','nav'=>'struktur','label'=>'Struktur Organisasi',
       'icon'=>'<circle cx="12" cy="5" r="2.2" stroke-width="1.6"/><circle cx="6" cy="18" r="2.2" stroke-width="1.6"/><circle cx="18" cy="18" r="2.2" stroke-width="1.6"/><path d="M12 7.2V12M12 12L6 15.8M12 12l6 3.8" stroke-width="1.6" stroke-linecap="round"/>'],
      ['href'=>'/web-desa-gilang/admin/aparatur/index.php','nav'=>'aparatur','label'=>'Aparatur Desa',
       'icon'=>'<circle cx="12" cy="8" r="3.4" stroke-width="1.6"/><path d="M4.5 20c1-3.8 4-5.8 7.5-5.8s6.5 2 7.5 5.8" stroke-width="1.6" stroke-linecap="round"/>'],
    ],
  ],
];

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
      --ap-ok-bg:#EAF3E9; --ap-ok-text:#2F6B3F;
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
        <h2 class="admin-topbar__title">Kelola Berita</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name">admin@desagilang.go.id</span>
        <span class="admin-topbar__avatar">AD</span>
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
              $gambar  = htmlspecialchars($b['gambar']);
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

<script src="/js/auth.js"></script>
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
</script>
</body>
</html>