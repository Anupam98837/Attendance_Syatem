@extends('pages.layout.structure')

@section('title', 'Location Exceptions')

@section('content')
@php
  $boardTitle = 'Location Exceptions';
  $boardLead = 'Review GPS, geofence, Wi-Fi/IP, and device-related attendance exceptions that need operational attention.';
  $boardEndpoint = '/api/attendance/hr/location-exceptions';
  $boardColumns = [
    ['key' => 'name', 'label' => 'Employee'],
    ['key' => 'employee_code', 'label' => 'Code'],
    ['key' => 'branch_name', 'label' => 'Branch'],
    ['key' => 'punch_type', 'label' => 'Punch'],
    ['key' => 'punch_time', 'label' => 'Punch Time'],
    ['key' => 'network_type', 'label' => 'Network'],
    ['key' => 'exception_reason', 'label' => 'Exception Reason'],
    ['key' => 'request_ip', 'label' => 'Request IP'],
  ];
  $boardFilters = [
    ['key' => 'from', 'label' => 'From', 'type' => 'date'],
    ['key' => 'to', 'label' => 'To', 'type' => 'date'],
    ['key' => 'branch_id', 'label' => 'Branch', 'type' => 'relation-select', 'source' => 'branches', 'placeholder' => 'All branches'],
  ];
@endphp
@include('modules.attendance.partials.dataBoard')
@endsection
