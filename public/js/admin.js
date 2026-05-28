// ============================================================
// ADMIN.JS - Admin Panel Module
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
  initAdmin();
});

async function initAdmin() {
  if (!isLoggedIn() || !isAdmin()) {
    document.getElementById('adminDenied').style.display = 'block';
    return;
  }

  document.getElementById('adminPanel').style.display = 'block';
  await loadAdminData();
}

async function loadAdminData() {
  try {
    await Promise.all([
      loadAdminStats(),
      loadAdminUsers(),
      loadAdminVotes(),
      loadAdminEncuestas(),
      loadAdminNoticias(),
      loadAdminCandidates()
    ]);
  } catch (err) {
    console.error('Error loading admin data:', err);
  }
}

async function loadAdminStats() {
  try {
    const stats = await apiFetch('/admin/stats');
    document.getElementById('adminStatUsers').textContent = stats.total_usuarios;
    document.getElementById('adminStatVotes').textContent = stats.total_votos;
    document.getElementById('adminStatPolls').textContent = stats.total_encuestas;
    document.getElementById('adminStatParticipation').textContent = stats.participacion + '%';
  } catch (err) {
    console.error('Error loading admin stats:', err);
  }
}

async function loadAdminUsers() {
  try {
    const usuarios = await apiFetch('/admin/usuarios');
    const tbody = document.getElementById('adminUsersTable');
    
    tbody.innerHTML = usuarios.map(u => `
      <tr>
        <td>${u.id}</td>
        <td class="fw-semibold">${u.nombre}</td>
        <td class="text-muted">${u.email}</td>
        <td><span class="badge-custom ${u.rol === 'admin' ? 'badge-admin' : 'badge-usuario'}">${u.rol}</span></td>
        <td>${u.departamento || '-'}</td>
        <td>${formatDate(u.fecha_registro)}</td>
        <td>${u.ha_votado ? '<i class="bi bi-check-circle-fill text-accent-green"></i>' : '<i class="bi bi-x-circle text-muted"></i>'}</td>
        <td>
          ${u.rol !== 'admin' ? `
            <button class="btn btn-sm ${u.activo ? 'btn-danger-custom' : 'btn-primary-custom'}" 
                    onclick="toggleUser(${u.id})" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
              ${u.activo ? 'Desactivar' : 'Activar'}
            </button>
          ` : '<span class="text-muted" style="font-size:0.75rem;">Admin</span>'}
        </td>
      </tr>
    `).join('');
  } catch (err) {
    console.error('Error loading users:', err);
  }
}

async function loadAdminVotes() {
  try {
    const votos = await apiFetch('/admin/votos');
    const tbody = document.getElementById('adminVotesTable');
    
    tbody.innerHTML = votos.map(v => `
      <tr>
        <td>${v.id}</td>
        <td class="fw-semibold">${v.usuario_nombre}</td>
        <td class="text-muted">${v.usuario_email}</td>
        <td class="text-accent-blue">${v.candidato_nombre}</td>
        <td class="text-muted">${v.partido}</td>
        <td>${formatDateTime(v.fecha_voto)}</td>
        <td>
          <button class="btn btn-danger-custom btn-sm" onclick="deleteVote(${v.id})" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
            <i class="bi bi-trash me-1"></i>Eliminar
          </button>
        </td>
      </tr>
    `).join('');

    if (votos.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay votos registrados</td></tr>';
    }
  } catch (err) {
    console.error('Error loading votes:', err);
  }
}

async function loadAdminEncuestas() {
  try {
    const encuestas = await apiFetch('/encuestas?activa=all');
    const container = document.getElementById('adminEncuestasContainer');
    
    container.innerHTML = `
      <div class="table-responsive">
        <table class="table table-custom">
          <thead>
            <tr><th>ID</th><th>Título</th><th>Tipo</th><th>Fuente</th><th>Fecha</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            ${encuestas.map(e => `
              <tr>
                <td>${e.id}</td>
                <td class="fw-semibold">${e.titulo}</td>
                <td><span class="badge-custom ${e.tipo === 'primera_vuelta' ? 'badge-centro' : 'badge-izquierda'}">${e.tipo === 'primera_vuelta' ? '1ra Vuelta' : '2da Vuelta'}</span></td>
                <td class="text-muted">${e.fuente || '-'}</td>
                <td>${formatDate(e.fecha_realizacion)}</td>
                <td>
                  <button class="btn btn-danger-custom btn-sm" onclick="deleteEncuesta(${e.id})" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                    <i class="bi bi-trash me-1"></i>Eliminar
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;
  } catch (err) {
    console.error('Error loading polls:', err);
  }
}

async function loadAdminNoticias() {
  try {
    const noticias = await apiFetch('/noticias');
    const container = document.getElementById('adminNoticiasContainer');
    
    container.innerHTML = `
      <div class="table-responsive">
        <table class="table table-custom">
          <thead>
            <tr><th>ID</th><th>Título</th><th>Categoría</th><th>Fuente</th><th>Destacada</th><th>Fecha</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            ${noticias.map(n => `
              <tr>
                <td>${n.id}</td>
                <td class="fw-semibold" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${n.titulo}</td>
                <td><span class="badge-custom badge-centro">${n.categoria}</span></td>
                <td class="text-muted">${n.fuente || '-'}</td>
                <td>${n.destacada ? '⭐' : '-'}</td>
                <td>${formatDate(n.fecha_publicacion)}</td>
                <td>
                  <button class="btn btn-danger-custom btn-sm" onclick="deleteNoticia(${n.id})" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                    <i class="bi bi-trash me-1"></i>Eliminar
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;
  } catch (err) {
    console.error('Error loading news:', err);
  }
}

