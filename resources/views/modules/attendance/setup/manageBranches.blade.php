@extends('pages.layout.structure')

@section('title', 'Manage Branches')

@section('content')
@php
  $resourcePageTitle = 'Manage Branches & Locations';
  $resourcePageLead = 'Create and maintain branch offices, geofence settings, Wi-Fi behavior, and mobile-data restrictions for office attendance.';
  $resourceEndpoint = '/api/attendance/admin/branches';
  $resourceSingular = 'Branch';
  $resourcePlural = 'Branches';
  $resourceColumns = [
    ['key' => 'name', 'label' => 'Branch'],
    ['key' => 'code', 'label' => 'Code'],
    ['key' => 'city', 'label' => 'City'],
    ['key' => 'geofence_radius_meters', 'label' => 'Geofence (m)'],
    ['key' => 'wifi_only', 'label' => 'Wi-Fi Only', 'type' => 'bool'],
    ['key' => 'allow_mobile_data', 'label' => 'Mobile Data', 'type' => 'bool'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
  ];
  $resourceFields = [
    ['name' => 'name', 'label' => 'Branch Name', 'required' => true, 'placeholder' => 'Enter branch name'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Enter branch code'],
    ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Enter full branch address'],
    ['name' => 'city', 'label' => 'City', 'placeholder' => 'Enter city'],
    ['name' => 'state', 'label' => 'State', 'placeholder' => 'Enter state'],
    ['name' => 'country', 'label' => 'Country', 'type' => 'select', 'options' => ['India' => 'India', 'Bangladesh' => 'Bangladesh', 'Nepal' => 'Nepal', 'Bhutan' => 'Bhutan', 'Sri Lanka' => 'Sri Lanka', 'UAE' => 'UAE'], 'placeholder' => 'Select country'],
    ['name' => 'postal_code', 'label' => 'Postal Code', 'placeholder' => 'Enter postal code'],
    ['name' => 'latitude', 'label' => 'Latitude', 'type' => 'number', 'step' => '0.0000001', 'placeholder' => '22.5726000'],
    ['name' => 'longitude', 'label' => 'Longitude', 'type' => 'number', 'step' => '0.0000001', 'placeholder' => '88.3639000'],
    ['name' => 'geofence_radius_meters', 'label' => 'Geofence Radius (meters)', 'type' => 'number', 'placeholder' => '100'],
    ['name' => 'wifi_only', 'label' => 'Wi-Fi Only', 'type' => 'checkbox', 'help' => 'Allow attendance only from approved branch Wi-Fi/IP context.'],
    ['name' => 'allow_mobile_data', 'label' => 'Allow Mobile Data', 'type' => 'checkbox', 'help' => 'If disabled, office attendance should not be accepted over mobile data.'],
    ['name' => 'allow_outside_geofence', 'label' => 'Allow Outside Geofence', 'type' => 'checkbox', 'help' => 'Permit branch attendance outside the defined radius.'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
  ];
  $resourceDefaults = ['status' => 'active'];
@endphp
@include('modules.attendance.partials.manageResource')
@endsection

@push('scripts')
<script>
(() => {
  function wrapCoordinateField(input, buttonId, buttonClass, buttonHtml) {
    if (!input || input.dataset.enhanced === '1') return;
    const wrapper = document.createElement('div');
    wrapper.className = 'input-group';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.id = buttonId;
    btn.className = buttonClass;
    btn.innerHTML = buttonHtml;
    wrapper.appendChild(btn);
    input.dataset.enhanced = '1';
  }

  function attachBranchLocationTools() {
    const latInput = document.querySelector('#resourceForm [data-field="latitude"]');
    const lngInput = document.querySelector('#resourceForm [data-field="longitude"]');
    if (!latInput || !lngInput || document.getElementById('branchLocationTools')) return;

    wrapCoordinateField(
      latInput,
      'branchUseCurrentLocation',
      'btn btn-outline-primary',
      '<i class="fa-solid fa-location-crosshairs me-1"></i>Current'
    );
    wrapCoordinateField(
      lngInput,
      'branchOpenMapPreview',
      'btn btn-outline-secondary',
      '<i class="fa-solid fa-map-location-dot me-1"></i>Map'
    );

    const wrap = document.createElement('div');
    wrap.id = 'branchLocationTools';
    wrap.className = 'full';
    wrap.innerHTML = `
      <div class="d-flex flex-wrap gap-2 align-items-center mt-2 px-1">
        <span class="att-inline-badge"><i class="fa-solid fa-satellite-dish"></i>Location Helper</span>
        <small class="text-muted" id="branchLocationMsg">Use Current to auto-fill coordinates, then Map to verify the branch pin.</small>
      </div>
    `;

    const parent = lngInput.closest('div');
    if (parent?.parentElement) {
      parent.parentElement.appendChild(wrap);
    }

    document.getElementById('branchUseCurrentLocation')?.addEventListener('click', () => {
      const msg = document.getElementById('branchLocationMsg');
      if (!navigator.geolocation) {
        if (msg) msg.textContent = 'Geolocation is not supported in this browser.';
        return;
      }
      if (msg) msg.textContent = 'Fetching current location…';
      navigator.geolocation.getCurrentPosition((pos) => {
        latInput.value = Number(pos.coords.latitude).toFixed(7);
        lngInput.value = Number(pos.coords.longitude).toFixed(7);
        if (msg) msg.textContent = `Current location applied (accuracy ±${Math.round(pos.coords.accuracy || 0)}m).`;
      }, () => {
        if (msg) msg.textContent = 'Could not fetch current location. Please allow location access and try again.';
      }, { enableHighAccuracy: true, timeout: 12000 });
    });

    document.getElementById('branchOpenMapPreview')?.addEventListener('click', () => {
      const lat = (latInput.value || '').trim();
      const lng = (lngInput.value || '').trim();
      const msg = document.getElementById('branchLocationMsg');
      if (!lat || !lng) {
        if (msg) msg.textContent = 'Enter or fetch latitude and longitude first to open map preview.';
        return;
      }
      window.open(`https://maps.google.com/?q=${encodeURIComponent(lat)},${encodeURIComponent(lng)}`, '_blank', 'noopener');
    });
  }

  document.addEventListener('click', (event) => {
    if (event.target.closest('#resourceAddBtn') || event.target.closest('.js-edit-resource')) {
      setTimeout(attachBranchLocationTools, 120);
    }
  });

  attachBranchLocationTools();
})();
</script>
@endpush
