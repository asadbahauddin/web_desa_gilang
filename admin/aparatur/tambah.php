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
//  Opsi dropdown
// ============================================================
$opsi_jabatan = [
  'Kepala Desa', 'Sekretaris Desa', 'Kaur Keuangan', 'Kaur Umum & TU',
  'Kaur Perencanaan', 'Kasi Pemerintahan', 'Kasi Kesejahteraan',
  'Kasi Pelayanan', 'Kepala Dusun I', 'Kepala Dusun II',
  'Kepala Dusun III', 'Kepala Dusun IV',
];
$opsi_agama      = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
$opsi_pendidikan = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
$opsi_jk         = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
$opsi_status     = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'];

// ============================================================
//  Proses form (POST)
//
//  Contoh simpan ke DB (PDO):
//  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $errors = [];
//    $nama    = trim($_POST['nama']    ?? '');
//    $jabatan = trim($_POST['jabatan'] ?? '');
//    if (!$nama)    $errors[] = 'Nama lengkap wajib diisi.';
//    if (!$jabatan) $errors[] = 'Jabatan wajib dipilih.';
//
//    if (empty($errors)) {
//      $fotoPath = '';
//      if (!empty($_FILES['foto']['tmp_name'])) {
//        $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
//        $fotoName = uniqid('ap_') . '.' . $ext;
//        $fotoPath = '/uploads/aparatur/' . $fotoName;
//        move_uploaded_file($_FILES['foto']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $fotoPath);
//      }
//      $stmt = $pdo->prepare("
//        INSERT INTO aparatur_desa
//          (nama, nip, jabatan, status, jk, agama, pendidikan, ttl, mulai, akhir, email, hp, alamat, bio, foto)
//        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
//      ");
//      $stmt->execute([
//        $nama,                       trim($_POST['nip']        ?? ''),
//        $jabatan,                    $_POST['status']           ?? 'aktif',
//        $_POST['jk']        ?? '',   $_POST['agama']            ?? '',
//        $_POST['pendidikan']?? '',   $_POST['ttl']    ?: null,
//        $_POST['mulai']     ?: null, $_POST['akhir']  ?: null,
//        trim($_POST['email']  ?? ''),trim($_POST['hp']   ?? ''),
//        trim($_POST['alamat'] ?? ''),trim($_POST['bio']  ?? ''),
//        $fotoPath,
//      ]);
//      header('Location: /admin/aparatur/index.php?saved=1');
//      exit;
//    }
//  }
// ============================================================

// Nilai POST untuk repopulate form setelah error (aktif saat pakai DB)
$post   = $_POST ?? [];
$errors = [];

