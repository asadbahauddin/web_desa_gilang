<?php
require_once '../config/session.php';
require_once '../config/auth.php';

// Sudah login? langsung ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captchaInput  = strtolower(trim($_POST['captcha'] ?? ''));
    $captchaAnswer = strtolower(trim($_SESSION['captcha_answer'] ?? ''));

    if ($username === '' || $password === '') {
        $error = 'Username dan kata sandi wajib diisi.';
    } elseif ($captchaInput === '') {
        $error = 'Kode verifikasi wajib diisi.';
    } elseif ($captchaInput !== $captchaAnswer) {
        $error = 'Kode verifikasi salah. Silakan coba lagi.';
        unset($_SESSION['captcha_answer']); // paksa generate ulang
    } elseif (!login($username, $password)) {
        $error = 'Username atau kata sandi salah.';
        unset($_SESSION['captcha_answer']);
    } else {
    unset($_SESSION['captcha_answer']);
    header('Location: dashboard.php');
    exit;
    }
}

// Paksa generate ulang jika user klik refresh
if (isset($_GET['refresh'])) {
    unset($_SESSION['captcha_answer']);
}

// Generate captcha teks baru jika belum ada di session
if (empty($_SESSION['captcha_answer'])) {
    $chars  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code   = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    $_SESSION['captcha_answer'] = strtolower($code);
}
$captchaText = strtoupper($_SESSION['captcha_answer']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Desa Gilang</title>
  <meta name="robots" content="noindex">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/login.css">
  <link rel="icon" href="../assets/logo/logo-desa.jpg">

  <style>
    /* ── Captcha PHP — teks distorsi ringan via canvas JS ── */
    .captcha-php {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .captcha-display {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    #captchaCanvas {
      border-radius: 8px;
      cursor: pointer;
      display: block;
    }
    .captcha-refresh {
      background: none;
      border: none;
      padding: 6px;
      cursor: pointer;
      color: #6b7280;
      border-radius: 8px;
      transition: background .15s;
      display: flex;
    }
    .captcha-refresh:hover { background: #f3f4f6; color: #111; }
    .captcha-refresh svg  { width: 18px; height: 18px; }
    .captcha-input-wrap   { position: relative; flex: 1; min-width: 140px; }
    .captcha-input-wrap input {
      width: 100%;
      font-family: 'IBM Plex Mono', monospace;
      letter-spacing: .12em;
      text-transform: uppercase;
    }
    .captcha-hint {
      display: block;
      font-size: 12px;
      color: #9ca3af;
      margin-top: 6px;
    }
  </style>
</head>
<body>

<div class="login-page">

  <!-- Kolom kiri — branding -->
  <div class="login-brand">
    <div class="login-brand__top">
      <img src="/assets/logo/logo-desa.png" alt="Logo Desa Gilang" class="login-brand__logo"
           onerror="this.style.display='none'">
      <span class="login-brand__title">Desa Gilang</span>
    </div>

    <div class="login-brand__seal" aria-hidden="true">
      <span>Panel<br>Resmi</span>
    </div>

    <div class="login-brand__quote">
      <p>"Pelayanan yang transparan dimulai dari data yang terkelola dengan baik."</p>
      <span>Panel Admin — Pemerintah Desa Gilang</span>
    </div>

    <a href="/index.php" class="login-brand__back">
      <svg viewBox="0 0 16 16" fill="none">
        <path d="M10 13L5 8l5-5" stroke="currentColor" stroke-width="1.6"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Kembali ke Situs Utama
    </a>
  </div>

  <!-- Kolom kanan — form login -->
  <div class="login-form-panel">
    <div class="login-form-box">
      <span class="eyebrow">Panel Admin</span>
      <h1>Masuk ke Dashboard</h1>
      <p class="text-muted">Masukkan username dan kata sandi untuk mengelola konten desa.</p>

      <?php if ($error !== ''): ?>
        <div class="login-error" role="alert">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" novalidate id="loginForm">

        <div class="form-group">
          <label for="loginUsername">Username</label>
          <input type="text" id="loginUsername" name="username"
                 placeholder="admin"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 required autocomplete="username" autofocus>
        </div>

        <div class="form-group form-group--password">
          <label for="loginPassword">Kata Sandi</label>
          <input type="password" id="loginPassword" name="password"
                 placeholder="Kata sandi" required autocomplete="current-password">
          <button type="button" class="password-toggle" id="togglePassword"
                  aria-label="Tampilkan kata sandi">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"
                    stroke="currentColor" stroke-width="1.6"/>
              <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
              <path class="eye-off-line" d="M3.5 19.5l17-15"
                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
          </button>
        </div>

        <!-- Captcha (dirender di server, dianimasikan JS) -->
        <div class="form-group form-group--captcha">
          <label for="captchaInput">Verifikasi Keamanan</label>
          <div class="captcha-php">
            <div class="captcha-display">
              <canvas id="captchaCanvas" width="150" height="52"
                      aria-label="Kode verifikasi, klik untuk mengganti"
                      title="Klik untuk mengganti kode"></canvas>
              <button type="button" class="captcha-refresh" id="captchaRefresh"
                      aria-label="Muat ulang kode verifikasi">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M4 4v6h6M20 20v-6h-6" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M5.5 15a8 8 0 0014.5-3M18.5 9A8 8 0 004 12"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
            <div class="captcha-input-wrap">
              <input type="text" id="captchaInput" name="captcha"
                     placeholder="Masukkan kode"
                     autocomplete="off" required maxlength="5"
                     value="<?= htmlspecialchars($_POST['captcha'] ?? '') ?>">
            </div>
          </div>
          <span class="captcha-hint">
            Klik kode atau tombol <strong>refresh</strong> untuk mengganti —
            tidak peka huruf besar/kecil.
          </span>
        </div>

        <button type="submit" class="btn btn--primary">
          Masuk
        </button>

      </form>
    </div>
  </div>

</div>

<script>
  // ── Teks captcha dari server ──────────────────────────────
  const CAPTCHA_TEXT = <?= json_encode($captchaText) ?>;

  // ── Render captcha di canvas ──────────────────────────────
  function drawCaptcha(text) {
    const canvas = document.getElementById('captchaCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = canvas.width, H = canvas.height;

    ctx.clearRect(0, 0, W, H);

    // Latar
    ctx.fillStyle = '#F4F1EB';
    ctx.beginPath();
    ctx.roundRect(0, 0, W, H, 8);
    ctx.fill();

    // Noise lines
    ctx.lineWidth = 1;
    for (let i = 0; i < 6; i++) {
      ctx.strokeStyle = `hsla(${Math.random()*360},40%,55%,0.35)`;
      ctx.beginPath();
      ctx.moveTo(Math.random() * W, Math.random() * H);
      ctx.lineTo(Math.random() * W, Math.random() * H);
      ctx.stroke();
    }

    // Dots
    for (let i = 0; i < 30; i++) {
      ctx.fillStyle = `hsla(${Math.random()*360},35%,50%,0.25)`;
      ctx.beginPath();
      ctx.arc(Math.random()*W, Math.random()*H, Math.random()*2+.5, 0, Math.PI*2);
      ctx.fill();
    }

    // Huruf
    const fonts = ['Fraunces', 'Georgia', 'serif'];
    const spacing = (W - 20) / text.length;
    for (let i = 0; i < text.length; i++) {
      ctx.save();
      const x = 14 + i * spacing + spacing / 2;
      const y = H / 2 + 2;
      ctx.translate(x, y);
      ctx.rotate((Math.random() - .5) * .55);
      ctx.font = `bold ${22 + Math.random()*6}px ${fonts[i % fonts.length]}`;
      ctx.fillStyle = `hsl(${200 + i*25},55%,28%)`;
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(text[i], 0, 0);
      ctx.restore();
    }
  }

  drawCaptcha(CAPTCHA_TEXT);

  // Klik canvas atau tombol refresh → reload halaman utk generate kode baru
  document.getElementById('captchaCanvas').addEventListener('click', function() {
    window.location.href = 'login.php?refresh=1';
  });
  document.getElementById('captchaRefresh').addEventListener('click', function() {
    window.location.href = 'login.php?refresh=1';
  });

  // ── Toggle tampilkan / sembunyikan password ───────────────
  (function () {
    var btn   = document.getElementById('togglePassword');
    var input = document.getElementById('loginPassword');
    var line  = document.querySelector('.eye-off-line');
    if (!btn || !input) return;
    var visible = false;
    btn.addEventListener('click', function () {
      visible = !visible;
      input.type = visible ? 'text' : 'password';
      if (line) line.style.display = visible ? 'none' : '';
    });
  })();
</script>

</body>
</html>