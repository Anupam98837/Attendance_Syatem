@extends('pages.landing.layout')

@section('title', $doctor['name'] . ' | LegMed Directory')
@section('meta_description', $doctor['seo_description'] ?: ($doctor['short_bio'] ?: ('View the public profile for ' . $doctor['name'] . '.')))

@section('content')
@php
  $callClinicName = config('app.name', 'LegMed');
  $callClinicLocation = $doctor['hospital_location'] ?: 'Asansol';
  $callPhoneNumber = '12345678';
  $callExtension = '510';
  $callSupportEmail = config('mail.from.address', 'support@legmed.test');
  $callLogo = asset('/assets/media/images/web/logo.png');
@endphp
<div class="landing-shell">
  <div class="landing-page-bar">
    <div class="landing-breadcrumbs">
      <a href="{{ route('directory.home') }}">Home</a>
      <span>&gt;</span>
      <a href="{{ route('directory.departments.index') }}">Departments</a>
      @if($doctor['department'] && $doctor['department_slug'])
        <span>&gt;</span>
        <a href="{{ route('directory.departments.show', ['slug' => $doctor['department_slug']]) }}">{{ $doctor['department'] }}</a>
      @endif
      <span>&gt;</span>
      <span>{{ $doctor['name'] }}</span>
    </div>
    <div class="landing-page-meta-minimal">
      {{ $doctor['hospital'] ?: 'Doctor profile' }}
    </div>
  </div>

  <section class="landing-section">
    <div class="landing-profile-layout">
      <aside class="landing-profile-rail landing-sticky">
        <article class="landing-profile-card landing-profile-identity">
          <div class="landing-profile-head">
            @if($doctor['image'])
              <img src="{{ $doctor['image'] }}" alt="{{ $doctor['name'] }}" class="landing-avatar">
            @else
              <div class="landing-avatar-fallback"><i class="fa-solid fa-user-doctor"></i></div>
            @endif

            <div>
              <h1 class="landing-profile-name">{{ $doctor['name'] }}</h1>
              <div class="landing-profile-subtitle">
                {{ $doctor['designation'] ?: 'Doctor' }}
                @if($doctor['department']) • {{ $doctor['department'] }} @endif
              </div>
              <div class="landing-profile-subtitle-accent">
                {{ $doctor['years_of_experience'] ? ($doctor['years_of_experience'] . ' years of experience') : 'Experience to be updated' }}
              </div>
            </div>
          </div>

          <div class="landing-profile-divider"></div>
          <div class="landing-profile-meta">
            <div class="landing-profile-meta-row">
              <div class="landing-profile-meta-line">
                <strong>Reviews</strong>
                <span>{{ $doctor['average_rating'] }} • {{ $doctor['review_count'] }}</span>
              </div>
              <div class="landing-profile-meta-line">
                <strong>Patients Treated</strong>
                <span>{{ $doctor['total_patients_treated'] ? number_format($doctor['total_patients_treated']) : '—' }}</span>
              </div>
            </div>
          </div>

          <div class="landing-profile-mini-grid">
            <div class="landing-profile-mini-item">
              <i class="fa-solid fa-graduation-cap"></i>
              <div>
                <strong>{{ $doctor['qualification_summary'] ?: '—' }}</strong>
                <span>Qualification summary</span>
              </div>
            </div>
          </div>

          <div class="landing-profile-actions">
            <button type="button" class="landing-profile-book-trigger js-open-book-modal" aria-label="Book now" title="Book now">
              <i class="fa-solid fa-calendar-plus"></i>
              <span>Book Now</span>
            </button>
            <button type="button" class="landing-profile-call-trigger js-open-call-modal" aria-label="Call now" title="Call now">
              <i class="fa-solid fa-phone-volume"></i>
              <span>Call Now</span>
            </button>
          </div>
        </article>

        <article class="landing-doctor-summary-card">
          <h3>Practice Snapshot</h3>
          <div class="landing-info-grid">
            @if($doctor['hospital'])
              <div class="landing-info-row"><i class="fa-solid fa-hospital"></i><span>{{ $doctor['hospital'] }}{{ $doctor['hospital_location'] ? ' • ' . $doctor['hospital_location'] : '' }}</span></div>
            @endif
            @if($doctor['department'])
              <div class="landing-info-row"><i class="fa-solid fa-stethoscope"></i><span>{{ $doctor['department'] }}</span></div>
            @endif
            @if($doctor['specialization'])
              <div class="landing-info-row"><i class="fa-solid fa-shield-heart"></i><span>{{ $doctor['specialization'] }}</span></div>
            @endif
            @if($doctor['phone'])
              <div class="landing-info-row"><i class="fa-solid fa-phone"></i><span>{{ $doctor['phone'] }}</span></div>
            @endif
            @if($doctor['email'])
              <div class="landing-info-row"><i class="fa-solid fa-envelope"></i><span>{{ $doctor['email'] }}</span></div>
            @endif
          </div>
        </article>

        <article class="landing-doctor-summary-card">
          <h3>Availability</h3>
          <div class="landing-pills">
            @if($doctor['online_consultation_available']) <span class="landing-pill"><i class="fa-solid fa-video"></i>Online Consultation</span> @endif
            @if($doctor['in_person_consultation_available']) <span class="landing-pill"><i class="fa-solid fa-user-check"></i>Clinic Visit</span> @endif
            @if($doctor['home_visit_available']) <span class="landing-pill"><i class="fa-solid fa-house-medical"></i>Home Visit</span> @endif
            @if($doctor['appointment_booking_available']) <span class="landing-pill"><i class="fa-solid fa-calendar-check"></i>Booking Open</span> @endif
          </div>
        </article>
      </aside>

      <div class="landing-profile-content">
        <section class="landing-tab-shell">
          <div class="landing-tab-nav" role="tablist" aria-label="Doctor profile tabs">
            <button type="button" class="landing-tab-btn is-active" data-tab-target="tab-overview">Overview</button>
            <button type="button" class="landing-tab-btn" data-tab-target="tab-specializations">Specializations</button>
            <button type="button" class="landing-tab-btn" data-tab-target="tab-experience">Experience & Credentials</button>
            <button type="button" class="landing-tab-btn" data-tab-target="tab-clinics">Clinics</button>
            <button type="button" class="landing-tab-btn" data-tab-target="tab-reviews">Reviews</button>
            @if($languages->isNotEmpty())
              <button type="button" class="landing-tab-btn" data-tab-target="tab-languages">Languages</button>
            @endif
          </div>

          <div id="tab-overview" class="landing-tab-panel is-active">
            <article class="landing-slab">
              <div class="landing-section-head mb-0">
                <div>
                  <span class="landing-badge">Overview</span>
                  <h2 class="mt-3 mb-2">About this doctor</h2>
                </div>
              </div>
              <p class="landing-copy mb-0">{{ $doctor['about_doctor'] ?: ($doctor['short_bio'] ?: 'Detailed biography will appear here once it is added in the admin profile workspace.') }}</p>
            </article>
          </div>

          <div id="tab-specializations" class="landing-tab-panel">
            <article class="landing-slab">
              <div class="landing-section-head mb-0">
                <div>
                  <span class="landing-badge">Specializations</span>
                  <h2 class="mt-3 mb-2">Areas of expertise and services</h2>
                </div>
              </div>
              <div class="landing-pills mb-3">
                @forelse($specializations as $specialization)
                  <span class="landing-pill"><i class="fa-solid fa-stethoscope"></i>{{ $specialization['name'] }}@if($specialization['is_primary']) • Primary @endif</span>
                @empty
                  <span class="landing-copy">No specialization records added yet.</span>
                @endforelse
              </div>

              @if($services->isNotEmpty())
                <div class="landing-list">
                  @foreach($services as $service)
                    <div class="landing-list-card">
                      <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div>
                          <h4 class="mb-1">{{ $service['name'] }}</h4>
                          @if($service['notes']) <div class="landing-copy">{{ $service['notes'] }}</div> @endif
                        </div>
                        <div class="text-md-end">
                          @if($service['fee'] !== null)<div class="fw-bold">From ₹{{ number_format((float) $service['fee'], 0) }}</div>@endif
                          @if($service['duration'])<div class="landing-muted">{{ $service['duration'] }} min</div>@endif
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </article>
          </div>

          <div id="tab-experience" class="landing-tab-panel">
            <article class="landing-slab">
              <div class="landing-section-head mb-0">
                <div>
                  <span class="landing-badge">Experience</span>
                  <h2 class="mt-3 mb-2">Education, registration, and professional background</h2>
                </div>
              </div>
              <div class="landing-metric-row mb-3">
                @if($doctor['qualification_summary']) <span class="landing-metric"><i class="fa-solid fa-graduation-cap"></i>{{ $doctor['qualification_summary'] }}</span> @endif
                @if($doctor['medical_registration_number']) <span class="landing-metric"><i class="fa-solid fa-id-card"></i>{{ $doctor['medical_registration_number'] }}</span> @endif
                @if($doctor['registration_council']) <span class="landing-metric"><i class="fa-solid fa-shield-halved"></i>{{ $doctor['registration_council'] }}</span> @endif
                @if($doctor['years_of_experience']) <span class="landing-metric"><i class="fa-solid fa-briefcase-medical"></i>{{ $doctor['years_of_experience'] }} years experience</span> @endif
              </div>

              <div class="landing-timeline">
                @forelse($qualifications as $qualification)
                  <div class="landing-timeline-item">
                    <h4 class="mb-1">{{ $qualification['name'] }}</h4>
                    <div class="landing-muted">
                      {{ $qualification['institute_name'] ?: $qualification['university_name'] ?: 'Qualification details' }}
                      @if($qualification['start_year'] || $qualification['end_year'])
                        • {{ $qualification['start_year'] ?: '—' }} - {{ $qualification['end_year'] ?: '—' }}
                      @endif
                    </div>
                    @if($qualification['description']) <p class="landing-copy mb-0 mt-2">{{ $qualification['description'] }}</p> @endif
                  </div>
                @empty
                  <p class="landing-copy mb-0">Qualification rows will appear here once they are added in admin.</p>
                @endforelse
              </div>
            </article>
          </div>

          <div id="tab-clinics" class="landing-tab-panel">
            <div class="landing-clinic-grid">
              @if($doctor['hospital'])
                <article class="landing-slab">
                  <div class="d-flex gap-3 align-items-start">
                    @if($doctor['hospital_image'])
                      <img src="{{ $doctor['hospital_image'] }}" alt="{{ $doctor['hospital'] }}" class="landing-avatar">
                    @else
                      <div class="landing-avatar-fallback"><i class="fa-solid fa-hospital"></i></div>
                    @endif
                    <div>
                      <div class="landing-badge mb-2"><i class="fa-solid fa-hospital"></i>Primary Hospital</div>
                      <h4 class="mb-1">{{ $doctor['hospital'] }}</h4>
                      @if($doctor['hospital_location']) <div class="landing-copy mb-0">{{ $doctor['hospital_location'] }}</div> @endif
                    </div>
                  </div>
                </article>
              @endif

              @forelse($clinics as $clinic)
                <article class="landing-slab">
                  <div class="d-flex justify-content-between gap-3 flex-wrap">
                    <div>
                      @if($clinic['is_primary']) <div class="landing-badge mb-2"><i class="fa-solid fa-location-dot"></i>Primary Clinic</div> @endif
                      <h4 class="mb-1">{{ $clinic['name'] }}</h4>
                      @if($clinic['location']) <div class="landing-muted">{{ $clinic['location'] }}</div> @endif
                      @if($clinic['address_line_1']) <p class="landing-copy mt-2 mb-0">{{ $clinic['address_line_1'] }}</p> @endif
                    </div>
                    <div class="text-md-end">
                      @if($clinic['consultation_fee'] !== null)<div class="fw-bold">Clinic fee ₹{{ number_format((float) $clinic['consultation_fee'], 0) }}</div>@endif
                      @if($clinic['room_no'])<div class="landing-muted">Room {{ $clinic['room_no'] }}</div>@endif
                    </div>
                  </div>
                </article>
              @empty
                <article class="landing-slab">
                  <p class="landing-copy mb-0">No clinic records added yet.</p>
                </article>
              @endforelse
            </div>
          </div>

          <div id="tab-reviews" class="landing-tab-panel">
            <article class="landing-slab">
              <div class="landing-section-head mb-0">
                <div>
                  <span class="landing-badge">Reviews</span>
                  <h2 class="mt-3 mb-2">Patient feedback and experience</h2>
                </div>
              </div>

              <div class="landing-metric-row mb-3">
                <span class="landing-metric"><i class="fa-solid fa-star"></i>{{ $doctor['average_rating'] }} average rating</span>
                <span class="landing-metric"><i class="fa-solid fa-message"></i>{{ $doctor['review_count'] }} reviews</span>
              </div>

              <div class="landing-list">
                @forelse($reviews as $review)
                  <div class="landing-list-card">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                      <div>
                        <h4 class="mb-1">{{ $review['patient_name'] ?: 'Patient' }}</h4>
                        <div class="landing-muted">{{ $review['created_at'] ?: 'Recently added' }}</div>
                      </div>
                      <div class="landing-metric">
                        <i class="fa-solid fa-star"></i>{{ $review['rating'] }}/5
                      </div>
                    </div>
                    @if($review['title'])
                      <div class="fw-bold mt-3">{{ $review['title'] }}</div>
                    @endif
                    <p class="landing-copy mb-0 mt-2">{{ $review['review_text'] }}</p>
                  </div>
                @empty
                  <div class="landing-list-card">
                    <p class="landing-copy mb-0">Reviews will appear here once patients mark a booking as done and share feedback.</p>
                  </div>
                @endforelse
              </div>
            </article>
          </div>

          @if($languages->isNotEmpty())
            <div id="tab-languages" class="landing-tab-panel">
              <article class="landing-slab">
                <span class="landing-badge">Languages</span>
                <h3 class="mt-3">Communication profile</h3>
                <div class="landing-pills mt-3">
                  @foreach($languages as $language)
                    <span class="landing-pill">{{ $language['name'] }}@if($language['proficiency']) • {{ \Illuminate\Support\Str::headline($language['proficiency']) }} @endif</span>
                  @endforeach
                </div>
              </article>
            </div>
          @endif
        </section>
      </div>
    </div>
  </section>

  <div class="landing-call-modal" id="doctorCallModal" aria-hidden="true">
    <div class="landing-call-card" role="dialog" aria-modal="true" aria-labelledby="doctorCallModalTitle">
      <div class="landing-call-card-head">
        <div class="landing-call-brand">
          <img src="{{ $callLogo }}" alt="{{ $callClinicName }}">
          <div>
            <strong id="doctorCallModalTitle">{{ $callClinicName }}</strong>
            <span>{{ $callClinicLocation }}</span>
          </div>
        </div>
        <button type="button" class="landing-call-close js-close-call-modal" aria-label="Close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="landing-call-card-body">
        <div class="landing-call-detail">
          <strong>Phone Number</strong>
          <div class="landing-call-number-row">
            <div class="landing-call-number" id="doctorCallNumber">{{ $callPhoneNumber }}</div>
            <button type="button" class="landing-call-copy" id="doctorCallCopyBtn">Copy</button>
          </div>
        </div>

        <div class="landing-call-detail">
          <strong>Dial The Extension Given Below After The Call Connects</strong>
          <span>Ext. {{ $callExtension }}</span>
        </div>

        <div class="landing-call-actions">
          <a href="tel:{{ $callPhoneNumber }}" class="landing-btn landing-btn-primary">
            <i class="fa-solid fa-phone"></i>
            <span>Call Now</span>
          </a>
        </div>

        <p class="landing-call-note">
          By calling this number, you agree to the Terms &amp; Conditions. If you could not connect with the center, please write to
          <a href="mailto:{{ $callSupportEmail }}">{{ $callSupportEmail }}</a>.
        </p>
      </div>
    </div>
  </div>

  <div class="landing-call-modal" id="doctorBookingModal" aria-hidden="true">
    <div class="landing-call-card is-booking" role="dialog" aria-modal="true" aria-labelledby="doctorBookingModalTitle">
      <div class="landing-call-card-head">
        <div class="landing-call-brand">
          <img src="{{ $callLogo }}" alt="{{ $doctor['name'] }}">
          <div>
            <strong id="doctorBookingModalTitle">Book {{ $doctor['name'] }}</strong>
            <span>Share patient details and request an appointment</span>
          </div>
        </div>
        <button type="button" class="landing-call-close js-close-book-modal" aria-label="Close booking modal">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="landing-call-card-body">
        <div class="landing-booking-loading is-visible" id="doctorBookingLoading">
          <i class="fa-solid fa-spinner fa-spin"></i>
          <span>Preparing your booking form...</span>
        </div>

        <form id="doctorBookingForm" class="landing-booking-panel" hidden novalidate>
          <div class="landing-book-alert" id="doctorBookingAlert"></div>

          <div class="landing-booking-header">
            <h3>Who is this appointment for?</h3>
            <p>Select whether you are booking for yourself or for someone in your family.</p>
          </div>

          <div class="landing-booking-choice-grid" role="radiogroup" aria-label="Booking for">
            <button type="button" class="landing-booking-choice is-active" data-booking-choice="self">
              <i class="fa-solid fa-user"></i>
              <strong>Me</strong>
              <span>We will use your account details and let you update anything needed.</span>
            </button>
            <button type="button" class="landing-booking-choice" data-booking-choice="family">
              <i class="fa-solid fa-users"></i>
              <strong>My Family</strong>
              <span>Create a patient profile for a parent, child, spouse, or another family member.</span>
            </button>
          </div>

          <input type="hidden" name="booking_for" id="doctorBookingFor" value="self">

          <div class="landing-booking-summary" id="doctorBookingSummary">
            <strong>Booking for you.</strong> Your saved account information will be used as the starting point for this form.
          </div>

          <div class="landing-booking-grid">
            <div class="landing-booking-field is-span-2">
              <label for="doctorBookingClinic">Clinic</label>
              <select name="clinic_id" id="doctorBookingClinic">
                <option value="">Select clinic</option>
              </select>
              <small id="doctorBookingClinicHint">Choose where you want to meet the doctor.</small>
              <div class="landing-booking-error" data-error-for="clinic_id"></div>
            </div>

            <div class="landing-booking-empty is-span-2" id="doctorBookingNoClinic" hidden>
              No clinic slot is mapped yet for this doctor. Your request will still be saved and the clinic can be confirmed later.
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingDate">Appointment Date</label>
              <input type="date" name="appointment_date" id="doctorBookingDate" min="{{ now()->toDateString() }}" required>
              <div class="landing-booking-error" data-error-for="appointment_date"></div>
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingTime">Preferred Time</label>
              <input type="time" name="appointment_time" id="doctorBookingTime">
              <small>Optional</small>
              <div class="landing-booking-error" data-error-for="appointment_time"></div>
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingFirstName">First Name</label>
              <input type="text" name="patient_first_name" id="doctorBookingFirstName" maxlength="100" required>
              <div class="landing-booking-error" data-error-for="patient_first_name"></div>
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingMiddleName">Middle Name</label>
              <input type="text" name="patient_middle_name" id="doctorBookingMiddleName" maxlength="100">
              <div class="landing-booking-error" data-error-for="patient_middle_name"></div>
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingLastName">Last Name</label>
              <input type="text" name="patient_last_name" id="doctorBookingLastName" maxlength="100">
              <div class="landing-booking-error" data-error-for="patient_last_name"></div>
            </div>

            <div class="landing-booking-field" id="doctorBookingRelationshipField" hidden>
              <label for="doctorBookingRelationship">Relationship</label>
              <input type="text" name="relationship_with_patient" id="doctorBookingRelationship" maxlength="100" placeholder="Example: Father, Mother, Child">
              <div class="landing-booking-error" data-error-for="relationship_with_patient"></div>
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingPhone">Phone Number</label>
              <input type="tel" name="patient_phone_number" id="doctorBookingPhone" maxlength="32" required>
              <div class="landing-booking-error" data-error-for="patient_phone_number"></div>
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingAltPhone">Alternative Phone</label>
              <input type="tel" name="patient_alternative_phone_number" id="doctorBookingAltPhone" maxlength="32">
              <div class="landing-booking-error" data-error-for="patient_alternative_phone_number"></div>
            </div>

            <div class="landing-booking-field">
              <label for="doctorBookingEmail">Email</label>
              <input type="email" name="patient_email" id="doctorBookingEmail" maxlength="255">
              <div class="landing-booking-error" data-error-for="patient_email"></div>
            </div>

            <div class="landing-booking-field is-span-2">
              <label for="doctorBookingAddress">Address</label>
              <textarea name="patient_address" id="doctorBookingAddress"></textarea>
              <div class="landing-booking-error" data-error-for="patient_address"></div>
            </div>

            <div class="landing-booking-field is-span-2">
              <label for="doctorBookingSymptoms">Symptoms or notes</label>
              <textarea name="symptoms" id="doctorBookingSymptoms" placeholder="Optional notes to help the clinic understand the request"></textarea>
              <div class="landing-booking-error" data-error-for="symptoms"></div>
            </div>
          </div>

          <div class="landing-booking-footer">
            <div class="landing-booking-footer-note">
              This request creates the patient and appointment records together.
            </div>
            <div class="landing-booking-actions">
              <button type="button" class="landing-btn landing-btn-light js-close-book-modal">
                <span>Cancel</span>
              </button>
              <button type="submit" class="landing-btn landing-btn-primary" id="doctorBookingSubmitBtn">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Confirm Booking</span>
              </button>
            </div>
          </div>
        </form>

        <div class="landing-book-success" id="doctorBookingSuccess">
          <div class="landing-book-success-icon">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div class="landing-book-alert is-success is-visible" id="doctorBookingSuccessAlert">Booking request saved successfully.</div>
          <div>
            <h3>Appointment request created</h3>
            <p id="doctorBookingSuccessText">Your booking details have been saved. Our team can now continue from the appointment record.</p>
          </div>
          <button type="button" class="landing-btn landing-btn-light js-close-book-modal">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to doctor profile</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  @if($similarDoctors->isNotEmpty())
    <section class="landing-section">
      <div class="landing-section-head">
        <div>
          <span class="landing-badge">More Doctors</span>
          <h2 class="mt-3 mb-0">Similar profiles from the same care flow</h2>
        </div>
      </div>
      <div class="landing-grid doctors">
        @foreach($similarDoctors as $item)
          <a href="{{ $item['href'] }}" class="landing-card-link">
            <article class="landing-card">
              <div class="d-flex gap-3 align-items-start">
                @if($item['image'])
                  <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="landing-avatar">
                @else
                  <div class="landing-avatar-fallback"><i class="fa-solid fa-user-doctor"></i></div>
                @endif
                <div class="flex-grow-1">
                  <h3 class="mb-1">{{ $item['name'] }}</h3>
                  <div class="landing-muted">{{ $item['designation'] ?: 'Doctor' }}</div>
                </div>
              </div>
              <div class="landing-metric-row">
                <span class="landing-metric"><i class="fa-solid fa-star"></i>{{ $item['rating'] }}</span>
                @if($item['consultation_fee']) <span class="landing-metric"><i class="fa-solid fa-indian-rupee-sign"></i>{{ $item['consultation_fee'] }}</span> @endif
              </div>
            </article>
          </a>
        @endforeach
      </div>
    </section>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabButtons = Array.from(document.querySelectorAll('.landing-tab-btn'));
  const tabPanels = Array.from(document.querySelectorAll('.landing-tab-panel'));
  const callModal = document.getElementById('doctorCallModal');
  const bookingModal = document.getElementById('doctorBookingModal');
  const openCallBtn = document.querySelector('.js-open-call-modal');
  const closeCallBtn = document.querySelector('.js-close-call-modal');
  const openBookBtns = Array.from(document.querySelectorAll('.js-open-book-modal'));
  const closeBookBtns = Array.from(document.querySelectorAll('.js-close-book-modal'));
  const copyBtn = document.getElementById('doctorCallCopyBtn');
  const callNumberEl = document.getElementById('doctorCallNumber');
  const authCheckUrl = '{{ url('/api/auth/check') }}';
  const bookingBootstrapUrl = @json(url('/api/bookings/doctors/' . $doctor['slug'] . '/bootstrap'));
  const bookingStoreUrl = @json(url('/api/bookings/doctors/' . $doctor['slug']));
  const currentUrl = new URL(window.location.href);
  const bookReturnUrl = currentUrl.pathname + '?book=1';
  const bookingForm = document.getElementById('doctorBookingForm');
  const bookingAlert = document.getElementById('doctorBookingAlert');
  const bookingLoading = document.getElementById('doctorBookingLoading');
  const bookingSuccess = document.getElementById('doctorBookingSuccess');
  const bookingSuccessText = document.getElementById('doctorBookingSuccessText');
  const bookingSuccessAlert = document.getElementById('doctorBookingSuccessAlert');
  const bookingSubmitBtn = document.getElementById('doctorBookingSubmitBtn');
  const bookingForInput = document.getElementById('doctorBookingFor');
  const bookingClinic = document.getElementById('doctorBookingClinic');
  const bookingClinicHint = document.getElementById('doctorBookingClinicHint');
  const bookingNoClinic = document.getElementById('doctorBookingNoClinic');
  const bookingSummary = document.getElementById('doctorBookingSummary');
  const bookingRelationshipField = document.getElementById('doctorBookingRelationshipField');
  const bookingChoiceButtons = Array.from(document.querySelectorAll('[data-booking-choice]'));
  const bookingErrorEls = Array.from(document.querySelectorAll('[data-error-for]'));
  const bookingPatientFields = {
    first_name: document.getElementById('doctorBookingFirstName'),
    middle_name: document.getElementById('doctorBookingMiddleName'),
    last_name: document.getElementById('doctorBookingLastName'),
    phone_number: document.getElementById('doctorBookingPhone'),
    alternative_phone_number: document.getElementById('doctorBookingAltPhone'),
    email: document.getElementById('doctorBookingEmail'),
    address: document.getElementById('doctorBookingAddress'),
  };

  const bookingState = {
    bootstrap: null,
    drafts: {
      self: null,
      family: {
        first_name: '',
        middle_name: '',
        last_name: '',
        phone_number: '',
        alternative_phone_number: '',
        email: '',
        address: '',
        relationship_with_patient: '',
      },
    },
    activeChoice: 'self',
    loaded: false,
  };

  function activateTab(targetId) {
    tabButtons.forEach((btn) => {
      btn.classList.toggle('is-active', btn.dataset.tabTarget === targetId);
    });

    tabPanels.forEach((panel) => {
      panel.classList.toggle('is-active', panel.id === targetId);
    });
  }

  tabButtons.forEach((btn) => {
    btn.addEventListener('click', function () {
      activateTab(btn.dataset.tabTarget);
    });
  });

  function syncBodyLock() {
    const hasOpenModal = [callModal, bookingModal].some((modal) => modal?.classList.contains('is-open'));
    document.body.style.overflow = hasOpenModal ? 'hidden' : '';
  }

  function openCallModal() {
    if (!callModal) return;
    callModal.classList.add('is-open');
    callModal.setAttribute('aria-hidden', 'false');
    syncBodyLock();
  }

  function closeCallModal() {
    if (!callModal) return;
    callModal.classList.remove('is-open');
    callModal.setAttribute('aria-hidden', 'true');
    syncBodyLock();
  }

  function openBookingModal() {
    if (!bookingModal) return;
    bookingModal.classList.add('is-open');
    bookingModal.setAttribute('aria-hidden', 'false');
    syncBodyLock();
  }

  function closeBookingModal() {
    if (!bookingModal) return;
    bookingModal.classList.remove('is-open');
    bookingModal.setAttribute('aria-hidden', 'true');
    syncBodyLock();
  }

  function getAuthToken() {
    return sessionStorage.getItem('token') || localStorage.getItem('token') || '';
  }

  function clearAuth() {
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('role');
    localStorage.removeItem('token');
    localStorage.removeItem('role');
  }

  function redirectToRegister() {
    window.location.assign('/register?redirect=' + encodeURIComponent(bookReturnUrl));
  }

  async function verifyActiveSession() {
    const token = getAuthToken();
    if (!token) return false;

    try {
      const response = await fetch(authCheckUrl, {
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token,
        },
      });

      if (!response.ok) {
        clearAuth();
        return false;
      }

      return true;
    } catch (error) {
      return false;
    }
  }

  function safeJson(response) {
    return response.json().catch(() => ({}));
  }

  function setBookingLoading(visible, message) {
    if (!bookingLoading) return;
    bookingLoading.classList.toggle('is-visible', visible);
    const textEl = bookingLoading.querySelector('span');
    if (textEl && message) {
      textEl.textContent = message;
    }
  }

  function setBookingAlert(type, message) {
    if (!bookingAlert) return;
    if (!message) {
      bookingAlert.className = 'landing-book-alert';
      bookingAlert.textContent = '';
      return;
    }

    bookingAlert.className = 'landing-book-alert is-visible ' + (type === 'success' ? 'is-success' : 'is-error');
    bookingAlert.textContent = message;
  }

  function resetBookingErrors() {
    bookingErrorEls.forEach((el) => {
      el.textContent = '';
      const field = el.closest('.landing-booking-field');
      field?.classList.remove('has-error');
    });
  }

  function applyBookingErrors(errors) {
    Object.entries(errors || {}).forEach(([fieldName, messages]) => {
      const errorEl = document.querySelector('[data-error-for="' + fieldName + '"]');
      if (!errorEl) return;
      const text = Array.isArray(messages) ? messages[0] : messages;
      errorEl.textContent = text || '';
      const field = errorEl.closest('.landing-booking-field');
      field?.classList.add('has-error');
    });
  }

  function captureCurrentDraft() {
    const choice = bookingState.activeChoice;
    if (!choice) return;

    bookingState.drafts[choice] = {
      first_name: bookingPatientFields.first_name?.value?.trim() || '',
      middle_name: bookingPatientFields.middle_name?.value?.trim() || '',
      last_name: bookingPatientFields.last_name?.value?.trim() || '',
      phone_number: bookingPatientFields.phone_number?.value?.trim() || '',
      alternative_phone_number: bookingPatientFields.alternative_phone_number?.value?.trim() || '',
      email: bookingPatientFields.email?.value?.trim() || '',
      address: bookingPatientFields.address?.value?.trim() || '',
      relationship_with_patient: document.getElementById('doctorBookingRelationship')?.value?.trim() || '',
    };
  }

  function fillPatientFields(values) {
    bookingPatientFields.first_name.value = values.first_name || '';
    bookingPatientFields.middle_name.value = values.middle_name || '';
    bookingPatientFields.last_name.value = values.last_name || '';
    bookingPatientFields.phone_number.value = values.phone_number || '';
    bookingPatientFields.alternative_phone_number.value = values.alternative_phone_number || '';
    bookingPatientFields.email.value = values.email || '';
    bookingPatientFields.address.value = values.address || '';
    const relationshipInput = document.getElementById('doctorBookingRelationship');
    if (relationshipInput) {
      relationshipInput.value = values.relationship_with_patient || '';
    }
  }

  function updateBookingSummary(choice) {
    if (!bookingSummary) return;

    if (choice === 'family') {
      bookingSummary.innerHTML = '<strong>Booking for a family member.</strong> A patient profile will be created or updated under your account so you can track who made the booking.';
      return;
    }

    bookingSummary.innerHTML = '<strong>Booking for you.</strong> Your saved account information will be used as the starting point for this form.';
  }

  function applyBookingChoice(choice) {
    captureCurrentDraft();

    bookingState.activeChoice = choice;
    bookingForInput.value = choice;
    bookingChoiceButtons.forEach((btn) => {
      btn.classList.toggle('is-active', btn.dataset.bookingChoice === choice);
    });

    bookingRelationshipField.hidden = choice !== 'family';
    updateBookingSummary(choice);

    const nextDraft = bookingState.drafts[choice] || {};
    fillPatientFields(nextDraft);
  }

  function renderClinicOptions(clinics) {
    if (!bookingClinic) return;

    bookingClinic.innerHTML = '<option value="">Select clinic</option>';

    if (!Array.isArray(clinics) || clinics.length === 0) {
      bookingClinic.closest('.landing-booking-field')?.setAttribute('hidden', 'hidden');
      bookingNoClinic.hidden = false;
      if (bookingClinicHint) {
        bookingClinicHint.textContent = 'Clinic will be assigned later.';
      }
      return;
    }

    bookingClinic.closest('.landing-booking-field')?.removeAttribute('hidden');
    bookingNoClinic.hidden = true;

    clinics.forEach((clinic, index) => {
      const option = document.createElement('option');
      option.value = clinic.clinic_id;
      const location = clinic.location ? ' - ' + clinic.location : '';
      const primary = clinic.is_primary ? ' (Primary)' : '';
      option.textContent = clinic.name + location + primary;
      if (clinic.is_primary || index === 0) {
        option.selected = true;
      }
      bookingClinic.appendChild(option);
    });
  }

  function resetBookingUiForLoad() {
    bookingForm?.reset();
    resetBookingErrors();
    setBookingAlert('', '');
    bookingSuccess.classList.remove('is-visible');
    bookingForm.hidden = true;
    bookingForInput.value = 'self';
    setBookingLoading(true, 'Preparing your booking form...');
  }

  async function loadBookingBootstrap(forceReload) {
    if (bookingState.loaded && bookingState.bootstrap && !forceReload) {
      return bookingState.bootstrap;
    }

    const token = getAuthToken();
    const response = await fetch(bookingBootstrapUrl, {
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + token,
      },
    });

    const data = await safeJson(response);

    if (response.status === 401) {
      clearAuth();
      redirectToRegister();
      return null;
    }

    if (!response.ok || data.status !== 'success') {
      throw new Error(data.message || 'Unable to load booking form right now.');
    }

    bookingState.bootstrap = data;
    bookingState.drafts.self = {
      ...(data.self_patient || {}),
      relationship_with_patient: '',
    };
    bookingState.drafts.family = {
      first_name: '',
      middle_name: '',
      last_name: '',
      phone_number: '',
      alternative_phone_number: '',
      email: '',
      address: '',
      relationship_with_patient: '',
    };
    bookingState.loaded = true;

    renderClinicOptions(data.clinics || []);
    fillPatientFields(bookingState.drafts.self);
    applyBookingChoice('self');

    return data;
  }

  async function beginBookingFlow(forceReload) {
    const isActive = await verifyActiveSession();
    if (!isActive) {
      redirectToRegister();
      return;
    }

    openBookingModal();
    resetBookingUiForLoad();

    try {
      await loadBookingBootstrap(forceReload);
      bookingForm.hidden = false;
      setBookingLoading(false);
    } catch (error) {
      setBookingLoading(false);
      setBookingAlert('error', error.message || 'Unable to load booking form right now.');
    }
  }

  async function submitBookingForm(event) {
    event.preventDefault();
    resetBookingErrors();
    setBookingAlert('', '');

    const token = getAuthToken();
    if (!token) {
      redirectToRegister();
      return;
    }

    captureCurrentDraft();

    const formData = new FormData(bookingForm);
    const payload = Object.fromEntries(formData.entries());

    bookingSubmitBtn.disabled = true;
    bookingSubmitBtn.querySelector('span').textContent = 'Saving...';

    try {
      const response = await fetch(bookingStoreUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer ' + token,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      const data = await safeJson(response);

      if (response.status === 401) {
        clearAuth();
        redirectToRegister();
        return;
      }

      if (response.status === 422) {
        applyBookingErrors(data.errors || {});
        setBookingAlert('error', data.message || 'Please check the highlighted fields and try again.');
        return;
      }

      if (!response.ok || data.status !== 'success') {
        setBookingAlert('error', data.message || 'Unable to create the booking right now.');
        return;
      }

      bookingForm.hidden = true;
      bookingSuccess.classList.add('is-visible');
      const appointment = data.appointment || {};
      const bookedFor = appointment.booking_for === 'family' ? 'family member' : 'yourself';
      const when = [appointment.appointment_date, appointment.appointment_time].filter(Boolean).join(' at ');
      bookingSuccessAlert.textContent = 'Booking request saved successfully.';
      bookingSuccessText.textContent = when
        ? 'Your appointment request for ' + bookedFor + ' has been saved for ' + when + '.'
        : 'Your appointment request for ' + bookedFor + ' has been saved successfully.';
    } catch (error) {
      setBookingAlert('error', 'Something went wrong while saving the booking. Please try again.');
    } finally {
      bookingSubmitBtn.disabled = false;
      bookingSubmitBtn.querySelector('span').textContent = 'Confirm Booking';
    }
  }

  openCallBtn?.addEventListener('click', openCallModal);
  closeCallBtn?.addEventListener('click', closeCallModal);
  openBookBtns.forEach((btn) => btn.addEventListener('click', async function () {
    beginBookingFlow(true);
  }));
  closeBookBtns.forEach((btn) => btn.addEventListener('click', closeBookingModal));
  bookingChoiceButtons.forEach((btn) => {
    btn.addEventListener('click', function () {
      applyBookingChoice(btn.dataset.bookingChoice);
    });
  });
  bookingForm?.addEventListener('submit', submitBookingForm);

  callModal?.addEventListener('click', function (event) {
    if (event.target === callModal) closeCallModal();
  });

  bookingModal?.addEventListener('click', function (event) {
    if (event.target === bookingModal) closeBookingModal();
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    closeCallModal();
    closeBookingModal();
  });

  copyBtn?.addEventListener('click', async function () {
    const value = callNumberEl?.textContent?.trim() || '';
    if (!value) return;

    try {
      await navigator.clipboard.writeText(value);
      copyBtn.textContent = 'Copied';
      copyBtn.classList.add('is-copied');
      window.setTimeout(() => {
        copyBtn.textContent = 'Copy';
        copyBtn.classList.remove('is-copied');
      }, 1400);
    } catch (error) {
      copyBtn.textContent = 'Copy failed';
      window.setTimeout(() => {
        copyBtn.textContent = 'Copy';
      }, 1400);
    }
  });

  if (currentUrl.searchParams.get('book') === '1') {
    verifyActiveSession().then((isActive) => {
      if (isActive) {
        beginBookingFlow(true);
        currentUrl.searchParams.delete('book');
        const nextUrl = currentUrl.pathname + (currentUrl.search ? currentUrl.search : '');
        window.history.replaceState({}, '', nextUrl);
      }
    });
  }
});
</script>
@endsection
