<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function bootstrap(Request $request, string $doctorSlug)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        $doctor = $this->bookableDoctor($doctorSlug);
        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor is not available for booking.',
            ], 404);
        }

        $user = DB::table('users')
            ->where('id', $auth['user_id'])
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required.',
            ], 401);
        }

        $selfPatient = DB::table('patients')
            ->where('user_id', $auth['user_id'])
            ->whereNull('deleted_at')
            ->first();

        $nameParts = $this->splitName((string) ($user->name ?? ''));
        $clinics = $this->doctorClinicBootstrapRows((int) $doctor->doctor_id);

        return response()->json([
            'status' => 'success',
            'doctor' => [
                'id' => (int) $doctor->doctor_id,
                'slug' => (string) ($doctor->doctor_slug ?? ''),
                'name' => (string) ($doctor->doctor_name ?? ''),
                'designation' => (string) ($doctor->designation_name ?? ''),
            ],
            'booking_for_options' => [
                ['value' => 'self', 'label' => 'Me'],
                ['value' => 'family', 'label' => 'My Family'],
            ],
            'self_patient' => [
                'first_name' => (string) ($selfPatient->first_name ?? $nameParts['first_name']),
                'middle_name' => (string) ($selfPatient->middle_name ?? ''),
                'last_name' => (string) ($selfPatient->last_name ?? $nameParts['last_name']),
                'phone_number' => (string) ($selfPatient->phone_number ?? $user->phone_number ?? ''),
                'alternative_phone_number' => (string) ($selfPatient->alternative_phone_number ?? $user->alternative_phone_number ?? ''),
                'email' => (string) ($selfPatient->email ?? $user->email ?? ''),
                'address' => (string) ($selfPatient->address ?? $user->address ?? ''),
            ],
            'clinics' => $clinics,
        ]);
    }

    public function store(Request $request, string $doctorSlug)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        $doctor = $this->bookableDoctor($doctorSlug);
        if (!$doctor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor is not available for booking.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'booking_for' => 'required|string|in:self,family',
            'relationship_with_patient' => 'nullable|string|max:100',
            'clinic_id' => 'nullable|integer',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'nullable|date_format:H:i',
            'patient_first_name' => 'required|string|max:100',
            'patient_middle_name' => 'nullable|string|max:100',
            'patient_last_name' => 'nullable|string|max:100',
            'patient_phone_number' => 'required|string|max:32',
            'patient_alternative_phone_number' => 'nullable|string|max:32',
            'patient_email' => 'nullable|email|max:255',
            'patient_address' => 'required|string',
            'symptoms' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $bookableClinics = $this->doctorClinicMap((int) $doctor->doctor_id);
        $doctorClinic = null;

        if (!empty($data['clinic_id'])) {
            $doctorClinic = $bookableClinics->get((int) $data['clinic_id']);
            if (!$doctorClinic) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Selected clinic is not available for this doctor.',
                ], 422);
            }
        } elseif ($bookableClinics->isNotEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select a clinic for this booking.',
            ], 422);
        }

        if (($data['booking_for'] ?? '') === 'family' && empty(trim((string) ($data['relationship_with_patient'] ?? '')))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please mention the relationship for the family member booking.',
            ], 422);
        }

        $result = DB::transaction(function () use ($auth, $data, $doctor, $doctorClinic) {
            $now = now();
            $bookingFor = (string) $data['booking_for'];
            $payload = [
                'first_name' => trim((string) $data['patient_first_name']),
                'middle_name' => $this->nullableString($data['patient_middle_name'] ?? null),
                'last_name' => $this->nullableString($data['patient_last_name'] ?? null),
                'phone_number' => trim((string) $data['patient_phone_number']),
                'alternative_phone_number' => $this->nullableString($data['patient_alternative_phone_number'] ?? null),
                'email' => $this->nullableString($data['patient_email'] ?? null),
                'address' => $this->nullableString($data['patient_address'] ?? null),
                'status' => 'active',
                'updated_at' => $now,
                'metadata' => json_encode([
                    'source' => 'public_doctor_booking',
                    'booking_for' => $bookingFor,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];

            if ($bookingFor === 'self') {
                $patient = DB::table('patients')
                    ->where('user_id', $auth['user_id'])
                    ->whereNull('deleted_at')
                    ->first();

                if ($patient) {
                    DB::table('patients')->where('id', $patient->id)->update($payload);
                    $patientId = (int) $patient->id;
                } else {
                    $patientId = DB::table('patients')->insertGetId(array_merge($payload, [
                        'uuid' => (string) Str::uuid(),
                        'user_id' => $auth['user_id'],
                        'created_by_user_id' => $auth['user_id'],
                        'created_at' => $now,
                    ]));
                }
            } else {
                $patient = DB::table('patients')
                    ->where('created_by_user_id', $auth['user_id'])
                    ->where('phone_number', trim((string) $data['patient_phone_number']))
                    ->where('first_name', trim((string) $data['patient_first_name']))
                    ->whereNull('deleted_at')
                    ->first();

                if ($patient) {
                    DB::table('patients')->where('id', $patient->id)->update($payload);
                    $patientId = (int) $patient->id;
                } else {
                    $patientId = DB::table('patients')->insertGetId(array_merge($payload, [
                        'uuid' => (string) Str::uuid(),
                        'user_id' => null,
                        'created_by_user_id' => $auth['user_id'],
                        'created_at' => $now,
                    ]));
                }
            }

            $appointmentId = DB::table('appointments')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'booked_by_user_id' => $auth['user_id'],
                'patient_id' => $patientId,
                'doctor_id' => (int) $doctor->doctor_id,
                'clinic_id' => $doctorClinic ? (int) $doctorClinic->clinic_id : null,
                'doctor_clinic_id' => $doctorClinic ? (int) $doctorClinic->doctor_clinic_id : null,
                'booking_for' => $bookingFor,
                'relationship_with_patient' => $this->nullableString($data['relationship_with_patient'] ?? null),
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $this->nullableString($data['appointment_time'] ?? null),
                'consultation_mode' => 'clinic_visit',
                'status' => 'pending',
                'status_note' => null,
                'reviewed_by_user_id' => null,
                'reviewed_at' => null,
                'cancelled_at' => null,
                'completed_at' => null,
                'symptoms' => $this->nullableString($data['symptoms'] ?? null),
                'metadata' => json_encode([
                    'source' => 'public_doctor_booking',
                    'doctor_slug' => (string) ($doctor->doctor_slug ?? ''),
                    'doctor_name' => (string) ($doctor->doctor_name ?? ''),
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return [
                'appointment_id' => $appointmentId,
            ];
        });

        $appointment = DB::table('appointments')
            ->where('id', $result['appointment_id'])
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'appointment' => [
                'id' => (int) ($appointment->id ?? 0),
                'uuid' => (string) ($appointment->uuid ?? ''),
                'booking_for' => (string) ($appointment->booking_for ?? ''),
                'appointment_date' => (string) ($appointment->appointment_date ?? ''),
                'appointment_time' => (string) ($appointment->appointment_time ?? ''),
                'status' => (string) ($appointment->status ?? ''),
            ],
        ], 201);
    }

    public function dashboard(Request $request)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        $status = trim((string) $request->query('status', ''));
        $doctorId = (int) ($request->query('doctor_id') ?? 0);
        $dateFrom = $this->nullableString($request->query('date_from'));
        $dateTo = $this->nullableString($request->query('date_to'));

        if ($this->isAdminRole($auth['role'])) {
            $recentQuery = $this->appointmentBaseQuery();
            $this->applyAppointmentFilters($recentQuery, $status, $doctorId, $dateFrom, $dateTo);

            $recent = $recentQuery->limit(6)->get();

            return response()->json([
                'status' => 'success',
                'role' => 'admin',
                'counts' => $this->appointmentStatusCounts(),
                'admin_overview' => $this->adminDashboardOverview(),
                'doctor_options' => $this->doctorFilterOptions(),
                'links' => [
                    'manage_bookings' => '/bookings/manage',
                    'profile' => '/profile',
                ],
                'recent' => $this->appointmentRows($recent),
            ]);
        }

        $recentQuery = $this->appointmentBaseQuery()
            ->where('a.booked_by_user_id', $auth['user_id']);
        $this->applyAppointmentFilters($recentQuery, $status, $doctorId, $dateFrom, $dateTo);

        $recent = $recentQuery->limit(6)->get();

        return response()->json([
            'status' => 'success',
            'role' => 'patient',
            'counts' => $this->appointmentStatusCounts($auth['user_id']),
            'doctor_options' => $this->doctorFilterOptions($auth['user_id']),
            'links' => [
                'my_bookings' => '/my-bookings',
                'profile' => '/profile',
                'find_doctors' => '/find-doctors/departments',
            ],
            'recent' => $this->appointmentRows($recent, $auth['user_id']),
        ]);
    }

    public function myBookings(Request $request)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        $status = trim((string) $request->query('status', ''));
        $doctorId = (int) ($request->query('doctor_id') ?? 0);
        $dateFrom = $this->nullableString($request->query('date_from'));
        $dateTo = $this->nullableString($request->query('date_to'));

        $query = $this->appointmentBaseQuery()
            ->where('a.booked_by_user_id', $auth['user_id']);
        $this->applyAppointmentFilters($query, $status, $doctorId, $dateFrom, $dateTo);

        $rows = $query->get();

        return response()->json([
            'status' => 'success',
            'counts' => $this->appointmentStatusCounts($auth['user_id']),
            'doctor_options' => $this->doctorFilterOptions($auth['user_id']),
            'bookings' => $this->appointmentRows($rows, $auth['user_id']),
        ]);
    }

    public function cancelMyBooking(Request $request, int $appointmentId)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        $validator = Validator::make($request->all(), [
            'status_note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $appointment = DB::table('appointments')
            ->where('id', $appointmentId)
            ->where('booked_by_user_id', $auth['user_id'])
            ->whereNull('deleted_at')
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found.',
            ], 404);
        }

        if (!in_array((string) $appointment->status, ['pending', 'approved'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This booking can no longer be cancelled.',
            ], 422);
        }

        DB::table('appointments')
            ->where('id', $appointmentId)
            ->update([
                'status' => 'cancelled',
                'status_note' => $this->nullableString($validator->validated()['status_note'] ?? null) ?: 'Cancelled by the user.',
                'reviewed_by_user_id' => $auth['user_id'],
                'reviewed_at' => now(),
                'cancelled_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking cancelled successfully.',
        ]);
    }

    public function submitReview(Request $request, int $appointmentId)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:160',
            'review_text' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $appointment = DB::table('appointments')
            ->where('id', $appointmentId)
            ->where('booked_by_user_id', $auth['user_id'])
            ->whereNull('deleted_at')
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found.',
            ], 404);
        }

        if ((string) $appointment->status !== 'done') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only completed bookings can be reviewed.',
            ], 422);
        }

        $existing = DB::table('doctor_reviews')
            ->where('appointment_id', $appointmentId)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'A review has already been submitted for this booking.',
            ], 422);
        }

        $data = $validator->validated();

        DB::transaction(function () use ($appointment, $auth, $data) {
            DB::table('doctor_reviews')->insert([
                'uuid' => (string) Str::uuid(),
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'patient_id' => $appointment->patient_id,
                'booked_by_user_id' => $auth['user_id'],
                'rating' => (int) $data['rating'],
                'title' => $this->nullableString($data['title'] ?? null),
                'review_text' => trim((string) $data['review_text']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->syncDoctorReviewAggregate((int) $appointment->doctor_id);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Review submitted successfully.',
        ]);
    }

    public function adminIndex(Request $request)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        if (!$this->isAdminRole($auth['role'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin users can manage bookings.',
            ], 403);
        }

        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));
        $doctorId = (int) ($request->query('doctor_id') ?? 0);
        $dateFrom = $this->nullableString($request->query('date_from'));
        $dateTo = $this->nullableString($request->query('date_to'));

        $query = $this->appointmentBaseQuery();

        $this->applyAppointmentFilters($query, $status, $doctorId, $dateFrom, $dateTo);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $like = '%' . $search . '%';
                $builder
                    ->where('doctor_user.name', 'like', $like)
                    ->orWhere('p.first_name', 'like', $like)
                    ->orWhere('p.last_name', 'like', $like)
                    ->orWhere('p.phone_number', 'like', $like)
                    ->orWhere('booker.name', 'like', $like)
                    ->orWhere('c.name', 'like', $like);
            });
        }

        $rows = $query->get();

        return response()->json([
            'status' => 'success',
            'counts' => $this->appointmentStatusCounts(),
            'doctor_options' => $this->doctorFilterOptions(),
            'bookings' => $this->appointmentRows($rows),
        ]);
    }

    public function adminUpdateStatus(Request $request, int $appointmentId)
    {
        $auth = $this->authContext($request);
        if ($auth['error']) {
            return $auth['error'];
        }

        if (!$this->isAdminRole($auth['role'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin users can manage bookings.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:approved,rejected,done',
            'status_note' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $appointment = DB::table('appointments')
            ->where('id', $appointmentId)
            ->whereNull('deleted_at')
            ->first();

        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found.',
            ], 404);
        }

        if ((string) $appointment->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'This booking can no longer be approved or rejected.',
            ], 422);
        }

        $data = $validator->validated();
        $nextStatus = (string) $data['status'];

        if ($nextStatus === 'done' && (string) $appointment->status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only approved bookings can be marked as done.',
            ], 422);
        }

        DB::transaction(function () use ($appointment, $appointmentId, $auth, $data, $nextStatus) {
            DB::table('appointments')
                ->where('id', $appointmentId)
                ->update([
                    'status' => $nextStatus,
                    'status_note' => trim((string) $data['status_note']),
                    'reviewed_by_user_id' => $auth['user_id'],
                    'reviewed_at' => now(),
                    'cancelled_at' => $nextStatus === 'cancelled' ? now() : null,
                    'completed_at' => $nextStatus === 'done'
                        ? ($appointment->completed_at ?: now())
                        : $appointment->completed_at,
                    'updated_at' => now(),
                ]);

            if ($nextStatus === 'done' && empty($appointment->completed_at)) {
                DB::table('doctors')
                    ->where('id', $appointment->doctor_id)
                    ->increment('total_patients_treated', 1, ['updated_at' => now()]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Booking status updated successfully.',
        ]);
    }

    private function authContext(Request $request): array
    {
        $userId = (int) ($request->attributes->get('auth_user_id') ?? 0);

        if ($userId <= 0) {
            return [
                'user_id' => 0,
                'role' => '',
                'error' => response()->json([
                    'status' => 'error',
                    'message' => 'Authentication required.',
                ], 401),
            ];
        }

        return [
            'user_id' => $userId,
            'role' => (string) ($request->attributes->get('auth_role') ?? ''),
            'error' => null,
        ];
    }

    private function bookableDoctor(string $doctorSlug): ?object
    {
        return DB::table('doctors as d')
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'd.user_id')
                    ->whereNull('u.deleted_at')
                    ->where('u.status', 'active');
            })
            ->leftJoin('designations as des', 'des.id', '=', 'd.designation_id')
            ->where('d.slug', $doctorSlug)
            ->whereNull('d.deleted_at')
            ->where('d.status', 'active')
            ->where('d.profile_visibility', 'public')
            ->where('d.appointment_booking_available', true)
            ->first([
                'd.id as doctor_id',
                'd.slug as doctor_slug',
                'u.name as doctor_name',
                'des.name as designation_name',
            ]);
    }

    private function doctorClinicBootstrapRows(int $doctorId): Collection
    {
        return DB::table('doctor_clinics as dc')
            ->join('clinics as c', 'c.id', '=', 'dc.clinic_id')
            ->where('dc.doctor_id', $doctorId)
            ->where('dc.appointment_booking_available', true)
            ->whereNull('c.deleted_at')
            ->where('c.status', 'active')
            ->orderByDesc('dc.is_primary')
            ->orderBy('dc.sort_order')
            ->orderBy('c.name')
            ->get([
                'dc.id as doctor_clinic_id',
                'dc.clinic_id',
                'dc.is_primary',
                'dc.consultation_fee',
                'dc.followup_fee',
                'dc.video_consultation_fee',
                'dc.online_consultation_available',
                'dc.in_person_consultation_available',
                'dc.room_no',
                'dc.visit_note',
                'c.name',
                'c.area',
                'c.city',
                'c.state',
                'c.address_line_1',
            ])->map(function ($row) {
                return [
                    'doctor_clinic_id' => (int) $row->doctor_clinic_id,
                    'clinic_id' => (int) $row->clinic_id,
                    'name' => (string) ($row->name ?? ''),
                    'is_primary' => (bool) ($row->is_primary ?? false),
                    'location' => collect([
                        (string) ($row->area ?? ''),
                        (string) ($row->city ?? ''),
                        (string) ($row->state ?? ''),
                    ])->filter()->implode(', '),
                    'address_line_1' => (string) ($row->address_line_1 ?? ''),
                    'consultation_fee' => $row->consultation_fee,
                    'followup_fee' => $row->followup_fee,
                    'video_consultation_fee' => $row->video_consultation_fee,
                    'online_consultation_available' => (bool) ($row->online_consultation_available ?? false),
                    'in_person_consultation_available' => (bool) ($row->in_person_consultation_available ?? false),
                    'room_no' => (string) ($row->room_no ?? ''),
                    'visit_note' => (string) ($row->visit_note ?? ''),
                ];
            })->values();
    }

    private function doctorClinicMap(int $doctorId)
    {
        return DB::table('doctor_clinics as dc')
            ->join('clinics as c', 'c.id', '=', 'dc.clinic_id')
            ->where('dc.doctor_id', $doctorId)
            ->where('dc.appointment_booking_available', true)
            ->whereNull('c.deleted_at')
            ->where('c.status', 'active')
            ->get([
                'dc.id as doctor_clinic_id',
                'dc.clinic_id',
                'dc.online_consultation_available',
                'dc.in_person_consultation_available',
            ])->keyBy(fn ($row) => (int) $row->clinic_id);
    }

    private function appointmentBaseQuery()
    {
        return DB::table('appointments as a')
            ->join('patients as p', 'p.id', '=', 'a.patient_id')
            ->join('doctors as d', 'd.id', '=', 'a.doctor_id')
            ->join('users as doctor_user', 'doctor_user.id', '=', 'd.user_id')
            ->join('users as booker', 'booker.id', '=', 'a.booked_by_user_id')
            ->leftJoin('clinics as c', 'c.id', '=', 'a.clinic_id')
            ->leftJoin('users as reviewer', 'reviewer.id', '=', 'a.reviewed_by_user_id')
            ->leftJoin('doctor_clinics as dc', 'dc.id', '=', 'a.doctor_clinic_id')
            ->leftJoin('doctor_reviews as dr', function ($join) {
                $join->on('dr.appointment_id', '=', 'a.id')
                    ->whereNull('dr.deleted_at');
            })
            ->whereNull('a.deleted_at')
            ->orderByDesc('a.created_at')
            ->select([
                'a.id',
                'a.uuid',
                'a.booked_by_user_id',
                'a.patient_id',
                'a.doctor_id',
                'a.booking_for',
                'a.relationship_with_patient',
                'a.appointment_date',
                'a.appointment_time',
                'a.consultation_mode',
                'a.status',
                'a.status_note',
                'a.reviewed_at',
                'a.cancelled_at',
                'a.completed_at',
                'a.created_at',
                'a.symptoms',
                'doctor_user.name as doctor_name',
                'd.slug as doctor_slug',
                'c.name as clinic_name',
                'c.address_line_1 as clinic_address',
                'c.area as clinic_area',
                'c.city as clinic_city',
                'c.state as clinic_state',
                'dc.room_no as clinic_room_no',
                'dc.visit_note as clinic_visit_note',
                'p.first_name as patient_first_name',
                'p.middle_name as patient_middle_name',
                'p.last_name as patient_last_name',
                'p.phone_number as patient_phone_number',
                'p.alternative_phone_number as patient_alternative_phone_number',
                'p.email as patient_email',
                'p.address as patient_address',
                'booker.name as booked_by_name',
                'booker.email as booked_by_email',
                'booker.phone_number as booked_by_phone_number',
                'reviewer.name as reviewed_by_name',
                'dr.id as review_id',
                'dr.rating as review_rating',
                'dr.title as review_title',
                'dr.review_text',
                'dr.created_at as review_created_at',
            ]);
    }

    private function appointmentRows($rows, ?int $viewerUserId = null): array
    {
        return collect($rows)->map(function ($row) use ($viewerUserId) {
            $patientName = collect([
                (string) ($row->patient_first_name ?? ''),
                (string) ($row->patient_middle_name ?? ''),
                (string) ($row->patient_last_name ?? ''),
            ])->filter()->implode(' ');

            $status = strtolower((string) ($row->status ?? ''));

            return [
                'id' => (int) ($row->id ?? 0),
                'uuid' => (string) ($row->uuid ?? ''),
                'doctor_id' => (int) ($row->doctor_id ?? 0),
                'doctor_name' => (string) ($row->doctor_name ?? ''),
                'doctor_slug' => (string) ($row->doctor_slug ?? ''),
                'clinic_name' => (string) ($row->clinic_name ?? ''),
                'clinic_address' => (string) ($row->clinic_address ?? ''),
                'clinic_location' => collect([
                    (string) ($row->clinic_area ?? ''),
                    (string) ($row->clinic_city ?? ''),
                    (string) ($row->clinic_state ?? ''),
                ])->filter()->implode(', '),
                'clinic_room_no' => (string) ($row->clinic_room_no ?? ''),
                'clinic_visit_note' => (string) ($row->clinic_visit_note ?? ''),
                'patient_name' => $patientName,
                'patient_phone_number' => (string) ($row->patient_phone_number ?? ''),
                'patient_alternative_phone_number' => (string) ($row->patient_alternative_phone_number ?? ''),
                'patient_email' => (string) ($row->patient_email ?? ''),
                'patient_address' => (string) ($row->patient_address ?? ''),
                'booked_by_name' => (string) ($row->booked_by_name ?? ''),
                'booked_by_email' => (string) ($row->booked_by_email ?? ''),
                'booked_by_phone_number' => (string) ($row->booked_by_phone_number ?? ''),
                'booking_for' => (string) ($row->booking_for ?? ''),
                'relationship_with_patient' => (string) ($row->relationship_with_patient ?? ''),
                'appointment_date' => (string) ($row->appointment_date ?? ''),
                'appointment_time' => (string) ($row->appointment_time ?? ''),
                'consultation_mode' => (string) ($row->consultation_mode ?? ''),
                'status' => $status,
                'status_note' => (string) ($row->status_note ?? ''),
                'reviewed_at' => (string) ($row->reviewed_at ?? ''),
                'reviewed_by_name' => (string) ($row->reviewed_by_name ?? ''),
                'cancelled_at' => (string) ($row->cancelled_at ?? ''),
                'completed_at' => (string) ($row->completed_at ?? ''),
                'created_at' => (string) ($row->created_at ?? ''),
                'symptoms' => (string) ($row->symptoms ?? ''),
                'review_id' => $row->review_id ? (int) $row->review_id : null,
                'review_rating' => $row->review_rating ? (int) $row->review_rating : null,
                'review_title' => (string) ($row->review_title ?? ''),
                'review_text' => (string) ($row->review_text ?? ''),
                'review_created_at' => (string) ($row->review_created_at ?? ''),
                'can_cancel' => $viewerUserId !== null
                    && (int) ($row->booked_by_user_id ?? 0) === $viewerUserId
                    && in_array($status, ['pending', 'approved'], true),
                'can_review' => $viewerUserId !== null
                    && (int) ($row->booked_by_user_id ?? 0) === $viewerUserId
                    && $status === 'done'
                    && empty($row->review_id),
            ];
        })->values()->all();
    }

    private function appointmentStatusCounts(?int $bookedByUserId = null): array
    {
        $query = DB::table('appointments as a')
            ->whereNull('a.deleted_at');

        if ($bookedByUserId !== null) {
            $query->where('a.booked_by_user_id', $bookedByUserId);
        }

        $rows = $query
            ->select('a.status', DB::raw('COUNT(*) as total'))
            ->groupBy('a.status')
            ->pluck('total', 'status');

        return [
            'total' => (int) $rows->sum(),
            'pending' => (int) ($rows['pending'] ?? 0),
            'approved' => (int) ($rows['approved'] ?? 0),
            'done' => (int) ($rows['done'] ?? 0),
            'rejected' => (int) ($rows['rejected'] ?? 0),
            'cancelled' => (int) ($rows['cancelled'] ?? 0),
        ];
    }

    private function adminDashboardOverview(): array
    {
        $counts = $this->appointmentStatusCounts();
        $today = now()->toDateString();
        $last7Days = collect(range(6, 0))->map(function (int $daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'key' => $date->toDateString(),
                'label' => $date->format('M d'),
            ];
        })->values();

        $trendRows = DB::table('appointments as a')
            ->whereNull('a.deleted_at')
            ->whereDate('a.created_at', '>=', now()->subDays(6)->toDateString())
            ->selectRaw('DATE(a.created_at) as booking_day')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN a.status = 'done' THEN 1 ELSE 0 END) as done_count")
            ->selectRaw("SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->groupBy(DB::raw('DATE(a.created_at)'))
            ->get()
            ->keyBy('booking_day');

        $trend = $last7Days->map(function (array $day) use ($trendRows) {
            $row = $trendRows->get($day['key']);

            return [
                'date' => $day['key'],
                'label' => $day['label'],
                'total' => (int) ($row->total_count ?? 0),
                'done' => (int) ($row->done_count ?? 0),
                'pending' => (int) ($row->pending_count ?? 0),
            ];
        })->values()->all();

        $bookingMix = DB::table('appointments as a')
            ->whereNull('a.deleted_at')
            ->select('a.booking_for', DB::raw('COUNT(*) as total'))
            ->groupBy('a.booking_for')
            ->pluck('total', 'booking_for');

        $topDoctors = DB::table('appointments as a')
            ->join('doctors as d', 'd.id', '=', 'a.doctor_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->whereNull('a.deleted_at')
            ->whereNull('d.deleted_at')
            ->whereNull('u.deleted_at')
            ->select([
                'd.id as doctor_id',
                'u.name as doctor_name',
                'd.total_patients_treated',
                'd.average_rating',
                'd.review_count',
            ])
            ->selectRaw('COUNT(a.id) as total_bookings')
            ->selectRaw("SUM(CASE WHEN a.status = 'done' THEN 1 ELSE 0 END) as done_bookings")
            ->selectRaw("SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending_bookings")
            ->groupBy('d.id', 'u.name', 'd.total_patients_treated', 'd.average_rating', 'd.review_count')
            ->orderByDesc('total_bookings')
            ->orderByDesc('done_bookings')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                return [
                    'doctor_id' => (int) ($row->doctor_id ?? 0),
                    'name' => (string) ($row->doctor_name ?? ''),
                    'total_bookings' => (int) ($row->total_bookings ?? 0),
                    'done_bookings' => (int) ($row->done_bookings ?? 0),
                    'pending_bookings' => (int) ($row->pending_bookings ?? 0),
                    'total_patients_treated' => (int) ($row->total_patients_treated ?? 0),
                    'average_rating' => round((float) ($row->average_rating ?? 0), 1),
                    'review_count' => (int) ($row->review_count ?? 0),
                ];
            })->values()->all();

        $totalReviews = DB::table('doctor_reviews')
            ->whereNull('deleted_at')
            ->count();

        $averageReview = DB::table('doctor_reviews')
            ->whereNull('deleted_at')
            ->avg('rating');

        $cards = [
            [
                'key' => 'total_bookings',
                'label' => 'Total Bookings',
                'value' => (int) $counts['total'],
                'meta' => 'All appointment requests in the system',
                'icon' => 'fa-calendar-check',
                'tone' => 'primary',
            ],
            [
                'key' => 'pending_bookings',
                'label' => 'Pending Action',
                'value' => (int) $counts['pending'],
                'meta' => 'Bookings waiting for admin review',
                'icon' => 'fa-hourglass-half',
                'tone' => 'warning',
            ],
            [
                'key' => 'done_bookings',
                'label' => 'Completed Visits',
                'value' => (int) $counts['done'],
                'meta' => 'Appointments already marked done',
                'icon' => 'fa-circle-check',
                'tone' => 'success',
            ],
            [
                'key' => 'active_doctors',
                'label' => 'Active Doctors',
                'value' => DB::table('doctors')
                    ->whereNull('deleted_at')
                    ->where('status', 'active')
                    ->count(),
                'meta' => 'Doctor profiles available in the platform',
                'icon' => 'fa-user-doctor',
                'tone' => 'info',
            ],
            [
                'key' => 'active_patients',
                'label' => 'Patient Records',
                'value' => DB::table('patients')
                    ->whereNull('deleted_at')
                    ->where('status', 'active')
                    ->count(),
                'meta' => 'Stored patient profiles and family records',
                'icon' => 'fa-user-group',
                'tone' => 'violet',
            ],
            [
                'key' => 'active_clinics',
                'label' => 'Active Clinics',
                'value' => DB::table('clinics')
                    ->whereNull('deleted_at')
                    ->where('status', 'active')
                    ->count(),
                'meta' => 'Clinics available for booking',
                'icon' => 'fa-hospital',
                'tone' => 'neutral',
            ],
        ];

        return [
            'cards' => $cards,
            'trend' => $trend,
            'status_distribution' => $counts,
            'booking_mix' => [
                'self' => (int) ($bookingMix['self'] ?? 0),
                'family' => (int) ($bookingMix['family'] ?? 0),
            ],
            'today' => [
                'new_bookings' => DB::table('appointments')
                    ->whereNull('deleted_at')
                    ->whereDate('created_at', $today)
                    ->count(),
                'appointments' => DB::table('appointments')
                    ->whereNull('deleted_at')
                    ->whereDate('appointment_date', $today)
                    ->count(),
            ],
            'reviews' => [
                'total' => (int) $totalReviews,
                'average_rating' => round((float) ($averageReview ?? 0), 1),
            ],
            'top_doctors' => $topDoctors,
        ];
    }

    private function doctorFilterOptions(?int $bookedByUserId = null): array
    {
        $query = DB::table('appointments as a')
            ->join('doctors as d', 'd.id', '=', 'a.doctor_id')
            ->join('users as u', 'u.id', '=', 'd.user_id')
            ->whereNull('a.deleted_at')
            ->whereNull('d.deleted_at')
            ->whereNull('u.deleted_at');

        if ($bookedByUserId !== null) {
            $query->where('a.booked_by_user_id', $bookedByUserId);
        }

        return $query
            ->distinct()
            ->orderBy('u.name')
            ->get(['d.id as doctor_id', 'u.name as doctor_name'])
            ->map(fn ($row) => [
                'id' => (int) ($row->doctor_id ?? 0),
                'name' => (string) ($row->doctor_name ?? ''),
            ])->values()->all();
    }

    private function applyAppointmentFilters($query, string $status, int $doctorId, ?string $dateFrom, ?string $dateTo): void
    {
        if ($status !== '' && $status !== 'all') {
            $query->where('a.status', $status);
        }

        if ($doctorId > 0) {
            $query->where('a.doctor_id', $doctorId);
        }

        if ($dateFrom) {
            $query->whereDate('a.appointment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('a.appointment_date', '<=', $dateTo);
        }
    }

    private function syncDoctorReviewAggregate(int $doctorId): void
    {
        $aggregate = DB::table('doctor_reviews')
            ->where('doctor_id', $doctorId)
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as review_count, COALESCE(AVG(rating), 0) as average_rating')
            ->first();

        DB::table('doctors')
            ->where('id', $doctorId)
            ->update([
                'review_count' => (int) ($aggregate->review_count ?? 0),
                'average_rating' => round((float) ($aggregate->average_rating ?? 0), 2),
                'updated_at' => now(),
            ]);
    }

    private function isAdminRole(string $role): bool
    {
        return in_array(strtolower(trim($role)), ['admin'], true);
    }

    private function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        $parts = array_values(array_filter($parts, fn ($value) => $value !== ''));

        if (empty($parts)) {
            return ['first_name' => '', 'last_name' => ''];
        }

        if (count($parts) === 1) {
            return ['first_name' => $parts[0], 'last_name' => ''];
        }

        return [
            'first_name' => array_shift($parts),
            'last_name' => implode(' ', $parts),
        ];
    }

    private function nullableString($value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value !== '' ? $value : null;
    }
}
