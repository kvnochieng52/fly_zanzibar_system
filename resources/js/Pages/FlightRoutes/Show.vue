<template>
  <Head :title="`Flight Route: ${route.name}`" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">{{ route.name }}</h1>
        <p class="mb-0 text-muted">
          Flight Route Details
        </p>
      </div>
      <div class="btn-group">
        <Link
          :href="route('flight-routes.edit', route.id)"
          class="btn btn-warning"
        >
          <i class="fas fa-edit me-2"></i>Edit Route
        </Link>
        <Link
          :href="route('flight-routes.index')"
          class="btn btn-outline-secondary"
        >
          <i class="fas fa-arrow-left me-2"></i>Back to Routes
        </Link>
      </div>
    </div>

    <div class="row">
      <!-- Route Information -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
              <i class="fas fa-info-circle me-2 text-primary"></i>Route Information
            </h5>
          </div>
          <div class="card-body">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label fw-bold">Route Code</label>
                <div>
                  <span class="badge bg-info fs-6">{{ route.code }}</span>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Status</label>
                <div>
                  <span
                    class="badge fs-6"
                    :class="route.is_active ? 'bg-success' : 'bg-secondary'"
                  >
                    {{ route.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>
              <div class="col-md-12">
                <label class="form-label fw-bold">Route</label>
                <div class="d-flex align-items-center gap-3">
                  <div class="text-center">
                    <div class="fw-bold text-primary">{{ route.origin_airport?.code }}</div>
                    <small class="text-muted">{{ route.origin_airport?.city }}</small>
                  </div>
                  <div>
                    <i class="fas fa-plane text-muted"></i>
                  </div>
                  <div class="text-center">
                    <div class="fw-bold text-primary">{{ route.destination_airport?.code }}</div>
                    <small class="text-muted">{{ route.destination_airport?.city }}</small>
                  </div>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    {{ route.origin_airport?.name }} to {{ route.destination_airport?.name }}
                  </small>
                </div>
              </div>
              <div class="col-md-4" v-if="route.distance_km">
                <label class="form-label fw-bold">Distance</label>
                <div>{{ route.distance_km }} km</div>
              </div>
              <div class="col-md-4" v-if="route.duration_minutes">
                <label class="form-label fw-bold">Duration</label>
                <div>{{ formatDuration(route.duration_minutes) }}</div>
              </div>
              <div class="col-md-4" v-if="route.base_price">
                <label class="form-label fw-bold">Base Price</label>
                <div>${{ Number(route.base_price).toFixed(2) }}</div>
              </div>
              <div class="col-12" v-if="route.description">
                <label class="form-label fw-bold">Description</label>
                <div class="text-muted">{{ route.description }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">
              <i class="fas fa-chart-bar me-2 text-success"></i>Route Statistics
            </h6>
          </div>
          <div class="card-body">
            <div class="text-center mb-3">
              <div class="display-4 fw-bold text-primary">
                {{ route.scheduled_flights ? route.scheduled_flights.length : 0 }}
              </div>
              <div class="text-muted">Scheduled Flights</div>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-2">
              <span>Created</span>
              <span class="fw-bold">{{ formatDate(route.created_at) }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Last Updated</span>
              <span class="fw-bold">{{ formatDate(route.updated_at) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scheduled Flights -->
    <div v-if="route.scheduled_flights && route.scheduled_flights.length > 0" class="card border-0 shadow-sm mt-4">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
          <i class="fas fa-calendar-alt me-2 text-info"></i>Scheduled Flights
          <span class="badge bg-secondary ms-2">{{ route.scheduled_flights.length }}</span>
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Flight Code</th>
                <th>Aircraft</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="flight in route.scheduled_flights" :key="flight.id">
                <td>
                  <Link
                    :href="route('scheduled-flights.show', flight.id)"
                    class="fw-bold text-primary text-decoration-none"
                  >
                    {{ flight.flight_code }}
                  </Link>
                </td>
                <td>
                  {{ flight.aircraft?.registration || 'N/A' }}
                  <br>
                  <small class="text-muted">{{ flight.aircraft?.model || '' }}</small>
                </td>
                <td>
                  {{ formatDateTime(flight.departure_time) }}
                </td>
                <td>
                  {{ formatDateTime(flight.arrival_time) }}
                </td>
                <td>
                  <span
                    class="badge"
                    :style="{ backgroundColor: flight.flight_status?.color || '#6c757d' }"
                  >
                    {{ flight.flight_status?.name || 'Unknown' }}
                  </span>
                </td>
                <td>
                  <Link
                    :href="route('scheduled-flights.show', flight.id)"
                    class="btn btn-sm btn-outline-primary"
                    title="View Flight"
                  >
                    <i class="fas fa-eye"></i>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Empty State for Scheduled Flights -->
    <div v-else class="card border-0 shadow-sm mt-4">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
          <i class="fas fa-calendar-alt me-2 text-info"></i>Scheduled Flights
        </h5>
      </div>
      <div class="card-body text-center py-4">
        <i class="fas fa-calendar-times fa-2x text-muted mb-3 d-block"></i>
        <p class="text-muted mb-3">No scheduled flights using this route yet.</p>
        <Link
          :href="route('scheduled-flights.create')"
          class="btn btn-primary"
        >
          <i class="fas fa-plus me-2"></i>Schedule First Flight
        </Link>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'FlightRouteShow',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    route: Object,
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString()
    },
    formatDateTime(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleString()
    },
    formatDuration(minutes) {
      if (!minutes) return 'N/A'
      const hours = Math.floor(minutes / 60)
      const mins = minutes % 60
      return `${hours}h ${mins}m`
    }
  }
}
</script>

<style scoped>
.table th {
  font-weight: 600;
  background-color: #f8f9fa !important;
}
</style>