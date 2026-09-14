// ---- Modal helpers ----
function openAuthModal(e) {
  if (e) e.preventDefault();
  var modal = document.getElementById('auth-modal');
  if (modal) modal.classList.remove('hidden');
}

function closeModal(el) {
  var modal = el.closest('.modal');
  if (modal) modal.classList.add('hidden');
}

document.addEventListener('click', function (e) {

  // ---- (+) add new ----
  var addBtn = e.target.closest('[data-contribute]');
  if (addBtn) {
    e.preventDefault();
    var modal = document.getElementById('contribution-modal');
    if (!modal) { openAuthModal(e); return; }

    var section = addBtn.dataset.contribute;
    document.getElementById('c-section').value = section;
    document.getElementById('c-type').value = 'new';
    document.getElementById('c-target').value = '';
    document.getElementById('modal-title').textContent = 'Add to ' + section.replace(/-/g, ' ');
    document.getElementById('modal-submit').textContent = 'Submit for Review';
    renderFields(section, null);
    modal.classList.remove('hidden');
  }

  // ---- ✎ edit existing ----
  var editBtn = e.target.closest('[data-edit]');
  if (editBtn) {
    e.preventDefault();
    var modal2 = document.getElementById('contribution-modal');
    if (!modal2) { openAuthModal(e); return; }

    var payload;
    try { payload = JSON.parse(editBtn.dataset.edit); } catch (err) { return; }

    document.getElementById('c-section').value = payload.section || '';
    document.getElementById('c-type').value = 'edit';
    document.getElementById('c-target').value = payload.id || '';
    document.getElementById('modal-title').textContent = 'Edit: ' + (payload.title || '');
    document.getElementById('modal-submit').textContent = 'Submit Edit for Review';
    renderFields(payload.section, payload);
    modal2.classList.remove('hidden');
  }

  // ---- Close ----
  if (e.target.classList.contains('modal-close')) closeModal(e.target);
  if (e.target.classList.contains('modal')) e.target.classList.add('hidden');
});

function renderFields(section, existing) {
  var c = document.getElementById('contribution-fields');
  if (!c) return;
  var v = existing || {};

  var input = function(name, label, placeholder, value) {
    return '<label>' + label + '</label><input name="' + name + '" placeholder="' + (placeholder || '') + '" value="' + (value || '') + '">';
  };
  var textarea = function(name, label, placeholder, value, rows) {
    return '<label>' + label + '</label><textarea name="' + name + '" rows="' + (rows || 3) + '" placeholder="' + (placeholder || '') + '">' + (value || '') + '</textarea>';
  };

  var sets = {
    events: input('title', 'Event name', '', v.title) + input('date', 'Date', 'Oct 12, 2026', v.date) + input('location', 'Location', 'Town Green', v.location) + textarea('body', 'Description', '', v.body),
    sports: input('title', 'Program', '', v.title) + input('season', 'Season', 'Fall 2026', v.season) + input('registration', 'Registration', 'Open until Sept 15', v.registration) + textarea('body', 'Details', '', v.body),
    construction: input('title', 'Project / Permit', '', v.title) + input('location', 'Address', '', v.location) + textarea('body', 'Details', '', v.body, 4),
    debates: input('title', 'Topic', '', v.title) + textarea('body', 'Positions & context', '', v.body, 4),
    recommendations: input('title', 'Place / event', '', v.title) + input('category', 'Category', 'Restaurant / Activity', v.category) + textarea('body', 'Why you recommend it', '', v.body),
    lore: input('title', 'Topic', '', v.title) + textarea('body', 'Local knowledge', '', v.body, 4),
    projects: input('title', 'Project', '', v.title) + textarea('body', 'Status / details', '', v.body, 4),
    government: input('title', 'Announcement', '', v.title) + textarea('body', 'Details', '', v.body, 4),
    topics: input('title', 'Topic', '', v.title) + textarea('body', 'Discussion', '', v.body, 4),
    'town-council': input('date', 'Meeting date', '', v.date) + input('location', 'Location', 'Town Hall', v.location) + textarea('bullets', 'Bullets (one per line)', '', (v.bullets || []).join('\n'), 6),
    'board-of-education': input('date', 'Meeting date', '', v.date) + input('location', 'Location', '', v.location) + textarea('bullets', 'Bullets (one per line)', '', (v.bullets || []).join('\n'), 6)
  };

  c.innerHTML = sets[section] || (input('title', 'Title', '', v.title) + textarea('body', 'Content', '', v.body, 4));
}
