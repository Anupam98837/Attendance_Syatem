@extends('pages.layout.structure')
@section('title', 'My Leaves')

@push('styles')
<style>
.leaves-wrap { display:grid; gap:22px; }

/* ── Hero ── */
.leaves-hero {
  position:relative; overflow:hidden;
  border:1px solid var(--line-strong); border-radius:32px; padding:30px;
  background:
    radial-gradient(circle at top right, rgba(124,58,237,.2), transparent 34%),
    linear-gradient(140deg, rgba(124,58,237,.1), rgba(14,165,233,.12));
  box-shadow:var(--shadow-2);
}
.leaves-kicker {
  display:inline-flex; align-items:center; gap:8px;
  padding:8px 13px; border-radius:999px;
  background:rgba(255,255,255,.78); border:1px solid rgba(124,58,237,.18);
  color:#7c3aed; font-size:12px; font-weight:800;
  text-transform:uppercase; letter-spacing:.08em;
}
.leaves-hero h1 { margin:12px 0 8px; font-size:clamp(1.8rem,3.5vw,2.6rem); letter-spacing:-.04em; }
.leaves-hero p  { margin:0; color:var(--muted-color); max-width:64ch; line-height:1.75; }

/* ── Tabs ── */
.leaves-tabs-nav {
  display:flex; gap:6px;
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:18px; padding:6px; box-shadow:var(--shadow-1);
}
.leaves-tab-btn {
  flex:1; display:flex; align-items:center; justify-content:center; gap:7px;
  padding:11px 14px; border-radius:12px;
  background:transparent; border:none; cursor:pointer;
  font-size:13px; font-weight:700; color:var(--muted-color); transition:all .18s ease;
}
.leaves-tab-btn:hover { background:var(--surface-2); color:var(--ink); }
.leaves-tab-btn.active {
  background:linear-gradient(135deg,#7c3aed,#6d28d9); color:#fff;
  box-shadow:0 3px 10px rgba(124,58,237,.3);
}
.leaves-tab-panel { display:none; }
.leaves-tab-panel.active { display:block; }

/* ── Panel card ── */
.leaves-card {
  background:var(--surface); border:1px solid var(--line-strong);
  border-radius:24px; box-shadow:var(--shadow-1); overflow:hidden;
}
.leaves-head {
  padding:18px 22px 14px; border-bottom:1px solid var(--line-soft);
  display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
}
.leaves-head h2 { margin:0; font-size:17px; }
.leaves-body { padding:22px; }

/* ── Table ── */
.leaves-table { width:100%; border-collapse:collapse; }
.leaves-table thead th {
  background:var(--surface-3); color:var(--ink);
  font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em;
  padding:10px 12px; white-space:nowrap; border-bottom:1px solid var(--line-strong);
}
.leaves-table tbody td {
  padding:12px 12px; border-top:1px solid var(--line-soft);
  font-size:13px; vertical-align:middle;
}
.leaves-table tbody tr:hover { background:var(--surface-2); }
.leaves-empty { text-align:center; padding:38px 16px; color:var(--muted-color); font-size:14px; }
.leaves-foot {
  display:flex; align-items:center; justify-content:space-between;
  padding:12px 18px; border-top:1px solid var(--line-soft); flex-wrap:wrap; gap:10px;
}
.leaves-foot .info { font-size:12px; color:var(--muted-color); }

/* ── Pills ── */
.pill {
  display:inline-flex; align-items:center; gap:4px;
  padding:4px 10px; border-radius:999px;
  font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
}
.pill-pending_approval { background:rgba(245,158,11,.13); color:#d97706; }
.pill-approved         { background:rgba(22,163,74,.12);  color:#16a34a; }
.pill-rejected         { background:rgba(220,38,38,.12);  color:#dc2626; }
.pill-default          { background:var(--surface-3);     color:var(--muted-color); }

/* ── Apply form ── */
.apply-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
@media(max-width:600px) { .apply-grid { grid-template-columns:1fr; } }
.apply-grid .full { grid-column:1/-1; }
.form-label-sm { font-size:12px; font-weight:700; color:var(--ink); margin-bottom:5px; display:block; }

/* ── Back link ── */
.back-link {
  display:inline-flex; align-items:center; gap:8px;
  font-size:13px; font-weight:700; color:var(--muted-color); text-decoration:none;
  transition:color .15s ease;
}
.back-link:hover { color:#7c3aed; }
</style>
@endpush

@section('content')
<div class="leaves-wrap anim-fade-in">

  {{-- Back link --}}
  <div>
    <a href="/dashboard" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

  {{-- Hero --}}
  <section class="leaves-hero">
    <div class="leaves-kicker"><i class="fa-solid fa-umbrella-beach"></i> Self Service</div>
    <h1>My Leaves</h1>
    <p>Apply for leave, track your requests, and see approval status — all in one place.</p>
  </section>

  {{-- Tabs --}}
  <nav class="leaves-tabs-nav">
    <button class="leaves-tab-btn active" data-tab="my-leaves">
      <i class="fa-solid fa-list-check"></i> My Requests
    </button>
    <button class="leaves-tab-btn" data-tab="apply">
      <i class="fa-solid fa-file-circle-plus"></i> Apply for Leave
    </button>
  </nav>

  {{-- MY LEAVES panel --}}
  <div class="leaves-tab-panel active" id="tab-my-leaves">
    <div class="leaves-card">
      <div class="leaves-head">
        <div>
          <h2><i class="fa-solid fa-list-check me-2" style="color:#7c3aed;"></i>Leave Requests</h2>
          <div class="small text-muted mt-1">All your submitted applications</div>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <select id="leaveStatusFilter" class="form-select form-select-sm" style="width:165px;border-radius:10px;">
            <option value="">All Statuses</option>
            <option value="pending_approval">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
          <button class="btn btn-sm btn-primary" id="leavesRefresh" style="border-radius:10px;">
            <i class="fa-solid fa-arrows-rotate me-1"></i>Refresh
          </button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="leaves-table">
          <thead>
            <tr>
              <th>Leave Type</th>
              <th>From</th>
              <th>To</th>
              <th>Days</th>
              <th>Status</th>
              <th>Applied On</th>
              <th>Reason</th>
              <th>HR Remarks</th>
            </tr>
          </thead>
          <tbody id="leavesTbody">
            <tr><td colspan="8" class="leaves-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading…</td></tr>
          </tbody>
        </table>
      </div>
      <div class="leaves-foot">
        <div class="info" id="leavesInfo">—</div>
        <ul class="pagination pagination-sm mb-0" id="leavesPager"></ul>
      </div>
    </div>
  </div>

  {{-- APPLY LEAVE panel --}}
  <div class="leaves-tab-panel" id="tab-apply">
    <div class="leaves-card">
      <div class="leaves-head">
        <h2><i class="fa-solid fa-file-circle-plus me-2" style="color:#7c3aed;"></i>Apply for Leave</h2>
      </div>
      <div class="leaves-body">
        <form id="applyForm" novalidate>
          <div class="apply-grid">

            <div>
              <label class="form-label-sm" for="leaveTypeId">Leave Type</label>
              <select id="leaveTypeId" class="form-select" style="border-radius:12px;">
                <option value="">Select leave type…</option>
              </select>
            </div>

            <div>
              <label class="form-label-sm" for="totalDays">
                Total Days <span id="totalDaysHint" class="small text-muted fw-normal"></span>
              </label>
              <input type="number" id="totalDays" class="form-control" min="0.5" step="0.5"
                placeholder="e.g. 1" style="border-radius:12px;">
            </div>

            <div>
              <label class="form-label-sm" for="leaveFrom">From Date <span class="text-danger">*</span></label>
              <input type="date" id="leaveFrom" class="form-control" style="border-radius:12px;" required>
            </div>

            <div>
              <label class="form-label-sm" for="leaveTo">To Date <span class="text-danger">*</span></label>
              <input type="date" id="leaveTo" class="form-control" style="border-radius:12px;" required>
            </div>

            <div class="full">
              <label class="form-label-sm" for="leaveReason">Reason <span class="text-danger">*</span></label>
              <textarea id="leaveReason" class="form-control" rows="3"
                placeholder="Briefly describe your reason for leave…"
                style="border-radius:12px;" required></textarea>
            </div>

          </div>

          {{-- Leave balance info --}}
          <div id="leaveBalanceInfo" class="mt-3" style="display:none;">
            <div class="d-flex flex-wrap gap-2" id="balanceChips"></div>
          </div>

          <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-primary px-4" style="border-radius:12px;background:#7c3aed;border-color:#7c3aed;" id="applySubmitBtn">
              <i class="fa-solid fa-paper-plane me-2"></i>Submit Request
            </button>
            <button type="button" class="btn btn-light px-4" style="border-radius:12px;" id="applyResetBtn">
              <i class="fa-solid fa-rotate-left me-2"></i>Reset
            </button>
          </div>

          <div id="applyMsg" class="mt-3" style="display:none;"></div>
        </form>
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

  const API = (path, opts={}) => fetch(path, {
    ...opts,
    headers: {
      'Authorization':'Bearer '+token, 'Accept':'application/json',
      ...(opts.body && !(opts.body instanceof FormData) ? {'Content-Type':'application/json'} : {}),
      ...(opts.headers||{})
    }
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

  const S = { leavesPage: 1 };

  function esc(v) {
    if (v===null||v===undefined||v==='') return '<span class="text-muted">—</span>';
    return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function monthLabel(y, m, d) {
    const dt = new Date(Date.UTC(Number(y), Number(m) - 1, Number(d), 12, 0, 0));
    return dt.toLocaleDateString([], { year:'numeric', month:'short', day:'2-digit', timeZone:'UTC' });
  }
  function pill(v) {
    if (!v) return '<span class="text-muted">—</span>';
    return `<span class="pill pill-${String(v).toLowerCase()}">${String(v).replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}</span>`;
  }
  function fmtDate(d) {
    if (!d) return '—';
    try {
      const raw = String(d).trim();
      const sql = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
      if (sql) return monthLabel(sql[1], sql[2], sql[3]);
      const opts = {year:'numeric', month:'short', day:'2-digit'};
      if (companyTz) opts.timeZone = companyTz;
      return new Date(d).toLocaleDateString([], opts);
    } catch { return d; }
  }

  /* ── Tabs ── */
  document.querySelectorAll('.leaves-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const tab = btn.dataset.tab;
      document.querySelectorAll('.leaves-tab-btn').forEach(b=>b.classList.toggle('active',b===btn));
      document.querySelectorAll('.leaves-tab-panel').forEach(p=>p.classList.toggle('active',p.id==='tab-'+tab));
      if (tab==='apply' && !window._leaveTypesLoaded) loadLeaveTypes();
    });
  });

  /* ── My leaves ── */
  async function loadLeaves() {
    document.getElementById('leavesTbody').innerHTML =
      `<tr><td colspan="8" class="leaves-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading…</td></tr>`;
    const p = new URLSearchParams({ page: S.leavesPage, per_page: 20 });
    const st = document.getElementById('leaveStatusFilter').value;
    if (st) p.set('status', st);
    try {
      const res  = await API('/api/attendance/mobile/leaves?' + p);
      const json = await res.json();
      if (!res.ok) { renderEmpty(json.message||'Failed to load'); return; }
      renderLeaves(json.data||[]);
      renderPager(json.pagination||{});
      document.getElementById('leavesInfo').textContent =
        `Showing ${(json.data||[]).length} of ${json.pagination?.total??'?'} requests`;
    } catch(e) { renderEmpty('Network error.'); }
  }

  function renderLeaves(rows) {
    const tb = document.getElementById('leavesTbody');
    if (!rows.length) { renderEmpty('No leave requests found.'); return; }
    tb.innerHTML = rows.map(r=>`<tr>
      <td>${r.leave_type_name ? `<strong>${esc(r.leave_type_name)}</strong>` : '<span class="text-muted">General</span>'}</td>
      <td>${fmtDate(r.from_date)}</td>
      <td>${fmtDate(r.to_date)}</td>
      <td><strong>${r.total_days??'—'}</strong></td>
      <td>${pill(r.status)}</td>
      <td>${fmtDate(r.applied_at||r.created_at)}</td>
      <td style="max-width:200px;word-break:break-word;">${esc(r.reason)}</td>
      <td>${esc(r.remarks)}</td>
    </tr>`).join('');
  }

  function renderEmpty(msg) {
    document.getElementById('leavesTbody').innerHTML =
      `<tr><td colspan="8" class="leaves-empty"><i class="fa-regular fa-folder-open me-2"></i>${msg}</td></tr>`;
  }

  function renderPager(pg) {
    const el = document.getElementById('leavesPager');
    if (!pg.last_page || pg.last_page<=1) { el.innerHTML=''; return; }
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

  document.getElementById('leavesRefresh').addEventListener('click', ()=>{ S.leavesPage=1; loadLeaves(); });
  document.getElementById('leaveStatusFilter').addEventListener('change', ()=>{ S.leavesPage=1; loadLeaves(); });
  document.getElementById('leavesPager').addEventListener('click', e=>{
    const btn=e.target.closest('[data-pg]');
    if (!btn) return;
    S.leavesPage=Number(btn.dataset.pg); loadLeaves();
  });

  /* ── Leave types ── */
  async function loadLeaveTypes() {
    window._leaveTypesLoaded = true;
    try {
      const res  = await API('/api/attendance/admin/leave-types?per_page=100');
      const json = await res.json();
      if (res.ok && json.data?.length) {
        const sel = document.getElementById('leaveTypeId');
        json.data.forEach(lt => {
          const opt = document.createElement('option');
          opt.value = lt.id;
          opt.textContent = lt.name + (lt.max_days_per_year ? ` (max ${lt.max_days_per_year} days/yr)` : '');
          opt.dataset.maxDays = lt.max_days_per_year || '';
          opt.dataset.desc    = lt.description || '';
          sel.appendChild(opt);
        });
      }
    } catch(e) { /* non-fatal */ }
  }

  /* ── Auto-calc days ── */
  ['leaveFrom','leaveTo'].forEach(id =>
    document.getElementById(id).addEventListener('change', autoCalc)
  );
  function autoCalc() {
    const from = document.getElementById('leaveFrom').value;
    const to   = document.getElementById('leaveTo').value;
    if (from && to) {
      const days = Math.round((new Date(to)-new Date(from))/86400000)+1;
      if (days > 0) {
        document.getElementById('totalDays').value = days;
        document.getElementById('totalDaysHint').textContent = `(${days} day${days!==1?'s':''})`;
      }
    }
  }

  /* ── Submit ── */
  document.getElementById('applyForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn    = document.getElementById('applySubmitBtn');
    const msg    = document.getElementById('applyMsg');
    const from   = document.getElementById('leaveFrom').value;
    const to     = document.getElementById('leaveTo').value;
    const reason = document.getElementById('leaveReason').value.trim();
    const ltId   = document.getElementById('leaveTypeId').value;
    const days   = document.getElementById('totalDays').value;

    if (!from || !to || !reason) {
      msg.style.display='block';
      msg.innerHTML=`<div class="alert alert-warning rounded-3 mb-0">Please fill in From Date, To Date, and Reason.</div>`;
      return;
    }
    if (new Date(to) < new Date(from)) {
      msg.style.display='block';
      msg.innerHTML=`<div class="alert alert-warning rounded-3 mb-0">To Date cannot be before From Date.</div>`;
      return;
    }

    btn.disabled=true;
    btn.innerHTML=`<i class="fa-solid fa-spinner fa-spin me-2"></i>Submitting…`;
    msg.style.display='none';

    const payload = { from_date:from, to_date:to, reason };
    if (ltId) payload.leave_type_id = parseInt(ltId);
    if (days) payload.total_days    = parseFloat(days);

    try {
      const res  = await API('/api/attendance/mobile/leaves',{ method:'POST', body:JSON.stringify(payload) });
      const json = await res.json();
      if (!res.ok || json.status==='error') {
        msg.style.display='block';
        msg.innerHTML=`<div class="alert alert-danger rounded-3 mb-0"><i class="fa-solid fa-circle-xmark me-2"></i>${json.message||'Submission failed.'}</div>`;
      } else {
        msg.style.display='block';
        msg.innerHTML=`<div class="alert alert-success rounded-3 mb-0"><i class="fa-solid fa-check-circle me-2"></i>Leave request submitted! It is pending HR approval.</div>`;
        document.getElementById('applyForm').reset();
        document.getElementById('totalDaysHint').textContent='';
        // Switch to my leaves tab and refresh
        document.querySelectorAll('.leaves-tab-btn').forEach(b=>b.classList.toggle('active',b.dataset.tab==='my-leaves'));
        document.querySelectorAll('.leaves-tab-panel').forEach(p=>p.classList.toggle('active',p.id==='tab-my-leaves'));
        S.leavesPage=1; loadLeaves();
      }
    } catch(e) {
      msg.style.display='block';
      msg.innerHTML=`<div class="alert alert-danger rounded-3 mb-0">Network error. Try again.</div>`;
    } finally {
      btn.disabled=false;
      btn.innerHTML=`<i class="fa-solid fa-paper-plane me-2"></i>Submit Request`;
    }
  });

  document.getElementById('applyResetBtn').addEventListener('click', ()=>{
    document.getElementById('applyForm').reset();
    document.getElementById('totalDaysHint').textContent='';
    document.getElementById('applyMsg').style.display='none';
  });

  /* ── Init ── */
  // Ensure timezone is resolved before rendering any dates
  ensureTimezone().then(() => {
    loadLeaves();
    loadLeaveTypes(); // load immediately so apply form is ready
  });
})();
</script>
@endpush
