@extends('pages.layout.structure')

@section('title', 'Pending Approvals')

@section('content')
@php
  $boardTitle = 'Pending Attendance Approvals';
  $boardLead = 'Review suspicious or policy-driven exceptions such as outside geofence, offline delay, device mismatch, and manual correction cases.';
  $boardEndpoint = '/api/attendance/hr/pending-approvals';
  $boardColumns = [
    ['key' => 'name', 'label' => 'Employee'],
    ['key' => 'employee_code', 'label' => 'Code'],
    ['key' => 'approval_type', 'label' => 'Approval Type', 'type' => 'badge'],
    ['key' => 'attendance_date', 'label' => 'Attendance Date', 'type' => 'date'],
    ['key' => 'punch_type', 'label' => 'Punch', 'type' => 'badge'],
    ['key' => 'punch_time', 'label' => 'Punch Time', 'type' => 'datetime'],
    ['key' => 'attendance_mode', 'label' => 'Attendance Mode', 'type' => 'badge'],
    ['key' => 'work_mode', 'label' => 'Work Mode', 'type' => 'badge'],
    ['key' => 'location_text', 'label' => 'Location', 'type' => 'location'],
    ['key' => 'exception_reason', 'label' => 'Exception'],
    ['key' => 'request_ip', 'label' => 'Request IP'],
    ['key' => 'status', 'label' => 'Queue Status', 'type' => 'badge'],
  ];
  $boardFilters = [
    ['key' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['pending_approval' => 'Pending Approval', 'approved' => 'Approved', 'rejected' => 'Rejected']],
    ['key' => 'approval_type', 'label' => 'Approval Type', 'type' => 'text'],
  ];
  $boardDefaultQuery = ['status' => 'pending_approval'];
  $boardActions = [
    ['type' => 'approve-approval', 'icon' => 'fa-solid fa-check', 'btn' => 'btn-success'],
    ['type' => 'reject-approval', 'icon' => 'fa-solid fa-xmark', 'btn' => 'btn-danger'],
  ];
@endphp
@include('modules.attendance.partials.dataBoard')
@endsection
