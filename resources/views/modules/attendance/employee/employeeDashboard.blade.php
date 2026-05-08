@extends('pages.layout.structure')
@section('title', 'My Attendance')

@push('styles')
<style>
/* ================================================================
   Employee Dashboard  –  self-contained styles
   ================================================================ */
.emp-wrap { display:grid; gap:22px; }

/* ── Hero ──────────────────────────────────────────────────── */
.emp-hero {
  position:relative; overflow:hidden;
  border:1px solid var(--line-strong); border-radius:32px; padding:30px;
  background:
    radial-gradient(circle at top right, rgba(14,165,233,.22), transparent 34%),
    linear-gradient(140deg, rgba(14,165,233,.12), rgba(37,99,235,.14));
  box-shadow:var(--shadow-2);
}
.emp-hero::after {
  content:""; position:absolute;
  width:220px; height:220px; right:-60px; bottom:-80px; border-radius:999px;
  background:radial-gradient(circle, rgba(14,165,233,.24), transparent 68%);
}
.emp-hero-grid {
  position:relative; z-index:1;
  display:grid; grid-template-columns:minmax(0,1.4fr) minmax(280px,.95fr); gap:22px; align-items:start;
}
@media(max-width:900px){ .emp-hero-grid { grid-template-columns:1fr; } }

.emp-kicker {
  display:inline-flex; align-items:center; gap:8px;
  padding:8px 13px; border-radius:999px;
  background:rgba(255,255,255,.78); border:1px solid rgba(14,165,233,.18);
  color:var(--primary-color); font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.08em;
}
.emp-hero h1 { margin:14px 0 8px; font-size:clamp(1.8rem,3.5vw,2.8rem); letter-spacing:-.04em; }
.emp-hero p  { margin:0; color:var(--muted-color); max-width:64ch; line-height:1.75; }
.emp-chip-row { display:flex; flex-wrap:wrap; gap:8px; margin-top:16px; }
.emp-chip {
  display:inline-flex; align-items:center; gap:7px;
  padding:9px 12px; border-radius:999px;
  background:rgba(255,255,255,.85); border:1px solid var(--line-soft);
  color:var(--ink); font-size:12px; font-weight:700;
}
.emp-chip i { color:var(--primary-color); }

/* ── Status badge ───────────────────────────────────────────── */
.emp-status-badge {
  display:inline-flex; align-items:center; gap:7px;
  padding:6px 14px; border-radius:999px;
  font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
}
.status-checked-in  { background:rgba(22,163,74,.14);  color:#16a34a; border:1px solid rgba(22,163,74,.25); }
.status-checked-out { background:rgba(14,165,233,.13); color:var(--primary-color); border:1px solid rgba(14,165,233,.22); }
.status-not-marked  { background:rgba(245,158,11,.13); color:#d97706; border:1px solid rgba(245,158,11,.22); }

/* ── Side punch panel ───────────────────────────────────────── */
.emp-punch-panel {
  position:relative; z-index:1;
  background:rgba(255,255,255,.88);
  border:1px solid rgba(14,165,233,.18);
  border-radius:24px; padding:20px;
  box-shadow:var(--shadow-1); backdrop-filter:blur(10px);
}
.emp-punch-panel h2 { margin:0 0 14px; font-size:16px; }

/* ── Time row ────────────────────────────────────────────────── */
.emp-time-row { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px; }
.emp-time-box {
  background:var(--surface-2); border:1px solid var(--line-soft);
  border-radius:14px; padding:12px; text-align:center;
}
.emp-time-box .tb-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--muted-color); font-weight:700; }
.emp-time-box .tb-val   { font-size:20px; font-weight:800; color:var(--ink); font-family:var(--font-head); margin-top:3px; }
.emp-time-box .tb-val.empty { color:var(--muted-color); font-size:14px; }

/* ── Work mode selector ──────────────────────────────────────── */
.mode-section-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--muted-color); font-weight:700; margin-bottom:6px; }
.mode-grid { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; }
.mode-btn {
  flex:1; min-width:60px;
  padding:8px 4px; border-radius:12px;
  border:2px solid var(--line-soft); background:var(--surface-2);
  cursor:pointer; font-size:11px; font-weight:700; color:var(--muted-color);
  text-align:center; transition:all .15s ease;
  display:flex; flex-direction:column; align-items:center; gap:4px;
}
.mode-btn i { font-size:15px; }
.mode-btn.active { border-color:var(--primary-color); background:rgba(14,165,233,.1); color:var(--primary-color); }
.mode-btn:hover:not(.active):not(:disabled) { border-color:var(--line-medium); background:var(--surface); }
.mode-btn:disabled { opacity:.35; cursor:not-allowed; }
.mode-btn.not-allowed { opacity:.28; cursor:not-allowed; position:relative; }
.mode-btn.not-allowed::after {
  content:"\f05e"; font-family:"Font Awesome 6 Free"; font-weight:900;
  position:absolute; top:4px; right:4px; font-size:9px; color:#dc2626;
}

