<?php
require_once __DIR__ . '/../../config/database.php';

$result   = mysqli_query($conn, "SELECT nama, jabatan, foto FROM aparatur WHERE status = 'aktif' ORDER BY jabatan ASC, nama ASC");
$aparatur = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

function inisial_aparatur_publik(string $nama): string {
  $kata = array_filter(explode(' ', $nama));
  return strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($kata, 0, 2))));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Aparatur Desa | Desa Gilang</title>

<link rel="stylesheet" href="../../css/style.css">
<link rel="stylesheet" href="../../css/navbar.css">
<link rel="stylesheet" href="../../css/footer.css">
<link rel="stylesheet" href="../../css/aparatur.css">

</head>
<body>

<?php require_once __DIR__ . '/../../components/navbar.php'; ?>

<main>

<section class="page-hero aparatur-hero">

<div class="container page-hero__content">

<span class="hero__eyebrow">
PROFIL DESA
</span>

<h1 class="page-hero__title">
Aparatur Desa Gilang
</h1>

<p class="page-hero__desc">
Mengenal perangkat desa yang bertugas memberikan pelayanan terbaik bagi masyarakat.
</p>

</div>

</section>



<!-- STATISTIK -->

<?php
$statistik = [
  ['jumlah' => count($aparatur), 'label' => 'Perangkat Desa'],
  ['jumlah' => count(array_filter($aparatur, fn($a) => str_starts_with($a['jabatan'], 'Kepala Dusun'))), 'label' => 'Kepala Dusun'],
  ['jumlah' => count(array_filter($aparatur, fn($a) => str_starts_with($a['jabatan'], 'Kasi'))), 'label' => 'Kasi'],
  ['jumlah' => count(array_filter($aparatur, fn($a) => str_starts_with($a['jabatan'], 'Kaur'))), 'label' => 'Kaur'],
];
?>

<section class="section">

<div class="container">

<div class="stats-grid">

<?php foreach ($statistik as $stat): ?>
<div class="stat-card">
<h2><?php echo (int)$stat['jumlah']; ?></h2>
<p><?php echo htmlspecialchars($stat['label']); ?></p>
</div>
<?php endforeach; ?>

</div>

</div>

</section>



<!-- KEPALA DESA -->

<?php
$kepalaDesa = array_values(array_filter($aparatur, fn($a) => $a['jabatan'] === 'Kepala Desa'))[0] ?? null;
?>

<?php if ($kepalaDesa) : ?>
<section class="section">

<div class="container">

<span class="eyebrow">
Pimpinan Desa
</span>

<h2 class="heading-leaf">
Kepala Desa
</h2>

<div class="kepala-card">

<?php if ($kepalaDesa['foto']) : ?>
<img
src="<?php echo htmlspecialchars($kepalaDesa['foto']); ?>"
alt="<?php echo htmlspecialchars($kepalaDesa['nama']); ?>">
<?php else : ?>
<div style="width:100%;height:100%;min-height:200px;display:flex;align-items:center;justify-content:center;background:var(--color-sage,#DCE7DC);font-weight:700;font-size:2rem;color:var(--color-leaf-dark,#2F6B3F);">
<?php echo htmlspecialchars(inisial_aparatur_publik($kepalaDesa['nama'])); ?>
</div>
<?php endif; ?>
<div>

<h2><?php echo htmlspecialchars($kepalaDesa['nama']); ?></h2>

<p>
Kepala Desa Gilang yang memimpin penyelenggaraan pemerintahan desa serta pembangunan demi kesejahteraan masyarakat.
</p>

<a href="#aparatur-grid" class="btn btn--primary">
Lihat Aparatur
</a>

</div>

</div>

</div>

</section>
<?php endif; ?>



<!-- APARATUR -->

<?php
$aparaturList = array_values(array_filter($aparatur, fn($a) => $a['jabatan'] !== 'Kepala Desa'));
?>

<section class="section section--alt">

<div class="container">

<div class="section__header">

<div>
<span class="eyebrow">
Perangkat Desa
</span>

<h2 class="heading-leaf">
Aparatur Desa
</h2>
</div>

<input
type="text"
id="searchAparatur"
class="search-aparatur"
placeholder="Cari aparatur...">

</div>



<div class="aparatur-grid" id="aparatur-grid">

<?php if (empty($aparaturList)) : ?>
<p class="text-muted">Belum ada data aparatur.</p>
<?php endif; ?>

<?php foreach ($aparaturList as $a): ?>
<div class="aparatur-card" data-nama="<?php echo htmlspecialchars(strtolower($a['nama'])); ?>">

<?php if ($a['foto']) : ?>
<img src="<?php echo htmlspecialchars($a['foto']); ?>" alt="<?php echo htmlspecialchars($a['nama']); ?>">
<?php else : ?>
<div style="width:100%;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;background:var(--color-sage,#DCE7DC);font-weight:700;font-size:1.4rem;color:var(--color-leaf-dark,#2F6B3F);border-radius:inherit;">
<?php echo htmlspecialchars(inisial_aparatur_publik($a['nama'])); ?>
</div>
<?php endif; ?>

<h3><?php echo htmlspecialchars($a['nama']); ?></h3>

<span><?php echo htmlspecialchars($a['jabatan']); ?></span>

</div>
<?php endforeach; ?>

</div>

</div>

</section>



<!-- CTA -->

<section class="cta-section">

<div class="container">

<h2>
Lihat Struktur Organisasi Desa
</h2>

<p>
Kenali susunan pemerintahan Desa Gilang secara lengkap.
</p>

<a href="struktur-organisasi.php"
class="btn btn--primary">
Struktur Organisasi
</a>

</div>

</section>

</main>

<?php require_once __DIR__ . "/../../components/footer.php"; ?>

<script src="../../js/navbar.js"></script>
<script src="../../js/script.js"></script>
<script>
  (function () {
    var input = document.getElementById('searchAparatur');
    var cards = document.querySelectorAll('#aparatur-grid .aparatur-card');
    if (!input) return;
    input.addEventListener('input', function () {
      var kata = input.value.toLowerCase().trim();
      cards.forEach(function (card) {
        var cocok = card.getAttribute('data-nama').includes(kata);
        card.style.display = cocok ? '' : 'none';
      });
    });
  })();
</script>

</body>
</html>