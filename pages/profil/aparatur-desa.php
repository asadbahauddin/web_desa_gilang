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
  ['jumlah' => 15, 'label' => 'Perangkat Desa'],
  ['jumlah' => 4,  'label' => 'Kepala Dusun'],
  ['jumlah' => 3,  'label' => 'Kasi'],
  ['jumlah' => 2,  'label' => 'Kaur'],
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
$kepalaDesa = [
  'foto'  => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=800&q=80',
  'nama'  => 'Bapak Suwarno, S.Sos.',
  'desk'  => 'Kepala Desa Gilang yang memimpin penyelenggaraan pemerintahan desa serta pembangunan demi kesejahteraan masyarakat.',
];
?>

<section class="section">

<div class="container">

<span class="eyebrow">
Pimpinan Desa
</span>

<h2 class="heading-leaf">
Kepala Desa
</h2>

<div class="kepala-card">

<img
src="<?php echo htmlspecialchars($kepalaDesa['foto']); ?>"
alt="Kepala Desa">
<div>

<h2><?php echo htmlspecialchars($kepalaDesa['nama']); ?></h2>

<p>
<?php echo htmlspecialchars($kepalaDesa['desk']); ?>
</p>

<a href="#aparatur-grid" class="btn btn--primary">
Lihat Aparatur
</a>

</div>

</div>

</div>

</section>



<!-- APARATUR -->

<?php
$aparaturList = [
  ['foto' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600', 'nama' => 'Siti Aminah',  'jabatan' => 'Sekretaris Desa'],
  ['foto' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=600', 'nama' => 'Ahmad Fauzi',  'jabatan' => 'Kasi Pemerintahan'],
  ['foto' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=600', 'nama' => 'Rudi Hartono', 'jabatan' => 'Kaur Umum'],
  ['foto' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=600', 'nama' => 'Nur Aisyah',   'jabatan' => 'Kaur Keuangan'],
];
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
class="search-aparatur"
placeholder="Cari aparatur...">

</div>



<div class="aparatur-grid" id="aparatur-grid">

<?php foreach ($aparaturList as $aparatur): ?>
<div class="aparatur-card">

<img src="<?php echo htmlspecialchars($aparatur['foto']); ?>">

<h3><?php echo htmlspecialchars($aparatur['nama']); ?></h3>

<span><?php echo htmlspecialchars($aparatur['jabatan']); ?></span>

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

</body>
</html>