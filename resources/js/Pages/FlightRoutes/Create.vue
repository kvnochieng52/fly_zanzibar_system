<template>
  <Head title="Create Flight Route" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Create Flight Route</h1>
        <p class="mb-0 text-muted">
          Create a new reusable flight route between airports
        </p>
      </div>
      <Link
        :href="route('flight-routes.index')"
        class="btn btn-outline-secondary"
      >
        <i class="fas fa-arrow-left me-2"></i>Back to Routes
      </Link>
    </div>

    <!-- Create Form -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
          <i class="fas fa-route me-2 text-primary"></i>Route Information
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
                  class="btn btn-primary"
                  :disabled="processing"
                >
                  <span v-if="processing">
                    <i class="fas fa-spinner fa-spin me-1"></i>
                    Creating...
                  </span>
                  <span v-else>
                    <i class="fas fa-save me-1"></i>
                    Create Route
                  </span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'FlightRouteCreate',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    airports: Array,
    errors: Object,
  },
  setup(props) {
    const form = useForm({
      name: '',
      origin_airport_id: '',
      destination_airport_id: '',
      distance_km: '',
      duration_minutes: '',
      base_price: '',
      description: '',
    })

    const submit = () => {
      form.post(route('flight-routes.store'))
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