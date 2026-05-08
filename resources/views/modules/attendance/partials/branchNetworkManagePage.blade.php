@push('styles')
<style>
.net-manage{display:grid;gap:18px}
.net-head,
.net-card,
.net-side{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:24px;
  box-shadow:var(--shadow-1);
}
.net-head{
  padding:24px;
  background:linear-gradient(140deg, rgba(15,118,110,.08), rgba(217,119,6,.10));
}
.net-head h1{margin:0 0 8px;font-size:30px}
.net-head p{margin:0;color:var(--muted-color);line-height:1.75;max-width:80ch}
.net-layout{
  display:grid;
  grid-template-columns:minmax(280px, 320px) minmax(0, 1fr);
  gap:18px;
}
.net-side{padding:18px}
.net-side h2{margin:0 0 6px;font-size:18px}
.net-side p{margin:0 0 14px;color:var(--muted-color);line-height:1.7}
.net-branch-list{
  display:grid;
  gap:10px;
  max-height:540px;
  overflow:auto;
}
.net-branch-btn{
  width:100%;
  text-align:left;
  border:1px solid var(--line-soft);
  border-radius:18px;
  background:var(--surface-2);
  padding:14px;
  transition:transform .16s ease, border-color .16s ease, background .16s ease;
}
.net-branch-btn:hover{
  transform:translateY(-1px);
  border-color:var(--line-strong);
}
.net-branch-btn.active{
  background:rgba(15,118,110,.08);
  border-color:rgba(15,118,110,.24);
}
.net-branch-btn strong{
  display:block;
  margin-bottom:4px;
  color:var(--ink);
}
.net-branch-btn span{
  display:block;
  color:var(--muted-color);
  font-size:12px;
  line-height:1.6;
}
.net-card{overflow:hidden}
.net-toolbar{
  padding:16px;
  border-bottom:1px solid var(--line-soft);
  display:flex;
  flex-wrap:wrap;
  align-items:end;
  justify-content:space-between;
  gap:12px;
}
.net-toolbar h2{margin:0;font-size:20px}
.net-toolbar p{margin:6px 0 0;color:var(--muted-color)}
.net-table thead th{
  background:var(--surface-3);
  color:var(--ink);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  white-space:nowrap;
}
.net-table tbody td{
  border-top:1px solid var(--line-soft);
  vertical-align:middle;
}
.net-table tbody tr:hover{background:var(--surface-2)}
.net-actions{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  justify-content:flex-end;
}
.net-empty{
  padding:42px 18px;
  text-align:center;
  color:var(--muted-color);
}
.net-badge{
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
.net-grid{
  display:grid;
  grid-template-columns:repeat(2, minmax(0,1fr));
  gap:14px;
}
.net-grid .full{grid-column:1 / -1}
.net-inline-field{
  display:flex;
  gap:10px;
  align-items:center;
}
.net-inline-field .form-control{
  flex:1;
}
@media (max-width: 991.98px){
  .net-layout{grid-template-columns:1fr}
}
@media (max-width: 767.98px){
  .net-grid{grid-template-columns:1fr}
  .net-inline-field{
    flex-direction:column;
    align-items:stretch;
  }
}
</style>
@endpush

<div class="net-manage">
  <section class="net-head">
    <span class="att-inline-badge"><i class="fa-solid fa-wifi"></i>Network Restriction Setup</span>
    <h1>Manage Branch Wi-Fi / IP Rules</h1>
    <p>Define the exact IPs, CIDR ranges, and network labels that are allowed for office attendance. Use this with branch Wi-Fi-only and mobile-data restriction policies to make office punching strict and predictable.</p>
  </section>

  <section class="net-layout">
    <aside class="net-side">
      <h2>Branches</h2>
      <p>Select a branch to manage its approved Wi-Fi and IP patterns.</p>
      <div class="net-branch-list" id="networkBranchList">
        <div class="net-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading branches...</div>
      </div>
    </aside>

    <div class="net-card">
      <div class="net-toolbar">
        <div>
          <span class="net-badge" id="networkBranchBadge"><i class="fa-solid fa-location-dot"></i>No branch selected</span>
          <h2 id="networkPanelTitle">Allowed Networks</h2>
          <p id="networkPanelLead">Pick a branch to load its network restrictions.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <button type="button" class="btn btn-light" id="networkRefreshBtn" disabled>
            <i class="fa-solid fa-arrows-rotate me-1"></i>Refresh
          </button>
          <button type="button" class="btn btn-primary" id="networkAddBtn" disabled>
            <i class="fa-solid fa-plus me-1"></i>Add Allowed Network
          </button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table net-table align-middle mb-0">
          <thead>
            <tr>
              <th>Label</th>
              <th>IP Pattern</th>
              <th>Network Type</th>
              <th>Notes</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="networkTbody">
            <tr><td colspan="6" class="net-empty"><i class="fa-regular fa-hand-pointer me-2"></i>Select a branch to continue.</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="networkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="networkModalTitle">Add Allowed Network</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="networkForm">
        <div class="modal-body">
          <div class="net-grid">
            <div>
              <label class="form-label">Label <span class="text-danger">*</span></label>
              <input type="text" class="form-control network-field" data-field="label" placeholder="Office LAN / Static IP" required>
            </div>
            <div>
              <label class="form-label">IP Pattern <span class="text-danger">*</span></label>
              <div class="net-inline-field">
                <input type="text" class="form-control network-field" data-field="ip_pattern" placeholder="192.168.1.0/24 or 127.0.0.1" required>
                <button type="button" class="btn btn-outline-primary" id="networkGetIpBtn">
                  <i class="fa-solid fa-crosshairs me-1"></i>Get My IP
                </button>
              </div>
            </div>
            <div>
              <label class="form-label">Network Type <span class="text-danger">*</span></label>
              <select class="form-select network-field" data-field="network_type">
                <option value="wifi">Wi-Fi</option>
                <option value="lan">LAN</option>
                <option value="public_ip">Public IP</option>
              </select>
            </div>
            <div>
              <label class="form-label d-block">Active</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input network-field" type="checkbox" role="switch" data-field="is_active" checked>
                <label class="form-check-label">Allow this network for attendance validation</label>
              </div>
            </div>
            <div class="full">
              <label class="form-label">Notes</label>
              <textarea class="form-control network-field" rows="4" data-field="notes" placeholder="Optional notes for HR/Admin"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="networkSaveBtn">
            <i class="fa-solid fa-floppy-disk me-1"></i>Save Allowed Network
          </button>
        </div>
      </form>
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

  const headers = (json = false) => json
    ? { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' }
    : { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

  const state = {
    branches: [],
    activeBranch: null,
    rows: [],
    editingId: null,
  };

  const branchList = document.getElementById('networkBranchList');
  const tbody = document.getElementById('networkTbody');
  const panelTitle = document.getElementById('networkPanelTitle');
  const panelLead = document.getElementById('networkPanelLead');
  const branchBadge = document.getElementById('networkBranchBadge');
  const addBtn = document.getElementById('networkAddBtn');
  const refreshBtn = document.getElementById('networkRefreshBtn');
  const form = document.getElementById('networkForm');
  const modalEl = document.getElementById('networkModal');
  const modalTitle = document.getElementById('networkModalTitle');
  const networkModal = modalEl ? new bootstrap.Modal(modalEl) : null;
  const getIpBtn = document.getElementById('networkGetIpBtn');

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function fieldElements() {
    return Array.from(document.querySelectorAll('.network-field'));
  }

  function fieldByName(name) {
    return document.querySelector(`.network-field[data-field="${name}"]`);
  }

  function resetForm() {
    state.editingId = null;
    modalTitle.textContent = 'Add Allowed Network';
    fieldElements().forEach((field) => {
      if (field.type === 'checkbox') {
        field.checked = field.dataset.field === 'is_active';
      } else {
        field.value = '';
      }
    });
    const networkType = document.querySelector('.network-field[data-field="network_type"]');
    if (networkType) networkType.value = 'wifi';
  }

  function setFormFromRow(row) {
    state.editingId = row.id;
    modalTitle.textContent = 'Edit Allowed Network';
    fieldElements().forEach((field) => {
      const key = field.dataset.field;
      if (field.type === 'checkbox') {
        field.checked = Boolean(Number(row[key] ?? 0));
      } else {
        field.value = row[key] ?? '';
      }
    });
  }

  function payloadFromForm() {
    const payload = {};
    fieldElements().forEach((field) => {
      const key = field.dataset.field;
      payload[key] = field.type === 'checkbox' ? field.checked : (field.value || null);
    });
    return payload;
  }

  function renderBranches() {
    if (!state.branches.length) {
      branchList.innerHTML = '<div class="net-empty"><i class="fa-regular fa-folder-open me-2"></i>No branches found.</div>';
      return;
    }

    branchList.innerHTML = state.branches.map((branch) => `
      <button type="button" class="net-branch-btn ${state.activeBranch?.id === branch.id ? 'active' : ''}" data-id="${branch.id}">
        <strong>${escapeHtml(branch.name)}</strong>
        <span>${escapeHtml(branch.code || 'No code')} • ${escapeHtml(branch.city || 'No city')}</span>
        <span>Wi-Fi only: ${branch.wifi_only ? 'Yes' : 'No'} • Mobile data: ${branch.allow_mobile_data ? 'Allowed' : 'Blocked'}</span>
      </button>
    `).join('');

    branchList.querySelectorAll('.net-branch-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const branch = state.branches.find((item) => Number(item.id) === Number(btn.dataset.id));
        if (!branch) return;
        state.activeBranch = branch;
        renderBranches();
        loadNetworks();
      });
    });
  }

  function renderRows() {
    if (!state.activeBranch) {
      tbody.innerHTML = '<tr><td colspan="6" class="net-empty"><i class="fa-regular fa-hand-pointer me-2"></i>Select a branch to continue.</td></tr>';
      return;
    }

    if (!state.rows.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="net-empty"><i class="fa-regular fa-folder-open me-2"></i>No allowed networks configured for this branch yet.</td></tr>';
      return;
    }

    tbody.innerHTML = state.rows.map((row) => `
      <tr>
        <td>${escapeHtml(row.label || '—')}</td>
        <td><code>${escapeHtml(row.ip_pattern || '—')}</code></td>
        <td>${escapeHtml(row.network_type || 'wifi')}</td>
        <td>${escapeHtml(row.notes || '—')}</td>
        <td>${row.is_active ? '<span class="net-badge"><i class="fa-solid fa-circle-check text-success"></i>Active</span>' : '<span class="net-badge"><i class="fa-solid fa-circle-xmark text-danger"></i>Inactive</span>'}</td>
        <td class="text-end">
          <div class="net-actions">
            <button type="button" class="btn btn-sm btn-light js-network-edit" data-id="${row.id}">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger js-network-delete" data-id="${row.id}">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');

    tbody.querySelectorAll('.js-network-edit').forEach((btn) => {
      btn.addEventListener('click', () => {
        const row = state.rows.find((item) => Number(item.id) === Number(btn.dataset.id));
        if (!row) return;
        setFormFromRow(row);
        networkModal?.show();
      });
    });

    tbody.querySelectorAll('.js-network-delete').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const row = state.rows.find((item) => Number(item.id) === Number(btn.dataset.id));
        if (!row) return;

        const result = await Swal.fire({
          title: 'Delete allowed network?',
          text: `${row.label || row.ip_pattern} will be removed from this branch.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Delete'
        });

        if (!result.isConfirmed) return;

        try {
          const response = await fetch(`/api/attendance/admin/branches/${state.activeBranch.id}/networks/${row.id}`, {
            method: 'DELETE',
            headers: headers(false)
          });
          const data = await response.json().catch(() => ({}));
          if (!response.ok) throw new Error(data.message || 'Could not delete network.');
          Swal.fire({ icon: 'success', title: 'Deleted', text: data.message || 'Allowed network removed.', timer: 1500, showConfirmButton: false });
          loadNetworks();
        } catch (error) {
          Swal.fire({ icon: 'error', title: 'Delete failed', text: error.message });
        }
      });
    });
  }

  async function loadBranches() {
    branchList.innerHTML = '<div class="net-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading branches...</div>';
    try {
      const response = await fetch('/api/attendance/admin/branches?per_page=200', { headers: headers(false) });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Could not load branches.');
      state.branches = data.data || [];
      if (!state.activeBranch && state.branches.length) {
        state.activeBranch = state.branches[0];
      }
      renderBranches();
      if (state.activeBranch) {
        loadNetworks();
      } else {
        renderRows();
      }
    } catch (error) {
      branchList.innerHTML = `<div class="net-empty text-danger">${escapeHtml(error.message)}</div>`;
      renderRows();
    }
  }

  async function loadNetworks() {
    if (!state.activeBranch) {
      renderRows();
      return;
    }

    addBtn.disabled = false;
    refreshBtn.disabled = false;
    branchBadge.innerHTML = `<i class="fa-solid fa-location-dot"></i>${escapeHtml(state.activeBranch.name)}`;
    panelTitle.textContent = `${state.activeBranch.name} Allowed Networks`;
    panelLead.textContent = `CIDR ranges and IPs used to validate office attendance at ${state.activeBranch.name}.`;
    tbody.innerHTML = '<tr><td colspan="6" class="net-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading allowed networks...</td></tr>';

    try {
      const response = await fetch(`/api/attendance/admin/branches/${state.activeBranch.id}/networks`, { headers: headers(false) });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Could not load allowed networks.');
      state.rows = data.data || [];
      renderRows();
    } catch (error) {
      tbody.innerHTML = `<tr><td colspan="6" class="net-empty text-danger">${escapeHtml(error.message)}</td></tr>`;
    }
  }

  addBtn?.addEventListener('click', () => {
    resetForm();
    networkModal?.show();
  });

  refreshBtn?.addEventListener('click', () => {
    loadNetworks();
  });

  getIpBtn?.addEventListener('click', async () => {
    const ipField = fieldByName('ip_pattern');
    const networkTypeField = fieldByName('network_type');
    if (!ipField) return;

    const originalHtml = getIpBtn.innerHTML;
    getIpBtn.disabled = true;
    getIpBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Getting';

    try {
      const response = await fetch('/api/attendance/admin/request-ip', { headers: headers(false) });
      const data = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(data.message || 'Could not get current IP.');

      const currentIp = data?.data?.ip || '';
      if (!currentIp) throw new Error('Current IP was not available.');

      ipField.value = currentIp;
      if (networkTypeField && !networkTypeField.value) {
        networkTypeField.value = 'public_ip';
      }

      const hint = data?.data?.hint ? ` ${data.data.hint}` : '';
      Swal.fire({
        icon: 'success',
        title: 'IP captured',
        text: `Current request IP: ${currentIp}.${hint}`,
        timer: 2800,
        showConfirmButton: false
      });
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Unable to get IP', text: error.message });
    } finally {
      getIpBtn.disabled = false;
      getIpBtn.innerHTML = originalHtml;
    }
  });

  form?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!state.activeBranch) return;

    const payload = payloadFromForm();
    const isEdit = !!state.editingId;
    const endpoint = isEdit
      ? `/api/attendance/admin/branches/${state.activeBranch.id}/networks/${state.editingId}`
      : `/api/attendance/admin/branches/${state.activeBranch.id}/networks`;

    try {
      const response = await fetch(endpoint, {
        method: isEdit ? 'PATCH' : 'POST',
        headers: headers(true),
        body: JSON.stringify(payload)
      });
      const data = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(data.message || 'Could not save allowed network.');
      networkModal?.hide();
      Swal.fire({ icon: 'success', title: 'Saved', text: data.message || 'Allowed network saved successfully.', timer: 1500, showConfirmButton: false });
      loadNetworks();
    } catch (error) {
      Swal.fire({ icon: 'error', title: 'Save failed', text: error.message });
    }
  });

  modalEl?.addEventListener('hidden.bs.modal', resetForm);

  loadBranches();
})();
</script>
@endpush
