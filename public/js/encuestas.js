// ============================================================
// ENCUESTAS.JS - Polls Module
// ============================================================

let allEncuestas = [];
let encuestaCharts = {};

document.addEventListener('DOMContentLoaded', () => {
  loadEncuestas();
});

async function loadEncuestas() {
  try {
    allEncuestas = await apiFetch('/encuestas');
    renderEncuestas(allEncuestas);
  } catch (err) {
    console.error('Error loading polls:', err);
    document.getElementById('encuestasContainer').innerHTML = 
      '<p class="text-muted text-center py-5">Error cargando encuestas</p>';
  }
}

function filterEncuestas(tipo, btn) {
  // Update tabs
  document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');

  let filtered = allEncuestas;
  if (tipo !== 'todas') {
    filtered = allEncuestas.filter(e => e.tipo === tipo);
  }
  renderEncuestas(filtered);
}

function renderEncuestas(encuestas) {
  const container = document.getElementById('encuestasContainer');

  if (encuestas.length === 0) {
    container.innerHTML = '<p class="text-muted text-center py-5">No hay encuestas disponibles</p>';
    return;
  }

  // Destroy existing charts
  Object.values(encuestaCharts).forEach(c => c.destroy());
  encuestaCharts = {};

  container.innerHTML = encuestas.map((enc, idx) => `
    <div class="glass-card-static mb-4 slide-up" style="animation-delay: ${idx * 0.1}s;">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
          <h5 class="mb-1">${enc.titulo}</h5>
          <div class="d-flex gap-3 flex-wrap">
            <span class="text-muted"><i class="bi bi-building me-1"></i>${enc.fuente || 'N/A'}</span>
            <span class="text-muted"><i class="bi bi-calendar me-1"></i>${formatDate(enc.fecha_realizacion)}</span>
            <span class="text-muted"><i class="bi bi-people me-1"></i>Muestra: ${enc.muestra ? enc.muestra.toLocaleString() : 'N/A'}</span>
            ${enc.margen_error ? `<span class="text-muted"><i class="bi bi-plus-slash-minus me-1"></i>±${enc.margen_error}%</span>` : ''}
          </div>
        </div>
        <span class="badge-custom ${enc.tipo === 'primera_vuelta' ? 'badge-centro' : 'badge-izquierda'}">
          ${enc.tipo === 'primera_vuelta' ? '1ra Vuelta' : '2da Vuelta'}
        </span>
      </div>
      ${enc.descripcion ? `<p class="text-muted mb-3" style="font-size: 0.9rem;">${enc.descripcion}</p>` : ''}
      <div class="row g-3">
        <div class="col-lg-7">
          <div style="height: 250px; position: relative;">
            <canvas id="encChart-${enc.id}"></canvas>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="table-responsive">
            <table class="table table-custom" style="font-size: 0.85rem;">
              <thead>
                <tr><th>#</th><th>Candidato</th><th>%</th></tr>
              </thead>
              <tbody>
                ${(enc.resultados || []).map((r, i) => `
                  <tr>
                    <td class="${i < 3 ? 'text-accent-gold fw-bold' : ''}">${i + 1}</td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div style="width:8px;height:8px;border-radius:50%;background:${r.color_partido || '#666'};"></div>
                        ${r.candidato_nombre}
                      </div>
                    </td>
                    <td class="fw-bold" style="color: ${r.color_partido || '#00d4ff'};">${r.porcentaje}%</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  `).join('');

  // Create charts after DOM render
  setTimeout(() => {
    encuestas.forEach(enc => {
      const ctx = document.getElementById(`encChart-${enc.id}`);
      if (ctx && enc.resultados) {
        encuestaCharts[enc.id] = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: enc.resultados.map(r => r.candidato_nombre.split(' ')[0]),
            datasets: [{
              label: 'Porcentaje',
              data: enc.resultados.map(r => r.porcentaje),
              backgroundColor: enc.resultados.map(r => (r.color_partido || '#666') + '80'),
              borderColor: enc.resultados.map(r => r.color_partido || '#666'),
              borderWidth: 2,
              borderRadius: 6,
              borderSkipped: false
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: {
                backgroundColor: 'rgba(10,10,26,0.9)',
                borderColor: 'rgba(0,212,255,0.3)',
                borderWidth: 1,
                callbacks: { label: (ctx) => `${ctx.raw}%` }
              }
            },
            scales: {
              x: { ticks: { color: '#a0a0c0', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.03)' } },
              y: { ticks: { color: '#a0a0c0', callback: v => v + '%' }, grid: { color: 'rgba(255,255,255,0.03)' }, beginAtZero: true }
            }
          }
        });
      }
    });
  }, 100);
}
