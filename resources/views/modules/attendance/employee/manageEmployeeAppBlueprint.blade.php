@extends('pages.layout.structure')

@section('title', 'Employee App Blueprint')

@section('content')
@php
  $pageIcon = 'fa-solid fa-mobile-screen-button';
  $pageKicker = 'Employee Experience';
  $pageTitle = 'Employee Mobile Attendance Blueprint';
  $pageSummary = 'This screen is the web version of the employee app flow so you can convert the same UX into mobile later. It follows the hybrid logic already supported by the backend: bootstrap, check-in/out, offline queue, sync, history, and leave.';
  $pagePersona = 'Employee';
  $pageCadence = 'Daily app usage';
  $pageStatus = 'Ready to convert into app screens';
  $heroPoints = [
    ['icon' => 'fa-solid fa-download', 'title' => 'Bootstrap first', 'text' => 'The app should always start by downloading the employee’s current branch, shift, policy, and work-mode rules.'],
    ['icon' => 'fa-solid fa-arrows-rotate', 'title' => 'Online and offline aware', 'text' => 'The same user action should work in both connected and disconnected states.'],
    ['icon' => 'fa-solid fa-location-arrow', 'title' => 'Track from start to end', 'text' => 'Once attendance starts, the app can keep sending location pings until checkout when policy allows.'],
  ];
  $metrics = [
    ['icon' => 'fa-solid fa-right-to-bracket', 'label' => 'Login', 'value' => 'Token Based', 'note' => 'App uses the existing auth flow.'],
    ['icon' => 'fa-solid fa-user-check', 'label' => 'Punch', 'value' => 'Check-in / Check-out', 'note' => 'Validated against branch, shift, and policy.'],
    ['icon' => 'fa-solid fa-database', 'label' => 'Offline Queue', 'value' => 'SQLite', 'note' => 'App stores unsynced punches locally until network returns.'],
    ['icon' => 'fa-solid fa-map-location-dot', 'label' => 'Tracking', 'value' => 'Ping During Session', 'note' => 'Optional continuous location stream while attendance is open.'],
  ];
  $primaryTitle = 'Employee Screen Set';
  $primaryIntro = 'These are the core mobile surfaces you can build directly from the backend that is already in place.';
  $primaryBlocks = [
    ['title' => 'Dashboard', 'text' => 'Show assigned branch, shift, attendance policy, today status, and sync health.'],
    ['title' => 'Check In / Check Out', 'text' => 'Capture GPS, selfie, device info, network type, and remarks if the policy asks for them.'],
    ['title' => 'History & Leave', 'text' => 'Let the employee review attendance rows, sync queue outcomes, and leave requests without admin help.'],
  ];
  $secondaryTitle = 'Mobile Behavior Rules';
  $secondaryIntro = 'The app should stay honest even when offline.';
  $secondaryBlocks = [
    ['title' => 'Never trust device time alone', 'text' => 'Send device punch time, but let the server compare sync delay and drift.'],
    ['title' => 'Queue items need stable local IDs', 'text' => 'Each offline punch should have a unique queue identifier to prevent duplicates.'],
    ['title' => 'Keep sync visible to employees', 'text' => 'People should know whether a punch is synced, pending, or flagged.'],
  ];
  $flowSteps = [
    ['title' => 'Employee logs in and bootstraps', 'text' => 'The app loads policy, branch, shift, and employee context.'],
    ['title' => 'Employee punches online or offline', 'text' => 'If online, record is sent immediately. If offline, it is stored locally.'],
    ['title' => 'Sync and review continue', 'text' => 'When connectivity returns, queued items sync and the app can show updated results.'],
  ];
  $dataPoints = [
    ['label' => 'mobile bootstrap'], ['label' => 'attendance_logs'], ['label' => 'attendance_sync_queue'], ['label' => 'attendance_location_tracks'], ['label' => 'leave_requests'],
  ];
  $apiRows = [
    ['method' => 'GET', 'endpoint' => '/api/attendance/mobile/bootstrap', 'purpose' => 'Load employee app context before the workday starts.'],
    ['method' => 'POST', 'endpoint' => '/api/attendance/mobile/punch', 'purpose' => 'Submit live check-in or check-out.'],
    ['method' => 'POST', 'endpoint' => '/api/attendance/mobile/sync', 'purpose' => 'Upload queued offline attendance items.'],
    ['method' => 'POST', 'endpoint' => '/api/attendance/mobile/location-ping', 'purpose' => 'Track employee location during an active attendance session.'],
    ['method' => 'GET', 'endpoint' => '/api/attendance/mobile/history', 'purpose' => 'Show employee attendance history in the app.'],
  ];
  $employeeCards = [
    [
      'title' => 'Employee Dashboard',
      'text' => 'Start with the minimum daily information the employee needs before they act.',
      'items' => [
        ['label' => 'Today', 'value' => 'Check-in pending'],
        ['label' => 'Shift', 'value' => 'General Shift'],
        ['label' => 'Branch', 'value' => 'Kolkata Office'],
        ['label' => 'Sync', 'value' => '0 pending'],
      ],
    ],
    [
      'title' => 'Attendance Capture',
      'text' => 'This screen should feel immediate and focused because it is used every day.',
      'items' => [
        ['label' => 'GPS', 'value' => 'Auto capture'],
        ['label' => 'Selfie', 'value' => 'Required'],
        ['label' => 'Network', 'value' => 'Wi-Fi / Offline'],
        ['label' => 'Mode', 'value' => 'Office'],
      ],
    ],
    [
      'title' => 'Offline Queue',
      'text' => 'Employees should always know if a punch is still waiting to sync.',
      'items' => [
        ['label' => 'Queue ID', 'value' => 'Local unique ID'],
        ['label' => 'Status', 'value' => 'Pending sync'],
        ['label' => 'Delay', 'value' => 'Show elapsed time'],
        ['label' => 'Retry', 'value' => 'Automatic'],
      ],
    ],
    [
      'title' => 'History & Leave',
      'text' => 'After attendance capture, employees need transparent access to their own records.',
      'items' => [
        ['label' => 'History', 'value' => 'Past punches'],
        ['label' => 'Leave', 'value' => 'Apply / track'],
        ['label' => 'Approval', 'value' => 'Show flagged items'],
        ['label' => 'Remarks', 'value' => 'Visible'],
      ],
    ],
  ];
@endphp
@include('modules.attendance.partials.workspace')
@endsection
