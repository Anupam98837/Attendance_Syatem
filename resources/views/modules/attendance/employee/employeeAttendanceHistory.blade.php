@extends('pages.layout.structure')
@section('title', 'My Attendance History')

@push('styles')
<style>
.hist-wrap { display:grid; gap:22px; }

/* ── Hero ── */
.hist-hero {
  position:relative; overflow:hidden;
  border:1px solid var(--line-strong); border-radius:32px; padding:30px;
  background:
    radial-gradient(circle at top right, rgba(37,99,235,.18), transparent 34%),
    linear-gradient(140deg, rgba(37,99,235,.1), rgba(14,165,233,.13));
  box-shadow:var(--shadow-2);
}
.hist-kicker {
  display:inline-flex; align-items:center; gap:8px;
  padding:8px 13px; border-radius:999px;
  background:rgba(255,255,255,.78); border:1px solid rgba(37,99,235,.18);
  color:var(--accent-color); font-size:12px; font-weight:800;
  text-transform:uppercase; letter-spacing:.08em;
}
.hist-hero h1 { margin:12px 0 8px; font-size:clamp(1.8rem,3.5vw,2.6rem); letter-spacing:-.04em; }
.hist-hero p  { margin:0; color:var(--muted-color); max-width:64ch; line-height:1.75; }

/* ── Filter bar ── */
.hist-filters {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:20px; padding:16px 20px; box-shadow:var(--shadow-1);
}
.hist-filter-row { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.hist-filter-row label { font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted-color); font-weight:700; display:block; margin-bottom:5px; }

/* ── Table card ── */
.hist-card {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:24px; box-shadow:var(--shadow-1); overflow:hidden;
}
.hist-table { width:100%; border-collapse:collapse; }
.hist-table thead th {
  background:var(--surface-3); color:var(--ink);
  font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
  padding:11px 13px; white-space:nowrap; border-bottom:1px solid var(--line-strong);
}
.hist-table tbody td {
  padding:12px 13px; border-top:1px solid var(--line-soft); font-size:13px; vertical-align:middle;
}
.hist-table tbody tr:hover { background:var(--surface-2); }
.hist-empty { text-align:center; padding:40px 16px; color:var(--muted-color); font-size:14px; }
.hist-foot {
  display:flex; align-items:center; justify-content:space-between;
  padding:13px 18px; border-top:1px solid var(--line-soft); flex-wrap:wrap; gap:10px;
}
.hist-foot .info { font-size:12px; color:var(--muted-color); }

