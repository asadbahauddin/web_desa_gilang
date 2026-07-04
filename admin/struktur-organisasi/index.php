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

// ============================================================
//  Data struktur organisasi — query DB asli
//
//  Struktur Organisasi hanyalah tampilan hierarki dari tabel
//  `aparatur` yang sama, dikelompokkan berdasarkan jabatan.
//  Menambah / mengubah / menghapus orangnya dilakukan lewat
//  menu Aparatur Desa — halaman ini murni tampilan.
// ============================================================
$result   = mysqli_query($conn, "SELECT nama, jabatan, foto FROM aparatur WHERE status = 'aktif' ORDER BY jabatan ASC");
$aparatur = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

$kades  = array_values(array_filter($aparatur, fn($a) => $a['jabatan'] === 'Kepala Desa'))[0]  ?? null;
$sekdes = array_values(array_filter($aparatur, fn($a) => $a['jabatan'] === 'Sekretaris Desa'))[0] ?? null;
$staf   = array_values(array_filter($aparatur, fn($a) => !in_array($a['jabatan'], ['Kepala Desa', 'Sekretaris Desa'])));

// ============================================================
//  Helper
// ============================================================
function inisial(string $nama): string {
  $kata = array_filter(explode(' ', $nama));
  return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($kata, 0, 2))));
}

function render_org_card(array $person, string $extra_class = ''): void {
  $nama    = htmlspecialchars($person['nama']);
  $jabatan = htmlspecialchars($person['jabatan']);
  $foto    = htmlspecialchars($person['foto'] ?? '');
  $class   = trim('org-card ' . $extra_class);
  echo '<div class="' . $class . '">';
  if ($foto) {
    echo '<img src="' . $foto . '" alt="Foto ' . $nama . '">';
  } else {
    echo '<div class="ap-foto-placeholder" style="margin:0 auto 20px;">' . htmlspecialchars(inisial($person['nama'])) . '</div>';
  }
  echo '<h3 style="font-family:\'Fraunces\',serif;font-size:16px;margin:0 0 4px;color:var(--ap-ink);">' . $nama . '</h3>';
  echo '<p style="margin:0;">' . $jabatan . '</p>';
  echo '</div>';
}

require __DIR__ . '/../_nav.php';
$current_page = 'struktur';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struktur Organisasi — Panel Admin Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="icon" href="/assets/logo/logo-desa.jpg">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <link rel="stylesheet" href="/css/struktur.css">

  <?php require __DIR__ . '/../_sidebar-style.php'; ?>
  <style>
    .ap-foto-placeholder{width:64px;height:64px;border-radius:50%;background:var(--sb-accent-soft);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#9A5A1F;flex-shrink:0;}
    .struktur-note{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;background:var(--ap-card);border:1px solid var(--ap-line);border-radius:14px;padding:16px 20px;margin-bottom:24px;}
    .struktur-note p{margin:0;font-size:13.5px;color:var(--ap-ink-muted);}
    .org-tree{margin-top:20px;}
    .org-card{max-width:260px;margin-inline:auto;}
  </style>
</head>
<body data-admin data-page="struktur">
<div class="admin-layout">

  <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>
  <?php require __DIR__ . '/../_sidebar.php'; ?>

  <!-- ============ MAIN CONTENT ============ -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button class="admin-topbar__toggle" id="sidebarToggle" aria-label="Buka menu">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <h2 class="admin-topbar__title">Struktur Organisasi</h2>
      </div>
      <div class="admin-topbar__user">
        <span class="admin-topbar__name"><?php echo htmlspecialchars($__admin_nama); ?></span>
        <span class="admin-topbar__avatar"><?php echo htmlspecialchars($__admin_inisial); ?></span>
      </div>
    </header>

    <main class="admin-content">

      <div class="admin-page-header">
        <div>
          <h1>Struktur Organisasi</h1>
          <p class="text-muted">Pratinjau hierarki pemerintahan Desa Gilang, sesuai yang tampil di halaman publik.</p>
        </div>
      </div>

      <div class="struktur-note">
        <p>Data di sini mengikuti data Aparatur Desa. Untuk menambah, mengubah, atau menghapus orangnya, gunakan menu Aparatur Desa.</p>
        <a href="/admin/aparatur/index.php" class="btn btn--ghost" style="padding:9px 14px;font-size:13px;white-space:nowrap;">Kelola Aparatur Desa →</a>
      </div>

      <div class="ap-card" style="padding:32px 24px;">
        <div class="org-tree">

          <!-- Kepala Desa -->
          <?php if ($kades) : ?>
          <div class="org-level">
            <?php render_org_card($kades, 'kepala'); ?>
          </div>
          <div class="line" aria-hidden="true"></div>
          <?php endif; ?>

          <!-- Sekretaris Desa -->
          <?php if ($sekdes) : ?>
          <div class="org-level">
            <?php render_org_card($sekdes, 'sekdes'); ?>
          </div>
          <?php endif; ?>

          <!-- Staf (Kaur & Kasi) -->
          <?php if (!empty($staf)) : ?>
          <div class="line" aria-hidden="true"></div>
          <div class="org-row">
            <?php foreach ($staf as $anggota) : ?>
              <?php render_org_card($anggota); ?>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

        </div>
      </div>

    </main>
  </div>
</div>

<?php require __DIR__ . '/../_sidebar-script.php'; ?>
</body>
</html>
