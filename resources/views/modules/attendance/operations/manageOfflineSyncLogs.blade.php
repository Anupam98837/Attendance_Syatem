@extends('pages.layout.structure')

@section('title', 'Offline Sync Logs')

@section('content')
@php
  $boardTitle = 'Offline Sync Logs';
  $boardLead = 'Monitor queued offline attendance items, retries, final sync states, and failure patterns from the hybrid employee flow.';
  $boardEndpoint = '/api/attendance/hr/offline-sync-logs';
  $boardColumns = [
    ['key' => 'name', 'label' => 'Employee'],
    ['key' => 'employee_code', 'label' => 'Code'],
    ['key' => 'queue_type', 'label' => 'Queue Type'],
    ['key' => 'attendance_mode', 'label' => 'Mode'],
    ['key' => 'device_id', 'label' => 'Device ID'],
    ['key' => 'sync_status', 'label' => 'Sync Status'],
    ['key' => 'attempts', 'label' => 'Attempts'],
    ['key' => 'queued_at', 'label' => 'Queued At'],
  ];
  $boardFilters = [
    ['key' => 'sync_status', 'label' => 'Sync Status', 'type' => 'text'],
    ['key' => 'queue_type', 'label' => 'Queue Type', 'type' => 'text'],
    ['key' => 'device_id', 'label' => 'Device ID', 'type' => 'text'],
    ['key' => 'from', 'label' => 'From', 'type' => 'date'],
    ['key' => 'to', 'label' => 'To', 'type' => 'date'],
    ['key' => 'branch_id', 'label' => 'Branch', 'type' => 'relation-select', 'source' => 'branches', 'placeholder' => 'All branches'],
  ];
@endphp
@include('modules.attendance.partials.dataBoard')
@endsection