/* ── Policy info strip ───────────────────────────────────────── */
.policy-strip {
  display:flex; flex-wrap:wrap; gap:6px; margin-bottom:10px;
}
.policy-pill {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 9px; border-radius:999px;
  font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
}
.pp-ok      { background:rgba(22,163,74,.1);  color:#16a34a; border:1px solid rgba(22,163,74,.2); }
.pp-req     { background:rgba(220,38,38,.08); color:#dc2626; border:1px solid rgba(220,38,38,.18); }
.pp-warn    { background:rgba(245,158,11,.1); color:#d97706; border:1px solid rgba(245,158,11,.2); }
.pp-neutral { background:var(--surface-3); color:var(--muted-color); border:1px solid var(--line-soft); }

/* ── WFH note field ─────────────────────────────────────────── */
#wfhNoteWrap { display:none; }

/* ── GPS bar ────────────────────────────────────────────────── */
.gps-bar {
  display:flex; align-items:center; gap:8px;
  font-size:12px; color:var(--muted-color); margin-top:8px;
  padding:8px 11px; background:var(--surface-2);
  border:1px solid var(--line-soft); border-radius:10px;
}
.gps-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.gps-dot.acquiring { background:#f59e0b; animation:pulseRing 1.4s ease infinite; }
.gps-dot.acquired  { background:#16a34a; }
.gps-dot.failed    { background:#dc2626; }
.geo-hint,
.device-hint {
  margin-top:8px;
  padding:9px 11px;
  border-radius:10px;
  border:1px solid var(--line-soft);
  background:var(--surface-2);
  font-size:12px;
  color:var(--muted-color);
}
.geo-hint.ok,
.device-hint.ok { color:#15803d; border-color:rgba(22,163,74,.24); background:rgba(22,163,74,.08); }
.geo-hint.warn { color:#b45309; border-color:rgba(245,158,11,.24); background:rgba(245,158,11,.08); }
.geo-hint.danger,
.device-hint.danger { color:#b91c1c; border-color:rgba(220,38,38,.24); background:rgba(220,38,38,.08); }
.evidence-wrap{
  display:grid;
  gap:8px;
  margin-top:10px;
}
.selfie-box{
  border:1px solid var(--line-soft);
  border-radius:14px;
  background:var(--surface-2);
  padding:12px;
}
.selfie-preview{
  display:none;
  width:100%;
  max-height:180px;
  object-fit:cover;
  border-radius:12px;
  border:1px solid var(--line-soft);
  margin-top:10px;
}
.selfie-meta{
  font-size:11px;
  color:var(--muted-color);
}
.camera-stage{
  display:none;
  width:100%;
  max-height:220px;
  object-fit:cover;
  border-radius:12px;
  border:1px solid var(--line-soft);
  background:#0f172a;
}
.camera-actions{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  margin-top:10px;
}
.network-hint{
  margin-top:8px;
  padding:9px 11px;
  border-radius:10px;
  border:1px solid var(--line-soft);
  background:var(--surface-2);
  font-size:12px;
  color:var(--muted-color);
}
.network-hint.ok { color:#15803d; border-color:rgba(22,163,74,.24); background:rgba(22,163,74,.08); }
.network-hint.warn { color:#b45309; border-color:rgba(245,158,11,.24); background:rgba(245,158,11,.08); }
.network-hint.danger { color:#b91c1c; border-color:rgba(220,38,38,.24); background:rgba(220,38,38,.08); }

/* ── Punch button ────────────────────────────────────────────── */
#punchBtn {
  width:100%; padding:15px; margin-top:12px;
  font-size:16px; font-weight:800; border-radius:16px; border:none; cursor:pointer;
  transition:all .2s ease; letter-spacing:.02em;
  display:flex; align-items:center; justify-content:center; gap:10px;
}
#punchBtn.btn-checkin  { background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; box-shadow:0 4px 16px rgba(22,163,74,.35); }
#punchBtn.btn-checkout { background:linear-gradient(135deg,#dc2626,#b91c1c); color:#fff; box-shadow:0 4px 16px rgba(220,38,38,.35); }
#punchBtn.btn-done     { background:var(--surface-3); color:var(--muted-color); box-shadow:none; cursor:not-allowed; }
#punchBtn:disabled     { opacity:.65; cursor:not-allowed; }
#punchBtn:hover:not(:disabled) { filter:brightness(1.08); transform:translateY(-1px); }
.punch-spinner { display:inline-block; width:16px; height:16px; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:rot .7s linear infinite; }

/* ── Monthly stats ───────────────────────────────────────────── */
.emp-stats-card {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:24px; box-shadow:var(--shadow-1); padding:22px 24px;
}
.emp-stats-head {
  display:flex; align-items:center; justify-content:space-between;
  flex-wrap:wrap; gap:12px; margin-bottom:18px;
}
.emp-stats-head h2 { margin:0; font-size:17px; }
.emp-stats-grid {
  display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:12px;
}
.stat-box {
  background:var(--surface-2); border:1px solid var(--line-soft);
  border-radius:16px; padding:16px 12px; text-align:center;
  transition:box-shadow .18s ease;
}
.stat-box:hover { box-shadow:var(--shadow-2); }
.stat-icon {
  width:36px; height:36px; border-radius:11px;
  display:flex; align-items:center; justify-content:center;
  font-size:15px; margin:0 auto 10px;
}
.stat-val   { font-size:24px; font-weight:800; font-family:var(--font-head); color:var(--ink); line-height:1; }
.stat-label { font-size:10px; text-transform:uppercase; letter-spacing:.07em; color:var(--muted-color); font-weight:700; margin-top:4px; }

/* ── Nav cards ───────────────────────────────────────────────── */
.emp-nav-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; }
.emp-nav-card {
  display:flex; align-items:center; gap:16px;
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:20px; padding:20px; text-decoration:none; color:var(--ink);
  box-shadow:var(--shadow-1); transition:all .2s ease;
}
.emp-nav-card:hover { box-shadow:var(--shadow-2); transform:translateY(-2px); color:var(--ink); }
.emp-nav-card-icon {
  width:48px; height:48px; border-radius:14px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; font-size:20px;
}
.emp-nav-card h3 { margin:0 0 3px; font-size:15px; }
.emp-nav-card p  { margin:0; font-size:12px; color:var(--muted-color); }
.emp-nav-card .arr { margin-left:auto; color:var(--muted-color); font-size:13px; flex-shrink:0; }

/* ── Sync queue ──────────────────────────────────────────────── */
.emp-panel-card {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:24px; box-shadow:var(--shadow-1); overflow:hidden;
}
.emp-panel-head {
  padding:18px 22px 14px;
  border-bottom:1px solid var(--line-soft);
  display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
}
.emp-panel-head h2 { margin:0; font-size:17px; }
.emp-panel-body { padding:18px 22px; }
.sync-item {
  display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
  padding:13px 0; border-bottom:1px solid var(--line-soft);
}
.sync-item:last-child { border-bottom:none; }
.sync-qid { font-size:10px; color:var(--muted-color); font-family:monospace; word-break:break-all; max-width:150px; }
.emp-table-empty { text-align:center; padding:32px 16px; color:var(--muted-color); font-size:14px; }
.session-grid {
  display:grid;
  grid-template-columns:repeat(2, minmax(0,1fr));
  gap:12px;
}
.session-box {
  border:1px solid var(--line-soft);
  border-radius:16px;
  background:var(--surface-2);
  padding:14px;
}
.session-box strong {
  display:block;
  margin-bottom:4px;
  color:var(--muted-color);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.05em;
}
.session-box span {
  color:var(--ink);
  line-height:1.65;
  font-weight:700;
}
.track-list {
  display:grid;
  gap:10px;
  margin-top:16px;
}
.track-item {
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:12px;
  padding:12px 0;
  border-top:1px solid var(--line-soft);
}
.track-item:first-child {
  border-top:none;
  padding-top:0;
}
.track-item b {
  display:block;
  margin-bottom:2px;
}
.track-item small {
  display:block;
  color:var(--muted-color);
  line-height:1.55;
}

/* ── Pills ───────────────────────────────────────────────────── */
.pill {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 10px; border-radius:999px;
  font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
}
.pill-synced            { background:rgba(22,163,74,.12);  color:#16a34a; }
.pill-sync_failed       { background:rgba(220,38,38,.12);  color:#dc2626; }
.pill-pending_approval  { background:rgba(245,158,11,.13); color:#d97706; }
.pill-offline_pending   { background:rgba(245,158,11,.13); color:#d97706; }
.pill-processing        { background:rgba(14,165,233,.12); color:var(--primary-color); }
.pill-duplicate_rejected{ background:rgba(100,116,139,.12);color:#475569; }
.pill-default           { background:var(--surface-3);     color:var(--muted-color); }

/* ── Toast ───────────────────────────────────────────────────── */
#toastBox { position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:10px; }
.emp-toast {
  min-width:270px; max-width:380px; padding:13px 16px; border-radius:14px;
  box-shadow:var(--shadow-3); font-size:14px; font-weight:600;
  display:flex; align-items:flex-start; gap:10px;
  animation:fadeInUp .3s ease both;
}
.emp-toast.success { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; }
.emp-toast.error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
.emp-toast.info    { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; }
@media (max-width: 767.98px) {
  .session-grid { grid-template-columns:1fr; }
}
</style>
@endpush

@section('content')
<div class="emp-wrap anim-fade-in" id="empWrap">

  {{-- ═══ HERO ══════════════════════════════════════════════════ --}}
  <section class="emp-hero">
    <div class="emp-hero-grid">

      {{-- Left: profile info --}}
      <div>
        <div class="emp-kicker"><i class="fa-solid fa-user-check"></i> Employee Portal</div>
        <h1 id="heroName">Loading…</h1>
        <p id="heroStatus">Fetching your profile and today's attendance status.</p>
        <div class="emp-chip-row" id="heroChips">
          <span class="emp-chip"><i class="fa-solid fa-spinner fa-spin"></i> Initialising…</span>
        </div>
      </div>

      {{-- Right: punch panel --}}
      <div class="emp-punch-panel" id="punchPanel">
        <h2><i class="fa-regular fa-clock me-2 text-primary"></i>Today's Attendance</h2>

        {{-- Check-in / Check-out times --}}
        <div class="emp-time-row">
          <div class="emp-time-box">
            <div class="tb-label">Check-In</div>
            <div class="tb-val empty" id="heroCI">—</div>
          </div>
          <div class="emp-time-box">
            <div class="tb-label">Check-Out</div>
            <div class="tb-val empty" id="heroCO">—</div>
          </div>
        </div>

        {{-- Policy info strip --}}
        <div class="policy-strip" id="policyStrip" style="display:none;"></div>

        {{-- Work mode selector (auto-built from policy) --}}
        <div id="modeSection">
          <div class="mode-section-label">Work Mode</div>
          <div class="mode-grid" id="modeGrid">
            <span class="emp-chip"><i class="fa-solid fa-spinner fa-spin"></i></span>
          </div>
        </div>

        {{-- WFH note (shown when require_work_note_for_wfh is true and WFH is selected) --}}
        <div id="wfhNoteWrap" class="mb-2">
          <input type="text" id="wfhNote" class="form-control form-control-sm"
            placeholder="Work note required for WFH ✱" style="border-radius:10px;">
        </div>

        {{-- Remarks --}}
        <input type="text" id="punchRemarks" class="form-control form-control-sm mb-2"
          placeholder="Remarks (optional)" style="border-radius:10px;">

        <div class="evidence-wrap">
          <div class="mode-section-label">Selfie Evidence</div>
          <div class="selfie-box">
            <video id="cameraStage" class="camera-stage" playsinline autoplay muted></video>
            <canvas id="cameraCanvas" class="d-none"></canvas>
            <div class="camera-actions">
              <button type="button" class="btn btn-sm btn-outline-primary" id="cameraStartBtn">
                <i class="fa-solid fa-camera me-1"></i>Open Camera
              </button>
              <button type="button" class="btn btn-sm btn-primary" id="cameraCaptureBtn" disabled>
                <i class="fa-solid fa-camera-retro me-1"></i>Capture Selfie
              </button>
              <button type="button" class="btn btn-sm btn-light" id="cameraResetBtn" disabled>
                <i class="fa-solid fa-rotate-left me-1"></i>Retake
              </button>
            </div>
            <div class="selfie-meta mt-2" id="selfieMeta">Live camera capture only. File upload is disabled.</div>
            <img id="selfiePreview" class="selfie-preview" alt="Selfie preview">
          </div>
        </div>

        {{-- GPS bar --}}
        <div class="gps-bar">
          <span class="gps-dot acquiring" id="gpsDot"></span>
          <span id="gpsText">Acquiring GPS…</span>
        </div>
        <div class="geo-hint" id="geoHint">Checking branch geofence and work-mode rules…</div>
        <div class="network-hint" id="networkHint">Checking current IP and network policy…</div>
        <div class="device-hint" id="deviceHint">Preparing browser device identity…</div>

        {{-- Punch button --}}
        <button id="punchBtn" class="btn-checkin" disabled>
          <span class="punch-spinner" id="punchSpinner" style="display:none;"></span>
          <i id="punchIcon" class="fa-solid fa-right-to-bracket"></i>
          <span id="punchLabel">Check In</span>
        </button>
        <div class="text-center mt-2" style="font-size:11px;color:var(--muted-color);" id="punchMeta">—</div>
      </div>

    </div>
  </section>

  {{-- ═══ MONTHLY STATS ══════════════════════════════════════════ --}}
  <div class="emp-stats-card" id="statsCard" style="display:none;">
    <div class="emp-stats-head">
      <div>
        <h2><i class="fa-solid fa-chart-bar me-2 text-primary"></i>This Month
          <span id="statsMonthLabel" class="small text-muted fw-normal ms-1"></span>
        </h2>
      </div>
      <input type="month" id="statsMonth" class="form-control form-control-sm" style="width:155px;border-radius:10px;">
    </div>
    <div class="emp-stats-grid stagger-children" id="statsGrid">
      {{-- filled by JS --}}
    </div>
  </div>

  <div class="emp-panel-card" id="sessionCard" style="display:none;">
    <div class="emp-panel-head">
      <div>
        <h2><i class="fa-solid fa-location-crosshairs me-2 text-primary"></i>Live Session & Tracking</h2>
        <div class="small text-muted mt-1">See the latest punch time, approval state, location snapshot, and background tracking updates for this session.</div>
      </div>
      <button class="btn btn-sm btn-outline-primary" id="sessionRefresh" style="border-radius:10px;">
        <i class="fa-solid fa-arrows-rotate me-1"></i>Refresh
      </button>
    </div>
    <div class="emp-panel-body" id="sessionBody">
      <div class="emp-table-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading active session…</div>
    </div>
  </div>

  {{-- ═══ SYNC QUEUE ═════════════════════════════════════════════ --}}
  <div class="emp-panel-card">
    <div class="emp-panel-head">
      <div>
        <h2><i class="fa-solid fa-rotate me-2 text-primary"></i>Sync Queue</h2>
        <div class="small text-muted mt-1">Status of your attendance data sync with the server</div>
      </div>
      <button class="btn btn-sm btn-primary" id="syncRefresh" style="border-radius:10px;">
        <i class="fa-solid fa-arrows-rotate me-1"></i>Refresh
      </button>
    </div>
    <div class="emp-panel-body" id="syncBody">
      <div class="emp-table-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading…</div>
    </div>
  </div>

</div>

<div id="toastBox"></div>
@endsection

@push('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  let localRole = (sessionStorage.getItem('role') || localStorage.getItem('role') || '').toLowerCase();
  if (!token) { window.location.replace('/'); return; }

  const API = (path, opts = {}) => fetch(path, {
    ...opts,
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json',
      ...(opts.body && !(opts.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
      ...(opts.headers || {}),
    },
  });

  const S = {
    emp: null,
    todayAtt: null,
    activeSession: null,
    recentTracks: [],
    deviceId: null,
    requestIp: null,
    selfieFile: null,
    selfiePreviewUrl: null,
    cameraStream: null,
    gpsCoords: null,
    gpsStatus: 'acquiring',
    geofenceStatus: { blocked: false, tone: 'warn', text: 'Checking branch geofence and work-mode rules…' },
    networkStatus: { blocked: false, tone: 'warn', text: 'Checking current IP and network policy…' },
    workMode: 'office',
    requireWfhNote: false,
    punchInProgress: false,
    companyTz: localStorage.getItem('companyTz') || null,
    trackTimer: null,
    trackIntervalSeconds: null,
    trackingTickBusy: false,
  };

  async function ensureEmployeeRole() {
    if (localRole) {
      if (localRole !== 'employee') {
        window.location.replace('/attendance/today');
        return false;
      }
      return true;
    }

    try {
      const res = await API('/api/auth/me-role');
      const json = await res.json();
      const role = String(json?.role || '').toLowerCase();
      if (res.ok && role) {
        localRole = role;
        sessionStorage.setItem('role', role);
        localStorage.setItem('role', role);
      }
      if (role && role !== 'employee') {
        window.location.replace('/attendance/today');
        return false;
      }
      return role === 'employee';
    } catch (error) {
      window.location.replace('/');
      return false;
    }
  }

  function toast(msg, type = 'info', ms = 4500) {
    const icons = { success:'fa-check-circle', error:'fa-circle-xmark', info:'fa-circle-info' };
    const el = document.createElement('div');
    el.className = `emp-toast ${type}`;
    el.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i><div>${msg}</div>`;
    document.getElementById('toastBox').appendChild(el);
    setTimeout(() => {
      el.style.opacity = '0';
      el.style.transition = 'opacity .4s';
      setTimeout(() => el.remove(), 400);
    }, ms);
  }

  function toBool(v) { return v === true || v === 1 || v === '1' || String(v).toLowerCase() === 'true'; }
  function esc(v) {
    if (v === null || v === undefined || v === '') return '—';
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
  function formatSqlTime(value) {
    const m = String(value || '').trim().match(/^(\d{2}):(\d{2})(?::(\d{2}))?$/);
    if (!m) return null;
    return formatClockParts(m[1], m[2]);
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
  function fmtTime(value) {
    if (!value) return null;
    try {
      const parsed = parseServerDate(value);
      if (parsed?.kind === 'sql_datetime') return formatSqlDateTime(parsed.raw)?.time || value;
      if (parsed?.kind === 'sql_time') return formatSqlTime(parsed.raw) || value;
      const dt = parsed instanceof Date ? parsed : new Date(value);
      return dt.toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', hour12:true, timeZone: S.companyTz || undefined });
    } catch { return value; }
  }
  function fmtDate(value) {
    if (!value) return '—';
    try {
      const parsed = parseServerDate(value);
      if (parsed?.kind === 'sql_date') return formatSqlDate(parsed.raw) || value;
      if (parsed?.kind === 'sql_datetime') return formatSqlDateTime(parsed.raw)?.date || value;
      const dt = parsed instanceof Date ? parsed : new Date(value);
      return dt.toLocaleDateString([], { year:'numeric', month:'short', day:'2-digit', timeZone: S.companyTz || undefined });
    } catch { return value; }
  }
  function fmtDateTime(value) {
    if (!value) return '—';
    try {
      const parsed = parseServerDate(value);
      if (parsed?.kind === 'sql_datetime') {
        const sql = formatSqlDateTime(parsed.raw);
        return sql ? `${sql.date}, ${sql.time}` : value;
      }
      const dt = parsed instanceof Date ? parsed : new Date(value);
      return dt.toLocaleString([], {
        year:'numeric',
        month:'short',
        day:'2-digit',
        hour:'2-digit',
        minute:'2-digit',
        hour12:true,
        timeZone: S.companyTz || undefined,
      });
    } catch { return value; }
  }
  function toHrs(min) {
    if (!min || min <= 0) return '0h';
    const h = Math.floor(min / 60);
    const m = min % 60;
    if (h === 0) return `${m}m`;
    if (m === 0) return `${h}h`;
    return `${h}h ${m}m`;
  }
  function coordLabel(lat, lng, acc = null) {
    if (lat === null || lat === undefined || lng === null || lng === undefined) return null;
    const base = `${Number(lat).toFixed(5)}, ${Number(lng).toFixed(5)}`;
    return acc ? `${base} (±${Math.round(Number(acc))}m)` : base;
  }
  function pillHtml(v) {
    if (!v) return '<span class="text-muted">—</span>';
    const cls = `pill-${String(v).toLowerCase()}`;
    const lbl = String(v).replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
    return `<span class="pill ${cls}">${lbl}</span>`;
  }
  function networkType() {
    const raw = (navigator.connection?.type || navigator.connection?.effectiveType || '').toLowerCase();
    if (raw.includes('wifi')) return 'wifi';
    if (raw.includes('cell') || raw.includes('mobile') || raw.includes('2g') || raw.includes('3g') || raw.includes('4g') || raw.includes('5g')) return 'mobile';
    return raw || null;
  }
  function currentIsoWithOffset() {
    const d = new Date();
    const pad = (n) => String(Math.trunc(Math.abs(n))).padStart(2, '0');
    const offset = -d.getTimezoneOffset();
    const sign = offset >= 0 ? '+' : '-';
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}${sign}${pad(offset / 60)}:${pad(offset % 60)}`;
  }
  async function refreshRequestIp() {
    try {
      const res = await API('/api/attendance/mobile/request-ip');
      const json = await res.json();
      if (res.ok && json?.data?.request_ip) {
        S.requestIp = json.data.request_ip;
      }
    } catch (error) {
      // IP refresh stays non-blocking for the dashboard.
    } finally {
      evaluateNetworkRules();
    }
  }
  function stopCamera() {
    if (S.cameraStream) {
      S.cameraStream.getTracks().forEach((track) => track.stop());
      S.cameraStream = null;
    }
    const video = document.getElementById('cameraStage');
    if (video) {
      video.pause?.();
      video.srcObject = null;
      video.style.display = 'none';
    }
    const startBtn = document.getElementById('cameraStartBtn');
    const captureBtn = document.getElementById('cameraCaptureBtn');
    if (startBtn) startBtn.disabled = false;
    if (captureBtn) captureBtn.disabled = true;
  }
  async function startCamera() {
    const video = document.getElementById('cameraStage');
    const meta = document.getElementById('selfieMeta');
    if (!navigator.mediaDevices?.getUserMedia) {
      toast('This browser does not support direct camera capture.', 'error');
      if (meta) meta.textContent = 'Direct camera capture is not supported in this browser.';
      return;
    }
    try {
      stopCamera();
      const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
      S.cameraStream = stream;
      video.srcObject = stream;
      video.style.display = 'block';
      document.getElementById('cameraCaptureBtn').disabled = false;
      document.getElementById('cameraStartBtn').disabled = true;
      if (meta) meta.textContent = 'Camera ready. Capture a live selfie now.';
    } catch (error) {
      toast('Camera access is required for live selfie capture.', 'error');
      if (meta) meta.textContent = 'Could not open camera. Please allow camera access and try again.';
    }
  }
  function deviceStorageKey() {
    return `attendance_device_id_${S.emp?.user_id || 'guest'}`;
  }
  function ensureDeviceId() {
    const key = deviceStorageKey();
    let id = localStorage.getItem(key);
    if (!id) {
      id = `web-${(crypto?.randomUUID ? crypto.randomUUID() : `${Date.now()}-${Math.random().toString(16).slice(2, 10)}`)}`;
      localStorage.setItem(key, id);
    }
    S.deviceId = id;
    const el = document.getElementById('deviceHint');
    if (el) {
      el.className = 'device-hint ok';
      el.textContent = `Device ready: ${id}`;
    }
    return id;
  }
  function clearSelfiePreview() {
    const preview = document.getElementById('selfiePreview');
    const meta = document.getElementById('selfieMeta');
    if (S.selfiePreviewUrl) {
      URL.revokeObjectURL(S.selfiePreviewUrl);
      S.selfiePreviewUrl = null;
    }
    S.selfieFile = null;
    if (preview) {
      preview.src = '';
      preview.style.display = 'none';
    }
    if (meta) meta.textContent = 'Live camera capture only. File upload is disabled.';
    document.getElementById('cameraResetBtn').disabled = true;
    stopCamera();
  }
  function handleSelfieSelection(file) {
    const preview = document.getElementById('selfiePreview');
    const meta = document.getElementById('selfieMeta');
    if (S.selfiePreviewUrl) {
      URL.revokeObjectURL(S.selfiePreviewUrl);
      S.selfiePreviewUrl = null;
    }
    S.selfieFile = file || null;
    if (!file) {
      if (preview) {
        preview.src = '';
        preview.style.display = 'none';
      }
      if (meta) meta.textContent = 'Live camera capture only. File upload is disabled.';
      updatePunchBtn();
      return;
    }
    S.selfiePreviewUrl = URL.createObjectURL(file);
    if (preview) {
      preview.src = S.selfiePreviewUrl;
      preview.style.display = 'block';
    }
    if (meta) meta.textContent = `${file.name} · ${Math.max(1, Math.round(file.size / 1024))} KB`;
    document.getElementById('cameraResetBtn').disabled = false;
    stopCamera();
    updatePunchBtn();
  }
  async function captureSelfie() {
    const video = document.getElementById('cameraStage');
    const canvas = document.getElementById('cameraCanvas');
    if (!S.cameraStream || !video?.videoWidth || !video?.videoHeight) {
      toast('Camera is not ready yet. Please wait a moment and try again.', 'error');
      return;
    }
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.9));
    if (!blob) {
      toast('Could not capture selfie image.', 'error');
      return;
    }
    const file = new File([blob], `selfie-${Date.now()}.jpg`, { type: 'image/jpeg' });
    handleSelfieSelection(file);
  }
  function ipToLong(ip) {
    const parts = String(ip || '').trim().split('.').map(Number);
    if (parts.length !== 4 || parts.some((part) => Number.isNaN(part) || part < 0 || part > 255)) return null;
    return (((parts[0] << 24) >>> 0) + ((parts[1] << 16) >>> 0) + ((parts[2] << 8) >>> 0) + (parts[3] >>> 0)) >>> 0;
  }
  function ipMatchesPattern(ip, pattern) {
    const cleanIp = String(ip || '').trim();
    const cleanPattern = String(pattern || '').trim();
    if (!cleanIp || !cleanPattern) return false;
    if (!cleanPattern.includes('/')) return cleanIp === cleanPattern;
    const [subnet, prefixRaw] = cleanPattern.split('/', 2);
    const ipLong = ipToLong(cleanIp);
    const subnetLong = ipToLong(subnet);
    const prefix = Number(prefixRaw);
    if (ipLong === null || subnetLong === null || Number.isNaN(prefix) || prefix < 0 || prefix > 32) return false;
    const mask = prefix === 0 ? 0 : ((0xFFFFFFFF << (32 - prefix)) >>> 0);
    return (ipLong & mask) === (subnetLong & mask);
  }
  function distanceMeters(lat1, lng1, lat2, lng2) {
    if ([lat1, lng1, lat2, lng2].some((v) => v === null || v === undefined || Number.isNaN(Number(v)))) return null;
    const toRad = (deg) => deg * Math.PI / 180;
    const R = 6371000;
    const dLat = toRad(Number(lat2) - Number(lat1));
    const dLng = toRad(Number(lng2) - Number(lng1));
    const a = Math.sin(dLat / 2) ** 2
      + Math.cos(toRad(Number(lat1))) * Math.cos(toRad(Number(lat2))) * Math.sin(dLng / 2) ** 2;
    return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
  }
  function evaluateGeofence() {
    const el = document.getElementById('geoHint');
    if (!S.emp) {
      S.geofenceStatus = { blocked: false, tone: 'warn', text: 'Checking branch geofence and work-mode rules…' };
    } else if (S.workMode !== 'office' || !toBool(S.emp.geofence_required)) {
      S.geofenceStatus = { blocked: false, tone: 'ok', text: `Geofence not required for ${S.workMode.toUpperCase()} mode.` };
    } else if (!S.gpsCoords) {
      S.geofenceStatus = { blocked: true, tone: 'warn', text: 'GPS is required to validate office geofence before punch.' };
    } else {
      const branchLat = S.emp.branch_latitude !== null && S.emp.branch_latitude !== undefined ? Number(S.emp.branch_latitude) : null;
      const branchLng = S.emp.branch_longitude !== null && S.emp.branch_longitude !== undefined ? Number(S.emp.branch_longitude) : null;
      const radius = S.emp.geofence_radius_meters !== null && S.emp.geofence_radius_meters !== undefined ? Number(S.emp.geofence_radius_meters) : null;
      if (!branchLat || !branchLng || !radius) {
        S.geofenceStatus = { blocked: false, tone: 'warn', text: 'Branch geofence is not configured completely. HR approval may be required.' };
      } else {
        const dist = distanceMeters(branchLat, branchLng, S.gpsCoords.lat, S.gpsCoords.lng);
        const inside = dist !== null ? dist <= radius : false;
        const allowOutside = toBool(S.emp.outside_location_allowed) || toBool(S.emp.branch_allow_outside_geofence);
        const needsApproval = !inside && toBool(S.emp.outside_location_requires_approval);
        if (inside) {
          S.geofenceStatus = { blocked: false, tone: 'ok', text: `Inside geofence · approx ${Math.round(dist)}m from branch center.` };
        } else if (allowOutside) {
          S.geofenceStatus = { blocked: false, tone: needsApproval ? 'warn' : 'ok', text: `Outside geofence by approx ${Math.round(dist - radius)}m. ${needsApproval ? 'This may go to HR approval.' : 'Allowed by policy.'}` };
        } else {
          S.geofenceStatus = { blocked: true, tone: 'danger', text: `Outside office geofence by approx ${Math.round(dist - radius)}m. Switch to an allowed mode or move into branch area.` };
        }
      }
    }
    if (el) {
      el.className = `geo-hint ${S.geofenceStatus.tone}`;
      el.textContent = S.geofenceStatus.text;
    }
  }
  function selfieRequiredForCurrentPunch() {
    const att = S.todayAtt;
    const isCheckout = !!(att?.check_in_time && !att?.check_out_time);
    if (isCheckout) {
      return toBool(S.emp?.checkout_selfie_required) || toBool(S.emp?.selfie_required);
    }
    return toBool(S.emp?.selfie_required);
  }
  function evaluateNetworkRules() {
    const el = document.getElementById('networkHint');
    const patterns = Array.isArray(S.emp?.branch_networks) ? S.emp.branch_networks : [];
    const requireIp = S.workMode === 'office' && (toBool(S.emp?.wifi_ip_restriction_required) || toBool(S.emp?.branch_wifi_only));
    let matched = false;
    let matchedPattern = null;
    for (const row of patterns) {
      if (ipMatchesPattern(S.requestIp, row.ip_pattern)) {
        matched = true;
        matchedPattern = row.ip_pattern;
        break;
      }
    }

    if (!S.emp) {
      S.networkStatus = { blocked: false, tone: 'warn', text: 'Checking current IP and network policy…' };
    } else if (S.workMode !== 'office') {
      S.networkStatus = { blocked: false, tone: 'ok', text: `IP restriction not required for ${S.workMode.toUpperCase()} mode.` };
    } else if (requireIp && !S.requestIp) {
      S.networkStatus = { blocked: true, tone: 'warn', text: 'Current request IP could not be detected yet.' };
    } else if (requireIp && !matched) {
      const loopbackHint = ['127.0.0.1', '::1'].includes(String(S.requestIp || '').trim())
        ? ' Localhost testing detected, so add 127.0.0.1 or ::1 in branch allowed networks.'
        : '';
      S.networkStatus = { blocked: true, tone: 'danger', text: `Current IP ${S.requestIp || '—'} is not in the allowed branch network list.${loopbackHint}` };
    } else if (matchedPattern) {
      S.networkStatus = { blocked: false, tone: 'ok', text: `Approved network matched · ${S.requestIp} → ${matchedPattern}` };
    } else {
      S.networkStatus = { blocked: false, tone: 'ok', text: `Current network accepted${S.requestIp ? ` · IP ${S.requestIp}` : ''}` };
    }

    if (el) {
      el.className = `network-hint ${S.networkStatus.tone}`;
      el.textContent = S.networkStatus.text;
    }
  }
  function activeLocationLabel() {
    return S.todayAtt?.display_location
      || S.todayAtt?.latest_log?.location_text
      || coordLabel(S.todayAtt?.latest_log?.latitude, S.todayAtt?.latest_log?.longitude, S.todayAtt?.latest_log?.gps_accuracy_meters)
      || coordLabel(S.todayAtt?.latest_track?.latitude, S.todayAtt?.latest_track?.longitude, S.todayAtt?.latest_track?.gps_accuracy_meters);
  }

  function setGPS(status, text) {
    S.gpsStatus = status;
    document.getElementById('gpsDot').className = 'gps-dot ' + status;
    document.getElementById('gpsText').textContent = text;
  }

  function startGPS() {
    setGPS('acquiring','Acquiring GPS…');
    if (!navigator.geolocation) {
      setGPS('failed','GPS not supported');
      evaluateGeofence();
      updatePunchBtn();
      return;
    }
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        S.gpsCoords = { lat: pos.coords.latitude, lng: pos.coords.longitude, acc: pos.coords.accuracy };
        setGPS('acquired', `${S.gpsCoords.lat.toFixed(5)}, ${S.gpsCoords.lng.toFixed(5)} (±${Math.round(S.gpsCoords.acc)}m)`);
        evaluateGeofence();
        updatePunchBtn();
      },
      () => {
        S.gpsCoords = null;
        setGPS('failed','Could not get GPS — proceeding without location');
        evaluateGeofence();
        updatePunchBtn();
      },
      { timeout:12000, enableHighAccuracy:true }
    );
  }

  async function loadBootstrap() {
    try {
      const res = await API('/api/attendance/mobile/bootstrap');
      const json = await res.json();
      if (!res.ok) {
        toast(json.message || 'Could not load profile', 'error');
        return;
      }
      const tz = json.data?.company?.timezone;
      if (tz) {
        S.companyTz = tz;
        localStorage.setItem('companyTz', tz);
      } else if (!S.companyTz) {
        S.companyTz = Intl.DateTimeFormat().resolvedOptions().timeZone;
      }
      S.emp = json.data.employee;
      S.emp.branch_networks = json.data.branch_networks || [];
      S.todayAtt = json.data.today_attendance;
      S.requestIp = json.data.request_ip || null;
      ensureDeviceId();
      renderHero();
      buildPolicyStrip();
      buildModeGrid();
      evaluateGeofence();
      evaluateNetworkRules();
      updatePunchBtn();
    } catch (error) {
      toast('Network error loading profile', 'error');
    }
  }

  function renderHero() {
    const e = S.emp;
    if (!e) return;
    document.getElementById('heroName').textContent = e.name || e.user_name || 'Employee';

    const att = S.todayAtt;
    const ci = att?.check_in_time;
    const co = att?.check_out_time;
    const badgeCls = ci ? (co ? 'status-checked-out' : 'status-checked-in') : 'status-not-marked';
    const badgeLbl = ci
      ? (co
          ? '<i class="fa-solid fa-right-from-bracket me-1"></i>Checked Out'
          : '<i class="fa-solid fa-circle me-1" style="color:#16a34a;animation:pulseRing 1.4s ease infinite;"></i>Checked In')
      : '<i class="fa-regular fa-clock me-1"></i>Not Yet Marked';

    document.getElementById('heroStatus').innerHTML = `<span class="emp-status-badge ${badgeCls} me-2">${badgeLbl}</span>`;
    document.getElementById('heroChips').innerHTML = [
      { icon:'fa-solid fa-id-card', text: e.employee_code || 'N/A' },
      { icon:'fa-solid fa-building', text: e.branch_name || 'No Branch' },
      { icon:'fa-solid fa-business-time', text: e.shift_name || 'No Shift' },
      { icon:'fa-solid fa-sitemap', text: e.department_name || 'No Dept' },
      { icon:'fa-solid fa-shield', text: e.policy_name || 'Default Policy' },
    ].map((chip) => `<span class="emp-chip"><i class="${chip.icon}"></i>${chip.text}</span>`).join('');

    const ciEl = document.getElementById('heroCI');
    const coEl = document.getElementById('heroCO');
    ciEl.textContent = '—';
    ciEl.classList.add('empty');
    coEl.textContent = '—';
    coEl.classList.add('empty');
    if (ci) { ciEl.textContent = fmtTime(ci); ciEl.classList.remove('empty'); }
    if (co) { coEl.textContent = fmtTime(co); coEl.classList.remove('empty'); }

    const location = activeLocationLabel();
    const meta = document.getElementById('punchMeta');
    if (ci && co) {
      meta.textContent = `Total today: ${toHrs(att.total_working_minutes)}${location ? `  ·  Last location: ${location}` : ''}`;
    } else if (ci) {
      meta.textContent = `Checked in at ${fmtTime(ci)}${location ? `  ·  ${location}` : ''}`;
    } else {
      meta.textContent = `Shift: ${e.shift_name || '—'}  ·  Start: ${fmtTime(e.start_time) || '—'}`;
    }
  }

  function buildPolicyStrip() {
    const e = S.emp;
    if (!e) return;
    const strip = document.getElementById('policyStrip');
    const pills = [];

    pills.push(toBool(e.gps_required)
      ? { cls:'pp-req', icon:'fa-location-dot', label:'GPS Required' }
      : { cls:'pp-ok', icon:'fa-location-dot', label:'GPS Optional' });

    if (toBool(e.selfie_required)) pills.push({ cls:'pp-req', icon:'fa-camera', label:'Selfie Required' });
    if (toBool(e.device_binding_required)) pills.push({ cls:'pp-warn', icon:'fa-mobile-screen', label:'Device Bound' });
    if (toBool(e.geofence_required)) pills.push({ cls:'pp-warn', icon:'fa-draw-polygon', label:'Geofence Required' });
    if (toBool(e.multiple_punch_allowed)) pills.push({ cls:'pp-ok', icon:'fa-repeat', label:'Multi-Punch OK' });
    if (!toBool(e.offline_attendance_allowed)) pills.push({ cls:'pp-req', icon:'fa-wifi-slash', label:'Online Only' });
    else if (toBool(e.offline_attendance_enabled)) pills.push({ cls:'pp-neutral', icon:'fa-cloud-arrow-up', label:'Offline OK' });
    if (toBool(e.wifi_ip_restriction_required) || toBool(e.branch_wifi_only)) pills.push({ cls:'pp-warn', icon:'fa-wifi', label:'Wi-Fi Restricted' });
    if (toBool(e.continuous_tracking_enabled)) pills.push({ cls:'pp-neutral', icon:'fa-location-crosshairs', label:'Tracking On' });

    if (!pills.length) {
      strip.style.display = 'none';
      return;
    }
    strip.innerHTML = pills.map((p) => `<span class="policy-pill ${p.cls}"><i class="fa-solid ${p.icon}"></i>${p.label}</span>`).join('');
    strip.style.display = 'flex';
  }

  function buildModeGrid() {
    const e = S.emp;
    if (!e) return;
    const wfhAllowed = toBool(e.allow_wfh_attendance) && toBool(e.wfh_attendance_enabled);
    const fieldAllowed = toBool(e.allow_field_attendance) && toBool(e.field_attendance_enabled);
    const hybridAllowed = wfhAllowed || fieldAllowed;
    S.requireWfhNote = toBool(e.require_work_note_for_wfh);

    const modes = [
      { mode:'office', icon:'fa-solid fa-building', label:'Office', allowed:true },
      { mode:'wfh', icon:'fa-solid fa-house-laptop', label:'WFH', allowed:wfhAllowed },
      { mode:'field', icon:'fa-solid fa-location-dot', label:'Field', allowed:fieldAllowed },
      { mode:'hybrid', icon:'fa-solid fa-shuffle', label:'Hybrid', allowed:hybridAllowed },
    ];

    S.workMode = e.work_mode && modes.find((m) => m.mode === e.work_mode && m.allowed) ? e.work_mode : 'office';
    const grid = document.getElementById('modeGrid');
    grid.innerHTML = modes.map((m) => {
      let cls = 'mode-btn';
      if (!m.allowed) cls += ' not-allowed';
      if (m.mode === S.workMode) cls += ' active';
      const title = m.allowed ? m.label : `${m.label} (not allowed by policy)`;
      return `<button class="${cls}" data-mode="${m.mode}" ${!m.allowed ? `disabled title="${title}"` : `title="${title}"`}>
        <i class="${m.icon}"></i>${m.label}
      </button>`;
    }).join('');

    updateWfhNote();
    evaluateGeofence();
    evaluateNetworkRules();
  }

  function setWorkMode(mode) {
    S.workMode = mode;
    document.querySelectorAll('.mode-btn').forEach((b) => b.classList.toggle('active', b.dataset.mode === mode));
    updateWfhNote();
    evaluateGeofence();
    evaluateNetworkRules();
    updatePunchBtn();
  }

  function updateWfhNote() {
    document.getElementById('wfhNoteWrap').style.display = (S.workMode === 'wfh' && S.requireWfhNote) ? 'block' : 'none';
  }

  document.getElementById('modeGrid').addEventListener('click', (event) => {
    const btn = event.target.closest('.mode-btn');
    if (btn && !btn.disabled) setWorkMode(btn.dataset.mode);
  });
  document.getElementById('cameraStartBtn').addEventListener('click', startCamera);
  document.getElementById('cameraCaptureBtn').addEventListener('click', captureSelfie);
  document.getElementById('cameraResetBtn').addEventListener('click', () => {
    clearSelfiePreview();
    startCamera();
  });

  function updatePunchBtn() {
    const btn = document.getElementById('punchBtn');
    const icon = document.getElementById('punchIcon');
    const label = document.getElementById('punchLabel');
    const att = S.todayAtt;
    const ci = att?.check_in_time;
    const co = att?.check_out_time;
    const mustHaveGps = toBool(S.emp?.gps_required) && !S.gpsCoords;
    const mustHaveDevice = toBool(S.emp?.device_binding_required) && !S.deviceId;
    const mustHaveSelfie = selfieRequiredForCurrentPunch() && !S.selfieFile;
    const blockedByGeofence = !!S.geofenceStatus?.blocked;
    const blockedByNetwork = !!S.networkStatus?.blocked;

    if (ci && co) {
      btn.className = 'btn-done';
      btn.disabled = true;
      icon.className = 'fa-solid fa-check-double';
      label.textContent = 'Attendance Complete';
      return;
    }
    if (ci && !co) {
      btn.className = 'btn-checkout';
      btn.disabled = S.punchInProgress || S.gpsStatus === 'acquiring' || mustHaveGps || mustHaveDevice || mustHaveSelfie || blockedByGeofence || blockedByNetwork;
      icon.className = 'fa-solid fa-right-from-bracket';
      label.textContent = 'Check Out';
      return;
    }
    btn.className = 'btn-checkin';
    btn.disabled = S.punchInProgress || S.gpsStatus === 'acquiring' || mustHaveGps || mustHaveDevice || mustHaveSelfie || blockedByGeofence || blockedByNetwork;
    icon.className = 'fa-solid fa-right-to-bracket';
    label.textContent = 'Check In';
  }

  async function loadActiveSession(showLoader = true) {
    const body = document.getElementById('sessionBody');
    if (showLoader) {
      body.innerHTML = `<div class="emp-table-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading active session…</div>`;
    }
    try {
      const res = await API('/api/attendance/mobile/active-session');
      const json = await res.json();
      if (!res.ok) throw new Error(json.message || 'Could not load active session.');
      S.activeSession = json.data?.attendance || null;
      S.recentTracks = json.data?.tracks || [];
      if (S.activeSession) {
        S.todayAtt = S.activeSession;
        renderHero();
      }
      renderSession();
      syncTrackingLoop();
    } catch (error) {
      body.innerHTML = `<div class="emp-table-empty text-danger">${esc(error.message)}</div>`;
    }
  }

  function renderSession() {
    const card = document.getElementById('sessionCard');
    const body = document.getElementById('sessionBody');
    const att = S.activeSession || S.todayAtt;
    card.style.display = '';

    if (!att) {
      body.innerHTML = `<div class="emp-table-empty"><i class="fa-regular fa-clock me-2"></i>No attendance session has been created for today yet.</div>`;
      return;
    }

    const active = !!(att.check_in_time && !att.check_out_time);
    const lastTrack = S.recentTracks[0] || att.latest_track || null;
    const lastPunch = att.latest_log || null;
    const trackingEnabled = toBool(S.emp?.continuous_tracking_enabled);
    const lastTrackLoc = coordLabel(lastTrack?.latitude, lastTrack?.longitude, lastTrack?.gps_accuracy_meters) || 'No tracked location yet';
    const lastPunchLoc = activeLocationLabel() || 'Location not captured';

    body.innerHTML = `
      <div class="session-grid">
        <div class="session-box"><strong>Session State</strong><span>${active ? 'Active attendance session' : 'Attendance session closed'}</span></div>
        <div class="session-box"><strong>Approval State</strong><span>${pillHtml(att.approval_status || 'approved')}</span></div>
        <div class="session-box"><strong>Last Punch</strong><span>${lastPunch ? `${esc(lastPunch.punch_type || 'Punch')} at ${esc(fmtDateTime(lastPunch.punch_time))}` : 'No punch log found'}</span></div>
        <div class="session-box"><strong>Last Punch Location</strong><span>${esc(lastPunchLoc)}</span></div>
        <div class="session-box"><strong>Network & IP</strong><span>${esc(lastPunch?.network_type || '—')} · ${esc(lastPunch?.request_ip || '—')}</span></div>
        <div class="session-box"><strong>Tracking</strong><span>${trackingEnabled ? `Enabled · ${S.recentTracks.length} recent pings` : 'Disabled by policy'}</span></div>
      </div>
      <div class="track-list">
        <div class="track-item">
          <div>
            <b>Latest tracked point</b>
            <small>${esc(lastTrackLoc)}</small>
            <small>${lastTrack ? `Captured ${esc(fmtDateTime(lastTrack.recorded_at))}` : 'No live tracking points have reached the server yet.'}</small>
          </div>
          <div>${pillHtml(lastTrack?.sync_status || (active ? 'processing' : 'synced'))}</div>
        </div>
        ${lastPunch?.exception_reason ? `<div class="track-item"><div><b>Exception Flag</b><small>${esc(lastPunch.exception_reason)}</small></div><div>${pillHtml('pending_approval')}</div></div>` : ''}
      </div>
    `;
  }

  function stopTrackingLoop() {
    if (S.trackTimer) clearInterval(S.trackTimer);
    S.trackTimer = null;
    S.trackIntervalSeconds = null;
  }

  async function sendTrackingPing() {
    const att = S.activeSession || S.todayAtt;
    if (S.trackingTickBusy || !navigator.onLine || !att?.id || !att?.check_in_time || att?.check_out_time) return;
    S.trackingTickBusy = true;
    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 10000, enableHighAccuracy: true });
      });
      const payload = {
        attendance_id: att.id,
        recorded_at: currentIsoWithOffset(),
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        gps_accuracy_meters: Math.round(position.coords.accuracy || 0),
        network_type: networkType(),
        source: 'employee_web_dashboard',
        sync_status: 'synced',
      };
      const res = await API('/api/attendance/mobile/location-ping', { method:'POST', body:JSON.stringify(payload) });
      const json = await res.json();
      if (res.ok && json.data) {
        S.recentTracks = [json.data, ...S.recentTracks].slice(0, 20);
        renderSession();
      }
    } catch (error) {
      // Tracking failures stay non-blocking on the employee dashboard.
    } finally {
      S.trackingTickBusy = false;
    }
  }

  function syncTrackingLoop() {
    const att = S.activeSession || S.todayAtt;
    const shouldTrack = !!(att?.check_in_time && !att?.check_out_time && toBool(S.emp?.continuous_tracking_enabled));
    if (!shouldTrack) {
      stopTrackingLoop();
      return;
    }
    const intervalSeconds = Math.max(30, Number(S.emp?.continuous_tracking_interval_seconds || 120));
    if (S.trackTimer && S.trackIntervalSeconds === intervalSeconds) return;
    stopTrackingLoop();
    S.trackIntervalSeconds = intervalSeconds;
    sendTrackingPing();
    S.trackTimer = window.setInterval(sendTrackingPing, intervalSeconds * 1000);
  }

  async function doPunch() {
    if (S.punchInProgress) return;
    const att = S.todayAtt;
    const punchType = (att?.check_in_time && !att?.check_out_time) ? 'check_out' : 'check_in';
    const needSelfie = selfieRequiredForCurrentPunch();

    if (toBool(S.emp?.device_binding_required) && !S.deviceId) {
      toast('Device identity is required before attendance punch.', 'error');
      ensureDeviceId();
      updatePunchBtn();
      return;
    }

    if (S.workMode === 'wfh' && S.requireWfhNote) {
      const note = document.getElementById('wfhNote').value.trim();
      if (!note) {
        toast('A work note is required for WFH attendance.', 'error');
        return;
      }
    }
    if (needSelfie && !S.selfieFile) {
      toast(`${punchType === 'check_out' ? 'Check-out' : 'Check-in'} selfie is required by policy.`, 'error');
      startCamera();
      return;
    }
    if (S.geofenceStatus?.blocked) {
      toast(S.geofenceStatus.text || 'You are outside the allowed geofence.', 'error');
      return;
    }
    if (S.networkStatus?.blocked) {
      toast(S.networkStatus.text || 'Current network or IP does not satisfy policy.', 'error');
      return;
    }

    S.punchInProgress = true;
    document.getElementById('punchSpinner').style.display = 'inline-block';
    document.getElementById('punchIcon').style.display = 'none';
    document.getElementById('punchBtn').disabled = true;

    const payload = new FormData();
    payload.append('punch_type', punchType);
    payload.append('attendance_mode', 'online');
    payload.append('work_mode', S.workMode);
    payload.append('occurred_at', currentIsoWithOffset());
    payload.append('internet_status', navigator.onLine ? 'online' : 'offline');
    if (networkType()) payload.append('network_type', networkType());
    const baseRemarks = document.getElementById('punchRemarks').value.trim() || '';
    let remarks = baseRemarks;

    if (S.workMode === 'wfh' && S.requireWfhNote) {
      remarks = `${document.getElementById('wfhNote').value.trim()} ${remarks}`.trim();
    }
    if (remarks) payload.append('remarks', remarks);
    if (S.gpsCoords) {
      payload.append('latitude', String(S.gpsCoords.lat));
      payload.append('longitude', String(S.gpsCoords.lng));
      payload.append('gps_accuracy_meters', String(Math.round(S.gpsCoords.acc)));
      payload.append('location_text', coordLabel(S.gpsCoords.lat, S.gpsCoords.lng, S.gpsCoords.acc));
    }
    if (S.deviceId) payload.append('device_id', S.deviceId);
    payload.append('device_name', `Web Browser (${navigator.platform || 'unknown'})`);
    payload.append('device_platform', 'web');
    payload.append('device_model', navigator.userAgentData?.platform || navigator.platform || 'browser');
    payload.append('app_version', 'web-dashboard-1.0.0');
    if (S.selfieFile) payload.append('selfie', S.selfieFile);

    try {
      const res = await API('/api/attendance/mobile/punch', { method:'POST', body:payload });
      const json = await res.json();
      if (!res.ok || json.status === 'error') {
        toast(json.message || 'Punch failed', 'error');
      } else {
        S.todayAtt = json.data?.attendance || S.todayAtt;
        clearSelfiePreview();
        renderHero();
        await loadActiveSession(false);
        toast(json.message || ((punchType === 'check_in' ? 'Checked in' : 'Checked out') + ' successfully'), 'success');
        if (json.data?.exceptions?.length) {
          toast(`⚠️ ${json.data.exceptions.join(', ')} — sent for HR review.`, 'info', 7000);
        }
        loadSummary(document.getElementById('statsMonth').value);
        loadSyncQueue();
      }
    } catch (error) {
      toast('Network error. Try again.', 'error');
    } finally {
      S.punchInProgress = false;
      document.getElementById('punchSpinner').style.display = 'none';
      document.getElementById('punchIcon').style.display = 'inline-block';
      updatePunchBtn();
    }
  }

  document.getElementById('punchBtn').addEventListener('click', doPunch);

  async function loadSummary(month) {
    try {
      const res = await API('/api/attendance/mobile/summary?month=' + month);
      const json = await res.json();
      if (!res.ok || !json.data) return;

      const d = json.data;
      const att = d.attendance;
      document.getElementById('statsCard').style.display = '';
      document.getElementById('statsMonthLabel').textContent =
        `${month}${d.leaves ? `  ·  Leaves: ${d.leaves.pending} pending, ${d.leaves.approved} approved` : ''}`;

      const cards = [
        { icon:'fa-solid fa-user-check', bg:'rgba(22,163,74,.12)', color:'#16a34a', val:att.present_days, label:'Present' },
        { icon:'fa-solid fa-clock', bg:'rgba(245,158,11,.13)', color:'#d97706', val:att.late_days, label:'Late' },
        { icon:'fa-regular fa-calendar-xmark', bg:'rgba(220,38,38,.12)', color:'#dc2626', val:att.absent_days, label:'Absent' },
        { icon:'fa-solid fa-circle-half-stroke', bg:'rgba(14,165,233,.12)', color:'var(--primary-color)', val:att.half_days, label:'Half Day' },
        { icon:'fa-solid fa-umbrella-beach', bg:'rgba(124,58,237,.12)', color:'#7c3aed', val:att.leave_days, label:'On Leave' },
        { icon:'fa-solid fa-bolt', bg:'rgba(245,158,11,.13)', color:'#d97706', val:toHrs(att.total_overtime_minutes), label:'Overtime' },
        { icon:'fa-solid fa-briefcase', bg:'rgba(37,99,235,.12)', color:'#2563eb', val:toHrs(att.total_working_minutes), label:'Work Hrs' },
        { icon:'fa-solid fa-triangle-exclamation', bg:'rgba(220,38,38,.09)', color:'#dc2626', val:att.pending_approvals, label:'Pending Appr.' },
      ];

      document.getElementById('statsGrid').innerHTML = cards.map((c) => `
        <div class="stat-box anim-scale-in">
          <div class="stat-icon" style="background:${c.bg};color:${c.color};"><i class="${c.icon}"></i></div>
          <div class="stat-val">${c.val}</div>
          <div class="stat-label">${c.label}</div>
        </div>`).join('');
    } catch (error) {
      // Summary is non-blocking.
    }
  }

  const statsMonthInput = document.getElementById('statsMonth');
  statsMonthInput.value = new Date().toISOString().slice(0,7);
  statsMonthInput.addEventListener('change', () => loadSummary(statsMonthInput.value));

  async function loadSyncQueue() {
    const body = document.getElementById('syncBody');
    body.innerHTML = `<div class="emp-table-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading…</div>`;
    try {
      const res = await API('/api/attendance/mobile/sync-queue');
      const json = await res.json();
      const rows = json.data || [];
      if (!rows.length) {
        body.innerHTML = `<div class="emp-table-empty"><i class="fa-regular fa-circle-check me-2 text-success"></i>All punches synced. No pending items.</div>`;
        return;
      }
      body.innerHTML = rows.map((r) => {
        const pl = (() => { try { return JSON.parse(r.payload || '{}'); } catch { return {}; } })();
        const type = (pl.punch_type || r.queue_type || 'punch').replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase());
        return `<div class="sync-item">
          <div>
            <div class="fw-bold mb-1" style="font-size:13px;">${type}</div>
            <div class="sync-qid">${r.uuid}</div>
            <div class="mt-1" style="font-size:11px;color:var(--muted-color);">
              ${r.queued_at ? `Queued: ${fmtDateTime(r.queued_at)}` : ''}
              ${r.synced_at ? ` · Synced: ${fmtDateTime(r.synced_at)}` : ''}
              ${r.last_error ? `<br><span class="text-danger">Error: ${esc(r.last_error)}</span>` : ''}
            </div>
          </div>
          <div>${pillHtml(r.sync_status)}</div>
        </div>`;
      }).join('');
    } catch (error) {
      body.innerHTML = `<div class="emp-table-empty text-danger">Could not load sync queue.</div>`;
    }
  }

  document.getElementById('syncRefresh').addEventListener('click', loadSyncQueue);
  document.getElementById('sessionRefresh').addEventListener('click', () => loadActiveSession(true));
  window.addEventListener('online', async () => {
    await refreshRequestIp();
    loadSyncQueue();
    loadActiveSession(false);
  });
  window.addEventListener('beforeunload', () => { stopTrackingLoop(); stopCamera(); });

  ensureEmployeeRole().then((allowed) => {
    if (!allowed) return;
    startGPS();
    loadBootstrap().then(async () => {
      await refreshRequestIp();
      loadSummary(statsMonthInput.value);
      loadActiveSession();
      loadSyncQueue();
    });
  });
})();
</script>
@endpush