async function loadAdminCandidates() {
  try {
    const candidatos = await apiFetch('/candidatos');
    const tbody = document.getElementById('adminCandidatesTable');
    
    tbody.innerHTML = candidatos.map(c => `
      <tr>
        <td>${c.id}</td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div style="width:28px;height:28px;border-radius:50%;background:${c.color_partido || '#666'};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.65rem;">
              ${getInitials(c.nombre)}
            </div>
            <span class="fw-semibold">${c.nombre}</span>
          </div>
        </td>
        <td class="text-muted">${c.partido}</td>
        <td class="text-accent-blue">${c.favorabilidad}%</td>
        <td class="text-accent-purple">${c.tendencia_redes}</td>
        <td class="${c.crecimiento_semanal >= 0 ? 'text-accent-green' : 'text-accent-red'}">
          ${c.crecimiento_semanal >= 0 ? '+' : ''}${c.crecimiento_semanal}%
        </td>
        <td class="fw-bold">${c.total_votos || 0}</td>
      </tr>
    `).join('');
  } catch (err) {
    console.error('Error loading candidates:', err);
  }
}

// Tab switching
function showAdminTab(tab, btn) {
  document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');

  document.querySelectorAll('.admin-content').forEach(c => c.style.display = 'none');
  
  const tabMap = {
    'usuarios': 'tabUsuarios',
    'votos': 'tabVotos',
    'encuestas': 'tabEncuestas',
    'noticias': 'tabNoticias',
    'candidatos': 'tabCandidatos'
  };

  document.getElementById(tabMap[tab]).style.display = 'block';
}

// CRUD Operations
async function toggleUser(id) {
  try {
    await apiFetch(`/admin/usuarios/${id}/toggle`, { method: 'PUT' });
    showToast('Estado de usuario actualizado', 'success');
    await loadAdminUsers();
  } catch (err) {
    showToast(err.message, 'error');
  }
}

async function deleteVote(id) {
  if (!confirm('¿Eliminar este voto? Esta acción no se puede deshacer.')) return;
  try {
    await apiFetch(`/admin/votos/${id}`, { method: 'DELETE' });
    showToast('Voto eliminado', 'success');
    await Promise.all([loadAdminVotes(), loadAdminStats()]);
  } catch (err) {
    showToast(err.message, 'error');
  }
}

async function deleteEncuesta(id) {
  if (!confirm('¿Eliminar esta encuesta?')) return;
  try {
    await apiFetch(`/encuestas/${id}`, { method: 'DELETE' });
    showToast('Encuesta eliminada', 'success');
    await loadAdminEncuestas();
  } catch (err) {
    showToast(err.message, 'error');
  }
}

async function deleteNoticia(id) {
  if (!confirm('¿Eliminar esta noticia?')) return;
  try {
    await apiFetch(`/noticias/${id}`, { method: 'DELETE' });
    showToast('Noticia eliminada', 'success');
    await loadAdminNoticias();
  } catch (err) {
    showToast(err.message, 'error');
  }
}

// Modal handlers
function showCreateNoticia() {
  const modal = new bootstrap.Modal(document.getElementById('modalNoticia'));
  modal.show();
}

function showCreateEncuesta() {
  const modal = new bootstrap.Modal(document.getElementById('modalEncuesta'));
  modal.show();
}

async function createNoticia() {
  try {
    await apiFetch('/noticias', {
      method: 'POST',
      body: JSON.stringify({
        titulo: document.getElementById('notTitulo').value,
        contenido: document.getElementById('notContenido').value,
        fuente: document.getElementById('notFuente').value,
        autor: document.getElementById('notAutor').value,
        categoria: document.getElementById('notCategoria').value,
        destacada: document.getElementById('notDestacada').checked ? 1 : 0
      })
    });
    
    bootstrap.Modal.getInstance(document.getElementById('modalNoticia')).hide();
    showToast('Noticia creada exitosamente', 'success');
    await loadAdminNoticias();
    document.getElementById('noticiaForm').reset();
  } catch (err) {
    showToast(err.message, 'error');
  }
}

async function createEncuesta() {
  try {
    await apiFetch('/encuestas', {
      method: 'POST',
      body: JSON.stringify({
        titulo: document.getElementById('encTitulo').value,
        tipo: document.getElementById('encTipo').value,
        fuente: document.getElementById('encFuente').value,
        fecha_realizacion: document.getElementById('encFecha').value,
        margen_error: document.getElementById('encMargen').value,
        muestra: document.getElementById('encMuestra').value
      })
    });
    
    bootstrap.Modal.getInstance(document.getElementById('modalEncuesta')).hide();
    showToast('Encuesta creada exitosamente', 'success');
    await loadAdminEncuestas();
    document.getElementById('encuestaForm').reset();
  } catch (err) {
    showToast(err.message, 'error');
  }
}
