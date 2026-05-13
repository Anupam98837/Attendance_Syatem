@php
  $boardTitle = $boardTitle ?? 'Data Board';
  $boardLead = $boardLead ?? 'Operational attendance data.';
  $boardEndpoint = $boardEndpoint ?? '/api/attendance/hr/attendance';
  $boardColumns = $boardColumns ?? [];
  $boardFilters = $boardFilters ?? [];
  $boardDefaultQuery = $boardDefaultQuery ?? [];
  $boardActions = $boardActions ?? [];
  $boardDetailEndpoint = $boardDetailEndpoint ?? '/api/attendance/hr/attendance/{id}/detail';
  $boardExportEndpoint = $boardExportEndpoint ?? null;
  $boardPrintable = $boardPrintable ?? false;
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
.att-detail{
  text-align:left;
}
.att-detail-grid,
.att-detail-proof-grid{
  display:grid;
  gap:12px;
  grid-template-columns:repeat(2, minmax(0, 1fr));
}
.att-detail-card{
  border:1px solid var(--line-soft);
  border-radius:18px;
  background:var(--surface-2);
  padding:14px;
}
.att-detail-card span{
  display:block;
  margin-bottom:6px;
  color:var(--muted-color);
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
}
.att-detail-card strong{
  color:var(--ink);
  line-height:1.65;
}
.att-detail-section{
  margin-top:18px;
}
.att-detail-section h4{
  margin:0 0 10px;
  font-size:14px;
  font-weight:800;
}
.att-detail-proof{
  border:1px solid var(--line-soft);
  border-radius:18px;
  padding:12px;
  background:var(--surface-2);
}
.att-detail-proof img{
  width:100%;
  max-height:320px;
  object-fit:contain;
  border-radius:14px;
  border:1px solid var(--line-soft);
  margin-top:10px;
  background:#fff;
}
.att-detail-table-wrap{
  overflow:auto;
  border:1px solid var(--line-soft);
  border-radius:18px;
}
.att-detail-table{
  width:100%;
  border-collapse:collapse;
  background:var(--surface);
}
.att-detail-table th,
.att-detail-table td{
  padding:10px 12px;
  border-top:1px solid var(--line-soft);
  vertical-align:top;
  font-size:13px;
}
.att-detail-table thead th{
  border-top:none;
  background:var(--surface-3);
  font-size:11px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  white-space:nowrap;
}
.att-detail-table small{
  display:block;
  color:var(--muted-color);
  line-height:1.55;
}
.att-detail-map{
  display:inline-flex;
  align-items:center;
  gap:6px;
  margin-top:8px;
  font-size:12px;
  font-weight:700;
  text-decoration:none;
}
@media (max-width: 767.98px){
  .att-detail-proof-grid{grid-template-columns:1fr}
}
/* Activity log stats grid */
.att-act-stats{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
}
.att-act-stat{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:3px;
  min-width:100px;
  padding:10px 14px;
  background:var(--surface-2,#f8f8f8);
  border:1px solid var(--border-color,#e8e8e8);
  border-radius:12px;
  text-align:center;
}
.att-act-stat i{
  font-size:16px;
  color:#7c3aed;
}
.att-act-stat .label{
  font-size:10px;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:.05em;
  color:var(--muted-color,#888);
}
.att-act-stat .val{
  font-size:13px;
  font-weight:700;
  color:var(--body-color,#222);
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
        @if ($boardPrintable)
          <button type="button" class="btn btn-light" id="boardPrintBtn"><i class="fa-solid fa-print me-1"></i>Print</button>
        @endif
        @if ($boardExportEndpoint)
          <button type="button" class="btn btn-success" id="boardExportBtn"><i class="fa-solid fa-file-excel me-1"></i>Export Excel</button>
        @endif
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
    detailEndpoint: @json($boardDetailEndpoint),
    exportEndpoint: @json($boardExportEndpoint),
    printable: @json($boardPrintable),
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
  const exportBtn = document.getElementById('boardExportBtn');
  const printBtn = document.getElementById('boardPrintBtn');
  const companyTz = localStorage.getItem('companyTz') || Intl.DateTimeFormat().resolvedOptions().timeZone;
  let relationFiltersLoaded = false;
  let lastRows = [];
  let lastSummary = {};

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
    if (!Number.isFinite(minutes)) return '0m';
    if (minutes < 0) return `-${formatMinutes(Math.abs(minutes))}`;
    if (minutes === 0) return '0m';
    const hrs = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (!hrs) return `${mins}m`;
    if (!mins) return `${hrs}h`;
    return `${hrs}h ${mins}m`;
  }

  function yesNoMaybe(value, yes = 'Yes', no = 'No', maybe = '—') {
    if (value === null || value === undefined || value === '') return maybe;
    if (value === true || value === 1 || value === '1' || String(value).toLowerCase() === 'true') return yes;
    if (value === false || value === 0 || value === '0' || String(value).toLowerCase() === 'false') return no;
    return String(value);
  }

  function locationLabel(row) {
    if (!row) return '—';
    return row.location_label
      || row.location_text
      || row.label
      || cellLocation(row.latitude, row.longitude, row.gps_accuracy_meters)
      || '—';
  }

  function cellLocation(latitude, longitude, accuracy) {
    if (latitude === null || latitude === undefined || longitude === null || longitude === undefined || latitude === '' || longitude === '') {
      return null;
    }
    const bits = [`${Number(latitude).toFixed(5)}, ${Number(longitude).toFixed(5)}`];
    if (accuracy !== null && accuracy !== undefined && accuracy !== '') bits.push(`±${Math.round(Number(accuracy))}m`);
    return bits.join(' · ');
  }

  function mapHref(latitude, longitude) {
    if (latitude === null || latitude === undefined || longitude === null || longitude === undefined || latitude === '' || longitude === '') return null;
    return `https://maps.google.com/?q=${encodeURIComponent(latitude)},${encodeURIComponent(longitude)}`;
  }
  function distanceLabel(meters) {
    const value = Number(meters || 0);
    if (!Number.isFinite(value) || value <= 0) return '0 m';
    if (value >= 1000) return `${(value / 1000).toFixed(2)} km`;
    return `${Math.round(value)} m`;
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

  function htmlToText(html) {
    const el = document.createElement('div');
    el.innerHTML = html;
    return (el.textContent || el.innerText || '').trim();
  }

  function textCellValue(row, column) {
    return htmlToText(cellValue(row, column)) || '—';
  }

  function activeFilterSummary() {
    return filters.map((filter) => {
      const value = state[filter.dataset.key];
      if (value === '' || value === null || value === undefined) return null;
      const label = filter.closest('div')?.querySelector('label')?.textContent?.trim() || filter.dataset.key;
      const selectedText = filter.tagName === 'SELECT'
        ? (filter.options[filter.selectedIndex]?.text || value)
        : value;
      return `${label}: ${selectedText}`;
    }).filter(Boolean);
  }

  async function exportBoard() {
    if (!config.exportEndpoint) return;
    const response = await fetch(`${config.exportEndpoint}?${buildQuery()}`, { headers: headers(false) });
    if (!response.ok) {
      let message = 'Could not export report.';
      try {
        const payload = await response.json();
        message = payload.message || message;
      } catch (_) {}
      throw new Error(message);
    }

    const blob = await response.blob();
    const disposition = response.headers.get('Content-Disposition') || '';
    const filenameMatch = disposition.match(/filename="?([^"]+)"?/i);
    const filename = filenameMatch?.[1] || `${config.title || 'attendance-report'}.xls`;
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url), 1500);
  }

  function printBoard() {
    if (!config.printable) return;

    const summaryBits = activeFilterSummary();
    const printWindow = window.open('', '_blank', 'noopener,width=1200,height=900');
    if (!printWindow) throw new Error('Popup blocked. Please allow popups to print this report.');

    const tableRows = lastRows.length
      ? lastRows.map((row) => `
          <tr>
            ${config.columns.map((column) => `<td>${escapeHtml(textCellValue(row, column))}</td>`).join('')}
          </tr>
        `).join('')
      : `<tr><td colspan="${config.columns.length}" style="text-align:center;color:#64748b;padding:18px;">No records available for print.</td></tr>`;

    const filterBlock = summaryBits.length
      ? `<p><strong>Filters:</strong> ${escapeHtml(summaryBits.join(' | '))}</p>`
      : '<p><strong>Filters:</strong> Default view</p>';

    printWindow.document.write(`
      <!doctype html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>${escapeHtml(@json($boardTitle))}</title>
          <style>
            body{font-family:Arial,Helvetica,sans-serif;color:#0f172a;margin:24px}
            h1{margin:0 0 8px;font-size:24px}
            p{margin:0 0 8px;font-size:13px;color:#334155}
            table{width:100%;border-collapse:collapse;margin-top:16px}
            th,td{border:1px solid #cbd5e1;padding:8px 10px;text-align:left;font-size:12px;vertical-align:top}
            th{background:#e2e8f0;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
            .meta{margin-bottom:14px}
            @media print{body{margin:12px}}
          </style>
        </head>
        <body>
          <div class="meta">
            <h1>${escapeHtml(@json($boardTitle))}</h1>
            <p>${escapeHtml(@json($boardLead))}</p>
            <p><strong>Printed:</strong> ${escapeHtml(new Date().toLocaleString([], { hour12: true, timeZone: companyTz }))}</p>
            ${filterBlock}
            <p><strong>Records:</strong> ${escapeHtml(String(lastSummary.count ?? lastRows.length ?? 0))}</p>
          </div>
          <table>
            <thead>
              <tr>${config.columns.map((column) => `<th>${escapeHtml(column.label || column.key)}</th>`).join('')}</tr>
            </thead>
            <tbody>${tableRows}</tbody>
          </table>
        </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
      printWindow.print();
    }, 250);
  }

  function actionButtons(row) {
    if (!config.actions.length) return '';
    return `
      <div class="att-board-actions">
        ${config.actions.map((action) => `
          <button type="button" class="btn btn-sm ${action.btn || 'btn-light'} js-board-action" data-action="${action.type}" data-id="${row[action.idKey || 'id']}" title="${escapeHtml(action.label || action.type)}">
            <i class="${action.icon}"></i>${action.label ? ` <span>${escapeHtml(action.label)}</span>` : ''}
          </button>
        `).join('')}
      </div>
    `;
  }

  function renderDetailCard(label, value) {
    return `
      <div class="att-detail-card">
        <span>${escapeHtml(label)}</span>
        <strong>${value || '—'}</strong>
      </div>
    `;
  }

  function renderProofCard(title, proof) {
    if (!proof?.url) {
      return `
        <div class="att-detail-proof">
          <strong>${escapeHtml(title)}</strong>
          <div class="text-muted mt-2">No selfie captured.</div>
        </div>
      `;
    }
    return `
      <div class="att-detail-proof">
        <strong>${escapeHtml(title)}</strong>
        <a href="${escapeHtml(proof.url)}" target="_blank" rel="noopener" class="att-detail-map"><i class="fa-solid fa-image"></i>Open full image</a>
        <img src="${escapeHtml(proof.url)}" alt="${escapeHtml(title)}">
      </div>
    `;
  }

  function renderLogRows(logs) {
    if (!logs.length) {
      return '<tr><td colspan="7" class="text-muted text-center py-3">No punch logs recorded.</td></tr>';
    }
    return logs.map((log) => {
      const mapUrl = mapHref(log.latitude, log.longitude);
      return `
        <tr>
          <td>${badgeHtml(log.punch_type || 'punch')}</td>
          <td>${escapeHtml(formatDateTime(log.punch_time) || '—')}</td>
          <td>${escapeHtml(locationLabel(log))}${mapUrl ? `<a class="att-detail-map" href="${escapeHtml(mapUrl)}" target="_blank" rel="noopener"><i class="fa-solid fa-map-location-dot"></i>Map</a>` : ''}</td>
          <td>${badgeHtml(log.attendance_mode || 'online')} ${log.work_mode ? badgeHtml(log.work_mode) : ''}</td>
          <td>${escapeHtml(log.request_ip || '—')}<small>${escapeHtml(log.network_type || '—')} · ${escapeHtml(log.internet_status || '—')}</small></td>
          <td>${log.selfie_url ? `<a href="${escapeHtml(log.selfie_url)}" target="_blank" rel="noopener" class="att-detail-map"><i class="fa-solid fa-camera"></i>Selfie</a>` : '<span class="text-muted">—</span>'}</td>
          <td>${log.exception_reason ? `${badgeHtml('pending_approval')}<small>${escapeHtml(log.exception_reason)}</small>` : badgeHtml(log.sync_status || 'synced')}</td>
        </tr>
      `;
    }).join('');
  }

  function renderTrackRows(tracks) {
    if (!tracks.length) {
      return '<tr><td colspan="6" class="text-muted text-center py-3">No live tracking points recorded.</td></tr>';
    }
    return tracks.slice().reverse().map((track) => {
      const mapUrl = mapHref(track.latitude, track.longitude);
      return `
        <tr>
          <td>${escapeHtml(formatDateTime(track.recorded_at) || '—')}</td>
          <td>${escapeHtml(locationLabel(track))}${mapUrl ? `<a class="att-detail-map" href="${escapeHtml(mapUrl)}" target="_blank" rel="noopener"><i class="fa-solid fa-map-location-dot"></i>Map</a>` : ''}</td>
          <td>${escapeHtml(track.network_type || '—')}<small>${escapeHtml(track.source || 'tracking')}</small></td>
          <td>${escapeHtml(track.speed_kmph !== null && track.speed_kmph !== undefined ? `${Number(track.speed_kmph).toFixed(1)} km/h` : '—')}</td>
          <td>${escapeHtml(track.battery_level !== null && track.battery_level !== undefined ? `${track.battery_level}%` : '—')}</td>
          <td>${badgeHtml(track.sync_status || 'synced')}</td>
        </tr>
      `;
    }).join('');
  }

  function renderApprovalRows(approvals) {
    if (!approvals.length) {
      return '<tr><td colspan="5" class="text-muted text-center py-3">No approval records.</td></tr>';
    }
    return approvals.map((approval) => `
      <tr>
        <td>${badgeHtml(approval.approval_type || 'approval')}</td>
        <td>${escapeHtml(formatDateTime(approval.requested_at) || '—')}<small>${escapeHtml(approval.requested_by_name || 'System')}</small></td>
        <td>${badgeHtml(approval.status || 'pending_approval')}</td>
        <td>${escapeHtml(approval.approver_name || '—')}<small>${escapeHtml(formatDateTime(approval.decided_at) || 'Pending')}</small></td>
        <td>${escapeHtml(approval.reason || approval.approver_remarks || '—')}</td>
      </tr>
    `).join('');
  }

  function renderActivitySection(rows) {
    if (!rows || !rows.length) {
      return `
        <div class="att-detail-section">
          <h4>Employee Activity Timeline</h4>
          <p class="text-muted small mb-0">No employee activity logs recorded for this session.</p>
        </div>`;
    }

    const activityRows = rows.map((row) => {
      const data = row.new_values || {};
      const details = [
        data.source ? `Source: ${data.source}` : null,
        data.network_type ? `Network: ${data.network_type}` : null,
        data.request_ip ? `IP: ${data.request_ip}` : null,
        data.location ? `Location: ${data.location}` : null,
        data.reason ? `Reason: ${data.reason}` : null,
      ].filter(Boolean).join(' · ');

      return `
        <tr>
          <td>${escapeHtml(formatDateTime(row.created_at) || '—')}</td>
          <td>${badgeHtml(row.activity || 'activity')}</td>
          <td>${escapeHtml(row.log_note || '—')}</td>
          <td>${escapeHtml(String(data.severity || data.category || 'info').replace(/_/g, ' '))}</td>
          <td>${details ? `<small>${escapeHtml(details)}</small>` : '<span class="text-muted">—</span>'}</td>
        </tr>
      `;
    }).join('');

    return `
      <div class="att-detail-section">
        <h4>Employee Activity Timeline</h4>
        <div class="att-detail-table-wrap">
          <table class="att-detail-table">
            <thead>
              <tr>
                <th>Occurred</th>
                <th>Activity</th>
                <th>Note</th>
                <th>Level</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody>${activityRows}</tbody>
          </table>
        </div>
      </div>`;
  }

  async function viewAttendanceDetail(id) {
    const endpoint = String(config.detailEndpoint || '').replace('{id}', encodeURIComponent(String(id)));
    if (!endpoint) throw new Error('Attendance detail endpoint is not configured.');
    const response = await fetch(endpoint, { headers: headers(false) });
    const payload = await response.json();
    if (!response.ok) throw new Error(payload.message || 'Could not load attendance detail.');

    const detail = payload.data || {};
    const attendance = detail.attendance || {};
    const journey = detail.journey || {};
    const proofs = detail.proofs || {};
    const logs = Array.isArray(detail.logs) ? detail.logs : [];
    const tracks = Array.isArray(detail.tracks) ? detail.tracks : [];
    const approvals = Array.isArray(detail.approvals) ? detail.approvals : [];
    const activityLogs = Array.isArray(detail.activity_logs) ? detail.activity_logs : [];
    const currentLocation = journey.current_location || {};
    const currentMap = mapHref(currentLocation.latitude, currentLocation.longitude);

    const summaryHtml = `
      <div class="att-detail">
        <div class="att-detail-grid">
          ${renderDetailCard('Employee', `<div>${escapeHtml(attendance.name || '—')}</div><div class="text-muted small">${escapeHtml(attendance.employee_code || '—')} · ${escapeHtml(attendance.department_name || 'No Department')}</div>`)}
          ${renderDetailCard('Attendance Day', `${escapeHtml(formatDate(attendance.attendance_date) || '—')}<div class="mt-1">${badgeHtml(attendance.status || 'present')} ${badgeHtml(attendance.approval_status || 'approved')}</div>`)}
          ${renderDetailCard('Shift Window', `${escapeHtml(attendance.shift_name || '—')}<div class="text-muted small">${escapeHtml(formatDateTime(attendance.shift_start_time) || attendance.shift_start_time || '—')} to ${escapeHtml(formatDateTime(attendance.shift_end_time) || attendance.shift_end_time || '—')}</div>`)}
          ${renderDetailCard('Check In / Out', `${escapeHtml(formatDateTime(attendance.check_in_time) || '—')}<div class="text-muted small mt-1">${escapeHtml(formatDateTime(attendance.check_out_time) || 'Still active')}</div>`)}
          ${renderDetailCard('Work Analytics', `Worked ${escapeHtml(formatMinutes(attendance.total_working_minutes))}<div class="text-muted small mt-1">Late ${escapeHtml(formatMinutes(attendance.late_minutes))} · OT ${escapeHtml(formatMinutes(attendance.overtime_minutes))}</div>`)}
          ${renderDetailCard('Policy Flags', `<div>${yesNoMaybe(attendance.within_geofence, 'Inside geofence', 'Outside geofence', 'Geofence not checked')}</div><div class="text-muted small mt-1">${yesNoMaybe(attendance.within_wifi_ip, 'Approved IP/Wi-Fi', 'IP/Wi-Fi mismatch', 'IP not checked')}</div>`)}
          ${renderDetailCard('Current Location', `${escapeHtml(currentLocation.label || '—')}${currentMap ? `<a class="att-detail-map" href="${escapeHtml(currentMap)}" target="_blank" rel="noopener"><i class="fa-solid fa-map-location-dot"></i>Open map</a>` : ''}`)}
          ${renderDetailCard('Tracking Summary', `${escapeHtml(String(journey.track_points ?? 0))} live points<div class="text-muted small mt-1">Route ${escapeHtml(distanceLabel(journey.path_distance_meters))} · Last seen ${escapeHtml(formatDateTime(journey.last_seen_at) || '—')}</div>`)}
        </div>

        <div class="att-detail-section">
          <h4>Selfie Proofs</h4>
          <div class="att-detail-proof-grid">
            ${renderProofCard('Check In Selfie', proofs.check_in_selfie)}
            ${renderProofCard('Check Out Selfie', proofs.check_out_selfie)}
          </div>
        </div>

        <div class="att-detail-section">
          <h4>Punch Timeline</h4>
          <div class="att-detail-table-wrap">
            <table class="att-detail-table">
              <thead>
                <tr>
                  <th>Punch</th>
                  <th>Time</th>
                  <th>Location</th>
                  <th>Mode</th>
                  <th>Network</th>
                  <th>Proof</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>${renderLogRows(logs)}</tbody>
            </table>
          </div>
        </div>

        <div class="att-detail-section">
          <h4>Movement Tracking</h4>
          <div class="att-detail-table-wrap">
            <table class="att-detail-table">
              <thead>
                <tr>
                  <th>Recorded At</th>
                  <th>Location</th>
                  <th>Source</th>
                  <th>Speed</th>
                  <th>Battery</th>
                  <th>Sync</th>
                </tr>
              </thead>
              <tbody>${renderTrackRows(tracks)}</tbody>
            </table>
          </div>
        </div>

        <div class="att-detail-section">
          <h4>Approval Trail</h4>
          <div class="att-detail-table-wrap">
            <table class="att-detail-table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Requested</th>
                  <th>Status</th>
                  <th>Handled By</th>
                  <th>Reason</th>
                </tr>
              </thead>
              <tbody>${renderApprovalRows(approvals)}</tbody>
            </table>
          </div>
        </div>

        ${renderActivitySection(activityLogs)}
      </div>
    `;

    await Swal.fire({
      title: 'Attendance Detail',
      html: summaryHtml,
      width: 1180,
      confirmButtonText: 'Close',
      customClass: { popup: 'swal-wide' },
    });
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
      lastRows = rows;
      lastSummary = data.summary || {};
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
      lastRows = [];
      lastSummary = {};
    }
  }

  async function handleAction(type, id) {
    if (type === 'view-attendance') {
      await viewAttendanceDetail(id);
      return;
    }

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
  exportBtn?.addEventListener('click', async () => {
    try {
      await exportBoard();
    } catch (error) {
      Swal.fire('Export failed', error.message, 'error');
    }
  });
  printBtn?.addEventListener('click', () => {
    try {
      printBoard();
    } catch (error) {
      Swal.fire('Print failed', error.message, 'error');
    }
  });
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
