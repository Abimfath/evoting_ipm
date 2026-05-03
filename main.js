// ============================================================
// E-VOTING IPM - MAIN JAVASCRIPT
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // ── Auto-hide alerts after 5 seconds ──
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-8px)';
      setTimeout(function () { alert.remove(); }, 400);
    }, 5000);
  });

  // ── Form validation feedback ──
  const forms = document.querySelectorAll('form[data-validate]');
  forms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      let valid = true;
      const requiredFields = form.querySelectorAll('[required]');
      requiredFields.forEach(function (field) {
        if (!field.value.trim()) {
          valid = false;
          field.style.borderColor = '#ef4444';
          field.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.15)';
          field.addEventListener('input', function () {
            field.style.borderColor = '';
            field.style.boxShadow = '';
          }, { once: true });
        }
      });
      if (!valid) {
        e.preventDefault();
        const existing = form.querySelector('.alert-error');
        if (!existing) {
          const msg = document.createElement('div');
          msg.className = 'alert alert-error';
          msg.innerHTML = '⚠️ Harap isi semua kolom yang diperlukan.';
          form.prepend(msg);
        }
      }
    });
  });

  // ── Confirm before vote ──
  const voteButtons = document.querySelectorAll('.btn-vote-confirm');
  voteButtons.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      const name = btn.dataset.name || 'kandidat ini';
      if (!confirm('Apakah Anda yakin ingin memilih ' + name + '?\n\nPilihan tidak dapat diubah setelah dikonfirmasi.')) {
        e.preventDefault();
      }
    });
  });

  // ── Confirm before delete ──
  const deleteLinks = document.querySelectorAll('.btn-delete-confirm');
  deleteLinks.forEach(function (link) {
    link.addEventListener('click', function (e) {
      if (!confirm('Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
        e.preventDefault();
      }
    });
  });

  // ── Password toggle visibility ──
  const toggles = document.querySelectorAll('.toggle-password');
  toggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      const target = document.querySelector(toggle.dataset.target);
      if (!target) return;
      if (target.type === 'password') {
        target.type = 'text';
        toggle.textContent = '🙈';
      } else {
        target.type = 'password';
        toggle.textContent = '👁️';
      }
    });
  });

  // ── File input preview ──
  const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
  fileInputs.forEach(function (input) {
    input.addEventListener('change', function () {
      const previewId = input.dataset.preview;
      const preview = document.getElementById(previewId);
      if (!preview) return;
      const file = input.files[0];
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  });

  // ── Animate stat numbers ──
  const statValues = document.querySelectorAll('.stat-value[data-target]');
  statValues.forEach(function (el) {
    const target = parseInt(el.dataset.target, 10);
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 50));
    const timer = setInterval(function () {
      current = Math.min(current + step, target);
      el.textContent = current;
      if (current >= target) clearInterval(timer);
    }, 25);
  });

  // ── Progress bars animate on load ──
  const bars = document.querySelectorAll('.progress-bar-fill[data-width]');
  bars.forEach(function (bar) {
    const w = bar.dataset.width;
    requestAnimationFrame(function () {
      setTimeout(function () {
        bar.style.width = w + '%';
      }, 200);
    });
  });

});
