<?php
// ============================================================
//  Helper upload berkas — dipakai oleh semua modul admin
//  yang punya upload gambar/PDF (Berita, Aparatur, Galeri, Dokumen).
// ============================================================

function upload_gambar(string $fileKey): ?string {
  if (empty($_FILES[$fileKey]['name']) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
    return null;
  }
  $allowed = ['jpg', 'jpeg', 'png', 'webp'];
  $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed, true)) {
    return null;
  }
  if ($_FILES[$fileKey]['size'] > 2 * 1024 * 1024) {
    return null;
  }
  $namaFile = uniqid('img_', true) . '.' . $ext;
  $tujuan   = __DIR__ . '/../uploads/gambar/' . $namaFile;
  if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $tujuan)) {
    return null;
  }
  return '/uploads/gambar/' . $namaFile;
}

function upload_pdf(string $fileKey): ?string {
  if (empty($_FILES[$fileKey]['name']) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
    return null;
  }
  $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
  if ($ext !== 'pdf') {
    return null;
  }
  if ($_FILES[$fileKey]['size'] > 5 * 1024 * 1024) {
    return null;
  }
  $namaFile = uniqid('dok_', true) . '.pdf';
  $tujuan   = __DIR__ . '/../uploads/pdf/' . $namaFile;
  if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $tujuan)) {
    return null;
  }
  return '/uploads/pdf/' . $namaFile;
}

function hapus_file_upload(?string $path): void {
  if (!$path) {
    return;
  }
  $full = __DIR__ . '/..' . $path;
  if (is_file($full)) {
    unlink($full);
  }
}
