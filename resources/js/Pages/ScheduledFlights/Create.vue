<template>
  <Head title="Schedule New Flight" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Schedule New Flight</h1>
        <p class="mb-0 text-muted">
          Create a new scheduled flight with multiple routes
        </p>
      </div>
      <Link
        :href="route('scheduled-flights.index')"
        class="btn btn-outline-secondary"
      >
        <i class="fas fa-arrow-left me-2"></i>Back to Flights
      </Link>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <form @submit.prevent="submit">
          <!-- Flight Basic Information -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fas fa-plane me-2"></i>Flight Details
              </h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <!-- First row: Aircraft, Flight Status, Flight Type -->
                <div class="col-md-4">
                  <label class="form-label required">Aircraft</label>
                  <select
                    v-model="form.aircraft_id"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.aircraft_id }"
                  >
                    <option value="">Select Aircraft</option>
                    <option
                      v-for="aircraft in aircraftList"
                      :key="aircraft.id"
                      :value="aircraft.id"
                    >
                      {{ aircraft.registration_number }}
                      ({{ aircraft.manufacturer?.name }} {{ aircraft.model?.name }})
                    </option>
                  </select>
                  <div v-if="errors.aircraft_id" class="invalid-feedback">
                    {{ errors.aircraft_id }}
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label required">Flight Status</label>
                  <select
                    v-model="form.flight_status_id"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.flight_status_id }"
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
                  <div v-if="errors.flight_status_id" class="invalid-feedback">
                    {{ errors.flight_status_id }}
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label required">Flight Type</label>
                  <select
                    v-model="form.flight_type"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.flight_type }"
                  >
                    <option value="">Select Flight Type</option>
                    <option value="passenger">Passenger</option>
                    <option value="cargo">Cargo</option>
                    <option value="mixed">Mixed (Passengers & Cargo)</option>
                  </select>
                  <div v-if="errors.flight_type" class="invalid-feedback">
                    {{ errors.flight_type }}
                  </div>
                </div>

                <!-- Second row: Primary Customer (full width) -->
                <div class="col-12">
                  <label class="form-label">Primary Customer</label>
                  <select
                    v-model="form.primary_customer_id"
                    class="form-select custom-select"
                    :class="{ 'is-invalid': errors.primary_customer_id }"
                  >
                    <option value="">Select Primary Customer (Optional)</option>
                    <option
                      v-for="customer in customersList"
                      :key="customer.id"
                      :value="customer.id"
                    >
                      {{ getCustomerDisplayName(customer) }} - {{ customer.email }}
                    </option>
                  </select>
                  <div v-if="errors.primary_customer_id" class="invalid-feedback">
                    {{ errors.primary_customer_id }}
                  </div>
                </div>

                <!-- Flight Date/Time -->
                <div class="col-md-6">
                  <label class="form-label required">Flight Departure Date/Time</label>
                  <input
                    v-model="form.total_departure_time"
                    type="datetime-local"
                    class="form-control"
                    :class="{ 'is-invalid': errors.total_departure_time }"
                  />
                  <div v-if="errors.total_departure_time" class="invalid-feedback">
                    {{ errors.total_departure_time }}
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Flight Arrival Date/Time (Optional)</label>
                  <input
                    v-model="form.total_arrival_time"
                    type="datetime-local"
                    class="form-control"
                    :class="{ 'is-invalid': errors.total_arrival_time }"
                  />
                  <div v-if="errors.total_arrival_time" class="invalid-feedback">
                    {{ errors.total_arrival_time }}
                  </div>
                  <small class="form-text text-muted">Leave empty to auto-calculate from routes</small>
                </div>

                <!-- Notes -->
                <div class="col-12">
                  <label class="form-label">Flight Notes</label>
                  <textarea
                    v-model="form.notes"
                    class="form-control"
                    :class="{ 'is-invalid': errors.notes }"
                    rows="2"
                    placeholder="Optional notes about this flight..."
                  ></textarea>
                  <div v-if="errors.notes" class="invalid-feedback">
                    {{ errors.notes }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Flight Route Calculator -->
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 d-flex align-items-center">
              <div class="d-flex align-items-center">
                <div class="bg-success rounded p-2 me-3">
                  <i class="fas fa-route text-white"></i>
                </div>
                <h5 class="mb-0 fw-bold">Route Calculator</h5>
              </div>
            </div>
            <div class="card-body">
              <!-- Locations Header -->
              <div class="d-flex align-items-center mb-3">
                <div class="bg-light rounded p-2 me-2">
                  <i class="fas fa-map-marker-alt text-success"></i>
                </div>
                <h6 class="mb-0 text-muted">Locations ({{ form.routes.length }})</h6>
              </div>

              <!-- Location Cards -->
              <div class="location-cards">
                <div
                  v-for="(route, index) in form.routes"
                  :key="index"
                  class="location-card mb-3"
                >
                  <!-- Origin Location -->
                  <div v-if="index === 0 || !isConnectedToPrevious(index)" class="location-item bg-light rounded p-3 mb-3 position-relative">
                    <div class="d-flex align-items-center">
                      <div class="location-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        {{ getLocationNumber(index, 'origin') }}
                      </div>
                      <div class="flex-grow-1">
                        <div class="location-name fw-bold">{{ getAirportName(route.origin_airport_id) }}</div>
                        <div class="location-code text-muted small">{{ getAirportCode(route.origin_airport_id) }}</div>
                        <select
                          v-model="route.origin_airport_id"
                          class="form-select custom-select mt-2"
                          :class="{ 'is-invalid': getRouteError(index, 'origin_airport_id') }"
                          @change="updateRouteConnections"
                        >
                          <option value="">Select Origin Airport</option>
                          <option
                            v-for="airport in getAvailableOriginAirports(index)"
                            :key="airport.id"
                            :value="airport.id"
                          >
                            {{ airport.iata_code || airport.icao_code || 'N/A' }} - {{ airport.name }} ({{ airport.city }})
                          </option>
                        </select>
                        <div v-if="getRouteError(index, 'origin_airport_id')" class="invalid-feedback">
                          {{ getRouteError(index, 'origin_airport_id') }}
                        </div>
                      </div>
                      <div class="location-icon ms-2">
                        <i class="fas fa-plane text-success"></i>
                      </div>
                    </div>
                  </div>

                  <!-- Connection Arrow -->
                  <div v-if="index === 0 || !isConnectedToPrevious(index)" class="route-arrow text-center mb-3">
                    <i class="fas fa-chevron-down text-success fs-4"></i>
                  </div>

                  <!-- Destination Location -->
                  <div class="location-item bg-light rounded p-3 position-relative">
                    <div class="d-flex align-items-center">
                      <div class="location-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                        {{ getLocationNumber(index, 'destination') }}
                      </div>
                      <div class="flex-grow-1">
                        <div class="location-name fw-bold">{{ getAirportName(route.destination_airport_id) }}</div>
                        <div class="location-code text-muted small">{{ getAirportCode(route.destination_airport_id) }}</div>
                        <select
                          v-model="route.destination_airport_id"
                          class="form-select custom-select mt-2"
                          :class="{ 'is-invalid': getRouteError(index, 'destination_airport_id') }"
                          @change="updateRouteConnections"
                        >
                          <option value="">Select Destination Airport</option>
                          <option
                            v-for="airport in getAvailableDestinationAirports(index)"
                            :key="airport.id"
                            :value="airport.id"
                          >
                            {{ airport.iata_code || airport.icao_code || 'N/A' }} - {{ airport.name }} ({{ airport.city }})
                          </option>
                        </select>
                        <div v-if="getRouteError(index, 'destination_airport_id')" class="invalid-feedback">
                          {{ getRouteError(index, 'destination_airport_id') }}
                        </div>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="location-icon ms-2 me-3">
                          <i class="fas fa-plane text-success"></i>
                        </div>
                        <button
                          v-if="form.routes.length > 1"
                          type="button"
                          class="btn btn-sm btn-outline-danger rounded-circle p-1"
                          style="width: 30px; height: 30px;"
                          @click="removeRoute(index)"
                          title="Remove location"
                        >
                          <i class="fas fa-times"></i>
                        </button>
                      </div>
                    </div>

                    <!-- Route Notes -->
                    <div v-if="route.notes || showRouteNotes === index" class="mt-3">
                      <textarea
                        v-model="route.notes"
                        class="form-control"
                        :class="{ 'is-invalid': getRouteError(index, 'notes') }"
                        rows="2"
                        placeholder="Optional notes for this route segment..."
                        @blur="() => { if (!route.notes) showRouteNotes = null }"
                      ></textarea>
                      <div v-if="getRouteError(index, 'notes')" class="invalid-feedback">
                        {{ getRouteError(index, 'notes') }}
                      </div>
                    </div>
                    <div v-else class="mt-2">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        @click="showRouteNotes = index"
                      >
                        <i class="fas fa-sticky-note me-1"></i>Add Notes
                      </button>
                    </div>
                  </div>

                  <!-- Connecting Arrow to Next Route -->
                  <div v-if="index < form.routes.length - 1" class="route-arrow text-center mb-3">
                    <div class="route-connector">
                      <i class="fas fa-chevron-down text-success fs-4"></i>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Add Location Button -->
              <div class="text-center mb-4">
                <button
                  type="button"
                  class="btn btn-outline-success rounded-pill px-4"
                  @click="addRoute"
                >
                  <i class="fas fa-plus me-2"></i>Add Location
                </button>
              </div>

              <!-- Route Results Section -->
              <div v-if="hasValidRoute" class="route-results mt-4">
                <div class="d-flex align-items-center mb-3">
                  <div class="bg-light rounded p-2 me-2">
                    <i class="fas fa-chart-line text-success"></i>
                  </div>
                  <h6 class="mb-0 text-muted">Route Results</h6>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary ms-auto"
                    @click="toggleRouteResults"
                  >
                    <i :class="showRouteResults ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                  </button>
                </div>

                <div v-show="showRouteResults" class="route-summary bg-light rounded p-4">
                  <div class="text-center mb-3">
                    <h6 class="text-muted mb-2">Total Route</h6>
                    <div class="route-overview h4 mb-3 text-success">
                      {{ flightSummary.originCode }} → {{ flightSummary.destinationCode }}
                    </div>
                    <div class="badge bg-warning text-dark" v-if="flightSummary.totalSegments > 1">
                      {{ flightSummary.totalSegments }} segments
                    </div>
                    <div class="badge bg-success" v-else>
                      Direct flight
                    </div>
                  </div>

                  <!-- Route Tips -->
                  <div class="route-tips">
                    <h6 class="mb-2">Flight Tips</h6>
                    <div class="small text-muted">
                      <div class="mb-1">
                        <i class="fas fa-lightbulb text-warning me-1"></i>
                        Flight code will be auto-generated (e.g., FZ101)
                      </div>
                      <div class="mb-1">
                        <i class="fas fa-route text-success me-1"></i>
                        Routes are automatically connected for multi-leg flights
                      </div>
                      <div>
                        <i class="fas fa-info-circle text-info me-1"></i>
                        Each location creates a segment in your flight plan
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="d-flex justify-content-end gap-2">
            <Link
              :href="route('scheduled-flights.index')"
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
                Scheduling...
              </span>
              <span v-else>
                <i class="fas fa-save me-1"></i>
                Schedule Flight
              </span>
            </button>
          </div>
        </form>
      </div>

      <!-- Flight Summary -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">
              <i class="fas fa-info-circle me-2"></i>Flight Summary
            </h5>
          </div>
          <div class="card-body">
            <div v-if="flightSummary.isValid" class="mb-3">
              <h6 class="mb-2">Route Overview</h6>
              <div class="d-flex align-items-center justify-content-center mb-2">
                <span class="fw-bold text-primary">{{ flightSummary.originCode }}</span>
                <i class="fas fa-arrow-right mx-2 text-muted"></i>
                <span class="fw-bold text-primary">{{ flightSummary.destinationCode }}</span>
              </div>
              <div class="small text-center">
                <span v-if="flightSummary.totalSegments > 1" class="badge bg-warning">
                  {{ flightSummary.totalSegments }} segments
                </span>
                <span v-else class="badge bg-success">Direct flight</span>
              </div>
            </div>


            <hr v-if="flightSummary.isValid">

            <div class="mb-3">
              <h6 class="mb-2">Flight Tips</h6>
              <div class="small text-muted">
                <div class="mb-1">
                  <i class="fas fa-lightbulb text-warning me-1"></i>
                  Flight code will be auto-generated (e.g., FZ101)
                </div>
                <div class="mb-1">
                  <i class="fas fa-route text-success me-1"></i>
                  Connect routes for multi-leg flights
                </div>
                <div>
                  <i class="fas fa-clock text-info me-1"></i>
                  Ensure arrival times are after departure times
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'ScheduledFlightCreate',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    aircraft: Array,
    airports: Array,
    statuses: Array,
    customers: Array,
    errors: Object,
  },
  data() {
    return {
      form: useForm({
        aircraft_id: '',
        flight_status_id: this.statuses.find(s => s.code === 'SCHEDULED')?.id || '',
        flight_type: 'passenger',
        primary_customer_id: '',
        total_departure_time: '',
        total_arrival_time: '',
        notes: '',
        routes: [
          {
            origin_airport_id: '',
            destination_airport_id: '',
            notes: '',
          }
        ],
      }),
      showRouteNotes: null,
      showRouteResults: true,
    }
  },
  computed: {
    aircraftList() {
      return this.aircraft || []
    },
    customersList() {
      return this.customers || []
    },
    processing() {
      return this.form.processing
    },
    flightSummary() {
      const routes = this.form.routes.filter(r => r.origin_airport_id && r.destination_airport_id)

      if (routes.length === 0) {
        return { isValid: false }
      }

      const firstRoute = routes[0]
      const lastRoute = routes[routes.length - 1]

      const originAirport = this.airports.find(a => a.id == parseInt(firstRoute.origin_airport_id))
      const destinationAirport = this.airports.find(a => a.id == parseInt(lastRoute.destination_airport_id))

      return {
        isValid: true,
        originCode: originAirport?.iata_code || originAirport?.icao_code || 'N/A',
        destinationCode: destinationAirport?.iata_code || destinationAirport?.icao_code || 'N/A',
        totalSegments: routes.length,
      }
    },
    hasValidRoute() {
      return this.form.routes.some(r => r.origin_airport_id && r.destination_airport_id)
    },
  },
  methods: {
    addRoute() {
      // Auto-connect routes: next origin should be previous destination
      const lastRoute = this.form.routes[this.form.routes.length - 1]
      const newRoute = {
        origin_airport_id: lastRoute.destination_airport_id || '',
        destination_airport_id: '',
        notes: '',
      }
      this.form.routes.push(newRoute)
    },
    removeRoute(index) {
      if (this.form.routes.length > 1) {
        this.form.routes.splice(index, 1)
        this.updateRouteConnections()
      }
    },
    updateRouteConnections() {
      // Auto-update connections between route segments
      for (let i = 1; i < this.form.routes.length; i++) {
        const prevRoute = this.form.routes[i - 1]
        if (prevRoute.destination_airport_id && !this.form.routes[i].origin_airport_id) {
          this.form.routes[i].origin_airport_id = prevRoute.destination_airport_id
        }
      }
    },
    getAvailableOriginAirports(index) {
      if (index === 0) {
        return this.airports
      }

      // For subsequent routes, origin should be connected to previous route's destination
      const prevRoute = this.form.routes[index - 1]
      if (prevRoute && prevRoute.destination_airport_id) {
        return this.airports.filter(airport => airport.id == prevRoute.destination_airport_id)
      }

      return this.airports
    },
    getAvailableDestinationAirports(index) {
      const currentRoute = this.form.routes[index]
      return this.airports.filter(airport =>
        airport.id != currentRoute.origin_airport_id
      )
    },
    getRouteError(index, field) {
      const key = `routes.${index}.${field}`
      return this.errors[key] || null
    },
    getRoutePreview(route) {
      if (!route.origin_airport_id || !route.destination_airport_id) return null

      const origin = this.airports.find(a => a.id == route.origin_airport_id)
      const destination = this.airports.find(a => a.id == route.destination_airport_id)

      if (!origin || !destination) return null

      return `${origin.iata_code || origin.icao_code || 'N/A'} (${origin.city}) → ${destination.iata_code || destination.icao_code || 'N/A'} (${destination.city})`
    },
    getCustomerDisplayName(customer) {
      if (customer.company_name) {
        return customer.company_name
      }
      return `${customer.first_name || ''} ${customer.last_name || ''}`.trim() || 'N/A'
    },
    getAirportName(airportId) {
      if (!airportId) return 'Select Location'
      const airport = this.airports.find(a => a.id == airportId)
      return airport ? airport.name : 'Unknown Location'
    },
    getAirportCode(airportId) {
      if (!airportId) return ''
      const airport = this.airports.find(a => a.id == airportId)
      return airport ? (airport.iata_code || airport.icao_code || airport.id) : ''
    },
    getLocationNumber(routeIndex, type) {
      // Calculate the sequential location number
      let number = 1

      for (let i = 0; i < routeIndex; i++) {
        number += 2 // Each route has origin and destination
      }

      if (type === 'destination') {
        number += 1
      }

      return number
    },
    isConnectedToPrevious(index) {
      if (index === 0) return false

      const currentRoute = this.form.routes[index]
      const prevRoute = this.form.routes[index - 1]

      return currentRoute.origin_airport_id === prevRoute.destination_airport_id
    },
    toggleRouteResults() {
      this.showRouteResults = !this.showRouteResults
    },
    submit() {
      this.form.post(route('scheduled-flights.store'))
    }
  }
}
</script>

