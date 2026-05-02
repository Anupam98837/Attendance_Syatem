@extends('pages.layout.structure')

@section('title', 'Manage Bookings')

@push('styles')
<style>
.abk-wrap{display:grid;gap:16px;max-width:100%;min-width:0}
.abk-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  flex-wrap:wrap;
}
.abk-head > div{min-width:0}
.abk-head h1{
  margin:0;
  font-size:30px;
  font-weight:800;
  color:var(--ink);
}
.abk-head p{
  margin:8px 0 0;
  color:var(--muted-color);
  line-height:1.65;
  max-width:840px;
}
.abk-actions-top{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  max-width:100%;
}
.abk-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  box-shadow:var(--shadow-1);
  padding:18px;
  max-width:100%;
  min-width:0;
  overflow:hidden;
}
.abk-table-wrap{
  width:100%;
  max-width:100%;
  overflow:auto;
  -webkit-overflow-scrolling:touch;
  border-radius:18px;
  border:1px solid var(--line-strong);
}
.abk-table-hint{
  display:none;
  margin-bottom:10px;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.5;
}
.abk-table{
  width:100%;
  min-width:1120px;
  border-collapse:separate;
  border-spacing:0;
}
.abk-table th,.abk-table td{
  padding:14px 12px;
  border-bottom:1px solid var(--line-strong);
  vertical-align:top;
  white-space:nowrap;
}
.abk-table td .abk-main,
.abk-table td .abk-sub{white-space:normal}
.abk-table th{
  color:var(--muted-color);
  text-transform:uppercase;
  letter-spacing:.08em;
  font-size:12px;
  background:var(--surface-2);
  position:sticky;
  top:0;
  z-index:1;
}
.abk-table tbody tr:last-child td{border-bottom:0}
.abk-table tbody tr.is-selected{background:var(--page-hover)}
.abk-main{
  color:var(--ink);
  font-weight:700;
}
.abk-sub{
  display:block;
  margin-top:5px;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.55;
}
.abk-status{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
}
.abk-status.pending{background:rgba(245,158,11,.12);color:#a16207}
.abk-status.approved{background:rgba(16,185,129,.12);color:#047857}
.abk-status.done{background:rgba(37,99,235,.12);color:#1d4ed8}
.abk-status.rejected{background:rgba(239,68,68,.12);color:#b91c1c}
.abk-status.cancelled{background:rgba(107,114,128,.12);color:#374151}
.abk-actions{
  display:flex;
  justify-content:flex-end;
}
.abk-action-item{
  display:flex;
  align-items:center;
  gap:10px;
  width:100%;
  border:0;
  background:transparent;
  color:var(--ink);
  border-radius:12px;
  padding:10px 12px;
  text-align:left;
  font-size:13px;
}
.abk-action-item:hover{
  background:var(--surface-2);
}
.abk-action-item.text-danger{
  color:#b91c1c!important;
}
.abk-action-item:disabled{
  opacity:.55;
  cursor:not-allowed;
}
.abk-action-toggle{
  width:36px;
  height:36px;
  display:inline-grid;
  place-items:center;
  border:1px solid var(--line-strong);
  border-radius:12px;
  background:var(--surface);
  color:var(--ink);
}
.abk-action-toggle:hover,
.abk-action-toggle.is-open{
  background:var(--surface-2);
}
#adminBookingFloatingMenu{
  position:fixed;
  top:0;
  left:0;
  min-width:210px;
  max-width:min(250px, calc(100vw - 20px));
  padding:8px;
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:16px;
  box-shadow:0 18px 38px rgba(15,23,42,.12);
  z-index:1080;
  display:none;
}
#adminBookingFloatingMenu .dropdown-divider{
  margin:6px 0;
  border-top:1px solid var(--line-strong);
}
.abk-empty{
  border:1px dashed var(--line-strong);
  border-radius:18px;
  padding:28px 18px;
  text-align:center;
  color:var(--muted-color);
}
.abk-drawer .offcanvas-body{
  display:grid;
  gap:14px;
}
.abk-drawer.offcanvas-end{
  --bs-offcanvas-width:min(100vw,400px);
}
.abk-filter-field{
  display:grid;
  gap:6px;
}
.abk-filter-field label{
  color:var(--ink);
  font-size:13px;
  font-weight:700;
}
.abk-filter-actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
@media (max-width: 767.98px){
  .abk-actions-top{width:100%}
  .abk-actions-top .btn{flex:1 1 calc(50% - 10px)}
  .abk-card{padding:16px}
  .abk-table{min-width:960px}
  .abk-table th,.abk-table td{padding:12px 10px;font-size:13px}
}
@media (max-width: 575.98px){
  .abk-head h1{font-size:26px}
  .abk-head p{font-size:14px}
  .abk-table-hint{display:block}
  .abk-actions-top .btn{flex:1 1 100%}
  .abk-filter-actions .btn{flex:1 1 0}
  .abk-drawer.offcanvas-end{--bs-offcanvas-width:100vw}
}
</style>
@endpush

@section('content')
<div class="abk-wrap">
  <div class="abk-head">
    <div>
      <h1>Manage Bookings</h1>
      <p>Review all appointment requests, use the filter drawer to narrow by status, doctor, date, or search, and manage approval, rejection, and done state from one screen.</p>
    </div>
    <div class="abk-actions-top">
      <button type="button" class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#manageBookingsFilterDrawer">
        <i class="fa fa-sliders me-2"></i>Filters
      </button>
      <button type="button" class="btn btn-primary" id="adminBookingRefreshBtn">
        <i class="fa fa-rotate-right me-2"></i>Refresh
      </button>
    </div>
  </div>

  <div class="abk-card">
    <div class="abk-table-hint">
      <i class="fa fa-arrow-left-long me-1"></i>Swipe sideways on mobile to see all columns.
    </div>
    <div class="abk-table-wrap">
      <table class="abk-table">
        <thead>
          <tr>
            <th>Doctor</th>
            <th>Patient</th>
            <th>Booked By</th>
            <th>Appointment</th>
            <th>Status</th>
            <th>Review</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="adminBookingTableBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="adminBookingFloatingMenu" aria-hidden="true"></div>

<div class="offcanvas offcanvas-end abk-drawer" tabindex="-1" id="manageBookingsFilterDrawer" aria-labelledby="manageBookingsFilterDrawerLabel">
  <div class="offcanvas-header">
    <h5 id="manageBookingsFilterDrawerLabel" class="mb-0">Filter Bookings</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="abk-filter-field">
      <label for="adminBookingFilterSearch">Search</label>
      <input type="search" id="adminBookingFilterSearch" class="form-control" placeholder="Doctor, patient, phone...">
    </div>
    <div class="abk-filter-field">
      <label for="adminBookingFilterStatus">Status</label>
      <select id="adminBookingFilterStatus" class="form-select">
        <option value="all">All status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="done">Done</option>
        <option value="rejected">Rejected</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    <div class="abk-filter-field">
      <label for="adminBookingFilterDoctor">Doctor</label>
      <select id="adminBookingFilterDoctor" class="form-select">
        <option value="0">All doctors</option>
      </select>
    </div>
    <div class="abk-filter-field">
      <label for="adminBookingFilterDateFrom">Date From</label>
      <input type="date" id="adminBookingFilterDateFrom" class="form-control">
    </div>
    <div class="abk-filter-field">
      <label for="adminBookingFilterDateTo">Date To</label>
      <input type="date" id="adminBookingFilterDateTo" class="form-control">
    </div>
    <div class="abk-filter-actions">
      <button type="button" class="btn btn-light" id="adminBookingFilterResetBtn">Reset</button>
      <button type="button" class="btn btn-primary" id="adminBookingFilterApplyBtn" data-bs-dismiss="offcanvas">Apply</button>
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
    tableBody: document.getElementById('adminBookingTableBody'),
    floatingMenu: document.getElementById('adminBookingFloatingMenu'),
    refresh: document.getElementById('adminBookingRefreshBtn'),
    filterSearch: document.getElementById('adminBookingFilterSearch'),
    filterStatus: document.getElementById('adminBookingFilterStatus'),
    filterDoctor: document.getElementById('adminBookingFilterDoctor'),
    filterDateFrom: document.getElementById('adminBookingFilterDateFrom'),
    filterDateTo: document.getElementById('adminBookingFilterDateTo'),
    filterReset: document.getElementById('adminBookingFilterResetBtn'),
    filterApply: document.getElementById('adminBookingFilterApplyBtn')
  };

  const rowsById = new Map();
  const state = {
    search: '',
    status: 'all',
    doctor_id: '0',
    date_from: '',
    date_to: '',
    doctorOptions: []
  };

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

  function buildParams() {
    const params = new URLSearchParams();
    if (state.search) params.set('search', state.search);
    if (state.status && state.status !== 'all') params.set('status', state.status);
    if (state.doctor_id && state.doctor_id !== '0') params.set('doctor_id', state.doctor_id);
    if (state.date_from) params.set('date_from', state.date_from);
    if (state.date_to) params.set('date_to', state.date_to);
    return params.toString();
  }

  function statusBadge(status) {
    const key = String(status || 'pending').toLowerCase();
    const label = key.charAt(0).toUpperCase() + key.slice(1);
    return `<span class="abk-status ${esc(key)}">${esc(label)}</span>`;
  }

  function renderDoctorOptions(options) {
    state.doctorOptions = Array.isArray(options) ? options : [];
    els.filterDoctor.innerHTML = '<option value="0">All doctors</option>' + state.doctorOptions.map((item) => `
      <option value="${esc(item.id)}">${esc(item.name)}</option>
    `).join('');
    els.filterDoctor.value = state.doctor_id;
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
      width: 780,
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
                <button type="button" class="btn btn-sm btn-light" id="adminClinicDetailsBtn"><i class="fa fa-hospital me-1"></i>Clinic Details</button>
              </div>
            </div>
            <div class="col-md-6"><strong>Patient</strong><div>${esc(item.patient_name || 'Patient')}</div></div>
            <div class="col-md-6"><strong>Patient Phone</strong><div>${esc(item.patient_phone_number || '—')}</div></div>
            <div class="col-md-6"><strong>Booked By</strong><div>${esc(item.booked_by_name || 'User')}</div></div>
            <div class="col-md-6"><strong>Booked By Email</strong><div>${esc(item.booked_by_email || '—')}</div></div>
            <div class="col-md-6"><strong>Booking For</strong><div>${esc(item.booking_for || 'self')}</div></div>
            <div class="col-md-6"><strong>Relationship</strong><div>${esc(item.relationship_with_patient || 'Self')}</div></div>
            <div class="col-md-6"><strong>Date</strong><div>${esc(item.appointment_date || '—')}</div></div>
            <div class="col-md-6"><strong>Time</strong><div>${esc(item.appointment_time || 'Not selected')}</div></div>
            <div class="col-md-6"><strong>Status</strong><div>${esc(item.status || 'pending')}</div></div>
            <div class="col-md-6"><strong>Mode</strong><div>${esc(item.consultation_mode || 'clinic_visit')}</div></div>
            <div class="col-12"><strong>Symptoms</strong><div>${esc(item.symptoms || 'No symptoms shared')}</div></div>
            <div class="col-12"><strong>Status Note</strong><div>${esc(item.status_note || 'No note available')}</div></div>
            <div class="col-12"><strong>Review</strong><div>${item.review_rating ? esc(item.review_rating + '/5 - ' + (item.review_title || item.review_text || 'Review submitted')) : 'No review submitted yet'}</div></div>
          </div>
        </div>
      `,
      didOpen: () => {
        document.getElementById('adminClinicDetailsBtn')?.addEventListener('click', function () {
          showClinicDetails(item);
        });
      },
      confirmButtonText: 'Close'
    });
  }

  async function updateStatus(id, status) {
    const label = status === 'approved' ? 'Approve' : status === 'done' ? 'Mark Done' : 'Reject';
    const result = await Swal.fire({
      title: label + ' this booking?',
      text: 'The patient will see this note in booking history.',
      input: 'textarea',
      inputLabel: 'Status note',
      inputPlaceholder: status === 'approved'
        ? 'Example: Approved for the requested slot.'
        : status === 'done'
          ? 'Example: Consultation completed successfully.'
          : 'Example: Requested slot unavailable, please pick another date.',
      inputValidator: (value) => {
        if (!String(value || '').trim()) {
          return 'Please write a note.';
        }
      },
      showCancelButton: true,
      confirmButtonText: label,
      confirmButtonColor: status === 'approved' ? '#059669' : status === 'done' ? '#2563eb' : '#dc2626'
    });

    if (!result.isConfirmed) return;

    try {
      const res = await fetch('/api/bookings/manage/' + encodeURIComponent(id) + '/status', {
        method: 'POST',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify({
          status,
          status_note: result.value || ''
        })
      });
      const data = await res.json().catch(() => ({}));

      if (res.status === 401) {
        clearAuthAndExit();
        return;
      }

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'Unable to update booking status.');
      }

      await Swal.fire({ icon: 'success', title: 'Updated', text: data.message || 'Booking status updated successfully.' });
      loadBookings();
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Unable to update booking status.' });
    }
  }

  function bindActions() {
  }

  function clearSelectedRows() {
    document.querySelectorAll('.abk-action-toggle.is-open').forEach((button) => {
      button.classList.remove('is-open');
      button.setAttribute('aria-expanded', 'false');
    });
    document.querySelectorAll('#adminBookingTableBody tr.is-selected').forEach((row) => row.classList.remove('is-selected'));
  }

  function hideFloatingMenu() {
    if (!els.floatingMenu) return;
    els.floatingMenu.style.display = 'none';
    els.floatingMenu.style.visibility = 'hidden';
    els.floatingMenu.innerHTML = '';
    els.floatingMenu.setAttribute('aria-hidden', 'true');
    delete els.floatingMenu.dataset.bookingId;
    clearSelectedRows();
  }

  function positionFloatingMenu(button) {
    if (!els.floatingMenu || !button) return;
    const rect = button.getBoundingClientRect();
    const gap = 8;

    let left = rect.right - els.floatingMenu.offsetWidth;
    let top = rect.bottom + gap;

    if (left < 10) left = 10;
    if (left + els.floatingMenu.offsetWidth > window.innerWidth - 10) {
      left = Math.max(10, window.innerWidth - els.floatingMenu.offsetWidth - 10);
    }

    if (top + els.floatingMenu.offsetHeight > window.innerHeight - 10) {
      const aboveTop = rect.top - els.floatingMenu.offsetHeight - gap;
      top = aboveTop >= 10 ? aboveTop : Math.max(10, window.innerHeight - els.floatingMenu.offsetHeight - 10);
    }

    els.floatingMenu.style.top = top + 'px';
    els.floatingMenu.style.left = left + 'px';
  }

  function buildFloatingMenuHtml(item) {
    const status = String(item.status || '').toLowerCase();
    const actions = [
      `<button type="button" class="abk-action-item" data-action="details"><i class="fa fa-eye"></i><span>View Details</span></button>`
    ];

    if (status === 'pending') {
      actions.push(`<button type="button" class="abk-action-item" data-action="approve"><i class="fa fa-check"></i><span>Approve Booking</span></button>`);
    }

    if (status === 'approved') {
      actions.push(`<button type="button" class="abk-action-item" data-action="done"><i class="fa fa-circle-check"></i><span>Mark as Done</span></button>`);
    }

    if (!['done', 'cancelled', 'rejected'].includes(status)) {
      actions.push(`<button type="button" class="abk-action-item text-danger" data-action="reject"><i class="fa fa-xmark"></i><span>Reject Booking</span></button>`);
    }

    if (['done', 'cancelled'].includes(status)) {
      actions.push('<button type="button" class="abk-action-item" disabled><i class="fa fa-lock"></i><span>Locked Status</span></button>');
    }

    return actions.join('');
  }

  function renderRows(items) {
    rowsById.clear();

    if (!Array.isArray(items) || !items.length) {
      els.tableBody.innerHTML = `
        <tr>
          <td colspan="7"><div class="abk-empty">No bookings found for the selected filters.</div></td>
        </tr>
      `;
      return;
    }

    items.forEach((item) => rowsById.set(String(item.id), item));

    els.tableBody.innerHTML = items.map((item) => `
      <tr data-booking-id="${esc(item.id)}">
        <td>
          <div class="abk-main">${esc(item.doctor_name || 'Doctor')}</div>
          <span class="abk-sub">${esc(item.clinic_name || 'Clinic to be confirmed')}</span>
        </td>
        <td>
          <div class="abk-main">${esc(item.patient_name || 'Patient')}</div>
          <span class="abk-sub">${esc(item.patient_phone_number || 'No phone')}</span>
          <span class="abk-sub">${item.booking_for === 'family' ? 'Family booking' : 'Self booking'}${item.relationship_with_patient ? ' • ' + esc(item.relationship_with_patient) : ''}</span>
        </td>
        <td>
          <div class="abk-main">${esc(item.booked_by_name || 'User')}</div>
          <span class="abk-sub">${esc(item.booked_by_email || 'No email')}</span>
        </td>
        <td>
          <div class="abk-main">${esc(item.appointment_date || '—')}</div>
          <span class="abk-sub">${esc(item.appointment_time || 'Time not selected')}</span>
          <span class="abk-sub">${esc(item.status_note || 'No status note yet')}</span>
        </td>
        <td>${statusBadge(item.status)}</td>
        <td>
          <div class="abk-main">${item.review_rating ? esc(item.review_rating + '/5') : 'No review yet'}</div>
          <span class="abk-sub">${esc(item.review_title || item.review_text || 'Review will appear after patient submission.')}</span>
        </td>
        <td>
          <div class="abk-actions">
            <button type="button" class="abk-action-toggle js-admin-action-toggle" data-id="${esc(item.id)}" aria-expanded="false" aria-label="Booking actions">
              <i class="fa fa-ellipsis-vertical"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');

    bindActions();
  }

  function syncDrawerFields() {
    els.filterSearch.value = state.search;
    els.filterStatus.value = state.status;
    els.filterDoctor.value = state.doctor_id;
    els.filterDateFrom.value = state.date_from;
    els.filterDateTo.value = state.date_to;
  }

  function applyDrawerFields() {
    state.search = els.filterSearch.value.trim();
    state.status = els.filterStatus.value || 'all';
    state.doctor_id = els.filterDoctor.value || '0';
    state.date_from = els.filterDateFrom.value || '';
    state.date_to = els.filterDateTo.value || '';
  }

  async function loadBookings() {
    try {
      const qs = buildParams();
      const res = await fetch('/api/bookings/manage' + (qs ? '?' + qs : ''), {
        headers: authHeaders()
      });
      const data = await res.json().catch(() => ({}));

      if (res.status === 401) {
        clearAuthAndExit();
        return;
      }

      if (res.status === 403) {
        throw new Error(data.message || 'Only admin users can open this page.');
      }

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'Unable to load bookings.');
      }

      renderDoctorOptions(data.doctor_options || []);
      renderRows(data.bookings || []);
      syncDrawerFields();
    } catch (error) {
      els.tableBody.innerHTML = `
        <tr>
          <td colspan="7"><div class="abk-empty">${esc(error.message || 'Unable to load bookings.')}</div></td>
        </tr>
      `;
    }
  }

  els.filterApply.addEventListener('click', function () {
    applyDrawerFields();
    loadBookings();
  });

  els.filterReset.addEventListener('click', function () {
    state.search = '';
    state.status = 'all';
    state.doctor_id = '0';
    state.date_from = '';
    state.date_to = '';
    syncDrawerFields();
    loadBookings();
  });

  els.refresh.addEventListener('click', loadBookings);

  els.tableBody.addEventListener('click', function (e) {
    const toggle = e.target.closest('.js-admin-action-toggle');
    if (!toggle) return;

    e.preventDefault();
    e.stopPropagation();

    const row = toggle.closest('tr');
    const bookingId = String(toggle.dataset.id || row?.dataset.bookingId || '');
    const item = rowsById.get(bookingId);
    if (!row || !item || !els.floatingMenu) return;

    if (els.floatingMenu.style.display === 'block' && els.floatingMenu.dataset.bookingId === bookingId) {
      hideFloatingMenu();
      return;
    }

    clearSelectedRows();
    row.classList.add('is-selected');
    toggle.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');

    els.floatingMenu.innerHTML = buildFloatingMenuHtml(item);
    els.floatingMenu.dataset.bookingId = bookingId;
    els.floatingMenu.style.display = 'block';
    els.floatingMenu.style.visibility = 'hidden';

    requestAnimationFrame(() => {
      positionFloatingMenu(toggle);
      els.floatingMenu.style.visibility = 'visible';
      els.floatingMenu.setAttribute('aria-hidden', 'false');
    });
  });

  document.addEventListener('click', function (e) {
    const actionBtn = e.target.closest('#adminBookingFloatingMenu [data-action]');
    if (actionBtn) {
      e.preventDefault();
      e.stopPropagation();
      const bookingId = els.floatingMenu?.dataset.bookingId || '';
      hideFloatingMenu();
      if (!bookingId) return;
      if (actionBtn.dataset.action === 'details') return showDetails(bookingId);
      if (actionBtn.dataset.action === 'approve') return updateStatus(bookingId, 'approved');
      if (actionBtn.dataset.action === 'done') return updateStatus(bookingId, 'done');
      if (actionBtn.dataset.action === 'reject') return updateStatus(bookingId, 'rejected');
      return;
    }

    if (!e.target.closest('#adminBookingFloatingMenu, .js-admin-action-toggle')) {
      hideFloatingMenu();
    }
  });

  window.addEventListener('scroll', hideFloatingMenu, true);
  window.addEventListener('resize', hideFloatingMenu);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') hideFloatingMenu();
  });

  loadBookings();
});
</script>
@endsection
