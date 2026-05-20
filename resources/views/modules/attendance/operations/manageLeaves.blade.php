@extends('pages.layout.structure')

@section('title', 'Leave Management')

@section('content')
@php
  $boardTitle = 'Leave Management';
  $boardLead = 'Review employee leave applications, approve or reject them, and keep attendance absence reporting clean.';
  $boardEndpoint = '/api/attendance/hr/leaves';
  $boardColumns = [
    ['key' => 'name', 'label' => 'Employee'],
    ['key' => 'employee_code', 'label' => 'Code'],
    ['key' => 'leave_type_name', 'label' => 'Leave Type'],
    ['key' => 'from_date', 'label' => 'From'],
    ['key' => 'to_date', 'label' => 'To'],
    ['key' => 'total_days', 'label' => 'Days'],
    ['key' => 'status', 'label' => 'Status'],
    ['key' => 'reason', 'label' => 'Reason'],
  ];
  $boardFilters = [
    ['key' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['' => 'All', 'pending_approval' => 'Pending Approval', 'approved' => 'Approved', 'rejected' => 'Rejected']],
    ['key' => 'from', 'label' => 'From', 'type' => 'date'],
    ['key' => 'to', 'label' => 'To', 'type' => 'date'],
    ['key' => 'branch_id', 'label' => 'Branch', 'type' => 'relation-select', 'source' => 'branches', 'placeholder' => 'All branches'],
  ];
  $boardActions = [
    ['type' => 'approve-leave', 'icon' => 'fa-solid fa-check', 'btn' => 'btn-success'],
    ['type' => 'reject-leave', 'icon' => 'fa-solid fa-xmark', 'btn' => 'btn-danger'],
  ];
@endphp
@include('modules.attendance.partials.dataBoard')
@endsection
