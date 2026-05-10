@extends('pages.layout.structure')
@section('title', 'Activity Logs')

@push('styles')
<style>
.alog-wrap { display:grid; gap:22px; }

/* ── Hero ── */
.alog-hero {
  position:relative; overflow:hidden;
  border:1px solid var(--line-strong); border-radius:32px; padding:30px;
  background:
    radial-gradient(circle at top right, rgba(124,58,237,.18), transparent 34%),
    linear-gradient(140deg, rgba(37,99,235,.1), rgba(14,165,233,.13));
  box-shadow:var(--shadow-2);
}
.alog-kicker {
  display:inline-flex; align-items:center; gap:8px;
  padding:8px 13px; border-radius:999px;
  background:rgba(255,255,255,.78); border:1px solid rgba(124,58,237,.18);
  color:#7c3aed; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.08em;
}
.alog-hero h1 { margin:12px 0 8px; font-size:clamp(1.8rem,3.5vw,2.6rem); letter-spacing:-.04em; }
.alog-hero p  { margin:0; color:var(--muted-color); max-width:64ch; line-height:1.75; }

/* ── Filters ── */
.alog-filters {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:20px; padding:16px 20px; box-shadow:var(--shadow-1);
}
.alog-filter-row { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.alog-filter-row label { font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted-color); font-weight:700; display:block; margin-bottom:5px; }

/* ── Table card ── */
.alog-card {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:24px; box-shadow:var(--shadow-1); overflow:hidden;
}
.alog-table { width:100%; border-collapse:collapse; }
.alog-table thead th {
  background:var(--surface-3); color:var(--ink);
  font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
  padding:11px 13px; white-space:nowrap; border-bottom:1px solid var(--line-strong);
}
.alog-table tbody td {
  padding:11px 13px; border-top:1px solid var(--line-soft); font-size:13px; vertical-align:middle;
}
.alog-table tbody tr:hover { background:var(--surface-2); cursor:pointer; }
.alog-foot {
  display:flex; align-items:center; justify-content:space-between;
  padding:13px 18px; border-top:1px solid var(--line-soft); flex-wrap:wrap; gap:10px;
}
.alog-foot .info { font-size:12px; color:var(--muted-color); }
.alog-empty { text-align:center; padding:40px 16px; color:var(--muted-color); font-size:14px; }

