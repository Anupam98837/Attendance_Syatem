<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicDirectoryController extends Controller
{
    private function assetUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = (string) $value;
        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        return '/' . ltrim($value, '/');
    }

    private function jsonArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) ($value ?? '[]'), true);
        return is_array($decoded) ? array_values($decoded) : [];
    }

    private function activeDoctorQuery()
    {
        return DB::table('doctors as d')
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'd.user_id')
                    ->whereNull('u.deleted_at')
                    ->where('u.status', 'active');
            })
            ->leftJoin('designations as des', 'des.id', '=', 'd.designation_id')
            ->leftJoin('departments as dep', 'dep.id', '=', 'd.primary_department_id')
            ->leftJoin('specializations as sp', 'sp.id', '=', 'd.primary_specialization_id')
            ->leftJoin('hospitals as h', 'h.id', '=', 'd.primary_hospital_id')
            ->whereNull('d.deleted_at')
            ->where('d.status', 'active')
            ->where('d.profile_visibility', 'public');
    }

    private function doctorCardColumns(): array
    {
        return [
            'd.id as doctor_id',
            'd.slug as doctor_slug',
            'd.uuid as doctor_uuid',
            'd.featured_status',
            'd.qualification_summary',
            'd.years_of_experience',
            'd.short_bio',
            'd.consultation_fee',
            'd.followup_fee',
            'd.video_consultation_fee',
            'd.average_rating',
            'd.review_count',
            'd.online_consultation_available',
            'd.in_person_consultation_available',
            'd.appointment_booking_available',
            'u.uuid as user_uuid',
            'u.name as doctor_name',
            'u.phone_number as doctor_phone',
            'u.whatsapp_number',
            'u.image as user_image',
            'des.name as designation_name',
            'dep.name as department_name',
            'dep.slug as department_slug',
            'sp.name as specialization_name',
            'h.name as hospital_name',
            'h.slug as hospital_slug',
        ];
    }

    private function doctorCardPayload(object $row): array
    {
        $bestFee = $row->consultation_fee ?? $row->video_consultation_fee ?? $row->followup_fee;

        return [
            'name' => (string) ($row->doctor_name ?? 'Doctor'),
            'slug' => (string) ($row->doctor_slug ?? ''),
            'href' => route('directory.doctors.show', ['slug' => (string) ($row->doctor_slug ?? '')]),
            'image' => $this->assetUrl($row->user_image ?? null),
            'designation' => (string) ($row->designation_name ?? ''),
            'department' => (string) ($row->department_name ?? ''),
            'department_slug' => (string) ($row->department_slug ?? ''),
            'specialization' => (string) ($row->specialization_name ?? ''),
            'hospital' => (string) ($row->hospital_name ?? ''),
            'hospital_slug' => (string) ($row->hospital_slug ?? ''),
            'qualification_summary' => (string) ($row->qualification_summary ?? ''),
            'years_of_experience' => (int) ($row->years_of_experience ?? 0),
            'short_bio' => Str::limit(trim((string) ($row->short_bio ?? '')), 130),
            'consultation_fee' => $bestFee !== null ? number_format((float) $bestFee, 0) : null,
            'rating' => number_format((float) ($row->average_rating ?? 0), 1),
            'review_count' => (int) ($row->review_count ?? 0),
            'featured' => (bool) ($row->featured_status ?? false),
            'online_consultation_available' => (bool) ($row->online_consultation_available ?? false),
            'in_person_consultation_available' => (bool) ($row->in_person_consultation_available ?? false),
            'appointment_booking_available' => (bool) ($row->appointment_booking_available ?? false),
            'phone' => (string) ($row->doctor_phone ?? ''),
            'whatsapp' => (string) ($row->whatsapp_number ?? ''),
            'call_href' => !empty($row->doctor_phone) ? 'tel:' . preg_replace('/[^0-9+]/', '', (string) $row->doctor_phone) : '',
            'appointment_href' => route('directory.doctors.show', ['slug' => (string) ($row->doctor_slug ?? '')]),
        ];
    }

    private function departmentDoctorCountSub(): \Illuminate\Database\Query\Builder
    {
        return DB::table('doctors as d')
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'd.user_id')
                    ->whereNull('u.deleted_at')
                    ->where('u.status', 'active');
            })
            ->selectRaw('COUNT(*)')
            ->whereNull('d.deleted_at')
            ->where('d.status', 'active')
            ->where('d.profile_visibility', 'public')
            ->whereColumn('d.primary_department_id', 'departments.id');
    }

    private function hospitalDoctorCountSub(): \Illuminate\Database\Query\Builder
    {
        return DB::table('doctors as d')
            ->join('users as u', function ($join) {
                $join->on('u.id', '=', 'd.user_id')
                    ->whereNull('u.deleted_at')
                    ->where('u.status', 'active');
            })
            ->selectRaw('COUNT(*)')
            ->whereNull('d.deleted_at')
            ->where('d.status', 'active')
            ->where('d.profile_visibility', 'public')
            ->whereColumn('d.primary_hospital_id', 'hospitals.id');
    }

    private function departmentCards(?int $limit = null, ?string $search = null): Collection
    {
        $query = DB::table('departments')
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->select([
                'id',
                'uuid',
                'name',
                'short_form',
                'slug',
                'image',
                'description',
                'sort_order',
            ])
            ->selectSub($this->departmentDoctorCountSub(), 'doctor_count')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($search) {
            $like = '%' . trim($search) . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('short_form', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->map(function ($row) {
            $count = (int) ($row->doctor_count ?? 0);

            return [
                'name' => (string) ($row->name ?? ''),
                'short_form' => (string) ($row->short_form ?? ''),
                'slug' => (string) ($row->slug ?? ''),
                'href' => route('directory.departments.show', ['slug' => (string) ($row->slug ?? '')]),
                'image' => $this->assetUrl($row->image ?? null),
                'description' => Str::limit(strip_tags((string) ($row->description ?? 'Dedicated doctors and modern care experience.')), 125),
                'doctor_count' => $count,
                'doctor_count_label' => $count === 1 ? '1 doctor' : $count . ' doctors',
            ];
        })->values();
    }

    private function hospitalCards(int $limit = 6): Collection
    {
        return DB::table('hospitals')
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->select([
                'id',
                'name',
                'slug',
                'image',
                'logo',
                'city',
                'state',
                'hospital_type',
                'short_description',
            ])
            ->selectSub($this->hospitalDoctorCountSub(), 'doctor_count')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => (string) ($row->name ?? ''),
                    'slug' => (string) ($row->slug ?? ''),
                    'image' => $this->assetUrl($row->image ?: $row->logo),
                    'location' => collect([(string) ($row->city ?? ''), (string) ($row->state ?? '')])->filter()->implode(', '),
                    'hospital_type' => (string) ($row->hospital_type ?? ''),
                    'description' => Str::limit((string) ($row->short_description ?? 'Trusted care environment with experienced specialists.'), 110),
                    'doctor_count' => (int) ($row->doctor_count ?? 0),
                ];
            })->values();
    }

    public function home()
    {
        $departmentCount = DB::table('departments')->whereNull('deleted_at')->where('status', 'active')->count();
        $doctorCount = (clone $this->activeDoctorQuery())->count();
        $hospitalCount = DB::table('hospitals')->whereNull('deleted_at')->where('status', 'active')->count();

        $featuredDoctors = (clone $this->activeDoctorQuery())
            ->where('d.featured_status', true)
            ->orderByDesc('d.average_rating')
            ->orderByDesc('d.review_count')
            ->orderBy('u.name')
            ->limit(6)
            ->get($this->doctorCardColumns())
            ->map(fn ($row) => $this->doctorCardPayload($row))
            ->values();

        if ($featuredDoctors->count() < 6) {
            $exclude = $featuredDoctors->pluck('slug')->filter()->values()->all();

            $fallback = (clone $this->activeDoctorQuery())
                ->when(!empty($exclude), fn ($q) => $q->whereNotIn('d.slug', $exclude))
                ->orderByDesc('d.average_rating')
                ->orderByDesc('d.review_count')
                ->orderByDesc('d.featured_status')
                ->orderBy('u.name')
                ->limit(6 - $featuredDoctors->count())
                ->get($this->doctorCardColumns())
                ->map(fn ($row) => $this->doctorCardPayload($row));

            $featuredDoctors = $featuredDoctors->concat($fallback)->values();
        }

        return view('pages.landing.home', [
            'stats' => [
                'department_count' => $departmentCount,
                'doctor_count' => $doctorCount,
                'hospital_count' => $hospitalCount,
            ],
            'departments' => $this->departmentCards(8),
            'featuredDoctors' => $featuredDoctors,
            'hospitals' => $this->hospitalCards(6),
        ]);
    }

    public function departments(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $departments = $this->departmentCards(null, $search !== '' ? $search : null);

        return view('pages.landing.departments', [
            'search' => $search,
            'departments' => $departments,
            'totalDepartments' => $departments->count(),
        ]);
    }

    public function departmentShow(Request $request, string $slug)
    {
        $department = DB::table('departments')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->first(['id', 'uuid', 'name', 'short_form', 'slug', 'image', 'description']);

        abort_unless($department, 404);

        $search = trim((string) $request->query('q', ''));
        $doctorQuery = (clone $this->activeDoctorQuery())
            ->where('d.primary_department_id', $department->id);

        if ($search !== '') {
            $like = '%' . $search . '%';
            $doctorQuery->where(function ($q) use ($like) {
                $q->where('u.name', 'like', $like)
                    ->orWhere('des.name', 'like', $like)
                    ->orWhere('sp.name', 'like', $like)
                    ->orWhere('h.name', 'like', $like);
            });
        }

        $doctors = $doctorQuery
            ->orderByDesc('d.featured_status')
            ->orderByDesc('d.average_rating')
            ->orderByDesc('d.review_count')
            ->orderBy('u.name')
            ->get($this->doctorCardColumns())
            ->map(fn ($row) => $this->doctorCardPayload($row))
            ->values();

        $relatedDepartments = $this->departmentCards(6)
            ->reject(fn ($item) => $item['slug'] === (string) $department->slug)
            ->values();

        return view('pages.landing.departmentDoctors', [
            'search' => $search,
            'department' => [
                'name' => (string) ($department->name ?? ''),
                'short_form' => (string) ($department->short_form ?? ''),
                'slug' => (string) ($department->slug ?? ''),
                'image' => $this->assetUrl($department->image ?? null),
                'description' => (string) ($department->description ?? ''),
            ],
            'doctors' => $doctors,
            'relatedDepartments' => $relatedDepartments,
        ]);
    }

    public function doctorShow(string $slug)
    {
        $doctor = (clone $this->activeDoctorQuery())
            ->leftJoin('registration_councils as rc', 'rc.id', '=', 'd.registration_council_id')
            ->where('d.slug', $slug)
            ->first([
                'd.id as doctor_id',
                'd.slug as doctor_slug',
                'd.cover_photo',
                'd.gallery',
                'd.qualification_summary',
                'd.years_of_experience',
                'd.medical_registration_number',
                'd.registration_year',
                'd.short_bio',
                'd.about_doctor',
                'd.consultation_fee',
                'd.followup_fee',
                'd.video_consultation_fee',
                'd.home_visit_fee',
                'd.average_rating',
                'd.review_count',
                'd.online_consultation_available',
                'd.in_person_consultation_available',
                'd.home_visit_available',
                'd.appointment_booking_available',
                'd.total_patients_treated',
                'd.total_surgeries',
                'd.total_consultations',
                'd.seo_title',
                'd.seo_description',
                'u.name as doctor_name',
                'u.email as doctor_email',
                'u.phone_number as doctor_phone',
                'u.alternative_email',
                'u.alternative_phone_number',
                'u.whatsapp_number',
                'u.address as doctor_address',
                'u.image as user_image',
                'des.name as designation_name',
                'dep.id as department_id',
                'dep.name as department_name',
                'dep.slug as department_slug',
                'sp.name as specialization_name',
                'h.name as hospital_name',
                'h.slug as hospital_slug',
                'h.city as hospital_city',
                'h.state as hospital_state',
                'h.image as hospital_image',
                'rc.name as registration_council_name',
            ]);

        abort_unless($doctor, 404);

        $specializations = DB::table('doctor_specializations as ds')
            ->join('specializations as s', 's.id', '=', 'ds.specialization_id')
            ->where('ds.doctor_id', $doctor->doctor_id)
            ->orderByDesc('ds.is_primary')
            ->orderBy('ds.sort_order')
            ->get(['s.name', 's.short_form', 'ds.is_primary'])
            ->map(fn ($row) => [
                'name' => (string) ($row->name ?? ''),
                'short_form' => (string) ($row->short_form ?? ''),
                'is_primary' => (bool) ($row->is_primary ?? false),
            ])->values();

        $languages = DB::table('doctor_languages as dl')
            ->join('languages as l', 'l.id', '=', 'dl.language_id')
            ->where('dl.doctor_id', $doctor->doctor_id)
            ->orderBy('dl.sort_order')
            ->get(['l.name', 'l.code', 'dl.proficiency_level'])
            ->map(fn ($row) => [
                'name' => (string) ($row->name ?? ''),
                'code' => (string) ($row->code ?? ''),
                'proficiency' => (string) ($row->proficiency_level ?? ''),
            ])->values();

        $services = DB::table('doctor_services as ds')
            ->join('services as s', 's.id', '=', 'ds.service_id')
            ->where('ds.doctor_id', $doctor->doctor_id)
            ->orderBy('ds.sort_order')
            ->get(['s.name', 's.short_form', 's.default_price', 's.default_duration_minutes', 'ds.custom_fee', 'ds.custom_duration_minutes', 'ds.notes'])
            ->map(fn ($row) => [
                'name' => (string) ($row->name ?? ''),
                'short_form' => (string) ($row->short_form ?? ''),
                'fee' => $row->custom_fee ?? $row->default_price,
                'duration' => $row->custom_duration_minutes ?? $row->default_duration_minutes,
                'notes' => (string) ($row->notes ?? ''),
            ])->values();

        $qualifications = DB::table('doctor_qualifications as dq')
            ->join('qualifications as q', 'q.id', '=', 'dq.qualification_id')
            ->where('dq.doctor_id', $doctor->doctor_id)
            ->orderBy('dq.sort_order')
            ->orderBy('q.name')
            ->get(['q.name', 'q.short_form', 'dq.institute_name', 'dq.university_name', 'dq.country', 'dq.start_year', 'dq.end_year', 'dq.description'])
            ->map(fn ($row) => [
                'name' => (string) ($row->name ?? ''),
                'short_form' => (string) ($row->short_form ?? ''),
                'institute_name' => (string) ($row->institute_name ?? ''),
                'university_name' => (string) ($row->university_name ?? ''),
                'country' => (string) ($row->country ?? ''),
                'start_year' => $row->start_year,
                'end_year' => $row->end_year,
                'description' => (string) ($row->description ?? ''),
            ])->values();

        $clinics = DB::table('doctor_clinics as dc')
            ->join('clinics as c', 'c.id', '=', 'dc.clinic_id')
            ->where('dc.doctor_id', $doctor->doctor_id)
            ->orderByDesc('dc.is_primary')
            ->orderBy('dc.sort_order')
            ->get([
                'c.name',
                'c.short_name',
                'c.city',
                'c.state',
                'c.area',
                'c.address_line_1',
                'c.map_url',
                'c.image',
                'dc.is_primary',
                'dc.consultation_fee',
                'dc.followup_fee',
                'dc.video_consultation_fee',
                'dc.online_consultation_available',
                'dc.in_person_consultation_available',
                'dc.appointment_booking_available',
                'dc.room_no',
                'dc.visit_note',
            ])
            ->map(fn ($row) => [
                'name' => (string) ($row->name ?? ''),
                'short_name' => (string) ($row->short_name ?? ''),
                'location' => collect([(string) ($row->area ?? ''), (string) ($row->city ?? ''), (string) ($row->state ?? '')])->filter()->implode(', '),
                'address_line_1' => (string) ($row->address_line_1 ?? ''),
                'map_url' => (string) ($row->map_url ?? ''),
                'image' => $this->assetUrl($row->image ?? null),
                'is_primary' => (bool) ($row->is_primary ?? false),
                'consultation_fee' => $row->consultation_fee,
                'followup_fee' => $row->followup_fee,
                'video_consultation_fee' => $row->video_consultation_fee,
                'online_consultation_available' => (bool) ($row->online_consultation_available ?? false),
                'in_person_consultation_available' => (bool) ($row->in_person_consultation_available ?? false),
                'appointment_booking_available' => (bool) ($row->appointment_booking_available ?? false),
                'room_no' => (string) ($row->room_no ?? ''),
                'visit_note' => (string) ($row->visit_note ?? ''),
            ])->values();

        $similarDoctors = (clone $this->activeDoctorQuery())
            ->where('d.id', '!=', $doctor->doctor_id)
            ->when($doctor->department_id, fn ($q) => $q->where('d.primary_department_id', $doctor->department_id))
            ->orderByDesc('d.featured_status')
            ->orderByDesc('d.average_rating')
            ->orderBy('u.name')
            ->limit(4)
            ->get($this->doctorCardColumns())
            ->map(fn ($row) => $this->doctorCardPayload($row))
            ->values();

        $reviews = DB::table('doctor_reviews as dr')
            ->join('patients as p', 'p.id', '=', 'dr.patient_id')
            ->where('dr.doctor_id', $doctor->doctor_id)
            ->whereNull('dr.deleted_at')
            ->where('dr.status', 'active')
            ->orderByDesc('dr.created_at')
            ->limit(8)
            ->get([
                'dr.rating',
                'dr.title',
                'dr.review_text',
                'dr.created_at',
                'p.first_name',
                'p.last_name',
            ])
            ->map(fn ($row) => [
                'rating' => (int) ($row->rating ?? 0),
                'title' => (string) ($row->title ?? ''),
                'review_text' => (string) ($row->review_text ?? ''),
                'created_at' => $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y') : '',
                'patient_name' => collect([
                    (string) ($row->first_name ?? ''),
                    (string) ($row->last_name ?? ''),
                ])->filter()->implode(' '),
            ])->values();

        return view('pages.landing.doctorProfile', [
            'doctor' => [
                'name' => (string) ($doctor->doctor_name ?? ''),
                'slug' => (string) ($doctor->doctor_slug ?? ''),
                'image' => $this->assetUrl($doctor->user_image ?? null),
                'cover_photo' => $this->assetUrl($doctor->cover_photo ?? null),
                'gallery' => collect($this->jsonArray($doctor->gallery))->map(fn ($item) => $this->assetUrl((string) $item) ?: (string) $item)->filter()->values(),
                'designation' => (string) ($doctor->designation_name ?? ''),
                'department' => (string) ($doctor->department_name ?? ''),
                'department_slug' => (string) ($doctor->department_slug ?? ''),
                'specialization' => (string) ($doctor->specialization_name ?? ''),
                'hospital' => (string) ($doctor->hospital_name ?? ''),
                'hospital_slug' => (string) ($doctor->hospital_slug ?? ''),
                'hospital_location' => collect([(string) ($doctor->hospital_city ?? ''), (string) ($doctor->hospital_state ?? '')])->filter()->implode(', '),
                'hospital_image' => $this->assetUrl($doctor->hospital_image ?? null),
                'qualification_summary' => (string) ($doctor->qualification_summary ?? ''),
                'years_of_experience' => (int) ($doctor->years_of_experience ?? 0),
                'medical_registration_number' => (string) ($doctor->medical_registration_number ?? ''),
                'registration_year' => $doctor->registration_year,
                'registration_council' => (string) ($doctor->registration_council_name ?? ''),
                'short_bio' => (string) ($doctor->short_bio ?? ''),
                'about_doctor' => (string) ($doctor->about_doctor ?? ''),
                'consultation_fee' => $doctor->consultation_fee,
                'followup_fee' => $doctor->followup_fee,
                'video_consultation_fee' => $doctor->video_consultation_fee,
                'home_visit_fee' => $doctor->home_visit_fee,
                'average_rating' => number_format((float) ($doctor->average_rating ?? 0), 1),
                'review_count' => (int) ($doctor->review_count ?? 0),
                'online_consultation_available' => (bool) ($doctor->online_consultation_available ?? false),
                'in_person_consultation_available' => (bool) ($doctor->in_person_consultation_available ?? false),
                'home_visit_available' => (bool) ($doctor->home_visit_available ?? false),
                'appointment_booking_available' => (bool) ($doctor->appointment_booking_available ?? false),
                'total_patients_treated' => (int) ($doctor->total_patients_treated ?? 0),
                'total_surgeries' => (int) ($doctor->total_surgeries ?? 0),
                'total_consultations' => (int) ($doctor->total_consultations ?? 0),
                'email' => (string) ($doctor->doctor_email ?? ''),
                'phone' => (string) ($doctor->doctor_phone ?? ''),
                'alternative_email' => (string) ($doctor->alternative_email ?? ''),
                'alternative_phone' => (string) ($doctor->alternative_phone_number ?? ''),
                'whatsapp' => (string) ($doctor->whatsapp_number ?? ''),
                'address' => (string) ($doctor->doctor_address ?? ''),
                'seo_title' => (string) ($doctor->seo_title ?? ''),
                'seo_description' => (string) ($doctor->seo_description ?? ''),
            ],
            'specializations' => $specializations,
            'languages' => $languages,
            'services' => $services,
            'qualifications' => $qualifications,
            'clinics' => $clinics,
            'reviews' => $reviews,
            'similarDoctors' => $similarDoctors,
        ]);
    }
}
