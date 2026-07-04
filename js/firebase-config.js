/* ==========================================================================
   FIREBASE-CONFIG.JS — Placeholder konfigurasi Firebase
   ----------------------------------------------------------------------
   File ini BELUM AKTIF/di-import di halaman manapun. Seluruh fitur saat
   ini (auth.js, berita.js, dokumen.js, galeri.js) masih memakai mock data
   di localStorage. Pada Tahap 12 (Integrasi Firebase), langkah-langkahnya:

   1. Buat project di https://console.firebase.google.com
   2. Aktifkan: Authentication (Email/Password), Firestore Database, Storage
   3. Salin konfigurasi project ke objek `firebaseConfig` di bawah ini
   4. Tambahkan Firebase SDK (via CDN <script type="module"> atau npm + bundler)
   5. Inisialisasi app, auth, db, storage di sini lalu export agar bisa
      dipakai oleh auth.js / berita.js / dokumen.js / galeri.js
   6. Ganti isi fungsi-fungsi yang bertanda komentar "GANTI DI TAHAP 12"
      di file-file tersebut dengan pemanggilan Firebase yang sesungguhnya.
   ========================================================================== */

import { initializeApp } from "https://www.gstatic.com/firebasejs/12.5.0/firebase-app.js";

import {
    getFirestore
} from "https://www.gstatic.com/firebasejs/12.5.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyBrHdg7kyLddLzew-DZ125TDuLBBOvHkw4",
  authDomain: "desa-gilang.firebaseapp.com",
  projectId: "desa-gilang",
  storageBucket: "desa-gilang.firebasestorage.app",
  messagingSenderId: "773097054948",
  appId: "1:773097054948:web:b5386f0f682130b55202f1"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

export { db };

// Contoh inisialisasi setelah Firebase SDK (modular v9+) ditambahkan via CDN:
//
// import { initializeApp } from "https://www.gstatic.com/firebasejs/10.x.x/firebase-app.js";
// import { getAuth } from "https://www.gstatic.com/firebasejs/10.x.x/firebase-auth.js";
// import { getFirestore } from "https://www.gstatic.com/firebasejs/10.x.x/firebase-firestore.js";
// import { getStorage } from "https://www.gstatic.com/firebasejs/10.x.x/firebase-storage.js";
//
// const app = initializeApp(firebaseConfig);
// export const auth = getAuth(app);
// export const db = getFirestore(app);
// export const storage = getStorage(app);