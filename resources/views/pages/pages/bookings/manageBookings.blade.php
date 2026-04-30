@extends('pages.layout.structure')

@section('title', 'Manage Bookings')

@push('styles')
<style>
.abk-wrap{display:grid;gap:16px}
.abk-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  flex-wrap:wrap;
}
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
  max-width:820px;
}
.abk-actions-top{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
.abk-toolbar{
  display:grid;
  grid-template-columns:repeat(5,minmax(0,1fr));
  gap:10px;
}
.abk-filter{
  position:relative;
}
.abk-filter i{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:var(--muted-color);
  pointer-events:none;
}
.abk-filter .form-select,
.abk-filter .form-control{
  padding-left:38px;
}
.abk-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  box-shadow:var(--shadow-1);
  padding:18px;
}
.abk-stats{
  display:grid;
  grid-template-columns:repeat(6,minmax(0,1fr));
  gap:12px;
}
.abk-stat{
  border:1px solid var(--line-strong);
  border-radius:18px;
  padding:16px;
  background:var(--surface-2);
}
.abk-stat span{
  display:block;
  color:var(--muted-color);
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.08em;
  font-weight:700;
}
.abk-stat strong{
  display:block;
  margin-top:10px;
  font-size:26px;
  color:var(--ink);
}
.abk-table-wrap{overflow:auto;-webkit-overflow-scrolling:touch}
.abk-table{
  width:100%;
  min-width:1380px;
  border-collapse:separate;
  border-spacing:0;
}
.abk-table th,.abk-table td{
  padding:14px 12px;
  border-bottom:1px solid var(--line-strong);
  vertical-align:top;
}
.abk-table th{
  color:var(--muted-color);
  text-transform:uppercase;
  letter-spacing:.08em;
  font-size:12px;
}
.abk-table tbody tr:last-child td{border-bottom:0}
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
.abk-empty{
  border:1px dashed var(--line-strong);
  border-radius:18px;
  padding:28px 18px;
  text-align:center;
  color:var(--muted-color);
}
.abk-actions{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
}
@media (max-width: 1399.98px){
  .abk-stats{grid-template-columns:repeat(3,minmax(0,1fr))}
}
@media (max-width: 991.98px){
  .abk-toolbar{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width: 767.98px){
  .abk-stats{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width: 575.98px){
  .abk-toolbar,
  .abk-stats{grid-template-columns:1fr}
  .abk-actions-top{width:100%}
  .abk-actions-top .btn{flex:1 1 0}
}
</style>
@endpush

@section('content')
<div class="abk-wrap">
  <div class="abk-head">
    <div>
      <h1>Manage Bookings</h1>
      <p>Review all appointment requests, filter by doctor and date, open complete booking details, and send approval or rejection notes back to the patient side.</p>
    </div>
    <div class="abk-actions-top">
      <button type="button" class="btn btn-primary" id="adminBookingRefreshBtn">
        <i class="fa fa-rotate-right me-2"></i>Refresh
      </button>
    </div>
  </div>

  <div class="abk-toolbar">
    <div class="abk-filter">
      <i class="fa fa-magnifying-glass"></i>
      <input type="search" id="adminBookingSearch" class="form-control" placeholder="Search doctor, patient, phone...">
    </div>
    <div class="abk-filter">
      <i class="fa fa-filter"></i>
      <select id="adminBookingStatusFilter" class="form-select">
        <option value="all">All status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="done">Done</option>
        <option value="rejected">Rejected</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    <div class="abk-filter">
      <i class="fa fa-user-doctor"></i>
      <select id="adminBookingDoctorFilter" class="form-select">
        <option value="0">All doctors</option>
      </select>
    </div>
    <div class="abk-filter">
      <i class="fa fa-calendar-day"></i>
      <input type="date" id="adminBookingDateFrom" class="form-control">
    </div>
    <div class="abk-filter">
      <i class="fa fa-calendar-check"></i>
      <input type="date" id="adminBookingDateTo" class="form-control">
    </div>
  </div>

  <div class="abk-stats" id="adminBookingStats"></div>

  <div class="abk-card">
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
    stats: document.getElementById('adminBookingStats'),
    tableBody: document.getElementById('adminBookingTableBody'),
    status: document.getElementById('adminBookingStatusFilter'),
    doctor: document.getElementById('adminBookingDoctorFilter'),
    dateFrom: document.getElementById('adminBookingDateFrom'),
    dateTo: document.getElementById('adminBookingDateTo'),
    search: document.getElementById('adminBookingSearch'),
    refresh: document.getElementById('adminBookingRefreshBtn')
  };

  let searchTimer = null;
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
    return `<span class="abk-status ${esc(key)}">${esc(label)}</span>`;
  }

  function renderDoctorOptions(options) {
    const current = els.doctor.value || '0';
    els.doctor.innerHTML = '<option value="0">All doctors</option>' + (Array.isArray(options) ? options.map((item) => `
      <option value="${esc(item.id)}">${esc(item.name)}</option>
    `).join('') : '');
    els.doctor.value = current;
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
      <div class="abk-stat">
        <span>${esc(label)}</span>
        <strong>${esc(value)}</strong>
      </div>
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
        const button = document.getElementById('adminClinicDetailsBtn');
        button?.addEventListener('click', function () {
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
      text: 'The patient will see this note in their booking history.',
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
    document.querySelectorAll('.js-admin-details').forEach((button) => {
      button.addEventListener('click', function () {
        showDetails(this.dataset.id);
      });
    });

    document.querySelectorAll('.js-admin-approve').forEach((button) => {
      button.addEventListener('click', function () {
        updateStatus(this.dataset.id, 'approved');
      });
    });

    document.querySelectorAll('.js-admin-reject').forEach((button) => {
      button.addEventListener('click', function () {
        updateStatus(this.dataset.id, 'rejected');
      });
    });

    document.querySelectorAll('.js-admin-done').forEach((button) => {
      button.addEventListener('click', function () {
        updateStatus(this.dataset.id, 'done');
      });
    });
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
      <tr>
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
            <button type="button" class="btn btn-light btn-sm js-admin-details" data-id="${esc(item.id)}"><i class="fa fa-eye me-1"></i>Details</button>
            ${String(item.status || '').toLowerCase() === 'pending' ? `<button type="button" class="btn btn-success btn-sm js-admin-approve" data-id="${esc(item.id)}"><i class="fa fa-check me-1"></i>Approve</button>` : ''}
            ${String(item.status || '').toLowerCase() === 'approved' ? `<button type="button" class="btn btn-primary btn-sm js-admin-done" data-id="${esc(item.id)}"><i class="fa fa-circle-check me-1"></i>Done</button>` : ''}
            ${!['done', 'cancelled', 'rejected'].includes(String(item.status || '').toLowerCase()) ? `<button type="button" class="btn btn-danger btn-sm js-admin-reject" data-id="${esc(item.id)}"><i class="fa fa-xmark me-1"></i>Reject</button>` : ''}
            ${['done', 'cancelled'].includes(String(item.status || '').toLowerCase()) ? '<span class="abk-sub">Locked status</span>' : ''}
          </div>
        </td>
      </tr>
    `).join('');

    bindActions();
  }

  async function loadBookings() {
    try {
      const params = new URLSearchParams({
        status: els.status.value || 'all',
        doctor_id: els.doctor.value || '0',
        date_from: els.dateFrom.value || '',
        date_to: els.dateTo.value || '',
        search: els.search.value || ''
      });

      const res = await fetch('/api/bookings/manage?' + params.toString(), {
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
      renderStats(data.counts || {});
      renderRows(data.bookings || []);
    } catch (error) {
      els.tableBody.innerHTML = `
        <tr>
          <td colspan="7"><div class="abk-empty">${esc(error.message || 'Unable to load bookings.')}</div></td>
        </tr>
      `;
    }
  }

  [els.status, els.doctor, els.dateFrom, els.dateTo].forEach((input) => {
    input.addEventListener('change', loadBookings);
  });

  els.search.addEventListener('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadBookings, 280);
  });

  els.refresh.addEventListener('click', loadBookings);
  loadBookings();
});
</script>
@endsection
