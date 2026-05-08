@extends('pages.layout.structure')

@section('title', 'Monthly Attendance')

@section('content')
@php
  $boardTitle = 'Monthly Attendance';
  $boardLead = 'Review month-level attendance summaries for payroll preparation, trend analysis, and workload monitoring.';
  $boardEndpoint = '/api/attendance/hr/reports';
  $boardColumns = [
    ['key' => 'name', 'label' => 'Employee'],
    ['key' => 'employee_code', 'label' => 'Code'],
    ['key' => 'department_name', 'label' => 'Department'],
    ['key' => 'branch_name', 'label' => 'Branch'],
    ['key' => 'marked_days', 'label' => 'Marked Days'],
    ['key' => 'present_days', 'label' => 'Present'],
    ['key' => 'late_days', 'label' => 'Late'],
    ['key' => 'half_days', 'label' => 'Half Day'],
    ['key' => 'total_working_minutes', 'label' => 'Working Minutes'],
    ['key' => 'total_overtime_minutes', 'label' => 'Overtime Minutes'],
  ];
  $boardFilters = [
    ['key' => 'month', 'label' => 'Month', 'type' => 'month'],
    ['key' => 'type', 'label' => 'Report Type', 'type' => 'select', 'options' => ['monthly' => 'Monthly', 'payroll' => 'Payroll']],
    ['key' => 'department_id', 'label' => 'Department', 'type' => 'relation-select', 'source' => 'departments', 'placeholder' => 'All departments'],
    ['key' => 'branch_id', 'label' => 'Branch', 'type' => 'relation-select', 'source' => 'branches', 'placeholder' => 'All branches'],
  ];
  $boardDefaultQuery = ['type' => 'monthly', 'month' => now()->format('Y-m')];
@endphp
@include('modules.attendance.partials.dataBoard')
@endsection
