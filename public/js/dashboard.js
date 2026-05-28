// ============================================================
// DASHBOARD.JS - Dashboard Module
// ============================================================

let chartBarVotos = null;
let chartPieVotos = null;
let chartTrends = null;
let dashboardInterval = null;

document.addEventListener('DOMContentLoaded', () => {
  loadDashboard();
  // Auto-refresh every 5 seconds
  dashboardInterval = setInterval(loadDashboard, 5000);
});

async function loadDashboard() {
  try {
    await Promise.all([
      loadDashboardStats(),
      loadDashboardCharts(),
      loadDashboardRanking(),
      loadDashboardNews(),
      loadDashboardComments(),
      loadTrendsChart()
    ]);
    document.getElementById('lastUpdate').textContent = 
      'Última actualización: ' + new Date().toLocaleTimeString('es-CO');
  } catch (err) {
    console.error('Error loading dashboard:', err);
  }
}

async function loadDashboardStats() {
  try {
    const resultados = await apiFetch('/resultados');
    const candidatos = await apiFetch('/candidatos');

    animateNumber(document.getElementById('statTotalVotos'), resultados.total_votos);
    document.getElementById('statCandidatos').textContent = candidatos.length;

    // Calculate participation
    const participation = resultados.total_votos > 0 ? 
      Math.round((resultados.total_votos / Math.max(resultados.total_votos, 100)) * 100) : 0;
    document.getElementById('statParticipacion').textContent = participation + '%';

    // Load encuestas count
    try {
      const encuestas = await apiFetch('/encuestas');
      document.getElementById('statEncuestas').textContent = encuestas.length;
    } catch(e) {}
  } catch (err) {
    console.error('Error loading stats:', err);
  }
}

async function loadDashboardCharts() {
  try {
    const data = await apiFetch('/resultados');
    const top8 = data.resultados.slice(0, 8);

    const labels = top8.map(r => r.nombre.split(' ')[0]);
    const votos = top8.map(r => r.votos);
    const porcentajes = top8.map(r => r.porcentaje || 0);
    const colors = top8.map(r => r.color_partido || '#666');

    // Update total
    const chartTotal = document.getElementById('chartVotesTotal');
    if (chartTotal) chartTotal.textContent = `Total: ${data.total_votos} votos`;

    // BAR CHART
    const barCtx = document.getElementById('chartBarVotos');
    if (barCtx) {
      if (chartBarVotos) {
        chartBarVotos.data.labels = labels;
        chartBarVotos.data.datasets[0].data = votos;
        chartBarVotos.data.datasets[0].backgroundColor = colors.map(c => c + '80');
        chartBarVotos.data.datasets[0].borderColor = colors;
        chartBarVotos.update('none');
      } else {
        chartBarVotos = new Chart(barCtx, {
          type: 'bar',
          data: {
            labels,
            datasets: [{
              label: 'Votos',
              data: votos,
              backgroundColor: colors.map(c => c + '80'),
              borderColor: colors,
              borderWidth: 2,
              borderRadius: 8,
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
                titleFont: { family: "'Inter', sans-serif", weight: '600' },
                bodyFont: { family: "'Inter', sans-serif" }
              }
            },
            scales: {
              x: { ticks: { color: '#a0a0c0', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.03)' } },
              y: { ticks: { color: '#a0a0c0' }, grid: { color: 'rgba(255,255,255,0.03)' }, beginAtZero: true }
            }
          }
        });
      }
    }

    // PIE CHART
    const pieCtx = document.getElementById('chartPieVotos');
    if (pieCtx) {
      if (chartPieVotos) {
        chartPieVotos.data.labels = labels;
        chartPieVotos.data.datasets[0].data = porcentajes;
        chartPieVotos.data.datasets[0].backgroundColor = colors.map(c => c + 'CC');
        chartPieVotos.update('none');
      } else {
        chartPieVotos = new Chart(pieCtx, {
          type: 'doughnut',
          data: {
            labels,
            datasets: [{
              data: porcentajes,
              backgroundColor: colors.map(c => c + 'CC'),
              borderColor: 'rgba(5,5,16,0.8)',
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: { color: '#a0a0c0', font: { size: 10 }, padding: 10, usePointStyle: true }
              },
              tooltip: {
                backgroundColor: 'rgba(10,10,26,0.9)',
                borderColor: 'rgba(0,212,255,0.3)',
                borderWidth: 1,
                callbacks: {
                  label: (ctx) => `${ctx.label}: ${ctx.raw}%`
                }
              }
            }
          }
        });
      }
    }
  } catch (err) {
    console.error('Error loading charts:', err);
  }
}

async function loadDashboardRanking() {
  try {
    const data = await apiFetch('/prediccion');
    const container = document.getElementById('rankingContainer');
    if (!container) return;

    if (!data.predicciones || data.predicciones.length === 0) {
      container.innerHTML = '<div class="text-center py-5 text-muted"><h4>No hay datos verificados</h4><p>A la espera de encuestas y tendencias reales de Colombia 2026.</p></div>';
      document.getElementById('mostLikelyName').textContent = 'No hay datos verificados';
      document.getElementById('mostLikelyProb').textContent = '0';
      return;
    }

    const top5 = data.predicciones.slice(0, 5);

    // Update most likely banner
    if (data.mas_opcionado) {
      document.getElementById('mostLikelyName').textContent = data.mas_opcionado.nombre;
      document.getElementById('mostLikelyProb').textContent = data.mas_opcionado.probabilidad_normalizada;
    }

    container.innerHTML = top5.map((p, i) => `
      <div class="ranking-item">
        <div class="ranking-pos">${i + 1}</div>
        <div class="ranking-avatar" style="background: ${p.color_partido || '#666'};">
          ${getInitials(p.nombre)}
        </div>
        <div class="ranking-info">
          <div class="ranking-name">${p.nombre}</div>
          <div class="ranking-party">${p.partido}</div>
        </div>
        <div class="ranking-bar-container d-none d-md-block">
          <div class="ranking-bar" style="width: ${p.probabilidad_normalizada}%; background: ${p.color_partido || '#666'};"></div>
        </div>
        <div class="ranking-percent" style="color: ${p.color_partido || '#00d4ff'};">
          ${p.probabilidad_normalizada}%
        </div>
      </div>
    `).join('');
  } catch (err) {
    console.error('Error loading ranking:', err);
  }
}

async function loadDashboardNews() {
  try {
    const noticias = await apiFetch('/noticias/destacadas');
    const container = document.getElementById('newsContainer');
    if (!container) return;

    container.innerHTML = noticias.slice(0, 4).map(n => `
      <div class="comment-item" style="cursor: pointer;" onclick="window.location.href='noticias.html'">
        <div class="comment-header">
          <span class="badge-custom" style="background: rgba(0,212,255,0.1); color: #00d4ff; font-size: 0.7rem;">${n.categoria}</span>
          <span class="comment-date">${formatDate(n.fecha_publicacion)}</span>
        </div>
        <div class="comment-user" style="color: var(--text-primary); font-size: 0.9rem;">${n.titulo}</div>
        <div class="comment-text" style="font-size: 0.8rem;">${n.resumen || n.contenido.substring(0, 100)}...</div>
      </div>
    `).join('');
  } catch (err) {
    console.error('Error loading news:', err);
  }
}

async function loadDashboardComments() {
  try {
    const comentarios = await apiFetch('/comentarios');
    const container = document.getElementById('commentsContainer');
    if (!container) return;

    // Show comment form if logged in
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
      commentForm.style.display = isLoggedIn() ? 'block' : 'none';
    }

    container.innerHTML = comentarios.slice(0, 5).map(c => `
      <div class="comment-item">
        <div class="comment-header">
          <span class="comment-user">${c.usuario_nombre}</span>
          <span class="comment-date">${formatDateTime(c.fecha_creacion)}</span>
        </div>
        <div class="comment-text">${c.contenido}</div>
      </div>
    `).join('');

    if (comentarios.length === 0) {
      container.innerHTML = '<p class="text-muted text-center py-3">No hay comentarios aún. ¡Sé el primero!</p>';
    }
  } catch (err) {
    console.error('Error loading comments:', err);
  }
}

