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

$stmt = mysqli_prepare($conn, "SELECT * FROM aparatur WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$aparatur = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Redirect jika ID tidak ditemukan
if (!$aparatur) {
  header('Location: /admin/aparatur/index.php');
  exit;
}

$errors = [];

// ============================================================
//  Proses form simpan (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama    = trim($_POST['nama']    ?? '');
  $jabatan = trim($_POST['jabatan'] ?? '');
  if (!$nama)    $errors[] = 'Nama lengkap wajib diisi.';
  if (!$jabatan) $errors[] = 'Jabatan wajib dipilih.';

  if (empty($errors)) {
    $fotoPath = $aparatur['foto'];
    $fotoBaru = upload_gambar('foto');
    if ($fotoBaru) {
      hapus_file_upload($fotoPath);
      $fotoPath = $fotoBaru;
    }
    if (!empty($_POST['hapus_foto'])) {
      hapus_file_upload($fotoPath);
      $fotoPath = '';
    }

    $nip        = trim($_POST['nip'] ?? '');
    $status     = $_POST['status'] ?? 'aktif';
    $jk         = $_POST['jk'] ?? '';
    $agama      = $_POST['agama'] ?? '';
    $pendidikan = $_POST['pendidikan'] ?? '';
    $ttl        = $_POST['ttl'] ?: null;
    $mulai      = $_POST['mulai'] ?: null;
    $akhir      = $_POST['akhir'] ?: null;
    $email      = trim($_POST['email'] ?? '');
    $hp         = trim($_POST['hp'] ?? '');
    $alamat     = trim($_POST['alamat'] ?? '');
    $bio        = trim($_POST['bio'] ?? '');

    $stmt = mysqli_prepare($conn, "
      UPDATE aparatur SET
        nama=?, nip=?, jabatan=?, status=?, jk=?, agama=?, pendidikan=?,
        ttl=?, mulai=?, akhir=?, email=?, hp=?, alamat=?, bio=?, foto=?
      WHERE id=?
    ");
    mysqli_stmt_bind_param(
      $stmt, 'sssssssssssssssi',
      $nama, $nip, $jabatan, $status, $jk, $agama, $pendidikan,
      $ttl, $mulai, $akhir, $email, $hp, $alamat, $bio, $fotoPath, $id
    );
    mysqli_stmt_execute($stmt);
    header('Location: /admin/aparatur/index.php?saved=1');
    exit;
  }
  $aparatur = array_merge($aparatur, $_POST);
}

// ============================================================
//  Opsi dropdown
// ============================================================
$opsi_jabatan = [
  'Kepala Desa','Sekretaris Desa','Kaur Keuangan','Kaur Umum & TU','Kaur Perencanaan',
  'Kasi Pemerintahan','Kasi Kesejahteraan','Kasi Pelayanan',
  'Kepala Dusun I','Kepala Dusun II','Kepala Dusun III','Kepala Dusun IV',
];

$opsi_agama      = ['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'];
$opsi_pendidikan = ['SD','SMP','SMA/SMK','D3','S1','S2','S3'];

// ============================================================
//  Helper: render <option> dengan selected otomatis
// ============================================================
function options_from(array $list, string $selected, bool $use_value = false): void {
  foreach ($list as $item) {
    $val = $use_value ? $item['value'] : $item;
    $lbl = $use_value ? $item['label'] : $item;
    $sel = ($val === $selected) ? ' selected' : '';
    echo '<option value="' . htmlspecialchars($val) . '"' . $sel . '>' . htmlspecialchars($lbl) . '</option>';
  }
}

$h = fn(string $key) => htmlspecialchars($aparatur[$key] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Aparatur — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">

  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
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
    .foto-existing-label{font-size:12px;color:var(--ap-ink-muted);margin-top:8px;font-family:'IBM Plex Mono',monospace;}
    .foto-hapus-btn{display:none;font-size:12px;color:var(--ap-off-text);background:none;border:none;cursor:pointer;padding:0;font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;margin-top:6px;}
    .foto-hapus-btn.visible{display:block;}
    .foto-hapus-lama{display:none;font-size:12px;color:var(--ap-off-text);background:none;border:none;cursor:pointer;padding:0;font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;margin-top:6px;}
    .foto-hapus-lama.visible{display:block;}
    .form-actions{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:20px 28px;border-top:1px solid var(--ap-line);background:#FAF8F3;flex-wrap:wrap;}
    .form-actions-right{display:flex;gap:10px;}
    .btn-batal{padding:9px 20px;border-radius:9px;border:1px solid var(--ap-line);background:#fff;color:var(--ap-ink);font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s ease;}
    .btn-batal:hover{background:#F0EDE5;}
    .btn-simpan{padding:9px 22px;border-radius:9px;border:none;background:var(--sb-bg);color:#fff;font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:background .15s ease;}
    .btn-simpan:hover{background:#2e4a32;}
    .btn-hapus-link{padding:9px 16px;border-radius:9px;border:1px solid #F5C9BF;background:#fff;color:var(--ap-off-text);font-size:13.5px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;text-decoration:none;transition:background .15s ease;}
    .btn-hapus-link:hover{background:var(--ap-off-bg);}
    .ap-breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--ap-ink-muted);margin-bottom:18px;flex-wrap:wrap;}
    .ap-breadcrumb a{color:var(--ap-ink-muted);text-decoration:none;}
    .ap-breadcrumb a:hover{color:var(--ap-ink);}
    .ap-breadcrumb span{color:var(--ap-ink);font-weight:600;}
    .ap-breadcrumb svg{width:14px;height:14px;stroke:currentColor;}
  </style>
</head>
<body data-admin data-page="aparatur">
<div class="admin-layout">

  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
  <?php require __DIR__ . '/../_nav.php'; $current_page = 'aparatur'; require __DIR__ . '/../_sidebar.php'; ?>

  <!-- ============ MAIN CONTENT ============ -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <h2 class="admin-topbar__title">Edit Aparatur</h2>
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
        <span><?php echo $h('nama'); ?></span>
      </nav>

      <div class="admin-page-header">
        <div>
          <h1>Edit Aparatur</h1>
          <p class="text-muted">Memperbarui data: <?php echo $h('nama'); ?></p>
        </div>
      </div>

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
            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke-width="1.6"/><circle cx="12" cy="12" r="3" stroke-width="1.6"/></svg>
            Foto Aparatur
          </h3>
          <div class="foto-upload-wrap">
            <div class="foto-circle <?php echo $aparatur['foto'] ? 'has-image' : ''; ?>"
                 id="fotoCircle" title="Klik untuk ganti foto"
                 onclick="document.getElementById('fotoFile').click()">
              <div class="foto-icon">
                <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.5" stroke-width="1.6"/><path d="M4 20c1-4 4-6 8-6s7 2 8 6" stroke-width="1.6" stroke-linecap="round"/></svg>
                <span>Ganti Foto</span>
              </div>
              <img id="fotoPreview"
                   src="<?php echo $h('foto'); ?>"
                   alt="Foto <?php echo $h('nama'); ?>">
            </div>
            <div class="foto-upload-info">
              <h4>Ganti Foto Aparatur</h4>
              <p>Format JPG, PNG, atau WEBP.<br>Ukuran maksimal 2 MB. Biarkan kosong jika tidak ingin mengganti foto.</p>
              <button type="button" class="foto-upload-btn" onclick="document.getElementById('fotoFile').click()">
                <svg viewBox="0 0 24 24" fill="none"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Pilih File Baru
              </button>
              <input type="file" id="fotoFile" name="foto" accept="image/jpeg,image/png,image/webp">
              <input type="hidden" name="hapus_foto" id="hapusFotoInput" value="">
              <p class="foto-upload-nama" id="fotoNama"></p>
              <p class="foto-existing-label" id="fotoExistingLabel">
                <?php echo $aparatur['foto'] ? 'Foto saat ini tersimpan' : ''; ?>
              </p>
              <button type="button" class="foto-hapus-btn" id="fotoHapus">✕ Batalkan pilihan</button>
              <button type="button" class="foto-hapus-lama <?php echo $aparatur['foto'] ? 'visible' : ''; ?>" id="fotoHapusLama">
                ✕ Hapus foto saat ini
              </button>
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
              <input type="text" class="form-control" id="inputNama" name="nama"
                     value="<?php echo $h('nama'); ?>" placeholder="Nama lengkap aparatur" required>
            </div>
            <div class="form-group">
              <label for="inputNip">NIP</label>
              <input type="text" class="form-control" id="inputNip" name="nip"
                     value="<?php echo $h('nip'); ?>" placeholder="18 digit NIP" maxlength="18">
            </div>
            <div class="form-group">
              <label for="inputTtl">TANGGAL LAHIR</label>
              <input type="date" class="form-control" id="inputTtl" name="ttl"
                     value="<?php echo $h('ttl'); ?>">
            </div>
            <div class="form-group">
              <label for="inputJk">JENIS KELAMIN</label>
              <select class="form-control" id="inputJk" name="jk">
                <option value="">— Pilih —</option>
                <?php options_from([['value'=>'L','label'=>'Laki-laki'],['value'=>'P','label'=>'Perempuan']], $aparatur['jk'] ?? '', true); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputAgama">AGAMA</label>
              <select class="form-control" id="inputAgama" name="agama">
                <option value="">— Pilih —</option>
                <?php options_from($opsi_agama, $aparatur['agama'] ?? ''); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputPendidikan">PENDIDIKAN TERAKHIR</label>
              <select class="form-control" id="inputPendidikan" name="pendidikan">
                <option value="">— Pilih —</option>
                <?php options_from($opsi_pendidikan, $aparatur['pendidikan'] ?? ''); ?>
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
              <select class="form-control" id="inputJabatan" name="jabatan" required>
                <option value="">— Pilih Jabatan —</option>
                <?php options_from($opsi_jabatan, $aparatur['jabatan'] ?? ''); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputStatus">STATUS <span class="req">*</span></label>
              <select class="form-control" id="inputStatus" name="status" required>
                <?php options_from([['value'=>'aktif','label'=>'Aktif'],['value'=>'nonaktif','label'=>'Nonaktif']], $aparatur['status'] ?? 'aktif', true); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="inputMulai">MASA JABATAN MULAI</label>
              <input type="date" class="form-control" id="inputMulai" name="mulai"
                     value="<?php echo $h('mulai'); ?>">
            </div>
            <div class="form-group">
              <label for="inputAkhir">MASA JABATAN BERAKHIR</label>
              <input type="date" class="form-control" id="inputAkhir" name="akhir"
                     value="<?php echo $h('akhir'); ?>">
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
                     value="<?php echo $h('email'); ?>" placeholder="contoh@email.com">
            </div>
            <div class="form-group">
              <label for="inputHp">NOMOR HP / WA</label>
              <input type="tel" class="form-control" id="inputHp" name="hp"
                     value="<?php echo $h('hp'); ?>" placeholder="08xx-xxxx-xxxx">
            </div>
            <div class="form-group form-grid--full">
              <label for="inputAlamat">ALAMAT</label>
              <textarea class="form-control" id="inputAlamat" name="alamat"
                        placeholder="Alamat lengkap tempat tinggal aparatur..."><?php echo $h('alamat'); ?></textarea>
            </div>
          </div>
        </div>

        <!-- Keterangan Tambahan -->
        <div class="form-section">
          <h3 class="form-section__title">
            <svg viewBox="0 0 24 24" fill="none"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5M8 13h8M8 17h5" stroke-width="1.6" stroke-linecap="round"/></svg>
            Keterangan Tambahan
          </h3>
          <div class="form-group">
            <label for="inputBio">BIO / DESKRIPSI SINGKAT</label>
            <textarea class="form-control" id="inputBio" name="bio" rows="3"
                      placeholder="Tuliskan bio singkat atau catatan tentang aparatur ini..."><?php echo $h('bio'); ?></textarea>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <a href="/admin/aparatur/hapus.php?id=<?php echo $aparatur['id']; ?>" class="btn-hapus-link"
             onclick="return confirm('Yakin ingin menghapus aparatur ini?')">
            🗑 Hapus Aparatur Ini
          </a>
          <div class="form-actions-right">
            <a href="/admin/aparatur/index.php" class="btn-batal">Batal</a>
            <button type="submit" class="btn-simpan">Simpan Perubahan</button>
          </div>
        </div>

      </form>
    </main>
  </div>
</div>

<script src="/js/aparatur.js"></script>
<script>
  /* Preview foto baru */
  var fotoFile     = document.getElementById('fotoFile');
  var fotoCircle   = document.getElementById('fotoCircle');
  var fotoPreview  = document.getElementById('fotoPreview');
  var fotoNama     = document.getElementById('fotoNama');
  var fotoHapus    = document.getElementById('fotoHapus');
  var fotoHapusLama = document.getElementById('fotoHapusLama');
  var hapusFotoInput = document.getElementById('hapusFotoInput');
  var fotoExisting = document.getElementById('fotoExistingLabel');
  var fotoAwal     = <?php echo json_encode($aparatur['foto']); ?>;

  fotoFile.addEventListener('change', function () {
    var file = fotoFile.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Ukuran file maksimal 2 MB.'); fotoFile.value = ''; return; }
    var reader = new FileReader();
    reader.onload = function (e) {
      fotoPreview.src = e.target.result;
      fotoCircle.classList.add('has-image');
      fotoNama.textContent = '📎 ' + file.name;
      fotoNama.classList.add('visible');
      fotoHapus.classList.add('visible');
      fotoHapusLama.classList.remove('visible');
      fotoExisting.textContent = '';
      hapusFotoInput.value = '';
    };
    reader.readAsDataURL(file);
  });

  fotoHapus.addEventListener('click', function () {
    fotoFile.value = '';
    if (fotoAwal) {
      fotoPreview.src = fotoAwal;
      fotoCircle.classList.add('has-image');
      fotoExisting.textContent = 'Foto saat ini tersimpan';
      fotoHapusLama.classList.add('visible');
    } else {
      fotoPreview.src = '';
      fotoCircle.classList.remove('has-image');
    }
    fotoNama.textContent = ''; fotoNama.classList.remove('visible');
    fotoHapus.classList.remove('visible');
    hapusFotoInput.value = '';
  });

  fotoHapusLama.addEventListener('click', function () {
    fotoPreview.src = '';
    fotoCircle.classList.remove('has-image');
    fotoExisting.textContent = 'Foto akan dihapus saat disimpan';
    fotoHapusLama.classList.remove('visible');
    fotoFile.value = '';
    fotoNama.textContent = ''; fotoNama.classList.remove('visible');
    fotoHapus.classList.remove('visible');
    hapusFotoInput.value = '1'; /* dikirim ke server sebagai sinyal hapus */
  });

</script>
<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>