// ============================================================
//  Helper: render <option> dengan selected otomatis
// ============================================================
function options_list(array $list, string $selected, bool $assoc = false): void {
  foreach ($list as $val => $lbl) {
    if (!$assoc) { $val = $lbl; }
    $sel = ($val === $selected) ? ' selected' : '';
    echo '<option value="' . htmlspecialchars((string)$val) . '"' . $sel . '>'
       . htmlspecialchars((string)$lbl) . '</option>';
  }
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
$p = fn(string $key, string $default = '') => htmlspecialchars($post[$key] ?? $default);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Aparatur — Panel Admin Desa Gilang</title>
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
    .form-card{background:#fff;border:1px solid var(--ap-line);border-radius:14px;overflow:hidden;}
    .form-section{padding:24px 28px;border-bottom:1px solid var(--ap-line);}
    .form-section:last-child{border-bottom:none;}
    .form-section__title{font-family:'Fraunces',serif;font-size:15px;font-weight:600;color:var(--ap-ink);margin:0 0 18px;display:flex;align-items:center;gap:8px;}
    .form-section__title svg{width:17px;height:17px;stroke:var(--sb-accent);}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px 20px;}
    .form-grid--full{grid-column:1/-1;}
    @media(max-width:640px){.form-grid{grid-template-columns:1fr;}}
    .form-group{display:flex;flex-direction:column;gap:6px;}
    .form-group label{font-size:12.5px;font-weight:600;color:var(--ap-ink-muted);letter-spacing:.02em;font-family:'IBM Plex Mono',monospace;}
    .form-group label span.req{color:#C9583F;}
    .form-control{padding:9px 12px;border-radius:9px;border:1px solid var(--ap-line);font-family:'Plus Jakarta Sans',sans-serif;font-size:13.5px;color:var(--ap-ink);background:#fff;transition:border-color .15s ease,box-shadow .15s ease;width:100%;box-sizing:border-box;}
    .form-control:focus{outline:none;border-color:var(--sb-accent);box-shadow:0 0 0 3px rgba(200,137,59,.14);}
    .form-control--error{border-color:var(--sb-danger);}
    select.form-control{cursor:pointer;}
    textarea.form-control{resize:vertical;min-height:90px;}
    .foto-upload-wrap{display:flex;align-items:flex-start;gap:24px;flex-wrap:wrap;}
    .foto-circle{width:100px;height:100px;border-radius:50%;border:2px dashed var(--ap-line);background:#FAF8F3;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:border-color .15s ease,background .15s ease;overflow:hidden;flex-shrink:0;position:relative;}
    .foto-circle:hover{border-color:var(--sb-accent);background:#FEF8EF;}
    .foto-circle img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;display:none;}
    .foto-circle.has-image img{display:block;}
    .foto-circle.has-image .foto-icon{display:none;}
    .foto-icon{display:flex;flex-direction:column;align-items:center;gap:5px;pointer-events:none;}
    .foto-icon svg{width:24px;height:24px;stroke:#C8C0A8;}
    .foto-icon span{font-size:11px;color:var(--ap-ink-muted);font-family:'IBM Plex Mono',monospace;}
    .foto-upload-info{flex:1;min-width:200px;}
    .foto-upload-info h4{font-size:13.5px;font-weight:600;color:var(--ap-ink);margin:0 0 6px;}
    .foto-upload-info p{font-size:12.5px;color:var(--ap-ink-muted);margin:0 0 12px;line-height:1.55;}
    .foto-upload-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:9px;border:1.5px solid var(--sb-accent);color:var(--sb-accent);font-size:13px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;background:none;transition:background .15s ease,color .15s ease;}
    .foto-upload-btn:hover{background:var(--sb-accent);color:#fff;}
    .foto-upload-btn svg{width:15px;height:15px;stroke:currentColor;}
    input#fotoFile{display:none;}
    .foto-upload-nama{display:none;font-size:12px;color:var(--ap-ink-muted);margin-top:8px;font-family:'IBM Plex Mono',monospace;}
    .foto-upload-nama.visible{display:block;}
    .foto-hapus-btn{display:none;font-size:12px;color:var(--ap-off-text);background:none;border:none;cursor:pointer;padding:0;font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;margin-top:6px;}
    .foto-hapus-btn.visible{display:block;}
    .form-actions{display:flex;justify-content:flex-end;gap:10px;padding:20px 28px;border-top:1px solid var(--ap-line);background:#FAF8F3;}
    .btn-batal{padding:9px 20px;border-radius:9px;border:1px solid var(--ap-line);background:#fff;color:var(--ap-ink);font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s ease;}
    .btn-batal:hover{background:#F0EDE5;}
    .btn-simpan{padding:9px 22px;border-radius:9px;border:none;background:var(--sb-bg);color:#fff;font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s ease;}
    .btn-simpan:hover{background:#2e4a32;}
    .ap-breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--ap-ink-muted);margin-bottom:18px;flex-wrap:wrap;}
    .ap-breadcrumb a{color:var(--ap-ink-muted);text-decoration:none;}
    .ap-breadcrumb a:hover{color:var(--ap-ink);}
    .ap-breadcrumb span{color:var(--ap-ink);font-weight:600;}
    .ap-breadcrumb svg{width:14px;height:14px;stroke:currentColor;}
    .form-errors{background:var(--ap-off-bg);border:1px solid #E0BBAF;border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:13.5px;color:var(--ap-off-text);}
    .form-errors ul{margin:6px 0 0 16px;padding:0;}
    .form-errors li{margin-bottom:3px;}
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
        <h2 class="admin-topbar__title">Tambah Aparatur</h2>
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
        <span>Tambah Aparatur</span>
      </nav>

      <div class="admin-page-header">
        <div>
          <h1>Tambah Aparatur</h1>
          <p class="text-muted">Isi data lengkap perangkat desa yang akan ditambahkan.</p>
        </div>
      </div>

      <!-- Error list (muncul saat validasi PHP gagal) -->
      <?php if (!empty($errors)) : ?>
      <div class="form-errors" role="alert">
        <strong>Mohon perbaiki kesalahan berikut:</strong>
        <ul>
          <?php foreach ($errors as $err) : ?>
          <li><?php echo htmlspecialchars($err); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- ============ FORM ============ -->
      <form class="form-card" method="POST" enctype="multipart/form-data" action="">

        <!-- Foto -->
        <div class="form-section">
          <h3 class="form-section__title">
            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke-width="1.6"/><circle cx="12" cy="12" r="3" stroke-width="1.6"/><path d="M3 9h2M19 9h2" stroke-width="1.6" stroke-linecap="round"/></svg>
            Foto Aparatur
          </h3>
          <div class="foto-upload-wrap">
            <div class="foto-circle" id="fotoCircle" title="Klik untuk pilih foto"
                 onclick="document.getElementById('fotoFile').click()">
              <div class="foto-icon">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke-width="1.6"/><path d="M4 20c1-4 4-6 8-6s7 2 8 6" stroke-width="1.6" stroke-linecap="round"/></svg>
                <span>Pilih Foto</span>
              </div>
              <img id="fotoPreview" src="" alt="Preview">
            </div>
            <div class="foto-upload-info">
              <h4>Unggah Foto Aparatur</h4>
              <p>Format JPG, PNG, atau WEBP.<br>Ukuran maksimal 2 MB.<br>Rasio 1:1 (persegi) disarankan untuk hasil terbaik.</p>
              <button type="button" class="foto-upload-btn" onclick="document.getElementById('fotoFile').click()">
                <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Pilih File
              </button>
              <input type="file" id="fotoFile" name="foto" accept="image/jpeg,image/png,image/webp">
              <p class="foto-upload-nama" id="fotoNama"></p>
              <button type="button" class="foto-hapus-btn" id="fotoHapus">✕ Hapus foto</button>
            </div>
          </div>
        </div>

        <!-- Data Diri -->
        <div class="form-section">
          <h3 class="form-section__title">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.4" stroke-width="1.6"/><path d="M4.5 20c1-3.8 4-5.8 7.5-5.8s6.5 2 7.5 5.8" stroke-width="1.6" stroke-linecap="round"/></svg>
            Data Diri
          </h3>
          <div class="form-grid">
            <div class="form-group form-grid--full">
              <label for="inputNama">NAMA LENGKAP <span class="req">*</span></label>
              <input type="text" class="form-control <?php echo in_array('Nama lengkap wajib diisi.', $errors) ? 'form-control--error' : ''; ?>"
                     id="inputNama" name="nama"
                     value="<?php echo $p('nama'); ?>"
                     placeholder="Contoh: Ahmad Supriyadi, S.Sos." required>
            </div>
            <div class="form-group">
              <label for="inputNip">NIP</label>
              <input type="text" class="form-control" id="inputNip" name="nip"
                     value="<?php echo $p('nip'); ?>" placeholder="18 digit NIP" maxlength="18">
            </div>
            <div class="form-group">
              <label for="inputTtl">TANGGAL LAHIR</label>
              <input type="date" class="form-control" id="inputTtl" name="ttl"
                     value="<?php echo $p('ttl'); ?>">
            </div>
            <div class="form-group">
              <label for="inputJk">JENIS KELAMIN</label>
              <select class="form-control" id="inputJk" name="jk">
                <option value="">— Pilih —</option>
                <?php options_list($opsi_jk, $p('jk'), true); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputAgama">AGAMA</label>
              <select class="form-control" id="inputAgama" name="agama">
                <option value="">— Pilih —</option>
                <?php options_list($opsi_agama, $p('agama')); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputPendidikan">PENDIDIKAN TERAKHIR</label>
              <select class="form-control" id="inputPendidikan" name="pendidikan">
                <option value="">— Pilih —</option>
                <?php options_list($opsi_pendidikan, $p('pendidikan')); ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Jabatan & Status -->
        <div class="form-section">
          <h3 class="form-section__title">
            <svg viewBox="0 0 24 24" fill="none"><rect x="2" y="7" width="20" height="14" rx="2" stroke-width="1.6"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke-width="1.6" stroke-linejoin="round"/></svg>
            Jabatan &amp; Status
          </h3>
          <div class="form-grid">
            <div class="form-group">
              <label for="inputJabatan">JABATAN <span class="req">*</span></label>
              <select class="form-control <?php echo in_array('Jabatan wajib dipilih.', $errors) ? 'form-control--error' : ''; ?>"
                      id="inputJabatan" name="jabatan" required>
                <option value="">— Pilih Jabatan —</option>
                <?php options_list($opsi_jabatan, $p('jabatan')); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputStatus">STATUS <span class="req">*</span></label>
              <select class="form-control" id="inputStatus" name="status" required>
                <?php options_list($opsi_status, $p('status', 'aktif'), true); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputMulai">MASA JABATAN MULAI</label>
              <input type="date" class="form-control" id="inputMulai" name="mulai"
                     value="<?php echo $p('mulai'); ?>">
            </div>
            <div class="form-group">
              <label for="inputAkhir">MASA JABATAN BERAKHIR</label>
              <input type="date" class="form-control" id="inputAkhir" name="akhir"
                     value="<?php echo $p('akhir'); ?>">
            </div>
          </div>
        </div>

        <!-- Kontak & Alamat -->
        <div class="form-section">
          <h3 class="form-section__title">
            <svg viewBox="0 0 24 24" fill="none"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z" stroke-width="1.6"/><circle cx="12" cy="10" r="3" stroke-width="1.6"/></svg>
            Kontak &amp; Alamat
          </h3>
          <div class="form-grid">
            <div class="form-group">
              <label for="inputEmail">EMAIL</label>
              <input type="email" class="form-control" id="inputEmail" name="email"
                     value="<?php echo $p('email'); ?>" placeholder="contoh@email.com">
            </div>
            <div class="form-group">
              <label for="inputHp">NOMOR HP / WA</label>
              <input type="tel" class="form-control" id="inputHp" name="hp"
                     value="<?php echo $p('hp'); ?>" placeholder="08xx-xxxx-xxxx">
            </div>
            <div class="form-group form-grid--full">
              <label for="inputAlamat">ALAMAT</label>
              <textarea class="form-control" id="inputAlamat" name="alamat"
                        placeholder="Alamat lengkap tempat tinggal aparatur..."><?php echo $p('alamat'); ?></textarea>
            </div>
          </div>
        </div>

        <!-- Keterangan Tambahan -->
        <div class="form-section">
          <h3 class="form-section__title">
            <svg viewBox="0 0 24 24" fill="none"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 13h8M8 17h5" stroke-width="1.6" stroke-linecap="round"/></svg>
            Keterangan Tambahan
          </h3>
          <div class="form-group">
            <label for="inputBio">BIO / DESKRIPSI SINGKAT</label>
            <textarea class="form-control" id="inputBio" name="bio" rows="3"
                      placeholder="Tuliskan bio singkat atau catatan tentang aparatur ini..."><?php echo $p('bio'); ?></textarea>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <a href="/admin/aparatur/index.php" class="btn-batal">Batal</a>
          <button type="submit" class="btn-simpan">Simpan Aparatur</button>
        </div>

      </form>
    </main>
  </div>
</div>

<script src="/js/auth.js"></script>
<script src="/js/aparatur.js"></script>
<script src="/js/dashboard.js"></script>
<script>
  /* Preview & hapus foto */
  var fotoFile   = document.getElementById('fotoFile');
  var fotoCircle = document.getElementById('fotoCircle');
  var fotoPreview= document.getElementById('fotoPreview');
  var fotoNama   = document.getElementById('fotoNama');
  var fotoHapus  = document.getElementById('fotoHapus');

  fotoFile.addEventListener('change', function () {
    var file = fotoFile.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
      alert('Ukuran file maksimal 2 MB.');
      fotoFile.value = '';
      return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
      fotoPreview.src = e.target.result;
      fotoCircle.classList.add('has-image');
      fotoNama.textContent = file.name;
      fotoNama.classList.add('visible');
      fotoHapus.classList.add('visible');
    };
    reader.readAsDataURL(file);
  });

  fotoHapus.addEventListener('click', function () {
    fotoFile.value = '';
    fotoPreview.src = '';
    fotoCircle.classList.remove('has-image');
    fotoNama.textContent = '';
    fotoNama.classList.remove('visible');
    fotoHapus.classList.remove('visible');
  });

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