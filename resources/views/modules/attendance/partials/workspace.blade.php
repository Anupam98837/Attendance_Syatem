@push('styles')
<style>
.attn-shell{
  display:grid;
  gap:22px;
}
.attn-hero{
  position:relative;
  overflow:hidden;
  background:
    radial-gradient(circle at top right, rgba(203, 213, 225, .35), transparent 35%),
    linear-gradient(135deg, rgba(15, 118, 110, .12), rgba(217, 119, 6, .14));
  border:1px solid var(--line-strong);
  border-radius:30px;
  padding:30px;
  box-shadow:var(--shadow-2);
}
.attn-hero::before{
  content:"";
  position:absolute;
  inset:auto 30px -50px auto;
  width:180px;
  height:180px;
  border-radius:999px;
  background:radial-gradient(circle, rgba(15,118,110,.18), transparent 70%);
}
.attn-kicker{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding:9px 14px;
  border-radius:999px;
  background:rgba(255,255,255,.74);
  border:1px solid rgba(15,118,110,.16);
  color:var(--primary-color);
  font-size:12px;
  font-weight:800;
  letter-spacing:.08em;
  text-transform:uppercase;
}
.attn-hero-grid{
  position:relative;
  z-index:1;
  display:grid;
  grid-template-columns:minmax(0, 1.5fr) minmax(280px, .9fr);
  gap:22px;
  align-items:end;
  margin-top:18px;
}
.attn-title{
  margin:0 0 10px;
  font-size:clamp(2rem, 4vw, 3.1rem);
  letter-spacing:-.05em;
}
.attn-copy{
  margin:0;
  max-width:70ch;
  color:var(--muted-color);
  line-height:1.8;
  font-size:15px;
}
.attn-chip-row{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
  margin-top:18px;
}
.attn-chip{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 13px;
  border-radius:999px;
  background:rgba(255,255,255,.86);
  border:1px solid var(--line-soft);
  color:var(--ink);
  font-size:12px;
  font-weight:700;
}
.attn-panel{
  background:rgba(255,255,255,.78);
  border:1px solid rgba(15,118,110,.14);
  border-radius:24px;
  padding:18px;
  box-shadow:var(--shadow-1);
  backdrop-filter:blur(10px);
}
.attn-panel h3{
  margin:0 0 10px;
  font-size:16px;
}
.attn-panel-list{
  display:grid;
  gap:12px;
}
.attn-panel-item{
  display:flex;
  align-items:flex-start;
  gap:12px;
  padding:12px 0;
  border-top:1px solid rgba(174,196,174,.6);
}
.attn-panel-item:first-child{
  border-top:0;
  padding-top:0;
}
.attn-panel-item i{
  width:18px;
  margin-top:2px;
  color:var(--accent-color);
}
.attn-panel-item strong{
  display:block;
  font-size:13px;
  margin-bottom:3px;
}
.attn-panel-item span{
  display:block;
  color:var(--muted-color);
  line-height:1.6;
  font-size:13px;
}
.attn-metrics{
  display:grid;
  grid-template-columns:repeat(4, minmax(0, 1fr));
  gap:16px;
}
.attn-metric{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:22px;
  padding:20px;
  box-shadow:var(--shadow-1);
}
.attn-metric span{
  display:flex;
  align-items:center;
  gap:8px;
  font-size:12px;
  font-weight:800;
  color:var(--primary-color);
  text-transform:uppercase;
  letter-spacing:.08em;
  margin-bottom:14px;
}
.attn-metric strong{
  display:block;
  font-size:30px;
  line-height:1;
  color:var(--ink);
}
.attn-metric small{
  display:block;
  margin-top:8px;
  color:var(--muted-color);
  line-height:1.6;
}
.attn-grid{
  display:grid;
  grid-template-columns:minmax(0, 1.25fr) minmax(0, .95fr);
  gap:18px;
}
.attn-card{
  background:var(--surface);
  border:1px solid var(--line-strong);
  border-radius:24px;
  padding:22px;
  box-shadow:var(--shadow-1);
}
.attn-card h2{
  margin:0 0 8px;
  font-size:20px;
}
.attn-card-intro{
  margin:0 0 18px;
  color:var(--muted-color);
  line-height:1.7;
}
.attn-list{
  display:grid;
  gap:12px;
}
.attn-list-item{
  display:grid;
  gap:6px;
  padding:14px 16px;
  border-radius:18px;
  border:1px solid var(--line-soft);
  background:var(--surface-2);
}
.attn-list-item strong{
  color:var(--ink);
  font-size:14px;
}
.attn-list-item span{
  color:var(--muted-color);
  line-height:1.65;
  font-size:13px;
}
.attn-flow{
  display:grid;
  gap:12px;
}
.attn-flow-step{
  display:grid;
  grid-template-columns:42px minmax(0, 1fr);
  gap:14px;
  align-items:flex-start;
}
.attn-flow-step em{
  display:grid;
  place-items:center;
  width:42px;
  height:42px;
  border-radius:14px;
  background:linear-gradient(135deg, rgba(15,118,110,.12), rgba(217,119,6,.16));
  color:var(--primary-color);
  font-style:normal;
  font-weight:800;
}
.attn-flow-step strong{
  display:block;
  margin-bottom:4px;
  color:var(--ink);
}
.attn-flow-step span{
  color:var(--muted-color);
  line-height:1.65;
  display:block;
}
.attn-api-table{
  width:100%;
  border-collapse:separate;
  border-spacing:0;
  overflow:hidden;
  border-radius:18px;
  border:1px solid var(--line-soft);
}
.attn-api-table th,
.attn-api-table td{
  padding:13px 14px;
  font-size:13px;
  vertical-align:top;
}
.attn-api-table thead th{
  background:var(--surface-3);
  color:var(--ink);
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:.06em;
  font-size:11px;
}
.attn-api-table tbody tr + tr td{
  border-top:1px solid var(--line-soft);
}
.attn-api-table code{
  color:var(--secondary-color);
  font-size:12px;
}
.attn-tags{
  display:flex;
  flex-wrap:wrap;
  gap:10px;
}
.attn-tag{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 12px;
  border-radius:14px;
  background:var(--surface-2);
  border:1px solid var(--line-soft);
  font-size:12px;
  font-weight:700;
  color:var(--ink);
}
.attn-employee-board{
  display:grid;
  grid-template-columns:repeat(2, minmax(0, 1fr));
  gap:16px;
}
.attn-mobile-card{
  border:1px solid var(--line-soft);
  border-radius:24px;
  background:
    linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,245,.96));
  padding:18px;
  min-height:220px;
  box-shadow:var(--shadow-1);
}
.attn-mobile-card h3{
  margin:0 0 10px;
  font-size:16px;
}
.attn-mobile-card p{
  color:var(--muted-color);
  line-height:1.7;
  margin:0 0 14px;
}
.attn-mobile-stack{
  display:grid;
  gap:10px;
}
.attn-mobile-stack span{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:10px;
  border:1px solid var(--line-soft);
  border-radius:14px;
  padding:10px 12px;
  background:var(--surface);
  font-size:12px;
  color:var(--ink);
}
.attn-mobile-stack span small{
  color:var(--muted-color);
}
@media (max-width: 1199.98px){
  .attn-metrics{grid-template-columns:repeat(2, minmax(0, 1fr))}
  .attn-grid{grid-template-columns:1fr}
}
@media (max-width: 991.98px){
  .attn-hero-grid{grid-template-columns:1fr}
  .attn-employee-board{grid-template-columns:1fr}
}
@media (max-width: 767.98px){
  .attn-hero{padding:22px}
  .attn-metrics{grid-template-columns:1fr}
  .attn-card{padding:18px}
}
</style>
@endpush

