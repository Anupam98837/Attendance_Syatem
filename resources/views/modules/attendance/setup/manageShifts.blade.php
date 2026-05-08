@extends('pages.layout.structure')

@section('title', 'Manage Shifts')

@section('content')
@php
  $resourcePageTitle = 'Manage Shifts';
  $resourcePageLead = 'Create complete shift rules for start/end time, grace, late mark, half-day, overtime, early checkout, and cross-day logic.';
  $resourceEndpoint = '/api/attendance/admin/shifts';
  $resourceSingular = 'Shift';
  $resourcePlural = 'Shifts';
  $resourceColumns = [
    ['key' => 'name', 'label' => 'Shift'],
    ['key' => 'start_time', 'label' => 'Start'],
    ['key' => 'end_time', 'label' => 'End'],
    ['key' => 'grace_minutes', 'label' => 'Grace'],
    ['key' => 'late_after_time', 'label' => 'Late After'],
    ['key' => 'full_day_working_minutes', 'label' => 'Full Day (min)'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
  ];
  $resourceFields = [
    ['name' => 'name', 'label' => 'Shift Name', 'required' => true, 'placeholder' => 'Enter shift name'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Enter shift code'],
    ['name' => 'start_time', 'label' => 'Start Time', 'type' => 'time'],
    ['name' => 'end_time', 'label' => 'End Time', 'type' => 'time'],
    ['name' => 'allow_cross_day', 'label' => 'Cross Day', 'type' => 'checkbox', 'help' => 'Enable when the shift ends on the next calendar day.'],
    ['name' => 'grace_minutes', 'label' => 'Grace Minutes', 'type' => 'number', 'placeholder' => '10'],
    ['name' => 'late_after_time', 'label' => 'Late After', 'type' => 'time'],
    ['name' => 'half_day_working_minutes', 'label' => 'Half Day Working Minutes', 'type' => 'number', 'placeholder' => '240'],
    ['name' => 'full_day_working_minutes', 'label' => 'Full Day Working Minutes', 'type' => 'number', 'placeholder' => '480'],
    ['name' => 'minimum_working_minutes', 'label' => 'Minimum Working Minutes', 'type' => 'number', 'placeholder' => '240'],
    ['name' => 'overtime_after_minutes', 'label' => 'Overtime After Minutes', 'type' => 'number', 'placeholder' => '480'],
    ['name' => 'early_checkout_before_minutes', 'label' => 'Early Checkout Before Minutes', 'type' => 'number', 'placeholder' => '30'],
    ['name' => 'break_minutes', 'label' => 'Break Minutes', 'type' => 'number', 'placeholder' => '60'],
    ['name' => 'week_days', 'label' => 'Week Days', 'type' => 'multiselect', 'full' => true, 'options' => ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'], 'help' => 'Hold Cmd/Ctrl to select multiple working days for this shift.'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
  ];
  $resourceDefaults = ['status' => 'active'];
@endphp
@include('modules.attendance.partials.manageResource')
@endsection
