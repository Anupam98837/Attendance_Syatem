@extends('pages.layout.structure')
@section('title', 'My Activity Log')

@push('styles')
<style>
.act-wrap { display:grid; gap:22px; }

/* ── Hero ── */
.act-hero {
  position:relative; overflow:hidden;
  border:1px solid var(--line-strong); border-radius:32px; padding:30px;
  background:
    radial-gradient(circle at top right, rgba(124,58,237,.2), transparent 34%),
    linear-gradient(140deg, rgba(37,99,235,.1), rgba(14,165,233,.14));
  box-shadow:var(--shadow-2);
}
.act-kicker {
  display:inline-flex; align-items:center; gap:8px;
  padding:8px 13px; border-radius:999px;
  background:rgba(255,255,255,.78); border:1px solid rgba(124,58,237,.18);
  color:#7c3aed; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.08em;
}
.act-hero h1 { margin:12px 0 8px; font-size:clamp(1.6rem,3vw,2.4rem); letter-spacing:-.04em; }
.act-hero p  { margin:0; color:var(--muted-color); max-width:64ch; line-height:1.75; }
.act-hero-meta { display:flex; flex-wrap:wrap; gap:10px; margin-top:16px; align-items:center; }

/* ── Date picker bar ── */
.act-date-bar {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:18px; padding:14px 18px; box-shadow:var(--shadow-1);
  display:flex; align-items:center; flex-wrap:wrap; gap:12px;
}

