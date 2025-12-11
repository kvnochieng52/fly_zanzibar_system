<template>
  <Head title="Add New Aircraft" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="mb-4">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item">
            <Link :href="route('aircraft.index')" class="text-decoration-none">Aircraft</Link>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Add New Aircraft</li>
        </ol>
      </nav>
      <h1 class="h3 mb-1 text-gray-900">Add New Aircraft</h1>
      <p class="mb-0 text-muted">
        Enter aircraft details and specifications
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
                    required
                    @change="onManufacturerChange"
                  >
                    <option value="">Select Manufacturer</option>
                    <option
                      v-for="manufacturer in manufacturers"
                      :key="manufacturer.id"
                      :value="manufacturer.id"
                    >
                      {{ manufacturer.name }}
                    </option>
                  </select>
                  <div v-if="errors.manufacturer_id" class="invalid-feedback">
                    {{ errors.manufacturer_id }}
                  </div>
                </div>

                <!-- Model/Type -->
                <div class="col-md-6 mb-3">
                  <label for="modelType" class="form-label required">Model</label>
                  <select
                    v-model="form.model_id"
                    id="modelType"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.model_id }"
                    :disabled="!form.manufacturer_id || availableModels.length === 0"
                    required
                  >
                    <option value="">Select Model</option>
                    <option
                      v-for="model in availableModels"
                      :key="model.id"
                      :value="model.id"
                    >
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
                    <option
                      v-for="status in statuses"
                      :key="status.id"
                      :value="status.id"
                    >
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
        <!-- Photo Upload Card -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="mb-0 fw-bold">
              <i class="fas fa-images text-info me-2"></i>Aircraft Photos
            </h5>
          </div>
          <div class="card-body">
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
                  <strong>Click to upload photos</strong>
                  <br>
                  <small>Drag and drop or click to browse</small>
                </div>
              </label>
            </div>

            <!-- Photo Preview -->
            <div v-if="uploadedPhotos.length > 0" class="uploaded-photos">
              <h6 class="small fw-semibold text-muted mb-2">Uploaded Photos ({{ uploadedPhotos.length }})</h6>
              <div class="row g-2">
                <div
                  v-for="(photo, index) in uploadedPhotos"
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
                      @click="removePhoto(index)"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Info Card -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="mb-0 fw-bold">
              <i class="fas fa-info-circle text-warning me-2"></i>Quick Tips
            </h5>
          </div>
          <div class="card-body">
            <div class="small text-muted">
              <div class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                <strong>Registration Number:</strong> Must be unique and follow ICAO format
              </div>
              <div class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                <strong>Serial Number:</strong> Manufacturer's unique identifier
              </div>
              <div class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                <strong>Photos:</strong> Help identify aircraft quickly. Max 5MB per image
              </div>
              <div>
                <i class="fas fa-check text-success me-2"></i>
                <strong>Documents:</strong> Add compliance documents after creating the aircraft
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
            :href="route('aircraft.index')"
            class="btn btn-outline-secondary"
          >
            <i class="fas fa-arrow-left me-2"></i>Back to Aircraft List
          </Link>
          <div class="d-flex gap-2">
            <button
              type="button"
              class="btn btn-outline-primary"
              @click="resetForm"
            >
              <i class="fas fa-undo me-2"></i>Reset Form
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="isSubmitting"
              @click="submitForm"
            >
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="fas fa-plus me-2"></i>
              {{ isSubmitting ? 'Creating...' : 'Create Aircraft' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const props = defineProps({
  manufacturers: Array,
  models: Array,
  homeBases: Array,
  statuses: Array,
  errors: Object,
});

// Form data
const form = useForm({
  registration_number: '',
  manufacturer_id: '',
  model_id: '',
  serial_number: '',
  year_of_manufacture: new Date().getFullYear(),
  total_airframe_hours: '',
  total_cycles: '',
  status_id: '',
  home_base: '',
  seating_configuration: '',
  total_capacity: '',
  max_passengers: '',
  max_takeoff_weight: '',
  max_landing_weight: '',
  empty_weight: '',
  useful_load: '',
  engine_type: '',
  number_of_engines: '',
  fuel_capacity: '',
  service_ceiling: '',
  maximum_range: '',
  cruise_speed: '',
  aircraft_category: '',
  certification_basis: '',
  ifr_certified: false,
  rvsm_approved: false,
  etops_rating: '',
  certificate_of_airworthiness: '',
  coa_issue_date: '',
  coa_expiry_date: '',
  avionics_suite: '',
  propeller_type: '',
  noise_certification: '',
  photos: [],
  notes: ''
});

// State
const isSubmitting = ref(false);
const uploadedPhotos = ref([]);
const availableModels = ref([]);

// Methods
const onManufacturerChange = async () => {
  // Clear model selection
  form.model_id = '';

  // Filter models by selected manufacturer
  if (form.manufacturer_id) {
    try {
      const response = await fetch(route('aircraft.models-by-manufacturer') + '?manufacturer_id=' + form.manufacturer_id, {
        method: 'GET',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        }
      });

      const data = await response.json();
      availableModels.value = data;
    } catch (error) {
      console.error('Error fetching models:', error);
      // Fallback to client-side filtering
      availableModels.value = props.models.filter(
        model => model.manufacturer_id == form.manufacturer_id
      );
    }
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
        uploadedPhotos.value.push({
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

const removePhoto = (index) => {
  uploadedPhotos.value.splice(index, 1);
  form.photos.splice(index, 1);
};

const submitForm = () => {
  isSubmitting.value = true;

  form.post(route('aircraft.store'), {
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

const resetForm = () => {
  if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
    form.reset();
    uploadedPhotos.value = [];

    // Reset year to current year
    form.year_of_manufacture = new Date().getFullYear();
    form.current_status = 'In Service';
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

.card {
  border-radius: 12px;
}

.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
}
</style>