async function loadTrendsChart() {
  try {
    const candidatos = await apiFetch('/candidatos');
    const top6 = candidatos.slice(0, 6);

    const trendCtx = document.getElementById('chartTrends');
    if (!trendCtx) return;

    const labels = top6.map(c => c.nombre.split(' ')[0]);
    const colors = top6.map(c => c.color_partido || '#666');

    // Simulated multi-platform data using tendencia_redes
    const twitterData = top6.map(c => Math.round(c.tendencia_redes * 1.2));
    const tiktokData = top6.map(c => Math.round(c.tendencia_redes * 1.5));
    const instaData = top6.map(c => Math.round(c.tendencia_redes * 0.9));

    if (chartTrends) {
      chartTrends.data.labels = labels;
      chartTrends.data.datasets[0].data = twitterData;
      chartTrends.data.datasets[1].data = tiktokData;
      chartTrends.data.datasets[2].data = instaData;
      chartTrends.update('none');
    } else {
      chartTrends = new Chart(trendCtx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'Twitter/X',
              data: twitterData,
              backgroundColor: 'rgba(29, 161, 242, 0.7)',
              borderColor: '#1DA1F2',
              borderWidth: 1,
              borderRadius: 4
            },
            {
              label: 'TikTok',
              data: tiktokData,
              backgroundColor: 'rgba(254, 44, 85, 0.7)',
              borderColor: '#FE2C55',
              borderWidth: 1,
              borderRadius: 4
            },
            {
              label: 'Instagram',
              data: instaData,
              backgroundColor: 'rgba(225, 48, 108, 0.7)',
              borderColor: '#E1306C',
              borderWidth: 1,
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
            x: { ticks: { color: '#a0a0c0', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.03)' } },
            y: { ticks: { color: '#a0a0c0' }, grid: { color: 'rgba(255,255,255,0.03)' }, beginAtZero: true }
          }
        }
      });
    }
  } catch (err) {
    console.error('Error loading trends:', err);
  }
}

async function submitComment() {
  const input = document.getElementById('commentInput');
  const contenido = input.value.trim();
  if (!contenido) return;

  try {
    await apiFetch('/comentarios', {
      method: 'POST',
      body: JSON.stringify({ contenido })
    });
    input.value = '';
    showToast('Comentario publicado', 'success');
    await loadDashboardComments();
  } catch (err) {
    showToast(err.message, 'error');
  }
}
