/**
 * Admin JS — Pusaka Himatif
 */

/* Toggle Sidebar (Mobile) */
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('sidebar-open');
}

/* Close sidebar when clicking outside on mobile */
document.addEventListener('click', function (e) {
  const sidebar  = document.getElementById('sidebar');
  const toggle   = document.querySelector('.sidebar-toggle');
  if (!sidebar || !toggle) return;
  if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
    sidebar.classList.remove('sidebar-open');
  }
});

/* Image preview on file input */
function previewImage(inputEl, previewId) {
  const preview = document.getElementById(previewId);
  if (!preview) return;
  const file = inputEl.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}

/* Auto-generate slug dari judul */
function generateSlug(val) {
  return val
    .toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')  // remove diacritics
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
}

const judulInput = document.getElementById('judul');
const slugInput  = document.getElementById('slug');

if (judulInput && slugInput) {
  judulInput.addEventListener('input', function () {
    if (!slugInput.dataset.manual) {
      slugInput.value = generateSlug(this.value);
    }
  });
  slugInput.addEventListener('input', function () {
    this.dataset.manual = 'true';
  });
}

/* Flash message auto-hide */
const flash = document.querySelector('.flash-message');
if (flash) {
  setTimeout(function () {
    flash.style.transition = 'opacity 0.5s ease';
    flash.style.opacity = '0';
    setTimeout(function () { flash.remove(); }, 500);
  }, 3500);
}
