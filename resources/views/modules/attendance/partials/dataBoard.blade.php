@php
  $boardTitle = $boardTitle ?? 'Data Board';
  $boardLead = $boardLead ?? 'Operational attendance data.';
  $boardEndpoint = $boardEndpoint ?? '/api/attendance/hr/attendance';
  $boardColumns = $boardColumns ?? [];
  $boardFilters = $boardFilters ?? [];
  $boardDefaultQuery = $boardDefaultQuery ?? [];
  $boardActions = $boardActions ?? [];
@endphp

@push('styles')
<style>
.att-board{display:grid;gap:18px}
.att-board-head,
.att-board-toolbar,
.att-board-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:24px;
  box-shadow:var(--shadow-1);
}
.att-board-head{
  padding:24px;
  background:linear-gradient(140deg, rgba(15,118,110,.08), rgba(217,119,6,.10));
}
.att-board-head h1{margin:0 0 8px;font-size:30px}
.att-board-head p{margin:0;color:var(--muted-color);line-height:1.75;max-width:78ch}
.att-board-toolbar{padding:16px}
.att-board-toolbar-row{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  align-items:end;
}
.att-board-table thead th{
  background:var(--surface-3);
  color:var(--ink);
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  white-space:nowrap;
}
.att-board-table tbody td{border-top:1px solid var(--line-soft);vertical-align:top}
.att-board-table tbody tr:hover{background:var(--surface-2)}
.att-board-card{overflow:hidden}
.att-board-foot{
  display:flex;
  flex-wrap:wrap;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:14px 16px;
  border-top:1px solid var(--line-soft);
}
.att-board-empty{padding:42px 18px;text-align:center;color:var(--muted-color)}
.att-board-actions{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  justify-content:flex-end;
}
.att-pill{
  display:inline-flex;
  align-items:center;
  gap:6px;
  padding:5px 10px;
  border-radius:999px;
  font-size:11px;
  font-weight:800;
  letter-spacing:.04em;
  text-transform:uppercase;
}
.att-pill.is-approved,
.att-pill.is-present,
.att-pill.is-synced{background:rgba(22,163,74,.12);color:#15803d}
.att-pill.is-pending-approval,
.att-pill.is-pending-sync,
.att-pill.is-processing,
.att-pill.is-late,
.att-pill.is-offline{background:rgba(245,158,11,.14);color:#b45309}
.att-pill.is-rejected,
.att-pill.is-absent,
.att-pill.is-sync-failed{background:rgba(220,38,38,.12);color:#b91c1c}
.att-pill.is-half-day,
.att-pill.is-manual,
.att-pill.is-default{background:rgba(59,130,246,.11);color:#1d4ed8}
.att-loc{
  display:grid;
  gap:4px;
}
.att-loc b{
  font-size:13px;
  color:var(--ink);
}
.att-loc small{
  color:var(--muted-color);
  line-height:1.5;
}
</style>
@endpush

<div class="att-board">
  <section class="att-board-head">
    <span class="att-inline-badge"><i class="fa-solid fa-chart-column"></i>Attendance Operations</span>
    <h1>{{ $boardTitle }}</h1>
    <p>{{ $boardLead }}</p>
  </section>

  <section class="att-board-toolbar">
    <div class="att-board-toolbar-row">
      @foreach ($boardFilters as $filter)
        <div>
          <label class="small text-muted d-block mb-1">{{ $filter['label'] }}</label>
          @if (in_array(($filter['type'] ?? 'text'), ['select', 'relation-select'], true))
            <select class="form-select board-filter" data-key="{{ $filter['key'] }}" @if (!empty($filter['source'])) data-source="{{ $filter['source'] }}" @endif>
              @foreach (($filter['options'] ?? []) as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
              @endforeach
            </select>
          @else
            <input type="{{ $filter['type'] ?? 'text' }}" class="form-control board-filter" data-key="{{ $filter['key'] }}" placeholder="{{ $filter['placeholder'] ?? '' }}">
          @endif
        </div>
      @endforeach
      <div class="ms-auto d-flex align-items-end gap-2">
        <button type="button" class="btn btn-light" id="boardResetBtn"><i class="fa-solid fa-rotate-left me-1"></i>Reset</button>
        <button type="button" class="btn btn-primary" id="boardRefreshBtn"><i class="fa-solid fa-arrows-rotate me-1"></i>Refresh</button>
      </div>
    </div>
  </section>

  <section class="att-board-card">
    <div class="table-responsive">
      <table class="table att-board-table align-middle mb-0">
        <thead>
          <tr>
            @foreach ($boardColumns as $column)
              <th>{{ $column['label'] }}</th>
            @endforeach
            @if (!empty($boardActions))
              <th class="text-end">Actions</th>
            @endif
          </tr>
        </thead>
        <tbody id="boardTbody">
          <tr>
            <td colspan="{{ count($boardColumns) + (!empty($boardActions) ? 1 : 0) }}" class="att-board-empty">
              <i class="fa-solid fa-spinner fa-spin me-2"></i>Loading data...
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="att-board-foot">
      <div class="small text-muted" id="boardInfo">—</div>
      <ul class="pagination mb-0" id="boardPager"></ul>
    </div>
  </section>
</div>

@push('scripts')
<script>
(() => {
  const config = {
    endpoint: @json($boardEndpoint),
    columns: @json($boardColumns),
    filters: @json($boardFilters),
    defaults: @json($boardDefaultQuery),
    actions: @json($boardActions),
  };

  const token = sessionStorage.getItem('token') || localStorage.getItem('token');
  if (!token) {
    window.location.replace('/');
    return;
  }

  const state = Object.assign({ page: 1, per_page: 20 }, config.defaults || {});
  const tbody = document.getElementById('boardTbody');
  const pager = document.getElementById('boardPager');
  const info = document.getElementById('boardInfo');
  const filters = Array.from(document.querySelectorAll('.board-filter'));
  const companyTz = localStorage.getItem('companyTz') || Intl.DateTimeFormat().resolvedOptions().timeZone;
  let relationFiltersLoaded = false;

  function headers(json = false) {
    return json
      ? { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json' }
      : { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };
  }

  async function loadRelationFilterOptions() {
    const relationFilters = filters.filter((filter) => filter.dataset.source);
    if (!relationFilters.length) {
      relationFiltersLoaded = true;
      return;
    }

    await Promise.all(relationFilters.map(async (filterEl) => {
      const source = filterEl.dataset.source;
      const response = await fetch(`/api/attendance/admin/${source}?per_page=200`, { headers: headers(false) });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || `Could not load ${source}.`);

      const rows = Array.isArray(data.data) ? data.data : [];
      const existingValue = state[filterEl.dataset.key] ?? filterEl.value ?? '';
      const placeholder = filterEl.dataset.placeholder || `All ${source.replace(/-/g, ' ')}`;
      filterEl.innerHTML = `<option value="">${escapeHtml(placeholder)}</option>` + rows.map((row) => {
        const value = row.id ?? row.uuid ?? '';
        const label = row.code ? `${row.name} (${row.code})` : (row.name || row.uuid || value);
        return `<option value="${escapeHtml(String(value))}">${escapeHtml(String(label))}</option>`;
      }).join('');
      filterEl.value = existingValue;
    }));

    relationFiltersLoaded = true;
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function buildQuery() {
    const params = new URLSearchParams();
    Object.entries(state).forEach(([key, value]) => {
      if (value !== '' && value !== null && value !== undefined) params.set(key, String(value));
    });
    return params.toString();
  }

  function pad2(v) {
    return String(v).padStart(2, '0');
  }

  function monthLabel(y, m, d) {
    const dt = new Date(Date.UTC(Number(y), Number(m) - 1, Number(d), 12, 0, 0));
    return dt.toLocaleDateString([], { year: 'numeric', month: 'short', day: '2-digit', timeZone: 'UTC' });
  }

  function formatClockParts(hh, mm) {
    const hour24 = Number(hh || 0);
    const hour12 = hour24 % 12 || 12;
    return `${pad2(hour12)}:${pad2(mm || 0)} ${hour24 >= 12 ? 'PM' : 'AM'}`;
  }

  function parseServerDate(value) {
    if (!value) return null;
    if (value instanceof Date) return value;
    const raw = String(value).trim();
    if (!raw) return null;
    if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return { raw, kind: 'sql_date' };
    if (/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}(:\d{2})?$/.test(raw)) return { raw, kind: 'sql_datetime' };
    if (/^\d{2}:\d{2}(:\d{2})?$/.test(raw)) return { raw, kind: 'sql_time' };
    const parsed = new Date(raw);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
  }

  function formatSqlDate(value) {
    const match = String(value || '').trim().match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!match) return null;
    return monthLabel(match[1], match[2], match[3]);
  }

  function formatSqlDateTime(value) {
    const match = String(value || '').trim().match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?$/);
    if (!match) return null;
    return {
      date: monthLabel(match[1], match[2], match[3]),
      time: formatClockParts(match[4], match[5]),
    };
  }

  function formatDate(value) {
    const parsed = parseServerDate(value);
    if (!parsed) return null;
    if (parsed?.kind === 'sql_date') return formatSqlDate(parsed.raw);
    if (parsed?.kind === 'sql_datetime') return formatSqlDateTime(parsed.raw)?.date || null;
    return parsed.toLocaleDateString([], { year: 'numeric', month: 'short', day: '2-digit', timeZone: companyTz });
  }

  function formatDateTime(value) {
    const parsed = parseServerDate(value);
    if (!parsed) return null;
    if (parsed?.kind === 'sql_datetime') {
      const sql = formatSqlDateTime(parsed.raw);
      return sql ? `${sql.date}, ${sql.time}` : null;
    }
    if (parsed?.kind === 'sql_time') {
      const tm = String(parsed.raw).trim().match(/^(\d{2}):(\d{2})(?::(\d{2}))?$/);
      return tm ? formatClockParts(tm[1], tm[2]) : null;
    }
    return parsed.toLocaleString([], {
      year: 'numeric',
      month: 'short',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
      timeZone: companyTz,
    });
  }

  function formatMinutes(value) {
    const minutes = Number(value || 0);
    if (!Number.isFinite(minutes) || minutes <= 0) return '0m';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (!hrs) return `${mins}m`;
    if (!mins) return `${hrs}h`;
    return `${hrs}h ${mins}m`;
  }

  function badgeHtml(value) {
    const label = String(value).replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
    const normalized = String(value).toLowerCase().replace(/_/g, '-');
    let cls = 'is-default';
    if (['approved', 'present', 'synced'].includes(normalized)) cls = 'is-approved';
    else if (['pending-approval', 'pending-sync', 'processing', 'late', 'offline', 'offline-pending'].includes(normalized)) cls = 'is-pending-approval';
    else if (['rejected', 'absent', 'sync-failed', 'duplicate-rejected'].includes(normalized)) cls = 'is-rejected';
    else if (['half-day', 'manual', 'manual-corrected'].includes(normalized)) cls = 'is-half-day';
    return `<span class="att-pill ${cls}">${escapeHtml(label)}</span>`;
  }

  function cellValue(row, column) {
    const raw = row[column.key];
    if (column.type === 'date') {
      if (raw === null || raw === undefined || raw === '') return '<span class="text-muted">—</span>';
      return escapeHtml(formatDate(raw) || String(raw));
    }
    if (column.type === 'datetime' || column.type === 'time') {
      if (raw === null || raw === undefined || raw === '') return '<span class="text-muted">—</span>';
      return escapeHtml(formatDateTime(raw) || String(raw));
    }
    if (column.type === 'duration') {
      if (raw === null || raw === undefined || raw === '') return '<span class="text-muted">—</span>';
      return escapeHtml(formatMinutes(raw));
    }
    if (column.type === 'badge') {
      if (raw === null || raw === undefined || raw === '') return '<span class="text-muted">—</span>';
      return badgeHtml(raw);
    }
    if (column.type === 'location') {
      const lat = row[column.latKey || 'latitude'];
      const lng = row[column.lngKey || 'longitude'];
      const coord = lat !== null && lat !== undefined && lng !== null && lng !== undefined
        ? `${Number(lat).toFixed(5)}, ${Number(lng).toFixed(5)}`
        : '';
      if (!raw && !coord) return '<span class="text-muted">—</span>';
      return `<div class="att-loc"><b>${escapeHtml(String(raw || coord))}</b>${raw && coord ? `<small>${escapeHtml(coord)}</small>` : ''}</div>`;
    }
    if (raw === null || raw === undefined || raw === '') return '<span class="text-muted">—</span>';
    if (/(^|_)(status|mode|type)$/.test(String(column.key)) || column.key === 'approval_status' || column.key === 'sync_status') {
      return badgeHtml(raw);
    }
    if (/_time$/.test(String(column.key)) && String(raw).includes('-')) {
      return escapeHtml(formatDateTime(raw) || String(raw));
    }
    if (/_date$/.test(String(column.key))) {
      return escapeHtml(formatDate(raw) || String(raw));
    }
    if (/_minutes$/.test(String(column.key))) {
      return escapeHtml(formatMinutes(raw));
    }
    return escapeHtml(String(raw));
  }

  function actionButtons(row) {
    if (!config.actions.length) return '';
    return `
      <div class="att-board-actions">
        ${config.actions.map((action) => `
          <button type="button" class="btn btn-sm ${action.btn || 'btn-light'} js-board-action" data-action="${action.type}" data-id="${row[action.idKey || 'id']}">
            <i class="${action.icon}"></i>
          </button>
        `).join('')}
      </div>
    `;
  }

  function renderRows(rows) {
    const colspan = config.columns.length + (config.actions.length ? 1 : 0);
    if (!rows.length) {
      tbody.innerHTML = `<tr><td colspan="${colspan}" class="att-board-empty"><i class="fa-regular fa-folder-open me-2"></i>No data found.</td></tr>`;
      return;
    }

    tbody.innerHTML = rows.map((row) => `
      <tr>
        ${config.columns.map((column) => `<td>${cellValue(row, column)}</td>`).join('')}
        ${config.actions.length ? `<td class="text-end">${actionButtons(row)}</td>` : ''}
      </tr>
    `).join('');
  }

  function renderPager(pagination) {
    if (!pagination || !pagination.last_page) {
      pager.innerHTML = '';
      return;
    }
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
    pager.innerHTML = items.join('');
  }

  async function loadBoard() {
    const colspan = config.columns.length + (config.actions.length ? 1 : 0);
    tbody.innerHTML = `<tr><td colspan="${colspan}" class="att-board-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Loading data...</td></tr>`;
    try {
      const response = await fetch(`${config.endpoint}?${buildQuery()}`, { headers: headers(false) });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Could not load data.');
      const rows = data.data || [];
      renderRows(rows);
      renderPager(data.pagination || {});
      if (data.pagination?.total !== undefined) {
        info.textContent = `Showing ${rows.length} of ${data.pagination.total} records.`;
      } else if (data.summary?.count !== undefined) {
        info.textContent = `Loaded ${data.summary.count} records.`;
      } else {
        info.textContent = `Loaded ${rows.length} records.`;
      }
    } catch (error) {
      tbody.innerHTML = `<tr><td colspan="${colspan}" class="att-board-empty text-danger">${escapeHtml(error.message)}</td></tr>`;
    }
  }

  async function handleAction(type, id) {
    if (type === 'approve-approval' || type === 'reject-approval') {
      const decision = type === 'approve-approval' ? 'approve' : 'reject';
      const { value: remarks } = await Swal.fire({
        title: `${decision === 'approve' ? 'Approve' : 'Reject'} attendance`,
        input: 'text',
        inputLabel: 'Remarks',
        inputPlaceholder: 'Optional remarks',
        showCancelButton: true
      });
      if (remarks === undefined) return;
      const response = await fetch(`/api/attendance/hr/approvals/${id}/decision`, {
        method: 'POST',
        headers: headers(true),
        body: JSON.stringify({ decision, remarks })
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Decision failed.');
      Swal.fire('Updated', data.message || 'Approval updated successfully.', 'success');
      loadBoard();
      return;
    }

    if (type === 'approve-leave' || type === 'reject-leave') {
      const decision = type === 'approve-leave' ? 'approve' : 'reject';
      const { value: remarks } = await Swal.fire({
        title: `${decision === 'approve' ? 'Approve' : 'Reject'} leave`,
        input: 'text',
        inputLabel: 'Remarks',
        inputPlaceholder: 'Optional remarks',
        showCancelButton: true
      });
      if (remarks === undefined) return;
      const response = await fetch(`/api/attendance/hr/leaves/${id}/decision`, {
        method: 'POST',
        headers: headers(true),
        body: JSON.stringify({ decision, remarks })
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Leave decision failed.');
      Swal.fire('Updated', data.message || 'Leave updated successfully.', 'success');
      loadBoard();
    }
  }

  filters.forEach((filter) => {
    const key = filter.dataset.key;
    filter.value = state[key] ?? '';
    filter.addEventListener(filter.type === 'search' || filter.type === 'text' ? 'input' : 'change', () => {
      state.page = 1;
      state[key] = filter.value.trim();
      loadBoard();
    });
  });

  document.getElementById('boardRefreshBtn').addEventListener('click', loadBoard);
  document.getElementById('boardResetBtn').addEventListener('click', () => {
    Object.keys(state).forEach((key) => delete state[key]);
    Object.assign(state, { page: 1, per_page: 20 }, config.defaults || {});
    filters.forEach((filter) => {
      const key = filter.dataset.key;
      filter.value = state[key] ?? '';
    });
    loadBoard();
  });
  pager.addEventListener('click', (event) => {
    const button = event.target.closest('[data-page]');
    if (!button || button.parentElement.classList.contains('disabled')) return;
    state.page = Number(button.dataset.page || '1');
    loadBoard();
  });
  tbody.addEventListener('click', async (event) => {
    const btn = event.target.closest('.js-board-action');
    if (!btn) return;
    try {
      await handleAction(btn.dataset.action, btn.dataset.id);
    } catch (error) {
      Swal.fire('Action failed', error.message, 'error');
    }
  });

  Promise.resolve(loadRelationFilterOptions()).catch(() => {
    // Keep page usable even if relation options fail to load.
  }).finally(() => {
    loadBoard();
  });
})();
</script>
@endpush
