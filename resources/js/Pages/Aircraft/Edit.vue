<template>
  <Head :title="`Edit ${aircraft.registration_number}`" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="mb-4">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item">
            <Link :href="route('aircraft.index')" class="text-decoration-none">Aircraft</Link>
          </li>
          <li class="breadcrumb-item">
            <Link :href="route('aircraft.show', aircraft.id)" class="text-decoration-none">{{ aircraft.registration_number }}</Link>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </nav>
      <h1 class="h3 mb-1 text-gray-900">Edit Aircraft</h1>
      <p class="mb-0 text-muted">
        Update aircraft details and specifications
      </p>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <!-- Aircraft Information Form -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="mb-0 fw-bold">
              <i class="fas fa-plane text-primary me-2"></i>Aircraft Information
            </h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitForm">
              <div class="row">
                <!-- Registration Number -->
                <div class="col-md-6 mb-3">
                  <label for="registrationNumber" class="form-label required">Registration Number</label>
                  <input
                    v-model="form.registration_number"
                    type="text"
                    id="registrationNumber"
                    class="form-control"
                    :class="{ 'is-invalid': errors.registration_number }"
                    placeholder="e.g., 5H-ABC"
                    required
                  />
                  <div v-if="errors.registration_number" class="invalid-feedback">
                    {{ errors.registration_number }}
                  </div>
                </div>

                <!-- Serial Number -->
                <div class="col-md-6 mb-3">
                  <label for="serialNumber" class="form-label required">Serial Number</label>
                  <input
                    v-model="form.serial_number"
                    type="text"
                    id="serialNumber"
                    class="form-control"
                    :class="{ 'is-invalid': errors.serial_number }"
                    placeholder="Aircraft serial number"
                    required
                  />
                  <div v-if="errors.serial_number" class="invalid-feedback">
                    {{ errors.serial_number }}
                  </div>
                </div>

                <!-- Manufacturer -->
                <div class="col-md-6 mb-3">
                  <label for="manufacturer" class="form-label required">Manufacturer</label>
                  <select
                    v-model="form.manufacturer_id"
                    id="manufacturer"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.manufacturer_id }"
                    @change="onManufacturerChange"
                    required
                  >
                    <option value="">Select Manufacturer</option>
                    <option v-for="manufacturer in manufacturers" :key="manufacturer.id" :value="manufacturer.id">
                      {{ manufacturer.name }}
                    </option>
                  </select>
                  <div v-if="errors.manufacturer_id" class="invalid-feedback">
                    {{ errors.manufacturer_id }}
                  </div>
                </div>

                <!-- Model/Type -->
                <div class="col-md-6 mb-3">
                  <label for="modelType" class="form-label required">Model/Type</label>
                  <select
                    v-model="form.model_id"
                    id="modelType"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.model_id }"
                    :disabled="!form.manufacturer_id"
                    required
                  >
                    <option value="">{{ form.manufacturer_id ? 'Select Model' : 'Select Manufacturer First' }}</option>
                    <option v-for="model in availableModels" :key="model.id" :value="model.id">
                      {{ model.name }}
                    </option>
                  </select>
                  <div v-if="errors.model_id" class="invalid-feedback">
                    {{ errors.model_id }}
                  </div>
                </div>

                <!-- Year of Manufacture -->
                <div class="col-md-4 mb-3">
                  <label for="yearOfManufacture" class="form-label required">Year of Manufacture</label>
                  <input
                    v-model="form.year_of_manufacture"
                    type="number"
                    id="yearOfManufacture"
                    class="form-control"
                    :class="{ 'is-invalid': errors.year_of_manufacture }"
                    :min="1900"
                    :max="new Date().getFullYear() + 1"
                    required
                  />
                  <div v-if="errors.year_of_manufacture" class="invalid-feedback">
                    {{ errors.year_of_manufacture }}
                  </div>
                </div>

                <!-- Current Status -->
                <div class="col-md-4 mb-3">
                  <label for="currentStatus" class="form-label required">Current Status</label>
                  <select
                    v-model="form.status_id"
                    id="currentStatus"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.status_id }"
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

                <!-- Home Base -->
                <div class="col-md-4 mb-3">
                  <label for="homeBase" class="form-label required">Home Base</label>
                  <input
                    v-model="form.home_base"
                    type="text"
                    id="homeBase"
                    class="form-control"
                    :class="{ 'is-invalid': errors.home_base }"
                    placeholder="e.g., HTDA, HTZA"
                    list="homeBaseList"
                    required
                  />
                  <datalist id="homeBaseList">
                    <option v-for="base in homeBases" :key="base" :value="base">{{ base }}</option>
                  </datalist>
                  <div v-if="errors.home_base" class="invalid-feedback">
                    {{ errors.home_base }}
                  </div>
                </div>

                <!-- Total Airframe Hours -->
                <div class="col-md-6 mb-3">
                  <label for="totalAirframeHours" class="form-label">Total Airframe Hours</label>
                  <div class="input-group">
                    <input
                      v-model="form.total_airframe_hours"
                      type="number"
                      id="totalAirframeHours"
                      class="form-control"
                      :class="{ 'is-invalid': errors.total_airframe_hours }"
                      step="0.1"
                      min="0"
                      placeholder="0.0"
                    />
                    <span class="input-group-text">hrs</span>
                    <div v-if="errors.total_airframe_hours" class="invalid-feedback">
                      {{ errors.total_airframe_hours }}
                    </div>
                  </div>
                </div>

                <!-- Total Cycles -->
                <div class="col-md-6 mb-3">
                  <label for="totalCycles" class="form-label">Total Cycles</label>
                  <input
                    v-model="form.total_cycles"
                    type="number"
                    id="totalCycles"
                    class="form-control"
                    :class="{ 'is-invalid': errors.total_cycles }"
                    min="0"
                    placeholder="0"
                  />
                  <div v-if="errors.total_cycles" class="invalid-feedback">
                    {{ errors.total_cycles }}
                  </div>
                </div>

                <!-- Seating Configuration -->
                <div class="col-12 mb-3">
                  <label for="seatingConfiguration" class="form-label">Seating Configuration</label>
                  <textarea
                    v-model="form.seating_configuration"
                    id="seatingConfiguration"
                    class="form-control"
                    :class="{ 'is-invalid': errors.seating_configuration }"
                    rows="2"
                    placeholder="Describe the seating arrangement (e.g., 2+2 seats, Economy: 180, Business: 32)"
                  ></textarea>
                  <div v-if="errors.seating_configuration" class="invalid-feedback">
                    {{ errors.seating_configuration }}
                  </div>
                </div>

                <!-- Capacity Information -->
                <div class="col-md-6 mb-3">
                  <label for="totalCapacity" class="form-label">Total Capacity</label>
                  <input
                    v-model="form.total_capacity"
                    type="number"
                    id="totalCapacity"
                    class="form-control"
                    :class="{ 'is-invalid': errors.total_capacity }"
                    min="1"
                    placeholder="Total seats/capacity"
                  />
                  <div v-if="errors.total_capacity" class="invalid-feedback">
                    {{ errors.total_capacity }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="maxPassengers" class="form-label">Max Passengers</label>
                  <input
                    v-model="form.max_passengers"
                    type="number"
                    id="maxPassengers"
                    class="form-control"
                    :class="{ 'is-invalid': errors.max_passengers }"
                    min="1"
                    placeholder="Maximum passengers"
                  />
                  <div v-if="errors.max_passengers" class="invalid-feedback">
                    {{ errors.max_passengers }}
                  </div>
                </div>

                <!-- Weight Specifications -->
                <div class="col-md-6 mb-3">
                  <label for="maxTakeoffWeight" class="form-label">Max Takeoff Weight (kg)</label>
                  <input
                    v-model="form.max_takeoff_weight"
                    type="number"
                    id="maxTakeoffWeight"
                    class="form-control"
                    :class="{ 'is-invalid': errors.max_takeoff_weight }"
                    step="0.01"
                    min="0"
                    placeholder="Maximum takeoff weight"
                  />
                  <div v-if="errors.max_takeoff_weight" class="invalid-feedback">
                    {{ errors.max_takeoff_weight }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="emptyWeight" class="form-label">Empty Weight (kg)</label>
                  <input
                    v-model="form.empty_weight"
                    type="number"
                    id="emptyWeight"
                    class="form-control"
                    :class="{ 'is-invalid': errors.empty_weight }"
                    step="0.01"
                    min="0"
                    placeholder="Empty weight"
                  />
                  <div v-if="errors.empty_weight" class="invalid-feedback">
                    {{ errors.empty_weight }}
                  </div>
                </div>

                <!-- Engine Information -->
                <div class="col-md-8 mb-3">
                  <label for="engineType" class="form-label">Engine Type</label>
                  <input
                    v-model="form.engine_type"
                    type="text"
                    id="engineType"
                    class="form-control"
                    :class="{ 'is-invalid': errors.engine_type }"
                    placeholder="e.g., CFM56-7B27, PW4077"
                  />
                  <div v-if="errors.engine_type" class="invalid-feedback">
                    {{ errors.engine_type }}
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label for="numberOfEngines" class="form-label">Number of Engines</label>
                  <select
                    v-model="form.number_of_engines"
                    id="numberOfEngines"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.number_of_engines }"
                  >
                    <option value="">Select</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                  </select>
                  <div v-if="errors.number_of_engines" class="invalid-feedback">
                    {{ errors.number_of_engines }}
                  </div>
                </div>

                <!-- Performance Data -->
                <div class="col-md-4 mb-3">
                  <label for="fuelCapacity" class="form-label">Fuel Capacity (L)</label>
                  <input
                    v-model="form.fuel_capacity"
                    type="number"
                    id="fuelCapacity"
                    class="form-control"
                    :class="{ 'is-invalid': errors.fuel_capacity }"
                    step="0.01"
                    min="0"
                    placeholder="Fuel capacity"
                  />
                  <div v-if="errors.fuel_capacity" class="invalid-feedback">
                    {{ errors.fuel_capacity }}
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label for="maximumRange" class="form-label">Maximum Range (NM)</label>
                  <input
                    v-model="form.maximum_range"
                    type="number"
                    id="maximumRange"
                    class="form-control"
                    :class="{ 'is-invalid': errors.maximum_range }"
                    min="0"
                    placeholder="Nautical miles"
                  />
                  <div v-if="errors.maximum_range" class="invalid-feedback">
                    {{ errors.maximum_range }}
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <label for="cruiseSpeed" class="form-label">Cruise Speed (kts)</label>
                  <input
                    v-model="form.cruise_speed"
                    type="number"
                    id="cruiseSpeed"
                    class="form-control"
                    :class="{ 'is-invalid': errors.cruise_speed }"
                    min="0"
                    placeholder="Knots"
                  />
                  <div v-if="errors.cruise_speed" class="invalid-feedback">
                    {{ errors.cruise_speed }}
                  </div>
                </div>

                <!-- Certification Information -->
                <div class="col-md-6 mb-3">
                  <label for="aircraftCategory" class="form-label">Aircraft Category</label>
                  <select
                    v-model="form.aircraft_category"
                    id="aircraftCategory"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.aircraft_category }"
                  >
                    <option value="">Select Category</option>
                    <option value="Normal">Normal</option>
                    <option value="Transport">Transport</option>
                    <option value="Commuter">Commuter</option>
                    <option value="Aerobatic">Aerobatic</option>
                    <option value="Utility">Utility</option>
                  </select>
                  <div v-if="errors.aircraft_category" class="invalid-feedback">
                    {{ errors.aircraft_category }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="certificateOfAirworthiness" class="form-label">Certificate of Airworthiness</label>
                  <input
                    v-model="form.certificate_of_airworthiness"
                    type="text"
                    id="certificateOfAirworthiness"
                    class="form-control"
                    :class="{ 'is-invalid': errors.certificate_of_airworthiness }"
                    placeholder="C of A number"
                  />
                  <div v-if="errors.certificate_of_airworthiness" class="invalid-feedback">
                    {{ errors.certificate_of_airworthiness }}
                  </div>
                </div>

                <!-- Operational Certifications -->
                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input
                      v-model="form.ifr_certified"
                      type="checkbox"
                      id="ifrCertified"
                      class="form-check-input"
                      :class="{ 'is-invalid': errors.ifr_certified }"
                    />
                    <label for="ifrCertified" class="form-check-label">
                      IFR Certified
                    </label>
                    <div v-if="errors.ifr_certified" class="invalid-feedback">
                      {{ errors.ifr_certified }}
                    </div>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input
                      v-model="form.rvsm_approved"
                      type="checkbox"
                      id="rvsmApproved"
                      class="form-check-input"
                      :class="{ 'is-invalid': errors.rvsm_approved }"
                    />
                    <label for="rvsmApproved" class="form-check-label">
                      RVSM Approved
                    </label>
                    <div v-if="errors.rvsm_approved" class="invalid-feedback">
                      {{ errors.rvsm_approved }}
                    </div>
                  </div>
                </div>

                <!-- Notes -->
                <div class="col-12 mb-3">
                  <label for="notes" class="form-label">Notes</label>
                  <textarea
                    v-model="form.notes"
                    id="notes"
                    class="form-control"
                    :class="{ 'is-invalid': errors.notes }"
                    rows="3"
                    placeholder="Additional notes or special information about this aircraft"
                  ></textarea>
                  <div v-if="errors.notes" class="invalid-feedback">
                    {{ errors.notes }}
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Photo Management Card -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="mb-0 fw-bold">
              <i class="fas fa-images text-info me-2"></i>Aircraft Photos
            </h5>
          </div>
          <div class="card-body">
            <!-- Existing Photos -->
            <div v-if="existingPhotos.length > 0" class="existing-photos mb-3">
              <h6 class="small fw-semibold text-muted mb-2">Current Photos ({{ existingPhotos.length }})</h6>
              <div class="row g-2">
                <div
                  v-for="(photo, index) in existingPhotos"
                  :key="index"
                  class="col-6"
                >
                  <div class="photo-preview position-relative">
                    <img
                      :src="`/storage/${photo}`"
                      class="img-fluid rounded"
                      style="height: 80px; width: 100%; object-fit: cover;"
                    />
                    <button
                      type="button"
                      class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                      style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: 0.3rem; --bs-btn-font-size: 0.7rem;"
                      @click="removeExistingPhoto(index)"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="upload-area mb-3">
              <input
                type="file"
                id="photoUpload"
                class="d-none"
                multiple
                accept="image/*"
                @change="handlePhotoUpload"
              />
              <label
                for="photoUpload"
                class="upload-label d-flex flex-column align-items-center justify-content-center text-center p-4 border-2 border-dashed border-secondary rounded cursor-pointer"
              >
                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                <div class="text-muted">
                  <strong>Add more photos</strong>
                  <br>
                  <small>Drag and drop or click to browse</small>
                </div>
              </label>
            </div>

            <!-- New Photo Preview -->
            <div v-if="newPhotos.length > 0" class="new-photos">
              <h6 class="small fw-semibold text-muted mb-2">New Photos ({{ newPhotos.length }})</h6>
              <div class="row g-2">
                <div
                  v-for="(photo, index) in newPhotos"
                  :key="index"
                  class="col-6"
                >
                  <div class="photo-preview position-relative">
                    <img
                      :src="photo.url"
                      class="img-fluid rounded"
                      style="height: 80px; width: 100%; object-fit: cover;"
                    />
                    <button
                      type="button"
                      class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                      style="--bs-btn-padding-y: 0.1rem; --bs-btn-padding-x: 0.3rem; --bs-btn-font-size: 0.7rem;"
                      @click="removeNewPhoto(index)"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Aircraft Summary Card -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="mb-0 fw-bold">
              <i class="fas fa-info-circle text-warning me-2"></i>Aircraft Summary
            </h5>
          </div>
          <div class="card-body">
            <div class="small">
              <div class="mb-2">
                <strong>Registration:</strong> {{ form.registration_number || 'Not set' }}
              </div>
              <div class="mb-2">
                <strong>Aircraft:</strong>
                {{ manufacturers.find(m => m.id == form.manufacturer_id)?.name || 'Not set' }}
                {{ availableModels.find(m => m.id == form.model_id)?.name || '' }}
              </div>
              <div class="mb-2">
                <strong>Year:</strong> {{ form.year_of_manufacture || 'Not set' }}
              </div>
              <div class="mb-2">
                <strong>Status:</strong>
                <span v-if="form.status_id" class="badge bg-primary ms-1">
                  {{ statuses.find(s => s.id == form.status_id)?.name }}
                </span>
                <span v-else class="text-muted">Not set</span>
              </div>
              <div class="mb-2">
                <strong>Hours:</strong> {{ form.total_airframe_hours || '0' }} hrs
              </div>
              <div>
                <strong>Cycles:</strong> {{ form.total_cycles || '0' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="d-flex justify-content-between">
          <Link
            :href="route('aircraft.show', aircraft.id)"
            class="btn btn-outline-secondary"
          >
            <i class="fas fa-arrow-left me-2"></i>Back to Aircraft Details
          </Link>
          <div class="d-flex gap-2">
            <button
              type="button"
              class="btn btn-outline-danger"
              @click="resetForm"
            >
              <i class="fas fa-undo me-2"></i>Reset Changes
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="isSubmitting"
              @click="submitForm"
            >
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="fas fa-save me-2"></i>
              {{ isSubmitting ? 'Updating...' : 'Update Aircraft' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const props = defineProps({
  aircraft: Object,
  manufacturers: Array,
  models: Array,
  homeBases: Array,
  statuses: Array,
  errors: Object,
});

// Form data
const form = useForm({
  registration_number: props.aircraft.registration_number || '',
  manufacturer_id: props.aircraft.manufacturer_id || '',
  model_id: props.aircraft.model_id || '',
  serial_number: props.aircraft.serial_number || '',
  year_of_manufacture: props.aircraft.year_of_manufacture || new Date().getFullYear(),
  total_airframe_hours: props.aircraft.total_airframe_hours || '',
  total_cycles: props.aircraft.total_cycles || '',
  status_id: props.aircraft.status_id || '',
  home_base: props.aircraft.home_base || '',
  seating_configuration: props.aircraft.seating_configuration || '',
  total_capacity: props.aircraft.total_capacity || '',
  max_passengers: props.aircraft.max_passengers || '',
  max_takeoff_weight: props.aircraft.max_takeoff_weight || '',
  max_landing_weight: props.aircraft.max_landing_weight || '',
  empty_weight: props.aircraft.empty_weight || '',
  useful_load: props.aircraft.useful_load || '',
  engine_type: props.aircraft.engine_type || '',
  number_of_engines: props.aircraft.number_of_engines || '',
  fuel_capacity: props.aircraft.fuel_capacity || '',
  service_ceiling: props.aircraft.service_ceiling || '',
  maximum_range: props.aircraft.maximum_range || '',
  cruise_speed: props.aircraft.cruise_speed || '',
  aircraft_category: props.aircraft.aircraft_category || '',
  certification_basis: props.aircraft.certification_basis || '',
  ifr_certified: props.aircraft.ifr_certified || false,
  rvsm_approved: props.aircraft.rvsm_approved || false,
  etops_rating: props.aircraft.etops_rating || '',
  certificate_of_airworthiness: props.aircraft.certificate_of_airworthiness || '',
  coa_issue_date: props.aircraft.coa_issue_date || '',
  coa_expiry_date: props.aircraft.coa_expiry_date || '',
  avionics_suite: props.aircraft.avionics_suite || '',
  propeller_type: props.aircraft.propeller_type || '',
  noise_certification: props.aircraft.noise_certification || '',
  photos: [...(props.aircraft.photos || [])],
  notes: props.aircraft.notes || ''
});

// State
const isSubmitting = ref(false);
const existingPhotos = ref([...(props.aircraft.photos || [])]);
const newPhotos = ref([]);
const availableModels = ref([]);

// Methods
const onManufacturerChange = () => {
  // Clear model selection when manufacturer changes
  form.model_id = '';

  // Filter models by selected manufacturer
  if (form.manufacturer_id) {
    availableModels.value = props.models.filter(
      model => model.manufacturer_id == form.manufacturer_id
    );
  } else {
    availableModels.value = [];
  }
};

const handlePhotoUpload = async (event) => {
  const files = Array.from(event.target.files);

  for (const file of files) {
    if (file.size > 5 * 1024 * 1024) { // 5MB limit
      alert(`File ${file.name} is too large. Maximum size is 5MB.`);
      continue;
    }

    const formData = new FormData();
    formData.append('photo', file);

    try {
      const response = await fetch(route('aircraft.upload-photo'), {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        newPhotos.value.push({
          path: result.path,
          url: result.url
        });
        form.photos.push(result.path);
      } else {
        alert(`Failed to upload ${file.name}`);
      }
    } catch (error) {
      console.error('Upload error:', error);
      alert(`Failed to upload ${file.name}`);
    }
  }

  // Clear the input
  event.target.value = '';
};

const removeExistingPhoto = (index) => {
  const photo = existingPhotos.value[index];
  existingPhotos.value.splice(index, 1);

  // Remove from form photos array
  const formPhotoIndex = form.photos.indexOf(photo);
  if (formPhotoIndex > -1) {
    form.photos.splice(formPhotoIndex, 1);
  }
};

const removeNewPhoto = (index) => {
  const photo = newPhotos.value[index];
  newPhotos.value.splice(index, 1);

  // Remove from form photos array
  const formPhotoIndex = form.photos.indexOf(photo.path);
  if (formPhotoIndex > -1) {
    form.photos.splice(formPhotoIndex, 1);
  }
};

const submitForm = () => {
  isSubmitting.value = true;

  form.put(route('aircraft.update', props.aircraft.id), {
    onSuccess: () => {
      // Success is handled by the backend redirect
    },
    onError: (errors) => {
      console.error('Validation errors:', errors);
      // Scroll to first error
      const firstErrorElement = document.querySelector('.is-invalid');
      if (firstErrorElement) {
        firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

// Initialize available models on component mount
onMounted(() => {
  // Set initial available models based on current manufacturer
  if (form.manufacturer_id) {
    availableModels.value = props.models.filter(
      model => model.manufacturer_id == form.manufacturer_id
    );
  }
});

const resetForm = () => {
  if (confirm('Are you sure you want to reset all changes? This will restore the original values.')) {
    // Reset form to original aircraft data
    form.registration_number = props.aircraft.registration_number || '';
    form.manufacturer_id = props.aircraft.manufacturer_id || '';
    form.model_id = props.aircraft.model_id || '';
    form.serial_number = props.aircraft.serial_number || '';
    form.year_of_manufacture = props.aircraft.year_of_manufacture || new Date().getFullYear();
    form.total_airframe_hours = props.aircraft.total_airframe_hours || '';
    form.total_cycles = props.aircraft.total_cycles || '';
    form.status_id = props.aircraft.status_id || '';
    form.home_base = props.aircraft.home_base || '';
    form.seating_configuration = props.aircraft.seating_configuration || '';
    form.total_capacity = props.aircraft.total_capacity || '';
    form.max_passengers = props.aircraft.max_passengers || '';
    form.max_takeoff_weight = props.aircraft.max_takeoff_weight || '';
    form.max_landing_weight = props.aircraft.max_landing_weight || '';
    form.empty_weight = props.aircraft.empty_weight || '';
    form.useful_load = props.aircraft.useful_load || '';
    form.engine_type = props.aircraft.engine_type || '';
    form.number_of_engines = props.aircraft.number_of_engines || '';
    form.fuel_capacity = props.aircraft.fuel_capacity || '';
    form.service_ceiling = props.aircraft.service_ceiling || '';
    form.maximum_range = props.aircraft.maximum_range || '';
    form.cruise_speed = props.aircraft.cruise_speed || '';
    form.aircraft_category = props.aircraft.aircraft_category || '';
    form.certification_basis = props.aircraft.certification_basis || '';
    form.ifr_certified = props.aircraft.ifr_certified || false;
    form.rvsm_approved = props.aircraft.rvsm_approved || false;
    form.etops_rating = props.aircraft.etops_rating || '';
    form.certificate_of_airworthiness = props.aircraft.certificate_of_airworthiness || '';
    form.coa_issue_date = props.aircraft.coa_issue_date || '';
    form.coa_expiry_date = props.aircraft.coa_expiry_date || '';
    form.avionics_suite = props.aircraft.avionics_suite || '';
    form.propeller_type = props.aircraft.propeller_type || '';
    form.noise_certification = props.aircraft.noise_certification || '';
    form.notes = props.aircraft.notes || '';
    form.photos = [...(props.aircraft.photos || [])];

    // Reset photo state
    existingPhotos.value = [...(props.aircraft.photos || [])];
    newPhotos.value = [];

    // Reset available models based on manufacturer
    onManufacturerChange();
  }
};
</script>

<style scoped>
.required::after {
  content: ' *';
  color: #dc3545;
}

.upload-area {
  transition: all 0.3s ease;
}

.upload-label:hover {
  background-color: #f8f9fa;
  border-color: #007bff !important;
}

.upload-label:hover .text-muted {
  color: #007bff !important;
}

.photo-preview {
  border-radius: 6px;
  overflow: hidden;
}

.cursor-pointer {
  cursor: pointer;
}

.breadcrumb-item a {
  color: #007bff;
}

.breadcrumb-item.active {
  color: #6c757d;
}

.card {
  transition: all 0.2s ease;
}

.card:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.form-control:focus,
.form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

.input-group-text {
  background-color: #f8f9fa;
  border-color: #dee2e6;
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