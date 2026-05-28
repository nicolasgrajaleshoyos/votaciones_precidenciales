// ============================================================
// VOTACION.JS - Voting Module
// ============================================================

let selectedCandidateId = null;

document.addEventListener('DOMContentLoaded', () => {
  initVotacion();
});

async function initVotacion() {
  // Check if user is logged in
  if (!isLoggedIn()) {
    document.getElementById('loginRequired').style.display = 'block';
    return;
  }

  // Check if already voted
  try {
    const votoData = await apiFetch('/mi-voto');
    if (votoData.ya_voto) {
      document.getElementById('alreadyVoted').style.display = 'block';
      document.getElementById('votedForName').textContent = votoData.voto.candidato_nombre;
      return;
    }
  } catch (err) {
    console.error('Error checking vote:', err);
  }

  // Show voting section
  document.getElementById('votingSection').style.display = 'block';
  await loadCandidates();
}

async function loadCandidates() {
  try {
    const candidatos = await apiFetch('/candidatos');
    const grid = document.getElementById('candidatesGrid');

    grid.innerHTML = candidatos.map(c => `
      <div class="col-6 col-md-4 col-lg-3">
        <div class="candidate-card" id="card-${c.id}" onclick="selectCandidate(${c.id}, '${c.nombre}')" style="--card-color: ${c.color_partido || '#666'}">
          <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: ${c.color_partido || '#666'};"></div>
          <div class="candidate-avatar" style="background: ${c.color_partido || '#666'};">
            ${getInitials(c.nombre)}
          </div>
          <div class="candidate-name">${c.nombre}</div>
          <div class="candidate-party">${c.partido}</div>
          ${getTendencyBadge(c.tendencia)}
          <div class="mt-2">
            <small class="text-muted" style="font-size: 0.75rem;">Fórmula: ${c.formula_vice || 'N/A'}</small>
          </div>
          <div class="mt-2" id="checkmark-${c.id}" style="display: none;">
            <i class="bi bi-check-circle-fill text-accent-green" style="font-size: 1.5rem;"></i>
          </div>
        </div>
      </div>
    `).join('');
  } catch (err) {
    showToast('Error cargando candidatos', 'error');
  }
}

function selectCandidate(id, nombre) {
  // Deselect previous
  document.querySelectorAll('.candidate-card').forEach(card => {
    card.classList.remove('selected');
    const checkId = card.id.replace('card-', 'checkmark-');
    const check = document.getElementById(checkId);
    if (check) check.style.display = 'none';
  });

  // Select new
  const card = document.getElementById(`card-${id}`);
  card.classList.add('selected');
  document.getElementById(`checkmark-${id}`).style.display = 'block';

  selectedCandidateId = id;
  document.getElementById('submitVoteBtn').disabled = false;
  document.getElementById('selectedCandidateText').innerHTML = 
    `Has seleccionado a <strong class="text-accent-blue">${nombre}</strong>`;
}

async function submitVote() {
  if (!selectedCandidateId) {
    showToast('Selecciona un candidato', 'warning');
    return;
  }

  if (!confirm('¿Estás seguro de votar por este candidato? Esta acción no se puede deshacer.')) {
    return;
  }

  const btn = document.getElementById('submitVoteBtn');
  btn.disabled = true;
  btn.innerHTML = '<div class="spinner-custom mx-auto" style="width:20px;height:20px;border-width:2px;"></div>';

  try {
    const data = await apiFetch('/votar', {
      method: 'POST',
      body: JSON.stringify({ candidato_id: selectedCandidateId })
    });

    showToast(data.message, 'success');

    // Update user in localStorage
    const user = getUser();
    user.ya_voto = true;
    user.candidato_votado = selectedCandidateId;
    localStorage.setItem('user', JSON.stringify(user));

    setTimeout(() => {
      window.location.reload();
    }, 1500);
  } catch (err) {
    showToast(err.message, 'error');
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check2-all me-2"></i>CONFIRMAR VOTO';
  }
}
