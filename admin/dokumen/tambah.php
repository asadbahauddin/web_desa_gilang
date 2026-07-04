<?php
// ===================================================================
// CATATAN UNTUK TAHAP SELANJUTNYA:
// File ini baru dikonversi ke .php dari .html (struktur & tampilan
// tetap sama). Logika simpan dokumen (addDokumen) masih di JS
// (dokumen.js) karena belum ada database, dan berkas PDF belum
// benar-benar diupload (hanya nama file yang disimpan).
//
// Saat database MySQL sudah siap nanti:
//   1. Ubah <form id="dokumenForm"> agar submit ke endpoint PHP
//      (method="POST", action="proses-tambah-dokumen.php",
//      enctype="multipart/form-data" karena ada file).
//   2. Proses upload file PDF di PHP (validasi tipe & ukuran file,
//      simpan ke folder server, misal /uploads/dokumen/), lalu
//      simpan path/nama filenya ke MySQL lewat query INSERT
//      (pakai prepared statement).
//   3. Tampilkan pesan sukses/gagal dari PHP (misal lewat session
//      flash message) setelah redirect ke index.php.
// ===================================================================

session_start();
if (empty($_SESSION['admin'])) {
  header('Location: /admin/login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Dokumen — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.png">

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body data-admin data-page="dokumen">

  <div class="admin-layout">
    <div id="sidebar-placeholder"></div>

    <div class="admin-main">
      <header class="admin-topbar">
        <div style="display:flex; align-items:center; gap:14px;">
          <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
            <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          </button>
          <h2 class="admin-topbar__title">Tambah Dokumen</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail">admin@desagilang.go.id</span>
          <span class="admin-topbar__avatar">AD</span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Tambah Dokumen Baru</h1>
            <p class="text-muted">Unggah dokumen resmi untuk ditampilkan di halaman Dokumen Publik.</p>
          </div>
          <a href="/admin/dokumen/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
        </div>

        <form id="dokumenForm" style="max-width: 640px;">
          <div class="admin-panel">
            <h3 class="admin-panel__title">Informasi Dokumen</h3>

            <div class="form-group">
              <label for="nama">Nama Dokumen</label>
              <input type="text" id="nama" name="nama" placeholder="Contoh: APBDes Tahun Anggaran 2027" required>
            </div>

            <div class="form-group">
              <label for="kategori">Kategori</label>
              <select id="kategori" name="kategori" required>
                <option value="persyaratan">Persyaratan Pelayanan</option>
                <option value="kesehatan">Informasi Kesehatan</option>
                <option value="pengumuman">Pengumuman</option>
                <option value="dokumen-desa">Dokumen Desa</option>
              </select>
            </div>

            <div class="form-group">
              <label for="tanggal">Tanggal Dokumen</label>
              <input type="date" id="tanggal" name="tanggal" required>
            </div>

            <div class="form-group">
              <label>Berkas Dokumen (PDF)</label>
              <div class="upload-dropzone" id="dropzone">
                <svg viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7 9m5-5l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a2 2 0 002 2h12a2 2 0 002-2v-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                <p><span>Klik untuk pilih file</span> atau seret PDF ke sini</p>
              </div>
              <input type="file" id="fileInput" accept="application/pdf" style="display:none;">
              <p class="text-muted" id="fileName" style="font-size:0.82rem; margin-top:8px;"></p>
              <p class="text-muted" style="font-size:0.78rem; margin-top:6px;">
                Sementara hanya menyimpan nama file — upload &amp; penyimpanan PDF asli aktif setelah Tahap 12 (MySQL + penyimpanan file).
              </p>
            </div>
          </div>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Simpan Dokumen</button>
            <a href="/admin/dokumen/index.php" class="btn btn--ghost">Batal</a>
          </div>
        </form>

      </main>
    </div>
  </div>

  <script src="/js/auth.js"></script>
  <script src="/js/berita.js"></script>
  <script src="/js/dokumen.js"></script>
  <script src="/js/galeri.js"></script>
  <script src="/js/dashboard.js"></script>

  <script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const fileNameLabel = document.getElementById('fileName');
    let selectedFileName = '';

    dropzone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
      if (fileInput.files[0]) {
        selectedFileName = fileInput.files[0].name;
        fileNameLabel.textContent = `📄 ${selectedFileName}`;
      }
    });

    document.getElementById('dokumenForm').addEventListener('submit', (e) => {
      e.preventDefault();
      addDokumen({
        nama: document.getElementById('nama').value.trim(),
        kategori: document.getElementById('kategori').value,
        tanggal: document.getElementById('tanggal').value,
        file: selectedFileName,
      });
      window.location.href = '/admin/dokumen/index.php?toast=Dokumen%20berhasil%20ditambahkan';
    });
  </script>
</body>
</html>