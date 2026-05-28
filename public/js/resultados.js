// ============================================================
// RESULTADOS.JS - Results Module
// ============================================================

let chartResultBars = null;
let chartResultPie = null;
let resultsInterval = null;

document.addEventListener('DOMContentLoaded', () => {
  loadResults();
  resultsInterval = setInterval(loadResults, 5000);
});

async function loadResults() {
  try {
    const data = await apiFetch('/resultados');
    
    // Update winner banner
    if (data.mas_votado) {
      document.getElementById('winnerName').textContent = data.mas_votado.nombre;
      document.getElementById('winnerPercent').textContent = data.mas_votado.porcentaje || 0;
      document.getElementById('winnerVotes').textContent = data.mas_votado.votos;
    }
    document.getElementById('totalVotosResult').textContent = data.total_votos;

    renderResultsCharts(data);
    renderResultsTable(data);
  } catch (err) {
    console.error('Error loading results:', err);
  }
}

function renderResultsCharts(data) {
  const resultados = data.resultados;
  const labels = resultados.map(r => r.nombre.split(' ').slice(0, 2).join(' '));
  const votos = resultados.map(r => r.votos);
  const porcentajes = resultados.map(r => r.porcentaje || 0);
  const colors = resultados.map(r => r.color_partido || '#666');

  // BAR CHART
  const barCtx = document.getElementById('chartResultBars');
  if (barCtx) {
    if (chartResultBars) {
      chartResultBars.data.labels = labels;
      chartResultBars.data.datasets[0].data = votos;
      chartResultBars.data.datasets[0].backgroundColor = colors.map(c => c + '80');
      chartResultBars.data.datasets[0].borderColor = colors;
      chartResultBars.update('none');
    } else {
      chartResultBars = new Chart(barCtx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Votos',
            data: votos,
            backgroundColor: colors.map(c => c + '80'),
            borderColor: colors,
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(10,10,26,0.9)',
              borderColor: 'rgba(0,212,255,0.3)',
              borderWidth: 1
            }
          },
          scales: {
            x: { ticks: { color: '#a0a0c0' }, grid: { color: 'rgba(255,255,255,0.03)' }, beginAtZero: true },
            y: { ticks: { color: '#a0a0c0', font: { size: 11 } }, grid: { display: false } }
          }
        }
      });
    }
  }

  // PIE CHART
  const pieCtx = document.getElementById('chartResultPie');
  if (pieCtx) {
    const top8 = resultados.slice(0, 8);
    if (chartResultPie) {
      chartResultPie.data.labels = top8.map(r => r.nombre.split(' ')[0]);
      chartResultPie.data.datasets[0].data = top8.map(r => r.porcentaje || 0);
      chartResultPie.data.datasets[0].backgroundColor = top8.map(r => (r.color_partido || '#666') + 'CC');
      chartResultPie.update('none');
    } else {
      chartResultPie = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
          labels: top8.map(r => r.nombre.split(' ')[0]),
          datasets: [{
            data: top8.map(r => r.porcentaje || 0),
            backgroundColor: top8.map(r => (r.color_partido || '#666') + 'CC'),
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
}

function renderResultsTable(data) {
  const tbody = document.getElementById('resultsTableBody');
  if (!tbody) return;

  tbody.innerHTML = data.resultados.map((r, i) => `
    <tr>
      <td><span class="fw-bold ${i < 3 ? 'text-accent-gold' : ''}">${i + 1}</span></td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div style="width:32px;height:32px;border-radius:50%;background:${r.color_partido || '#666'};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.7rem;">
            ${getInitials(r.nombre)}
          </div>
          <span class="fw-semibold">${r.nombre}</span>
        </div>
      </td>
      <td><span class="text-muted">${r.partido || 'Independiente'}</span></td>
      <td><span class="fw-bold">${r.votos}</span></td>
      <td><span class="fw-bold" style="color: ${r.color_partido || '#00d4ff'};">${r.porcentaje || 0}%</span></td>
      <td style="min-width: 150px;">
        <div class="progress-custom">
          <div class="progress-bar-custom" style="width: ${r.porcentaje || 0}%; background: ${r.color_partido || '#00d4ff'};"></div>
        </div>
      </td>
    </tr>
  `).join('');
}
