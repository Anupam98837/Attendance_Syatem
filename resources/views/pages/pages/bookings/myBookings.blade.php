@extends('pages.layout.structure')

@section('title', 'My Bookings')

@push('styles')
<style>
.mbk-wrap{display:grid;gap:16px;max-width:100%;min-width:0}
.mbk-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  flex-wrap:wrap;
}
.mbk-head > div{min-width:0}
.mbk-head h1{
  margin:0;
  font-size:30px;
  font-weight:800;
  color:var(--ink);
}
.mbk-head p{
  margin:8px 0 0;
  color:var(--muted-color);
  line-height:1.65;
  max-width:820px;
}
.mbk-actions-top{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  max-width:100%;
}
.mbk-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  box-shadow:var(--shadow-1);
  padding:18px;
  max-width:100%;
  min-width:0;
  overflow:hidden;
}
.mbk-table-wrap{
  width:100%;
  max-width:100%;
  overflow:auto;
  -webkit-overflow-scrolling:touch;
  border-radius:18px;
  border:1px solid var(--line-strong);
}
.mbk-table-hint{
  display:none;
  margin-bottom:10px;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.5;
}
.mbk-table{
  width:100%;
  min-width:1040px;
  border-collapse:separate;
  border-spacing:0;
}
.mbk-table th,.mbk-table td{
  padding:14px 12px;
  border-bottom:1px solid var(--line-strong);
  vertical-align:top;
  white-space:nowrap;
}
.mbk-table td .mbk-main,
.mbk-table td .mbk-sub{white-space:normal}
.mbk-table th{
  color:var(--muted-color);
  text-transform:uppercase;
  letter-spacing:.08em;
  font-size:12px;
  background:var(--surface-2);
  position:sticky;
  top:0;
  z-index:1;
}
.mbk-table tbody tr:last-child td{border-bottom:0}
.mbk-table tbody tr.is-selected{background:var(--page-hover)}
.mbk-main{
  color:var(--ink);
  font-weight:700;
}
.mbk-sub{
  display:block;
  margin-top:5px;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.55;
}
.mbk-status{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
}
.mbk-status.pending{background:rgba(245,158,11,.12);color:#a16207}
.mbk-status.approved{background:rgba(16,185,129,.12);color:#047857}
.mbk-status.done{background:rgba(37,99,235,.12);color:#1d4ed8}
.mbk-status.rejected{background:rgba(239,68,68,.12);color:#b91c1c}
.mbk-status.cancelled{background:rgba(107,114,128,.12);color:#374151}
.mbk-actions{
  display:flex;
  justify-content:flex-end;
}
.mbk-action-item{
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
.mbk-action-item:hover{
  background:var(--surface-2);
}
.mbk-action-item.text-danger{
  color:#b91c1c!important;
}
.mbk-action-item:disabled{
  opacity:.55;
  cursor:not-allowed;
}
.mbk-action-toggle{
  width:36px;
  height:36px;
  display:inline-grid;
  place-items:center;
  border:1px solid var(--line-strong);
  border-radius:12px;
  background:var(--surface);
  color:var(--ink);
}
.mbk-action-toggle:hover,
.mbk-action-toggle.is-open{
  background:var(--surface-2);
}
#myBookingFloatingMenu{
  position:fixed;
  top:0;
  left:0;
  min-width:200px;
  max-width:min(240px, calc(100vw - 20px));
  padding:8px;
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:16px;
  box-shadow:0 18px 38px rgba(15,23,42,.12);
  z-index:1080;
  display:none;
}
#myBookingFloatingMenu .dropdown-divider{
  margin:6px 0;
  border-top:1px solid var(--line-strong);
}
.mbk-empty{
  border:1px dashed var(--line-strong);
  border-radius:18px;
  padding:28px 18px;
  text-align:center;
  color:var(--muted-color);
}
.mbk-drawer .offcanvas-body{
  display:grid;
  gap:14px;
}
.mbk-drawer.offcanvas-end{
  --bs-offcanvas-width:min(100vw,380px);
}
.mbk-filter-field{
  display:grid;
  gap:6px;
}
.mbk-filter-field label{
  color:var(--ink);
  font-size:13px;
  font-weight:700;
}
.mbk-filter-actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
@media (max-width: 767.98px){
  .mbk-actions-top{width:100%}
  .mbk-actions-top .btn{flex:1 1 calc(50% - 10px)}
  .mbk-card{padding:16px}
  .mbk-table{min-width:900px}
  .mbk-table th,.mbk-table td{padding:12px 10px;font-size:13px}
}
@media (max-width: 575.98px){
  .mbk-head h1{font-size:26px}
  .mbk-head p{font-size:14px}
  .mbk-table-hint{display:block}
  .mbk-actions-top .btn{flex:1 1 100%}
  .mbk-filter-actions .btn{flex:1 1 0}
  .mbk-drawer.offcanvas-end{--bs-offcanvas-width:100vw}
}
</style>
@endpush

@section('content')
<div class="mbk-wrap">
  <div class="mbk-head">
    <div>
      <h1>My Bookings</h1>
      <p>See every booking made from your account, open full details, cancel when needed, and view or submit doctor reviews after admin marks a visit as done.</p>
    </div>
    <div class="mbk-actions-top">
      <button type="button" class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#myBookingsFilterDrawer">
        <i class="fa fa-sliders me-2"></i>Filters
      </button>
      <a href="/find-doctors/departments" class="btn btn-primary">
        <i class="fa fa-magnifying-glass me-2"></i>Book Another Doctor
      </a>
      <button type="button" class="btn btn-light" id="bookingRefreshBtn">
        <i class="fa fa-rotate-right me-2"></i>Refresh
      </button>
    </div>
  </div>

  <div class="mbk-card">
    <div class="mbk-table-hint">
      <i class="fa fa-arrow-left-long me-1"></i>Swipe sideways on mobile to see all columns.
    </div>
    <div class="mbk-table-wrap">
      <table class="mbk-table">
        <thead>
          <tr>
            <th>Doctor</th>
            <th>Patient</th>
            <th>Appointment</th>
            <th>Status</th>
            <th>Review</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="bookingTableBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="myBookingFloatingMenu" aria-hidden="true"></div>

<div class="offcanvas offcanvas-end mbk-drawer" tabindex="-1" id="myBookingsFilterDrawer" aria-labelledby="myBookingsFilterDrawerLabel">
  <div class="offcanvas-header">
    <h5 id="myBookingsFilterDrawerLabel" class="mb-0">Filter My Bookings</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="mbk-filter-field">
      <label for="bookingFilterStatus">Status</label>
      <select id="bookingFilterStatus" class="form-select">
        <option value="all">All status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="done">Done</option>
        <option value="rejected">Rejected</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    <div class="mbk-filter-field">
      <label for="bookingFilterDoctor">Doctor</label>
      <select id="bookingFilterDoctor" class="form-select">
        <option value="0">All doctors</option>
      </select>
    </div>
    <div class="mbk-filter-field">
      <label for="bookingFilterDateFrom">Date From</label>
      <input type="date" id="bookingFilterDateFrom" class="form-control">
    </div>
    <div class="mbk-filter-field">
      <label for="bookingFilterDateTo">Date To</label>
      <input type="date" id="bookingFilterDateTo" class="form-control">
    </div>
    <div class="mbk-filter-actions">
      <button type="button" class="btn btn-light" id="bookingFilterResetBtn">Reset</button>
      <button type="button" class="btn btn-primary" id="bookingFilterApplyBtn" data-bs-dismiss="offcanvas">Apply</button>
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
    tableBody: document.getElementById('bookingTableBody'),
    floatingMenu: document.getElementById('myBookingFloatingMenu'),
    refresh: document.getElementById('bookingRefreshBtn'),
    filterStatus: document.getElementById('bookingFilterStatus'),
    filterDoctor: document.getElementById('bookingFilterDoctor'),
    filterDateFrom: document.getElementById('bookingFilterDateFrom'),
    filterDateTo: document.getElementById('bookingFilterDateTo'),
    filterReset: document.getElementById('bookingFilterResetBtn'),
    filterApply: document.getElementById('bookingFilterApplyBtn')
  };

  const rowsById = new Map();
  const state = {
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
    if (state.status && state.status !== 'all') params.set('status', state.status);
    if (state.doctor_id && state.doctor_id !== '0') params.set('doctor_id', state.doctor_id);
    if (state.date_from) params.set('date_from', state.date_from);
    if (state.date_to) params.set('date_to', state.date_to);
    return params.toString();
  }

  function statusBadge(status) {
    const key = String(status || 'pending').toLowerCase();
    const label = key.charAt(0).toUpperCase() + key.slice(1);
    return `<span class="mbk-status ${esc(key)}">${esc(label)}</span>`;
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

  function showReview(id) {
    const item = rowsById.get(String(id));
    if (!item) return;

    Swal.fire({
      title: item.review_title || 'Doctor Review',
      width: 680,
      html: `
        <div class="text-start">
          <div class="mb-2"><strong>Doctor:</strong> ${esc(item.doctor_name || 'Doctor')}</div>
          <div class="mb-2"><strong>Rating:</strong> ${esc(item.review_rating || '—')}/5</div>
          <div class="mb-2"><strong>Submitted:</strong> ${esc(item.review_created_at || '—')}</div>
          <div class="border rounded-4 p-3 bg-light-subtle">${esc(item.review_text || 'No review text')}</div>
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
                <button type="button" class="btn btn-sm btn-light" id="bookingClinicDetailsBtn"><i class="fa fa-hospital me-1"></i>Clinic Details</button>
              </div>
            </div>
            <div class="col-md-6"><strong>Patient</strong><div>${esc(item.patient_name || 'Patient')}</div></div>
            <div class="col-md-6"><strong>Phone</strong><div>${esc(item.patient_phone_number || '—')}</div></div>
            <div class="col-md-6"><strong>Alternative Phone</strong><div>${esc(item.patient_alternative_phone_number || '—')}</div></div>
            <div class="col-md-6"><strong>Email</strong><div>${esc(item.patient_email || '—')}</div></div>
            <div class="col-md-6"><strong>Booking For</strong><div>${esc(item.booking_for || 'self')}</div></div>
            <div class="col-md-6"><strong>Relationship</strong><div>${esc(item.relationship_with_patient || 'Self')}</div></div>
            <div class="col-md-6"><strong>Date</strong><div>${esc(item.appointment_date || '—')}</div></div>
            <div class="col-md-6"><strong>Time</strong><div>${esc(item.appointment_time || 'Not selected')}</div></div>
            <div class="col-md-6"><strong>Status</strong><div>${esc(item.status || 'pending')}</div></div>
            <div class="col-md-6"><strong>Mode</strong><div>${esc(item.consultation_mode || 'clinic_visit')}</div></div>
            <div class="col-12"><strong>Address</strong><div>${esc(item.patient_address || 'No address shared')}</div></div>
            <div class="col-12"><strong>Symptoms</strong><div>${esc(item.symptoms || 'No symptoms shared')}</div></div>
            <div class="col-12"><strong>Status Note</strong><div>${esc(item.status_note || 'No note available')}</div></div>
          </div>
        </div>
      `,
      didOpen: () => {
        document.getElementById('bookingClinicDetailsBtn')?.addEventListener('click', function () {
          showClinicDetails(item);
        });
      },
      confirmButtonText: 'Close'
    });
  }

  async function cancelBooking(id) {
    const result = await Swal.fire({
      title: 'Cancel this booking?',
      text: 'You can add a short cancellation note for the admin team.',
      input: 'textarea',
      inputPlaceholder: 'Optional cancellation note',
      showCancelButton: true,
      confirmButtonText: 'Cancel booking',
      confirmButtonColor: '#dc2626'
    });

    if (!result.isConfirmed) return;

    try {
      const res = await fetch('/api/bookings/mine/' + encodeURIComponent(id) + '/cancel', {
        method: 'POST',
        headers: authHeaders({ 'Content-Type': 'application/json' }),
        body: JSON.stringify({ status_note: result.value || '' })
      });
      const data = await res.json().catch(() => ({}));

      if (res.status === 401) {
        clearAuthAndExit();
        return;
      }

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'Unable to cancel booking.');
      }

      await Swal.fire({ icon: 'success', title: 'Cancelled', text: data.message || 'Booking cancelled successfully.' });
      loadBookings();
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Unable to cancel booking.' });
    }
  }

  async function submitReview(id) {
    const item = rowsById.get(String(id));
    if (!item) return;

    const result = await Swal.fire({
      title: 'Review Dr. ' + (item.doctor_name || 'Doctor'),
      width: 620,
      html: `
        <div class="text-start">
          <label class="form-label">Rating</label>
          <select id="bookingReviewRating" class="form-select mb-3">
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Very Good</option>
            <option value="3">3 - Good</option>
            <option value="2">2 - Fair</option>
            <option value="1">1 - Poor</option>
          </select>
          <label class="form-label">Title</label>
          <input id="bookingReviewTitle" class="form-control mb-3" maxlength="160" placeholder="Short review title">
          <label class="form-label">Review</label>
          <textarea id="bookingReviewText" class="form-control" rows="5" maxlength="2000" placeholder="Share your experience"></textarea>
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Submit Review',
      preConfirm: () => {
        const rating = document.getElementById('bookingReviewRating').value;
        const title = document.getElementById('bookingReviewTitle').value;
        const reviewText = document.getElementById('bookingReviewText').value;
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
      loadBookings();
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'Unable to submit review.' });
    }
  }

  function bindActions() {
    document.querySelectorAll('.js-view-review').forEach((button) => {
      button.addEventListener('click', function () {
        showReview(this.dataset.id);
      });
    });
  }

  function clearSelectedRows() {
    document.querySelectorAll('.mbk-action-toggle.is-open').forEach((button) => {
      button.classList.remove('is-open');
      button.setAttribute('aria-expanded', 'false');
    });
    document.querySelectorAll('#bookingTableBody tr.is-selected').forEach((row) => row.classList.remove('is-selected'));
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
    const actions = [
      `<button type="button" class="mbk-action-item" data-action="details"><i class="fa fa-eye"></i><span>View Details</span></button>`
    ];

    if (item.can_review) {
      actions.push(`<button type="button" class="mbk-action-item" data-action="review"><i class="fa fa-star"></i><span>Give Review</span></button>`);
    }

    if (item.can_cancel) {
      actions.push(`<button type="button" class="mbk-action-item text-danger" data-action="cancel"><i class="fa fa-ban"></i><span>Cancel Booking</span></button>`);
    }

    if (!item.can_review && !item.can_cancel) {
      actions.push('<button type="button" class="mbk-action-item" disabled><i class="fa fa-lock"></i><span>No More Actions</span></button>');
    }

    return actions.join('');
  }

  function renderRows(items) {
    rowsById.clear();

    if (!Array.isArray(items) || !items.length) {
      els.tableBody.innerHTML = `
        <tr>
          <td colspan="6"><div class="mbk-empty">No bookings found for the selected filters.</div></td>
        </tr>
      `;
      return;
    }

    items.forEach((item) => rowsById.set(String(item.id), item));

    els.tableBody.innerHTML = items.map((item) => `
      <tr data-booking-id="${esc(item.id)}">
        <td>
          <div class="mbk-main">${esc(item.doctor_name || 'Doctor')}</div>
          <span class="mbk-sub">${esc(item.clinic_name || 'Clinic to be confirmed')}</span>
        </td>
        <td>
          <div class="mbk-main">${esc(item.patient_name || 'Patient')}</div>
          <span class="mbk-sub">${item.booking_for === 'family' ? 'Family booking' : 'Self booking'}${item.relationship_with_patient ? ' • ' + esc(item.relationship_with_patient) : ''}</span>
        </td>
        <td>
          <div class="mbk-main">${esc(item.appointment_date || '—')}</div>
          <span class="mbk-sub">${esc(item.appointment_time || 'Time not selected')}</span>
          <span class="mbk-sub">${esc(item.status_note || 'No status note yet')}</span>
        </td>
        <td>${statusBadge(item.status)}</td>
        <td>
          ${item.review_id ? `<button type="button" class="btn btn-light btn-sm js-view-review" data-id="${esc(item.id)}"><i class="fa fa-eye me-1"></i>View</button>` : '<span class="mbk-sub">No review yet</span>'}
        </td>
        <td>
          <div class="mbk-actions">
            <button type="button" class="mbk-action-toggle js-booking-action-toggle" data-id="${esc(item.id)}" aria-expanded="false" aria-label="Booking actions">
              <i class="fa fa-ellipsis-vertical"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');

    bindActions();
  }

  function syncDrawerFields() {
    els.filterStatus.value = state.status;
    els.filterDoctor.value = state.doctor_id;
    els.filterDateFrom.value = state.date_from;
    els.filterDateTo.value = state.date_to;
  }

  function applyDrawerFields() {
    state.status = els.filterStatus.value || 'all';
    state.doctor_id = els.filterDoctor.value || '0';
    state.date_from = els.filterDateFrom.value || '';
    state.date_to = els.filterDateTo.value || '';
  }

  async function loadBookings() {
    try {
      const qs = buildParams();
      const res = await fetch('/api/bookings/mine' + (qs ? '?' + qs : ''), {
        headers: authHeaders()
      });
      const data = await res.json().catch(() => ({}));

      if (res.status === 401) {
        clearAuthAndExit();
        return;
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
          <td colspan="6"><div class="mbk-empty">${esc(error.message || 'Unable to load bookings.')}</div></td>
        </tr>
      `;
    }
  }

  els.filterApply.addEventListener('click', function () {
    applyDrawerFields();
    loadBookings();
  });

  els.filterReset.addEventListener('click', function () {
    state.status = 'all';
    state.doctor_id = '0';
    state.date_from = '';
    state.date_to = '';
    syncDrawerFields();
    loadBookings();
  });

  els.tableBody.addEventListener('click', function (e) {
    const reviewBtn = e.target.closest('.js-view-review');
    if (reviewBtn) {
      e.preventDefault();
      showReview(reviewBtn.dataset.id);
      return;
    }

    const toggle = e.target.closest('.js-booking-action-toggle');
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
    const actionBtn = e.target.closest('#myBookingFloatingMenu [data-action]');
    if (actionBtn) {
      e.preventDefault();
      e.stopPropagation();
      const bookingId = els.floatingMenu?.dataset.bookingId || '';
      hideFloatingMenu();
      if (!bookingId) return;
      if (actionBtn.dataset.action === 'details') return showDetails(bookingId);
      if (actionBtn.dataset.action === 'review') return submitReview(bookingId);
      if (actionBtn.dataset.action === 'cancel') return cancelBooking(bookingId);
      return;
    }

    if (!e.target.closest('#myBookingFloatingMenu, .js-booking-action-toggle')) {
      hideFloatingMenu();
    }
  });

  window.addEventListener('scroll', hideFloatingMenu, true);
  window.addEventListener('resize', hideFloatingMenu);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') hideFloatingMenu();
  });

  els.refresh.addEventListener('click', loadBookings);
  loadBookings();
});
</script>
@endsection
