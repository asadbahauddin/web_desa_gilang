<?php
// ===================================================================
// CATATAN UNTUK TAHAP SELANJUTNYA:
// Sama seperti edit-berita.php, halaman ini baru dikonversi ke .php.
// Data berita masih diambil & dihapus lewat JS (getBeritaById,
// deleteBerita) karena belum ada database.
//
// Saat MySQL sudah siap nanti:
//   1. Ambil data berita berdasarkan $_GET['id'] via PHP + query SQL
//      untuk ditampilkan di halaman konfirmasi ini.
//   2. Tombol "Hapus" sebaiknya jadi <form method="POST" action="proses-hapus-berita.php">
//      yang mengirim id berita, lalu PHP menjalankan DELETE FROM berita
//      WHERE id = ... (pakai prepared statement).
//   3. Kalau berita punya file gambar yang diupload ke server (bukan
//      cuma URL), hapus juga file fisiknya di server saat record
//      dihapus dari database.
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
  <title>Hapus Berita — Panel Admin Desa Gilang</title>
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
          <h2 class="admin-topbar__title">Hapus Berita</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail">admin@desagilang.go.id</span>
          <span class="admin-topbar__avatar">AD</span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Hapus Berita</h1>
            <p class="text-muted">Periksa kembali sebelum menghapus berita secara permanen.</p>
          </div>
          <a href="/admin/berita/index.php" class="btn btn--ghost">← Kembali ke Daftar</a>
        </div>

        <div class="admin-form">
          <div class="admin-panel">
            <h3 class="admin-panel__title">Detail Berita</h3>

            <div class="upload-preview is-visible" id="imgPreview" style="margin-bottom:16px;">
              <img src="" alt="Pratinjau gambar">
            </div>

            <div class="form-group">
              <label>Judul Berita</label>
              <p id="judul" style="font-weight:600;"></p>
            </div>

            <div class="form-group">
              <label>Ringkasan Singkat</label>
              <p id="excerpt"></p>
            </div>

            <div class="form-group">
              <label>Kategori</label>
              <p id="kategori"></p>
            </div>

            <div class="form-group">
              <label>Tanggal Terbit</label>
              <p id="tanggal"></p>
            </div>

            <div class="form-group">
              <label>Status</label>
              <p id="status"></p>
            </div>

            <div class="admin-alert admin-alert--danger" style="margin-top:8px;">
              ⚠️ Tindakan ini tidak dapat dibatalkan. Berita yang sudah dihapus tidak bisa dikembalikan.
            </div>
          </div>
        </div>

        <div class="admin-form__actions">
          <button type="button" class="btn btn--danger" id="confirmDeleteBtn">Ya, Hapus Berita</button>
          <a href="/admin/berita/index.php" class="btn btn--ghost">Batal</a>
        </div>

      </main>
    </div>
  </div>

  <script src="/js/auth.js"></script>
  <script src="/js/berita.js"></script>
  <script src="/js/dokumen.js"></script>
  <script src="/js/galeri.js"></script>
  <script src="/js/dashboard.js"></script>

  <script>
    const params = new URLSearchParams(window.location.search);
    const beritaId = params.get('id');
    const preview = document.getElementById('imgPreview');

    function loadBerita() {
      const item = beritaId ? getBeritaById(beritaId) : null;
      if (!item) {
        window.location.href = '/admin/berita/index.php?toast=Berita%20tidak%20ditemukan';
        return;
      }
      document.getElementById('judul').textContent = item.judul;
      document.getElementById('excerpt').textContent = item.excerpt;
      document.getElementById('kategori').textContent = item.kategori;
      document.getElementById('tanggal').textContent = item.tanggal;
      document.getElementById('status').textContent = item.status === 'published' ? 'Tayang' : 'Draft';
      preview.querySelector('img').src = item.gambar;
      preview.classList.toggle('is-visible', !!item.gambar);
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
      if (!beritaId) return;
      deleteBerita(beritaId);
      window.location.href = '/admin/berita/index.php?toast=Berita%20berhasil%20dihapus';
    });

    document.addEventListener('DOMContentLoaded', loadBerita);
  </script>
</body>
</html>