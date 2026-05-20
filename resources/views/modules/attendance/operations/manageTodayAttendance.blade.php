@extends('pages.layout.structure')

@section('title', 'Today Attendance')

@push('styles')
<style>
.live-wrap{display:grid;gap:20px}
.live-hero,
.live-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:24px;
  box-shadow:var(--shadow-1);
}
.live-hero{
  padding:24px;
  background:linear-gradient(145deg, rgba(15,118,110,.08), rgba(59,130,246,.10));
}
.live-hero h1{margin:0 0 8px;font-size:30px}
.live-hero p{margin:0;color:var(--muted-color);line-height:1.75;max-width:78ch}
.live-stats{
  display:grid;
  grid-template-columns:repeat(6, minmax(0, 1fr));
  gap:14px;
}
.live-stat{
  padding:18px;
  border-radius:20px;
  border:1px solid var(--line-strong);
  background:var(--surface);
  box-shadow:var(--shadow-1);
}
.live-stat span{
  display:flex;
  align-items:center;
  gap:8px;
  margin-bottom:10px;
  color:var(--primary-color);
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.07em;
}
.live-stat strong{
  display:block;
  font-size:28px;
  color:var(--ink);
}
.live-stat small{
  display:block;
  margin-top:6px;
  color:var(--muted-color);
}
.live-grid{
  display:grid;
  grid-template-columns:minmax(0, 1.55fr) minmax(320px, .9fr);
  gap:20px;
}
.live-filter-row{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  align-items:end;
}
.live-filter-row > div{min-width:160px}
.live-filter-row .search-box{flex:1 1 240px}
.live-table-wrap{overflow:auto}
.live-table thead th{
  background:var(--surface-3);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  white-space:nowrap;
}
.live-table tbody td{
  border-top:1px solid var(--line-soft);
  vertical-align:top;
}
.live-table tbody tr:hover{background:var(--surface-2)}
.live-table tbody tr.is-active{background:rgba(22,163,74,.04)}
.live-empty{
  padding:34px 16px;
  text-align:center;
  color:var(--muted-color);
}
.live-pill{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:5px 10px;
  border-radius:999px;
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.04em;
}
.live-pill.ok{background:rgba(22,163,74,.12);color:#15803d}
.live-pill.warn{background:rgba(245,158,11,.14);color:#b45309}
.live-pill.danger{background:rgba(220,38,38,.12);color:#b91c1c}
.live-pill.info{background:rgba(59,130,246,.12);color:#1d4ed8}
.live-user strong{display:block;font-size:14px}
.live-user small{display:block;color:var(--muted-color);margin-top:3px}
.live-mini{
  display:grid;
  gap:2px;
}
.live-mini strong{font-size:13px}
.live-mini small{color:var(--muted-color)}
.live-map-frame{
  width:100%;
  height:300px;
  border:0;
  border-radius:18px;
  background:var(--surface-2);
}
.live-map-empty{
  min-height:300px;
  display:grid;
  place-items:center;
  padding:18px;
  text-align:center;
  color:var(--muted-color);
  border-radius:18px;
  border:1px dashed var(--line-medium);
  background:var(--surface-2);
}
.live-detail-grid{
  display:grid;
  gap:12px;
  margin-top:16px;
  grid-template-columns:repeat(2, minmax(0, 1fr));
}
.live-detail-item{
  border:1px solid var(--line-soft);
  border-radius:16px;
  padding:14px;
  background:var(--surface-2);
}
.live-detail-item span{
  display:block;
  color:var(--muted-color);
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  margin-bottom:5px;
}
.live-detail-item strong{
  color:var(--ink);
  line-height:1.65;
}
.live-pager{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  justify-content:flex-end;
  padding:14px 16px 16px;
  border-top:1px solid var(--line-soft);
}
.live-pager button{
  min-width:38px;
  border-radius:12px;
}
.live-action-row{
  display:flex;
  justify-content:flex-end;
  gap:8px;
  flex-wrap:wrap;
}
@media (max-width: 1399.98px){
  .live-stats{grid-template-columns:repeat(3, minmax(0, 1fr))}
}
@media (max-width: 1099.98px){
  .live-grid{grid-template-columns:1fr}
}
@media (max-width: 767.98px){
  .live-stats{grid-template-columns:repeat(2, minmax(0, 1fr))}
  .live-detail-grid{grid-template-columns:1fr}
}
</style>
@endpush

@section('content')
<div class="live-wrap">
  <section class="live-hero">
    <span class="att-inline-badge"><i class="fa-solid fa-satellite-dish"></i>Live Attendance Monitor</span>
    <h1>Today Attendance</h1>
    <p>Track who checked in, who is late, who is still active, where they punched from, and which records need HR attention. The map panel follows the selected employee row.</p>
  </section>

  <section class="live-stats" id="liveStats">
    <div class="live-stat"><span><i class="fa-solid fa-users"></i>Total Employees</span><strong>—</strong><small>loading</small></div>
    <div class="live-stat"><span><i class="fa-solid fa-user-check"></i>Marked</span><strong>—</strong><small>loading</small></div>
    <div class="live-stat"><span><i class="fa-solid fa-bolt"></i>Active Sessions</span><strong>—</strong><small>loading</small></div>
    <div class="live-stat"><span><i class="fa-solid fa-clock"></i>Late</span><strong>—</strong><small>loading</small></div>
    <div class="live-stat"><span><i class="fa-solid fa-user-clock"></i>Pending Approval</span><strong>—</strong><small>loading</small></div>
    <div class="live-stat"><span><i class="fa-solid fa-map-location-dot"></i>Location Exceptions</span><strong>—</strong><small>loading</small></div>
  </section>

  <section class="live-grid">
    <div class="live-card">
      <div style="padding:18px 18px 14px;border-bottom:1px solid var(--line-soft);">
        <div class="live-filter-row">
          <div>
            <label class="small text-muted d-block mb-1">Date</label>
            <input type="date" class="form-control" id="fltDate">
          </div>
          <div>
            <label class="small text-muted d-block mb-1">Status</label>
            <select class="form-select" id="fltStatus">
              <option value="">All</option>
              <option value="present">Present</option>
              <option value="late">Late</option>
              <option value="pending_approval">Pending Approval</option>
              <option value="not_marked">Not Marked</option>
            </select>
          </div>
          <div>
            <label class="small text-muted d-block mb-1">Branch</label>
            <select class="form-select" id="fltBranch">
              <option value="">All branches</option>
            </select>
          </div>
          <div class="search-box">
            <label class="small text-muted d-block mb-1">Search</label>
            <input type="search" class="form-control" id="fltQ" placeholder="Employee, code, email, phone">
          </div>
          <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-success" id="liveExportBtn"><i class="fa-solid fa-file-excel me-1"></i>Export Excel</button>
            <button type="button" class="btn btn-light" id="liveResetBtn"><i class="fa-solid fa-rotate-left me-1"></i>Reset</button>
            <button type="button" class="btn btn-primary" id="liveRefreshBtn"><i class="fa-solid fa-arrows-rotate me-1"></i>Refresh</button>
          </div>
        </div>
      </div>
      <div class="live-table-wrap">
        <table class="table live-table align-middle mb-0">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Shift</th>
              <th>Status</th>
              <th>Check In</th>
              <th>Check Out</th>
              <th>Late</th>
              <th>Working</th>
              <th>Approval</th>
              <th>Location</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="liveTbody">
            <tr><td colspan="10" class="live-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading today attendance…</td></tr>
          </tbody>
        </table>
      </div>
      <div class="d-flex align-items-center justify-content-between gap-3 px-3 py-3 border-top" style="border-color:var(--line-soft)!important;">
        <div class="small text-muted" id="liveInfo">—</div>
        <div class="live-pager p-0 border-0" id="livePager"></div>
      </div>
    </div>

    <div class="live-card" style="padding:18px;">
      <div>
        <span class="att-inline-badge"><i class="fa-solid fa-location-arrow"></i>Selected Employee</span>
        <h2 class="mt-3 mb-1" style="font-size:22px;" id="selName">Select a row</h2>
        <p class="mb-0 text-muted" id="selLead">Pick any employee row to inspect current attendance details and map location.</p>
      </div>
      <div class="mt-3" id="mapContainer">
        <div class="live-map-empty">Location preview appears here once an employee row has coordinates.</div>
      </div>
      <div class="live-detail-grid" id="detailGrid">
        <div class="live-detail-item">
          <span>Current State</span>
          <strong>Waiting for row selection.</strong>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) { window.location.replace('/'); return; }

  const state = {
    page: 1,
    per_page: 20,
    selected: null,
    filters: {
      date: new Date().toISOString().slice(0, 10),
      status: '',
      branch_id: '',
      q: '',
    },
    companyTz: localStorage.getItem('companyTz') || Intl.DateTimeFormat().resolvedOptions().timeZone,
  };

  const api = (path) => fetch(path, {
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json',
    },
  });

  async function loadBranchOptions() {
    const select = document.getElementById('fltBranch');
    if (!select) return;
    const res = await api('/api/attendance/admin/branches?per_page=200');
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Could not load branches.');
    const rows = Array.isArray(json.data) ? json.data : [];
    select.innerHTML = '<option value="">All branches</option>' + rows.map((row) => {
      const value = row.id ?? row.uuid ?? '';
      const label = row.code ? `${row.name} (${row.code})` : (row.name || value);
      return `<option value="${esc(String(value))}">${esc(String(label))}</option>`;
    }).join('');
    select.value = state.filters.branch_id || '';
  }

  function esc(v) {
    return String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function pad2(v) { return String(v).padStart(2, '0'); }
  function monthLabel(y, m, d) {
    const dt = new Date(Date.UTC(Number(y), Number(m) - 1, Number(d), 12, 0, 0));
    return dt.toLocaleDateString([], { year:'numeric', month:'short', day:'2-digit', timeZone:'UTC' });
  }
  function formatClockParts(hh, mm) {
    const hour24 = Number(hh || 0);
    const hour12 = hour24 % 12 || 12;
    return `${pad2(hour12)}:${pad2(mm || 0)} ${hour24 >= 12 ? 'PM' : 'AM'}`;
  }
  function parseServerDate(value) {
    if (!value) return null;
    const raw = String(value).trim();
    if (!raw) return null;
    if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return { raw, kind: 'sql_date' };
    if (/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}(:\d{2})?$/.test(raw)) return { raw, kind: 'sql_datetime' };
    const parsed = new Date(raw);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
  }
  function fmtDateTime(value) {
    const parsed = parseServerDate(value);
    if (!parsed) return '—';
    if (parsed.kind === 'sql_datetime') {
      const m = parsed.raw.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);
      return m ? `${monthLabel(m[1], m[2], m[3])}, ${formatClockParts(m[4], m[5])}` : parsed.raw;
    }
    if (parsed.kind === 'sql_date') return monthLabel(...parsed.raw.split('-'));
    return parsed.toLocaleString([], { year:'numeric', month:'short', day:'2-digit', hour:'2-digit', minute:'2-digit', hour12:true, timeZone: state.companyTz });
  }
  function fmtTime(value) {
    const parsed = parseServerDate(value);
    if (!parsed) return '—';
    if (parsed.kind === 'sql_datetime') {
      const m = parsed.raw.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);
      return m ? formatClockParts(m[4], m[5]) : parsed.raw;
    }
    return parsed.toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', hour12:true, timeZone: state.companyTz });
  }
  function minutesLabel(min) {
    if (min === null || min === undefined || min === '') return '—';
    const minutes = Number(min);
    if (!Number.isFinite(minutes) || minutes <= 0) return '0m';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (!hrs) return `${mins}m`;
    if (!mins) return `${hrs}h`;
    return `${hrs}h ${mins}m`;
  }
  function badge(value, fallback = 'info') {
    const raw = String(value || '').toLowerCase();
    const label = String(value || 'Unknown').replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
    let tone = fallback;
    if (['present', 'approved', 'synced', 'active'].includes(raw)) tone = 'ok';
    else if (['late', 'pending_approval', 'processing', 'not_marked'].includes(raw)) tone = 'warn';
    else if (['rejected', 'absent', 'sync_failed'].includes(raw)) tone = 'danger';
    return `<span class="live-pill ${tone}">${esc(label)}</span>`;
  }
  function mapCoords(row) {
    const lat = row.latest_track_latitude ?? row.latest_latitude ?? null;
    const lng = row.latest_track_longitude ?? row.latest_longitude ?? null;
    if (lat === null || lng === null) return null;
    return { lat: Number(lat), lng: Number(lng) };
  }
  function locationLabel(row) {
    const coords = mapCoords(row);
    const text = row.latest_location_text || '';
    if (text && coords) return `${text} (${coords.lat.toFixed(5)}, ${coords.lng.toFixed(5)})`;
    if (text) return text;
    if (coords) return `${coords.lat.toFixed(5)}, ${coords.lng.toFixed(5)}`;
    return 'No location captured';
  }
  function infoLabel(row) {
    const bits = [];
    if (row.attendance_mode) bits.push(String(row.attendance_mode).toUpperCase());
    if (row.work_mode) bits.push(String(row.work_mode).toUpperCase());
    if (row.latest_punch_type) bits.push(String(row.latest_punch_type).replace('_', ' '));
    return bits.join(' · ') || '—';
  }

  function buildQuery() {
    const params = new URLSearchParams({
      page: String(state.page),
      per_page: String(state.per_page),
      date: state.filters.date,
    });
    if (state.filters.status) params.set('status', state.filters.status);
    if (state.filters.branch_id) params.set('branch_id', state.filters.branch_id);
    if (state.filters.q) params.set('q', state.filters.q);
    return params.toString();
  }

  async function exportTodayAttendance() {
    const res = await api(`/api/attendance/hr/live-attendance/export?${buildQuery()}`);
    if (!res.ok) {
      let message = 'Could not export today attendance.';
      try {
        const json = await res.json();
        message = json.message || message;
      } catch (_) {}
      throw new Error(message);
    }
    const blob = await res.blob();
    const disposition = res.headers.get('Content-Disposition') || '';
    const filenameMatch = disposition.match(/filename="?([^"]+)"?/i);
    const filename = filenameMatch?.[1] || `today_attendance_${state.filters.date}.xls`;
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1500);
  }

  function renderStats(summary) {
    const cards = [
      { icon:'fa-users', title:'Total Employees', value:summary.total_employees, note:`${summary.not_marked} not marked` },
      { icon:'fa-user-check', title:'Marked', value:summary.marked, note:`${summary.present} present` },
      { icon:'fa-bolt', title:'Active Sessions', value:summary.active_sessions, note:'currently checked in' },
      { icon:'fa-clock', title:'Late', value:summary.late, note:`avg ${summary.average_late_minutes} min delay` },
      { icon:'fa-user-clock', title:'Pending Approval', value:summary.pending_approval, note:`${summary.pending_sync} sync/policy flags` },
      { icon:'fa-map-location-dot', title:'Location Exceptions', value:summary.location_exceptions, note:`${summary.offline_records} offline records` },
    ];

    document.getElementById('liveStats').innerHTML = cards.map((card) => `
      <div class="live-stat">
        <span><i class="fa-solid ${card.icon}"></i>${esc(card.title)}</span>
        <strong>${esc(card.value)}</strong>
        <small>${esc(card.note)}</small>
      </div>
    `).join('');
  }

  function renderRows(rows) {
    const tbody = document.getElementById('liveTbody');
    if (!rows.length) {
      tbody.innerHTML = `<tr><td colspan="10" class="live-empty"><i class="fa-regular fa-folder-open me-2"></i>No attendance rows found for the selected filters.</td></tr>`;
      return;
    }

    tbody.innerHTML = rows.map((row, index) => {
      const coords = mapCoords(row);
      const mapHref = coords ? `https://maps.google.com/?q=${coords.lat},${coords.lng}` : '';
      const active = !!row.active_session;
      const selected = state.selected && Number(state.selected.user_id) === Number(row.user_id);
      return `
        <tr class="${active ? 'is-active' : ''} ${selected ? 'table-active' : ''}" data-row-index="${index}">
          <td>
            <div class="live-user">
              <strong>${esc(row.name)}</strong>
              <small>${esc(row.employee_code || '—')} · ${esc(row.department_name || 'No Dept')}</small>
            </div>
          </td>
          <td>
            <div class="live-mini">
              <strong>${esc(row.shift_name || '—')}</strong>
              <small>${esc(fmtTime(row.shift_start_time))} - ${esc(fmtTime(row.shift_end_time))}</small>
            </div>
          </td>
          <td>${active ? badge('active') : badge(row.attendance_status || row.live_status || 'not_marked')}</td>
          <td>${esc(fmtTime(row.check_in_time))}</td>
          <td>${esc(fmtTime(row.check_out_time))}</td>
          <td>${row.late_minutes ? `<span class="text-warning fw-bold">${esc(minutesLabel(row.late_minutes))}</span>` : '<span class="text-muted">On time</span>'}</td>
          <td>${esc(minutesLabel(row.total_working_minutes))}</td>
          <td>${badge(row.approval_status || 'approved')}</td>
          <td>
            <div class="live-mini">
              <strong>${esc(locationLabel(row))}</strong>
              <small>${esc(infoLabel(row))}</small>
            </div>
          </td>
          <td class="text-end">
            <div class="live-action-row">
              ${coords ? `<a class="btn btn-sm btn-outline-primary js-map-link" href="${mapHref}" target="_blank" rel="noopener">Map</a>` : ''}
              ${row.attendance_id ? `<button type="button" class="btn btn-sm btn-primary js-view-detail" data-attendance-id="${row.attendance_id}">View</button>` : '<span class="text-muted">—</span>'}
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  function proofImage(url, label) {
    if (!url) {
      return `<div class="live-detail-item"><span>${esc(label)}</span><strong>No selfie captured.</strong></div>`;
    }
    return `
      <div class="live-detail-item">
        <span>${esc(label)}</span>
        <strong><a href="${esc(url)}" target="_blank" rel="noopener">Open full image</a></strong>
        <img src="${esc(url)}" alt="${esc(label)}" style="width:100%;max-height:320px;object-fit:contain;border-radius:14px;border:1px solid var(--line-soft);margin-top:10px;background:#fff;">
      </div>
    `;
  }

  function detailTrackRows(rows) {
    if (!rows.length) {
      return '<tr><td colspan="4" class="text-center text-muted py-3">No movement points recorded.</td></tr>';
    }
    return rows.slice().reverse().map((row) => `
      <tr>
        <td>${esc(fmtDateTime(row.recorded_at))}</td>
        <td>${esc(row.location_label || '—')}</td>
        <td>${esc(row.network_type || '—')}</td>
        <td>${badge(row.sync_status || 'synced')}</td>
      </tr>
    `).join('');
  }

  function detailLogRows(rows) {
    if (!rows.length) {
      return '<tr><td colspan="5" class="text-center text-muted py-3">No punch logs recorded.</td></tr>';
    }
    return rows.map((row) => `
      <tr>
        <td>${badge(row.punch_type || 'punch')}</td>
        <td>${esc(fmtDateTime(row.punch_time))}</td>
        <td>${esc(row.location_label || '—')}</td>
        <td>${esc(row.request_ip || '—')}</td>
        <td>${row.selfie_url ? `<a href="${esc(row.selfie_url)}" target="_blank" rel="noopener">Selfie</a>` : '<span class="text-muted">—</span>'}</td>
      </tr>
    `).join('');
  }

  function buildActivitySection(rows) {
    if (!rows || !rows.length) {
      return `
        <div class="mt-3">
          <h3 style="font-size:15px;margin-bottom:10px;">Employee Activity Timeline</h3>
          <p class="text-muted small">No employee activity logs recorded for this session.</p>
        </div>`;
    }

    const activityRows = rows.map((row) => {
      const data = row.new_values || {};
      const details = [
        data.source ? `Source: ${data.source}` : null,
        data.network_type ? `Network: ${data.network_type}` : null,
        data.request_ip ? `IP: ${data.request_ip}` : null,
        data.location ? `Location: ${data.location}` : null,
      ].filter(Boolean).join(' · ');
      return `
        <tr>
          <td>${esc(fmtDateTime(row.created_at))}</td>
          <td>${badge(row.activity || 'activity')}</td>
          <td>${esc(row.log_note || '—')}</td>
          <td>${esc(String(data.severity || data.category || 'info').replace(/_/g, ' '))}</td>
          <td>${details ? `<small>${esc(details)}</small>` : '<span class="text-muted">—</span>'}</td>
        </tr>
      `;
    }).join('');

    return `
      <div class="mt-3">
        <h3 style="font-size:15px;margin-bottom:10px;">Employee Activity Timeline</h3>
        <div style="overflow:auto;border:1px solid var(--line-soft);border-radius:16px;">
          <table class="table mb-0">
            <thead><tr><th>Occurred</th><th>Activity</th><th>Note</th><th>Level</th><th>Details</th></tr></thead>
            <tbody>${activityRows}</tbody>
          </table>
        </div>
      </div>`;
  }

  async function openAttendanceDetail(attendanceId) {
    const res = await api(`/api/attendance/hr/attendance/${encodeURIComponent(attendanceId)}/detail`);
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Could not load attendance detail.');

    const detail = json.data || {};
    const attendance = detail.attendance || {};
    const journey = detail.journey || {};
    const proofs = detail.proofs || {};
    const logs = Array.isArray(detail.logs) ? detail.logs : [];
    const tracks = Array.isArray(detail.tracks) ? detail.tracks : [];
    const activityLogs = Array.isArray(detail.activity_logs) ? detail.activity_logs : [];
    const current = journey.current_location || {};
    const currentMap = current.latitude !== null && current.latitude !== undefined && current.longitude !== null && current.longitude !== undefined
      ? `https://maps.google.com/?q=${current.latitude},${current.longitude}`
      : null;

    await Swal.fire({
      title: 'Attendance Detail',
      width: 1160,
      confirmButtonText: 'Close',
      html: `
        <div class="text-start">
          <div class="live-detail-grid" style="margin-top:0;">
            <div class="live-detail-item"><span>Employee</span><strong>${esc(attendance.name || '—')} · ${esc(attendance.employee_code || '—')}</strong></div>
            <div class="live-detail-item"><span>Attendance Day</span><strong>${esc(attendance.attendance_date || '—')} · ${String(attendance.status || 'present').replace(/_/g, ' ')}</strong></div>
            <div class="live-detail-item"><span>Current Location</span><strong>${esc(current.label || '—')}${currentMap ? `<br><a href="${esc(currentMap)}" target="_blank" rel="noopener">Open map</a>` : ''}</strong></div>
            <div class="live-detail-item"><span>Analytics</span><strong>Late ${esc(minutesLabel(attendance.late_minutes))} · Working ${esc(minutesLabel(attendance.total_working_minutes))} · OT ${esc(minutesLabel(attendance.overtime_minutes))}</strong></div>
            <div class="live-detail-item"><span>Tracking Summary</span><strong>${esc(String(journey.track_points || 0))} points · Last seen ${esc(fmtDateTime(journey.last_seen_at) || '—')}</strong></div>
            <div class="live-detail-item"><span>Network & Approval</span><strong>${attendance.within_wifi_ip === 1 ? 'Allowed IP matched' : attendance.within_wifi_ip === 0 ? 'IP mismatch' : 'IP not checked'} · ${String(attendance.approval_status || 'approved').replace(/_/g, ' ')}</strong></div>
          </div>

          <div class="live-detail-grid mt-3">
            ${proofImage(proofs.check_in_selfie?.url || null, 'Check In Selfie')}
            ${proofImage(proofs.check_out_selfie?.url || null, 'Check Out Selfie')}
          </div>

          <div class="mt-3">
            <h3 style="font-size:15px;margin-bottom:10px;">Punch Timeline</h3>
            <div style="overflow:auto;border:1px solid var(--line-soft);border-radius:16px;">
              <table class="table mb-0">
                <thead><tr><th>Punch</th><th>Time</th><th>Location</th><th>Request IP</th><th>Proof</th></tr></thead>
                <tbody>${detailLogRows(logs)}</tbody>
              </table>
            </div>
          </div>

          <div class="mt-3">
            <h3 style="font-size:15px;margin-bottom:10px;">Movement Tracking</h3>
            <div style="overflow:auto;border:1px solid var(--line-soft);border-radius:16px;">
              <table class="table mb-0">
                <thead><tr><th>Recorded At</th><th>Location</th><th>Network</th><th>Sync</th></tr></thead>
                <tbody>${detailTrackRows(tracks)}</tbody>
              </table>
            </div>
          </div>

          ${buildActivitySection(activityLogs)}
        </div>
      `,
    });
  }

  function renderPager(pg) {
    const pager = document.getElementById('livePager');
    if (!pg.last_page || pg.last_page <= 1) { pager.innerHTML = ''; return; }
    const page = Number(pg.page || 1);
    const last = Number(pg.last_page || 1);
    const items = [];
    items.push(`<button class="btn btn-sm btn-light ${page <= 1 ? 'disabled' : ''}" data-page="${page - 1}">Prev</button>`);
    for (let i = 1; i <= last; i += 1) {
      if (i === 1 || i === last || Math.abs(i - page) <= 1) {
        items.push(`<button class="btn btn-sm ${i === page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>`);
      } else if (Math.abs(i - page) === 2) {
        items.push('<button class="btn btn-sm btn-light disabled">…</button>');
      }
    }
    items.push(`<button class="btn btn-sm btn-light ${page >= last ? 'disabled' : ''}" data-page="${page + 1}">Next</button>`);
    pager.innerHTML = items.join('');
  }

  function renderSelected(row) {
    state.selected = row;
    const coords = mapCoords(row);
    document.getElementById('selName').textContent = row.name || 'Selected Employee';
    document.getElementById('selLead').textContent = `${row.employee_code || '—'} · ${row.department_name || 'No Dept'} · ${row.branch_name || 'No Branch'}`;

    const mapContainer = document.getElementById('mapContainer');
    if (coords) {
      mapContainer.innerHTML = `<iframe class="live-map-frame" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=${coords.lat},${coords.lng}&z=16&output=embed"></iframe>`;
    } else {
      mapContainer.innerHTML = `<div class="live-map-empty">No coordinates were captured yet for this employee on the selected day.</div>`;
    }

    const detailGrid = document.getElementById('detailGrid');
    const lastSeen = row.latest_track_time || row.latest_punch_time || null;
    detailGrid.innerHTML = [
      ['Current State', row.active_session ? 'Employee is still checked in.' : 'No active session right now.'],
      ['Attendance Analytics', `Late: ${minutesLabel(row.late_minutes)} · Working: ${minutesLabel(row.total_working_minutes)} · Overtime: ${minutesLabel(row.overtime_minutes)}`],
      ['Punch Summary', `${fmtTime(row.check_in_time)} check in · ${fmtTime(row.check_out_time)} check out · ${row.latest_punch_type ? row.latest_punch_type.replace('_', ' ') : 'no recent punch type'}`],
      ['Location Snapshot', `${locationLabel(row)}${lastSeen ? ` · seen ${fmtDateTime(lastSeen)}` : ''}`],
      ['Network & Policy', `${row.latest_internet_status || '—'} internet · ${row.latest_network_type || row.latest_track_network_type || '—'} network · ${row.within_wifi_ip === 1 ? 'approved Wi-Fi/IP' : row.within_wifi_ip === 0 ? 'Wi-Fi/IP mismatch' : 'Wi-Fi not checked'}`],
      ['Approval & Exception', `${String(row.approval_status || 'approved').replace(/_/g, ' ')}${row.latest_exception_reason ? ` · ${row.latest_exception_reason}` : ''}`],
    ].map(([label, value]) => `
      <div class="live-detail-item">
        <span>${esc(label)}</span>
        <strong>${esc(value)}</strong>
      </div>
    `).join('');
  }

  async function loadStats() {
    const res = await api(`/api/attendance/hr/dashboard?date=${encodeURIComponent(state.filters.date)}`);
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Could not load attendance summary.');
    renderStats(json.data?.summary || {});
  }

  async function loadRows() {
    const tbody = document.getElementById('liveTbody');
    tbody.innerHTML = `<tr><td colspan="10" class="live-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading today attendance…</td></tr>`;
    const res = await api(`/api/attendance/hr/live-attendance?${buildQuery()}`);
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Could not load live attendance.');
    const rows = json.data || [];
    renderRows(rows);
    renderPager(json.pagination || {});
    document.getElementById('liveInfo').textContent = `Showing ${rows.length} of ${json.pagination?.total ?? 0} records for ${state.filters.date}.`;
    if (rows.length && !state.selected) renderSelected(rows[0]);
    if (rows.length && state.selected) {
      const next = rows.find((row) => Number(row.user_id) === Number(state.selected.user_id));
      if (next) renderSelected(next);
    }
    window.__liveRows = rows;
  }

  async function refreshAll() {
    try {
      await Promise.all([loadStats(), loadRows()]);
    } catch (error) {
      document.getElementById('liveTbody').innerHTML = `<tr><td colspan="10" class="live-empty text-danger">${esc(error.message)}</td></tr>`;
    }
  }

  document.getElementById('fltDate').value = state.filters.date;
  document.getElementById('fltDate').addEventListener('change', (event) => {
    state.page = 1;
    state.filters.date = event.target.value || new Date().toISOString().slice(0, 10);
    state.selected = null;
    refreshAll();
  });
  document.getElementById('fltStatus').addEventListener('change', (event) => {
    state.page = 1;
    state.filters.status = event.target.value;
    state.selected = null;
    refreshAll();
  });
  document.getElementById('fltBranch').addEventListener('change', (event) => {
    state.page = 1;
    state.filters.branch_id = event.target.value;
    state.selected = null;
    refreshAll();
  });
  document.getElementById('fltQ').addEventListener('input', (event) => {
    state.page = 1;
    state.filters.q = event.target.value.trim();
    refreshAll();
  });
  document.getElementById('liveRefreshBtn').addEventListener('click', refreshAll);
  document.getElementById('liveExportBtn').addEventListener('click', () => {
    exportTodayAttendance().catch((error) => {
      Swal.fire('Export failed', error.message, 'error');
    });
  });
  document.getElementById('liveResetBtn').addEventListener('click', () => {
    state.page = 1;
    state.selected = null;
    state.filters = { date: new Date().toISOString().slice(0, 10), status: '', branch_id: '', q: '' };
    document.getElementById('fltDate').value = state.filters.date;
    document.getElementById('fltStatus').value = '';
    document.getElementById('fltBranch').value = '';
    document.getElementById('fltQ').value = '';
    refreshAll();
  });
  document.getElementById('livePager').addEventListener('click', (event) => {
    const button = event.target.closest('[data-page]');
    if (!button || button.classList.contains('disabled')) return;
    state.page = Number(button.dataset.page || 1);
    loadRows();
  });
  document.getElementById('liveTbody').addEventListener('click', (event) => {
    const detailBtn = event.target.closest('.js-view-detail');
    if (detailBtn) {
      event.stopPropagation();
      openAttendanceDetail(detailBtn.dataset.attendanceId).catch((error) => {
        Swal.fire('Could not open detail', error.message, 'error');
      });
      return;
    }
    const rowEl = event.target.closest('tr[data-row-index]');
    if (!rowEl) return;
    const row = (window.__liveRows || [])[Number(rowEl.dataset.rowIndex || -1)];
    if (!row) return;
    renderSelected(row);
    renderRows(window.__liveRows || []);
  });

  Promise.resolve(loadBranchOptions()).catch(() => {
    // Keep live attendance usable even if branches fail to load.
  }).finally(() => {
    refreshAll();
  });
})();
</script>
@endpush
