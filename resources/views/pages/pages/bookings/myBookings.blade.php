@extends('pages.layout.structure')

@section('title', 'My Bookings')

@push('styles')
<style>
.mbk-wrap{display:grid;gap:16px}
.mbk-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  flex-wrap:wrap;
}
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
  max-width:780px;
}
.mbk-toolbar{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:10px;
  width:min(100%,860px);
}
.mbk-filter{
  position:relative;
}
.mbk-filter i{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:var(--muted-color);
  pointer-events:none;
}
.mbk-filter .form-select,
.mbk-filter .form-control{
  padding-left:38px;
}
.mbk-actions-top{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
.mbk-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  box-shadow:var(--shadow-1);
  padding:18px;
}
.mbk-stats{
  display:grid;
  grid-template-columns:repeat(6,minmax(0,1fr));
  gap:12px;
}
.mbk-stat{
  border:1px solid var(--line-strong);
  border-radius:18px;
  padding:16px;
  background:var(--surface-2);
}
.mbk-stat span{
  display:block;
  color:var(--muted-color);
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.08em;
  font-weight:700;
}
.mbk-stat strong{
  display:block;
  margin-top:10px;
  font-size:26px;
  color:var(--ink);
}
.mbk-table-wrap{overflow:auto;-webkit-overflow-scrolling:touch}
.mbk-table{
  width:100%;
  min-width:1220px;
  border-collapse:separate;
  border-spacing:0;
}
.mbk-table th,.mbk-table td{
  padding:14px 12px;
  border-bottom:1px solid var(--line-strong);
  vertical-align:top;
}
.mbk-table th{
  color:var(--muted-color);
  text-transform:uppercase;
  letter-spacing:.08em;
  font-size:12px;
}
.mbk-table tbody tr:last-child td{border-bottom:0}
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
  gap:8px;
  flex-wrap:wrap;
}
.mbk-empty{
  border:1px dashed var(--line-strong);
  border-radius:18px;
  padding:28px 18px;
  text-align:center;
  color:var(--muted-color);
}
@media (max-width: 1399.98px){
  .mbk-stats{grid-template-columns:repeat(3,minmax(0,1fr))}
}
@media (max-width: 991.98px){
  .mbk-toolbar{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width: 767.98px){
  .mbk-stats{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width: 575.98px){
  .mbk-toolbar,
  .mbk-stats{grid-template-columns:1fr}
  .mbk-actions-top{width:100%}
  .mbk-actions-top .btn{flex:1 1 0}
}
</style>
@endpush

@section('content')
<div class="mbk-wrap">
  <div class="mbk-head">
    <div>
      <h1>My Bookings</h1>
      <p>See every booking made from your account, open the full details, cancel when needed, and leave doctor reviews after admin marks the visit as done.</p>
    </div>
    <div class="mbk-actions-top">
      <a href="/find-doctors/departments" class="btn btn-primary">
        <i class="fa fa-magnifying-glass me-2"></i>Book Another Doctor
      </a>
      <button type="button" class="btn btn-light" id="bookingRefreshBtn">
        <i class="fa fa-rotate-right me-2"></i>Refresh
      </button>
    </div>
  </div>

  <div class="mbk-toolbar">
    <div class="mbk-filter">
      <i class="fa fa-filter"></i>
      <select id="bookingStatusFilter" class="form-select">
        <option value="all">All status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="done">Done</option>
        <option value="rejected">Rejected</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    <div class="mbk-filter">
      <i class="fa fa-user-doctor"></i>
      <select id="bookingDoctorFilter" class="form-select">
        <option value="0">All doctors</option>
      </select>
    </div>
    <div class="mbk-filter">
      <i class="fa fa-calendar-day"></i>
      <input type="date" id="bookingDateFrom" class="form-control">
    </div>
    <div class="mbk-filter">
      <i class="fa fa-calendar-check"></i>
      <input type="date" id="bookingDateTo" class="form-control">
    </div>
  </div>

  <div class="mbk-stats" id="bookingStats"></div>

  <div class="mbk-card">
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
    stats: document.getElementById('bookingStats'),
    tableBody: document.getElementById('bookingTableBody'),
    status: document.getElementById('bookingStatusFilter'),
    doctor: document.getElementById('bookingDoctorFilter'),
    dateFrom: document.getElementById('bookingDateFrom'),
    dateTo: document.getElementById('bookingDateTo'),
    refresh: document.getElementById('bookingRefreshBtn')
  };

  let filterTimer = null;
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
    return `<span class="mbk-status ${esc(key)}">${esc(label)}</span>`;
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
      <div class="mbk-stat">
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
        const button = document.getElementById('bookingClinicDetailsBtn');
        button?.addEventListener('click', function () {
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
    document.querySelectorAll('.js-booking-details').forEach((button) => {
      button.addEventListener('click', function () {
        showDetails(this.dataset.id);
      });
    });

    document.querySelectorAll('.js-cancel-booking').forEach((button) => {
      button.addEventListener('click', function () {
        cancelBooking(this.dataset.id);
      });
    });

    document.querySelectorAll('.js-submit-review').forEach((button) => {
      button.addEventListener('click', function () {
        submitReview(this.dataset.id);
      });
    });
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
      <tr>
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
          <div class="mbk-main">${item.review_rating ? esc(item.review_rating + '/5') : 'No review yet'}</div>
          <span class="mbk-sub">${esc(item.review_title || item.review_text || 'Review becomes available after the booking is marked done.')}</span>
        </td>
        <td>
          <div class="mbk-actions">
            <button type="button" class="btn btn-light btn-sm js-booking-details" data-id="${esc(item.id)}"><i class="fa fa-eye me-1"></i>Details</button>
            ${item.can_cancel ? `<button type="button" class="btn btn-outline-danger btn-sm js-cancel-booking" data-id="${esc(item.id)}"><i class="fa fa-ban me-1"></i>Cancel</button>` : ''}
            ${item.can_review ? `<button type="button" class="btn btn-success btn-sm js-submit-review" data-id="${esc(item.id)}"><i class="fa fa-star me-1"></i>Review</button>` : ''}
            <a href="/doctor/${encodeURIComponent(item.doctor_slug || '')}" class="btn btn-light btn-sm"><i class="fa fa-arrow-up-right-from-square me-1"></i>Doctor</a>
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
        date_to: els.dateTo.value || ''
      });

      const res = await fetch('/api/bookings/mine?' + params.toString(), {
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
      renderStats(data.counts || {});
      renderRows(data.bookings || []);
    } catch (error) {
      els.tableBody.innerHTML = `
        <tr>
          <td colspan="6"><div class="mbk-empty">${esc(error.message || 'Unable to load bookings.')}</div></td>
        </tr>
      `;
    }
  }

  [els.status, els.doctor, els.dateFrom, els.dateTo].forEach((input) => {
    input.addEventListener('change', loadBookings);
  });

  els.refresh.addEventListener('click', loadBookings);
  loadBookings();
});
</script>
@endsection
