// ============================================================
// AUTH.JS - Authentication Module
// ============================================================

// Render navbar auth section
function renderNavAuth() {
  const container = document.getElementById('navAuth');
  if (!container) return;

  if (isLoggedIn()) {
    const user = getUser();
    container.innerHTML = `
      ${user.rol === 'admin' ? '<a href="admin.html" class="btn btn-sm" style="color: var(--accent-gold);"><i class="bi bi-shield-check me-1"></i>Admin</a>' : ''}
      <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle" style="color: var(--text-secondary);" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle me-1"></i>${user.nombre}
        </button>
        <ul class="dropdown-menu dropdown-menu-end" style="background: var(--bg-secondary); border: 1px solid var(--border-glass);">
          <li><span class="dropdown-item" style="color: var(--text-muted); font-size: 0.8rem;">${user.email}</span></li>
          <li><hr class="dropdown-divider" style="border-color: var(--border-glass);"></li>
          <li><a class="dropdown-item" href="#" style="color: var(--accent-red);" onclick="handleLogout()"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
        </ul>
      </div>
    `;
  } else {
    container.innerHTML = `
      <a href="login.html" class="btn btn-login">Iniciar Sesión</a>
      <a href="register.html" class="btn btn-register">Registrarse</a>
    `;
  }
}

// Handle login
async function handleLogin(e) {
  e.preventDefault();
  const btn = document.getElementById('loginBtn');
  const alertDiv = document.getElementById('loginAlert');

  btn.disabled = true;
  btn.innerHTML = '<div class="spinner-custom mx-auto" style="width:20px;height:20px;border-width:2px;"></div>';
  alertDiv.classList.add('d-none');

  try {
    const data = await apiFetch('/login', {
      method: 'POST',
      body: JSON.stringify({
        email: document.getElementById('loginEmail').value,
        password: document.getElementById('loginPassword').value
      })
    });

    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));

    showToast('¡Bienvenido, ' + data.user.nombre + '!', 'success');

    setTimeout(() => {
      window.location.href = 'index.html';
    }, 500);
  } catch (err) {
    alertDiv.textContent = err.message;
    alertDiv.classList.remove('d-none');
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión';
  }
}

// Handle register
async function handleRegister(e) {
  e.preventDefault();
  const btn = document.getElementById('registerBtn');
  const alertDiv = document.getElementById('registerAlert');

  const password = document.getElementById('regPassword').value;
  const confirm = document.getElementById('regPasswordConfirm').value;

  if (password !== confirm) {
    alertDiv.textContent = 'Las contraseñas no coinciden.';
    alertDiv.classList.remove('d-none');
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<div class="spinner-custom mx-auto" style="width:20px;height:20px;border-width:2px;"></div>';
  alertDiv.classList.add('d-none');

  try {
    const data = await apiFetch('/register', {
      method: 'POST',
      body: JSON.stringify({
        nombre: document.getElementById('regNombre').value,
        email: document.getElementById('regEmail').value,
        password: password,
        cedula: document.getElementById('regCedula').value,
        departamento: document.getElementById('regDepartamento').value,
        ciudad: document.getElementById('regCiudad').value
      })
    });

    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));

    showToast('¡Cuenta creada exitosamente!', 'success');

    setTimeout(() => {
      window.location.href = 'index.html';
    }, 500);
  } catch (err) {
    alertDiv.textContent = err.message;
    alertDiv.classList.remove('d-none');
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-person-plus me-2"></i>Crear Cuenta';
  }
}

// Handle logout
function handleLogout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  showToast('Sesión cerrada', 'info');
  setTimeout(() => {
    window.location.href = 'index.html';
  }, 300);
}

// Init auth on page load
document.addEventListener('DOMContentLoaded', () => {
  renderNavAuth();
});
