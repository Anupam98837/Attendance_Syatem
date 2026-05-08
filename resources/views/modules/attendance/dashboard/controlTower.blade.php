@extends('pages.layout.structure')

@section('title', 'Dashboard')

@push('styles')
<style>
.ctrl-wrap{display:grid;gap:22px}
.ctrl-hero{
  position:relative;
  overflow:hidden;
  border:1px solid var(--line-strong);
  border-radius:32px;
  padding:30px;
  background:
    radial-gradient(circle at top right, rgba(15,118,110,.18), transparent 30%),
    linear-gradient(140deg, rgba(15,118,110,.10), rgba(217,119,6,.12));
  box-shadow:var(--shadow-2);
}
.ctrl-hero::after{
  content:"";
  position:absolute;
  width:240px;
  height:240px;
  right:-70px;
  bottom:-90px;
  border-radius:999px;
  background:radial-gradient(circle, rgba(217,119,6,.2), transparent 68%);
}
.ctrl-hero-top{
  position:relative;
  z-index:1;
  display:grid;
  grid-template-columns:minmax(0, 1.4fr) minmax(280px, .9fr);
  gap:20px;
}
.ctrl-kicker{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:8px 13px;
  border-radius:999px;
  background:rgba(255,255,255,.74);
  border:1px solid rgba(15,118,110,.14);
  color:var(--primary-color);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.08em;
}
.ctrl-hero h1{
  margin:16px 0 10px;
  font-size:clamp(2.1rem, 4vw, 3.35rem);
  letter-spacing:-.05em;
}
.ctrl-hero p{
  margin:0;
  max-width:72ch;
  color:var(--muted-color);
  line-height:1.8;
}
.ctrl-chip-row{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  margin-top:18px;
}
.ctrl-chip{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 13px;
  border-radius:999px;
  background:rgba(255,255,255,.82);
  border:1px solid var(--line-soft);
  color:var(--ink);
  font-size:12px;
  font-weight:700;
}
.ctrl-side{
  position:relative;
  z-index:1;
  border:1px solid rgba(15,118,110,.14);
  background:rgba(255,255,255,.78);
  border-radius:24px;
  padding:18px;
  box-shadow:var(--shadow-1);
  backdrop-filter:blur(10px);
}
.ctrl-side h2{
  margin:0 0 10px;
  font-size:18px;
}
.ctrl-side-list{
  display:grid;
  gap:12px;
}
.ctrl-side-item{
  padding:12px 0;
  border-top:1px solid rgba(174,196,174,.55);
}
.ctrl-side-item:first-child{
  border-top:0;
  padding-top:0;
}
.ctrl-side-item strong{
  display:block;
  margin-bottom:4px;
  font-size:13px;
}
.ctrl-side-item span{
  display:block;
  color:var(--muted-color);
  font-size:13px;
  line-height:1.65;
}
.ctrl-metrics{
  display:grid;
  grid-template-columns:repeat(4, minmax(0,1fr));
  gap:16px;
}
.ctrl-metric{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  padding:20px;
  box-shadow:var(--shadow-1);
}
.ctrl-metric span{
  display:flex;
  align-items:center;
  gap:8px;
  color:var(--primary-color);
  font-size:12px;
  font-weight:800;
  letter-spacing:.08em;
  text-transform:uppercase;
  margin-bottom:14px;
}
.ctrl-metric strong{
  display:block;
  font-size:28px;
  color:var(--ink);
}
.ctrl-metric small{
  display:block;
  margin-top:8px;
  color:var(--muted-color);
  line-height:1.6;
}
.ctrl-grid{
  display:grid;
  grid-template-columns:repeat(3, minmax(0,1fr));
  gap:18px;
}
.ctrl-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:24px;
  padding:22px;
  box-shadow:var(--shadow-1);
  display:grid;
  gap:16px;
}
.ctrl-card-head{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:12px;
}
.ctrl-card h2{
  margin:0;
  font-size:19px;
}
.ctrl-card p{
  color:var(--muted-color);
  line-height:1.7;
}
.ctrl-link-list{
  display:grid;
  gap:10px;
}
.ctrl-link{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
  padding:12px 14px;
  border-radius:16px;
  border:1px solid var(--line-soft);
  background:var(--surface-2);
  color:var(--ink);
  font-size:13px;
  font-weight:700;
}
.ctrl-link span{
  color:var(--muted-color);
  font-weight:600;
}
.ctrl-employee{
  display:grid;
  grid-template-columns:repeat(2, minmax(0,1fr));
  gap:16px;
}
.ctrl-phone{
  border:1px solid var(--line-soft);
  border-radius:24px;
  padding:18px;
  background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,245,.95));
  box-shadow:var(--shadow-1);
}
.ctrl-phone h3{
  margin:0 0 8px;
  font-size:16px;
}
.ctrl-phone p{
  margin:0 0 12px;
  color:var(--muted-color);
}
.ctrl-phone-list{
  display:grid;
  gap:10px;
}
.ctrl-phone-list div{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
  padding:10px 12px;
  border-radius:14px;
  border:1px solid var(--line-soft);
  background:var(--surface);
  font-size:12px;
  color:var(--ink);
}
.ctrl-phone-list small{color:var(--muted-color)}
@media (max-width: 1199.98px){
  .ctrl-grid{grid-template-columns:1fr 1fr}
  .ctrl-metrics{grid-template-columns:repeat(2, minmax(0,1fr))}
}
@media (max-width: 991.98px){
  .ctrl-hero-top{grid-template-columns:1fr}
  .ctrl-grid{grid-template-columns:1fr}
  .ctrl-employee{grid-template-columns:1fr}
}
@media (max-width: 767.98px){
  .ctrl-metrics{grid-template-columns:1fr}
  .ctrl-hero{padding:22px}
}
</style>
@endpush

