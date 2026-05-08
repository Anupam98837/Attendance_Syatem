@extends('pages.layout.structure')

@section('title', 'Attendance Register')

@section('content')
@php
  $boardTitle = 'Attendance Register';
  $boardLead = 'Review the core attendance ledger across dates, modes, approval states, and sync states before payroll, approvals, or manual corrections.';
  $boardEndpoint = '/api/attendance/hr/attendance';
  $boardColumns = [
    ['key' => 'attendance_date', 'label' => 'Date', 'type' => 'date'],
    ['key' => 'name', 'label' => 'Employee'],
    ['key' => 'employee_code', 'label' => 'Code'],
    ['key' => 'department_name', 'label' => 'Department'],
    ['key' => 'branch_name', 'label' => 'Branch'],
    ['key' => 'shift_name', 'label' => 'Shift'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
    ['key' => 'approval_status', 'label' => 'Approval', 'type' => 'badge'],
    ['key' => 'sync_status', 'label' => 'Sync', 'type' => 'badge'],
    ['key' => 'attendance_mode', 'label' => 'Mode', 'type' => 'badge'],
    ['key' => 'check_in_time', 'label' => 'Check In', 'type' => 'datetime'],
    ['key' => 'check_out_time', 'label' => 'Check Out', 'type' => 'datetime'],
    ['key' => 'total_working_minutes', 'label' => 'Working', 'type' => 'duration'],
    ['key' => 'late_minutes', 'label' => 'Late', 'type' => 'duration'],
    ['key' => 'overtime_minutes', 'label' => 'OT', 'type' => 'duration'],
  ];
  $boardFilters = [
    ['key' => 'q', 'label' => 'Search', 'type' => 'search', 'placeholder' => 'Employee, code, email, phone'],
    ['key' => 'date', 'label' => 'Date', 'type' => 'date'],
    ['key' => 'from', 'label' => 'From', 'type' => 'date'],
    ['key' => 'to', 'label' => 'To', 'type' => 'date'],
    ['key' => 'department_id', 'label' => 'Department', 'type' => 'relation-select', 'source' => 'departments', 'placeholder' => 'All departments'],
    ['key' => 'branch_id', 'label' => 'Branch', 'type' => 'relation-select', 'source' => 'branches', 'placeholder' => 'All branches'],
    ['key' => 'shift_id', 'label' => 'Shift', 'type' => 'relation-select', 'source' => 'shifts', 'placeholder' => 'All shifts'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'text'],
    ['key' => 'approval_status', 'label' => 'Approval', 'type' => 'text'],
    ['key' => 'sync_status', 'label' => 'Sync', 'type' => 'text'],
    ['key' => 'attendance_mode', 'label' => 'Mode', 'type' => 'text'],
    ['key' => 'work_mode', 'label' => 'Work Mode', 'type' => 'text'],
  ];
  $boardDefaultQuery = ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->toDateString()];
@endphp
@include('modules.attendance.partials.dataBoard')
@endsection
