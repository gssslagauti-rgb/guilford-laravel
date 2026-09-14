document.addEventListener('click', function (e) {
  var btn = e.target.closest('[data-contribute]');
  if (!btn) return;
  e.preventDefault();
  var modal = document.getElementById('contribution-modal');
  if (modal) modal.classList.remove('hidden');
});