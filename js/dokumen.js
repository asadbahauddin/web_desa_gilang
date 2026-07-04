/* ==========================================================================
   DATA LAYER — Dokumen (mock localStorage)
   ----------------------------------------------------------------------
   GANTI DI TAHAP 12: ganti seluruh isi fungsi di bawah dengan pemanggilan
   Firestore (collection(db,'dokumen'), getDocs, addDoc, updateDoc, deleteDoc).
   Nama fungsi & bentuk data SENGAJA dipertahankan sama supaya halaman HTML
   tidak perlu diubah saat integrasi Firebase nanti.
   ========================================================================== */

const DOKUMEN_STORAGE_KEY = 'desaGilang_dokumen';

function seedDokumenIfEmpty() {
  if (localStorage.getItem(DOKUMEN_STORAGE_KEY)) return;
  const seed = [
    { id: 'dok-1', nama: 'Syarat Pengajuan KTP Baru', kategori: 'persyaratan', tanggal: '2026-01-10', file: '' },
    { id: 'dok-2', nama: 'Syarat Pembuatan Surat Keterangan Tidak Mampu (SKTM)', kategori: 'persyaratan', tanggal: '2025-11-22', file: '' },
    { id: 'dok-3', nama: 'Jadwal Posyandu Bulan Juni 2026', kategori: 'kesehatan', tanggal: '2026-06-01', file: '' },
    { id: 'dok-4', nama: 'Informasi Imunisasi Anak Tahun 2026', kategori: 'kesehatan', tanggal: '2026-05-15', file: '' },
    { id: 'dok-5', nama: 'Pengumuman Penerimaan Bantuan Sosial', kategori: 'pengumuman', tanggal: '2026-06-10', file: '' },
    { id: 'dok-6', nama: 'Pengumuman Libur Pelayanan Kantor Desa', kategori: 'pengumuman', tanggal: '2026-05-28', file: '' },
    { id: 'dok-7', nama: 'APBDes Tahun Anggaran 2026', kategori: 'dokumen-desa', tanggal: '2026-01-15', file: '' },
    { id: 'dok-8', nama: 'Peraturan Desa No. 3 Tahun 2025', kategori: 'dokumen-desa', tanggal: '2025-12-02', file: '' },
    { id: 'dok-9', nama: 'SK Penetapan Perangkat Desa', kategori: 'dokumen-desa', tanggal: '2025-01-10', file: '' },
  ];
  localStorage.setItem(DOKUMEN_STORAGE_KEY, JSON.stringify(seed));
}

function getAllDokumen() {
  seedDokumenIfEmpty();
  return JSON.parse(localStorage.getItem(DOKUMEN_STORAGE_KEY) || '[]');
}

function getDokumenById(id) {
  return getAllDokumen().find((d) => d.id === id) || null;
}

function addDokumen(data) {
  const items = getAllDokumen();
  const newItem = { id: `dok-${Date.now()}`, ...data };
  items.unshift(newItem);
  localStorage.setItem(DOKUMEN_STORAGE_KEY, JSON.stringify(items));
  return newItem;
}

function updateDokumen(id, data) {
  const items = getAllDokumen().map((d) => (d.id === id ? { ...d, ...data } : d));
  localStorage.setItem(DOKUMEN_STORAGE_KEY, JSON.stringify(items));
}

function deleteDokumen(id) {
  const items = getAllDokumen().filter((d) => d.id !== id);
  localStorage.setItem(DOKUMEN_STORAGE_KEY, JSON.stringify(items));
}

/* ========================================================================== */

/* ==========================================================================
   DOKUMEN.JS — Filter kategori & pencarian pada tabel Dokumen Publik
   ========================================================================== */

function initDokumenFilter() {
  const chips = document.querySelectorAll('.filter-chip');
  const searchInput = document.getElementById('dokumenSearch');
  const rows = document.querySelectorAll('.dokumen-table tbody tr');
  const emptyState = document.getElementById('dokumenEmpty');

  if (!chips.length || !rows.length) return;

  let activeCategory = 'semua';

  function applyFilter() {
    const keyword = (searchInput?.value || '').toLowerCase().trim();
    let visibleCount = 0;

    rows.forEach((row) => {
      const category = row.getAttribute('data-category') || '';
      const name = row.getAttribute('data-name') || row.textContent.toLowerCase();

      const matchCategory = activeCategory === 'semua' || category === activeCategory;
      const matchKeyword = !keyword || name.toLowerCase().includes(keyword);

      const isVisible = matchCategory && matchKeyword;
      row.style.display = isVisible ? '' : 'none';
      if (isVisible) visibleCount += 1;
    });

    if (emptyState) {
      emptyState.classList.toggle('is-visible', visibleCount === 0);
    }
  }

  chips.forEach((chip) => {
    chip.addEventListener('click', () => {
      chips.forEach((c) => c.classList.remove('is-active'));
      chip.classList.add('is-active');
      activeCategory = chip.getAttribute('data-filter') || 'semua';
      applyFilter();
    });
  });

  searchInput?.addEventListener('input', applyFilter);
}

document.addEventListener('DOMContentLoaded', initDokumenFilter);