/* ── Pills ── */
.pill {
  display:inline-flex; align-items:center; gap:4px;
  padding:4px 10px; border-radius:999px;
  font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
}
.pill-present            { background:rgba(22,163,74,.12);  color:#16a34a; }
.pill-late               { background:rgba(245,158,11,.14); color:#d97706; }
.pill-absent             { background:rgba(220,38,38,.12);  color:#dc2626; }
.pill-half_day           { background:rgba(14,165,233,.12); color:var(--primary-color); }
.pill-leave              { background:rgba(124,58,237,.12); color:#7c3aed; }
.pill-holiday            { background:rgba(124,58,237,.12); color:#7c3aed; }
.pill-week_off           { background:rgba(100,116,139,.12);color:#475569; }
.pill-pending_approval   { background:rgba(245,158,11,.13); color:#d97706; }
.pill-manual_corrected   { background:rgba(14,165,233,.12); color:var(--primary-color); }
.pill-approved           { background:rgba(22,163,74,.12);  color:#16a34a; }
.pill-rejected           { background:rgba(220,38,38,.12);  color:#dc2626; }
.pill-default            { background:var(--surface-3);     color:var(--muted-color); }

/* ── Back link ── */
.back-link {
  display:inline-flex; align-items:center; gap:8px;
  font-size:13px; font-weight:700; color:var(--muted-color);
  text-decoration:none; transition:color .15s ease;
}
.back-link:hover { color:var(--primary-color); }
</style>
@endpush

@section('content')
<div class="hist-wrap anim-fade-in">

  {{-- Back link --}}
  <div>
    <a href="/dashboard" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

  {{-- Hero --}}
  <section class="hist-hero">
    <div class="hist-kicker"><i class="fa-solid fa-calendar-days"></i> My Records</div>
    <h1>Attendance History</h1>
    <p>Review all your past check-in and check-out records, working hours, late arrivals, and approval statuses.</p>
  </section>

  {{-- Filters --}}
  <div class="hist-filters">
    <div class="hist-filter-row">
      <div>
        <label>From Date</label>
        <input type="date" id="histFrom" class="form-control form-control-sm" style="width:150px;border-radius:10px;">
      </div>
      <div>
        <label>To Date</label>
        <input type="date" id="histTo" class="form-control form-control-sm" style="width:150px;border-radius:10px;">
      </div>
      <div>
        <label>Status</label>
        <select id="histStatus" class="form-select form-select-sm" style="width:165px;border-radius:10px;">
          <option value="">All Statuses</option>
          <option value="present">Present</option>
          <option value="late">Late</option>
          <option value="absent">Absent</option>
          <option value="half_day">Half Day</option>
          <option value="leave">On Leave</option>
          <option value="holiday">Holiday</option>
          <option value="week_off">Week Off</option>
          <option value="pending_approval">Pending Approval</option>
          <option value="manual_corrected">Manual Corrected</option>
        </select>
      </div>
      <div>
        <label>Per Page</label>
        <select id="histPerPage" class="form-select form-select-sm" style="width:90px;border-radius:10px;">
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div class="ms-auto d-flex align-items-end gap-2">
        <button class="btn btn-sm btn-light" id="histReset" style="border-radius:10px;">
          <i class="fa-solid fa-rotate-left me-1"></i>Reset
        </button>
        <button class="btn btn-sm btn-primary" id="histRefresh" style="border-radius:10px;">
          <i class="fa-solid fa-arrows-rotate me-1"></i>Refresh
        </button>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="hist-card">
    <div class="table-responsive">
      <table class="hist-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Day</th>
            <th>Status</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Work Hrs</th>
            <th>Late (min)</th>
            <th>Overtime</th>
            <th>Mode</th>
            <th>Work Mode</th>
            <th>Approval</th>
            <th>Activity</th>
          </tr>
        </thead>
        <tbody id="histTbody">
          <tr><td colspan="12" class="hist-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading your attendance history…</td></tr>
        </tbody>
      </table>
    </div>
    <div class="hist-foot">
      <div class="info" id="histInfo">—</div>
      <ul class="pagination pagination-sm mb-0" id="histPager"></ul>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) { window.location.replace('/'); return; }

  const API = (path) => fetch(path, {
    headers: { 'Authorization':'Bearer '+token, 'Accept':'application/json' }
  });

  // Timezone — use cached value from dashboard visit, or fetch from bootstrap once
  let companyTz = localStorage.getItem('companyTz') || null;

  async function ensureTimezone() {
    if (companyTz) return;
    try {
      const res  = await fetch('/api/attendance/mobile/bootstrap', {
        headers: { 'Authorization':'Bearer '+token, 'Accept':'application/json' }
      });
      const json = await res.json();
      if (res.ok) {
        const tz = json.data?.company?.timezone;
        if (tz) { companyTz = tz; localStorage.setItem('companyTz', tz); }
      }
    } catch {}
    if (!companyTz) companyTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
  }

  const S = { page: 1 };

  function esc(v) {
    if (v===null||v===undefined||v==='') return '<span class="text-muted">—</span>';
    return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
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
    if (/^\d{2}:\d{2}(:\d{2})?$/.test(raw)) return { raw, kind: 'sql_time' };
    const parsed = new Date(raw);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
  }
  function formatSqlDate(value) {
    const m = String(value || '').trim().match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!m) return null;
    return monthLabel(m[1], m[2], m[3]);
  }
  function formatSqlDateTime(value) {
    const m = String(value || '').trim().match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?$/);
    if (!m) return null;
    return {
      date: monthLabel(m[1], m[2], m[3]),
      time: formatClockParts(m[4], m[5]),
    };
  }
  function pill(v) {
    if (!v) return '<span class="text-muted">—</span>';
    const cls = `pill-${String(v).toLowerCase()}`;
    return `<span class="pill ${cls}">${String(v).replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}</span>`;
  }
  function fmtDate(d) {
    if (!d) return '—';
    try {
      const parsed = parseServerDate(d);
      if (parsed?.kind === 'sql_date') return formatSqlDate(parsed.raw) || d;
      if (parsed?.kind === 'sql_datetime') return formatSqlDateTime(parsed.raw)?.date || d;
      const opts = {year:'numeric', month:'short', day:'2-digit'};
      if (companyTz) opts.timeZone = companyTz;
      return ((parsed instanceof Date ? parsed : new Date(d))).toLocaleDateString([], opts);
    } catch { return d; }
  }
  function fmtDay(d) {
    if (!d) return '';
    try {
      const sqlDate = formatSqlDate(d);
      if (sqlDate) {
        const base = String(d).trim().split(' ')[0];
        const m = base.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (m) {
          const dt = new Date(Date.UTC(Number(m[1]), Number(m[2]) - 1, Number(m[3]), 12, 0, 0));
          return dt.toLocaleDateString([], { weekday:'short', timeZone:'UTC' });
        }
      }
      const opts = {weekday:'short'};
      if (companyTz) opts.timeZone = companyTz;
      const parsed = parseServerDate(d);
      return ((parsed instanceof Date ? parsed : new Date(d))).toLocaleDateString([], opts);
    } catch { return ''; }
  }
  function fmtTime(ts) {
    if (!ts) return null;
    try {
      const parsed = parseServerDate(ts);
      if (parsed?.kind === 'sql_datetime') return formatSqlDateTime(parsed.raw)?.time || ts;
      if (parsed?.kind === 'sql_time') {
        const tm = String(parsed.raw).trim().match(/^(\d{2}):(\d{2})(?::(\d{2}))?$/);
        return tm ? formatClockParts(tm[1], tm[2]) : ts;
      }
      const opts = {hour:'2-digit', minute:'2-digit'};
      if (companyTz) opts.timeZone = companyTz;
      return ((parsed instanceof Date ? parsed : new Date(ts))).toLocaleTimeString([], { ...opts, hour12:true });
    } catch { return ts; }
  }
  function toHrs(min) {
    if (!min || min <= 0) return '<span class="text-muted">0h</span>';
    const h = Math.floor(min/60), m = min%60;
    if (h===0) return `<strong>${m}m</strong>`;
    if (m===0) return `<strong>${h}h</strong>`;
    return `<strong>${h}h</strong> <span class="text-muted">${m}m</span>`;
  }

  function buildQuery() {
    const p = new URLSearchParams({ page: S.page, per_page: document.getElementById('histPerPage').value });
    const from = document.getElementById('histFrom').value;
    const to   = document.getElementById('histTo').value;
    const st   = document.getElementById('histStatus').value;
    if (from) p.set('from', from);
    if (to)   p.set('to', to);
    if (st)   p.set('status', st);
    return p.toString();
  }

  async function load() {
    document.getElementById('histTbody').innerHTML =
      `<tr><td colspan="12" class="hist-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading…</td></tr>`;
    try {
      const res  = await API('/api/attendance/mobile/history?' + buildQuery());
      const json = await res.json();
      if (!res.ok) { renderEmpty(json.message||'Failed to load'); return; }
      render(json.data || []);
      renderPager(json.pagination || {});
      document.getElementById('histInfo').textContent =
        `Showing ${(json.data||[]).length} of ${json.pagination?.total ?? '?'} records`;
    } catch(e) {
      renderEmpty('Network error. Please try again.');
    }
  }

  function render(rows) {
    const tb = document.getElementById('histTbody');
    if (!rows.length) { renderEmpty('No attendance records found for the selected filters.'); return; }
    tb.innerHTML = rows.map(r => {
      const ci = fmtTime(r.check_in_time);
      const co = fmtTime(r.check_out_time);
      return `<tr>
        <td><strong>${fmtDate(r.attendance_date)}</strong></td>
        <td><span class="text-muted">${fmtDay(r.attendance_date)}</span></td>
        <td>${pill(r.status)}</td>
        <td>${ci ? `<span style="color:#16a34a;font-weight:700;">${ci}</span>` : '<span class="text-muted">—</span>'}</td>
        <td>${co ? `<span style="color:#dc2626;font-weight:700;">${co}</span>` : '<span class="text-muted">—</span>'}</td>
        <td>${toHrs(r.total_working_minutes)}</td>
        <td>${r.late_minutes > 0 ? `<span style="color:#dc2626;font-weight:700;">${r.late_minutes}m</span>` : '<span class="text-muted">0</span>'}</td>
        <td>${r.overtime_minutes > 0 ? `<span style="color:#7c3aed;font-weight:700;">${toHrs(r.overtime_minutes)}</span>` : '<span class="text-muted">—</span>'}</td>
        <td>${r.attendance_mode ? `<span class="pill pill-default">${esc(r.attendance_mode)}</span>` : '<span class="text-muted">—</span>'}</td>
        <td>${r.work_mode ? `<span class="pill pill-default">${esc(r.work_mode)}</span>` : '<span class="text-muted">—</span>'}</td>
        <td>${pill(r.approval_status)}</td>
        <td>
          <a href="/attendance/employee-activity?date=${esc(r.attendance_date)}"
             style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;
                    background:rgba(124,58,237,.1);color:#7c3aed;font-size:11px;font-weight:700;
                    text-decoration:none;border:1px solid rgba(124,58,237,.18);transition:all .15s;"
             title="View activity tracking for ${esc(r.attendance_date)}">
            <i class="fa-solid fa-satellite-dish"></i> Activity
          </a>
        </td>
      </tr>`;
    }).join('');
  }

  function renderEmpty(msg) {
    document.getElementById('histTbody').innerHTML =
      `<tr><td colspan="12" class="hist-empty"><i class="fa-regular fa-folder-open me-2"></i>${msg}</td></tr>`;
  }

  function renderPager(pg) {
    const el = document.getElementById('histPager');
    if (!pg.last_page || pg.last_page <= 1) { el.innerHTML=''; return; }
    const page = Number(pg.page||1), last = Number(pg.last_page||1);
    const items = [];
    items.push(`<li class="page-item ${page<=1?'disabled':''}"><button class="page-link" data-pg="${page-1}">‹</button></li>`);
    for (let i=1; i<=last; i++) {
      if (i===1||i===last||Math.abs(i-page)<=1)
        items.push(`<li class="page-item ${i===page?'active':''}"><button class="page-link" data-pg="${i}">${i}</button></li>`);
      else if (Math.abs(i-page)===2)
        items.push('<li class="page-item disabled"><span class="page-link">…</span></li>');
    }
    items.push(`<li class="page-item ${page>=last?'disabled':''}"><button class="page-link" data-pg="${page+1}">›</button></li>`);
    el.innerHTML = items.join('');
  }

  // Set default date range (last 30 days)
  const today = new Date(), from = new Date(today);
  from.setDate(from.getDate() - 30);
  document.getElementById('histTo').value   = today.toISOString().split('T')[0];
  document.getElementById('histFrom').value = from.toISOString().split('T')[0];

  // Wire events
  document.getElementById('histRefresh').addEventListener('click', () => { S.page=1; load(); });
  document.getElementById('histReset').addEventListener('click', () => {
    document.getElementById('histFrom').value   = from.toISOString().split('T')[0];
    document.getElementById('histTo').value     = today.toISOString().split('T')[0];
    document.getElementById('histStatus').value = '';
    S.page = 1; load();
  });
  ['histFrom','histTo','histStatus','histPerPage'].forEach(id =>
    document.getElementById(id).addEventListener('change', () => { S.page=1; load(); })
  );
  document.getElementById('histPager').addEventListener('click', e => {
    const btn = e.target.closest('[data-pg]');
    if (!btn) return;
    S.page = Number(btn.dataset.pg); load();
  });

  // Ensure timezone is set before rendering any dates/times
  ensureTimezone().then(() => load());
})();
</script>
@endpush