/* ── Status row badges ── */
.act-badge-row { display:flex; flex-wrap:wrap; gap:8px; }
.act-badge {
  display:inline-flex; align-items:center; gap:6px;
  padding:6px 12px; border-radius:999px;
  font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
}
.ab-green  { background:rgba(22,163,74,.12);  color:#16a34a; border:1px solid rgba(22,163,74,.2); }
.ab-blue   { background:rgba(14,165,233,.12); color:var(--primary-color); border:1px solid rgba(14,165,233,.2); }
.ab-purple { background:rgba(124,58,237,.12); color:#7c3aed; border:1px solid rgba(124,58,237,.2); }
.ab-amber  { background:rgba(245,158,11,.13); color:#d97706; border:1px solid rgba(245,158,11,.2); }
.ab-red    { background:rgba(220,38,38,.12);  color:#dc2626; border:1px solid rgba(220,38,38,.2); }
.ab-neutral{ background:var(--surface-3);     color:var(--muted-color); border:1px solid var(--line-soft); }

/* ── Section card ── */
.act-card {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:24px; box-shadow:var(--shadow-1); overflow:hidden;
}
.act-card-head {
  padding:18px 22px 14px; border-bottom:1px solid var(--line-soft);
  display:flex; align-items:center; gap:10px;
}
.act-card-head h2 { margin:0; font-size:16px; flex:1; }
.act-card-body { padding:20px 22px; }

/* ── Stat grid ── */
.act-stat-grid {
  display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:12px;
}
.act-stat {
  background:var(--surface-2); border:1px solid var(--line-soft);
  border-radius:16px; padding:16px 12px; text-align:center;
}
.act-stat-icon {
  width:36px; height:36px; border-radius:10px;
  display:flex; align-items:center; justify-content:center;
  font-size:15px; margin:0 auto 10px;
}
.act-stat-val   { font-size:22px; font-weight:800; font-family:var(--font-head); color:var(--ink); line-height:1; }
.act-stat-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--muted-color); font-weight:700; margin-top:5px; }

/* ── GPS path table ── */
.path-table { width:100%; border-collapse:collapse; font-size:12px; }
.path-table th {
  background:var(--surface-3); color:var(--ink); font-size:10px; font-weight:800;
  text-transform:uppercase; letter-spacing:.06em; padding:8px 10px;
  border-bottom:1px solid var(--line-strong); white-space:nowrap;
}
.path-table td { padding:8px 10px; border-top:1px solid var(--line-soft); vertical-align:middle; }
.path-table tr:hover { background:var(--surface-2); }
.move-dot { width:8px; height:8px; border-radius:50%; display:inline-block; }
.move-dot.moving   { background:#16a34a; }
.move-dot.still    { background:#f59e0b; }

/* ── WiFi timeline ── */
.wifi-item {
  display:flex; align-items:flex-start; gap:14px;
  padding:12px 0; border-bottom:1px solid var(--line-soft);
}
.wifi-item:last-child { border-bottom:none; }
.wifi-icon-wrap {
  width:38px; height:38px; border-radius:12px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; font-size:16px;
}
.wifi-details h4 { margin:0 0 3px; font-size:13px; }
.wifi-details .wifi-meta { font-size:11px; color:var(--muted-color); }

/* ── Event stream ── */
.event-item {
  display:flex; align-items:flex-start; gap:12px;
  padding:10px 0; border-bottom:1px solid var(--line-soft); font-size:12px;
}
.event-item:last-child { border-bottom:none; }
.event-dot {
  width:8px; height:8px; border-radius:50%; margin-top:5px; flex-shrink:0;
}
.ev-gps    { background:#14b8a6; }
.ev-wifi   { background:#3b82f6; }
.ev-office { background:#8b5cf6; }
.ev-app    { background:#f59e0b; }
.ev-move   { background:#22c55e; }
.ev-battery{ background:#ef4444; }
.ev-other  { background:#94a3b8; }

/* ── Empty state ── */
.act-empty {
  text-align:center; padding:60px 20px; color:var(--muted-color);
}
.act-empty i { font-size:48px; margin-bottom:16px; display:block; opacity:.3; }

/* ── Back link ── */
.back-link {
  display:inline-flex; align-items:center; gap:8px;
  font-size:13px; font-weight:700; color:var(--muted-color);
  text-decoration:none; transition:color .15s ease;
}
.back-link:hover { color:var(--primary-color); }

/* ── Live tracking banner ── */
.live-banner {
  background:linear-gradient(135deg,rgba(22,163,74,.1),rgba(14,165,233,.1));
  border:1px solid rgba(22,163,74,.25); border-radius:16px;
  padding:14px 18px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;
}
.live-dot {
  width:10px; height:10px; border-radius:50%; background:#16a34a;
  animation:pulseRing 1.4s ease infinite; flex-shrink:0;
}
</style>
@endpush

@section('content')
<div class="act-wrap anim-fade-in">

  {{-- Back --}}
  <div>
    <a href="/attendance/employee-history" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to Attendance History
    </a>
  </div>

  {{-- Hero --}}
  <section class="act-hero">
    <div class="act-kicker"><i class="fa-solid fa-satellite-dish"></i> Activity Tracking</div>
    <h1>My Daily Activity Log</h1>
    <p>Detailed GPS traversal, WiFi connections, office presence, movement, and app usage for any work day.</p>
    <div class="act-hero-meta" id="heroMeta">
      <span class="act-badge ab-neutral"><i class="fa-solid fa-spinner fa-spin"></i> Loading…</span>
    </div>
  </section>

  {{-- Date selector --}}
  <div class="act-date-bar">
    <label style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted-color);font-weight:700;">
      Select Date
    </label>
    <input type="date" id="actDate" class="form-control form-control-sm" style="width:160px;border-radius:10px;">
    <button class="btn btn-sm btn-primary" id="actLoad" style="border-radius:10px;">
      <i class="fa-solid fa-arrows-rotate me-1"></i>Load
    </button>
    <span class="ms-auto" id="actSyncInfo" style="font-size:12px;color:var(--muted-color);"></span>
  </div>

  {{-- Live tracking banner (shown when viewing today) --}}
  <div class="live-banner" id="liveBanner" style="display:none;">
    <span class="live-dot"></span>
    <div style="flex:1;">
      <strong style="font-size:13px;">Live Tracking Active</strong>
      <span style="font-size:12px;color:var(--muted-color);margin-left:8px;" id="liveStatus">Watching GPS…</span>
    </div>
    <button class="btn btn-sm btn-outline-success" id="syncNowBtn" style="border-radius:10px;font-size:12px;">
      <i class="fa-solid fa-cloud-arrow-up me-1"></i>Sync Now
    </button>
  </div>

  {{-- Main content (hidden until loaded) --}}
  <div id="actContent" style="display:none; display:grid; gap:22px;">

    {{-- Overview stat grid --}}
    <div class="act-card">
      <div class="act-card-head">
        <div class="act-stat-icon" style="background:rgba(14,165,233,.12);color:var(--primary-color);">
          <i class="fa-solid fa-chart-simple"></i>
        </div>
        <h2>Day Overview</h2>
        <span class="act-badge ab-neutral" id="platformBadge" style="font-size:11px;"></span>
      </div>
      <div class="act-card-body">
        <div class="act-stat-grid" id="overviewGrid"></div>
      </div>
    </div>

    {{-- GPS tracking --}}
    <div class="act-card">
      <div class="act-card-head">
        <div class="act-stat-icon" style="background:rgba(22,163,74,.12);color:#16a34a;">
          <i class="fa-solid fa-location-dot"></i>
        </div>
        <h2>GPS Tracking</h2>
        <div class="ms-auto d-flex gap-2" id="gpsBadges"></div>
      </div>
      <div class="act-card-body">
        <div class="act-stat-grid mb-4" id="gpsGrid"></div>
        <div id="gpsPathSection" style="display:none;">
          <div style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted-color);font-weight:700;margin-bottom:10px;">
            GPS Path Waypoints
          </div>
          <div style="max-height:320px;overflow-y:auto;border-radius:12px;border:1px solid var(--line-soft);">
            <table class="path-table">
              <thead>
                <tr>
                  <th>#</th><th>Time</th><th>Latitude</th><th>Longitude</th>
                  <th>Accuracy</th><th>Speed (km/h)</th><th>Status</th>
                </tr>
              </thead>
              <tbody id="gpsPathBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- WiFi --}}
    <div class="act-card">
      <div class="act-card-head">
        <div class="act-stat-icon" style="background:rgba(37,99,235,.12);color:#2563eb;">
          <i class="fa-solid fa-wifi"></i>
        </div>
        <h2>WiFi Activity</h2>
      </div>
      <div class="act-card-body">
        <div class="act-stat-grid mb-4" id="wifiGrid"></div>
        <div id="wifiNetworksSection" style="display:none;">
          <div style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted-color);font-weight:700;margin-bottom:10px;">
            Networks Connected
          </div>
          <div id="wifiNetworksList"></div>
        </div>
      </div>
    </div>

    {{-- Office presence --}}
    <div class="act-card">
      <div class="act-card-head">
        <div class="act-stat-icon" style="background:rgba(124,58,237,.12);color:#7c3aed;">
          <i class="fa-solid fa-building"></i>
        </div>
        <h2>Office Presence</h2>
      </div>
      <div class="act-card-body">
        <div class="act-stat-grid" id="officeGrid"></div>
      </div>
    </div>

    {{-- Movement & App --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:22px;">
      <div class="act-card">
        <div class="act-card-head">
          <div class="act-stat-icon" style="background:rgba(245,158,11,.12);color:#d97706;">
            <i class="fa-solid fa-person-walking"></i>
          </div>
          <h2>Movement</h2>
        </div>
        <div class="act-card-body">
          <div class="act-stat-grid" id="movementGrid"></div>
        </div>
      </div>

      <div class="act-card">
        <div class="act-card-head">
          <div class="act-stat-icon" style="background:rgba(14,165,233,.12);color:var(--primary-color);">
            <i class="fa-solid fa-mobile-screen"></i>
          </div>
          <h2>App Usage</h2>
        </div>
        <div class="act-card-body">
          <div class="act-stat-grid" id="appGrid"></div>
        </div>
      </div>
    </div>

    {{-- Raw event stream --}}
    <div class="act-card" id="eventsCard" style="display:none;">
      <div class="act-card-head">
        <div class="act-stat-icon" style="background:rgba(100,116,139,.12);color:#475569;">
          <i class="fa-solid fa-list-ul"></i>
        </div>
        <h2>Raw Event Stream</h2>
        <span style="font-size:12px;color:var(--muted-color);margin-left:auto;" id="eventsCount"></span>
      </div>
      <div class="act-card-body" style="max-height:400px;overflow-y:auto;" id="eventsList"></div>
    </div>

  </div>

  {{-- Empty state --}}
  <div class="act-card" id="actEmpty" style="display:none;">
    <div class="act-empty">
      <i class="fa-solid fa-satellite-dish"></i>
      <div style="font-size:16px;font-weight:700;margin-bottom:8px;">No Activity Log</div>
      <p style="max-width:42ch;margin:0 auto;">No tracking data has been synced for this date yet. Open the mobile app and check in to start capturing activity.</p>
    </div>
  </div>

  {{-- Loading --}}
  <div class="act-card" id="actLoading">
    <div class="act-empty">
      <i class="fa-solid fa-spinner fa-spin" style="opacity:.5;"></i>
      <div>Loading activity data…</div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) { window.location.replace('/'); return; }

  let companyTz = localStorage.getItem('companyTz') || null;
  const API = (path, opts={}) => fetch(path, {
    ...opts,
    headers: { 'Authorization':'Bearer '+token, 'Accept':'application/json', ...(opts.headers||{}) }
  });

  /* ── helpers ── */
  function esc(v) {
    if (v===null||v===undefined||v==='') return '—';
    return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function fmtTime(ts) {
    if (!ts) return '—';
    try {
      const opts = {hour:'2-digit', minute:'2-digit', second:'2-digit'};
      if (companyTz) opts.timeZone = companyTz;
      return new Date(ts).toLocaleTimeString([], opts);
    } catch { return String(ts); }
  }
  function fmtDate(d) {
    if (!d) return '—';
    try {
      const opts = {year:'numeric', month:'short', day:'2-digit', weekday:'long'};
      if (companyTz) opts.timeZone = companyTz;
      return new Date(d+'T00:00:00').toLocaleDateString([], opts);
    } catch { return d; }
  }
  function toHrs(min) {
    if (!min || min <= 0) return '0h';
    const h = Math.floor(min/60), m = min%60;
    if (h===0) return `${m}m`;
    if (m===0) return `${h}h`;
    return `${h}h ${m}m`;
  }
  function toKm(meters) {
    if (!meters || meters===0) return '0m';
    if (meters < 1000) return `${meters}m`;
    return `${(meters/1000).toFixed(2)}km`;
  }
  function statBox(icon, bg, color, val, label) {
    return `<div class="act-stat">
      <div class="act-stat-icon" style="background:${bg};color:${color};">${icon}</div>
      <div class="act-stat-val">${val}</div>
      <div class="act-stat-label">${label}</div>
    </div>`;
  }

  /* ── State ── */
  const urlDate = new URLSearchParams(window.location.search).get('date');
  let currentDate = urlDate || new Date().toISOString().split('T')[0];
  let activityLog = null;

  /* ── Date picker init ── */
  const dateInput = document.getElementById('actDate');
  dateInput.value = currentDate;
  dateInput.addEventListener('change', () => { currentDate = dateInput.value; loadActivity(); });
  document.getElementById('actLoad').addEventListener('click', () => {
    currentDate = dateInput.value; loadActivity();
  });

  /* ── Load activity log ── */
  async function loadActivity() {
    document.getElementById('actLoading').style.display = '';
    document.getElementById('actContent').style.display = 'none';
    document.getElementById('actEmpty').style.display   = 'none';

    try {
      const res  = await API('/api/attendance/mobile/activity-log?date=' + currentDate);
      const json = await res.json();
      activityLog = json.data;
    } catch(e) {
      activityLog = null;
    }

    document.getElementById('actLoading').style.display = 'none';
    const isToday = currentDate === new Date().toISOString().split('T')[0];
    document.getElementById('liveBanner').style.display = isToday ? '' : 'none';

    if (!activityLog) {
      document.getElementById('actEmpty').style.display = '';
      document.getElementById('heroMeta').innerHTML =
        `<span class="act-badge ab-neutral"><i class="fa-solid fa-calendar-xmark me-1"></i>${fmtDate(currentDate)}</span>
         <span class="act-badge ab-amber"><i class="fa-solid fa-circle-exclamation me-1"></i>No data synced</span>`;
      return;
    }

    renderAll(activityLog);
    document.getElementById('actContent').style.display = 'grid';
  }

  /* ── Render everything ── */
  function renderAll(d) {
    // Sync info
    const syncAt = d.synced_at ? `Last synced: ${fmtTime(d.synced_at)}` : 'Not yet synced';
    document.getElementById('actSyncInfo').textContent = syncAt;

    // Hero meta
    const attendanceStatus = d.attendance_status
      ? `<span class="act-badge ab-green"><i class="fa-solid fa-circle-check me-1"></i>${esc(d.attendance_status).replace(/_/g,' ')}</span>` : '';
    document.getElementById('heroMeta').innerHTML = `
      <span class="act-badge ab-blue"><i class="fa-solid fa-calendar me-1"></i>${fmtDate(currentDate)}</span>
      ${attendanceStatus}
      ${d.check_in_time ? `<span class="act-badge ab-green"><i class="fa-solid fa-right-to-bracket me-1"></i>In: ${fmtTime(d.check_in_time)}</span>` : ''}
      ${d.check_out_time ? `<span class="act-badge ab-red"><i class="fa-solid fa-right-from-bracket me-1"></i>Out: ${fmtTime(d.check_out_time)}</span>` : ''}
    `;

    // Platform badge
    const platformIcons = {android:'fa-android', ios:'fa-apple', web:'fa-globe'};
    document.getElementById('platformBadge').innerHTML = d.platform
      ? `<i class="fa-brands ${platformIcons[d.platform]||'fa-mobile-screen'} me-1"></i>${esc(d.platform)}`
      : '';

    // ── Overview ──
    document.getElementById('overviewGrid').innerHTML = [
      statBox('<i class="fa-solid fa-clock-rotate-left"></i>','rgba(14,165,233,.12)','var(--primary-color)',
        d.session_start ? fmtTime(d.session_start):'—', 'Session Start'),
      statBox('<i class="fa-solid fa-clock"></i>','rgba(22,163,74,.12)','#16a34a',
        d.session_end ? fmtTime(d.session_end):'—', 'Session End'),
      statBox('<i class="fa-solid fa-location-dot"></i>','rgba(22,163,74,.12)','#16a34a',
        toKm(d.total_distance_meters), 'Total Distance'),
      statBox('<i class="fa-solid fa-person-walking"></i>','rgba(245,158,11,.12)','#d97706',
        d.movement_event_count||0, 'Movement Events'),
      statBox('<i class="fa-solid fa-battery-three-quarters"></i>','rgba(124,58,237,.12)','#7c3aed',
        d.battery_start_percent!==null ? `${d.battery_start_percent}%` : '—', 'Battery Start'),
      statBox('<i class="fa-solid fa-battery-half"></i>','rgba(220,38,38,.12)','#dc2626',
        d.battery_end_percent!==null ? `${d.battery_end_percent}%` : '—', 'Battery End'),
    ].join('');

    // ── GPS ──
    const isTraveling = d.is_traveling;
    document.getElementById('gpsBadges').innerHTML = isTraveling
      ? '<span class="act-badge ab-amber"><i class="fa-solid fa-car me-1"></i>Traveling Day</span>'
      : '<span class="act-badge ab-green"><i class="fa-solid fa-building me-1"></i>Stayed Local</span>';

    document.getElementById('gpsGrid').innerHTML = [
      statBox('<i class="fa-solid fa-satellite-dish"></i>','rgba(22,163,74,.12)','#16a34a',
        d.gps_connect_count||0, 'GPS Connected'),
      statBox('<i class="fa-solid fa-signal-slash"></i>','rgba(220,38,38,.12)','#dc2626',
        d.gps_disconnect_count||0, 'GPS Disconnected'),
      statBox('<i class="fa-solid fa-route"></i>','rgba(14,165,233,.12)','var(--primary-color)',
        toKm(d.total_distance_meters), 'Distance'),
      statBox('<i class="fa-solid fa-gauge-high"></i>','rgba(245,158,11,.12)','#d97706',
        d.max_speed_kmh ? `${parseFloat(d.max_speed_kmh).toFixed(1)} km/h` : '—', 'Max Speed'),
      statBox('<i class="fa-solid fa-gauge"></i>','rgba(37,99,235,.12)','#2563eb',
        d.avg_speed_kmh ? `${parseFloat(d.avg_speed_kmh).toFixed(1)} km/h` : '—', 'Avg Speed'),
      statBox('<i class="fa-solid fa-map-location-dot"></i>','rgba(124,58,237,.12)','#7c3aed',
        d.furthest_distance_from_office_meters ? toKm(d.furthest_distance_from_office_meters) : '—', 'Farthest from Office'),
      statBox('<i class="fa-solid fa-car-side"></i>','rgba(245,158,11,.12)','#d97706',
        toHrs(d.time_traveling_minutes), 'Time Traveling'),
      statBox('<i class="fa-solid fa-circle-stop"></i>','rgba(100,116,139,.12)','#475569',
        toHrs(d.time_stationary_minutes), 'Time Stationary'),
    ].join('');

    // GPS path table
    const path = d.gps_path || [];
    if (path.length) {
      document.getElementById('gpsPathSection').style.display = '';
      document.getElementById('gpsPathBody').innerHTML = path.map((p,i) => {
        const moving = p.is_moving || (p.spd_kmh > 0.5);
        return `<tr>
          <td class="text-muted">${i+1}</td>
          <td><strong>${fmtTime(p.ts)}</strong></td>
          <td>${p.lat ? p.lat.toFixed(6) : '—'}</td>
          <td>${p.lng ? p.lng.toFixed(6) : '—'}</td>
          <td>${p.acc ? `±${Math.round(p.acc)}m` : '—'}</td>
          <td>${p.spd_kmh != null ? parseFloat(p.spd_kmh).toFixed(1) : '—'}</td>
          <td><span class="move-dot ${moving?'moving':'still'}"></span> ${moving?'Moving':'Stationary'}</td>
        </tr>`;
      }).join('');
    }

    // ── WiFi ──
    document.getElementById('wifiGrid').innerHTML = [
      statBox('<i class="fa-solid fa-wifi"></i>','rgba(37,99,235,.12)','#2563eb',
        d.wifi_connect_count||0, 'Connected'),
      statBox('<i class="fa-solid fa-wifi" style="opacity:.4;"></i>','rgba(220,38,38,.12)','#dc2626',
        d.wifi_disconnect_count||0, 'Disconnected'),
      statBox('<i class="fa-solid fa-shuffle"></i>','rgba(245,158,11,.12)','#d97706',
        d.wifi_switch_count||0, 'Network Switches'),
      statBox('<i class="fa-solid fa-tower-broadcast"></i>','rgba(14,165,233,.12)','var(--primary-color)',
        (d.wifi_networks_seen||[]).length, 'Networks Seen'),
    ].join('');

    const nets = d.wifi_networks_seen || [];
    if (nets.length) {
      document.getElementById('wifiNetworksSection').style.display = '';
      document.getElementById('wifiNetworksList').innerHTML = nets.map(n => `
        <div class="wifi-item">
          <div class="wifi-icon-wrap" style="background:rgba(37,99,235,.1);color:#2563eb;">
            <i class="fa-solid fa-wifi"></i>
          </div>
          <div class="wifi-details">
            <h4>${esc(n.ssid||'Unknown SSID')}</h4>
            <div class="wifi-meta">
              ${n.bssid ? `BSSID: ${esc(n.bssid)} &nbsp;·&nbsp; ` : ''}
              ${n.connected_at ? `Connected: ${fmtTime(n.connected_at)}` : ''}
              ${n.disconnected_at ? ` &nbsp;·&nbsp; Disconnected: ${fmtTime(n.disconnected_at)}` : ''}
              ${n.duration_seconds ? ` &nbsp;·&nbsp; Duration: ${Math.round(n.duration_seconds/60)}m` : ''}
            </div>
          </div>
        </div>`).join('');
    }

    // ── Office ──
    document.getElementById('officeGrid').innerHTML = [
      statBox('<i class="fa-solid fa-building"></i>','rgba(22,163,74,.12)','#16a34a',
        toHrs(d.time_inside_office_minutes), 'Time Inside Office'),
      statBox('<i class="fa-solid fa-person-running"></i>','rgba(220,38,38,.12)','#dc2626',
        toHrs(d.time_outside_office_minutes), 'Time Outside Office'),
      statBox('<i class="fa-solid fa-door-open"></i>','rgba(14,165,233,.12)','var(--primary-color)',
        d.office_entry_count||0, 'Office Entries'),
      statBox('<i class="fa-solid fa-door-closed"></i>','rgba(245,158,11,.12)','#d97706',
        d.office_exit_count||0, 'Office Exits'),
    ].join('');

    // ── Movement ──
    document.getElementById('movementGrid').innerHTML = [
      statBox('<i class="fa-solid fa-person-walking"></i>','rgba(22,163,74,.12)','#16a34a',
        d.movement_event_count||0, 'Movement Events'),
      statBox('<i class="fa-solid fa-hourglass-end"></i>','rgba(245,158,11,.12)','#d97706',
        toHrs(d.idle_streak_max_minutes), 'Longest Idle'),
    ].join('');

    // ── App usage ──
    document.getElementById('appGrid').innerHTML = [
      statBox('<i class="fa-solid fa-eye"></i>','rgba(22,163,74,.12)','#16a34a',
        toHrs(d.app_foreground_minutes), 'App Foreground'),
      statBox('<i class="fa-solid fa-eye-slash"></i>','rgba(100,116,139,.12)','#475569',
        toHrs(d.app_background_minutes), 'App Background'),
      statBox('<i class="fa-solid fa-arrow-up-from-bracket"></i>','rgba(14,165,233,.12)','var(--primary-color)',
        d.app_foreground_count||0, 'Foreground Opens'),
      statBox('<i class="fa-solid fa-arrow-down-to-bracket"></i>','rgba(245,158,11,.12)','#d97706',
        d.app_background_count||0, 'Background Events'),
    ].join('');

    // ── Raw events ──
    const events = d.raw_events || [];
    if (events.length) {
      document.getElementById('eventsCard').style.display = '';
      document.getElementById('eventsCount').textContent = `${events.length} events`;
      const typeColors = {
        gps:'ev-gps', wifi:'ev-wifi', office:'ev-office',
        app:'ev-app', move:'ev-move', battery:'ev-battery'
      };
      document.getElementById('eventsList').innerHTML = events.map(ev => {
        const dotCls = typeColors[ev.type] || 'ev-other';
        return `<div class="event-item">
          <span class="event-dot ${dotCls}"></span>
          <span style="color:var(--muted-color);min-width:80px;font-family:monospace;">${fmtTime(ev.ts)}</span>
          <span style="font-weight:700;min-width:90px;">${esc(ev.type||'event')}</span>
          <span style="color:var(--muted-color);">${ev.msg ? esc(ev.msg) : JSON.stringify(ev.payload||{}).substring(0,120)}</span>
        </div>`;
      }).join('');
    }
  }

  /* ══════════════════════════════════════════════════════════
     BROWSER LIVE TRACKING (active when viewing today)
  ══════════════════════════════════════════════════════════ */
  const TRACKING = {
    session_start:            new Date().toISOString(),
    session_end:              null,
    platform:                 'web',
    gps_connect_count:        0,
    gps_disconnect_count:     0,
    gps_path:                 [],
    total_distance_meters:    0,
    time_traveling_minutes:   0,
    time_stationary_minutes:  0,
    max_speed_kmh:            0,
    avg_speed_kmh:            0,
    office_entry_count:       0,
    office_exit_count:        0,
    time_inside_office_minutes:  0,
    time_outside_office_minutes: 0,
    movement_event_count:     0,
    idle_streak_max_minutes:  0,
    app_foreground_count:     0,
    app_background_count:     0,
    app_foreground_minutes:   0,
    app_background_minutes:   0,
    raw_events:               [],
    _lastPos:                 null,
    _lastActivity:            Date.now(),
    _idleStart:               null,
    _fgStart:                 Date.now(),
    _bgStart:                 null,
    _watchId:                 null,
    _syncTimer:               null,
  };

  // ── Haversine distance ──
  function haversine(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const dLat = (lat2-lat1)*Math.PI/180;
    const dLng = (lng2-lng1)*Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  }

  function logEvent(type, msg, payload={}) {
    TRACKING.raw_events.push({ type, ts: new Date().toISOString(), msg, payload });
    if (TRACKING.raw_events.length > 500) TRACKING.raw_events.shift();
  }

  // ── GPS tracking ──
  function startGPSTracking() {
    if (!navigator.geolocation) return;
    TRACKING.gps_connect_count++;
    logEvent('gps','GPS connected');
    TRACKING._watchId = navigator.geolocation.watchPosition(
      pos => {
        const { latitude: lat, longitude: lng, accuracy: acc, speed } = pos.coords;
        const ts  = new Date().toISOString();
        const spdKmh = speed != null ? speed * 3.6 : 0;
        let isMoving = false;

        if (TRACKING._lastPos) {
          const dist = haversine(TRACKING._lastPos.lat, TRACKING._lastPos.lng, lat, lng);
          if (dist > 5) {  // >5m = movement
            TRACKING.total_distance_meters += dist;
            TRACKING.movement_event_count++;
            isMoving = true;
            TRACKING._lastActivity = Date.now();
            if (TRACKING._idleStart) {
              const idleMin = Math.round((Date.now()-TRACKING._idleStart)/60000);
              TRACKING.idle_streak_max_minutes = Math.max(TRACKING.idle_streak_max_minutes, idleMin);
              TRACKING._idleStart = null;
            }
            logEvent('move', `Moved ${Math.round(dist)}m`, {dist_m: Math.round(dist), spd: spdKmh.toFixed(1)});
          }
        } else {
          TRACKING._idleStart = Date.now();
        }
        if (spdKmh > TRACKING.max_speed_kmh) TRACKING.max_speed_kmh = spdKmh;

        TRACKING._lastPos = {lat, lng};
        TRACKING.gps_path.push({ lat, lng, acc: Math.round(acc), ts, spd_kmh: parseFloat(spdKmh.toFixed(1)), is_moving: isMoving });
        if (TRACKING.gps_path.length > 200) TRACKING.gps_path.shift(); // cap at 200 points in browser
        document.getElementById('liveStatus').textContent = `GPS: ${lat.toFixed(5)}, ${lng.toFixed(5)} · ${Math.round(dist||0)}m moved`;
      },
      err => {
        TRACKING.gps_disconnect_count++;
        logEvent('gps','GPS disconnected', {code: err.code});
        document.getElementById('liveStatus').textContent = 'GPS unavailable';
      },
      { enableHighAccuracy: true, timeout: 15000, maximumAge: 10000 }
    );
  }

  // ── Page visibility ──
  document.addEventListener('visibilitychange', () => {
    const now = Date.now();
    if (document.hidden) {
      TRACKING.app_background_count++;
      TRACKING.app_foreground_minutes += Math.round((now - (TRACKING._fgStart||now))/60000);
      TRACKING._bgStart = now;
      logEvent('app','App went to background');
    } else {
      TRACKING.app_foreground_count++;
      if (TRACKING._bgStart) {
        TRACKING.app_background_minutes += Math.round((now - TRACKING._bgStart)/60000);
      }
      TRACKING._fgStart = now;
      logEvent('app','App came to foreground');
    }
  });

  // ── Periodic tick (every 60s) ── updates time counters
  setInterval(() => {
    const now = Date.now();
    const idleMin = TRACKING._idleStart ? Math.round((now-TRACKING._idleStart)/60000) : 0;
    TRACKING.idle_streak_max_minutes = Math.max(TRACKING.idle_streak_max_minutes, idleMin);
    // Rough moving/stationary split
    const isMoving = (now - TRACKING._lastActivity) < 120000; // moved in last 2 min
    if (isMoving) TRACKING.time_traveling_minutes++;
    else { TRACKING.time_stationary_minutes++; if (!TRACKING._idleStart) TRACKING._idleStart=now; }
  }, 60000);

  // ── Sync to server ──
  async function syncTracking() {
    TRACKING.session_end = new Date().toISOString();
    const payload = {
      attendance_date:               currentDate,
      session_start:                 TRACKING.session_start,
      session_end:                   TRACKING.session_end,
      platform:                      'web',
      gps_connect_count:             TRACKING.gps_connect_count,
      gps_disconnect_count:          TRACKING.gps_disconnect_count,
      gps_path:                      TRACKING.gps_path,
      total_distance_meters:         Math.round(TRACKING.total_distance_meters),
      time_traveling_minutes:        TRACKING.time_traveling_minutes,
      time_stationary_minutes:       TRACKING.time_stationary_minutes,
      max_speed_kmh:                 parseFloat(TRACKING.max_speed_kmh.toFixed(1)),
      movement_event_count:          TRACKING.movement_event_count,
      idle_streak_max_minutes:       TRACKING.idle_streak_max_minutes,
      app_foreground_count:          TRACKING.app_foreground_count,
      app_background_count:          TRACKING.app_background_count,
      app_foreground_minutes:        TRACKING.app_foreground_minutes,
      app_background_minutes:        TRACKING.app_background_minutes,
      raw_events:                    TRACKING.raw_events,
      client_created_at:             TRACKING.session_start,
    };
    try {
      await fetch('/api/attendance/mobile/activity-log', {
        method: 'POST',
        headers: { 'Authorization':'Bearer '+token, 'Accept':'application/json', 'Content-Type':'application/json' },
        body: JSON.stringify(payload),
      });
      document.getElementById('actSyncInfo').textContent = 'Synced at ' + new Date().toLocaleTimeString();
    } catch {}
  }

  document.getElementById('syncNowBtn').addEventListener('click', () => {
    syncTracking().then(() => loadActivity());
  });

  // ── Init ──
  const isToday = currentDate === new Date().toISOString().split('T')[0];
  if (isToday) {
    startGPSTracking();
    // Auto-sync every 5 minutes
    TRACKING._syncTimer = setInterval(syncTracking, 5 * 60000);
    window.addEventListener('beforeunload', syncTracking);
  }

  loadActivity();
})();
</script>
@endpush
