<?php
$current = $_SERVER['REQUEST_URI'];

$menu_utama = [
  [
    'page'  => 'dashboard',
    'label' => 'Dashboard',
    'href' => '/admin/dashboard.php',
    'icon'  => '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="3" width="8" height="5" rx="2" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="11" width="8" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="14" width="8" height="7" rx="2" stroke="currentColor" stroke-width="1.6"/></svg>',
  ],
];

$menu_konten = [
  [
    'page'  => 'berita',
    'label' => 'Berita',
    'href'  => '/admin/berita/index.php',
    'icon'  => '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M7 9h10M7 13h10M7 17h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
  ],
  [
    'page'  => 'dokumen',
    'label' => 'Dokumen',
    'href'  => '/admin/dokumen/index.php',
    'icon'  => '<svg viewBox="0 0 24 24" fill="none"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8l-5-5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M14 3v5h5" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
  ],
  [
    'page'  => 'galeri',
    'label' => 'Galeri',
    'href'  => '/admin/galeri/index.php',
    'icon'  => '<svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.6"/><circle cx="8.5" cy="8.5" r="1.8" stroke="currentColor" stroke-width="1.6"/><path d="M21 16l-5.5-5.5L9 17" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
  ],
];

// Helper: cek apakah link aktif
function is_active(string $href, string $current): bool {
  return str_contains($current, parse_url($href, PHP_URL_PATH));
}
?>

<aside class="admin-sidebar">

  <!-- Brand -->
  <div class="admin-sidebar__brand">
    <img src="/assets/logo/logo-desa.jpg"
         alt="Logo Desa Gilang"
         class="admin-sidebar__logo"
         onerror="this.style.display='none'">
    <div>
      <p class="admin-sidebar__brand-title">Desa Gilang</p>
      <p class="admin-sidebar__brand-sub">PANEL ADMIN</p>
    </div>
  </div>

  <!-- Navigasi -->
  <nav class="admin-sidebar__nav">

    <!-- Menu Utama -->
    <span class="admin-sidebar__label">Menu Utama</span>
    <?php foreach ($menu_utama as $item) : ?>
    <a href="<?php echo htmlspecialchars($item['href']); ?>"
       class="admin-sidebar__link <?php echo is_active($item['href'], $current) ? 'active' : ''; ?>"
       data-page="<?php echo htmlspecialchars($item['page']); ?>">
      <?php echo $item['icon']; ?>
      <?php echo htmlspecialchars($item['label']); ?>
    </a>
    <?php endforeach; ?>

    <!-- Konten -->
    <span class="admin-sidebar__label">Konten</span>
    <?php foreach ($menu_konten as $item) : ?>
    <a href="<?php echo htmlspecialchars($item['href']); ?>"
       class="admin-sidebar__link <?php echo is_active($item['href'], $current) ? 'active' : ''; ?>"
       data-page="<?php echo htmlspecialchars($item['page']); ?>">
      <?php echo $item['icon']; ?>
      <?php echo htmlspecialchars($item['label']); ?>
    </a>
    <?php endforeach; ?>

  </nav>

  <!-- Logout -->
  <div class="admin-sidebar__footer">
    <form method="post" action="/admin/logout.php">
      <?php if (function_exists('generate_csrf_token')) : ?>
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
      <?php endif; ?>
      <button type="submit" class="admin-sidebar__logout" id="sidebarLogout">
        <svg viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Keluar
      </button>
    </form>
  </div>

</aside>