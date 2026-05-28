// ============================================================
// PREDICCION.JS - Prediction Module
// ============================================================

let chartPredPie = null;
let chartPredBar = null;
let predInterval = null;

document.addEventListener('DOMContentLoaded', () => {
  loadPredicciones();
  predInterval = setInterval(loadPredicciones, 5000);
});

async function loadPredicciones() {
  try {
    const data = await apiFetch('/prediccion');
    
    // Update banner
    if (data.mas_opcionado) {
      document.getElementById('predName').textContent = data.mas_opcionado.nombre;
      document.getElementById('predProb').textContent = data.mas_opcionado.probabilidad_normalizada;
    }

    renderPredRanking(data.predicciones);
    renderPredCharts(data.predicciones);
    renderPredTable(data.predicciones);
  } catch (err) {
    console.error('Error loading predictions:', err);
  }
}

function renderPredRanking(predicciones) {
  const container = document.getElementById('predRankingContainer');
  if (!container) return;

  container.innerHTML = predicciones.map((p, i) => `
    <div class="ranking-item">
      <div class="ranking-pos">${i + 1}</div>
      <div class="ranking-avatar" style="background: ${p.color_partido || '#666'};">
        ${getInitials(p.nombre)}
      </div>
      <div class="ranking-info">
        <div class="ranking-name">${p.nombre}</div>
        <div class="ranking-party">${p.partido} ${getTendencyBadge(p.tendencia)}</div>
      </div>
      <div class="ranking-bar-container d-none d-md-block">
        <div class="ranking-bar" style="width: ${p.probabilidad_normalizada}%; background: ${p.color_partido || '#666'};"></div>
      </div>
      <div class="ranking-percent" style="color: ${p.color_partido || '#00d4ff'};">
        ${p.probabilidad_normalizada}%
      </div>
    </div>
  `).join('');
}

function renderPredCharts(predicciones) {
  const top8 = predicciones.slice(0, 8);
  const labels = top8.map(p => p.nombre.split(' ')[0]);
  const colors = top8.map(p => p.color_partido || '#666');

  // PIE
  const pieCtx = document.getElementById('chartPredPie');
  if (pieCtx) {
    const piData = top8.map(p => p.probabilidad_normalizada);
    if (chartPredPie) {
      chartPredPie.data.labels = labels;
      chartPredPie.data.datasets[0].data = piData;
      chartPredPie.data.datasets[0].backgroundColor = colors.map(c => c + 'CC');
      chartPredPie.update('none');
    } else {
      chartPredPie = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data: piData,
            backgroundColor: colors.map(c => c + 'CC'),
            borderColor: 'rgba(5,5,16,0.8)',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '55%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: '#a0a0c0', font: { size: 10 }, padding: 8, usePointStyle: true }
            },
            tooltip: {
              backgroundColor: 'rgba(10,10,26,0.9)',
              callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw}%` }
            }
          }
        }
      });
    }
  }

  // BAR - Stacked comparison
  const barCtx = document.getElementById('chartPredBar');
  if (barCtx) {
    if (chartPredBar) {
      chartPredBar.data.labels = labels;
      chartPredBar.data.datasets[0].data = top8.map(p => p.desglose.comp_intencion);
      chartPredBar.data.datasets[1].data = top8.map(p => p.desglose.comp_favorabilidad);
      chartPredBar.data.datasets[2].data = top8.map(p => p.desglose.comp_redes);
      chartPredBar.data.datasets[3].data = top8.map(p => p.desglose.comp_crecimiento);
      chartPredBar.update('none');
    } else {
      chartPredBar = new Chart(barCtx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'Intención de voto (50%)',
              data: top8.map(p => p.desglose.comp_intencion),
              backgroundColor: 'rgba(0, 212, 255, 0.7)',
              borderRadius: 4
            },
            {
              label: 'Favorabilidad (20%)',
              data: top8.map(p => p.desglose.comp_favorabilidad),
              backgroundColor: 'rgba(124, 58, 237, 0.7)',
              borderRadius: 4
            },
            {
              label: 'Redes Sociales (20%)',
              data: top8.map(p => p.desglose.comp_redes),
              backgroundColor: 'rgba(236, 72, 153, 0.7)',
              borderRadius: 4
            },
            {
              label: 'Crecimiento (10%)',
              data: top8.map(p => p.desglose.comp_crecimiento),
              backgroundColor: 'rgba(245, 158, 11, 0.7)',
              borderRadius: 4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              labels: { color: '#a0a0c0', font: { size: 11 }, usePointStyle: true }
            },
            tooltip: {
              backgroundColor: 'rgba(10,10,26,0.9)',
              borderColor: 'rgba(0,212,255,0.3)',
              borderWidth: 1
            }
          },
          scales: {
            x: { stacked: true, ticks: { color: '#a0a0c0', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.03)' } },
            y: { stacked: true, ticks: { color: '#a0a0c0' }, grid: { color: 'rgba(255,255,255,0.03)' }, beginAtZero: true }
          }
        }
      });
    }
  }
}

function renderPredTable(predicciones) {
  const tbody = document.getElementById('predTableBody');
  if (!tbody) return;

  tbody.innerHTML = predicciones.map((p, i) => `
    <tr>
      <td class="fw-bold ${i < 3 ? 'text-accent-gold' : ''}">${i + 1}</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div style="width:28px;height:28px;border-radius:50%;background:${p.color_partido || '#666'};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.65rem;">
            ${getInitials(p.nombre)}
          </div>
          <div>
            <div class="fw-semibold" style="font-size:0.85rem;">${p.nombre}</div>
            <div class="text-muted" style="font-size:0.75rem;">${p.partido}</div>
          </div>
        </div>
      </td>
      <td class="text-accent-blue">${p.desglose.comp_intencion}</td>
      <td class="text-accent-purple">${p.desglose.comp_favorabilidad}</td>
      <td class="text-accent-pink">${p.desglose.comp_redes}</td>
      <td class="text-accent-gold">${p.desglose.comp_crecimiento}</td>
      <td>
        <span class="fw-bold" style="color: ${p.color_partido || '#00d4ff'}; font-size: 1.1rem;">
          ${p.probabilidad_normalizada}%
        </span>
      </td>
    </tr>
  `).join('');
}
