<template>
  <Head title="Add New Staff" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">
          Add New Staff Member
        </h1>
        <p class="mb-0 text-muted">Create a new staff member or crew profile</p>
      </div>
      <Link :href="route('staff.index')" class="btn btn-outline-secondary">
        Back to Staff List
      </Link>
    </div>

    <!-- Form Card -->
    <form @submit.prevent="submitForm" class="needs-validation" novalidate>
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <!-- Progress Steps -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="progress" style="height: 4px;">
                <div
                  class="progress-bar bg-primary"
                  role="progressbar"
                  :style="{ width: progressPercentage + '%' }"
                ></div>
              </div>
              <div class="d-flex justify-content-between mt-2">
                <span
                  v-for="(step, index) in steps"
                  :key="index"
                  class="badge"
                  :class="currentStep >= index ? 'bg-primary' : 'bg-light text-muted'"
                >
                  {{ step }}
                </span>
              </div>
            </div>
          </div>

          <!-- Step 1: Basic Information -->
          <div v-show="currentStep === 0" class="step-content">
            <h5 class="card-title mb-4">
              Basic Information
            </h5>

            <div class="clearfix"></div>

            <div class="row g-3">
              <!-- First Name -->
              <div class="col-md-3">
                <label for="first_name" class="form-label required">First Name</label>
                <input
                  v-model="form.first_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.first_name }"
                  id="first_name"
                  placeholder="John"
                  required
                >
                <div v-if="errors.first_name" class="invalid-feedback">
                  {{ errors.first_name }}
                </div>
              </div>

              <!-- Last Name -->
              <div class="col-md-3">
                <label for="last_name" class="form-label required">Last Name</label>
                <input
                  v-model="form.last_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.last_name }"
                  id="last_name"
                  placeholder="Smith"
                  required
                >
                <div v-if="errors.last_name" class="invalid-feedback">
                  {{ errors.last_name }}
                </div>
              </div>

              <!-- Middle Name -->
              <div class="col-md-3">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input
                  v-model="form.middle_name"
                  type="text"
                  class="form-control"
                  id="middle_name"
                  placeholder="Optional"
                >
              </div>

              <!-- Gender -->
              <div class="col-md-3">
                <label for="gender_id" class="form-label required">Gender</label>
                <select
                  v-model="form.gender_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.gender_id }"
                  id="gender_id"
                  required
                >
                  <option value="">Select Gender</option>
                  <option v-for="gender in genders" :key="gender.id" :value="gender.id">
                    {{ gender.name }}
                  </option>
                </select>
                <div v-if="errors.gender_id" class="invalid-feedback">
                  {{ errors.gender_id }}
                </div>
              </div>

              <!-- Profile Photo -->
              <div class="col-md-3">
                <label for="profile_photo" class="form-label">Profile Photo</label>
                <input
                  @change="handlePhotoUpload"
                  type="file"
                  class="form-control"
                  :class="{ 'is-invalid': errors.profile_photo }"
                  id="profile_photo"
                  accept="image/*"
                  ref="photoInput"
                  :disabled="photoUploading"
                >
                <div v-if="errors.profile_photo" class="invalid-feedback">
                  {{ errors.profile_photo }}
                </div>
                <small class="form-text text-muted">Max 10MB, JPG/PNG/WEBP</small>

                <!-- Upload Progress -->
                <div v-if="photoUploading" class="mt-2">
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                      Uploading...
                    </div>
                  </div>
                </div>

                <!-- Upload Success -->
                <div v-if="photoUploaded" class="mt-2 text-success small">
                  ✓ Photo uploaded successfully
                </div>
              </div>

              <!-- Date of Birth -->
              <div class="col-md-3">
                <label for="date_of_birth" class="form-label required">Date of Birth</label>
                <input
                  v-model="form.date_of_birth"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.date_of_birth }"
                  id="date_of_birth"
                  required
                >
                <div v-if="errors.date_of_birth" class="invalid-feedback">
                  {{ errors.date_of_birth }}
                </div>
              </div>

              <!-- Country -->
              <div class="col-md-3">
                <label for="country_id" class="form-label required">Country</label>
                <select
                  v-model="form.country_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.country_id }"
                  id="country_id"
                  required
                >
                  <option value="">Select Country</option>
                  <option v-for="country in countries" :key="country.id" :value="country.id">
                    {{ country.name }}
                  </option>
                </select>
                <div v-if="errors.country_id" class="invalid-feedback">
                  {{ errors.country_id }}
                </div>
              </div>

              <!-- Hire Date -->
              <div class="col-md-3">
                <label for="hire_date" class="form-label required">Hire Date</label>
                <input
                  v-model="form.hire_date"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.hire_date }"
                  id="hire_date"
                  required
                >
                <div v-if="errors.hire_date" class="invalid-feedback">
                  {{ errors.hire_date }}
                </div>
              </div>

              <!-- Contact Information Section -->
              <div class="col-12 mt-4">
                <h6 class="text-muted mb-3">Contact Information</h6>
              </div>

              <!-- Row 3: Email, Primary Phone, Secondary Phone, Address Line 1 -->
              <div class="col-md-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': errors.email }"
                  id="email"
                >
                <div v-if="errors.email" class="invalid-feedback">
                  {{ errors.email }}
                </div>
              </div>

              <div class="col-md-3">
                <label for="phone_primary" class="form-label">Primary Phone</label>
                <input
                  v-model="form.phone_primary"
                  type="tel"
                  class="form-control"
                  id="phone_primary"
                >
              </div>

              <div class="col-md-3">
                <label for="phone_secondary" class="form-label">Secondary Phone</label>
                <input
                  v-model="form.phone_secondary"
                  type="tel"
                  class="form-control"
                  id="phone_secondary"
                >
              </div>

              <div class="col-md-3">
                <label for="address_line_1" class="form-label">Address Line 1</label>
                <input
                  v-model="form.address_line_1"
                  type="text"
                  class="form-control"
                  id="address_line_1"
                >
              </div>

              <!-- Row 4: Address Line 2, City, State/Region, Postal Code -->
              <div class="col-md-3">
                <label for="address_line_2" class="form-label">Address Line 2</label>
                <input
                  v-model="form.address_line_2"
                  type="text"
                  class="form-control"
                  id="address_line_2"
                >
              </div>

              <div class="col-md-3">
                <label for="city" class="form-label">City</label>
                <input
                  v-model="form.city"
                  type="text"
                  class="form-control"
                  id="city"
                >
              </div>

              <div class="col-md-3">
                <label for="state_region" class="form-label">State/Region</label>
                <input
                  v-model="form.state_region"
                  type="text"
                  class="form-control"
                  id="state_region"
                >
              </div>

              <div class="col-md-3">
                <label for="postal_code" class="form-label">Postal Code</label>
                <input
                  v-model="form.postal_code"
                  type="text"
                  class="form-control"
                  id="postal_code"
                >
              </div>

              <!-- Next of Kin Information Section -->
              <div class="col-12 mt-4">
                <h6 class="text-muted mb-3">Next of Kin Information</h6>
              </div>

              <!-- Row 5: Next of Kin Name, Phone, Email, Relationship -->
              <div class="col-md-3">
                <label for="next_of_kin_name" class="form-label">Next of Kin Name</label>
                <input
                  v-model="form.next_of_kin_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.next_of_kin_name }"
                  id="next_of_kin_name"
                >
                <div v-if="errors.next_of_kin_name" class="invalid-feedback">
                  {{ errors.next_of_kin_name }}
                </div>
              </div>

              <div class="col-md-3">
                <label for="next_of_kin_phone" class="form-label">Next of Kin Phone</label>
                <input
                  v-model="form.next_of_kin_phone"
                  type="tel"
                  class="form-control"
                  :class="{ 'is-invalid': errors.next_of_kin_phone }"
                  id="next_of_kin_phone"
                >
                <div v-if="errors.next_of_kin_phone" class="invalid-feedback">
                  {{ errors.next_of_kin_phone }}
                </div>
              </div>

              <div class="col-md-3">
                <label for="next_of_kin_email" class="form-label">Next of Kin Email</label>
                <input
                  v-model="form.next_of_kin_email"
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': errors.next_of_kin_email }"
                  id="next_of_kin_email"
                >
                <div v-if="errors.next_of_kin_email" class="invalid-feedback">
                  {{ errors.next_of_kin_email }}
                </div>
              </div>

              <div class="col-md-3">
                <label for="next_of_kin_relationship" class="form-label">Relationship</label>
                <select
                  v-model="form.next_of_kin_relationship"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.next_of_kin_relationship }"
                  id="next_of_kin_relationship"
                >
                  <option value="">Select Relationship</option>
                  <option value="spouse">Spouse</option>
                  <option value="parent">Parent</option>
                  <option value="child">Child</option>
                  <option value="sibling">Sibling</option>
                  <option value="guardian">Guardian</option>
                  <option value="friend">Friend</option>
                  <option value="other">Other</option>
                </select>
                <div v-if="errors.next_of_kin_relationship" class="invalid-feedback">
                  {{ errors.next_of_kin_relationship }}
                </div>
              </div>

              <!-- Additional Notes -->
              <div class="col-12 mt-3">
                <label for="notes" class="form-label">Additional Notes</label>
                <textarea
                  v-model="form.notes"
                  class="form-control"
                  id="notes"
                  rows="3"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Step 2: Employment Information -->
          <div v-show="currentStep === 1" class="step-content">
            <h5 class="card-title mb-4">
              Employment Information
            </h5>

            <div class="clearfix"></div>

            <div class="row g-3">
              <!-- Department -->
              <div class="col-md-3">
                <label for="department_id" class="form-label required">Department</label>
                <select
                  v-model="form.department_id"
                  @change="filterPositions"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.department_id }"
                  id="department_id"
                  required
                >
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }} ({{ dept.code }})
                  </option>
                </select>
                <div v-if="errors.department_id" class="invalid-feedback">
                  {{ errors.department_id }}
                </div>
              </div>

              <!-- Position -->
              <div class="col-md-3">
                <label for="position_id" class="form-label required">Position</label>
                <select
                  v-model="form.position_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.position_id }"
                  id="position_id"
                  required
                  :disabled="!form.department_id"
                >
                  <option value="">{{ form.department_id ? 'Select Position' : 'Select Department First' }}</option>
                  <option v-for="position in filteredPositions" :key="position.id" :value="position.id">
                    {{ position.title }} ({{ position.type }})
                  </option>
                </select>
                <div v-if="errors.position_id" class="invalid-feedback">
                  {{ errors.position_id }}
                </div>
              </div>

              <!-- Status -->
              <div class="col-md-3">
                <label for="status_id" class="form-label required">Status</label>
                <select
                  v-model="form.status_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.status_id }"
                  id="status_id"
                  required
                >
                  <option value="">Select Status</option>
                  <option v-for="status in statuses" :key="status.id" :value="status.id">
                    {{ status.name }}
                  </option>
                </select>
                <div v-if="errors.status_id" class="invalid-feedback">
                  {{ errors.status_id }}
                </div>
              </div>

              <!-- Employment Type -->
              <div class="col-md-3">
                <label for="employment_type_id" class="form-label required">Employment Type</label>
                <select
                  v-model="form.employment_type_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.employment_type_id }"
                  id="employment_type_id"
                  required
                >
                  <option value="">Select Employment Type</option>
                  <option v-for="empType in employmentTypes" :key="empType.id" :value="empType.id">
                    {{ empType.name }}
                  </option>
                </select>
                <div v-if="errors.employment_type_id" class="invalid-feedback">
                  {{ errors.employment_type_id }}
                </div>
              </div>

              <!-- Work Location -->
              <div class="col-md-3">
                <label for="work_location_id" class="form-label">Work Location</label>
                <select
                  v-model="form.work_location_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.work_location_id }"
                  id="work_location_id"
                >
                  <option value="">Select Work Location</option>
                  <option v-for="location in workLocations" :key="location.id" :value="location.id">
                    {{ location.name }}
                  </option>
                </select>
                <div v-if="errors.work_location_id" class="invalid-feedback">
                  {{ errors.work_location_id }}
                </div>
              </div>

              <!-- Contract Start Date -->
              <div class="col-md-3">
                <label for="contract_start_date" class="form-label">Contract Start Date</label>
                <input
                  v-model="form.contract_start_date"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.contract_start_date }"
                  id="contract_start_date"
                >
                <div v-if="errors.contract_start_date" class="invalid-feedback">
                  {{ errors.contract_start_date }}
                </div>
              </div>

              <!-- Contract End Date -->
              <div class="col-md-3">
                <label for="contract_end_date" class="form-label">Contract End Date</label>
                <input
                  v-model="form.contract_end_date"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.contract_end_date }"
                  id="contract_end_date"
                >
                <div v-if="errors.contract_end_date" class="invalid-feedback">
                  {{ errors.contract_end_date }}
                </div>
              </div>

              <!-- Supervisor Name -->
              <div class="col-md-3">
                <label for="supervisor_name" class="form-label">Supervisor Name</label>
                <input
                  v-model="form.supervisor_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.supervisor_name }"
                  id="supervisor_name"
                  placeholder="John Smith"
                >
                <div v-if="errors.supervisor_name" class="invalid-feedback">
                  {{ errors.supervisor_name }}
                </div>
              </div>

              <!-- Salary -->
              <div class="col-md-3">
                <label for="salary" class="form-label">Monthly Salary (TZS)</label>
                <div class="input-group">
                  <span class="input-group-text">TZS</span>
                  <input
                    v-model="form.salary"
                    type="number"
                    step="0.01"
                    class="form-control"
                    :class="{ 'is-invalid': errors.salary }"
                    id="salary"
                    placeholder="2500000.00"
                  >
                </div>
                <div v-if="errors.salary" class="invalid-feedback">
                  {{ errors.salary }}
                </div>
              </div>

              <!-- Current Base -->
              <div class="col-md-3">
                <label for="current_base" class="form-label">Current Base</label>
                <input
                  v-model="form.current_base"
                  type="text"
                  class="form-control"
                  id="current_base"
                  placeholder="Zanzibar Airport"
                >
                <small class="form-text text-muted">For crew members only</small>
              </div>
            </div>
          </div>



          <!-- Step 3: Documents & Education -->
          <div v-show="currentStep === 2" class="step-content">
            <h5 class="card-title mb-4">
              Documents & Education
            </h5>

            <div class="clearfix"></div>

            <div class="row g-3">
              <!-- Identification Type -->
              <div class="col-md-3">
                <label for="identification_type_id" class="form-label">Identification Type</label>
                <select
                  v-model="form.identification_type_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.identification_type_id }"
                  id="identification_type_id"
                >
                  <option value="">Select Identification Type</option>
                  <option v-for="idType in identificationTypes" :key="idType.id" :value="idType.id">
                    {{ idType.name }}
                  </option>
                </select>
                <div v-if="errors.identification_type_id" class="invalid-feedback">
                  {{ errors.identification_type_id }}
                </div>
              </div>

              <!-- Identification Number -->
              <div class="col-md-3">
                <label for="identification_number" class="form-label">Identification Number</label>
                <input
                  v-model="form.identification_number"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.identification_number }"
                  id="identification_number"
                  placeholder="123456789"
                >
                <div v-if="errors.identification_number" class="invalid-feedback">
                  {{ errors.identification_number }}
                </div>
              </div>

              <!-- Identification File -->
              <div class="col-md-3">
                <label for="identification_file" class="form-label">Identification File</label>
                <input
                  @change="handleFileUpload"
                  type="file"
                  class="form-control"
                  :class="{ 'is-invalid': errors.identification_file }"
                  id="identification_file"
                  accept=".pdf,.jpg,.jpeg,.png"
                >
                <small class="form-text text-muted">Upload PDF, JPG or PNG</small>
                <div v-if="errors.identification_file" class="invalid-feedback">
                  {{ errors.identification_file }}
                </div>
              </div>

              <!-- Highest Qualification -->
              <div class="col-md-3">
                <label for="highest_qualification_id" class="form-label">Highest Qualification</label>
                <select
                  v-model="form.highest_qualification_id"
                  class="form-select custom-select"
                  :class="{ 'is-invalid': errors.highest_qualification_id }"
                  id="highest_qualification_id"
                >
                  <option value="">Select Qualification</option>
                  <option v-for="qualification in qualifications" :key="qualification.id" :value="qualification.id">
                    {{ qualification.name }}
                  </option>
                </select>
                <div v-if="errors.highest_qualification_id" class="invalid-feedback">
                  {{ errors.highest_qualification_id }}
                </div>
              </div>

              <!-- Qualification Name -->
              <div class="col-md-3">
                <label for="qualification_name" class="form-label">Qualification Name</label>
                <input
                  v-model="form.qualification_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.qualification_name }"
                  id="qualification_name"
                  placeholder="Bachelor of Science in Aviation"
                >
                <div v-if="errors.qualification_name" class="invalid-feedback">
                  {{ errors.qualification_name }}
                </div>
              </div>

              <!-- Institution Name -->
              <div class="col-md-3">
                <label for="institution_name" class="form-label">Institution Name</label>
                <input
                  v-model="form.institution_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.institution_name }"
                  id="institution_name"
                  placeholder="University of Dar es Salaam"
                >
                <div v-if="errors.institution_name" class="invalid-feedback">
                  {{ errors.institution_name }}
                </div>
              </div>

              <!-- Graduation Year -->
              <div class="col-md-3">
                <label for="graduation_year" class="form-label">Graduation Year</label>
                <input
                  v-model="form.graduation_year"
                  type="number"
                  min="1950"
                  max="2030"
                  class="form-control"
                  :class="{ 'is-invalid': errors.graduation_year }"
                  id="graduation_year"
                  placeholder="2020"
                >
                <div v-if="errors.graduation_year" class="invalid-feedback">
                  {{ errors.graduation_year }}
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Card Footer with Navigation -->
        <div class="card-footer bg-transparent border-top">
          <div class="d-flex justify-content-between">
            <button
              v-if="currentStep > 0"
              @click="previousStep"
              type="button"
              class="btn btn-outline-secondary"
            >
              Previous
            </button>
            <div v-else></div>

            <div class="d-flex gap-2">
              <button
                v-if="currentStep < steps.length - 1"
                @click="nextStep"
                type="button"
                class="btn btn-primary"
                :disabled="!canProceed"
              >
                Next
              </button>
              <button
                v-else
                type="submit"
                class="btn btn-success"
                :disabled="processing || !canProceed"
              >
                {{ processing ? 'Creating...' : 'Create Staff Member' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'

const props = defineProps({
  departments: Array,
  positions: Array,
  genders: Array,
  statuses: Array,
  countries: Array,
  employmentTypes: Array,
  workLocations: Array,
  qualifications: Array,
  identificationTypes: Array,
  errors: Object
})

// Form steps
const steps = ['Basic Info', 'Employment', 'Documents & Education']
const currentStep = ref(0)

// Photo upload state
const photoUploading = ref(false)
const photoUploaded = ref(false)
const photoInput = ref(null)

// Form data
const form = useForm({
  // Basic Information
  first_name: '',
  middle_name: '',
  last_name: '',
  profile_photo: null,
  date_of_birth: '',
  gender_id: '',
  country_id: '',

  // Employment Information
  hire_date: new Date().toISOString().split('T')[0],
  contract_start_date: '',
  contract_end_date: '',
  department_id: '',
  position_id: '',
  status_id: '',
  employment_type_id: '',
  current_base: '',
  work_location_id: '',
  supervisor_name: '',
  salary: '',

  // Contact Information
  email: '',
  phone_primary: '',
  phone_secondary: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state_region: '',
  postal_code: '',
  country: 'Tanzania', // Keep for backward compatibility

  // Next of Kin
  next_of_kin_name: '',
  next_of_kin_phone: '',
  next_of_kin_email: '',
  next_of_kin_relationship: '',

  // Identification
  identification_type: '', // Keep for backward compatibility
  identification_type_id: '',
  identification_number: '',
  identification_file: null,

  // Education
  highest_qualification_id: '',
  qualification_name: '',
  institution_name: '',
  graduation_year: '',

  // Notes
  notes: ''
})

// Filtered positions based on selected department
const filteredPositions = computed(() => {
  if (!form.department_id) return []
  return props.positions.filter(position => position.department_id == form.department_id)
})

// Progress percentage
const progressPercentage = computed(() => {
  return ((currentStep.value + 1) / steps.length) * 100
})

// Can proceed to next step validation
const canProceed = computed(() => {
  switch (currentStep.value) {
    case 0: // Basic Information (now includes contact and next of kin)
      return form.first_name && form.last_name && form.date_of_birth &&
             form.gender_id && form.country_id && form.hire_date
    case 1: // Employment Information
      return form.department_id && form.position_id && form.status_id && form.employment_type_id
    case 2: // Documents & Education (all optional)
      return true
    default:
      return false
  }
})

// Navigation methods
const nextStep = () => {
  if (currentStep.value < steps.length - 1 && canProceed.value) {
    currentStep.value++
    scrollToTop()
  }
}

const previousStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--
    scrollToTop()
  }
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Filter positions when department changes
const filterPositions = () => {
  form.position_id = '' // Reset position when department changes
}

// Handle file uploads
// Handle photo upload with AJAX
const handlePhotoUpload = async (event) => {
  const file = event.target.files[0]

  if (!file) return

  photoUploading.value = true
  photoUploaded.value = false

  const formData = new FormData()
  formData.append('profile_photo', file)

  try {
    const response = await axios.post('/staff/upload-photo', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    if (response.data.success) {
      form.profile_photo = response.data.path
      photoUploaded.value = true
    }
  } catch (error) {
    console.error('Photo upload failed:', error)
    if (error.response?.data?.errors?.profile_photo) {
      // Handle validation errors
      alert(error.response.data.errors.profile_photo[0])
    } else {
      alert('Photo upload failed. Please try again.')
    }
    // Reset the input
    if (photoInput.value) {
      photoInput.value.value = ''
    }
  } finally {
    photoUploading.value = false
  }
}

// Handle other file uploads
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  const fieldId = event.target.id

  if (file && fieldId === 'identification_file') {
    form.identification_file = file
  }
}

// Submit form
const submitForm = () => {
  form.post(route('staff.store'), {
    onSuccess: () => {
      // Redirect handled by controller
    },
    onError: (errors) => {
      // Find the step with errors and navigate to it
      const errorFields = Object.keys(errors)

      // Step 0: Basic Information (now includes contact and next of kin fields)
      if (errorFields.some(field => ['first_name', 'middle_name', 'last_name', 'profile_photo', 'date_of_birth', 'gender_id', 'country_id', 'hire_date', 'email', 'phone_primary', 'phone_secondary', 'address_line_1', 'address_line_2', 'city', 'state_region', 'postal_code', 'country', 'next_of_kin_name', 'next_of_kin_phone', 'next_of_kin_email', 'next_of_kin_relationship', 'notes'].includes(field))) {
        currentStep.value = 0
      }
      // Step 1: Employment Information
      else if (errorFields.some(field => ['contract_start_date', 'contract_end_date', 'department_id', 'position_id', 'status_id', 'employment_type_id', 'current_base', 'work_location_id', 'supervisor_name', 'salary'].includes(field))) {
        currentStep.value = 1
      }
      // Step 2: Documents & Education
      else if (errorFields.some(field => ['identification_type', 'identification_number', 'identification_file', 'highest_qualification_id', 'qualification_name', 'institution_name', 'graduation_year'].includes(field))) {
        currentStep.value = 2
      }
      // Default to first step if no specific field mapping
      else {
        currentStep.value = 0
      }

      scrollToTop()
    }
  })
}

// Set default status to Active on mount
onMounted(() => {
  const activeStatus = props.statuses.find(status => status.code === 'ACTIVE')
  if (activeStatus) {
    form.status_id = activeStatus.id
  }
})
</script>

<style scoped>
.required::after {
  content: " *";
  color: #dc3545;
}

.step-content {
  min-height: 400px;
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateX(20px); }
  to { opacity: 1; transform: translateX(0); }
}

.form-control:focus,
.form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card {
  border-radius: 12px;
}

.card-footer {
  border-radius: 0 0 12px 12px;
}

.progress {
  border-radius: 10px;
}

.progress-bar {
  border-radius: 10px;
  transition: width 0.3s ease;
}

.btn {
  border-radius: 8px;
  font-weight: 500;
}

.input-group-text {
  background-color: #f8f9fa;
  border-color: #ced4da;
}

.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
}

.custom-select {
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
}

.custom-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-select:disabled {
  background-color: #e9ecef;
}
</style>