@section('content')
<div class="ctrl-wrap">
  <section class="ctrl-hero">
    <div class="ctrl-hero-top">
      <div>
        <span class="ctrl-kicker"><i class="fa-solid fa-clipboard-user"></i>Attendance Control Tower</span>
        <h1 id="dashboardGreeting">Welcome back</h1>
        <p id="dashboardLead">Loading the attendance workspace...</p>
        <div class="ctrl-chip-row" id="dashboardMeta">
          <span class="ctrl-chip"><i class="fa-solid fa-spinner fa-spin"></i>Checking account</span>
        </div>
      </div>

      <aside class="ctrl-side">
        <h2>System Direction</h2>
        <div class="ctrl-side-list">
          <div class="ctrl-side-item">
            <strong>Admin setup first</strong>
            <span>Company rules, branches, shifts, policies, holidays, and leave types define the attendance backbone.</span>
          </div>
          <div class="ctrl-side-item">
            <strong>HR operations second</strong>
            <span>Employees, live attendance, approvals, reports, and sync reviews keep the day moving.</span>
          </div>
          <div class="ctrl-side-item">
            <strong>Employee app follows backend rules</strong>
            <span>The mobile flow reads bootstrap data and then uses the same validation engine online or offline.</span>
          </div>
        </div>
      </aside>
    </div>
  </section>

  <section class="ctrl-metrics">
    <article class="ctrl-metric">
      <span><i class="fa-solid fa-gears"></i>Setup Layers</span>
      <strong>7</strong>
      <small>Company, branches, departments, designations, shifts, policies, and leave types.</small>
    </article>
    <article class="ctrl-metric">
      <span><i class="fa-solid fa-users"></i>Workforce Control</span>
      <strong>2-Level</strong>
      <small>Users plus attendance profiles for cleaner employee onboarding.</small>
    </article>
    <article class="ctrl-metric">
      <span><i class="fa-solid fa-cloud-arrow-up"></i>Hybrid Flow</span>
      <strong>Online + Offline</strong>
      <small>Queue, sync, approval, and audit paths are ready in the backend.</small>
    </article>
    <article class="ctrl-metric">
      <span><i class="fa-solid fa-route"></i>Employee Tracking</span>
      <strong>Start to End</strong>
      <small>Continuous location pings can run during an active attendance session.</small>
    </article>
  </section>

  <section class="ctrl-grid">
    <article class="ctrl-card">
      <div class="ctrl-card-head">
        <div>
          <h2>Admin Setup</h2>
          <p>The prerequisite layer that decides how attendance is supposed to behave before employees ever mark attendance.</p>
        </div>
        <span class="ctrl-chip"><i class="fa-solid fa-shield-halved"></i>Admin</span>
      </div>
      <div class="ctrl-link-list">
        <a class="ctrl-link" href="/attendance/company">Company Settings <span>working week, timezone, defaults</span></a>
        <a class="ctrl-link" href="/attendance/branches">Branches & Locations <span>geofence, Wi-Fi/IP, branch rules</span></a>
        <a class="ctrl-link" href="/attendance/shifts">Shifts <span>timing, grace, overtime, cross-day</span></a>
        <a class="ctrl-link" href="/attendance/policies">Attendance Policies <span>GPS, selfie, offline, device, work mode</span></a>
      </div>
    </article>

    <article class="ctrl-card">
      <div class="ctrl-card-head">
        <div>
          <h2>HR Workspace</h2>
          <p>The daily operations layer for monitoring, approvals, employee maintenance, and exception handling.</p>
        </div>
        <span class="ctrl-chip"><i class="fa-solid fa-user-tie"></i>HR</span>
      </div>
      <div class="ctrl-link-list">
        <a class="ctrl-link" href="/attendance/employees">Employees <span>branch, shift, policy, device profile</span></a>
        <a class="ctrl-link" href="/attendance/today">Today Attendance <span>live board, not marked, hybrid states</span></a>
        <a class="ctrl-link" href="/attendance/pending-approvals">Pending Approvals <span>offline, geofence, manual review</span></a>
        <a class="ctrl-link" href="/attendance/reports">Reports <span>daily, monthly, payroll, exceptions</span></a>
      </div>
    </article>

    <article class="ctrl-card">
      <div class="ctrl-card-head">
        <div>
          <h2>Mobile Conversion</h2>
          <p>The employee-side experience is laid out here so the same flow can be converted into the app with less guesswork.</p>
        </div>
        <span class="ctrl-chip"><i class="fa-solid fa-mobile-screen-button"></i>Employee</span>
      </div>
      <div class="ctrl-link-list">
        <a class="ctrl-link" href="/attendance/employee-mobile">Employee App Blueprint <span>dashboard, punch, queue, leave</span></a>
        <a class="ctrl-link" href="/attendance/offline-sync-logs">Offline Sync Logs <span>see how queued punches are reviewed</span></a>
        <a class="ctrl-link" href="/attendance/location-exceptions">Location Exceptions <span>GPS, geofence, Wi-Fi mismatch flow</span></a>
        <a class="ctrl-link" href="/attendance/leaves">Leave Management <span>employee apply, HR decide</span></a>
      </div>
    </article>
  </section>

  <section class="ctrl-card">
    <div class="ctrl-card-head">
      <div>
        <h2>Employee Screen Shape</h2>
        <p>This is the web-side visual plan for the mobile app you want to build next.</p>
      </div>
      <a class="btn btn-primary" href="/attendance/employee-mobile">Open Employee Blueprint</a>
    </div>

    <div class="ctrl-employee">
      <article class="ctrl-phone">
        <h3>Daily Attendance</h3>
        <p>The employee should see only the information needed to act confidently.</p>
        <div class="ctrl-phone-list">
          <div><b>Check In</b><small>GPS + selfie + branch rule</small></div>
          <div><b>Check Out</b><small>same session, same trust flow</small></div>
          <div><b>Sync Status</b><small>synced / pending / failed</small></div>
          <div><b>Shift Context</b><small>policy-driven</small></div>
        </div>
      </article>

      <article class="ctrl-phone">
        <h3>Hybrid Safety</h3>
        <p>The app should stay useful offline without becoming easy to abuse.</p>
        <div class="ctrl-phone-list">
          <div><b>Offline Queue</b><small>SQLite local items</small></div>
          <div><b>Local Queue ID</b><small>dedupe on server</small></div>
          <div><b>Delayed Sync</b><small>can move to approval</small></div>
          <div><b>Tracking Pings</b><small>during open attendance only</small></div>
        </div>
      </article>
    </div>
  </section>
