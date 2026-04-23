/* ============================================
   Student Result Management System – Main JS
   Toast notifications, sidebar toggle, utilities
   ============================================ */

// ==========================================
//  TOAST NOTIFICATIONS
// ==========================================

/**
 * Show a toast notification.
 * @param {string} message - The message to display
 * @param {string} type - 'success' | 'error' | 'info'
 * @param {number} duration - Auto-dismiss time in ms (default: 3500)
 */
function showToast(message, type = 'info', duration = 3500) {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  // Icon mapping
  const icons = {
    success: 'fa-circle-check',
    error: 'fa-circle-exclamation',
    info: 'fa-circle-info'
  };

  // Create toast element
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <i class="fas ${icons[type] || icons.info}"></i>
    <span class="toast-msg">${message}</span>
    <button class="toast-close" onclick="dismissToast(this)">
      <i class="fas fa-xmark"></i>
    </button>
  `;

  container.appendChild(toast);

  // Auto-remove after duration
  setTimeout(() => {
    if (toast.parentElement) {
      toast.classList.add('removing');
      setTimeout(() => toast.remove(), 300);
    }
  }, duration);
}

/**
 * Dismiss a toast manually.
 * @param {HTMLElement} btn - The close button element
 */
function dismissToast(btn) {
  const toast = btn.closest('.toast');
  if (toast) {
    toast.classList.add('removing');
    setTimeout(() => toast.remove(), 300);
  }
}

// ==========================================
//  SIDEBAR TOGGLE (Mobile Responsive)
// ==========================================

document.addEventListener('DOMContentLoaded', function () {
  const hamburger = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (hamburger && sidebar && overlay) {
    // Toggle sidebar on hamburger click
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    });

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }
});
