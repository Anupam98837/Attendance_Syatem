@push('styles')
<style>
.emp-manage{display:grid;gap:18px}
.emp-head,.emp-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:24px;
  box-shadow:var(--shadow-1);
}
.emp-head{
  padding:24px;
  background:linear-gradient(140deg, rgba(15,118,110,.08), rgba(217,119,6,.10));
}
.emp-head h1{margin:0 0 8px;font-size:30px}
.emp-head p{margin:0;color:var(--muted-color);line-height:1.75;max-width:80ch}
.emp-card{overflow:hidden}
.emp-toolbar{padding:16px}
.emp-toolbar-row{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  align-items:end;
}
.emp-table thead th{
  background:var(--surface-3);
  color:var(--ink);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  white-space:nowrap;
}
.emp-table tbody td{border-top:1px solid var(--line-soft);vertical-align:middle}
.emp-table tbody tr:hover{background:var(--surface-2)}
.emp-status{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 10px;
  border-radius:999px;
  border:1px solid var(--line-soft);
  background:var(--surface-2);
  font-size:12px;
  font-weight:700;
}
.emp-actions{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  justify-content:flex-end;
}
.emp-footer{
  display:flex;
  flex-wrap:wrap;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:14px 16px;
  border-top:1px solid var(--line-soft);
}
.emp-empty{padding:42px 18px;text-align:center;color:var(--muted-color)}
.emp-grid{
  display:grid;
  grid-template-columns:repeat(2, minmax(0,1fr));
  gap:14px;
}
.emp-grid .full{grid-column:1 / -1}
.emp-designation-row{
  display:flex;
  gap:10px;
  align-items:center;
}
.emp-designation-row .form-select{
  flex:1;
}
.emp-mini-grid{
  display:grid;
  grid-template-columns:repeat(3, minmax(0,1fr));
  gap:14px;
}
.emp-history{
  display:grid;
  gap:10px;
}
.emp-history-item{
  padding:12px 14px;
  border-radius:16px;
  border:1px solid var(--line-soft);
  background:var(--surface-2);
}
.emp-history-item strong{display:block;margin-bottom:4px}
.emp-history-item span{display:block;color:var(--muted-color);font-size:13px;line-height:1.6}
@media (max-width: 991.98px){
  .emp-mini-grid{grid-template-columns:1fr 1fr}
}
@media (max-width: 767.98px){
  .emp-grid,.emp-mini-grid{grid-template-columns:1fr}
  .emp-designation-row{
    flex-direction:column;
    align-items:stretch;
  }
}
</style>
@endpush

<div class="emp-manage">
  <section class="emp-head">
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
      <div>
        <span class="att-inline-badge"><i class="fa-solid fa-users"></i>Workforce Management</span>
        <h1>Manage Employees</h1>
        <p>Create and maintain attendance-ready employee profiles with branch, department, designation, shift, attendance policy, work mode, manager, and device context.</p>
      </div>
      <button type="button" class="btn btn-primary" id="employeeAddBtn">
        <i class="fa-solid fa-user-plus me-1"></i>Add Employee
      </button>
    </div>
  </section>

  <section class="emp-card">
    <div class="emp-toolbar">
      <div class="emp-toolbar-row">
        <div>
          <label class="small text-muted d-block mb-1">Search</label>
          <input type="search" class="form-control" id="employeeSearch" placeholder="Name, email, phone, employee code">
        </div>
        <div>
          <label class="small text-muted d-block mb-1">Department</label>
          <select class="form-select" id="employeeDepartmentFilter"><option value="">All</option></select>
        </div>
        <div>
          <label class="small text-muted d-block mb-1">Branch</label>
          <select class="form-select" id="employeeBranchFilter"><option value="">All</option></select>
        </div>
        <div>
          <label class="small text-muted d-block mb-1">Status</label>
          <select class="form-select" id="employeeStatusFilter">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div>
          <label class="small text-muted d-block mb-1">Per Page</label>
          <select class="form-select" id="employeePerPage">
            <option value="10">10</option>
            <option value="20" selected>20</option>
            <option value="50">50</option>
          </select>
        </div>
        <div class="ms-auto d-flex align-items-end gap-2">
          <button type="button" class="btn btn-light" id="employeeResetBtn"><i class="fa-solid fa-rotate-left me-1"></i>Reset</button>
          <button type="button" class="btn btn-primary" id="employeeRefreshBtn"><i class="fa-solid fa-arrows-rotate me-1"></i>Refresh</button>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table emp-table align-middle mb-0">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Role</th>
            <th>Department</th>
            <th>Branch</th>
            <th>Shift</th>
            <th>Policy</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="employeeTbody">
          <tr><td colspan="8" class="emp-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading employees...</td></tr>
        </tbody>
      </table>
    </div>
    <div class="emp-footer">
      <div class="small text-muted" id="employeeInfo">—</div>
      <ul class="pagination mb-0" id="employeePager"></ul>
    </div>
  </section>
