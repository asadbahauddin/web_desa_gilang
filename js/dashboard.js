/* ==========================================================================
   DASHBOARD.JS
   ========================================================================== */

import { getAllBerita } from "./berita.js";
import { getAllDokumen } from "./dokumen.js";
import { getAllGaleri } from "./galeri.js";

/* ==========================================================================
   SIDEBAR
   ========================================================================== */

async function initAdminSidebar() {
  const placeholder = document.getElementById("sidebar-placeholder");
  if (!placeholder) return;

  try {
    const res = await fetch("/components/sidebar-admin.html");
    placeholder.innerHTML = await res.text();
  } catch (err) {
    console.error("[initAdminSidebar]", err);
    return;
  }

  const currentPage = document.body.getAttribute("data-page");

  document.querySelectorAll(".admin-sidebar__link").forEach((link) => {
    if (link.dataset.page === currentPage) {
      link.classList.add("is-active");
    }
  });

  document
    .getElementById("sidebarLogout")
    ?.addEventListener("click", logout);

  const emailEl = document.getElementById("topbarEmail");

  if (emailEl) {
    emailEl.textContent =
      sessionStorage.getItem("desaGilangAdminEmail") ||
      "admin@desagilang.go.id";
  }
}

/* ==========================================================================
   SIDEBAR MOBILE
   ========================================================================== */

function initSidebarToggle() {
  const toggle = document.getElementById("sidebarToggle");

  toggle?.addEventListener("click", () => {
    document.body.classList.toggle("sidebar-open");
  });
}

/* ==========================================================================
   TOAST
   ========================================================================== */

function showToast(message) {
  let toast = document.getElementById("appToast");

  if (!toast) {
    toast = document.createElement("div");

    toast.id = "appToast";
    toast.className = "toast";

    toast.innerHTML = `
      <svg viewBox="0 0 24 24" fill="none">
        <path
          d="M5 13l4 4L19 7"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"/>
      </svg>
      <span></span>
    `;

    document.body.appendChild(toast);
  }

  toast.querySelector("span").textContent = message;

  toast.classList.add("is-visible");

  clearTimeout(toast._timer);

  toast._timer = setTimeout(() => {
    toast.classList.remove("is-visible");
  }, 3000);
}

/* ==========================================================================
   TOAST QUERY
   ========================================================================== */

function consumeToastFromQuery() {
  const params = new URLSearchParams(window.location.search);

  const msg = params.get("toast");

  if (!msg) return;

  showToast(msg);

  params.delete("toast");

  const newUrl =
    window.location.pathname +
    (params.toString() ? "?" + params.toString() : "");

  history.replaceState({}, "", newUrl);
}

/* ==========================================================================
   DASHBOARD STATISTICS (FIREBASE)
   ========================================================================== */

async function initDashboardStats() {
  const beritaCount = document.getElementById("statBeritaCount");
  const dokumenCount = document.getElementById("statDokumenCount");
  const galeriCount = document.getElementById("statGaleriCount");

  if (!beritaCount && !dokumenCount && !galeriCount) return;

  try {
    const berita = await getAllBerita();
    const dokumen = await getAllDokumen();
    const galeri = await getAllGaleri();

    if (beritaCount) beritaCount.textContent = berita.length;
    if (dokumenCount) dokumenCount.textContent = dokumen.length;
    if (galeriCount) galeriCount.textContent = galeri.length;
  } catch (err) {
    console.error("Gagal mengambil statistik dashboard:", err);
  }
}

/* ==========================================================================
   INIT
   ========================================================================== */

document.addEventListener("DOMContentLoaded", async () => {
  guardAdminPage();

  await initAdminSidebar();

  initSidebarToggle();

  consumeToastFromQuery();

  await initDashboardStats();
});