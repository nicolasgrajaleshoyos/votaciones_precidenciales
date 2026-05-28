// ============================================================
// APP.JS - Core Application Logic
// Elecciones Colombia 2026
// ============================================================

const API_URL = window.location.origin + '/api';

// ============================================================
// UTILITY FUNCTIONS
// ============================================================

function getToken() {
  return localStorage.getItem('token');
}

function getUser() {
  const user = localStorage.getItem('user');
  return user ? JSON.parse(user) : null;
}

function isLoggedIn() {
  return !!getToken();
}

function isAdmin() {
  const user = getUser();
  return user && user.rol === 'admin';
}

// API Helper
async function apiFetch(endpoint, options = {}) {
  const token = getToken();
  const headers = {
    'Content-Type': 'application/json',
    ...options.headers
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  try {
    const response = await fetch(`${API_URL}${endpoint}`, {
      ...options,
      headers
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.error || data.message || 'Error en la solicitud');
    }

    return data;
  } catch (err) {
    if (err.message.includes('Token inválido') || err.message.includes('Acceso denegado')) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = 'login.html';
    }
    throw err;
  }
}

// Toast notification
function showToast(message, type = 'info') {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const icons = {
    success: 'bi-check-circle-fill text-accent-green',
    error: 'bi-x-circle-fill text-accent-red',
    warning: 'bi-exclamation-triangle-fill text-accent-gold',
    info: 'bi-info-circle-fill text-accent-blue'
  };

  const toast = document.createElement('div');
  toast.className = 'toast-custom';
  toast.innerHTML = `
    <div class="d-flex align-items-center gap-2">
      <i class="bi ${icons[type] || icons.info}" style="font-size: 1.2rem;"></i>
      <span>${message}</span>
      <button class="btn btn-sm ms-auto" style="color: var(--text-muted);" onclick="this.parentElement.parentElement.remove()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  `;

  container.appendChild(toast);
  setTimeout(() => toast.remove(), 5000);
}

// Format date
function formatDate(dateStr) {
  if (!dateStr) return '';
  // Force local timezone parsing for simple YYYY-MM-DD strings by converting hyphens to slashes
  const normalizedStr = dateStr.includes('T') ? dateStr : dateStr.replace(/-/g, '/');
  const date = new Date(normalizedStr);
  return date.toLocaleDateString('es-CO', {
    year: 'numeric', month: 'short', day: 'numeric'
  });
}

function formatDateTime(dateStr) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('es-CO', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
}

// Get initials from name
function getInitials(name) {
  return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
}

// Toggle password visibility
function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'bi bi-eye';
  }
}

// Get tendency badge HTML
function getTendencyBadge(tendencia) {
  const classes = {
    'izquierda': 'badge-izquierda',
    'centro-izquierda': 'badge-centro-izquierda',
    'centro': 'badge-centro',
    'centro-derecha': 'badge-centro-derecha',
    'derecha': 'badge-derecha'
  };
  const labels = {
    'izquierda': 'Izquierda',
    'centro-izquierda': 'Centro-Izq.',
    'centro': 'Centro',
    'centro-derecha': 'Centro-Der.',
    'derecha': 'Derecha'
  };
  return `<span class="badge-custom ${classes[tendencia] || ''}">${labels[tendencia] || tendencia}</span>`;
}

// Chart.js default config for dark theme
function getChartDefaults() {
  return {
    color: '#a0a0c0',
    borderColor: 'rgba(255, 255, 255, 0.05)',
    font: { family: "'Inter', sans-serif" }
  };
}

// Animate number counter
function animateNumber(element, target, duration = 1000) {
  const start = parseInt(element.textContent.replace(/[^0-9-]/g, '')) || 0;
  const increment = (target - start) / (duration / 16);
  let current = start;

  const timer = setInterval(() => {
    current += increment;
    if ((increment > 0 && current >= target) || (increment < 0 && current <= target)) {
      current = target;
      clearInterval(timer);
    }
    element.textContent = Math.round(current).toLocaleString('es-CO');
  }, 16);
}
