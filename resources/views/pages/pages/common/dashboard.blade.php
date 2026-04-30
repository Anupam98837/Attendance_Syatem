@extends('pages.layout.structure')

@section('title', 'Dashboard')

@push('styles')
<style>
.bdash-wrap{display:grid;gap:18px}
.bdash-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  flex-wrap:wrap;
}
.bdash-head-copy h1{
  margin:0;
  font-size:clamp(25px,3vw,36px);
  font-weight:800;
  color:var(--ink);
}
.bdash-head-copy p{
  margin:8px 0 0;
  color:var(--muted-color);
  max-width:760px;
  line-height:1.7;
}
.bdash-actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
.bdash-grid{
  display:grid;
  grid-template-columns:repeat(6,minmax(0,1fr));
  gap:14px;
}
.bdash-stat{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:20px;
  padding:18px;
  box-shadow:var(--shadow-1);
}
.bdash-stat-label{
  color:var(--muted-color);
  font-size:12px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:.08em;
}
.bdash-stat-value{
  margin-top:12px;
  font-size:30px;
  font-weight:800;
  color:var(--ink);
  line-height:1;
}
.bdash-panels{
  display:grid;
  grid-template-columns:minmax(0,1.4fr) minmax(340px,.9fr);
  gap:16px;
}
.bdash-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  padding:18px;
  box-shadow:var(--shadow-1);
}
.bdash-card-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  margin-bottom:14px;
}
.bdash-card-head h2{
  margin:0;
  font-size:18px;
  font-weight:800;
  color:var(--ink);
}
.bdash-card-head p{
  margin:6px 0 0;
  color:var(--muted-color);
  line-height:1.6;
  font-size:13px;
}
.bdash-table-wrap{overflow:auto}
.bdash-table-wrap{-webkit-overflow-scrolling:touch}
.bdash-table{
  width:100%;
  min-width:860px;
  border-collapse:separate;
  border-spacing:0;
}
.bdash-table th,
.bdash-table td{
  padding:13px 12px;
  border-bottom:1px solid var(--line-strong);
  vertical-align:top;
}
.bdash-table th{
  color:var(--muted-color);
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.07em;
}
.bdash-table tbody tr:last-child td{border-bottom:0}
.bdash-main{
  color:var(--ink);
  font-weight:700;
}
.bdash-sub{
  display:block;
  margin-top:4px;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.5;
}
.bdash-list{
  display:grid;
  gap:12px;
}
.bdash-list-item{
  border:1px solid var(--line-strong);
  border-radius:18px;
  padding:14px;
  background:var(--surface-2);
}
.bdash-list-item strong{
  display:block;
  color:var(--ink);
}
.bdash-list-item span{
  display:block;
  margin-top:6px;
  color:var(--muted-color);
  line-height:1.55;
  font-size:13px;
}
.bdash-empty{
  border:1px dashed var(--line-strong);
  border-radius:18px;
  padding:28px 18px;
  text-align:center;
  color:var(--muted-color);
}
.bdash-loader{
  min-height:220px;
  display:grid;
  place-items:center;
  color:var(--muted-color);
  text-align:center;
  line-height:1.6;
}
.bdash-pill{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
  line-height:1;
}
.bdash-pill.pending{background:rgba(245,158,11,.12);color:#a16207}
.bdash-pill.approved{background:rgba(16,185,129,.12);color:#047857}
.bdash-pill.done{background:rgba(37,99,235,.12);color:#1d4ed8}
.bdash-pill.rejected{background:rgba(239,68,68,.12);color:#b91c1c}
.bdash-pill.cancelled{background:rgba(107,114,128,.12);color:#374151}
.bdash-row-actions{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
}
@media (max-width: 1399.98px){
  .bdash-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
}
@media (max-width: 1199.98px){
  .bdash-panels{grid-template-columns:1fr}
}
@media (max-width: 767.98px){
  .bdash-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  .bdash-actions{width:100%}
  .bdash-actions .btn{flex:1 1 0}
}
@media (max-width: 575.98px){
  .bdash-grid{grid-template-columns:1fr}
}
</style>
@endpush

@section('content')
<div class="bdash-wrap">
  <div class="bdash-head">
    <div class="bdash-head-copy">
      <h1 id="dashboardTitle">Dashboard</h1>
      <p id="dashboardSubtitle">Loading your booking summary...</p>
    </div>
    <div class="bdash-actions" id="dashboardActions"></div>
  </div>

  <div id="dashboardLoader" class="bdash-loader">
    <div>
      <div class="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
      <div>Loading dashboard...</div>
    </div>
  </div>

  <div id="dashboardContent" style="display:none;">
    <div class="bdash-grid" id="dashboardStats"></div>

    <div class="bdash-panels">
      <div class="bdash-card">
        <div class="bdash-card-head">
          <div>
            <h2 id="dashboardRecentTitle">Recent bookings</h2>
            <p id="dashboardRecentSubtitle">Latest booking activity from your account.</p>
          </div>
        </div>
        <div class="bdash-table-wrap">
          <table class="bdash-table">
            <thead>
              <tr>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="dashboardRecentBody"></tbody>
          </table>
        </div>
      </div>

      <div class="bdash-card">
        <div class="bdash-card-head">
          <div>
            <h2>Quick view</h2>
            <p>Key things worth checking from the booking flow.</p>
          </div>
        </div>
        <div class="bdash-list" id="dashboardHighlights"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  if (!token) {
    window.location.href = '/login';
    return;
  }

  const els = {
    title: document.getElementById('dashboardTitle'),
    subtitle: document.getElementById('dashboardSubtitle'),
    actions: document.getElementById('dashboardActions'),
    loader: document.getElementById('dashboardLoader'),
    content: document.getElementById('dashboardContent'),
    stats: document.getElementById('dashboardStats'),
    recentTitle: document.getElementById('dashboardRecentTitle'),
    recentSubtitle: document.getElementById('dashboardRecentSubtitle'),
    recentBody: document.getElementById('dashboardRecentBody'),
    highlights: document.getElementById('dashboardHighlights')
  };

  let currentRole = 'patient';
  const rowsById = new Map();

  function authHeaders(extra = {}) {
    return Object.assign({
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }, extra);
  }

  function clearAuthAndExit() {
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('role');
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    window.location.href = '/login';
  }

  function esc(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function statusBadge(status) {
    const key = String(status || 'pending').toLowerCase();
    const label = key.charAt(0).toUpperCase() + key.slice(1);
    return `<span class="bdash-pill ${esc(key)}">${esc(label)}</span>`;
  }

  function renderStats(counts) {
    const items = [
      ['Total', counts.total || 0],
      ['Pending', counts.pending || 0],
      ['Approved', counts.approved || 0],
      ['Done', counts.done || 0],
      ['Rejected', counts.rejected || 0],
      ['Cancelled', counts.cancelled || 0]
    ];

    els.stats.innerHTML = items.map(([label, value]) => `
      <div class="bdash-stat">
        <div class="bdash-stat-label">${esc(label)}</div>
        <div class="bdash-stat-value">${esc(value)}</div>
      </div>
    `).join('');
  }

  function renderActions(role, links) {
    const items = role === 'admin'
      ? [
          ['Manage Bookings', links.manage_bookings || '/bookings/manage', 'fa-calendar-check', 'btn-primary'],
          ['My Profile', links.profile || '/profile', 'fa-user', 'btn-light']
        ]
      : [
          ['My Bookings', links.my_bookings || '/my-bookings', 'fa-calendar-days', 'btn-primary'],
          ['Find Doctors', links.find_doctors || '/find-doctors/departments', 'fa-magnifying-glass', 'btn-light'],
          ['My Profile', links.profile || '/profile', 'fa-user', 'btn-light']
        ];

    els.actions.innerHTML = items.map(([label, href, icon, klass]) => `
      <a href="${esc(href)}" class="btn ${klass}">
        <i class="fa ${esc(icon)} me-2"></i>${esc(label)}
      </a>
    `).join('');
  }

  function renderHighlights(role, counts) {
    const items = role === 'admin'
      ? [
          `There are <strong>${esc(counts.pending || 0)} pending</strong> bookings waiting for admin action.`,
          `Completed bookings are now tracked as <strong>${esc(counts.done || 0)} done</strong> entries.`,
          `Use Manage Bookings to approve or reject requests with notes that patients can read later.`
        ]
      : [
          `You have created <strong>${esc(counts.total || 0)} bookings</strong> from this account.`,
          `After a doctor visit is finished, mark the booking as <strong>Done</strong> and leave a review.`,
          `Open My Bookings anytime to view full details, filters, notes, and review status.`
        ];

    els.highlights.innerHTML = items.map((text) => `
      <div class="bdash-list-item"><span>${text}</span></div>
    `).join('');
  }

  function showClinicDetails(item) {
    Swal.fire({
      title: 'Clinic Details',
      width: 620,
      html: `
        <div class="text-start">
          <table class="table table-sm align-middle mb-0">
            <tbody>
              <tr><th class="ps-0">Clinic</th><td>${esc(item.clinic_name || 'Clinic to be confirmed')}</td></tr>
              <tr><th class="ps-0">Location</th><td>${esc(item.clinic_location || '—')}</td></tr>
              <tr><th class="ps-0">Address</th><td>${esc(item.clinic_address || '—')}</td></tr>
              <tr><th class="ps-0">Room</th><td>${esc(item.clinic_room_no || '—')}</td></tr>
              <tr><th class="ps-0">Visit Note</th><td>${esc(item.clinic_visit_note || 'No clinic note available')}</td></tr>
            </tbody>
          </table>
        </div>
      `,
      confirmButtonText: 'Close'
    });
  }

  function showDetails(id) {
    const item = rowsById.get(String(id));
    if (!item) return;

    Swal.fire({
      title: 'Booking Details',
      width: 760,
      html: `
        <div class="text-start">
          <div class="row g-3">
            <div class="col-md-6">
              <strong>Doctor</strong>
              <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                <span>${esc(item.doctor_name || 'Doctor')}</span>
                ${item.doctor_slug ? `<a class="btn btn-sm btn-light" href="/doctor/${encodeURIComponent(item.doctor_slug)}" target="_blank" rel="noopener"><i class="fa fa-arrow-up-right-from-square me-1"></i>View</a>` : ''}
              </div>
            </div>
            <div class="col-md-6">
              <strong>Clinic</strong>
              <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                <span>${esc(item.clinic_name || 'Clinic to be confirmed')}</span>
                <button type="button" class="btn btn-sm btn-light" id="dashboardClinicDetailsBtn"><i class="fa fa-hospital me-1"></i>Clinic Details</button>
              </div>
            </div>
            <div class="col-md-6"><strong>Patient</strong><div>${esc(item.patient_name || 'Patient')}</div></div>
            <div class="col-md-6"><strong>Phone</strong><div>${esc(item.patient_phone_number || '—')}</div></div>
            <div class="col-md-6"><strong>Booking For</strong><div>${esc(item.booking_for || 'self')}</div></div>
            <div class="col-md-6"><strong>Relationship</strong><div>${esc(item.relationship_with_patient || 'Self')}</div></div>
            <div class="col-md-6"><strong>Date</strong><div>${esc(item.appointment_date || '—')}</div></div>
            <div class="col-md-6"><strong>Time</strong><div>${esc(item.appointment_time || 'Not selected')}</div></div>
            <div class="col-md-6"><strong>Status</strong><div>${esc(item.status || 'pending')}</div></div>
            <div class="col-md-6"><strong>Consultation Mode</strong><div>${esc(item.consultation_mode || 'clinic_visit')}</div></div>
            <div class="col-12"><strong>Symptoms</strong><div>${esc(item.symptoms || 'No symptoms shared')}</div></div>
            <div class="col-12"><strong>Status Note</strong><div>${esc(item.status_note || 'No note available')}</div></div>
          </div>
        </div>
      `,
      didOpen: () => {
        const button = document.getElementById('dashboardClinicDetailsBtn');
        button?.addEventListener('click', function () {
          showClinicDetails(item);
        });
      },
      confirmButtonText: 'Close'
    });
  }

  async function submitReview(id) {
    const item = rowsById.get(String(id));
    if (!item) return;

    const result = await Swal.fire({
      title: 'Rate Dr. ' + (item.doctor_name || 'Doctor'),
      width: 620,
      html: `
        <div class="text-start">
          <label class="form-label">Rating</label>
          <select id="dashboardReviewRating" class="form-select mb-3">
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Very Good</option>
            <option value="3">3 - Good</option>
            <option value="2">2 - Fair</option>
            <option value="1">1 - Poor</option>
          </select>
          <label class="form-label">Title</label>
          <input id="dashboardReviewTitle" class="form-control mb-3" maxlength="160" placeholder="Short headline">
          <label class="form-label">Review</label>
          <textarea id="dashboardReviewText" class="form-control" rows="5" maxlength="2000" placeholder="Share your experience"></textarea>
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Submit Review',
      preConfirm: () => {
        const rating = document.getElementById('dashboardReviewRating').value;
        const title = document.getElementById('dashboardReviewTitle').value;
        const reviewText = document.getElementById('dashboardReviewText').value;
        if (!String(reviewText || '').trim()) {
          Swal.showValidationMessage('Please write your review.');
          return false;
        }
        return { rating, title, review_text: reviewText };
      }
    });

    if (!result.isConfirmed) return;

    try {
      const res = await fetch('/api/bookings/mine/' + encodeURIComponent(id) + '/review', {
        method: 'POST',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify(result.value)
      });
      const data = await res.json().catch(() => ({}));

      if (res.status === 401) {
        clearAuthAndExit();
        return;
      }

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'Unable to submit review.');
      }

      await Swal.fire({ icon: 'success', title: 'Review Submitted', text: data.message || 'Review submitted successfully.' });
      loadDashboard();
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Unable to submit review.' });
    }
  }

  function bindRecentActions() {
    document.querySelectorAll('.js-dashboard-details').forEach((button) => {
      button.addEventListener('click', function () {
        showDetails(this.dataset.id);
      });
    });

    document.querySelectorAll('.js-dashboard-review').forEach((button) => {
      button.addEventListener('click', function () {
        submitReview(this.dataset.id);
      });
    });
  }

  function renderRecent(items) {
    rowsById.clear();

    if (!Array.isArray(items) || !items.length) {
      els.recentBody.innerHTML = `
        <tr>
          <td colspan="5">
            <div class="bdash-empty">No bookings found yet.</div>
          </td>
        </tr>
      `;
      return;
    }

    items.forEach((item) => rowsById.set(String(item.id), item));

    els.recentBody.innerHTML = items.map((item) => `
      <tr>
        <td>
          <div class="bdash-main">${esc(item.doctor_name || 'Doctor')}</div>
          <span class="bdash-sub">${esc(item.clinic_name || 'Clinic to be confirmed')}</span>
        </td>
        <td>
          <div class="bdash-main">${esc(item.patient_name || 'Patient')}</div>
          <span class="bdash-sub">${item.booking_for === 'family' ? 'Family booking' : 'Self booking'}</span>
        </td>
        <td>
          <div class="bdash-main">${esc(item.appointment_date || '—')}</div>
          <span class="bdash-sub">${esc(item.appointment_time || 'Time not selected')}</span>
        </td>
        <td>${statusBadge(item.status)}</td>
        <td>
          <div class="bdash-row-actions">
            <button type="button" class="btn btn-light btn-sm js-dashboard-details" data-id="${esc(item.id)}"><i class="fa fa-eye me-1"></i>Details</button>
            ${currentRole === 'patient' && item.can_review ? `<button type="button" class="btn btn-primary btn-sm js-dashboard-review" data-id="${esc(item.id)}"><i class="fa fa-star me-1"></i>Review</button>` : ''}
            ${currentRole === 'admin' ? `<a href="/bookings/manage" class="btn btn-primary btn-sm"><i class="fa fa-arrow-up-right-from-square me-1"></i>Manage</a>` : ''}
          </div>
        </td>
      </tr>
    `).join('');

    bindRecentActions();
  }

  async function loadDashboard() {
    try {
      const res = await fetch('/api/bookings/dashboard', { headers: authHeaders() });
      const data = await res.json().catch(() => ({}));

      if (res.status === 401) {
        clearAuthAndExit();
        return;
      }

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'Unable to load dashboard.');
      }

      currentRole = data.role || 'patient';
      const counts = data.counts || {};
      const links = data.links || {};
      const recent = Array.isArray(data.recent) ? data.recent : [];

      els.title.textContent = currentRole === 'admin' ? 'Admin Booking Dashboard' : 'My Booking Dashboard';
      els.subtitle.textContent = currentRole === 'admin'
        ? 'Review booking requests, check detailed status, and keep the booking queue moving.'
        : 'Track your appointment requests, mark visits as done, and review doctors after your care journey is complete.';
      els.recentTitle.textContent = currentRole === 'admin' ? 'Latest booking requests' : 'Recent bookings';
      els.recentSubtitle.textContent = currentRole === 'admin'
        ? 'Newest appointment activity across the platform.'
        : 'Recent booking activity from your account, including review-ready visits.';

      renderActions(currentRole, links);
      renderStats(counts);
      renderRecent(recent);
      renderHighlights(currentRole, counts);

      els.loader.style.display = 'none';
      els.content.style.display = '';
    } catch (error) {
      els.loader.innerHTML = `<div class="bdash-empty">${esc(error.message || 'Unable to load dashboard.')}</div>`;
    }
  }

  loadDashboard();
});
</script>
@endsection