<style scoped>
.required::after {
  content: " *";
  color: #dc3545;
}

/* Location Cards Styling */
.location-cards {
  max-width: 100%;
}

.location-item {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  transition: all 0.3s ease;
}

.location-item:hover {
  background-color: #e9ecef;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.location-number {
  width: 40px;
  height: 40px;
  font-weight: bold;
  font-size: 16px;
  flex-shrink: 0;
}

.location-name {
  font-size: 16px;
  color: #2c3e50;
  margin-bottom: 2px;
}

.location-code {
  font-size: 12px;
  color: #6c757d;
  margin-bottom: 8px;
}

/* Route Arrows */
.route-arrow {
  position: relative;
  z-index: 1;
}

.route-connector {
  position: relative;
}

.route-connector::before {
  content: '';
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 2px;
  height: 20px;
  background: linear-gradient(to bottom, transparent, #28a745);
  z-index: -1;
}

/* Route Results Section */
.route-results {
  border: 1px solid #e9ecef;
  border-radius: 0.375rem;
  background: #ffffff;
}

.route-summary {
  border: 1px solid #e3f2fd;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.route-overview {
  font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* Button Styling */
.btn-outline-success.rounded-pill {
  border-width: 2px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-outline-success.rounded-pill:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

/* Card Header Styling */
.card-header.bg-transparent {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
  border-bottom: 1px solid #e9ecef;
}

/* Animation for add/remove */
.location-card {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Remove button styling */
.btn-outline-danger.rounded-circle {
  transition: all 0.2s ease;
}

.btn-outline-danger.rounded-circle:hover {
  transform: scale(1.1);
}

/* Form select improvements */
.location-item .form-select {
  border: 1px solid #ced4da;
  background-color: white;
  transition: all 0.2s ease;
}

.location-item .form-select:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

/* Tips styling */
.route-tips {
  background: rgba(255, 255, 255, 0.8);
  border-radius: 0.375rem;
  padding: 1rem;
  border: 1px solid #e9ecef;
}

/* Custom select styling (matching staff forms) */
.custom-select {
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
}

.custom-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control:focus,
.form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>