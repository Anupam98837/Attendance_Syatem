@php
  $resourcePageTitle = $resourcePageTitle ?? 'Manage Resource';
  $resourcePageLead = $resourcePageLead ?? 'Maintain attendance setup data.';
  $resourceEndpoint = $resourceEndpoint ?? '/api/attendance/admin/departments';
  $resourceSingular = $resourceSingular ?? 'Record';
  $resourcePlural = $resourcePlural ?? 'Records';
  $resourceFields = $resourceFields ?? [];
  $resourceColumns = $resourceColumns ?? [];
  $resourceStatusOptions = $resourceStatusOptions ?? ['active' => 'Active', 'inactive' => 'Inactive'];
  $resourceDefaults = $resourceDefaults ?? [];
@endphp

@push('styles')
<style>
.att-manage{display:grid;gap:18px}
.att-page-head{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:16px;
  padding:22px;
  border:1px solid var(--line-strong);
  border-radius:24px;
  background:linear-gradient(140deg, rgba(15,118,110,.08), rgba(217,119,6,.10));
  box-shadow:var(--shadow-1);
}
.att-page-head h1{margin:0 0 8px;font-size:28px}
.att-page-head p{margin:0;color:var(--muted-color);line-height:1.75;max-width:70ch}
.att-page-head .badge{
  background:rgba(255,255,255,.8);
  color:var(--primary-color);
  border:1px solid rgba(15,118,110,.18);
  border-radius:999px;
  padding:9px 12px;
  font-size:12px;
}
.att-toolbar,
.att-table-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  box-shadow:var(--shadow-1);
}
.att-toolbar{padding:16px}
.att-toolbar-row{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  align-items:center;
}
.att-toolbar-row .form-control,
.att-toolbar-row .form-select{max-width:220px}
.att-table-card{overflow:hidden}
.att-table-card .table{margin:0}
.att-table-card .table thead th{
  background:var(--surface-3);
  color:var(--ink);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  border-bottom:1px solid var(--line-strong);
  white-space:nowrap;
}
.att-table-card .table tbody td{
  vertical-align:middle;
  border-top:1px solid var(--line-soft);
}
.att-table-card .table tbody tr:hover{background:var(--surface-2)}
.att-card-footer{
  display:flex;
  flex-wrap:wrap;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:14px 16px;
  border-top:1px solid var(--line-soft);
}
.att-empty{
  padding:42px 18px;
  text-align:center;
  color:var(--muted-color);
}
.att-inline-badge{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:6px 10px;
  border-radius:999px;
  border:1px solid var(--line-soft);
  background:var(--surface-2);
  font-size:12px;
  font-weight:700;
  color:var(--ink);
}
.att-actions{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  justify-content:flex-end;
}
.att-modal-grid{
  display:grid;
  grid-template-columns:repeat(2, minmax(0, 1fr));
  gap:14px;
}
.att-modal-grid .full{grid-column:1 / -1}
.att-modal-grid .form-select[multiple]{
  min-height:132px;
}
.att-help{
  font-size:12px;
  color:var(--muted-color);
  margin-top:6px;
}
.att-req{
  color:#dc2626;
  font-weight:800;
  margin-left:3px;
}
.att-json-chip{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:4px 8px;
  border-radius:999px;
  background:var(--surface-2);
  border:1px solid var(--line-soft);
  font-size:11px;
  color:var(--muted-color);
  margin:2px 6px 2px 0;
}
@media (max-width: 767.98px){
  .att-page-head{padding:18px}
  .att-modal-grid{grid-template-columns:1fr}
}
</style>
@endpush

