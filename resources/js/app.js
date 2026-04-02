import './bootstrap';

const pages = ['dashboard','detected','alerts','reports','import','transactions','subscriptions'];

function setPage(name) {
  pages.forEach(p => {
    const el = document.getElementById('page-' + p);
    if (el) el.style.display = p === name ? 'block' : 'none';
  });
  document.querySelectorAll('.nav-item').forEach(item => {
    item.classList.remove('active');
    if (item.getAttribute('onclick') && item.getAttribute('onclick').includes("'" + name + "'")) {
      item.classList.add('active');
    }
  });
  if (name === 'reports') setTimeout(initTrend, 50);
}

function showModal(id) {
  const overlay = document.getElementById('modal-overlay');
  overlay.style.display = 'flex';
  document.querySelectorAll('.modal-box').forEach(m => m.style.display = 'none');
  const box = document.getElementById(id);
  if (box) box.style.display = 'block';
}

function closeModal() {
  document.getElementById('modal-overlay').style.display = 'none';
}

document.getElementById('modal-overlay').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

function filterSubs(tab, cat) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  tab.classList.add('active');
  document.querySelectorAll('.sub-row[data-cat]').forEach(row => {
    row.style.display = (cat === 'all' || row.dataset.cat === cat) ? '' : 'none';
  });
}

function confirmDetection(btn) {
  btn.closest('.sub-row').style.opacity = '0.4';
  btn.textContent = 'Confirmed ✓';
  btn.disabled = true;
}

function dismissDetection(btn) {
  btn.closest('.sub-row').style.opacity = '0.4';
  btn.textContent = 'Dismissed';
  btn.disabled = true;
}

function markAllRead() {
  document.querySelectorAll('.alert-row').forEach(r => r.style.opacity = '0.5');
}

new Chart(document.getElementById('catChart'), {
  type: 'doughnut',
  data: {
    labels: ['Streaming', 'SaaS', 'Utilities', 'Other'],
    datasets: [{
      data: [45, 98, 32, 109],
      backgroundColor: ['#1a56db', '#059669', '#d97706', '#7c3aed'],
      borderWidth: 2,
      borderColor: '#ffffff'
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false, cutout: '68%',
    plugins: {
      legend: { display: false },
      tooltip: { callbacks: { label: ctx => ' $' + ctx.parsed + '/mo' } }
    }
  }
});

function initTrend() {
  if (document.getElementById('trendChart')._chart) return;
  const c = new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
      labels: ['Oct','Nov','Dec','Jan','Feb','Mar'],
      datasets: [{
        label: 'Monthly spend',
        data: [241, 248, 261, 255, 266, 284],
        borderColor: '#1a56db', backgroundColor: '#1a56db18',
        tension: 0.4, fill: true, pointRadius: 4, pointBackgroundColor: '#1a56db'
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { ticks: { callback: v => '$' + v, font: { size: 11 } }, grid: { color: '#e5e7eb' } },
        x: { ticks: { font: { size: 11 } }, grid: { display: false } }
      }
    }
  });
  document.getElementById('trendChart')._chart = c;
}

