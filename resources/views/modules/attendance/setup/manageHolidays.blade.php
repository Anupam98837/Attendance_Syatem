@extends('pages.layout.structure')

@section('title', 'Manage Holidays')

@section('content')
@php
  $resourcePageTitle = 'Manage Holidays';
  $resourcePageLead = 'Build the holiday calendar used by attendance and absence reporting. Optional branch and department IDs can be attached where scope is not company-wide.';
  $resourceEndpoint = '/api/attendance/admin/holidays';
  $resourceSingular = 'Holiday';
  $resourcePlural = 'Holidays';
  $resourceColumns = [
    ['key' => 'name', 'label' => 'Holiday'],
    ['key' => 'holiday_date', 'label' => 'Date'],
    ['key' => 'holiday_type', 'label' => 'Type'],
    ['key' => 'branch_id', 'label' => 'Branch'],
    ['key' => 'department_id', 'label' => 'Department'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
  ];
  $resourceFields = [
    ['name' => 'name', 'label' => 'Holiday Name', 'required' => true, 'placeholder' => 'Enter holiday name'],
    ['name' => 'holiday_date', 'label' => 'Holiday Date', 'type' => 'date', 'placeholder' => 'Select holiday date'],
    ['name' => 'holiday_type', 'label' => 'Holiday Type', 'type' => 'select', 'options' => ['public' => 'Public Holiday', 'festival' => 'Festival Holiday', 'branch' => 'Branch Holiday', 'restricted' => 'Restricted Holiday'], 'placeholder' => 'Select holiday type'],
    ['name' => 'branch_id', 'label' => 'Branch', 'type' => 'select', 'source' => 'branches', 'help' => 'Optional. Select a branch when the holiday is branch-specific.'],
    ['name' => 'department_id', 'label' => 'Department', 'type' => 'select', 'source' => 'departments', 'help' => 'Optional. Select a department when the holiday is team-specific.'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Describe the holiday scope'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
  ];
  $resourceDefaults = ['status' => 'active'];
@endphp
@include('modules.attendance.partials.manageResource')
@endsection
