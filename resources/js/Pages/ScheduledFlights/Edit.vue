<template>
  <Head :title="`Edit Flight ${flight.flight_code}`" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Edit Flight {{ flight.flight_code }}</h1>
        <p class="mb-0 text-muted">
          Update flight details and scheduling information
        </p>
      </div>
      <div class="d-flex gap-2">
        <Link
          :href="route('scheduled-flights.show', flight.id)"
          class="btn btn-outline-info"
        >
          <i class="fas fa-eye me-2"></i>View Flight
        </Link>
        <Link
          :href="route('scheduled-flights.index')"
          class="btn btn-outline-secondary"
        >
          <i class="fas fa-arrow-left me-2"></i>Back to Flights
        </Link>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
              <i class="fas fa-edit me-2"></i>Flight Details - {{ flight.flight_code }}
            </h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="submit">
              <!-- Basic Flight Information -->
              <div class="row g-3 mb-4">
                <!-- First row: Aircraft, Flight Status, Flight Type -->
                <div class="col-md-4">
                  <label class="form-label">Aircraft <span class="text-danger">*</span></label>
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
                  <label class="form-label">Flight Status <span class="text-danger">*</span></label>
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
                  <label class="form-label">Flight Type <span class="text-danger">*</span></label>
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
                    <option v-for="customer in customersList" :key="customer.id" :value="customer.id">
                      {{ getCustomerDisplayName(customer) }} - {{ customer.email }}
                    </option>
                  </select>
                  <div v-if="errors.primary_customer_id" class="invalid-feedback">
                    {{ errors.primary_customer_id }}
                  </div>
                </div>

                <!-- Third row: Date/Time fields -->
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

                <!-- Notes section -->
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

              <!-- Route Section -->

              <!-- Flight Route Calculator -->
              <div class="border rounded p-4 mb-4">
                <div class="d-flex align-items-center mb-4">
                  <div class="bg-success rounded p-2 me-3">
                    <i class="fas fa-route text-white"></i>
                  </div>
                  <h6 class="mb-0 fw-bold">Route Calculator</h6>
                </div>

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
                            :class="{ 'is-invalid': errors[`routes.${index}.origin_airport_id`] }"
                            @change="onRouteChange(index)"
                          >
                            <option value="">Select Origin Airport</option>
                            <option
                              v-for="airport in airports"
                              :key="airport.id"
                              :value="airport.id"
                            >
                              {{ getAirportCode(airport.id) }} - {{ airport.name }} ({{ airport.city }})
                            </option>
                          </select>
                          <div v-if="errors[`routes.${index}.origin_airport_id`]" class="invalid-feedback">
                            {{ errors[`routes.${index}.origin_airport_id`] }}
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
                            :class="{ 'is-invalid': errors[`routes.${index}.destination_airport_id`] }"
                            @change="onRouteChange(index)"
                          >
                            <option value="">Select Destination Airport</option>
                            <option
                              v-for="airport in airports"
                              :key="airport.id"
                              :value="airport.id"
                              :disabled="airport.id == route.origin_airport_id"
                            >
                              {{ getAirportCode(airport.id) }} - {{ airport.name }} ({{ airport.city }})
                            </option>
                          </select>
                          <div v-if="errors[`routes.${index}.destination_airport_id`]" class="invalid-feedback">
                            {{ errors[`routes.${index}.destination_airport_id`] }}
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

                      <!-- Actual Times (Edit-specific) -->
                      <div class="row mt-3">
                        <div class="col-md-6">
                          <label class="form-label small">Actual Departure</label>
                          <input
                            v-model="route.actual_departure_time"
                            type="datetime-local"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`routes.${index}.actual_departure_time`] }"
                          />
                          <small class="form-text text-muted">Leave empty if not yet departed</small>
                          <div v-if="errors[`routes.${index}.actual_departure_time`]" class="invalid-feedback">
                            {{ errors[`routes.${index}.actual_departure_time`] }}
                          </div>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label small">Actual Arrival</label>
                          <input
                            v-model="route.actual_arrival_time"
                            type="datetime-local"
                            class="form-control form-control-sm"
                            :class="{ 'is-invalid': errors[`routes.${index}.actual_arrival_time`] }"
                          />
                          <small class="form-text text-muted">Leave empty if not yet arrived</small>
                          <div v-if="errors[`routes.${index}.actual_arrival_time`]" class="invalid-feedback">
                            {{ errors[`routes.${index}.actual_arrival_time`] }}
                          </div>
                        </div>
                      </div>

                      <!-- Route Notes -->
                      <div v-if="route.notes || showRouteNotes === index" class="mt-3">
                        <textarea
                          v-model="route.notes"
                          class="form-control"
                          :class="{ 'is-invalid': errors[`routes.${index}.notes`] }"
                          rows="2"
                          placeholder="Optional notes for this route segment..."
                          @blur="() => { if (!route.notes) showRouteNotes = null }"
                        ></textarea>
                        <div v-if="errors[`routes.${index}.notes`]" class="invalid-feedback">
                          {{ errors[`routes.${index}.notes`] }}
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
              </div>

              <!-- Flight Notes -->
              <div class="mb-4">
                <label class="form-label">Flight Notes</label>
                <textarea
                  v-model="form.notes"
                  class="form-control"
                  :class="{ 'is-invalid': errors.notes }"
                  rows="3"
                  placeholder="Overall flight notes (separate from individual segment notes)..."
                ></textarea>
                <div v-if="errors.notes" class="invalid-feedback">
                  {{ errors.notes }}
                </div>
              </div>

              <!-- Form Actions -->
              <div class="d-flex justify-content-end gap-2">
                <Link
                  :href="route('scheduled-flights.show', flight.id)"
                  class="btn btn-secondary"
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
                    Update Flight
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <!-- Flight Summary -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">
              <i class="fas fa-info-circle me-2"></i>Flight Summary
            </h5>
          </div>
          <div class="card-body">
            <dl class="row mb-0">
              <dt class="col-5">Flight Code:</dt>
              <dd class="col-7 fw-bold text-primary">{{ flight.flight_code }}</dd>

              <dt class="col-5">Route:</dt>
              <dd class="col-7">{{ getFlightRoute() }}</dd>

              <dt class="col-5">Total Duration:</dt>
              <dd class="col-7">{{ getTotalDuration() }}</dd>

              <dt class="col-5">Segments:</dt>
              <dd class="col-7">{{ form.routes.length }}</dd>

              <dt class="col-5">Created:</dt>
              <dd class="col-7">{{ formatDate(flight.created_at) }}</dd>

              <dt class="col-5">Last Updated:</dt>
              <dd class="col-7">{{ formatDate(flight.updated_at) }}</dd>
            </dl>
          </div>
        </div>

        <!-- Route Overview -->
        <div v-if="form.routes.length > 0" class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0">
              <i class="fas fa-map me-2"></i>Route Overview
            </h5>
          </div>
          <div class="card-body">
            <div v-if="form.routes.length === 1" class="text-center">
              <div class="d-flex align-items-center justify-content-center">
                <div class="text-center">
                  <strong>{{ getAirportCode(form.routes[0].origin_airport_id) }}</strong>
                  <br>
                  <small>{{ getAirportName(form.routes[0].origin_airport_id) }}</small>
                </div>
                <div class="mx-3">
                  <i class="fas fa-arrow-right text-primary fa-lg"></i>
                </div>
                <div class="text-center">
                  <strong>{{ getAirportCode(form.routes[0].destination_airport_id) }}</strong>
                  <br>
                  <small>{{ getAirportName(form.routes[0].destination_airport_id) }}</small>
                </div>
              </div>
            </div>
            <div v-else>
              <div class="route-overview">
                <div
                  v-for="(route, index) in form.routes"
                  :key="index"
                  class="route-step mb-2"
                >
                  <div class="d-flex align-items-center">
                    <span class="badge bg-primary rounded-circle me-2" style="width: 20px; height: 20px;">
                      {{ index + 1 }}
                    </span>
                    <span class="fw-bold">{{ getAirportCode(route.origin_airport_id) }}</span>
                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                    <span class="fw-bold">{{ getAirportCode(route.destination_airport_id) }}</span>
                  </div>
                  <div v-if="route.departure_time" class="ms-4 small text-muted">
                    {{ formatDateTime(route.departure_time) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tips -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
              <i class="fas fa-lightbulb me-2"></i>Editing Tips
            </h5>
          </div>
          <div class="card-body">
            <div class="small">
              <ul class="mb-0 ps-3">
                <li>Add or remove route segments as needed</li>
                <li>Subsequent routes auto-connect to previous destinations</li>
                <li>Update actual times when flight status changes</li>
                <li>Use segment notes for specific leg details</li>
                <li>Flight notes are for overall flight information</li>
              </ul>
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
  name: 'ScheduledFlightEdit',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    flight: Object,
    aircraft: Array,
    airports: Array,
    statuses: Array,
    customers: Array,
  },
  data() {
    return {
      form: useForm({
        aircraft_id: this.flight.aircraft_id,
        flight_status_id: this.flight.flight_status_id,
        primary_customer_id: this.flight.primary_customer_id || '',
        total_departure_time: this.formatDateTimeForInput(this.flight.total_departure_time),
        total_arrival_time: this.formatDateTimeForInput(this.flight.total_arrival_time),
        notes: this.flight.notes || '',
        routes: this.initializeRoutes(),
      }),
      showRouteNotes: null,
    }
  },
  computed: {
    aircraftList() {
      return this.aircraft || []
    },
    customersList() {
      return this.customers || []
    },
    errors() {
      return this.form.errors
    },
    processing() {
      return this.form.processing
    },
  },
  methods: {
    initializeRoutes() {
      // Convert existing flight route segments to form data
      if (this.flight.schedule_routes && this.flight.schedule_routes.length > 0) {
        return this.flight.schedule_routes.map(route => ({
          id: route.id, // Include ID for updates
          origin_airport_id: route.origin_airport_id,
          destination_airport_id: route.destination_airport_id,
          actual_departure_time: this.formatDateTimeForInput(route.actual_departure_time),
          actual_arrival_time: this.formatDateTimeForInput(route.actual_arrival_time),
          notes: route.notes || '',
        }))
      }

      // Fallback to single route if no schedule_routes data
      return [{
        origin_airport_id: '',
        destination_airport_id: '',
        actual_departure_time: '',
        actual_arrival_time: '',
        notes: '',
      }]
    },
    addRoute() {
      const lastRoute = this.form.routes[this.form.routes.length - 1]
      const newRoute = {
        origin_airport_id: lastRoute.destination_airport_id || '',
        destination_airport_id: '',
        actual_departure_time: '',
        actual_arrival_time: '',
        notes: '',
      }
      this.form.routes.push(newRoute)
    },
    removeRoute(index) {
      if (this.form.routes.length > 1) {
        this.form.routes.splice(index, 1)
        // Update subsequent routes to maintain connection
        this.updateRouteConnections()
      }
    },
    updateRouteConnections() {
      // Auto-connect subsequent routes after removal
      for (let i = 1; i < this.form.routes.length; i++) {
        const previousRoute = this.form.routes[i - 1]
        if (previousRoute.destination_airport_id && !this.form.routes[i].origin_airport_id) {
          this.form.routes[i].origin_airport_id = previousRoute.destination_airport_id
        }
      }
    },
    onRouteChange(index) {
      // Auto-connect next route if this route's destination changes
      if (index < this.form.routes.length - 1) {
        const currentRoute = this.form.routes[index]
        const nextRoute = this.form.routes[index + 1]

        if (currentRoute.destination_airport_id && !nextRoute.origin_airport_id) {
          nextRoute.origin_airport_id = currentRoute.destination_airport_id
        }
      }
    },
    hasRouteError(index) {
      return Object.keys(this.errors).some(key => key.startsWith(`routes.${index}.`))
    },
    getAirportCode(airportId) {
      if (!airportId) return 'N/A'
      const airport = this.airports.find(a => a.id == airportId)
      return airport ? (airport.iata_code || airport.icao_code || airport.id) : 'N/A'
    },
    getAirportName(airportId) {
      if (!airportId) return 'Select airport'
      const airport = this.airports.find(a => a.id == airportId)
      return airport ? airport.name : 'Unknown airport'
    },
    getFlightRoute() {
      if (this.form.routes.length === 0) return 'No routes configured'

      if (this.form.routes.length === 1) {
        const route = this.form.routes[0]
        const origin = this.getAirportCode(route.origin_airport_id)
        const destination = this.getAirportCode(route.destination_airport_id)
        return `${origin} → ${destination}`
      }

      // Multi-segment route
      const airports = [this.getAirportCode(this.form.routes[0].origin_airport_id)]
      this.form.routes.forEach(route => {
        airports.push(this.getAirportCode(route.destination_airport_id))
      })
      return airports.join(' → ')
    },
    getTotalDuration() {
      if (this.form.routes.length === 0) return 'N/A'

      const firstRoute = this.form.routes[0]
      const lastRoute = this.form.routes[this.form.routes.length - 1]

      if (!firstRoute.departure_time || !lastRoute.arrival_time) return 'N/A'

      const departure = new Date(firstRoute.departure_time)
      const arrival = new Date(lastRoute.arrival_time)
      const diff = arrival - departure

      if (diff <= 0) return 'N/A'

      const hours = Math.floor(diff / (1000 * 60 * 60))
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))

      return `${hours}h ${minutes}m`
    },
    getCustomerDisplayName(customer) {
      if (customer.company_name) {
        return customer.company_name
      }
      return `${customer.first_name || ''} ${customer.last_name || ''}`.trim() || 'N/A'
    },
    submit() {
      this.form.put(route('scheduled-flights.update', this.flight.id))
    },
    formatDateTimeForInput(datetime) {
      if (!datetime) return ''

      // Convert to local timezone and format for datetime-local input
      const date = new Date(datetime)
      const year = date.getFullYear()
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const day = String(date.getDate()).padStart(2, '0')
      const hours = String(date.getHours()).padStart(2, '0')
      const minutes = String(date.getMinutes()).padStart(2, '0')

      return `${year}-${month}-${day}T${hours}:${minutes}`
    },
    formatDate(datetime) {
      if (!datetime) return 'N/A'
      return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    formatDateTime(datetime) {
      if (!datetime) return 'N/A'
      return new Date(datetime).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    getAirportName(airportId) {
      if (!airportId) return 'Select Location'
      const airport = this.airports.find(a => a.id == airportId)
      return airport ? airport.name : 'Unknown Location'
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
    }
  }
}
</script>

<style scoped>
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

/* Form improvements */
.location-item .form-select {
  border: 1px solid #ced4da;
  background-color: white;
  transition: all 0.2s ease;
}

.location-item .form-select:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control-sm:focus,
.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-label {
  font-weight: 600;
  color: #495057;
  font-size: 0.875rem;
}

.invalid-feedback {
  display: block;
}

.text-danger {
  color: #dc3545 !important;
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