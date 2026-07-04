/* =========================================================================
   captcha.js — Verifikasi keamanan (captcha kanvas) untuk login admin
   Catatan: validasi ini berjalan di sisi klien (cocok untuk tahap demo).
   Saat integrasi Firebase (Tahap 12) aktif, pertimbangkan memindahkan
   verifikasi ke captcha sisi server (mis. reCAPTCHA/Turnstile) untuk
   perlindungan anti-bot yang sesungguhnya.

   PENTING: file ini harus dimuat SEBELUM /js/auth.js pada halaman HTML,
   supaya pemeriksaan captcha selalu berjalan lebih dulu saat form disubmit.
   ========================================================================= */

(function () {
  "use strict";

  /* -----------------------------------------------------------------
     1) Toggle tampilkan/sembunyikan kata sandi
     ----------------------------------------------------------------- */
  var pwdInput  = document.getElementById("loginPassword");
  var pwdToggle = document.getElementById("togglePassword");

  if (pwdInput && pwdToggle) {
    pwdToggle.addEventListener("click", function () {
      var isHidden = pwdInput.type === "password";
      pwdInput.type = isHidden ? "text" : "password";
      pwdToggle.classList.toggle("is-active", isHidden);
      pwdToggle.setAttribute(
        "aria-label",
        isHidden ? "Sembunyikan kata sandi" : "Tampilkan kata sandi"
      );
    });
  }

  /* -----------------------------------------------------------------
     2) Captcha kanvas
     ----------------------------------------------------------------- */
  var canvas   = document.getElementById("captchaCanvas");
  var refresh  = document.getElementById("captchaRefresh");
  var input    = document.getElementById("captchaInput");
  var stub     = document.getElementById("captchaStub");
  var form     = document.getElementById("loginForm");
  var errorBox = document.getElementById("loginError");

  if (!canvas) return; // halaman tidak memiliki captcha

  var ctx = canvas.getContext("2d");
  // Hindari karakter yang mudah tertukar: 0/O, 1/I/L, 5/S
  var CHARSET = "ABCDEFGHJKMNPQRTUVWXY346789";
  var CODE_LENGTH = 5;
  var currentCode = "";

  var INK_TONES = ["#2C4A30", "#1F2A22", "#4E7A53", "#8A5A2A"];

  function randomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  function generateCode(length) {
    var out = "";
    for (var i = 0; i < length; i++) {
      out += CHARSET.charAt(randomInt(0, CHARSET.length - 1));
    }
    return out;
  }

  function drawCaptcha() {
    var w = canvas.width;
    var h = canvas.height;

    currentCode = generateCode(CODE_LENGTH);

    // latar kertas
    ctx.clearRect(0, 0, w, h);
    ctx.fillStyle = "#F6F1E6";
    ctx.fillRect(0, 0, w, h);

    // guratan tipis seperti garis ledger
    ctx.strokeStyle = "rgba(44,74,48,0.10)";
    ctx.lineWidth = 1;
    for (var l = 0; l < 4; l++) {
      var y = randomInt(6, h - 6);
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.bezierCurveTo(w * 0.33, y + randomInt(-8, 8), w * 0.66, y + randomInt(-8, 8), w, y);
      ctx.stroke();
    }

    // karakter
    var charWidth = w / (CODE_LENGTH + 1);
    ctx.textBaseline = "middle";
    for (var i = 0; i < currentCode.length; i++) {
      var ch = currentCode[i];
      var x = charWidth * (i + 1);
      var y = h / 2 + randomInt(-4, 4);
      var angle = (randomInt(-16, 16) * Math.PI) / 180;
      var size = randomInt(22, 27);

      ctx.save();
      ctx.translate(x, y);
      ctx.rotate(angle);
      ctx.font = "600 " + size + "px 'IBM Plex Mono', monospace";
      ctx.fillStyle = INK_TONES[randomInt(0, INK_TONES.length - 1)];
      ctx.textAlign = "center";
      ctx.fillText(ch, 0, 0);
      ctx.restore();
    }

    // titik derau (noise)
    for (var d = 0; d < 18; d++) {
      ctx.fillStyle = "rgba(44,74,48," + (randomInt(8, 18) / 100) + ")";
      ctx.beginPath();
      ctx.arc(randomInt(0, w), randomInt(0, h), 1, 0, Math.PI * 2);
      ctx.fill();
    }

    resetCaptchaState();
  }

  function resetCaptchaState() {
    if (stub) stub.classList.remove("is-valid", "is-shaking");
    if (input) input.value = "";
  }

  function isCaptchaValid() {
    if (!input) return true;
    return input.value.trim().toUpperCase() === currentCode;
  }

  function showError(message) {
    if (!errorBox) return;
    errorBox.textContent = message;
    errorBox.classList.add("is-visible");
  }

  function clearError() {
    if (!errorBox) return;
    errorBox.classList.remove("is-visible");
    errorBox.textContent = "";
  }

  function spinRefreshIcon() {
    if (!refresh) return;
    refresh.classList.add("is-spinning");
    window.setTimeout(function () {
      refresh.classList.remove("is-spinning");
    }, 400);
  }

  // gambar ulang saat tombol refresh atau kanvas diklik
  if (refresh) {
    refresh.addEventListener("click", function () {
      drawCaptcha();
      spinRefreshIcon();
      if (input) input.focus();
    });
  }
  canvas.addEventListener("click", function () {
    drawCaptcha();
    if (input) input.focus();
  });

  // bersihkan status error saat pengguna mulai mengetik ulang
  if (input) {
    input.addEventListener("input", function () {
      if (stub) stub.classList.remove("is-shaking");
      clearError();
    });
  }

  // periksa captcha LEBIH DULU sebelum logika login (auth.js) dijalankan
  if (form) {
    form.addEventListener("submit", function (event) {
      if (!isCaptchaValid()) {
        event.preventDefault();
        event.stopImmediatePropagation();

        showError("Kode verifikasi tidak sesuai. Silakan coba lagi.");
        if (stub) {
          stub.classList.remove("is-shaking");
          // restart animasi
          void stub.offsetWidth;
          stub.classList.add("is-shaking");
        }
        drawCaptcha();
        if (input) input.focus();
        return;
      }

      // captcha benar — tandai visual lalu lanjutkan ke handler login
      clearError();
      if (stub) stub.classList.add("is-valid");

      var submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.classList.add("is-loading");
    });
  }

  drawCaptcha();
})();