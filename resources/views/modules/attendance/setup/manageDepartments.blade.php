@extends('pages.layout.structure')

@section('title', 'Manage Departments')

@section('content')
@php
  $resourcePageTitle = 'Manage Departments';
  $resourcePageLead = 'Maintain department master data used across user onboarding, employee attendance profiles, and reporting filters.';
  $resourceEndpoint = '/api/attendance/admin/departments';
  $resourceSingular = 'Department';
  $resourcePlural = 'Departments';
  $resourceColumns = [
    ['key' => 'name', 'label' => 'Department'],
    ['key' => 'code', 'label' => 'Code'],
    ['key' => 'description', 'label' => 'Description'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
  ];
  $resourceFields = [
    ['name' => 'name', 'label' => 'Department Name', 'required' => true, 'placeholder' => 'Enter department name'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Enter department code'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Describe the department'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
  ];
  $resourceDefaults = ['status' => 'active'];
@endphp
@include('modules.attendance.partials.manageResource')
@endsection
