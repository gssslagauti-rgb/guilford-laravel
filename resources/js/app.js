import './bootstrap';
// ---- Contribution modal ----
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

  // Click on (+)
  var addBtn = e.target.closest('[data-contribute]');
  if (addBtn && !addBtn.hasAttribute('onclick')) {
    e.preventDefault();
    var section = addBtn.dataset.contribute;
    var modal = document.getElementById('contribution-modal');

    if (!modal) {
      openAuthModal(e);
      return;
    }

    document.getElementById('c-section').value = section;
    document.getElementById('modal-title').textContent = 'Add to ' + section.replace(/-/g, ' ');
    renderFields(section);
    modal.classList.remove('hidden');
  }

  // Close modal
  if (e.target.classList.contains('modal-close')) closeModal(e.target);
  if (e.target.classList.contains('modal')) e.target.classList.add('hidden');
});

function renderFields(section) {
  var c = document.getElementById('contribution-fields');
  if (!c) return;

  var sets = {
    events: '<label>Event name</label><input name="title" required>'
          + '<label>Date</label><input name="date" placeholder="Oct 12, 2026">'
          + '<label>Location</label><input name="location" placeholder="Town Green">'
          + '<label>Description</label><textarea name="body" rows="3"></textarea>',

    sports: '<label>Program</label><input name="title" required>'
          + '<label>Season</label><input name="season" placeholder="Fall 2026">'
          + '<label>Registration</label><input name="registration" placeholder="Open until Sept 15">'
          + '<label>Details</label><textarea name="body" rows="3"></textarea>',

    construction: '<label>Project / Permit</label><input name="title" required>'
                + '<label>Address</label><input name="location">'
                + '<label>Details</label><textarea name="body" rows="4"></textarea>',

    debates: '<label>Topic</label><input name="title" required>'
           + '<label>Positions & context</label><textarea name="body" rows="4"></textarea>',

    recommendations: '<label>Place / event</label><input name="title" required>'
                   + '<label>Category</label><input name="category" placeholder="Restaurant / Activity">'
                   + '<label>Why you recommend it</label><textarea name="body" rows="3"></textarea>',

    lore: '<label>Topic</label><input name="title" required>'
        + '<label>Local knowledge</label><textarea name="body" rows="4"></textarea>',

    projects: '<label>Project</label><input name="title" required>'
            + '<label>Status / details</label><textarea name="body" rows="4"></textarea>',

    government: '<label>Announcement</label><input name="title" required>'
              + '<label>Details</label><textarea name="body" rows="4"></textarea>',

    topics: '<label>Topic</label><input name="title" required>'
          + '<label>Discussion</label><textarea name="body" rows="4"></textarea>',

    "town-council": '<label>Meeting date</label><input name="date">'
                  + '<label>Location</label><input name="location" placeholder="Town Hall">'
                  + '<label>Bullets (one per line)</label><textarea name="bullets" rows="6"></textarea>',

    "board-of-education": '<label>Meeting date</label><input name="date">'
                        + '<label>Location</label><input name="location">'
                        + '<label>Bullets (one per line)</label><textarea name="bullets" rows="6"></textarea>'
  };

  c.innerHTML = sets[section] || '<label>Title</label><input name="title" required><label>Content</label><textarea name="body" rows="4"></textarea>';
}