@php
  $pageIcon = $pageIcon ?? 'fa-solid fa-layer-group';
  $pageKicker = $pageKicker ?? 'Attendance Module';
  $pageTitle = $pageTitle ?? 'Attendance Workspace';
  $pageSummary = $pageSummary ?? 'This module is ready for the new attendance flow.';
  $pagePersona = $pagePersona ?? 'Admin / HR';
  $pageCadence = $pageCadence ?? 'Always on';
  $pageStatus = $pageStatus ?? 'Configured from the attendance backend';
  $heroPoints = $heroPoints ?? [];
  $metrics = $metrics ?? [];
  $primaryBlocks = $primaryBlocks ?? [];
  $secondaryBlocks = $secondaryBlocks ?? [];
  $flowSteps = $flowSteps ?? [];
  $apiRows = $apiRows ?? [];
  $dataPoints = $dataPoints ?? [];
  $employeeCards = $employeeCards ?? [];
@endphp

<div class="attn-shell">
  <section class="attn-hero">
    <span class="attn-kicker"><i class="{{ $pageIcon }}"></i>{{ $pageKicker }}</span>
    <div class="attn-hero-grid">
      <div>
        <h1 class="attn-title">{{ $pageTitle }}</h1>
        <p class="attn-copy">{{ $pageSummary }}</p>
        <div class="attn-chip-row">
          <span class="attn-chip"><i class="fa-solid fa-user-group"></i>{{ $pagePersona }}</span>
          <span class="attn-chip"><i class="fa-solid fa-bolt"></i>{{ $pageCadence }}</span>
          <span class="attn-chip"><i class="fa-solid fa-shield-heart"></i>{{ $pageStatus }}</span>
        </div>
      </div>

      <aside class="attn-panel">
        <h3>Operational Angle</h3>
        <div class="attn-panel-list">
          @foreach ($heroPoints as $point)
            <div class="attn-panel-item">
              <i class="{{ $point['icon'] ?? 'fa-solid fa-check' }}"></i>
              <div>
                <strong>{{ $point['title'] ?? '' }}</strong>
                <span>{{ $point['text'] ?? '' }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </aside>
    </div>
  </section>

  @if (!empty($metrics))
    <section class="attn-metrics">
      @foreach ($metrics as $metric)
        <article class="attn-metric">
          <span><i class="{{ $metric['icon'] ?? 'fa-solid fa-chart-line' }}"></i>{{ $metric['label'] ?? '' }}</span>
          <strong>{{ $metric['value'] ?? '' }}</strong>
          <small>{{ $metric['note'] ?? '' }}</small>
        </article>
      @endforeach
    </section>
  @endif

  <section class="attn-grid">
    <article class="attn-card">
      <h2>{{ $primaryTitle ?? 'What This Page Controls' }}</h2>
      <p class="attn-card-intro">{{ $primaryIntro ?? 'This page anchors a required part of the attendance program and keeps the workflow predictable.' }}</p>
      <div class="attn-list">
        @foreach ($primaryBlocks as $block)
          <div class="attn-list-item">
            <strong>{{ $block['title'] ?? '' }}</strong>
            <span>{{ $block['text'] ?? '' }}</span>
          </div>
        @endforeach
      </div>
    </article>

    <article class="attn-card">
      <h2>{{ $secondaryTitle ?? 'Controls & Guardrails' }}</h2>
      <p class="attn-card-intro">{{ $secondaryIntro ?? 'These rules keep hybrid attendance trustworthy even when people move between online and offline states.' }}</p>
      <div class="attn-list">
        @foreach ($secondaryBlocks as $block)
          <div class="attn-list-item">
            <strong>{{ $block['title'] ?? '' }}</strong>
            <span>{{ $block['text'] ?? '' }}</span>
          </div>
        @endforeach
      </div>
    </article>
  </section>

  @if (!empty($flowSteps))
    <section class="attn-grid">
      <article class="attn-card">
        <h2>{{ $flowTitle ?? 'Workflow' }}</h2>
        <p class="attn-card-intro">{{ $flowIntro ?? 'This is the execution path the module supports.' }}</p>
        <div class="attn-flow">
          @foreach ($flowSteps as $index => $step)
            <div class="attn-flow-step">
              <em>{{ $index + 1 }}</em>
              <div>
                <strong>{{ $step['title'] ?? '' }}</strong>
                <span>{{ $step['text'] ?? '' }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </article>

      <article class="attn-card">
        <h2>{{ $dataTitle ?? 'Key Data Touchpoints' }}</h2>
        <p class="attn-card-intro">{{ $dataIntro ?? 'These entities stay connected to this module across setup, attendance capture, and review.' }}</p>
        <div class="attn-tags">
          @foreach ($dataPoints as $point)
            <span class="attn-tag"><i class="{{ $point['icon'] ?? 'fa-solid fa-database' }}"></i>{{ $point['label'] ?? '' }}</span>
          @endforeach
        </div>
      </article>
    </section>
  @endif

  @if (!empty($apiRows))
    <section class="attn-card">
      <h2>{{ $apiTitle ?? 'Connected Backend APIs' }}</h2>
      <p class="attn-card-intro">{{ $apiIntro ?? 'These endpoints already exist in the backend and are the intended integration points for the frontend or mobile app.' }}</p>
      <div class="table-responsive">
        <table class="attn-api-table">
          <thead>
            <tr>
              <th>Method</th>
              <th>Endpoint</th>
              <th>Purpose</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($apiRows as $row)
              <tr>
                <td><span class="attn-tag"><i class="fa-solid fa-plug"></i>{{ $row['method'] ?? '' }}</span></td>
                <td><code>{{ $row['endpoint'] ?? '' }}</code></td>
                <td>{{ $row['purpose'] ?? '' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
  @endif

  @if (!empty($employeeCards))
    <section class="attn-card">
      <h2>{{ $employeeTitle ?? 'Employee App Conversion Notes' }}</h2>
      <p class="attn-card-intro">{{ $employeeIntro ?? 'These screens are shaped as mobile-friendly prototypes so you can convert the same flow into the app later.' }}</p>
      <div class="attn-employee-board">
        @foreach ($employeeCards as $card)
          <article class="attn-mobile-card">
            <h3>{{ $card['title'] ?? '' }}</h3>
            <p>{{ $card['text'] ?? '' }}</p>
            <div class="attn-mobile-stack">
              @foreach (($card['items'] ?? []) as $item)
                <span>
                  <b>{{ $item['label'] ?? '' }}</b>
                  <small>{{ $item['value'] ?? '' }}</small>
                </span>
              @endforeach
            </div>
          </article>
        @endforeach
      </div>
    </section>
  @endif
</div>
