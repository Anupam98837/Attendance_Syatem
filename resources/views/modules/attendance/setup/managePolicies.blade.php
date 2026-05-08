@extends('pages.layout.structure')

@section('title', 'Manage Policies')

@section('content')
@php
  $resourcePageTitle = 'Manage Attendance Policies';
  $resourcePageLead = 'Configure the trust model for office, field, and WFH attendance including GPS, selfie, offline, Wi-Fi/IP, and device rules.';
  $resourceEndpoint = '/api/attendance/admin/attendance-policies';
  $resourceSingular = 'Policy';
  $resourcePlural = 'Policies';
  $resourceColumns = [
    ['key' => 'name', 'label' => 'Policy'],
    ['key' => 'gps_required', 'label' => 'GPS', 'type' => 'bool'],
    ['key' => 'selfie_required', 'label' => 'Selfie', 'type' => 'bool'],
    ['key' => 'offline_attendance_allowed', 'label' => 'Offline', 'type' => 'bool'],
    ['key' => 'geofence_required', 'label' => 'Geofence', 'type' => 'bool'],
    ['key' => 'wifi_ip_restriction_required', 'label' => 'Wi-Fi/IP', 'type' => 'bool'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
  ];
  $resourceFields = [
    ['name' => 'name', 'label' => 'Policy Name', 'required' => true, 'placeholder' => 'Enter policy name'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Enter policy code'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Describe this attendance policy'],
    ['name' => 'gps_required', 'label' => 'GPS Required', 'type' => 'checkbox'],
    ['name' => 'selfie_required', 'label' => 'Selfie Required', 'type' => 'checkbox'],
    ['name' => 'checkout_selfie_required', 'label' => 'Checkout Selfie Required', 'type' => 'checkbox'],
    ['name' => 'face_verification_mode', 'label' => 'Face Verification', 'type' => 'select', 'options' => ['optional' => 'Optional', 'required' => 'Required', 'disabled' => 'Disabled']],
    ['name' => 'offline_attendance_allowed', 'label' => 'Offline Attendance Allowed', 'type' => 'checkbox'],
    ['name' => 'multiple_punch_allowed', 'label' => 'Multiple Punch Allowed', 'type' => 'checkbox'],
    ['name' => 'geofence_required', 'label' => 'Geofence Required', 'type' => 'checkbox'],
    ['name' => 'outside_location_allowed', 'label' => 'Outside Location Allowed', 'type' => 'checkbox'],
    ['name' => 'outside_location_requires_approval', 'label' => 'Outside Location Requires Approval', 'type' => 'checkbox'],
    ['name' => 'device_binding_required', 'label' => 'Device Binding Required', 'type' => 'checkbox'],
    ['name' => 'wifi_ip_restriction_required', 'label' => 'Wi-Fi/IP Restriction Required', 'type' => 'checkbox'],
    ['name' => 'allow_mobile_data', 'label' => 'Allow Mobile Data', 'type' => 'checkbox'],
    ['name' => 'auto_approve_clean_records', 'label' => 'Auto Approve Clean Records', 'type' => 'checkbox'],
    ['name' => 'allow_field_attendance', 'label' => 'Allow Field Attendance', 'type' => 'checkbox'],
    ['name' => 'allow_wfh_attendance', 'label' => 'Allow WFH Attendance', 'type' => 'checkbox'],
    ['name' => 'require_work_note_for_wfh', 'label' => 'WFH Work Note Required', 'type' => 'checkbox'],
    ['name' => 'offline_sync_limit_hours', 'label' => 'Offline Sync Limit Hours', 'type' => 'number', 'placeholder' => '24'],
    ['name' => 'time_drift_tolerance_minutes', 'label' => 'Time Drift Tolerance Minutes', 'type' => 'number', 'placeholder' => '10'],
    ['name' => 'max_offline_records_per_day', 'label' => 'Max Offline Records Per Day', 'type' => 'number', 'placeholder' => '4'],
    ['name' => 'continuous_tracking_interval_seconds', 'label' => 'Tracking Interval Seconds', 'type' => 'number', 'placeholder' => '180'],
    ['name' => 'allowed_punch_types', 'label' => 'Allowed Punch Types', 'type' => 'multiselect', 'full' => true, 'options' => ['check_in' => 'Check In', 'check_out' => 'Check Out'], 'help' => 'Select which punch actions the policy allows.'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
  ];
  $resourceDefaults = ['status' => 'active'];
@endphp
@include('modules.attendance.partials.manageResource')
@endsection
