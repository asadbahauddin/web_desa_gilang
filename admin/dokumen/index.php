<?php
// ===================================================================
// CATATAN UNTUK TAHAP SELANJUTNYA:
// File ini baru dikonversi ke .php dari .html (struktur & tampilan
// tetap sama). Data dokumen masih diambil & dihapus lewat JS
// (getAllDokumen, deleteDokumen di dokumen.js) karena belum ada
// database.
//
// Saat database MySQL sudah siap nanti:
//   1. Ganti getAllDokumen() dengan query SELECT dari PHP, lalu
//      render <tbody> langsung di PHP (foreach), atau tetap pakai
//      AJAX (fetch) ke endpoint PHP yang mengembalikan JSON.
//   2. Tombol hapus sebaiknya memanggil endpoint PHP
//      (proses-hapus-dokumen.php) yang menjalankan DELETE FROM
//      dokumen WHERE id = ... (pakai prepared statement), termasuk
//      menghapus file dokumen fisik di server jika ada.
//   3. Fitur pencarian bisa tetap di client-side (JS) seperti sekarang,
//      atau dipindah ke query SQL (LIKE) kalau datanya sudah banyak.
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
  <title>Kelola Dokumen — Panel Admin Desa Gilang</title>
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
          <h2 class="admin-topbar__title">Kelola Dokumen</h2>
        </div>
        <div class="admin-topbar__user">
          <span class="admin-topbar__name" id="topbarEmail">admin@desagilang.go.id</span>
          <span class="admin-topbar__avatar">AD</span>
        </div>
      </header>

      <main class="admin-content">

        <div class="admin-page-header">
          <div>
            <h1>Daftar Dokumen</h1>
            <p class="text-muted">Kelola dokumen publik yang tampil di halaman Dokumen Publik.</p>
          </div>
          <a href="/admin/dokumen/tambah.php" class="btn btn--primary">+ Tambah Dokumen</a>
        </div>

        <div class="admin-toolbar">
          <div class="admin-search">
            <svg viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="5.2" stroke="currentColor" stroke-width="1.5"/><path d="M14 14l-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" id="searchInput" placeholder="Cari nama dokumen...">
          </div>
        </div>

        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Nama Dokumen</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="dokumenTableBody"></tbody>
          </table>

          <div class="admin-empty" id="emptyState">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
            <p>Belum ada dokumen yang cocok. Coba kata kunci lain atau tambah dokumen baru.</p>
          </div>
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
    const DOKUMEN_KATEGORI_LABEL = {
      persyaratan: 'Persyaratan Pelayanan', kesehatan: 'Informasi Kesehatan', pengumuman: 'Pengumuman', 'dokumen-desa': 'Dokumen Desa',
    };

    function formatTanggalDok(iso) {
      if (!iso) return '-';
      return new Date(iso).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function renderDokumenTable() {
      const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
      const tbody = document.getElementById('dokumenTableBody');
      const emptyState = document.getElementById('emptyState');

      const items = getAllDokumen().filter((d) => d.nama.toLowerCase().includes(keyword));

      tbody.innerHTML = items.map((d) => `
        <tr>
          <td style="font-weight:600; color:var(--color-forest);">${d.nama}</td>
          <td><span class="dokumen-badge">${DOKUMEN_KATEGORI_LABEL[d.kategori] || d.kategori}</span></td>
          <td class="font-mono">${formatTanggalDok(d.tanggal)}</td>
          <td>
            <div class="admin-table__actions">
              <a href="/admin/dokumen/edit.php?id=${d.id}" class="btn-icon btn-icon--edit" aria-label="Edit">
                <svg viewBox="0 0 24 24" fill="none"><path d="M4 20h4l11-11-4-4L4 16v4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
              </a>
              <button class="btn-icon btn-icon--delete" data-delete-id="${d.id}" aria-label="Hapus">
                <svg viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
            </div>
          </td>
        </tr>
      `).join('');

      emptyState.classList.toggle('is-visible', items.length === 0);

      tbody.querySelectorAll('[data-delete-id]').forEach((btn) => {
        btn.addEventListener('click', () => {
          if (confirm('Hapus dokumen ini? Tindakan tidak dapat dibatalkan.')) {
            deleteDokumen(btn.getAttribute('data-delete-id'));
            renderDokumenTable();
            showToast('Dokumen berhasil dihapus.');
          }
        });
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      renderDokumenTable();
      document.getElementById('searchInput').addEventListener('input', renderDokumenTable);
    });
  </script>
</body>
</html>