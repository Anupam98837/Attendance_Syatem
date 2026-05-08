@extends('pages.layout.structure')

@section('title', 'Attendance Reports')

@section('content')
@php
  $boardTitle = 'Attendance Reports';
  $boardLead = 'Use the reporting engine for daily, late, absent, overtime, offline, location exception, and payroll-related attendance outputs.';
  $boardEndpoint = '/api/attendance/hr/reports';
  $boardColumns = [
    ['key' => 'attendance_date', 'label' => 'Date'],
    ['key' => 'name', 'label' => 'Employee'],
    ['key' => 'employee_code', 'label' => 'Code'],
    ['key' => 'department_name', 'label' => 'Department'],
    ['key' => 'branch_name', 'label' => 'Branch'],
    ['key' => 'status', 'label' => 'Status'],
    ['key' => 'attendance_mode', 'label' => 'Mode'],
    ['key' => 'total_working_minutes', 'label' => 'Working Minutes'],
    ['key' => 'overtime_minutes', 'label' => 'Overtime'],
  ];
  $boardFilters = [
    ['key' => 'type', 'label' => 'Report Type', 'type' => 'select', 'options' => ['daily' => 'Daily', 'late' => 'Late', 'overtime' => 'Overtime', 'offline' => 'Offline', 'location_exception' => 'Location Exception', 'payroll' => 'Payroll', 'monthly' => 'Monthly', 'absent' => 'Absent']],
    ['key' => 'date', 'label' => 'Date', 'type' => 'date'],
    ['key' => 'from', 'label' => 'From', 'type' => 'date'],
    ['key' => 'to', 'label' => 'To', 'type' => 'date'],
    ['key' => 'month', 'label' => 'Month', 'type' => 'month'],
    ['key' => 'branch_id', 'label' => 'Branch', 'type' => 'relation-select', 'source' => 'branches', 'placeholder' => 'All branches'],
    ['key' => 'department_id', 'label' => 'Department', 'type' => 'relation-select', 'source' => 'departments', 'placeholder' => 'All departments'],
  ];
  $boardDefaultQuery = ['type' => 'daily', 'date' => now()->toDateString()];
@endphp
@include('modules.attendance.partials.dataBoard')
@endsection