/* ── Mini pills ── */
.mini-pill {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 8px; border-radius:999px;
  font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
}
.mp-green  { background:rgba(22,163,74,.12);  color:#16a34a; }
.mp-amber  { background:rgba(245,158,11,.13); color:#d97706; }
.mp-blue   { background:rgba(14,165,233,.12); color:var(--primary-color); }
.mp-purple { background:rgba(124,58,237,.12); color:#7c3aed; }
.mp-red    { background:rgba(220,38,38,.12);  color:#dc2626; }
.mp-neutral{ background:var(--surface-3);     color:var(--muted-color); }

/* ── Progress bar ── */
.mini-bar { height:5px; border-radius:999px; background:var(--line-soft); overflow:hidden; width:80px; display:inline-block; vertical-align:middle; }
.mini-bar-fill { height:100%; border-radius:999px; }

/* ── Detail modal ── */
.alog-modal-bg {
  position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1050;
  display:flex; align-items:center; justify-content:center; padding:20px;
}
.alog-modal {
  background:var(--surface); border-radius:28px; box-shadow:var(--shadow-3);
  width:100%; max-width:800px; max-height:90vh; overflow-y:auto;
}
.alog-modal-head {
  padding:22px 26px 16px; border-bottom:1px solid var(--line-soft);
  display:flex; align-items:center; gap:12px;
}
.alog-modal-head h2 { margin:0; font-size:18px; flex:1; }
.alog-modal-body { padding:22px 26px; display:grid; gap:18px; }
.alog-modal-section-title {
  font-size:10px; text-transform:uppercase; letter-spacing:.07em;
  color:var(--muted-color); font-weight:800; margin-bottom:10px;
}
.detail-grid {
  display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:10px;
}
.detail-box {
  background:var(--surface-2); border:1px solid var(--line-soft);
  border-radius:14px; padding:14px 12px; text-align:center;
}
.detail-box-val   { font-size:19px; font-weight:800; color:var(--ink); line-height:1; }
.detail-box-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--muted-color); margin-top:5px; }
.gps-mini-table { width:100%; border-collapse:collapse; font-size:11px; max-height:220px; overflow-y:auto; display:block; }
.gps-mini-table th { background:var(--surface-3); padding:6px 8px; font-size:9px; text-transform:uppercase; letter-spacing:.06em; border-bottom:1px solid var(--line-strong); }
.gps-mini-table td { padding:6px 8px; border-top:1px solid var(--line-soft); }
.gps-mini-table tr:hover { background:var(--surface-2); }
</style>
@endpush

@section('content')
<div class="alog-wrap anim-fade-in">

  {{-- Hero --}}
  <section class="alog-hero">
    <div class="alog-kicker"><i class="fa-solid fa-satellite-dish"></i> HR Dashboard</div>
    <h1>Employee Activity Logs</h1>
    <p>Review GPS travel paths, WiFi connectivity patterns, office presence, and movement data for every employee's work day.</p>
  </section>

  {{-- Filters --}}
  <div class="alog-filters">
    <div class="alog-filter-row">
      <div>
        <label>From Date</label>
        <input type="date" id="alogFrom" class="form-control form-control-sm" style="width:145px;border-radius:10px;">
      </div>
      <div>
        <label>To Date</label>
        <input type="date" id="alogTo" class="form-control form-control-sm" style="width:145px;border-radius:10px;">
      </div>
      <div>
        <label>Search Employee</label>
        <input type="text" id="alogSearch" class="form-control form-control-sm" placeholder="Name or code…" style="width:190px;border-radius:10px;">
      </div>
      <div>
        <label>Per Page</label>
        <select id="alogPerPage" class="form-select form-select-sm" style="width:85px;border-radius:10px;">
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div class="ms-auto d-flex align-items-end gap-2">
        <button class="btn btn-sm btn-light" id="alogReset" style="border-radius:10px;">
          <i class="fa-solid fa-rotate-left me-1"></i>Reset
        </button>
        <button class="btn btn-sm btn-primary" id="alogRefresh" style="border-radius:10px;">
          <i class="fa-solid fa-arrows-rotate me-1"></i>Refresh
        </button>
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="alog-card">
    <div class="table-responsive">
      <table class="alog-table">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Date</th>
            <th>Attendance</th>
            <th>Platform</th>
            <th>GPS Conn/Disc</th>
            <th>Distance</th>
            <th>Travel</th>
            <th>WiFi Switches</th>
            <th>Office In/Out</th>
            <th>Outside Office</th>
            <th>Stationary</th>
            <th>Max Speed</th>
            <th>Battery Δ</th>
            <th>Synced</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="alogTbody">
          <tr><td colspan="15" class="alog-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading…</td></tr>
        </tbody>
      </table>
    </div>
    <div class="alog-foot">
      <div class="info" id="alogInfo">—</div>
      <ul class="pagination pagination-sm mb-0" id="alogPager"></ul>
    </div>
  </div>

</div>

{{-- Detail modal (hidden) --}}
<div class="alog-modal-bg" id="detailModal" style="display:none;" onclick="if(event.target===this)closeModal()">
  <div class="alog-modal">
    <div class="alog-modal-head">
      <div>
        <div style="font-size:11px;color:var(--muted-color);font-weight:700;text-transform:uppercase;letter-spacing:.06em;" id="modalSubtitle">Activity Log Detail</div>
        <h2 id="modalTitle">—</h2>
      </div>
      <button class="btn btn-sm btn-light ms-auto" onclick="closeModal()" style="border-radius:10px;">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="alog-modal-body" id="modalBody">
      <div style="text-align:center;padding:40px;color:var(--muted-color);">
        <i class="fa-solid fa-spinner fa-spin"></i> Loading details…
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) { window.location.replace('/'); return; }
  const API = (path) => fetch(path, { headers:{'Authorization':'Bearer '+token,'Accept':'application/json'} });

  let companyTz = localStorage.getItem('companyTz') || null;
  const S = { page: 1 };

  function esc(v) {
    if (v===null||v===undefined||v==='') return '—';
    return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function fmtTime(ts) {
    if (!ts) return '—';
    try {
      const opts = {hour:'2-digit', minute:'2-digit'};
      if (companyTz) opts.timeZone = companyTz;
      return new Date(ts).toLocaleTimeString([], opts);
    } catch { return String(ts); }
  }
  function fmtDate(d) {
    if (!d) return '—';
    try {
      const opts = {year:'numeric', month:'short', day:'2-digit'};
      if (companyTz) opts.timeZone = companyTz;
      return new Date(d+'T00:00:00').toLocaleDateString([], opts);
    } catch { return d; }
  }
  function toHrs(min) {
    if (!min || min<=0) return '<span class="text-muted">0h</span>';
    const h=Math.floor(min/60), m=min%60;
    if (h===0) return `<strong>${m}m</strong>`;
    if (m===0) return `<strong>${h}h</strong>`;
    return `<strong>${h}h</strong> <span class="text-muted">${m}m</span>`;
  }
  function toKm(meters) {
    if (!meters||meters===0) return '<span class="text-muted">0m</span>';
    if (meters<1000) return `<strong>${meters}m</strong>`;
    return `<strong>${(meters/1000).toFixed(2)}km</strong>`;
  }
  function platformIcon(p) {
    const icons={android:'fa-android',ios:'fa-apple',web:'fa-globe'};
    if (!p) return '<span class="text-muted">—</span>';
    return `<i class="fa-brands ${icons[p]||'fa-mobile-screen'} me-1"></i>${esc(p)}`;
  }
  function attPill(s) {
    if (!s) return '<span class="text-muted">—</span>';
    const m = {'present':'mp-green','late':'mp-amber','absent':'mp-red','on_leave':'mp-purple'};
    return `<span class="mini-pill ${m[s]||'mp-neutral'}">${esc(s).replace(/_/g,' ')}</span>`;
  }
  function batteryDelta(s, e) {
    if (s===null||s===undefined||e===null||e===undefined) return '<span class="text-muted">—</span>';
    const delta = e - s;
    const cls   = delta >= 0 ? 'mp-green' : (delta < -30 ? 'mp-red' : 'mp-amber');
    return `<span class="mini-pill ${cls}">${s}% → ${e}% (${delta>0?'+':''}${delta}%)</span>`;
  }
  function miniBar(val, max, color) {
    const pct = max>0 ? Math.min(100, Math.round(val/max*100)) : 0;
    return `<div class="mini-bar"><div class="mini-bar-fill" style="width:${pct}%;background:${color};"></div></div>`;
  }

  /* ── Load list ── */
  function buildQuery() {
    const p = new URLSearchParams({ page: S.page, per_page: document.getElementById('alogPerPage').value });
    const from   = document.getElementById('alogFrom').value;
    const to     = document.getElementById('alogTo').value;
    const search = document.getElementById('alogSearch').value.trim();
    if (from)   p.set('from', from);
    if (to)     p.set('to', to);
    if (search) p.set('search', search);
    return p.toString();
  }

  async function load() {
    document.getElementById('alogTbody').innerHTML =
      `<tr><td colspan="15" class="alog-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading…</td></tr>`;
    try {
      const res  = await API('/api/attendance/hr/activity-logs?' + buildQuery());
      const json = await res.json();
      if (!res.ok) { renderEmpty(json.message||'Failed to load'); return; }
      render(json.data||[]);
      renderPager(json.pagination||{});
      document.getElementById('alogInfo').textContent =
        `Showing ${(json.data||[]).length} of ${json.pagination?.total??'?'} records`;
    } catch(e) { renderEmpty('Network error.'); }
  }

  function render(rows) {
    const tb = document.getElementById('alogTbody');
    if (!rows.length) { renderEmpty('No activity logs found for the selected filters.'); return; }
    tb.innerHTML = rows.map(r => `
      <tr onclick="openModal(${r.id})" title="Click for full detail">
        <td>
          <strong>${esc(r.employee_name)}</strong>
          ${r.employee_code ? `<br><span class="text-muted" style="font-size:11px;">${esc(r.employee_code)}</span>` : ''}
        </td>
        <td><strong>${fmtDate(r.attendance_date)}</strong></td>
        <td>${attPill(r.attendance_status)}</td>
        <td>${platformIcon(r.platform)}</td>
        <td>
          <span style="color:#16a34a;font-weight:700;">${r.gps_connect_count||0}↑</span>
          <span class="text-muted mx-1">/</span>
          <span style="color:#dc2626;font-weight:700;">${r.gps_disconnect_count||0}↓</span>
        </td>
        <td>${toKm(r.total_distance_meters)}</td>
        <td>
          ${r.is_traveling
            ? '<span class="mini-pill mp-amber"><i class="fa-solid fa-car me-1"></i>Traveling</span>'
            : '<span class="mini-pill mp-green"><i class="fa-solid fa-building me-1"></i>Local</span>'}
          ${r.time_traveling_minutes > 0
            ? `<br><span class="text-muted" style="font-size:11px;">${toHrs(r.time_traveling_minutes)}</span>` : ''}
        </td>
        <td>
          ${r.wifi_switch_count > 0
            ? `<strong style="color:#d97706;">${r.wifi_switch_count}</strong> switches`
            : `<span class="text-muted">${r.wifi_switch_count||0}</span>`}
          <br><span class="text-muted" style="font-size:11px;">${r.wifi_connect_count||0}↑ / ${r.wifi_disconnect_count||0}↓</span>
        </td>
        <td>
          <span style="color:#16a34a;font-weight:700;">${r.office_entry_count||0}↑</span>
          <span class="text-muted mx-1">/</span>
          <span style="color:#dc2626;font-weight:700;">${r.office_exit_count||0}↓</span>
        </td>
        <td>
          ${toHrs(r.time_outside_office_minutes)}
          ${r.time_outside_office_minutes > 60
            ? `<br>${miniBar(r.time_outside_office_minutes,r.time_inside_office_minutes+r.time_outside_office_minutes,'#dc2626')}`
            : ''}
        </td>
        <td>${toHrs(r.time_stationary_minutes)}</td>
        <td>${r.max_speed_kmh ? `<strong>${parseFloat(r.max_speed_kmh).toFixed(1)}</strong><span class="text-muted"> km/h</span>` : '<span class="text-muted">—</span>'}</td>
        <td>${batteryDelta(r.battery_start_percent, r.battery_end_percent)}</td>
        <td>${r.synced_at ? `<span class="text-muted" style="font-size:11px;">${fmtTime(r.synced_at)}</span>` : '<span class="mini-pill mp-amber">Not synced</span>'}</td>
        <td><button class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:11px;" onclick="event.stopPropagation();openModal(${r.id})"><i class="fa-solid fa-expand me-1"></i>Detail</button></td>
      </tr>`).join('');
  }

  function renderEmpty(msg) {
    document.getElementById('alogTbody').innerHTML =
      `<tr><td colspan="15" class="alog-empty"><i class="fa-regular fa-folder-open me-2"></i>${msg}</td></tr>`;
  }

  function renderPager(pg) {
    const el = document.getElementById('alogPager');
    if (!pg.last_page||pg.last_page<=1) { el.innerHTML=''; return; }
    const page=Number(pg.page||1), last=Number(pg.last_page||1);
    const items=[];
    items.push(`<li class="page-item ${page<=1?'disabled':''}"><button class="page-link" data-pg="${page-1}">‹</button></li>`);
    for (let i=1;i<=last;i++) {
      if (i===1||i===last||Math.abs(i-page)<=1)
        items.push(`<li class="page-item ${i===page?'active':''}"><button class="page-link" data-pg="${i}">${i}</button></li>`);
      else if (Math.abs(i-page)===2)
        items.push('<li class="page-item disabled"><span class="page-link">…</span></li>');
    }
    items.push(`<li class="page-item ${page>=last?'disabled':''}"><button class="page-link" data-pg="${page+1}">›</button></li>`);
    el.innerHTML=items.join('');
  }

  /* ── Detail modal ── */
  window.openModal = async (id) => {
    document.getElementById('detailModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = 'Loading…';
    document.getElementById('modalBody').innerHTML =
      '<div style="text-align:center;padding:40px;"><i class="fa-solid fa-spinner fa-spin"></i></div>';
    try {
      const res  = await API('/api/attendance/hr/activity-logs/'+id);
      const json = await res.json();
      if (!res.ok) { document.getElementById('modalBody').innerHTML = `<div class="text-danger">${esc(json.message)}</div>`; return; }
      renderModal(json.data);
    } catch(e) {
      document.getElementById('modalBody').innerHTML = '<div class="text-danger">Network error.</div>';
    }
  };
  window.closeModal = () => { document.getElementById('detailModal').style.display='none'; };
  document.addEventListener('keydown', e => { if (e.key==='Escape') closeModal(); });

  function renderModal(d) {
    document.getElementById('modalSubtitle').textContent = fmtDate(d.attendance_date);
    document.getElementById('modalTitle').textContent = d.employee_name || 'Activity Detail';

    const dbox = (val,label) =>
      `<div class="detail-box"><div class="detail-box-val">${val}</div><div class="detail-box-label">${label}</div></div>`;

    function toHrsPlain(min) {
      if (!min||min<=0) return '0h';
      const h=Math.floor(min/60), m=min%60;
      if (h===0) return `${m}m`;
      if (m===0) return `${h}h`;
      return `${h}h ${m}m`;
    }
    function toKmPlain(m) {
      if (!m||m===0) return '0m';
      if (m<1000) return `${m}m`;
      return `${(m/1000).toFixed(2)}km`;
    }

    let gpsPathHtml = '';
    if (d.gps_path && d.gps_path.length) {
      gpsPathHtml = `
        <div>
          <div class="alog-modal-section-title">GPS Path (${d.gps_path.length} waypoints)</div>
          <table class="gps-mini-table">
            <thead><tr><th>#</th><th>Time</th><th>Latitude</th><th>Longitude</th><th>Accuracy</th><th>Speed km/h</th><th>Moving</th></tr></thead>
            <tbody>${d.gps_path.map((p,i)=>`<tr>
              <td>${i+1}</td><td>${fmtTime(p.ts)}</td>
              <td>${p.lat!=null?p.lat.toFixed(6):'—'}</td><td>${p.lng!=null?p.lng.toFixed(6):'—'}</td>
              <td>${p.acc?`±${p.acc}m`:'—'}</td><td>${p.spd_kmh!=null?p.spd_kmh:'—'}</td>
              <td>${p.is_moving?'<span style="color:#16a34a;">●</span> Yes':'<span style="color:#f59e0b;">○</span> No'}</td>
            </tr>`).join('')}</tbody>
          </table>
        </div>`;
    }

    let wifiHtml = '';
    if (d.wifi_networks_seen && d.wifi_networks_seen.length) {
      wifiHtml = `
        <div>
          <div class="alog-modal-section-title">WiFi Networks (${d.wifi_networks_seen.length})</div>
          ${d.wifi_networks_seen.map(n=>`
            <div style="padding:10px;background:var(--surface-2);border-radius:12px;margin-bottom:8px;font-size:12px;">
              <strong>${esc(n.ssid||'Unknown')}</strong>
              ${n.bssid?`<span class="text-muted ms-2">${esc(n.bssid)}</span>`:''}
              <div class="text-muted mt-1">
                ${n.connected_at?`In: ${fmtTime(n.connected_at)}`:''}
                ${n.disconnected_at?` · Out: ${fmtTime(n.disconnected_at)}`:''}
                ${n.duration_seconds?` · ${Math.round(n.duration_seconds/60)}m`:''}
              </div>
            </div>`).join('')}
        </div>`;
    }

    document.getElementById('modalBody').innerHTML = `
      <div>
        <div class="alog-modal-section-title">GPS Summary</div>
        <div class="detail-grid">
          ${dbox(d.gps_connect_count||0,'GPS Connected')}
          ${dbox(d.gps_disconnect_count||0,'GPS Disconnected')}
          ${dbox(toKmPlain(d.total_distance_meters),'Total Distance')}
          ${dbox(d.max_speed_kmh?parseFloat(d.max_speed_kmh).toFixed(1)+' km/h':'—','Max Speed')}
          ${dbox(d.furthest_distance_from_office_meters?toKmPlain(d.furthest_distance_from_office_meters):'—','Farthest from Office')}
          ${dbox(d.is_traveling?'Yes':'No','Was Traveling')}
          ${dbox(toHrsPlain(d.time_traveling_minutes),'Time Traveling')}
          ${dbox(toHrsPlain(d.time_stationary_minutes),'Time Stationary')}
        </div>
      </div>
      <div>
        <div class="alog-modal-section-title">WiFi Summary</div>
        <div class="detail-grid">
          ${dbox(d.wifi_connect_count||0,'Connects')}
          ${dbox(d.wifi_disconnect_count||0,'Disconnects')}
          ${dbox(d.wifi_switch_count||0,'Network Switches')}
          ${dbox((d.wifi_networks_seen||[]).length,'Networks Seen')}
        </div>
      </div>
      <div>
        <div class="alog-modal-section-title">Office Presence</div>
        <div class="detail-grid">
          ${dbox(d.office_entry_count||0,'Entries')}
          ${dbox(d.office_exit_count||0,'Exits')}
          ${dbox(toHrsPlain(d.time_inside_office_minutes),'Inside Office')}
          ${dbox(toHrsPlain(d.time_outside_office_minutes),'Outside Office')}
        </div>
      </div>
      <div>
        <div class="alog-modal-section-title">Movement & App</div>
        <div class="detail-grid">
          ${dbox(d.movement_event_count||0,'Movement Events')}
          ${dbox(toHrsPlain(d.idle_streak_max_minutes),'Longest Idle')}
          ${dbox(toHrsPlain(d.app_foreground_minutes),'App Foreground')}
          ${dbox(toHrsPlain(d.app_background_minutes),'App Background')}
          ${dbox(d.battery_start_percent!==null?d.battery_start_percent+'%':'—','Battery Start')}
          ${dbox(d.battery_end_percent!==null?d.battery_end_percent+'%':'—','Battery End')}
        </div>
      </div>
      ${gpsPathHtml}
      ${wifiHtml}
    `;
  }

  /* ── Wire events ── */
  const today = new Date().toISOString().split('T')[0];
  const from30 = new Date(); from30.setDate(from30.getDate()-30);
  document.getElementById('alogTo').value   = today;
  document.getElementById('alogFrom').value = from30.toISOString().split('T')[0];

  document.getElementById('alogRefresh').addEventListener('click', ()=>{ S.page=1; load(); });
  document.getElementById('alogReset').addEventListener('click', ()=>{
    document.getElementById('alogFrom').value   = from30.toISOString().split('T')[0];
    document.getElementById('alogTo').value     = today;
    document.getElementById('alogSearch').value = '';
    S.page=1; load();
  });
  document.getElementById('alogSearch').addEventListener('keydown', e=>{ if(e.key==='Enter'){S.page=1;load();} });
  ['alogFrom','alogTo','alogPerPage'].forEach(id=>
    document.getElementById(id).addEventListener('change',()=>{ S.page=1; load(); })
  );
  document.getElementById('alogPager').addEventListener('click', e=>{
    const btn=e.target.closest('[data-pg]');
    if (!btn) return;
    S.page=Number(btn.dataset.pg); load();
  });

  load();
})();
</script>
@endpush
