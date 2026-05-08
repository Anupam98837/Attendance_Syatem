@extends('pages.layout.structure')

@section('title', 'Manage Designations')

@section('content')
@php
  $resourcePageTitle = 'Manage Designations';
  $resourcePageLead = 'Keep the designation master clean so employee profiles and workforce analytics stay meaningful.';
  $resourceEndpoint = '/api/attendance/admin/designations';
  $resourceSingular = 'Designation';
  $resourcePlural = 'Designations';
  $resourceColumns = [
    ['key' => 'name', 'label' => 'Designation'],
    ['key' => 'code', 'label' => 'Code'],
    ['key' => 'description', 'label' => 'Description'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
  ];
  $resourceFields = [
    ['name' => 'name', 'label' => 'Designation Name', 'required' => true, 'placeholder' => 'Enter designation name'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Enter designation code'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Describe the designation'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
  ];
  $resourceDefaults = ['status' => 'active'];
@endphp
@include('modules.attendance.partials.manageResource')
@endsection
