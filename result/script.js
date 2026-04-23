/* ============================================
   Student Result Management System – Main JS
   Shared utilities, dummy data, auth, sidebar, toasts
   ============================================ */

// ==========================================
//  DUMMY DATA – Seeded on first page load
// ==========================================

const DUMMY_STUDENTS = [
  {
    id: 1, name: 'Arihant Sharma', roll: '2024001', class: '12th', section: 'A',
    marks: { math: 92, science: 88, english: 78, hindi: 85, socialScience: 90 },
    total: 433, percentage: 86.6, grade: 'A', addedAt: '2026-03-10T10:00:00Z'
  },
  {
    id: 2, name: 'Priya Verma', roll: '2024002', class: '12th', section: 'A',
    marks: { math: 75, science: 82, english: 90, hindi: 88, socialScience: 70 },
    total: 405, percentage: 81.0, grade: 'A', addedAt: '2026-03-10T10:05:00Z'
  },
  {
    id: 3, name: 'Rahul Singh', roll: '2024003', class: '11th', section: 'B',
    marks: { math: 55, science: 60, english: 65, hindi: 70, socialScience: 50 },
    total: 300, percentage: 60.0, grade: 'C', addedAt: '2026-03-10T10:10:00Z'
  },
  {
    id: 4, name: 'Sneha Patel', roll: '2024004', class: '10th', section: 'A',
    marks: { math: 95, science: 97, english: 92, hindi: 90, socialScience: 94 },
    total: 468, percentage: 93.6, grade: 'A+', addedAt: '2026-03-10T10:15:00Z'
  },
  {
    id: 5, name: 'Amit Kumar', roll: '2024005', class: '12th', section: 'B',
    marks: { math: 40, science: 38, english: 45, hindi: 50, socialScience: 35 },
    total: 208, percentage: 41.6, grade: 'F', addedAt: '2026-03-10T10:20:00Z'
  },
  {
    id: 6, name: 'Kavya Nair', roll: '2024006', class: '11th', section: 'A',
    marks: { math: 83, science: 79, english: 88, hindi: 72, socialScience: 80 },
    total: 402, percentage: 80.4, grade: 'A', addedAt: '2026-03-11T09:00:00Z'
  },
  {
    id: 7, name: 'Rohit Mehra', roll: '2024007', class: '10th', section: 'C',
    marks: { math: 67, science: 72, english: 58, hindi: 65, socialScience: 60 },
    total: 322, percentage: 64.4, grade: 'C', addedAt: '2026-03-11T09:05:00Z'
  },
  {
    id: 8, name: 'Ananya Gupta', roll: '2024008', class: '12th', section: 'A',
    marks: { math: 88, science: 91, english: 85, hindi: 80, socialScience: 87 },
    total: 431, percentage: 86.2, grade: 'A', addedAt: '2026-03-11T09:10:00Z'
  },
  {
    id: 9, name: 'Vikram Joshi', roll: '2024009', class: '11th', section: 'B',
    marks: { math: 48, science: 52, english: 44, hindi: 55, socialScience: 42 },
    total: 241, percentage: 48.2, grade: 'F', addedAt: '2026-03-11T09:15:00Z'
  },
  {
    id: 10, name: 'Divya Agarwal', roll: '2024010', class: '10th', section: 'A',
    marks: { math: 76, science: 80, english: 72, hindi: 68, socialScience: 74 },
    total: 370, percentage: 74.0, grade: 'B', addedAt: '2026-03-12T08:00:00Z'
  },
  {
    id: 11, name: 'Siddharth Rao', roll: '2024011', class: '12th', section: 'C',
    marks: { math: 60, science: 65, english: 55, hindi: 70, socialScience: 62 },
    total: 312, percentage: 62.4, grade: 'C', addedAt: '2026-03-12T08:05:00Z'
  },
  {
    id: 12, name: 'Meera Iyer', roll: '2024012', class: '11th', section: 'A',
    marks: { math: 90, science: 85, english: 95, hindi: 88, socialScience: 92 },
    total: 450, percentage: 90.0, grade: 'A+', addedAt: '2026-03-12T08:10:00Z'
  }
];

// ==========================================
//  LOCAL STORAGE – Student data management
// ==========================================

/**
 * Seed dummy data into localStorage if no data exists yet.
 */
function seedData() {
  if (!localStorage.getItem('srms_students')) {
    localStorage.setItem('srms_students', JSON.stringify(DUMMY_STUDENTS));
  }
}

/**
 * Retrieve all students from localStorage.
 * @returns {Array} Array of student objects
 */
function getStudents() {
  seedData();
  try {
    return JSON.parse(localStorage.getItem('srms_students')) || [];
  } catch {
    return [];
  }
}

/**
 * Save students array to localStorage.
 * @param {Array} students - Array of student objects
 */
function saveStudents(students) {
  localStorage.setItem('srms_students', JSON.stringify(students));
}

// ==========================================
//  GRADE CALCULATION
// ==========================================

/**
 * Calculate grade from percentage.
 * @param {number} percentage - The student's percentage
 * @returns {string} The grade letter
 */
function calcGrade(percentage) {
  if (percentage >= 90) return 'A+';
  if (percentage >= 80) return 'A';
  if (percentage >= 65) return 'B';
  if (percentage >= 50) return 'C';
  return 'F';
}

// ==========================================
//  AUTHENTICATION GUARD
// ==========================================

/**
 * Check if admin is logged in. Redirect to login page if not.
 */
function checkAuth() {
  if (sessionStorage.getItem('srms_logged_in') !== 'true') {
    window.location.href = 'index.html';
  }
}

// ==========================================
//  TOAST NOTIFICATIONS
// ==========================================

/**
 * Show a toast notification.
 * @param {string} message - The message to display
 * @param {string} type - 'success' | 'error' | 'info'
 * @param {number} duration - Time in ms before auto-dismiss (default: 3500)
 */
function showToast(message, type = 'info', duration = 3500) {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  // Pick icon
  const icons = {
    success: 'fa-circle-check',
    error: 'fa-circle-exclamation',
    info: 'fa-circle-info'
  };

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <i class="fas ${icons[type] || icons.info}"></i>
    <span class="toast-msg">${message}</span>
    <button class="toast-close" onclick="this.parentElement.classList.add('removing'); setTimeout(() => this.parentElement.remove(), 300);">
      <i class="fas fa-xmark"></i>
    </button>
  `;

  container.appendChild(toast);

  // Auto-remove
  setTimeout(() => {
    toast.classList.add('removing');
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// ==========================================
//  SIDEBAR TOGGLE (Mobile)
// ==========================================

document.addEventListener('DOMContentLoaded', function () {
  const hamburger = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (hamburger && sidebar && overlay) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }

  // Seed data on every page load so dummy data is always available
  seedData();
});