</div>
@endsection

@section('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) {
    window.location.replace('/');
    return;
  }

  const greetingEl = document.getElementById('dashboardGreeting');
  const leadEl = document.getElementById('dashboardLead');
  const metaEl = document.getElementById('dashboardMeta');

  function roleLabel(role){
    const map = { admin: 'Admin', hr: 'HR', employee: 'Employee' };
    return map[String(role || '').toLowerCase()] || 'Employee';
  }

  function formatDate(){
    return new Intl.DateTimeFormat([], {
      weekday: 'short',
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(new Date());
  }

  fetch('/api/auth/check', {
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }
  })
    .then(async (response) => {
      const data = await response.json().catch(() => ({}));
      if (!response.ok || !data.user) {
        throw new Error(data.message || 'Session expired');
      }
      return data.user;
    })
    .then((user) => {
      const name = user.name || 'Team member';
      const role = String(user.role || 'employee').toLowerCase();
      greetingEl.textContent = `Welcome, ${name}`;
      leadEl.textContent = `You are signed in as ${roleLabel(role)}. This dashboard is now focused on the hybrid attendance system, with separate spaces for admin setup, HR operations, and employee app conversion.`;
      metaEl.innerHTML = `
        <span class="ctrl-chip"><i class="fa-solid fa-user-shield"></i>${roleLabel(role)}</span>
        <span class="ctrl-chip"><i class="fa-solid fa-signal"></i>${user.status || 'active'}</span>
        <span class="ctrl-chip"><i class="fa-solid fa-calendar-day"></i>${formatDate()}</span>
      `;
    })
    .catch(() => {
      sessionStorage.removeItem('token');
      sessionStorage.removeItem('role');
      localStorage.removeItem('token');
      localStorage.removeItem('role');
      window.location.replace('/');
    });
})();
</script>
@endsection