</div>

<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="employeeModalTitle">Add Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="employeeForm">
        <div class="modal-body" style="max-height:calc(100vh - 210px); overflow-y:auto;">
          <div class="emp-grid">
            <div>
              <label class="form-label">Name</label>
              <input type="text" class="form-control employee-field" data-field="name">
            </div>
            <div>
              <label class="form-label">Employee Code</label>
              <input type="text" class="form-control employee-field" data-field="employee_code">
            </div>
            <div>
              <label class="form-label">Email</label>
              <input type="email" class="form-control employee-field" data-field="email">
            </div>
            <div>
              <label class="form-label">Phone</label>
              <input type="text" class="form-control employee-field" data-field="phone_number">
            </div>
            <div>
              <label class="form-label">Password</label>
              <input type="password" class="form-control employee-field" data-field="password">
            </div>
            <div>
              <label class="form-label">Login Role</label>
              <input type="text" class="form-control" value="Employee" readonly>
            </div>
            <div>
              <label class="form-label">Department</label>
              <select class="form-select employee-field relation-select" data-field="department_id" data-source="departments"></select>
            </div>
            <div>
              <label class="form-label">Designation</label>
              <div class="emp-designation-row">
                <select class="form-select employee-field relation-select" data-field="designation_id" data-source="designations" id="employeeDesignationSelect"></select>
                <button type="button" class="btn btn-outline-primary" id="employeeDesignationOtherBtn">
                  <i class="fa-solid fa-plus me-1"></i>Other
                </button>
              </div>
            </div>
            <div>
              <label class="form-label">Branch</label>
              <select class="form-select employee-field relation-select" data-field="branch_id" data-source="branches"></select>
            </div>
            <div>
              <label class="form-label">Shift</label>
              <select class="form-select employee-field relation-select" data-field="shift_id" data-source="shifts"></select>
            </div>
            <div>
              <label class="form-label">Attendance Policy</label>
              <select class="form-select employee-field relation-select" data-field="attendance_policy_id" data-source="attendance-policies"></select>
            </div>
            <div>
              <label class="form-label">Manager</label>
              <select class="form-select employee-field" data-field="manager_user_id" id="employeeManagerSelect"></select>
            </div>
            <div>
              <label class="form-label">Work Mode</label>
              <select class="form-select employee-field" data-field="work_mode">
                <option value="office">Office</option>
                <option value="field">Field</option>
                <option value="wfh">WFH</option>
                <option value="hybrid">Hybrid</option>
              </select>
            </div>
            <div>
              <label class="form-label">Employment Type</label>
              <input type="text" class="form-control employee-field" data-field="employment_type" placeholder="permanent / contract">
            </div>
            <div>
              <label class="form-label">Join Date</label>
              <input type="date" class="form-control employee-field" data-field="join_date">
            </div>
            <div>
              <label class="form-label">User Status</label>
              <select class="form-select employee-field" data-field="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div>
              <label class="form-label">Employee Status</label>
              <select class="form-select employee-field" data-field="employee_status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="full">
              <label class="form-label">Address</label>
              <textarea class="form-control employee-field" rows="2" data-field="address"></textarea>
            </div>
            <div class="full">
              <label class="form-label">Notes</label>
              <textarea class="form-control employee-field" rows="3" data-field="notes"></textarea>
            </div>
            <div class="full">
              <div class="emp-mini-grid">
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input employee-check" type="checkbox" data-field="offline_attendance_enabled">
                  <label class="form-check-label">Offline attendance enabled</label>
                </div>
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input employee-check" type="checkbox" data-field="field_attendance_enabled">
                  <label class="form-check-label">Field attendance enabled</label>
                </div>
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input employee-check" type="checkbox" data-field="wfh_attendance_enabled">
                  <label class="form-check-label">WFH attendance enabled</label>
                </div>
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input employee-check" type="checkbox" data-field="continuous_tracking_enabled">
                  <label class="form-check-label">Continuous tracking enabled</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Save Employee</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="employeeHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Employee Attendance History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="employeeHistoryWrap" class="emp-history">
          <div class="emp-empty"><i class="fa-solid fa-clock-rotate-left me-2"></i>Select an employee record to load history.</div>
        </div>
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

  const api = {
    employees: '/api/attendance/hr/employees',
    departments: '/api/attendance/admin/departments?per_page=200',
    designations: '/api/attendance/admin/designations?per_page=200',
    createDesignation: '/api/attendance/admin/designations',
    branches: '/api/attendance/admin/branches?per_page=200',
    shifts: '/api/attendance/admin/shifts?per_page=200',
    policies: '/api/attendance/admin/attendance-policies?per_page=200',
    users: '/api/users/all',
  };

  const state = { page: 1, per_page: 20, q: '', department_id: '', branch_id: '', status: '' };
  let editingId = null;
  let managers = [];
  let designationRows = [];

  const corporateDesignationSeeds = [
    { name: 'Chief Executive Officer', code: 'CEO' },
    { name: 'Chief Operating Officer', code: 'COO' },
    { name: 'Chief Financial Officer', code: 'CFO' },
    { name: 'Chief Technology Officer', code: 'CTO' },
    { name: 'Vice President', code: 'VP' },
    { name: 'Director', code: 'DIR' },
    { name: 'Senior Manager', code: 'SR-MGR' },
    { name: 'Manager', code: 'MGR' },
    { name: 'Assistant Manager', code: 'AST-MGR' },
    { name: 'Team Lead', code: 'TL' },
    { name: 'Senior Software Engineer', code: 'SR-SWE' },
    { name: 'Software Engineer', code: 'SWE' },
    { name: 'QA Engineer', code: 'QA' },
    { name: 'Business Analyst', code: 'BA' },
    { name: 'Product Manager', code: 'PM' },
    { name: 'Accounts Executive', code: 'ACC-EXEC' },
    { name: 'Admin Executive', code: 'ADMIN-EXEC' },
    { name: 'Sales Executive', code: 'SALES-EXEC' },
    { name: 'Marketing Executive', code: 'MKT-EXEC' },
    { name: 'Customer Support Executive', code: 'CS-EXEC' },
    { name: 'Operations Executive', code: 'OPS-EXEC' },
    { name: 'Field Executive', code: 'FIELD-EXEC' },
    { name: 'Receptionist', code: 'RECEP' },
    { name: 'Data Entry Operator', code: 'DEO' },
    { name: 'Intern', code: 'INTERN' },
  ];

  const els = {
    tbody: document.getElementById('employeeTbody'),
    info: document.getElementById('employeeInfo'),
    pager: document.getElementById('employeePager'),
    search: document.getElementById('employeeSearch'),
    department: document.getElementById('employeeDepartmentFilter'),
    branch: document.getElementById('employeeBranchFilter'),
    status: document.getElementById('employeeStatusFilter'),
    perPage: document.getElementById('employeePerPage'),
    addBtn: document.getElementById('employeeAddBtn'),
    refreshBtn: document.getElementById('employeeRefreshBtn'),
    resetBtn: document.getElementById('employeeResetBtn'),
    form: document.getElementById('employeeForm'),
    modalTitle: document.getElementById('employeeModalTitle'),
    historyWrap: document.getElementById('employeeHistoryWrap'),
    managerSelect: document.getElementById('employeeManagerSelect'),
    designationSelect: document.getElementById('employeeDesignationSelect'),
    designationOtherBtn: document.getElementById('employeeDesignationOtherBtn'),
  };

  const employeeModal = new bootstrap.Modal(document.getElementById('employeeModal'));
  const historyModal = new bootstrap.Modal(document.getElementById('employeeHistoryModal'));

  function headers(json = false) {
    return json
      ? { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' }
      : { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function statusBadge(value) {
    return `<span class="emp-status">${escapeHtml(value || 'active')}</span>`;
  }

  function query() {
    const params = new URLSearchParams();
    Object.entries(state).forEach(([key, value]) => {
      if (value !== '') params.set(key, String(value));
    });
    return params.toString();
  }

  async function fetchJson(url) {
    const response = await fetch(url, { headers: headers(false) });
    const data = await response.json();
    if (!response.ok) throw new Error(data.message || 'Request failed.');
    return data;
  }

  function renderOptions(select, items, placeholder) {
    const current = select.value;
    select.innerHTML = `<option value="">${placeholder}</option>` + items.map((item) => `<option value="${item.id}">${escapeHtml(item.name || item.label || item.employee_code || item.email || 'Option')}</option>`).join('');
    if (current) select.value = current;
  }

  function renderDesignationOptions(selectedValue = '') {
    if (!els.designationSelect) return;

    const merged = [...designationRows];
    corporateDesignationSeeds.forEach((seed) => {
      const exists = merged.some((row) => String(row.name || '').toLowerCase() === seed.name.toLowerCase());
      if (!exists) {
        merged.push({ id: `seed:${seed.code}`, name: seed.name, code: seed.code });
      }
    });

    merged.sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));

    els.designationSelect.innerHTML = '<option value="">Optional</option>' +
      merged.map((item) => `<option value="${item.id}">${escapeHtml(item.name || 'Designation')}</option>`).join('') +
      '<option value="__other__">Other (Add New)</option>';

    if (selectedValue) {
      els.designationSelect.value = String(selectedValue);
    }
  }

  async function loadReferenceOptions() {
    const [departments, designations, branches, shifts, policies, users] = await Promise.all([
      fetchJson(api.departments),
      fetchJson(api.designations),
      fetchJson(api.branches),
      fetchJson(api.shifts),
      fetchJson(api.policies),
      fetchJson(api.users),
    ]);

    renderOptions(els.department, departments.data || [], 'All departments');
    renderOptions(els.branch, branches.data || [], 'All branches');
    designationRows = designations.data || [];

    document.querySelectorAll('.relation-select').forEach((select) => {
      const source = select.dataset.source;
      const sourceItems = ({
        departments: departments.data || [],
        designations: designationRows,
        branches: branches.data || [],
        shifts: shifts.data || [],
        'attendance-policies': policies.data || [],
      })[source] || [];

      if (source === 'designations' && select === els.designationSelect) {
        renderDesignationOptions(select.value);
      } else {
        renderOptions(select, sourceItems, 'Optional');
      }
    });

    managers = (users.users || users.data || []).map((user) => ({
      id: user.id,
      name: user.name ? `${user.name} (${user.email || user.role || 'user'})` : `User ${user.id}`
    }));
    renderOptions(els.managerSelect, managers, 'Optional manager');
  }

  function renderEmployees(rows) {
    if (!rows.length) {
      els.tbody.innerHTML = '<tr><td colspan="8" class="emp-empty"><i class="fa-regular fa-folder-open me-2"></i>No employees found.</td></tr>';
      return;
    }

    els.tbody.innerHTML = rows.map((row) => `
      <tr>
        <td>
          <strong>${escapeHtml(row.name || '—')}</strong>
          <div class="text-muted small">${escapeHtml(row.employee_code || 'No employee code')}</div>
          <div class="text-muted small">${escapeHtml(row.email || '')}</div>
        </td>
        <td>${statusBadge(row.role || 'employee')}</td>
        <td>${escapeHtml(row.department_name || '—')}</td>
        <td>${escapeHtml(row.branch_name || '—')}</td>
        <td>${escapeHtml(row.shift_name || '—')}</td>
        <td>${escapeHtml(row.policy_name || '—')}</td>
        <td>${statusBadge(row.employee_status || row.user_status || 'active')}</td>
        <td class="text-end">
          <div class="emp-actions">
            <button type="button" class="btn btn-sm btn-light js-history" data-id="${row.id}"><i class="fa-solid fa-clock-rotate-left"></i></button>
            <button type="button" class="btn btn-sm btn-light js-edit" data-id="${row.id}"><i class="fa-solid fa-pen"></i></button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  function renderPager(pagination) {
    const page = Number(pagination.page || 1);
    const last = Number(pagination.last_page || 1);
    const items = [];
    items.push(`<li class="page-item ${page <= 1 ? 'disabled' : ''}"><button class="page-link" data-page="${page - 1}">Prev</button></li>`);
    for (let i = 1; i <= last; i += 1) {
      if (i === 1 || i === last || Math.abs(i - page) <= 1) {
        items.push(`<li class="page-item ${i === page ? 'active' : ''}"><button class="page-link" data-page="${i}">${i}</button></li>`);
      } else if (Math.abs(i - page) === 2) {
        items.push('<li class="page-item disabled"><span class="page-link">…</span></li>');
      }
    }
    items.push(`<li class="page-item ${page >= last ? 'disabled' : ''}"><button class="page-link" data-page="${page + 1}">Next</button></li>`);
    els.pager.innerHTML = items.join('');
  }

  async function loadEmployees() {
    els.tbody.innerHTML = '<tr><td colspan="8" class="emp-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading employees...</td></tr>';
    try {
      const data = await fetchJson(`${api.employees}?${query()}`);
      renderEmployees(data.data || []);
      renderPager(data.pagination || {});
      els.info.textContent = `Showing ${(data.data || []).length} of ${data.pagination?.total || 0} employees.`;
    } catch (error) {
      els.tbody.innerHTML = `<tr><td colspan="8" class="emp-empty text-danger">${escapeHtml(error.message)}</td></tr>`;
    }
  }

  function resetForm() {
    editingId = null;
    els.form.reset();
    document.querySelectorAll('.employee-check').forEach((checkbox) => {
      checkbox.checked = false;
    });
    renderDesignationOptions('');
  }

  function setFieldValue(fieldName, value) {
    const input = els.form.querySelector(`[data-field="${fieldName}"]`);
    if (!input) return;
    input.value = value ?? '';
  }

  async function openEdit(id) {
    try {
      const data = await fetchJson(`${api.employees}/${id}`);
      const profile = data.data?.profile || {};

      resetForm();
      editingId = id;
      els.modalTitle.textContent = 'Edit Employee';

      const map = {
        name: profile.name,
        employee_code: profile.employee_code,
        email: profile.email,
        phone_number: profile.phone_number,
        department_id: profile.department_id,
        designation_id: profile.designation_id,
        branch_id: profile.branch_id,
        shift_id: profile.shift_id,
        attendance_policy_id: profile.attendance_policy_id,
        manager_user_id: profile.manager_user_id,
        work_mode: profile.work_mode,
        employment_type: profile.employment_type,
        join_date: profile.join_date,
        status: profile.user_status,
        employee_status: profile.status,
        address: profile.address,
        notes: profile.notes,
      };

      Object.entries(map).forEach(([key, value]) => setFieldValue(key, value));
      ['offline_attendance_enabled', 'field_attendance_enabled', 'wfh_attendance_enabled', 'continuous_tracking_enabled'].forEach((key) => {
        const checkbox = els.form.querySelector(`[data-field="${key}"]`);
        if (checkbox) checkbox.checked = Boolean(profile[key]);
      });

      employeeModal.show();
    } catch (error) {
      Swal.fire('Unable to edit', error.message, 'error');
    }
  }

  function collectPayload() {
    const payload = {};
    Array.from(els.form.querySelectorAll('.employee-field')).forEach((input) => {
      const key = input.dataset.field;
      const value = input.value.trim();
      if (key === 'designation_id' && String(value).startsWith('seed:')) {
        payload[key] = null;
        return;
      }
      payload[key] = value === '' ? null : value;
    });
    Array.from(els.form.querySelectorAll('.employee-check')).forEach((input) => {
      payload[input.dataset.field] = input.checked;
    });
    if (editingId && !payload.password) delete payload.password;
    return payload;
  }

  async function promptAddDesignation() {
    const result = await Swal.fire({
      title: 'Add designation',
      target: document.getElementById('employeeModal'),
      heightAuto: false,
      returnFocus: false,
      html: `
        <input id="designationNameInput" class="swal2-input" placeholder="Designation name">
        <input id="designationCodeInput" class="swal2-input" placeholder="Code (optional)">
        <textarea id="designationDescriptionInput" class="swal2-textarea" placeholder="Description (optional)"></textarea>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Save designation',
      allowOutsideClick: () => !Swal.isLoading(),
      allowEscapeKey: () => !Swal.isLoading(),
      showLoaderOnConfirm: true,
      didOpen: () => {
        document.getElementById('designationNameInput')?.focus();
      },
      preConfirm: async () => {
        const name = document.getElementById('designationNameInput').value.trim();
        const code = document.getElementById('designationCodeInput').value.trim();
        const description = document.getElementById('designationDescriptionInput').value.trim();

        if (!name) {
          Swal.showValidationMessage('Designation name is required.');
          return false;
        }

        const response = await fetch(api.createDesignation, {
          method: 'POST',
          headers: headers(true),
          body: JSON.stringify({ name, code: code || null, description: description || null, status: 'active' })
        });
        const data = await response.json();
        if (!response.ok) {
          Swal.showValidationMessage(data.message || 'Could not create designation.');
          return false;
        }

        return data;
      }
    });

    if (!result.isConfirmed || !result.value) {
      if (els.designationSelect?.value === '__other__') {
        els.designationSelect.value = '';
      }
      return;
    }

    try {
      const data = result.value;
      await loadReferenceOptions();
      if (els.designationSelect) {
        const identifier = data.data?.id ?? data.data?.uuid ?? '';
        if (identifier !== '') els.designationSelect.value = String(identifier);
      }

      Swal.fire('Saved', data.message || 'Designation created successfully.', 'success');
    } catch (error) {
      Swal.fire('Unable to save', error.message, 'error');
      if (els.designationSelect?.value === '__other__') {
        els.designationSelect.value = '';
      }
    }
  }

  async function saveEmployee(event) {
    event.preventDefault();
    const method = editingId ? 'PATCH' : 'POST';
    const url = editingId ? `${api.employees}/${editingId}` : api.employees;
    try {
      const response = await fetch(url, {
        method,
        headers: headers(true),
        body: JSON.stringify(collectPayload())
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Could not save employee.');
      employeeModal.hide();
      await loadEmployees();
      Swal.fire('Saved', data.message || 'Employee saved successfully.', 'success');
    } catch (error) {
      Swal.fire('Unable to save', error.message, 'error');
    }
  }

  async function openHistory(id) {
    historyModal.show();
    els.historyWrap.innerHTML = '<div class="emp-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading attendance history...</div>';
    try {
      const data = await fetchJson(`${api.employees}/${id}/attendance-history`);
      const rows = data.data || [];
      if (!rows.length) {
        els.historyWrap.innerHTML = '<div class="emp-empty"><i class="fa-regular fa-calendar-xmark me-2"></i>No attendance history found.</div>';
        return;
      }
      els.historyWrap.innerHTML = rows.map((row) => `
        <div class="emp-history-item">
          <strong>${escapeHtml(row.attendance_date || 'Unknown date')} • ${escapeHtml(row.status || 'pending')}</strong>
          <span>Check in: ${escapeHtml(row.check_in_time || '—')} | Check out: ${escapeHtml(row.check_out_time || '—')}</span>
          <span>Mode: ${escapeHtml(row.attendance_mode || '—')} | Approval: ${escapeHtml(row.approval_status || '—')} | Sync: ${escapeHtml(row.sync_status || '—')}</span>
        </div>
      `).join('');
    } catch (error) {
      els.historyWrap.innerHTML = `<div class="emp-empty text-danger">${escapeHtml(error.message)}</div>`;
    }
  }

  els.addBtn.addEventListener('click', () => {
    resetForm();
    els.modalTitle.textContent = 'Add Employee';
    employeeModal.show();
  });
  els.refreshBtn.addEventListener('click', loadEmployees);
  els.resetBtn.addEventListener('click', () => {
    state.page = 1;
    state.per_page = 20;
    state.q = '';
    state.department_id = '';
    state.branch_id = '';
    state.status = '';
    els.search.value = '';
    els.department.value = '';
    els.branch.value = '';
    els.status.value = '';
    els.perPage.value = '20';
    loadEmployees();
  });
  els.search.addEventListener('input', () => {
    state.page = 1;
    state.q = els.search.value.trim();
    loadEmployees();
  });
  els.department.addEventListener('change', () => {
    state.page = 1;
    state.department_id = els.department.value;
    loadEmployees();
  });
  els.branch.addEventListener('change', () => {
    state.page = 1;
    state.branch_id = els.branch.value;
    loadEmployees();
  });
  els.status.addEventListener('change', () => {
    state.page = 1;
    state.status = els.status.value;
    loadEmployees();
  });
  els.designationSelect?.addEventListener('change', () => {
    if (els.designationSelect.value === '__other__') {
      promptAddDesignation();
    }
  });
  els.designationOtherBtn?.addEventListener('click', promptAddDesignation);
  els.perPage.addEventListener('change', () => {
    state.page = 1;
    state.per_page = Number(els.perPage.value || 20);
    loadEmployees();
  });
  els.form.addEventListener('submit', saveEmployee);
  els.pager.addEventListener('click', (event) => {
    const button = event.target.closest('[data-page]');
    if (!button || button.parentElement.classList.contains('disabled')) return;
    state.page = Number(button.dataset.page || '1');
    loadEmployees();
  });
  els.tbody.addEventListener('click', (event) => {
    const edit = event.target.closest('.js-edit');
    const history = event.target.closest('.js-history');
    if (edit) openEdit(edit.dataset.id);
    if (history) openHistory(history.dataset.id);
  });

  Promise.all([loadReferenceOptions(), loadEmployees()]).catch((error) => {
    Swal.fire('Unable to load employee workspace', error.message, 'error');
  });
})();
</script>
@endpush
