// ============================================================
// NOTICIAS.JS - News Module
// ============================================================

let allNoticias = [];

document.addEventListener('DOMContentLoaded', () => {
  loadNoticias();
});

async function loadNoticias() {
  try {
    allNoticias = await apiFetch('/noticias');
    renderNoticias(allNoticias);
  } catch (err) {
    console.error('Error loading news:', err);
    document.getElementById('noticiasGrid').innerHTML = 
      '<p class="text-muted text-center py-5 col-12">Error cargando noticias</p>';
  }
}

function filterNoticias(categoria, btn) {
  document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');

  let filtered = allNoticias;
  if (categoria !== 'todas') {
    filtered = allNoticias.filter(n => n.categoria === categoria);
  }
  renderNoticias(filtered);
}

function renderNoticias(noticias) {
  const grid = document.getElementById('noticiasGrid');

  if (noticias.length === 0) {
    grid.innerHTML = '<p class="text-muted text-center py-5 col-12">No hay noticias en esta categoría</p>';
    return;
  }

  const categoryColors = {
    'politica': { bg: 'rgba(0, 212, 255, 0.1)', color: '#00d4ff' },
    'encuestas': { bg: 'rgba(124, 58, 237, 0.1)', color: '#7c3aed' },
    'debate': { bg: 'rgba(245, 158, 11, 0.1)', color: '#f59e0b' },
    'economia': { bg: 'rgba(16, 185, 129, 0.1)', color: '#10b981' },
    'social': { bg: 'rgba(236, 72, 153, 0.1)', color: '#ec4899' },
    'internacional': { bg: 'rgba(6, 182, 212, 0.1)', color: '#06b6d4' }
  };

  grid.innerHTML = noticias.map((n, i) => {
    const catStyle = categoryColors[n.categoria] || categoryColors['politica'];
    return `
      <div class="col-md-6 col-lg-4 slide-up" style="animation-delay: ${i * 0.05}s;">
        <div class="news-card">
          <span class="news-category" style="background: ${catStyle.bg}; color: ${catStyle.color};">
            ${n.categoria}
          </span>
          ${n.destacada ? '<span class="badge-custom badge-admin ms-2" style="font-size: 0.65rem;">⭐ Destacada</span>' : ''}
          <h5 class="news-title">${n.titulo}</h5>
          <p class="news-excerpt">${n.resumen || n.contenido.substring(0, 200)}...</p>
          <div class="news-meta">
            <span><i class="bi bi-newspaper me-1"></i>${n.fuente || 'N/A'}</span>
            <span><i class="bi bi-calendar me-1"></i>${formatDate(n.fecha_publicacion)}</span>
          </div>
        </div>
      </div>
    `;
  }).join('');
}
