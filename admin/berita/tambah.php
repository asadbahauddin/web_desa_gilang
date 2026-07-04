<?php
// ===================================================================
// CATATAN UNTUK TAHAP SELANJUTNYA:
// File ini baru dikonversi ke .php dari .html (struktur & tampilan
// tetap sama). Logika simpan berita (addBerita) masih di JS
// (berita.js) karena belum ada database.
//
// Saat database MySQL sudah siap nanti, bagian yang perlu diganti:
//   1. Ubah <form id="beritaForm"> agar submit ke endpoint PHP
//      (method="POST", action="proses-tambah-berita.php") alih-alih
//      ditangani penuh oleh JS.
//   2. Untuk upload gambar sampul, tambahkan <input type="file">
//      + enctype="multipart/form-data" pada form, dan proses upload
//      filenya di PHP sebelum disimpan path/nama filenya ke MySQL.
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
  <title>Tambah Berita — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.png">

  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body data-admin data-page="berita">

  <div class="admin-layout">
    <div id="sidebar-placeholder"></div>

    <div class="admin-main">
      <header class="admin-topbar">
        <div style="display:flex; align-items:center; gap:14px;">
          <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
            <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          </button>
          <h2 class="admin-topbar__title">Tambah Berita</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail">admin@desagilang.go.id</span>
          <span class="admin-topbar__avatar">AD</span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Tambah Berita</h1>
            <p class="text-muted">Buat dan publikasikan berita baru untuk situs desa.</p>
          </div>
          <a href="/admin/berita/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
        </div>

        <form id="beritaForm">
          <div class="admin-form">

            <div class="admin-panel">
              <h3 class="admin-panel__title">Informasi Berita</h3>

              <div class="form-group">
                <label for="judul">Judul Berita</label>
                <input type="text" id="judul" name="judul" required>
              </div>

              <div class="form-group">
                <label for="excerpt">Ringkasan Singkat</label>
                <input type="text" id="excerpt" name="excerpt" required>
              </div>

              <div class="form-group">
                <label for="konten">Isi Berita</label>
                <textarea id="konten" name="konten" required></textarea>
              </div>
            </div>

            <div>
              <div class="admin-panel">
                <h3 class="admin-panel__title">Publikasi</h3>

                <div class="form-group">
                  <label for="kategori">Kategori</label>
                  <select id="kategori" name="kategori" required>
                    <option value="kegiatan">Kegiatan</option>
                    <option value="ekonomi">Ekonomi</option>
                    <option value="pemerintahan">Pemerintahan</option>
                    <option value="sosial">Sosial</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="tanggal">Tanggal Terbit</label>
                  <input type="date" id="tanggal" name="tanggal" required>
                </div>

                <div class="form-group">
                  <label for="status">Status</label>
                  <select id="status" name="status" required>
                    <option value="published">Tayang</option>
                    <option value="draft">Draft</option>
                  </select>
                </div>
              </div>

              <div class="admin-panel">
                <h3 class="admin-panel__title">Gambar Sampul</h3>
                <div class="form-group">
                  <label for="gambar">URL Gambar</label>
                  <input type="text" id="gambar" name="gambar" placeholder="https://...">
                  <p class="text-muted" style="font-size:0.78rem; margin-top:6px;">
                    Sementara pakai URL gambar — upload file asli aktif setelah Tahap 12 (MySQL + penyimpanan file).
                  </p>
                </div>
                <div class="upload-preview" id="imgPreview">
                  <img src="" alt="Pratinjau gambar">
                </div>
              </div>
            </div>

          </div>

          <div class="admin-form__actions">
            <button type="submit" class="btn btn--primary">Simpan Berita</button>
            <a href="/admin/berita/index.php" class="btn btn--ghost">Batal</a>
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
    const gambarInput = document.getElementById('gambar');
    const preview = document.getElementById('imgPreview');

    gambarInput.addEventListener('input', () => {
      preview.querySelector('img').src = gambarInput.value.trim();
      preview.classList.toggle('is-visible', !!gambarInput.value.trim());
    });

    document.getElementById('beritaForm').addEventListener('submit', (e) => {
      e.preventDefault();
      addBerita({
        judul: document.getElementById('judul').value.trim(),
        excerpt: document.getElementById('excerpt').value.trim(),
        konten: document.getElementById('konten').value.trim(),
        kategori: document.getElementById('kategori').value,
        tanggal: document.getElementById('tanggal').value,
        status: document.getElementById('status').value,
        gambar: gambarInput.value.trim(),
      });
      window.location.href = '/admin/berita/index.php?toast=Berita%20berhasil%20disimpan';
    });
  </script>
</body>
</html>
