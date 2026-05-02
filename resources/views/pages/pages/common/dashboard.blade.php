@extends('pages.layout.structure')

@section('title', 'Dashboard')

@push('styles')
<style>
.bdash-wrap{display:grid;gap:16px;max-width:100%;min-width:0}
.bdash-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:16px;
  flex-wrap:wrap;
}
.bdash-head > div{min-width:0}
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
  max-width:100%;
}
#dashboardActions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  max-width:100%;
}
.bdash-admin-shell{
  display:grid;
  gap:16px;
}
.bdash-metric-grid{
  display:grid;
  grid-template-columns:repeat(6,minmax(0,1fr));
  gap:12px;
}
.bdash-metric-card{
  position:relative;
  overflow:hidden;
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  box-shadow:var(--shadow-1);
  padding:18px;
}
.bdash-metric-card::after{
  content:"";
  position:absolute;
  inset:auto 0 0 0;
  height:4px;
  background:var(--metric-accent, #2563eb);
  opacity:.9;
}
.bdash-metric-card-top{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
}
.bdash-metric-card span{
  display:block;
  color:var(--muted-color);
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.08em;
  font-weight:700;
}
.bdash-metric-card strong{
  display:block;
  margin-top:10px;
  color:var(--ink);
  font-size:30px;
  line-height:1;
}
.bdash-metric-card p{
  margin:10px 0 0;
  color:var(--muted-color);
  font-size:13px;
  line-height:1.6;
}
.bdash-metric-icon{
  width:44px;
  height:44px;
  border-radius:14px;
  display:grid;
  place-items:center;
  background:rgba(15,23,42,.06);
  color:var(--metric-accent, #2563eb);
  font-size:16px;
  flex:0 0 auto;
}
.bdash-tone-primary{--metric-accent:#2563eb}
.bdash-tone-warning{--metric-accent:#d97706}
.bdash-tone-success{--metric-accent:#059669}
.bdash-tone-info{--metric-accent:#0891b2}
.bdash-tone-violet{--metric-accent:#7c3aed}
.bdash-tone-neutral{--metric-accent:#475569}
.bdash-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  padding:18px;
  box-shadow:var(--shadow-1);
  max-width:100%;
  min-width:0;
  overflow:hidden;
}
.bdash-card-head{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:12px;
  margin-bottom:14px;
  flex-wrap:wrap;
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
.bdash-table-wrap{
  width:100%;
  max-width:100%;
  overflow:auto;
  -webkit-overflow-scrolling:touch;
  border-radius:18px;
  border:1px solid var(--line-strong);
}
.bdash-table-hint{
  display:none;
  margin-bottom:10px;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.5;
}
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
  white-space:nowrap;
}
.bdash-table td .bdash-sub,
.bdash-table td .bdash-main{white-space:normal}
.bdash-table th{
  color:var(--muted-color);
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.07em;
  background:var(--surface-2);
  position:sticky;
  top:0;
  z-index:1;
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
.bdash-list-item span{
  display:block;
  color:var(--muted-color);
  line-height:1.6;
  font-size:13px;
}
.bdash-top-line{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  color:var(--ink);
  font-weight:700;
}
.bdash-top-meta{
  display:flex;
  flex-wrap:wrap;
  gap:8px 12px;
  margin-top:8px;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.5;
}
.bdash-snapshot{
  display:grid;
  gap:16px;
}
.bdash-snapshot-block{
  display:grid;
  gap:10px;
}
.bdash-snapshot-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:10px 12px;
  border:1px solid var(--line-strong);
  border-radius:16px;
  background:var(--surface-2);
}
.bdash-snapshot-row span{
  color:var(--muted-color);
  font-size:13px;
}
.bdash-snapshot-row strong{
  color:var(--ink);
  font-size:18px;
}
.bdash-progress-group{
  display:grid;
  gap:10px;
}
.bdash-progress-row{
  display:grid;
  gap:6px;
}
.bdash-progress-copy{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  color:var(--muted-color);
  font-size:12px;
}
.bdash-progress-track{
  width:100%;
  height:9px;
  border-radius:999px;
  background:rgba(148,163,184,.18);
  overflow:hidden;
}
.bdash-progress-fill{
  display:block;
  height:100%;
  border-radius:999px;
  background:var(--progress-accent, #2563eb);
}
.bdash-progress-fill.is-success{--progress-accent:#059669}
.bdash-progress-fill.is-warning{--progress-accent:#d97706}
.bdash-progress-fill.is-danger{--progress-accent:#dc2626}
.bdash-progress-fill.is-neutral{--progress-accent:#64748b}
.bdash-progress-fill.is-violet{--progress-accent:#7c3aed}
.bdash-trend-wrap{
  display:grid;
  gap:14px;
}
.bdash-trend-legend{
  display:flex;
  flex-wrap:wrap;
  gap:14px;
  color:var(--muted-color);
  font-size:12px;
}
.bdash-legend-dot{
  display:inline-flex;
  align-items:center;
  gap:6px;
}
.bdash-legend-dot::before{
  content:"";
  width:10px;
  height:10px;
  border-radius:999px;
  background:var(--legend-color, #2563eb);
}
.bdash-legend-dot.total::before{--legend-color:#2563eb}
.bdash-legend-dot.done::before{--legend-color:#059669}
.bdash-trend-bars{
  display:grid;
  grid-template-columns:repeat(7,minmax(0,1fr));
  gap:10px;
  align-items:end;
  min-height:220px;
}
.bdash-trend-col{min-width:0}
.bdash-trend-track{
  height:180px;
  display:flex;
  align-items:flex-end;
  justify-content:center;
  gap:6px;
}
.bdash-trend-bar{
  width:18px;
  min-height:6px;
  border-radius:999px 999px 0 0;
}
.bdash-trend-bar.total{background:rgba(37,99,235,.28)}
.bdash-trend-bar.done{background:#059669}
.bdash-trend-label{
  margin-top:10px;
  text-align:center;
  color:var(--muted-color);
  font-size:12px;
}
.bdash-trend-value{
  text-align:center;
  color:var(--ink);
  font-size:12px;
  font-weight:700;
  margin-top:4px;
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
  min-width:170px;
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
}
.bdash-drawer .offcanvas-body{
  display:grid;
  gap:14px;
}
.bdash-drawer.offcanvas-end{
  --bs-offcanvas-width:min(100vw,380px);
}
.bdash-filter-field{
  display:grid;
  gap:6px;
}
.bdash-filter-field label{
  color:var(--ink);
  font-size:13px;
  font-weight:700;
}
.bdash-filter-actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
@media (max-width: 1199.98px){
  .bdash-metric-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
}
@media (max-width: 767.98px){
  .bdash-actions{width:100%}
  .bdash-actions .btn{flex:1 1 calc(50% - 10px)}
  #dashboardActions{width:100%}
  #dashboardActions .btn{flex:1 1 calc(50% - 10px)}
  .bdash-card{padding:16px}
  .bdash-table th,.bdash-table td{padding:12px 10px;font-size:13px}
  .bdash-metric-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width: 575.98px){
  .bdash-head-copy p{font-size:14px}
  .bdash-table-hint{display:block}
  .bdash-actions .btn,
  #dashboardActions .btn{flex:1 1 100%}
  .bdash-filter-actions .btn{flex:1 1 0}
  .bdash-drawer.offcanvas-end{--bs-offcanvas-width:100vw}
  .bdash-metric-grid{grid-template-columns:1fr}
  .bdash-trend-bars{gap:6px}
  .bdash-trend-bar{width:12px}
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
    <div class="bdash-actions">
      <button type="button" class="btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#dashboardFilterDrawer">
        <i class="fa fa-sliders me-2"></i>Filters
      </button>
      <div id="dashboardActions"></div>
    </div>
  </div>

  <div id="dashboardLoader" class="bdash-loader">
    <div>
      <div class="spinner-border text-primary mb-3" role="status" aria-hidden="true"></div>
      <div>Loading dashboard...</div>
    </div>
  </div>

  <div id="dashboardContent" style="display:none;">
    <div id="dashboardAdminOverview" class="bdash-admin-shell" style="display:none;">
      <div class="bdash-metric-grid" id="dashboardAdminCards"></div>

      <div class="row g-3">
        <div class="col-12 col-xxl-8">
          <div class="bdash-card">
            <div class="bdash-card-head">
              <div>
                <h2>System Activity</h2>
                <p>Booking volume across the last 7 days with completed visits highlighted.</p>
              </div>
            </div>
            <div class="bdash-trend-wrap">
              <div class="bdash-trend-legend">
                <span class="bdash-legend-dot total">Total bookings</span>
                <span class="bdash-legend-dot done">Done bookings</span>
              </div>
              <div id="dashboardAdminTrend"></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-xxl-4">
          <div class="bdash-card">
            <div class="bdash-card-head">
              <div>
                <h2>System Snapshot</h2>
                <p>Today’s movement, booking mix, and overall status balance.</p>
              </div>
            </div>
            <div class="bdash-snapshot" id="dashboardAdminSnapshot"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-xl-8">
        <div class="bdash-card">
          <div class="bdash-card-head">
            <div>
              <h2 id="dashboardRecentTitle">Recent bookings</h2>
              <p id="dashboardRecentSubtitle">Latest booking activity from your account.</p>
            </div>
          </div>
          <div class="bdash-table-hint">
            <i class="fa fa-arrow-left-long me-1"></i>Swipe sideways on mobile to see all columns.
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
      </div>
      <div class="col-12 col-xl-4">
        <div class="bdash-card">
          <div class="bdash-card-head">
            <div>
              <h2 id="dashboardHighlightsTitle">Quick View</h2>
              <p id="dashboardHighlightsSubtitle">Important booking context at a glance.</p>
            </div>
          </div>
          <div class="bdash-list" id="dashboardHighlights"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="offcanvas offcanvas-end bdash-drawer" tabindex="-1" id="dashboardFilterDrawer" aria-labelledby="dashboardFilterDrawerLabel">
  <div class="offcanvas-header">
    <h5 id="dashboardFilterDrawerLabel" class="mb-0">Filter Recent Bookings</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="bdash-filter-field">
      <label for="dashboardFilterStatus">Status</label>
      <select id="dashboardFilterStatus" class="form-select">
        <option value="all">All status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="done">Done</option>
        <option value="rejected">Rejected</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    <div class="bdash-filter-field">
      <label for="dashboardFilterDoctor">Doctor</label>
      <select id="dashboardFilterDoctor" class="form-select">
        <option value="0">All doctors</option>
      </select>
    </div>
    <div class="bdash-filter-field">
      <label for="dashboardFilterDateFrom">Date From</label>
      <input type="date" id="dashboardFilterDateFrom" class="form-control">
    </div>
    <div class="bdash-filter-field">
      <label for="dashboardFilterDateTo">Date To</label>
      <input type="date" id="dashboardFilterDateTo" class="form-control">
    </div>
    <div class="bdash-filter-actions">
      <button type="button" class="btn btn-light" id="dashboardFilterResetBtn">Reset</button>
      <button type="button" class="btn btn-primary" id="dashboardFilterApplyBtn" data-bs-dismiss="offcanvas">Apply</button>
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
    adminOverview: document.getElementById('dashboardAdminOverview'),
    adminCards: document.getElementById('dashboardAdminCards'),
    adminTrend: document.getElementById('dashboardAdminTrend'),
    adminSnapshot: document.getElementById('dashboardAdminSnapshot'),
    recentTitle: document.getElementById('dashboardRecentTitle'),
    recentSubtitle: document.getElementById('dashboardRecentSubtitle'),
    recentBody: document.getElementById('dashboardRecentBody'),
    highlightsTitle: document.getElementById('dashboardHighlightsTitle'),
    highlightsSubtitle: document.getElementById('dashboardHighlightsSubtitle'),
    highlights: document.getElementById('dashboardHighlights'),
    filterStatus: document.getElementById('dashboardFilterStatus'),
    filterDoctor: document.getElementById('dashboardFilterDoctor'),
    filterDateFrom: document.getElementById('dashboardFilterDateFrom'),
    filterDateTo: document.getElementById('dashboardFilterDateTo'),
    filterReset: document.getElementById('dashboardFilterResetBtn'),
    filterApply: document.getElementById('dashboardFilterApplyBtn')
  };

  let currentRole = 'patient';
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

  function formatNumber(value) {
    return Number(value || 0).toLocaleString();
  }

  function statusBadge(status) {
    const key = String(status || 'pending').toLowerCase();
    const label = key.charAt(0).toUpperCase() + key.slice(1);
    return `<span class="bdash-pill ${esc(key)}">${esc(label)}</span>`;
  }

  function buildParams() {
    const params = new URLSearchParams();
    if (state.status && state.status !== 'all') params.set('status', state.status);
    if (state.doctor_id && state.doctor_id !== '0') params.set('doctor_id', state.doctor_id);
    if (state.date_from) params.set('date_from', state.date_from);
    if (state.date_to) params.set('date_to', state.date_to);
    return params.toString();
  }

  function renderDoctorOptions(options) {
    state.doctorOptions = Array.isArray(options) ? options : [];
    els.filterDoctor.innerHTML = '<option value="0">All doctors</option>' + state.doctorOptions.map((item) => `
      <option value="${esc(item.id)}">${esc(item.name)}</option>
    `).join('');
    els.filterDoctor.value = state.doctor_id;
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

  function renderAdminOverview(overview, counts) {
    if (currentRole !== 'admin' || !overview) {
      els.adminOverview.style.display = 'none';
      els.adminCards.innerHTML = '';
      els.adminTrend.innerHTML = '';
      els.adminSnapshot.innerHTML = '';
      return;
    }

    els.adminOverview.style.display = '';

    const toneClass = {
      primary: 'bdash-tone-primary',
      warning: 'bdash-tone-warning',
      success: 'bdash-tone-success',
      info: 'bdash-tone-info',
      violet: 'bdash-tone-violet',
      neutral: 'bdash-tone-neutral'
    };

    els.adminCards.innerHTML = (overview.cards || []).map((card) => `
      <article class="bdash-metric-card ${toneClass[card.tone] || 'bdash-tone-primary'}">
        <div class="bdash-metric-card-top">
          <div>
            <span>${esc(card.label || '')}</span>
            <strong>${formatNumber(card.value || 0)}</strong>
          </div>
          <i class="fa ${esc(card.icon || 'fa-chart-line')} bdash-metric-icon" aria-hidden="true"></i>
        </div>
        <p>${esc(card.meta || '')}</p>
      </article>
    `).join('');

    const trend = Array.isArray(overview.trend) ? overview.trend : [];
    const maxTotal = Math.max(1, ...trend.map((item) => Number(item.total || 0)));
    els.adminTrend.innerHTML = trend.length ? `
      <div class="bdash-trend-bars">
        ${trend.map((item) => {
          const totalHeight = Math.max(6, Math.round((Number(item.total || 0) / maxTotal) * 100));
          const doneHeight = Math.max(item.done ? 6 : 0, Math.round((Number(item.done || 0) / maxTotal) * 100));
          return `
            <div class="bdash-trend-col">
              <div class="bdash-trend-track">
                <span class="bdash-trend-bar total" style="height:${totalHeight}%"></span>
                <span class="bdash-trend-bar done" style="height:${doneHeight}%"></span>
              </div>
              <div class="bdash-trend-label">${esc(item.label || '')}</div>
              <div class="bdash-trend-value">${formatNumber(item.total || 0)}</div>
            </div>
          `;
        }).join('')}
      </div>
    ` : `<div class="bdash-empty">No booking activity available yet.</div>`;

    const distribution = overview.status_distribution || counts || {};
    const total = Math.max(1, Number(distribution.total || 0));
    const selfCount = Number(overview.booking_mix?.self || 0);
    const familyCount = Number(overview.booking_mix?.family || 0);
    const mixTotal = Math.max(1, selfCount + familyCount);
    const reviewAverage = Number(overview.reviews?.average_rating || 0).toFixed(1);

    els.adminSnapshot.innerHTML = `
      <div class="bdash-snapshot-block">
        <div class="bdash-snapshot-row">
          <span>New bookings today</span>
          <strong>${formatNumber(overview.today?.new_bookings || 0)}</strong>
        </div>
        <div class="bdash-snapshot-row">
          <span>Appointments today</span>
          <strong>${formatNumber(overview.today?.appointments || 0)}</strong>
        </div>
        <div class="bdash-snapshot-row">
          <span>Total doctor reviews</span>
          <strong>${formatNumber(overview.reviews?.total || 0)}</strong>
        </div>
        <div class="bdash-snapshot-row">
          <span>Average rating</span>
          <strong>${esc(reviewAverage)}/5</strong>
        </div>
      </div>
      <div class="bdash-snapshot-block">
        <div class="bdash-progress-row">
          <div class="bdash-progress-copy">
            <span>Self bookings</span>
            <strong>${formatNumber(selfCount)}</strong>
          </div>
          <div class="bdash-progress-track"><span class="bdash-progress-fill" style="width:${Math.round((selfCount / mixTotal) * 100)}%"></span></div>
        </div>
        <div class="bdash-progress-row">
          <div class="bdash-progress-copy">
            <span>Family bookings</span>
            <strong>${formatNumber(familyCount)}</strong>
          </div>
          <div class="bdash-progress-track"><span class="bdash-progress-fill is-violet" style="width:${Math.round((familyCount / mixTotal) * 100)}%"></span></div>
        </div>
      </div>
      <div class="bdash-progress-group">
        ${[
          ['Pending', distribution.pending || 0, 'is-warning'],
          ['Approved', distribution.approved || 0, ''],
          ['Done', distribution.done || 0, 'is-success'],
          ['Rejected', distribution.rejected || 0, 'is-danger'],
          ['Cancelled', distribution.cancelled || 0, 'is-neutral']
        ].map(([label, value, klass]) => `
          <div class="bdash-progress-row">
            <div class="bdash-progress-copy">
              <span>${esc(label)}</span>
              <strong>${formatNumber(value)}</strong>
            </div>
            <div class="bdash-progress-track"><span class="bdash-progress-fill ${klass}" style="width:${Math.round((Number(value || 0) / total) * 100)}%"></span></div>
          </div>
        `).join('')}
      </div>
    `;
  }

  function renderHighlights(role, counts, overview) {
    if (role === 'admin') {
      els.highlightsTitle.textContent = 'Top Doctors';
      els.highlightsSubtitle.textContent = 'The busiest doctors in the current booking dataset.';
      const doctors = Array.isArray(overview?.top_doctors) ? overview.top_doctors : [];
      els.highlights.innerHTML = doctors.length ? doctors.map((doctor, index) => `
        <div class="bdash-list-item">
          <div class="bdash-top-line">
            <span>${esc((index + 1) + '. ' + (doctor.name || 'Doctor'))}</span>
            <span>${formatNumber(doctor.total_bookings || 0)} bookings</span>
          </div>
          <div class="bdash-top-meta">
            <span>Done: ${formatNumber(doctor.done_bookings || 0)}</span>
            <span>Pending: ${formatNumber(doctor.pending_bookings || 0)}</span>
            <span>Treated: ${formatNumber(doctor.total_patients_treated || 0)}</span>
            <span>Rating: ${esc(Number(doctor.average_rating || 0).toFixed(1))}/5</span>
          </div>
        </div>
      `).join('') : `<div class="bdash-empty">No doctor activity available yet.</div>`;
      return;
    }

    els.highlightsTitle.textContent = 'Quick View';
    els.highlightsSubtitle.textContent = 'Important booking context at a glance.';
    const items = [
      `Bookings created from this account: <strong>${esc(counts.total || 0)}</strong>.`,
      `Reviews can be submitted after admin marks a booking as <strong>Done</strong>.`,
      `Use the filter drawer to focus on one doctor, one date range, or one booking status.`
    ];

    els.highlights.innerHTML = items.map((text) => `<div class="bdash-list-item"><span>${text}</span></div>`).join('');
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
        document.getElementById('dashboardClinicDetailsBtn')?.addEventListener('click', function () {
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
      title: 'Review Dr. ' + (item.doctor_name || 'Doctor'),
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
          <td colspan="5"><div class="bdash-empty">No bookings found for the selected filters.</div></td>
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
            ${currentRole === 'patient' && item.can_review ? `<button type="button" class="btn btn-success btn-sm js-dashboard-review" data-id="${esc(item.id)}"><i class="fa fa-star me-1"></i>Review</button>` : ''}
            ${currentRole === 'admin' ? `<a href="/bookings/manage" class="btn btn-primary btn-sm"><i class="fa fa-arrow-up-right-from-square me-1"></i>Manage</a>` : ''}
          </div>
        </td>
      </tr>
    `).join('');

    bindRecentActions();
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

  async function loadDashboard() {
    try {
      const qs = buildParams();
      const res = await fetch('/api/bookings/dashboard' + (qs ? '?' + qs : ''), { headers: authHeaders() });
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
      const adminOverview = data.admin_overview || null;

      els.title.textContent = currentRole === 'admin' ? 'Admin Booking Dashboard' : 'My Booking Dashboard';
      els.subtitle.textContent = currentRole === 'admin'
        ? 'Monitor system-wide bookings, review operational activity, and manage the appointment pipeline.'
        : 'Track appointment requests, filter recent bookings, and review doctors after visits are marked done.';
      els.recentTitle.textContent = currentRole === 'admin' ? 'Latest booking requests' : 'Recent bookings';
      els.recentSubtitle.textContent = currentRole === 'admin'
        ? 'Newest booking activity across the platform.'
        : 'Newest booking activity from your account.';

      renderActions(currentRole, data.links || {});
      renderDoctorOptions(data.doctor_options || []);
      renderAdminOverview(adminOverview, counts);
      renderRecent(data.recent || []);
      renderHighlights(currentRole, counts, adminOverview);
      syncDrawerFields();

      els.loader.style.display = 'none';
      els.content.style.display = '';
    } catch (error) {
      els.loader.innerHTML = `<div class="bdash-empty">${esc(error.message || 'Unable to load dashboard.')}</div>`;
    }
  }

  els.filterApply.addEventListener('click', function () {
    applyDrawerFields();
    loadDashboard();
  });

  els.filterReset.addEventListener('click', function () {
    state.status = 'all';
    state.doctor_id = '0';
    state.date_from = '';
    state.date_to = '';
    syncDrawerFields();
    loadDashboard();
  });

  loadDashboard();
});
</script>
@endsection
