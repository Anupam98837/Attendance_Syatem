@extends('pages.layout.structure')

@section('title', 'Manage Leave Types')

@section('content')
@php
  $resourcePageTitle = 'Manage Leave Types';
  $resourcePageLead = 'Define the leave catalog used by the employee app and HR leave approval workflow.';
  $resourceEndpoint = '/api/attendance/admin/leave-types';
  $resourceSingular = 'Leave Type';
  $resourcePlural = 'Leave Types';
  $resourceColumns = [
    ['key' => 'name', 'label' => 'Leave Type'],
    ['key' => 'code', 'label' => 'Code'],
    ['key' => 'days_allowed', 'label' => 'Days Allowed'],
    ['key' => 'is_paid', 'label' => 'Paid', 'type' => 'bool'],
    ['key' => 'requires_approval', 'label' => 'Approval', 'type' => 'bool'],
    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
  ];
  $resourceFields = [
    ['name' => 'name', 'label' => 'Leave Type Name', 'required' => true, 'placeholder' => 'Enter leave type name'],
    ['name' => 'code', 'label' => 'Code', 'placeholder' => 'Enter leave code'],
    ['name' => 'days_allowed', 'label' => 'Days Allowed', 'type' => 'number', 'step' => '0.01', 'placeholder' => '12'],
    ['name' => 'is_paid', 'label' => 'Paid Leave', 'type' => 'checkbox'],
    ['name' => 'requires_approval', 'label' => 'Requires Approval', 'type' => 'checkbox'],
    ['name' => 'requires_document', 'label' => 'Requires Document', 'type' => 'checkbox'],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'full' => true, 'placeholder' => 'Describe when this leave type is used'],
    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive'], 'required' => true],
  ];
  $resourceDefaults = ['status' => 'active'];
@endphp
@include('modules.attendance.partials.manageResource')
@endsection
