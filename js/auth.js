/* ==========================================================================
   AUTH.JS — Login, Route Guard, Logout
   ----------------------------------------------------------------------
   CATATAN PENTING:
   File ini masih memakai mock auth berbasis sessionStorage karena Firebase
   belum disambungkan (lihat Tahap 12). Setiap fungsi di bawah ditandai
   dengan komentar "GANTI DI TAHAP 12" pada bagian yang nanti diganti
   dengan Firebase Authentication (signInWithEmailAndPassword, onAuthStateChanged,
   signOut), supaya HTML & alur halaman TIDAK perlu diubah sama sekali.
   ========================================================================== */

const AUTH_SESSION_KEY = 'desaGilangAdminSession';

/** Mengecek apakah admin sedang login (mock) */
function isAdminLoggedIn() {
  // GANTI DI TAHAP 12 -> ganti dengan onAuthStateChanged(auth, callback)
  return sessionStorage.getItem(AUTH_SESSION_KEY) === 'true';
}

/** Proses login (mock — menerima email & password apa saja yang tidak kosong) */
function mockLogin(email, password) {
  // GANTI DI TAHAP 12 -> ganti isi fungsi ini dengan:
  // return signInWithEmailAndPassword(auth, email, password)
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      if (email && password && password.length >= 6) {
        sessionStorage.setItem(AUTH_SESSION_KEY, 'true');
        sessionStorage.setItem('desaGilangAdminEmail', email);
        resolve({ email });
      } else {
        reject(new Error('Email atau kata sandi salah. Kata sandi minimal 6 karakter.'));
      }
    }, 600); // simulasi delay network
  });
}

/** Logout (mock) */
function logout() {
  // GANTI DI TAHAP 12 -> ganti dengan signOut(auth)
  sessionStorage.removeItem(AUTH_SESSION_KEY);
  sessionStorage.removeItem('desaGilangAdminEmail');
  window.location.href = '/admin/login.html';
}

/** Dipasang di setiap halaman admin (kecuali login.html) untuk menolak akses jika belum login */
function guardAdminPage() {
  if (!isAdminLoggedIn()) {
    window.location.href = '/admin/login.html';
  }
}

/** Inisialisasi form login di admin/login.html */
function initLoginForm() {
  const form = document.getElementById('loginForm');
  if (!form) return;

  const errorBox = document.getElementById('loginError');
  const submitBtn = form.querySelector('button[type="submit"]');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorBox?.classList.remove('is-visible');

    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Memproses...';

    try {
      await mockLogin(email, password);
      window.location.href = '/admin/dashboard.html';
    } catch (err) {
      if (errorBox) {
        errorBox.textContent = err.message;
        errorBox.classList.add('is-visible');
      }
      submitBtn.disabled = false;
      submitBtn.textContent = 'Masuk';
    }
  });

  // Toggle tampil/sembunyi kata sandi
  const toggle = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('loginPassword');
  toggle?.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
  });
}

document.addEventListener('DOMContentLoaded', initLoginForm);