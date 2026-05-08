@php
  $companyTimezones = [
    'Asia/Kolkata' => 'Asia/Kolkata (IST)',
    'Asia/Dhaka' => 'Asia/Dhaka (BST)',
    'Asia/Kathmandu' => 'Asia/Kathmandu (NPT)',
    'Asia/Dubai' => 'Asia/Dubai (GST)',
    'UTC' => 'UTC',
  ];

  $attendanceModes = [
    'online' => 'Online Only',
    'offline' => 'Offline Only',
    'online_offline_hybrid' => 'Online + Offline Hybrid',
  ];

  $currencies = [
    'INR' => 'INR - Indian Rupee',
    'BDT' => 'BDT - Bangladeshi Taka',
    'NPR' => 'NPR - Nepalese Rupee',
    'AED' => 'AED - UAE Dirham',
    'USD' => 'USD - US Dollar',
  ];

  $dateFormats = [
    'Y-m-d' => '2026-05-08',
    'd-m-Y' => '08-05-2026',
    'd/m/Y' => '08/05/2026',
    'm/d/Y' => '05/08/2026',
    'd M Y' => '08 May 2026',
  ];

  $timeFormats = [
    'H:i' => '24-hour (14:30)',
    'h:i A' => '12-hour (02:30 PM)',
  ];

  $dayOptions = [
    'mon' => 'Monday',
    'tue' => 'Tuesday',
    'wed' => 'Wednesday',
    'thu' => 'Thursday',
    'fri' => 'Friday',
    'sat' => 'Saturday',
    'sun' => 'Sunday',
  ];

  $offlineLimits = [
    6 => '6 hours',
    12 => '12 hours',
    24 => '24 hours',
    48 => '48 hours',
    72 => '72 hours',
  ];
@endphp

@push('styles')
<style>
.att-company{display:grid;gap:18px}
.att-company-head,
.att-company-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:24px;
  box-shadow:var(--shadow-1);
}
.att-company-head{
  padding:24px;
  background:linear-gradient(140deg, rgba(15,118,110,.08), rgba(217,119,6,.10));
}
.att-company-head h1{margin:0 0 8px;font-size:30px}
.att-company-head p{margin:0;color:var(--muted-color);line-height:1.75;max-width:76ch}
.att-company-card{padding:22px}
.att-company-grid{
  display:grid;
  grid-template-columns:repeat(2, minmax(0, 1fr));
  gap:16px;
}
.att-company-grid .full{grid-column:1 / -1}
.att-company-picker{
  display:flex;
  gap:10px;
  align-items:center;
}
.att-company-picker .form-control{
  background:var(--surface);
}
.att-company-meta{
  display:grid;
  grid-template-columns:repeat(3, minmax(0, 1fr));
  gap:14px;
}
.att-company-meta article{
  border:1px solid var(--line-soft);
  background:var(--surface-2);
  border-radius:18px;
  padding:16px;
}
.att-company-meta span{
  display:block;
  color:var(--muted-color);
  font-size:12px;
  text-transform:uppercase;
  letter-spacing:.06em;
  margin-bottom:8px;
}
.att-company-meta strong{font-size:20px;color:var(--ink)}
.att-day-grid{
  display:grid;
  grid-template-columns:repeat(2, minmax(0,1fr));
  gap:12px;
}
.att-day-option{
  border:1px solid var(--line-soft);
  border-radius:14px;
  padding:12px 14px;
  background:var(--surface-2);
}
@media (max-width: 991.98px){
  .att-company-meta{grid-template-columns:1fr}
}
@media (max-width: 767.98px){
  .att-company-grid,
  .att-day-grid{grid-template-columns:1fr}
  .att-company-picker{flex-direction:column;align-items:stretch}
}
</style>
@endpush

