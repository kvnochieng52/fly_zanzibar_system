<template>
  <Head title="Edit Flight Route" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Edit Flight Route</h1>
        <p class="mb-0 text-muted">
          Update flight route: {{ route.name }}
        </p>
      </div>
      <div class="btn-group">
        <Link
          :href="route('flight-routes.show', route.id)"
          class="btn btn-outline-info"
        >
          <i class="fas fa-eye me-2"></i>View Route
        </Link>
        <Link
          :href="route('flight-routes.index')"
          class="btn btn-outline-secondary"
        >
          <i class="fas fa-arrow-left me-2"></i>Back to Routes
        </Link>
      </div>
    </div>

    <!-- Edit Form -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
          <i class="fas fa-edit me-2 text-warning"></i>Route Information
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-4">
            <!-- Route Name -->
            <div class="col-md-6">
              <label class="form-label required">Route Name</label>
              <input
                v-model="form.name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                placeholder="e.g., Zanzibar to Dar es Salaam"
                maxlength="100"
              />
              <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name }}
              </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="form-check form-switch">
                <input
                  v-model="form.is_active"
                  class="form-check-input"
                  type="checkbox"
                  role="switch"
                  id="is_active"
                />
                <label class="form-check-label" for="is_active">
                  <span v-if="form.is_active" class="badge bg-success">Active</span>
                  <span v-else class="badge bg-secondary">Inactive</span>
                </label>
              </div>
            </div>

            <!-- Origin Airport -->
            <div class="col-md-6">
              <label class="form-label required">Origin Airport</label>
              <select
                v-model="form.origin_airport_id"
                class="form-select"
                :class="{ 'is-invalid': errors.origin_airport_id }"
                @change="updateRoutePreview"
              >
                <option value="">Select origin airport</option>
                <option
                  v-for="airport in airports"
                  :key="airport.id"
                  :value="airport.id"
                >
                  {{ airport.code }} - {{ airport.name }} ({{ airport.city }})
                </option>
              </select>
              <div v-if="errors.origin_airport_id" class="invalid-feedback">
                {{ errors.origin_airport_id }}
              </div>
            </div>

            <!-- Destination Airport -->
            <div class="col-md-6">
              <label class="form-label required">Destination Airport</label>
              <select
                v-model="form.destination_airport_id"
                class="form-select"
                :class="{ 'is-invalid': errors.destination_airport_id }"
                @change="updateRoutePreview"
              >
                <option value="">Select destination airport</option>
                <option
                  v-for="airport in availableDestinations"
                  :key="airport.id"
                  :value="airport.id"
                >
                  {{ airport.code }} - {{ airport.name }} ({{ airport.city }})
                </option>
              </select>
              <div v-if="errors.destination_airport_id" class="invalid-feedback">
                {{ errors.destination_airport_id }}
              </div>
            </div>

            <!-- Route Preview -->
            <div v-if="routePreview" class="col-md-6">
              <label class="form-label">Route Preview</label>
              <div class="form-control-plaintext">
                <span class="badge bg-info fs-6">{{ routePreview }}</span>
              </div>
            </div>

            <!-- Route Code -->
            <div class="col-md-6">
              <label class="form-label">Route Code</label>
              <div class="form-control-plaintext">
                <span class="badge bg-secondary">{{ route.code }}</span>
                <small class="text-muted ms-2">Auto-generated</small>
              </div>
            </div>

            <!-- Distance -->
            <div class="col-md-4">
              <label class="form-label">Distance (km)</label>
              <input
                v-model="form.distance_km"
                type="number"
                class="form-control"
                :class="{ 'is-invalid': errors.distance_km }"
                placeholder="0"
                min="0"
                step="0.01"
              />
              <div v-if="errors.distance_km" class="invalid-feedback">
                {{ errors.distance_km }}
              </div>
            </div>

            <!-- Duration -->
            <div class="col-md-4">
              <label class="form-label">Duration (minutes)</label>
              <input
                v-model="form.duration_minutes"
                type="number"
                class="form-control"
                :class="{ 'is-invalid': errors.duration_minutes }"
                placeholder="0"
                min="1"
              />
              <div v-if="errors.duration_minutes" class="invalid-feedback">
                {{ errors.duration_minutes }}
              </div>
            </div>

            <!-- Base Price -->
            <div class="col-md-4">
              <label class="form-label">Base Price ($)</label>
              <input
                v-model="form.base_price"
                type="number"
                class="form-control"
                :class="{ 'is-invalid': errors.base_price }"
                placeholder="0.00"
                min="0"
                step="0.01"
              />
              <div v-if="errors.base_price" class="invalid-feedback">
                {{ errors.base_price }}
              </div>
            </div>

            <!-- Description -->
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea
                v-model="form.description"
                class="form-control"
                :class="{ 'is-invalid': errors.description }"
                rows="3"
                placeholder="Optional description for this route..."
                maxlength="1000"
              ></textarea>
              <div class="form-text">
                {{ form.description ? form.description.length : 0 }}/1000 characters
              </div>
              <div v-if="errors.description" class="invalid-feedback">
                {{ errors.description }}
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="row mt-4">
            <div class="col-12">
              <div class="d-flex justify-content-end gap-2">
                <Link
                  :href="route('flight-routes.index')"
                  class="btn btn-secondary"
                  :disabled="processing"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="btn btn-warning"
                  :disabled="processing"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-1"></i>
                    Updating...
                  </span>
                  <span v-else>
                    <i class="fas fa-save me-1"></i>
                    Update Route
                  </span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Route Usage Warning -->
    <div v-if="route.scheduled_flights && route.scheduled_flights.length > 0" class="card border-warning mt-4">
      <div class="card-header bg-warning bg-opacity-10 border-warning">
        <h6 class="mb-0 text-warning">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Route Usage Warning
        </h6>
      </div>
      <div class="card-body">
        <p class="mb-2">
          This route is currently used by {{ route.scheduled_flights.length }} scheduled flight(s).
          Changes to the airports will affect existing flights.
        </p>
        <small class="text-muted">
          Consider carefully before modifying the origin or destination airports.
        </small>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'FlightRouteEdit',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    route: Object,
    airports: Array,
    errors: Object,
  },
  setup(props) {
    const form = useForm({
      name: props.route.name,
      origin_airport_id: props.route.origin_airport_id,
      destination_airport_id: props.route.destination_airport_id,
      distance_km: props.route.distance_km,
      duration_minutes: props.route.duration_minutes,
      base_price: props.route.base_price,
      description: props.route.description,
      is_active: props.route.is_active,
    })

    const submit = () => {
      form.put(route('flight-routes.update', props.route.id))
    }

    return {
      form,
      submit,
    }
  },
  data() {
    return {
      routePreview: '',
    }
  },
  computed: {
    processing() {
      return this.form.processing
    },
    availableDestinations() {
      return this.airports.filter(
        airport => airport.id != this.form.origin_airport_id
      )
    },
  },
  mounted() {
    this.updateRoutePreview()
  },
  methods: {
    updateRoutePreview() {
      if (this.form.origin_airport_id && this.form.destination_airport_id) {
        const origin = this.airports.find(a => a.id == this.form.origin_airport_id)
        const destination = this.airports.find(a => a.id == this.form.destination_airport_id)

        if (origin && destination) {
          this.routePreview = `${origin.code} → ${destination.code}`
        }
      } else {
        this.routePreview = ''
      }
    }
  }
}
</script>

<style scoped>
.required::after {
  content: " *";
  color: #dc3545;
}
</style>