<div class="att-manage">
  <section class="att-page-head">
    <div>
      <span class="badge"><i class="fa-solid fa-gear me-1"></i>Attendance Setup Module</span>
      <h1>{{ $resourcePageTitle }}</h1>
      <p>{{ $resourcePageLead }}</p>
    </div>
    <button type="button" class="btn btn-primary" id="resourceAddBtn">
      <i class="fa-solid fa-plus me-1"></i>Add {{ $resourceSingular }}
    </button>
  </section>

  <section class="att-toolbar">
    <div class="att-toolbar-row">
      <div>
        <label class="small text-muted d-block mb-1">Search</label>
        <input id="resourceSearch" type="search" class="form-control" placeholder="Search {{ strtolower($resourcePlural) }}...">
      </div>
      <div>
        <label class="small text-muted d-block mb-1">Status</label>
        <select id="resourceStatusFilter" class="form-select">
          <option value="">All</option>
          @foreach ($resourceStatusOptions as $statusKey => $statusLabel)
            <option value="{{ $statusKey }}">{{ $statusLabel }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="small text-muted d-block mb-1">Per Page</label>
        <select id="resourcePerPage" class="form-select">
          <option value="10">10</option>
          <option value="20" selected>20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div class="ms-auto d-flex align-items-end gap-2">
        <button type="button" class="btn btn-light" id="resourceResetBtn">
          <i class="fa-solid fa-rotate-left me-1"></i>Reset
        </button>
        <button type="button" class="btn btn-primary" id="resourceRefreshBtn">
          <i class="fa-solid fa-arrows-rotate me-1"></i>Refresh
        </button>
      </div>
    </div>
  </section>

  <section class="att-table-card">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            @foreach ($resourceColumns as $column)
              <th>{{ $column['label'] }}</th>
            @endforeach
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="resourceTbody">
          <tr>
            <td colspan="{{ count($resourceColumns) + 1 }}" class="att-empty">
              <i class="fa-solid fa-spinner fa-spin me-2"></i>Loading {{ strtolower($resourcePlural) }}...
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="att-card-footer">
      <div class="small text-muted" id="resourceInfo">—</div>
      <ul class="pagination mb-0" id="resourcePager"></ul>
    </div>
  </section>
</div>

<div class="modal fade" id="resourceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resourceModalTitle">Add {{ $resourceSingular }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="resourceForm">
        <div class="modal-body">
          <div class="att-modal-grid">
            @foreach ($resourceFields as $field)
              @php
                $fieldName = $field['name'];
                $fieldType = $field['type'] ?? 'text';
                $isFull = !empty($field['full']);
                $isRequired = !empty($field['required']);
                $placeholder = $field['placeholder']
                  ?? match ($fieldType) {
                    'textarea' => 'Enter ' . strtolower($field['label']),
                    'select' => 'Select ' . strtolower($field['label']),
                    'multiselect' => 'Select one or more options',
                    'date' => 'Select ' . strtolower($field['label']),
                    'time' => 'Select ' . strtolower($field['label']),
                    'number' => 'Enter ' . strtolower($field['label']),
                    default => 'Enter ' . strtolower($field['label']),
                  };
              @endphp
              <div class="{{ $isFull ? 'full' : '' }}">
                <label class="form-label">{{ $field['label'] }}@if($isRequired)<span class="att-req">*</span>@endif</label>
                @if ($fieldType === 'textarea')
                  <textarea class="form-control resource-field" rows="{{ $field['rows'] ?? 3 }}" data-field="{{ $fieldName }}" placeholder="{{ $placeholder }}" @if($isRequired) required @endif></textarea>
                @elseif ($fieldType === 'select')
                  <select class="form-select resource-field" data-field="{{ $fieldName }}" @if (!empty($field['source'])) data-source="{{ $field['source'] }}" @endif @if($isRequired) required @endif>
                    <option value="">Select {{ strtolower($field['label']) }}</option>
                    @foreach (($field['options'] ?? []) as $optionValue => $optionLabel)
                      <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                    @endforeach
                  </select>
                @elseif ($fieldType === 'multiselect')
                  <select class="form-select resource-field" data-field="{{ $fieldName }}" multiple @if($isRequired) required @endif>
                    @foreach (($field['options'] ?? []) as $optionValue => $optionLabel)
                      <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                    @endforeach
                  </select>
                @elseif ($fieldType === 'checkbox')
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input resource-field" type="checkbox" role="switch" data-field="{{ $fieldName }}">
                    <label class="form-check-label">{{ $field['help'] ?? ('Enable ' . strtolower($field['label'])) }}</label>
                  </div>
                @else
                  <input
                    type="{{ $fieldType }}"
                    class="form-control resource-field"
                    data-field="{{ $fieldName }}"
                    placeholder="{{ $placeholder }}"
                    @if (!empty($field['step'])) step="{{ $field['step'] }}" @endif
                    @if (!empty($field['min'])) min="{{ $field['min'] }}" @endif
                    @if (!empty($field['max'])) max="{{ $field['max'] }}" @endif
                    @if($isRequired) required @endif
                  >
                @endif
                @if (!empty($field['help']) && $fieldType !== 'checkbox')
                  <div class="att-help">{{ $field['help'] }}</div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="resourceSaveBtn">
            <i class="fa-solid fa-floppy-disk me-1"></i>Save {{ $resourceSingular }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
(() => {
  const config = {
    endpoint: @json($resourceEndpoint),
    singular: @json($resourceSingular),
    plural: @json($resourcePlural),
    fields: @json($resourceFields),
    columns: @json($resourceColumns),
    defaults: @json($resourceDefaults),
  };

  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) {
    window.location.replace('/');
    return;
  }

  const state = { page: 1, per_page: 20, q: '', status: '' };
  let editingId = null;
  let relationOptionsLoaded = false;

  const els = {
    search: document.getElementById('resourceSearch'),
    status: document.getElementById('resourceStatusFilter'),
    perPage: document.getElementById('resourcePerPage'),
    tbody: document.getElementById('resourceTbody'),
    info: document.getElementById('resourceInfo'),
    pager: document.getElementById('resourcePager'),
    modalTitle: document.getElementById('resourceModalTitle'),
    form: document.getElementById('resourceForm'),
    addBtn: document.getElementById('resourceAddBtn'),
    refreshBtn: document.getElementById('resourceRefreshBtn'),
    resetBtn: document.getElementById('resourceResetBtn'),
  };

  const resourceModal = new bootstrap.Modal(document.getElementById('resourceModal'));

  function authHeaders(extra = {}) {
    return Object.assign({
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }, extra);
  }

  async function loadRelationOptions() {
    const relationFields = config.fields.filter((field) => field.type === 'select' && field.source);
    if (!relationFields.length) {
      relationOptionsLoaded = true;
      return;
    }

    await Promise.all(relationFields.map(async (field) => {
      const select = els.form.querySelector(`[data-field="${field.name}"]`);
      if (!select) return;

      const response = await fetch(`/api/attendance/admin/${field.source}?per_page=200`, {
        headers: authHeaders({ 'Content-Type': undefined })
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || `Could not load ${field.label}.`);

      const rows = Array.isArray(data.data) ? data.data : [];
      const currentValue = select.value;
      const defaultOption = `<option value="">Select ${String(field.label || '').toLowerCase()}</option>`;
      select.innerHTML = defaultOption + rows.map((row) => {
        const value = row.id ?? row.uuid ?? '';
        const label = row.code ? `${row.name} (${row.code})` : (row.name || row.uuid || value);
        return `<option value="${escapeHtml(String(value))}">${escapeHtml(String(label))}</option>`;
      }).join('');
      select.value = currentValue || '';
    }));

    relationOptionsLoaded = true;
  }

  function queryString() {
    const params = new URLSearchParams();
    params.set('page', String(state.page));
    params.set('per_page', String(state.per_page));
    if (state.q) params.set('q', state.q);
    if (state.status) params.set('status', state.status);
    return params.toString();
  }

  function badge(value) {
    if (value === null || value === undefined || value === '') return '<span class="text-muted">—</span>';
    return `<span class="att-inline-badge">${escapeHtml(String(value))}</span>`;
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function formatCell(row, column) {
    const raw = row[column.key];
    const type = column.type || 'text';

    if (type === 'status') {
      return badge(raw || 'active');
    }

    if (type === 'bool') {
      return badge(raw ? 'Yes' : 'No');
    }

    if (type === 'array') {
      const items = Array.isArray(raw) ? raw : parseMaybeJson(raw);
      if (!items.length) return '<span class="text-muted">—</span>';
      return items.map((item) => `<span class="att-json-chip">${escapeHtml(String(item))}</span>`).join('');
    }

    if (type === 'json') {
      const data = parseMaybeJson(raw);
      if (!data.length) return '<span class="text-muted">—</span>';
      return data.map((item) => `<span class="att-json-chip">${escapeHtml(String(item))}</span>`).join('');
    }

    if (raw === null || raw === undefined || raw === '') {
      return '<span class="text-muted">—</span>';
    }

    return escapeHtml(String(raw));
  }

  function parseMaybeJson(value) {
    if (!value) return [];
    if (Array.isArray(value)) return value;
    try {
      const parsed = JSON.parse(value);
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return String(value).split(',').map((item) => item.trim()).filter(Boolean);
    }
  }

  function renderRows(rows) {
    if (!rows.length) {
      els.tbody.innerHTML = `<tr><td colspan="${config.columns.length + 1}" class="att-empty"><i class="fa-regular fa-folder-open me-2"></i>No ${config.plural.toLowerCase()} found.</td></tr>`;
      return;
    }

    const rowIdentifier = (row) => row.uuid || row.id || row.code || row.name || '';

    els.tbody.innerHTML = rows.map((row) => `
      <tr>
        ${config.columns.map((column) => `<td>${formatCell(row, column)}</td>`).join('')}
        <td class="text-end">
          <div class="att-actions">
            <button type="button" class="btn btn-sm btn-light js-edit-resource" data-id="${escapeHtml(String(rowIdentifier(row)))}">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button type="button" class="btn btn-sm btn-light js-delete-resource" data-id="${escapeHtml(String(rowIdentifier(row)))}">
              <i class="fa-solid fa-trash"></i>
            </button>
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

  async function loadRows() {
    els.tbody.innerHTML = `<tr><td colspan="${config.columns.length + 1}" class="att-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading ${config.plural.toLowerCase()}...</td></tr>`;
    try {
      const response = await fetch(`${config.endpoint}?${queryString()}`, {
        headers: authHeaders({ 'Content-Type': undefined })
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || `Could not load ${config.plural.toLowerCase()}.`);
      renderRows(data.data || []);
      renderPager(data.pagination || {});
      els.info.textContent = `Showing ${(data.data || []).length} of ${data.pagination?.total || 0} ${config.plural.toLowerCase()}.`;
    } catch (error) {
      els.tbody.innerHTML = `<tr><td colspan="${config.columns.length + 1}" class="att-empty text-danger">${escapeHtml(error.message)}</td></tr>`;
    }
  }

  function applyValue(field, value) {
    const input = els.form.querySelector(`[data-field="${field.name}"]`);
    if (!input) return;
    const type = field.type || 'text';

    if (type === 'checkbox') {
      input.checked = Boolean(value);
      return;
    }

    if (type === 'multiselect') {
      const items = parseMaybeJson(value);
      Array.from(input.options).forEach((option) => {
        option.selected = items.includes(option.value);
      });
      return;
    }

    if (type === 'array' || type === 'json') {
      const items = parseMaybeJson(value);
      input.value = items.join(', ');
      return;
    }

    input.value = value ?? '';
  }

  function resetForm() {
    editingId = null;
    els.form.reset();
    config.fields.forEach((field) => {
      const defaultValue = config.defaults[field.name];
      applyValue(field, defaultValue ?? '');
    });
  }

  async function openEdit(id) {
    if (!id) {
      Swal.fire('Unable to edit', 'Record identifier is missing for this row.', 'error');
      return;
    }
    try {
      if (!relationOptionsLoaded) {
        await loadRelationOptions();
      }
      const response = await fetch(`${config.endpoint}/${encodeURIComponent(id)}`, {
        headers: authHeaders({ 'Content-Type': undefined })
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || `Could not load ${config.singular.toLowerCase()}.`);

      resetForm();
      editingId = id;
      els.modalTitle.textContent = `Edit ${config.singular}`;
      config.fields.forEach((field) => applyValue(field, data.data?.[field.name]));
      resourceModal.show();
    } catch (error) {
      Swal.fire('Unable to edit', error.message, 'error');
    }
  }

  function collectPayload() {
    const payload = {};
    config.fields.forEach((field) => {
      const input = els.form.querySelector(`[data-field="${field.name}"]`);
      if (!input) return;
      const type = field.type || 'text';

      if (type === 'checkbox') {
        payload[field.name] = input.checked;
        return;
      }

      if (type === 'multiselect') {
        payload[field.name] = Array.from(input.selectedOptions).map((option) => option.value).filter(Boolean);
        return;
      }

      const value = input.value?.trim?.() ?? input.value;
      if (value === '') {
        payload[field.name] = null;
        return;
      }

      if (type === 'number') {
        payload[field.name] = Number(value);
      } else if (type === 'array' || type === 'json') {
        payload[field.name] = value.split(',').map((item) => item.trim()).filter(Boolean);
      } else {
        payload[field.name] = value;
      }
    });
    return payload;
  }

  async function saveResource(event) {
    event.preventDefault();

    const payload = collectPayload();
    const method = editingId ? 'PATCH' : 'POST';
    const url = editingId ? `${config.endpoint}/${encodeURIComponent(editingId)}` : config.endpoint;

    try {
      const response = await fetch(url, {
        method,
        headers: authHeaders(),
        body: JSON.stringify(payload)
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Save failed.');
      resourceModal.hide();
      await loadRows();
      Swal.fire('Saved', data.message || `${config.singular} saved successfully.`, 'success');
    } catch (error) {
      Swal.fire('Unable to save', error.message, 'error');
    }
  }

  async function deleteResource(id) {
    if (!id) {
      Swal.fire('Unable to delete', 'Record identifier is missing for this row.', 'error');
      return;
    }
    const result = await Swal.fire({
      title: `Delete ${config.singular}?`,
      text: `This will remove the selected ${config.singular.toLowerCase()} from the active list.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      confirmButtonColor: '#dc5a33'
    });

    if (!result.isConfirmed) return;

    try {
      const response = await fetch(`${config.endpoint}/${encodeURIComponent(id)}`, {
        method: 'DELETE',
        headers: authHeaders({ 'Content-Type': undefined })
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Delete failed.');
      await loadRows();
      Swal.fire('Deleted', data.message || `${config.singular} deleted successfully.`, 'success');
    } catch (error) {
      Swal.fire('Unable to delete', error.message, 'error');
    }
  }

  els.addBtn.addEventListener('click', () => {
    resetForm();
    els.modalTitle.textContent = `Add ${config.singular}`;
    Promise.resolve(relationOptionsLoaded ? null : loadRelationOptions()).then(() => {
      resourceModal.show();
    }).catch((error) => {
      Swal.fire('Unable to load form', error.message, 'error');
    });
  });

  els.refreshBtn.addEventListener('click', loadRows);
  els.resetBtn.addEventListener('click', () => {
    state.page = 1;
    state.q = '';
    state.status = '';
    state.per_page = 20;
    els.search.value = '';
    els.status.value = '';
    els.perPage.value = '20';
    loadRows();
  });
  els.search.addEventListener('input', () => {
    state.page = 1;
    state.q = els.search.value.trim();
    loadRows();
  });
  els.status.addEventListener('change', () => {
    state.page = 1;
    state.status = els.status.value;
    loadRows();
  });
  els.perPage.addEventListener('change', () => {
    state.page = 1;
    state.per_page = Number(els.perPage.value || 20);
    loadRows();
  });
  els.form.addEventListener('submit', saveResource);
  els.pager.addEventListener('click', (event) => {
    const target = event.target.closest('[data-page]');
    if (!target || target.parentElement.classList.contains('disabled')) return;
    state.page = Number(target.dataset.page || '1');
    loadRows();
  });
  els.tbody.addEventListener('click', (event) => {
    const editBtn = event.target.closest('.js-edit-resource');
    const deleteBtn = event.target.closest('.js-delete-resource');
    if (editBtn) openEdit(editBtn.dataset.id);
    if (deleteBtn) deleteResource(deleteBtn.dataset.id);
  });

  Promise.resolve(loadRelationOptions()).catch(() => {
    // Keep page usable even if relation dropdowns fail; modal load will retry.
  }).finally(() => {
    resetForm();
    loadRows();
  });
})();
</script>
@endpush