<div class="att-company">
  <section class="att-company-head">
    <span class="att-inline-badge"><i class="fa-solid fa-building"></i>Company Control</span>
    <h1>Company Attendance Settings</h1>
    <p>Define the global attendance defaults that every branch, shift, policy, and employee eventually inherits from. This is the first setup screen for the system.</p>
  </section>

  <section class="att-company-card">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <h2 class="mb-1">Global Configuration</h2>
        <div class="text-muted">Working days, weekly off, timezone, hybrid mode, and offline defaults.</div>
      </div>
      <button type="button" class="btn btn-primary" id="companySaveBtn">
        <i class="fa-solid fa-floppy-disk me-1"></i>Save Settings
      </button>
    </div>

    <div class="att-company-grid" id="companyFormWrap">
      <div>
        <label class="form-label">Company Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control company-field" data-field="company_name" placeholder="Enter company name">
      </div>
      <div>
        <label class="form-label">Legal Name</label>
        <input type="text" class="form-control company-field" data-field="legal_name" placeholder="Enter legal entity name">
      </div>
      <div>
        <label class="form-label">Company Code</label>
        <input type="text" class="form-control company-field" data-field="company_code" placeholder="Enter company code">
      </div>
      <div>
        <label class="form-label">Timezone <span class="text-danger">*</span></label>
        <select class="form-select company-field" data-field="timezone">
          <option value="">Select timezone</option>
          @foreach ($companyTimezones as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Attendance Mode <span class="text-danger">*</span></label>
        <select class="form-select company-field" data-field="attendance_mode">
          <option value="">Select mode</option>
          @foreach ($attendanceModes as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select company-field" data-field="status">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div>
        <label class="form-label">Default Grace Time (minutes)</label>
        <input type="number" class="form-control company-field" data-field="default_grace_time_minutes" min="0" placeholder="10">
      </div>
      <div>
        <label class="form-label">Offline Sync Limit</label>
        <select class="form-select company-field" data-field="offline_sync_limit_hours">
          <option value="">Select limit</option>
          @foreach ($offlineLimits as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Default Currency</label>
        <select class="form-select company-field" data-field="default_currency">
          <option value="">Select currency</option>
          @foreach ($currencies as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Date Format</label>
        <select class="form-select company-field" data-field="date_format">
          <option value="">Select format</option>
          @foreach ($dateFormats as $value => $label)
            <option value="{{ $value }}">{{ $value }} - {{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Time Format</label>
        <select class="form-select company-field" data-field="time_format">
          <option value="">Select format</option>
          @foreach ($timeFormats as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="full">
        <label class="form-label">Working Days <span class="text-danger">*</span></label>
        <div class="att-company-picker">
          <input type="text" class="form-control" id="workingDaysDisplay" readonly placeholder="Select working days">
          <button type="button" class="btn btn-outline-primary" data-day-target="working_days" id="workingDaysBtn">
            <i class="fa-solid fa-calendar-check me-1"></i>Select Days
          </button>
        </div>
      </div>
      <div class="full">
        <label class="form-label">Weekly Offs <span class="text-danger">*</span></label>
        <div class="att-company-picker">
          <input type="text" class="form-control" id="weeklyOffsDisplay" readonly placeholder="Select weekly offs">
          <button type="button" class="btn btn-outline-primary" data-day-target="weekly_offs" id="weeklyOffsBtn">
            <i class="fa-solid fa-calendar-xmark me-1"></i>Select Days
          </button>
        </div>
      </div>
    </div>
  </section>

  <section class="att-company-meta">
    <article>
      <span>Hybrid Direction</span>
      <strong>Admin First</strong>
      <div class="text-muted mt-2">Company settings should exist before shift and policy rollout starts.</div>
    </article>
    <article>
      <span>Validation Model</span>
      <strong>Server Truth</strong>
      <div class="text-muted mt-2">Employee devices can read the rules, but the backend remains the final validator.</div>
    </article>
    <article>
      <span>Offline Safety</span>
      <strong>Delay Aware</strong>
      <div class="text-muted mt-2">Default offline windows help decide when synced punches need review.</div>
    </article>
  </section>
</div>

<div class="modal fade" id="companyDayModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="companyDayModalTitle">Select Days</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="att-day-grid" id="companyDayModalGrid">
          @foreach ($dayOptions as $value => $label)
            <label class="att-day-option">
              <input type="checkbox" class="form-check-input me-2 company-day-option" value="{{ $value }}">
              {{ $label }}
            </label>
          @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" id="companyDayClearBtn">Clear</button>
        <button type="button" class="btn btn-primary" id="companyDayApplyBtn">Apply</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(() => {
  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) {
    window.location.replace('/');
    return;
  }

  const endpoint = '/api/attendance/admin/company';
  const fields = Array.from(document.querySelectorAll('.company-field'));
  const dayLabels = @json($dayOptions);
  const state = {
    working_days: [],
    weekly_offs: [],
    dayTarget: 'working_days',
  };

  const dayModalEl = document.getElementById('companyDayModal');
  const dayModal = dayModalEl ? new bootstrap.Modal(dayModalEl) : null;
  const dayModalTitle = document.getElementById('companyDayModalTitle');
  const dayCheckboxes = Array.from(document.querySelectorAll('.company-day-option'));
  const workingDaysDisplay = document.getElementById('workingDaysDisplay');
  const weeklyOffsDisplay = document.getElementById('weeklyOffsDisplay');

  function authHeaders() {
    return {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
  }

  function toArray(value) {
    if (!value) return [];
    if (Array.isArray(value)) return value;
    try {
      const parsed = JSON.parse(value);
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return String(value).split(',').map((item) => item.trim()).filter(Boolean);
    }
  }

  function labelizeDays(values) {
    return values.map((item) => dayLabels[item] || item).join(', ');
  }

  function renderDayDisplays() {
    workingDaysDisplay.value = state.working_days.length ? labelizeDays(state.working_days) : '';
    weeklyOffsDisplay.value = state.weekly_offs.length ? labelizeDays(state.weekly_offs) : '';
  }

  function fillForm(data = {}) {
    fields.forEach((field) => {
      const key = field.dataset.field;
      field.value = data[key] ?? '';
    });

    state.working_days = toArray(data.working_days);
    state.weekly_offs = toArray(data.weekly_offs);
    renderDayDisplays();
  }

  function payload() {
    const out = {};
    fields.forEach((field) => {
      const key = field.dataset.field;
      const value = field.value.trim();
      if (value === '') {
        out[key] = null;
      } else if (field.type === 'number' || field.tagName === 'SELECT' && ['default_grace_time_minutes', 'offline_sync_limit_hours'].includes(key)) {
        out[key] = Number(value);
      } else {
        out[key] = value;
      }
    });

    out.working_days = state.working_days;
    out.weekly_offs = state.weekly_offs;
    return out;
  }

  function syncDayModal() {
    const values = state[state.dayTarget] || [];
    dayCheckboxes.forEach((checkbox) => {
      checkbox.checked = values.includes(checkbox.value);
    });
    dayModalTitle.textContent = state.dayTarget === 'working_days' ? 'Select Working Days' : 'Select Weekly Offs';
  }

  async function loadCompany() {
    const response = await fetch(endpoint, { headers: authHeaders() });
    const data = await response.json();
    if (!response.ok) throw new Error(data.message || 'Could not load company settings.');
    fillForm(data.data || {});
  }

  async function saveCompany() {
    try {
      const response = await fetch(endpoint, {
        method: 'PATCH',
        headers: authHeaders(),
        body: JSON.stringify(payload())
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Could not save company settings.');
      fillForm(data.data || {});
      Swal.fire('Saved', data.message || 'Company settings saved successfully.', 'success');
    } catch (error) {
      Swal.fire('Unable to save', error.message, 'error');
    }
  }

  document.querySelectorAll('[data-day-target]').forEach((button) => {
    button.addEventListener('click', () => {
      state.dayTarget = button.dataset.dayTarget;
      syncDayModal();
      dayModal?.show();
    });
  });

  document.getElementById('companyDayApplyBtn').addEventListener('click', () => {
    state[state.dayTarget] = dayCheckboxes.filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value);
    renderDayDisplays();
    dayModal?.hide();
  });

  document.getElementById('companyDayClearBtn').addEventListener('click', () => {
    dayCheckboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });
  });

  document.getElementById('companySaveBtn').addEventListener('click', saveCompany);
  loadCompany().catch((error) => Swal.fire('Unable to load', error.message, 'error'));
})();
</script>
@endpush
