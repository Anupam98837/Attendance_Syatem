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

  {{-- ═══ QUICK NAV CARDS ════════════════════════════════════════ --}}
  <div class="emp-nav-grid">
    <a href="/attendance/employee-history" class="emp-nav-card">
      <div class="emp-nav-card-icon" style="background:rgba(14,165,233,.12);color:var(--primary-color);">
        <i class="fa-solid fa-calendar-days"></i>
      </div>
      <div>
        <h3>Attendance History</h3>
        <p>View all past attendance records</p>
      </div>
      <i class="fa-solid fa-arrow-right arr"></i>
    </a>
    <a href="/attendance/employee-leaves" class="emp-nav-card">
      <div class="emp-nav-card-icon" style="background:rgba(124,58,237,.12);color:#7c3aed;">
        <i class="fa-solid fa-umbrella-beach"></i>
      </div>
      <div>
        <h3>Leaves</h3>
        <p>Apply for leave or check status</p>
      </div>
      <i class="fa-solid fa-arrow-right arr"></i>
    </a>
    <a href="/attendance/employee-activity" class="emp-nav-card">
      <div class="emp-nav-card-icon" style="background:rgba(20,184,166,.12);color:#0d9488;">
        <i class="fa-solid fa-satellite-dish"></i>
      </div>
      <div>
        <h3>Activity Log</h3>
        <p>GPS path, WiFi &amp; movement tracking</p>
      </div>
      <i class="fa-solid fa-arrow-right arr"></i>
    </a>
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

  function currentAuthToken() {
    return sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  }

  function hasActiveAuthToken() {
    return !!currentAuthToken();
  }

  const API = (path, opts = {}) => fetch(path, {
    ...opts,
    headers: {
      'Authorization': 'Bearer ' + currentAuthToken(),
      'Accept': 'application/json',
      ...(opts.body && !(opts.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
      ...(opts.headers || {}),
    },
  });

  const ACTIVITY_DEDUPE_STATE_KEY = 'employeeAttendanceActivityDedupeStateV3';
  const ACTIVITY_ISSUE_STATE_KEY = 'employeeAttendanceActivityIssueStateV1';

  function readActivityDedupeState() {
    try {
      const parsed = JSON.parse(localStorage.getItem(ACTIVITY_DEDUPE_STATE_KEY) || '{}');
      return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
    } catch (_) {
      return {};
    }
  }

  function writeActivityDedupeState(state) {
    const now = Date.now();
    const cleaned = {};
    Object.entries(state || {})
      .filter(([, value]) => Number(value) && now - Number(value) < IMPORTANT_ACTIVITY_DEDUPE_MS)
      .slice(-250)
      .forEach(([key, value]) => { cleaned[key] = Number(value); });
    localStorage.setItem(ACTIVITY_DEDUPE_STATE_KEY, JSON.stringify(cleaned));
    return cleaned;
  }

  function readActivityIssueState() {
    try {
      const parsed = JSON.parse(localStorage.getItem(ACTIVITY_ISSUE_STATE_KEY) || '{}');
      return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
    } catch (_) {
      return {};
    }
  }

  function writeActivityIssueState(state) {
    const now = Date.now();
    const cleaned = {};
    Object.entries(state || {})
      .filter(([, value]) => value && typeof value === 'object')
      .filter(([, value]) => !Number(value.logged_at) || now - Number(value.logged_at) < 24 * 60 * 60 * 1000)
      .slice(-250)
      .forEach(([key, value]) => { cleaned[key] = value; });
    localStorage.setItem(ACTIVITY_ISSUE_STATE_KEY, JSON.stringify(cleaned));
    return cleaned;
  }

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
    gpsWatchId: null,
    presenceTimer: null,
    sessionRefreshBusy: false,
    lastTrackSentAt: 0,
    lastTrackCoords: null,
    activityQueueTimer: null,
    activityFlushBusy: false,
    lastLoggedIp: null,
    lastLoggedGpsState: null,
    activityTrackingDisabled: false,
    lastImportantActivityAt: readActivityDedupeState(),
    activeImportantIssues: readActivityIssueState(),
    activitySessionKey: localStorage.getItem('employeeActivitySessionKey') || `sess_${Date.now()}_${Math.random().toString(36).slice(2, 8)}`,
  };
  localStorage.setItem('employeeActivitySessionKey', S.activitySessionKey);
  const ACTIVITY_QUEUE_KEY = 'employeeAttendanceActivityQueue';
  const ACTIVITY_FLUSH_DELAY_MS = 20000;
  const ACTIVITY_FLUSH_BATCH = 25;

  // Keep the activity log useful: only security / mismatch / recovery / punch-result events are stored.
  // Normal dashboard open, focus, heartbeat, GPS acquired, and movement pings are ignored.
  const IMPORTANT_ACTIVITY_TYPES = new Set([
    'gps_unavailable',
    'gps_available_again',
    'gps_geofence_mismatch',
    'gps_geofence_unverified',
    'gps_geofence_matched_again',
    'request_ip_missing',
    'request_ip_changed',
    'branch_wifi_ip_mismatch',
    'branch_wifi_ip_matched_again',
    'punch_blocked_geofence',
    'punch_blocked_network',
    'punch_failed',
    'punch_captured',
    'attendance_flagged_for_review',
  ]);

  // Stateful issues log once while they are active. When they recover, a single recovery log is stored
  // and the issue is cleared, so the same issue can be logged again only if it really happens again.
  const STATEFUL_ISSUE_ACTIVITY_TYPES = new Set([
    'gps_unavailable',
    'gps_geofence_mismatch',
    'gps_geofence_unverified',
    'request_ip_missing',
    'branch_wifi_ip_mismatch',
  ]);

  const RECOVERY_ACTIVITY_TYPES = new Set([
    'gps_available_again',
    'gps_geofence_matched_again',
    'branch_wifi_ip_matched_again',
  ]);

  // Used only for one-shot punch logs; stateful issue logs use active/resolved state instead.
  const IMPORTANT_ACTIVITY_DEDUPE_MS = 12 * 60 * 60 * 1000;

  function activeAttendanceId() {
    return S.activeSession?.id || S.todayAtt?.id || null;
  }

  function importantActivityMode(data = {}) {
    return data.required_mode || data.work_mode || S.workMode || 'office';
  }

  function issueStateKey(activity, data = {}, options = {}) {
    const att = options.attendanceId ?? data.attendance_id ?? activeAttendanceId() ?? 'no-attendance';
    const mode = importantActivityMode(data);
    if (activity === 'gps_unavailable' || activity === 'gps_available_again') return `gps:${att}:${mode}`;
    if (activity === 'gps_geofence_mismatch' || activity === 'gps_geofence_unverified' || activity === 'gps_geofence_matched_again') return `geofence:${att}:${mode}`;
    if (activity === 'request_ip_missing' || activity === 'branch_wifi_ip_mismatch' || activity === 'branch_wifi_ip_matched_again') return `network:${att}:${mode}`;
    return `${activity}:${att}:${mode}`;
  }

  function importantActivityDedupeKey(activity, data = {}, options = {}) {
    if (options.dedupeKey) return options.dedupeKey;
    const att = options.attendanceId ?? data.attendance_id ?? activeAttendanceId() ?? 'no-attendance';
    if (STATEFUL_ISSUE_ACTIVITY_TYPES.has(activity) || RECOVERY_ACTIVITY_TYPES.has(activity)) {
      return `${activity}:${issueStateKey(activity, data, options)}`;
    }
    if (activity === 'request_ip_changed') return `${activity}:${att}:${data.previous_ip || 'old'}>${data.current_ip || 'new'}`;
    if (activity.startsWith('punch_') || activity === 'attendance_flagged_for_review') return `${activity}:${att}:${data.punch_type || 'punch'}:${data.reason || ''}:${JSON.stringify(data.exceptions || [])}`;
    return `${activity}:${att}`;
  }

  function queueAlreadyHasActivityKey(key) {
    if (!key) return false;
    return getActivityQueue().some((item) => item?.dedupe_key === key || item?.data?.activity_dedupe_key === key);
  }

  function resolveRecoveryIssueKeys(activity, data = {}, options = {}) {
    const baseKey = issueStateKey(activity, data, options);
    const issues = { ...readActivityIssueState(), ...S.activeImportantIssues };
    const matched = [];

    if (activity === 'gps_available_again') {
      if (issues[baseKey]?.activity === 'gps_unavailable') matched.push(baseKey);
    } else if (activity === 'gps_geofence_matched_again') {
      if (['gps_geofence_mismatch', 'gps_geofence_unverified'].includes(issues[baseKey]?.activity)) matched.push(baseKey);
    } else if (activity === 'branch_wifi_ip_matched_again') {
      if (['request_ip_missing', 'branch_wifi_ip_mismatch'].includes(issues[baseKey]?.activity)) matched.push(baseKey);
    }

    return matched;
  }

  function shouldStoreImportantActivity(activity, data = {}, options = {}) {
    if (S.activityTrackingDisabled || !hasActiveAuthToken()) return false;
    if (!IMPORTANT_ACTIVITY_TYPES.has(activity)) return false;

    // Do not create two records for the same issue: GPS unavailable already explains why geofence cannot verify.
    if (activity === 'gps_geofence_unverified' && data.reason === 'gps_missing') return false;

    const now = Date.now();

    if (STATEFUL_ISSUE_ACTIVITY_TYPES.has(activity)) {
      const issueKey = issueStateKey(activity, data, options);
      const issueState = { ...readActivityIssueState(), ...S.activeImportantIssues };
      const existing = issueState[issueKey];
      if (existing?.activity === activity) return false;

      issueState[issueKey] = {
        activity,
        logged_at: now,
        attendance_id: options.attendanceId ?? data.attendance_id ?? activeAttendanceId() ?? null,
        work_mode: importantActivityMode(data),
        request_ip: data.current_ip || S.requestIp || null,
      };
      S.activeImportantIssues = writeActivityIssueState(issueState);
      options._resolvedDedupeKey = importantActivityDedupeKey(activity, data, options);
      if (queueAlreadyHasActivityKey(options._resolvedDedupeKey)) return false;
      return true;
    }

    if (RECOVERY_ACTIVITY_TYPES.has(activity)) {
      const matchedIssueKeys = resolveRecoveryIssueKeys(activity, data, options);
      if (!matchedIssueKeys.length) return false;

      const issueState = { ...readActivityIssueState(), ...S.activeImportantIssues };
      matchedIssueKeys.forEach((key) => { delete issueState[key]; });
      S.activeImportantIssues = writeActivityIssueState(issueState);
      options._resolvedDedupeKey = importantActivityDedupeKey(activity, data, options);
      if (queueAlreadyHasActivityKey(options._resolvedDedupeKey)) return false;
      return true;
    }

    const key = importantActivityDedupeKey(activity, data, options);
    options._resolvedDedupeKey = key;
    const state = { ...readActivityDedupeState(), ...S.lastImportantActivityAt };
    const last = Number(state[key] || 0);

    if (queueAlreadyHasActivityKey(key)) return false;
    if (last && now - last < IMPORTANT_ACTIVITY_DEDUPE_MS) return false;

    state[key] = now;
    S.lastImportantActivityAt = writeActivityDedupeState(state);
    return true;
  }

  function stopActivityDetectionAfterLogout() {
    if (S.activityTrackingDisabled) return;
    S.activityTrackingDisabled = true;
    if (S.activityQueueTimer) clearTimeout(S.activityQueueTimer);
    if (S.presenceTimer) clearInterval(S.presenceTimer);
    stopTrackingLoop();
    stopLocationWatch();
    stopCamera();
  }

  function guardActivityAuth() {
    if (hasActiveAuthToken() && !S.activityTrackingDisabled) return true;
    stopActivityDetectionAfterLogout();
    return false;
  }

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
    if (min === null || min === undefined || min === '') return '—';
    const minutes = Number(min);
    if (!Number.isFinite(minutes) || minutes <= 0) return '0h';
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
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
  function getActivityQueue() {
    try {
      const parsed = JSON.parse(localStorage.getItem(ACTIVITY_QUEUE_KEY) || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (_) {
      return [];
    }
  }
  function setActivityQueue(items) {
    localStorage.setItem(ACTIVITY_QUEUE_KEY, JSON.stringify(items));
  }
  function queueEmployeeActivity(activity, data = {}, options = {}) {
    if (!guardActivityAuth()) return false;
    if (!shouldStoreImportantActivity(activity, data, options)) return false;
    const item = {
      activity,
      title: options.title || '',
      description: options.description || '',
      attendance_id: options.attendanceId ?? activeAttendanceId(),
      occurred_at: currentIsoWithOffset(),
      source: options.source || 'employee_web_dashboard',
      local_queue_id: `act_${Date.now()}_${Math.random().toString(36).slice(2, 9)}`,
      dedupe_key: options._resolvedDedupeKey || importantActivityDedupeKey(activity, data, options),
      session_key: S.activitySessionKey,
      category: options.category || 'attendance_exception',
      severity: options.severity || 'warn',
      data: {
        ...data,
        page: 'employee_dashboard',
        work_mode: S.workMode || null,
        network_type: networkType(),
        request_ip: S.requestIp || null,
        location: coordLabel(S.gpsCoords?.lat, S.gpsCoords?.lng, S.gpsCoords?.acc),
      },
    };
    const queue = getActivityQueue();
    queue.push(item);
    setActivityQueue(queue.slice(-120));
    scheduleActivityFlush(options.immediate === true);
    return true;
  }
  function scheduleActivityFlush(immediate = false) {
    if (!guardActivityAuth()) return;
    if (S.activityQueueTimer) clearTimeout(S.activityQueueTimer);
    S.activityQueueTimer = window.setTimeout(() => flushActivityQueue(), immediate ? 100 : ACTIVITY_FLUSH_DELAY_MS);
  }
  async function flushActivityQueue(force = false) {
    if (!guardActivityAuth()) return;
    if (S.activityFlushBusy || !navigator.onLine) return;
    const queue = getActivityQueue();
    if (!queue.length) return;
    S.activityFlushBusy = true;
    try {
      const items = queue.slice(0, force ? queue.length : ACTIVITY_FLUSH_BATCH).map((item) => ({
        ...item,
        attendance_id: item.attendance_id ?? S.todayAtt?.id ?? S.activeSession?.id ?? null,
      }));
      const res = await API('/api/attendance/mobile/activity-log/sync', {
        method: 'POST',
        body: JSON.stringify({ items }),
      });
      const json = await res.json();
      if (res.ok && json.status === 'success') {
        setActivityQueue(queue.slice(items.length));
      }
    } catch (_) {
      // Keep queue local until a later flush succeeds.
    } finally {
      S.activityFlushBusy = false;
    }
  }
  async function refreshRequestIp() {
    if (!guardActivityAuth()) return;
    try {
      const res = await API('/api/attendance/mobile/request-ip');
      const json = await res.json();
      if (res.ok && json?.data?.request_ip) {
        const previousIp = S.requestIp;
        S.requestIp = json.data.request_ip;
        const hasActiveAttendance = !!(S.todayAtt?.check_in_time && !S.todayAtt?.check_out_time);
        if (hasActiveAttendance && previousIp && previousIp !== S.requestIp) {
          queueEmployeeActivity('request_ip_changed', {
            attendance_id: activeAttendanceId(),
            previous_ip: previousIp,
            current_ip: S.requestIp,
          }, { title: 'Request IP changed during attendance', severity: 'warn', source: 'employee_web_network', immediate: true });
        }
        S.lastLoggedIp = S.requestIp;
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
  async function batteryLevel() {
    if (!navigator.getBattery) return null;
    try {
      const battery = await navigator.getBattery();
      return Number.isFinite(battery?.level) ? Math.round(battery.level * 100) : null;
    } catch (error) {
      return null;
    }
  }
  function trackIntervalMs() {
    return Math.max(30000, Number(S.emp?.continuous_tracking_interval_seconds || 120) * 1000);
  }
  function shouldTrackNow() {
    const att = S.activeSession || S.todayAtt;
    return !!(att?.id && att?.check_in_time && !att?.check_out_time && toBool(S.emp?.continuous_tracking_enabled));
  }
  function shouldSendMovementPing(coords, force = false) {
    if (!shouldTrackNow() || !coords) return false;
    if (force || !S.lastTrackSentAt || !S.lastTrackCoords) return true;
    const elapsed = Date.now() - S.lastTrackSentAt;
    const moved = distanceMeters(S.lastTrackCoords.lat, S.lastTrackCoords.lng, coords.lat, coords.lng);
    return elapsed >= trackIntervalMs() || (moved !== null && moved >= 20);
  }
  function stopLocationWatch() {
    if (S.gpsWatchId !== null && navigator.geolocation?.clearWatch) {
      navigator.geolocation.clearWatch(S.gpsWatchId);
    }
    S.gpsWatchId = null;
  }
  function applyGpsPosition(position, { triggerTracking = false, forceTracking = false, source = 'employee_web_dashboard' } = {}) {
    if (!position?.coords) return;
    S.gpsCoords = {
      lat: position.coords.latitude,
      lng: position.coords.longitude,
      acc: position.coords.accuracy,
    };
    setGPS('acquired', `${S.gpsCoords.lat.toFixed(5)}, ${S.gpsCoords.lng.toFixed(5)} (±${Math.round(S.gpsCoords.acc)}m)`);
    evaluateGeofence();
    updatePunchBtn();
    if (triggerTracking && shouldSendMovementPing(S.gpsCoords, forceTracking)) {
      sendTrackingPing(S.gpsCoords, source, forceTracking);
    }
  }
  function evaluateGeofence() {
    const el = document.getElementById('geoHint');
    let logPayload = null;
    let logType = null;
    let logTitle = null;
    let logSeverity = 'warn';

    if (!S.emp) {
      S.geofenceStatus = { blocked: false, tone: 'warn', text: 'Checking branch geofence and work-mode rules…' };
    } else if (S.workMode !== 'office' || !toBool(S.emp.geofence_required)) {
      S.geofenceStatus = { blocked: false, tone: 'ok', text: `Geofence not required for ${S.workMode.toUpperCase()} mode.` };
    } else if (!S.gpsCoords) {
      S.geofenceStatus = { blocked: true, tone: 'warn', text: 'GPS is required to validate office geofence before punch.' };
      // Do not log a second “geofence unverified” row while GPS is unavailable/acquiring.
      // The single GPS unavailable record is enough and avoids duplicate rows.
    } else {
      const branchLat = S.emp.branch_latitude !== null && S.emp.branch_latitude !== undefined ? Number(S.emp.branch_latitude) : null;
      const branchLng = S.emp.branch_longitude !== null && S.emp.branch_longitude !== undefined ? Number(S.emp.branch_longitude) : null;
      const radius = S.emp.geofence_radius_meters !== null && S.emp.geofence_radius_meters !== undefined ? Number(S.emp.geofence_radius_meters) : null;
      if (!branchLat || !branchLng || !radius) {
        S.geofenceStatus = { blocked: false, tone: 'warn', text: 'Branch geofence is not configured completely. HR approval may be required.' };
        logType = 'gps_geofence_unverified';
        logTitle = 'Branch geofence not configured';
        logPayload = { attendance_id: activeAttendanceId(), reason: 'branch_geofence_not_configured' };
      } else {
        const dist = distanceMeters(branchLat, branchLng, S.gpsCoords.lat, S.gpsCoords.lng);
        const inside = dist !== null ? dist <= radius : false;
        const allowOutside = toBool(S.emp.outside_location_allowed) || toBool(S.emp.branch_allow_outside_geofence);
        const needsApproval = !inside && toBool(S.emp.outside_location_requires_approval);
        if (inside) {
          S.geofenceStatus = { blocked: false, tone: 'ok', text: `Inside geofence · approx ${Math.round(dist)}m from branch center.` };
          queueEmployeeActivity('gps_geofence_matched_again', {
            attendance_id: activeAttendanceId(),
            distance_meters: Math.round(dist || 0),
            radius_meters: radius,
            current_location: coordLabel(S.gpsCoords.lat, S.gpsCoords.lng, S.gpsCoords.acc),
          }, {
            title: 'GPS back inside office geofence',
            severity: 'info',
            source: 'employee_web_gps',
            immediate: true,
          });
        } else if (allowOutside) {
          S.geofenceStatus = { blocked: false, tone: needsApproval ? 'warn' : 'ok', text: `Outside geofence by approx ${Math.round(dist - radius)}m. ${needsApproval ? 'This may go to HR approval.' : 'Allowed by policy.'}` };
          if (needsApproval) {
            logType = 'gps_geofence_mismatch';
            logTitle = 'GPS outside geofence, approval required';
            logPayload = {
              attendance_id: activeAttendanceId(),
              distance_meters: Math.round(dist || 0),
              outside_by_meters: Math.max(0, Math.round((dist || 0) - radius)),
              radius_meters: radius,
              allowed_outside: true,
              requires_approval: true,
              current_location: coordLabel(S.gpsCoords.lat, S.gpsCoords.lng, S.gpsCoords.acc),
            };
          }
        } else {
          S.geofenceStatus = { blocked: true, tone: 'danger', text: `Outside office geofence by approx ${Math.round(dist - radius)}m. Switch to an allowed mode or move into branch area.` };
          logType = 'gps_geofence_mismatch';
          logTitle = 'GPS outside allowed office geofence';
          logSeverity = 'error';
          logPayload = {
            attendance_id: activeAttendanceId(),
            distance_meters: Math.round(dist || 0),
            outside_by_meters: Math.max(0, Math.round((dist || 0) - radius)),
            radius_meters: radius,
            allowed_outside: false,
            requires_approval: false,
            current_location: coordLabel(S.gpsCoords.lat, S.gpsCoords.lng, S.gpsCoords.acc),
          };
        }
      }
    }
    if (el) {
      el.className = `geo-hint ${S.geofenceStatus.tone}`;
      el.textContent = S.geofenceStatus.text;
    }
    if (logType && logPayload) {
      queueEmployeeActivity(logType, logPayload, {
        title: logTitle,
        severity: logSeverity,
        source: 'employee_web_gps',
        immediate: true,
      });
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
      queueEmployeeActivity('request_ip_missing', {
        attendance_id: activeAttendanceId(),
        required_mode: S.workMode,
        allowed_networks: patterns.map((row) => row.ip_pattern).filter(Boolean),
      }, { title: 'Request IP missing for office network validation', severity: 'warn', source: 'employee_web_network', immediate: true });
    } else if (requireIp && !matched) {
      const loopbackHint = ['127.0.0.1', '::1'].includes(String(S.requestIp || '').trim())
        ? ' Localhost testing detected, so add 127.0.0.1 or ::1 in branch allowed networks.'
        : '';
      S.networkStatus = { blocked: true, tone: 'danger', text: `Current IP ${S.requestIp || '—'} is not in the allowed branch network list.${loopbackHint}` };
      queueEmployeeActivity('branch_wifi_ip_mismatch', {
        attendance_id: activeAttendanceId(),
        current_ip: S.requestIp || null,
        network_type: networkType(),
        connection_type: navigator.connection?.type || null,
        effective_type: navigator.connection?.effectiveType || null,
        allowed_networks: patterns.map((row) => row.ip_pattern).filter(Boolean),
        reason: 'current_ip_not_in_allowed_branch_networks',
      }, { title: 'Wi-Fi/IP does not match branch network', severity: 'error', source: 'employee_web_network', immediate: true });
    } else if (matchedPattern) {
      S.networkStatus = { blocked: false, tone: 'ok', text: `Approved network matched · ${S.requestIp} → ${matchedPattern}` };
      queueEmployeeActivity('branch_wifi_ip_matched_again', {
        attendance_id: activeAttendanceId(),
        current_ip: S.requestIp || null,
        matched_pattern: matchedPattern,
        network_type: networkType(),
        required_mode: S.workMode,
      }, {
        title: 'Wi-Fi/IP verified again',
        severity: 'info',
        source: 'employee_web_network',
        immediate: true,
      });
    } else {
      S.networkStatus = { blocked: false, tone: 'ok', text: `Current network accepted${S.requestIp ? ` · IP ${S.requestIp}` : ''}` };
      queueEmployeeActivity('branch_wifi_ip_matched_again', {
        attendance_id: activeAttendanceId(),
        current_ip: S.requestIp || null,
        matched_pattern: null,
        network_type: networkType(),
        required_mode: S.workMode,
      }, {
        title: 'Network/IP accepted again',
        severity: 'info',
        source: 'employee_web_network',
        immediate: true,
      });
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
    if (status === 'failed') {
      queueEmployeeActivity('gps_unavailable', {
        attendance_id: activeAttendanceId(),
        gps_status: status,
        detail: text,
        gps_required: toBool(S.emp?.gps_required),
      }, {
        title: 'GPS unavailable',
        severity: 'warn',
        source: 'employee_web_gps',
        immediate: true,
      });
    } else if (status === 'acquired') {
      queueEmployeeActivity('gps_available_again', {
        attendance_id: activeAttendanceId(),
        gps_status: status,
        detail: text,
        current_location: coordLabel(S.gpsCoords?.lat, S.gpsCoords?.lng, S.gpsCoords?.acc),
      }, {
        title: 'GPS available again',
        severity: 'info',
        source: 'employee_web_gps',
        immediate: true,
      });
    }
    S.lastLoggedGpsState = status;
  }

  function startGPS() {
    if (!guardActivityAuth()) return;
    setGPS('acquiring','Acquiring GPS…');
    if (!navigator.geolocation) {
      setGPS('failed','GPS not supported');
      evaluateGeofence();
      updatePunchBtn();
      return;
    }
    navigator.geolocation.getCurrentPosition(
      (pos) => applyGpsPosition(pos),
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
      queueEmployeeActivity('dashboard_bootstrap_loaded', {
        employee_code: S.emp?.employee_code || null,
        branch_name: S.emp?.branch_name || null,
        shift_name: S.emp?.shift_name || null,
        tracking_enabled: toBool(S.emp?.continuous_tracking_enabled),
      }, { title: 'Dashboard bootstrap loaded', source: 'employee_web_dashboard', immediate: true });
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
    const previousMode = S.workMode;
    S.workMode = mode;
    document.querySelectorAll('.mode-btn').forEach((b) => b.classList.toggle('active', b.dataset.mode === mode));
    updateWfhNote();
    evaluateGeofence();
    evaluateNetworkRules();
    updatePunchBtn();
    if (previousMode && previousMode !== mode) {
      queueEmployeeActivity('work_mode_changed', {
        previous_mode: previousMode,
        current_mode: mode,
      }, { title: 'Work mode changed', source: 'employee_web_dashboard' });
    }
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
    if (S.sessionRefreshBusy) return;
    S.sessionRefreshBusy = true;
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
    } finally {
      S.sessionRefreshBusy = false;
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
    if (S.trackTimer || S.gpsWatchId !== null) {
      queueEmployeeActivity('tracking_stopped', {
        active_attendance_id: S.activeSession?.id || S.todayAtt?.id || null,
      }, { title: 'Tracking stopped', source: 'employee_web_tracking' });
    }
    S.trackTimer = null;
    S.trackIntervalSeconds = null;
    stopLocationWatch();
  }

  async function sendTrackingPing(preferredCoords = null, source = 'employee_web_dashboard', force = false) {
    const att = S.activeSession || S.todayAtt;
    if (S.trackingTickBusy || !navigator.onLine || !att?.id || !att?.check_in_time || att?.check_out_time) return;
    S.trackingTickBusy = true;
    try {
      let coords = preferredCoords;
      if (!coords && S.gpsCoords) coords = S.gpsCoords;
      if (!coords) {
        const position = await new Promise((resolve, reject) => {
          navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 10000, enableHighAccuracy: true });
        });
        coords = { lat: position.coords.latitude, lng: position.coords.longitude, acc: position.coords.accuracy };
      }
      if (!coords || (!force && !shouldSendMovementPing(coords, false))) return;
      const battery = await batteryLevel();
      const payload = {
        attendance_id: att.id,
        recorded_at: currentIsoWithOffset(),
        latitude: coords.lat,
        longitude: coords.lng,
        gps_accuracy_meters: Math.round(coords.acc || 0),
        battery_level: battery,
        network_type: networkType(),
        source,
        sync_status: 'synced',
      };
      const res = await API('/api/attendance/mobile/location-ping', { method:'POST', body:JSON.stringify(payload) });
      const json = await res.json();
      if (res.ok && json.data) {
        S.gpsCoords = coords;
        S.lastTrackSentAt = Date.now();
        S.lastTrackCoords = { lat: Number(coords.lat), lng: Number(coords.lng) };
        S.recentTracks = [json.data, ...S.recentTracks].slice(0, 20);
        if (S.todayAtt) S.todayAtt.latest_track = json.data;
        queueEmployeeActivity('movement_tracked', {
          attendance_id: att.id,
          source,
          location: coordLabel(coords.lat, coords.lng, coords.acc),
          network_type: networkType(),
        }, { title: 'Movement tracked', source: 'employee_web_tracking' });
        renderHero();
        renderSession();
      }
    } catch (error) {
      // Tracking failures stay non-blocking on the employee dashboard.
    } finally {
      S.trackingTickBusy = false;
    }
  }

  function startLocationWatch() {
    if (!shouldTrackNow() || S.gpsWatchId !== null || !navigator.geolocation?.watchPosition) return;
    S.gpsWatchId = navigator.geolocation.watchPosition(
      (position) => applyGpsPosition(position, { triggerTracking: true, source: 'employee_web_watch' }),
      () => {},
      { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
    );
  }

  function syncTrackingLoop() {
    if (!shouldTrackNow()) {
      stopTrackingLoop();
      return;
    }
    const intervalSeconds = Math.max(30, Number(S.emp?.continuous_tracking_interval_seconds || 120));
    startLocationWatch();
    if (S.trackTimer && S.trackIntervalSeconds === intervalSeconds) return;
    stopTrackingLoop();
    startLocationWatch();
    S.trackIntervalSeconds = intervalSeconds;
    queueEmployeeActivity('tracking_started', {
      interval_seconds: intervalSeconds,
      attendance_id: S.activeSession?.id || S.todayAtt?.id || null,
    }, { title: 'Tracking started', source: 'employee_web_tracking' });
    sendTrackingPing(S.gpsCoords, 'employee_web_interval', true);
    S.trackTimer = window.setInterval(sendTrackingPing, intervalSeconds * 1000);
  }

  async function refreshLiveSignals(reason = 'manual') {
    await refreshRequestIp();
    if (reason !== 'silent') {
      startGPS();
    }
    if (shouldTrackNow()) {
      await sendTrackingPing(S.gpsCoords, `employee_web_${reason}`, true);
    }
    await loadActiveSession(false);
  }

  function startPresenceLoop() {
    if (S.presenceTimer) clearInterval(S.presenceTimer);
    S.presenceTimer = window.setInterval(() => {
      if (document.hidden) return;
      refreshLiveSignals('heartbeat');
    }, 45000);
  }

  async function doPunch() {
    if (S.punchInProgress) return;
    const att = S.todayAtt;
    const punchType = (att?.check_in_time && !att?.check_out_time) ? 'check_out' : 'check_in';
    const needSelfie = selfieRequiredForCurrentPunch();
    queueEmployeeActivity('punch_attempted', {
      punch_type: punchType,
      selfie_required: needSelfie,
    }, { title: `${punchType === 'check_in' ? 'Check-in' : 'Check-out'} attempted`, source: 'employee_web_punch', immediate: true });

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
      queueEmployeeActivity('punch_blocked_geofence', {
        attendance_id: activeAttendanceId(),
        punch_type: punchType,
        reason: S.geofenceStatus.text || 'Geofence blocked punch',
        current_location: coordLabel(S.gpsCoords?.lat, S.gpsCoords?.lng, S.gpsCoords?.acc),
      }, { title: 'Punch blocked by geofence', severity: 'error', source: 'employee_web_punch', immediate: true });
      toast(S.geofenceStatus.text || 'You are outside the allowed geofence.', 'error');
      return;
    }
    if (S.networkStatus?.blocked) {
      queueEmployeeActivity('punch_blocked_network', {
        attendance_id: activeAttendanceId(),
        punch_type: punchType,
        reason: S.networkStatus.text || 'Network/IP policy blocked punch',
        current_ip: S.requestIp || null,
        network_type: networkType(),
      }, { title: 'Punch blocked by Wi-Fi/IP policy', severity: 'error', source: 'employee_web_punch', immediate: true });
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
        queueEmployeeActivity('punch_failed', {
          punch_type: punchType,
          reason: json.message || 'Punch failed',
        }, { title: 'Punch failed', severity: 'error', source: 'employee_web_punch', immediate: true });
        toast(json.message || 'Punch failed', 'error');
      } else {
        S.todayAtt = json.data?.attendance || S.todayAtt;
        clearSelfiePreview();
        renderHero();
        await loadActiveSession(false);
        queueEmployeeActivity('punch_captured', {
          punch_type: punchType,
          approval_status: json.data?.approval_status || null,
          exceptions: json.data?.exceptions || [],
        }, { title: 'Punch captured', source: 'employee_web_punch', immediate: true });
        toast(json.message || ((punchType === 'check_in' ? 'Checked in' : 'Checked out') + ' successfully'), 'success');
        if (json.data?.exceptions?.length) {
          queueEmployeeActivity('attendance_flagged_for_review', {
            punch_type: punchType,
            exceptions: json.data.exceptions,
          }, { title: 'Attendance flagged for review', severity: 'warn', source: 'employee_web_punch', immediate: true });
          toast(`⚠️ ${json.data.exceptions.join(', ')} — sent for HR review.`, 'info', 7000);
        }
        loadSummary(document.getElementById('statsMonth').value);
        loadSyncQueue();
        flushActivityQueue(true);
      }
    } catch (error) {
      queueEmployeeActivity('punch_failed', {
        punch_type: punchType,
        reason: 'Network error',
      }, { title: 'Punch failed', severity: 'error', source: 'employee_web_punch', immediate: true });
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

  document.addEventListener('click', (event) => {
    const logoutTrigger = event.target.closest('[data-logout], #logoutBtn, .logout-btn, .js-logout, a[href*="logout"], button[onclick*="logout"]');
    if (logoutTrigger) stopActivityDetectionAfterLogout();
  }, true);

  window.addEventListener('storage', (event) => {
    if (['token', 'role'].includes(event.key) && !hasActiveAuthToken()) {
      stopActivityDetectionAfterLogout();
    }
  });

  window.addEventListener('online', async () => {
    if (!guardActivityAuth()) return;
    queueEmployeeActivity('network_online', {}, { title: 'Internet connected', source: 'employee_web_network', immediate: true });
    await refreshLiveSignals('online');
    loadSyncQueue();
    flushActivityQueue(true);
  });
  window.addEventListener('offline', () => {
    if (!guardActivityAuth()) return;
    queueEmployeeActivity('network_offline', {}, { title: 'Internet disconnected', severity: 'warn', source: 'employee_web_network', immediate: true });
  });
  window.addEventListener('focus', () => {
    if (!guardActivityAuth()) return;
    refreshLiveSignals('focus');
  });
  window.addEventListener('pageshow', () => {
    if (!guardActivityAuth()) return;
    refreshLiveSignals('pageshow');
  });
  document.addEventListener('visibilitychange', () => {
    if (!guardActivityAuth()) return;
    queueEmployeeActivity(document.hidden ? 'dashboard_hidden' : 'dashboard_visible', {}, {
      title: document.hidden ? 'Dashboard hidden' : 'Dashboard visible',
      source: 'employee_web_dashboard',
    });
    if (!document.hidden) {
      refreshLiveSignals('visible');
      flushActivityQueue(true);
    }
  });
  navigator.connection?.addEventListener?.('change', () => {
    if (!guardActivityAuth()) return;
    refreshRequestIp();
    if (shouldTrackNow()) sendTrackingPing(S.gpsCoords, 'employee_web_network_change', true);
  });
  window.addEventListener('beforeunload', () => {
    if (S.presenceTimer) clearInterval(S.presenceTimer);
    stopTrackingLoop();
    stopLocationWatch();
    stopCamera();
  });

  ensureEmployeeRole().then((allowed) => {
    if (!allowed || !guardActivityAuth()) return;
    startGPS();
    loadBootstrap().then(async () => {
      await refreshRequestIp();
      loadSummary(statsMonthInput.value);
      loadActiveSession();
      loadSyncQueue();
      startPresenceLoop();
      flushActivityQueue(true);
    });
  });
})();
</script>
@endpush
