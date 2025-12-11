<template>
  <Head title="Scheduled Flights" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Scheduled Flights</h1>
        <p class="mb-0 text-muted">
          View and manage all scheduled flights in your system
        </p>
      </div>
      <Link
        :href="route('scheduled-flights.create')"
        class="btn btn-primary"
      >
        <i class="fas fa-plus me-2"></i>Schedule New Flight
      </Link>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Search Flights</label>
            <input
              v-model="form.search"
              type="text"
              class="form-control"
              placeholder="Flight code or aircraft registration..."
              @input="search"
            />
          </div>
          <div class="col-md-3">
            <label class="form-label">Filter by Status</label>
            <select v-model="form.status_id" class="form-select custom-select" @change="search">
              <option value="">All Statuses</option>
              <option
                v-for="status in statuses"
                :key="status.id"
                :value="status.id"
              >
                {{ status.name }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Filter by Date</label>
            <input
              v-model="form.date"
              type="date"
              class="form-control"
              @change="search"
            />
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button @click="clearFilters" class="btn btn-outline-secondary">
              <i class="fas fa-times me-1"></i>Clear
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-primary">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white small">Total Flights</div>
              <div class="stat-value text-white fw-bold">{{ flights.total || 0 }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-success">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-plane-departure"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white small">Today's Flights</div>
              <div class="stat-value text-white fw-bold">{{ todayFlights || 0 }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-info">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-clock"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white small">On Time</div>
              <div class="stat-value text-white fw-bold">{{ onTimeFlights || 0 }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-warning">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white small">Delayed</div>
              <div class="stat-value text-white fw-bold">{{ delayedFlights || 0 }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Flights Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
          <i class="fas fa-list me-2 text-primary"></i>Flights List
          <span class="badge bg-secondary ms-2">{{ flights.total || 0 }} total</span>
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Flight Code</th>
                <th>Aircraft</th>
                <th>Route</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="flights.data && flights.data.length === 0">
                <td colspan="7" class="text-center py-4 text-muted">
                  <i class="fas fa-plane-slash fa-2x mb-3 d-block"></i>
                  No scheduled flights found
                </td>
              </tr>
              <tr v-for="flight in flights.data" :key="flight.id">
                <td>
                  <Link
                    :href="route('scheduled-flights.show', flight.id)"
                    class="fw-bold text-primary text-decoration-none"
                  >
                    {{ flight.flight_code }}
                  </Link>
                </td>
                <td>
                  <div>
                    <div class="fw-medium">{{ flight.aircraft?.registration_number || 'N/A' }}</div>
                    <small class="text-muted">{{ flight.aircraft?.manufacturer?.name }} {{ flight.aircraft?.model?.name }}</small>
                  </div>
                </td>
                <td>
                  <div v-if="flight.schedule_routes && flight.schedule_routes.length > 0">
                    <div class="fw-medium">
                      {{ getRouteDisplay(flight) }}
                    </div>
                    <small class="text-muted">
                      {{ getRouteDetails(flight) }}
                    </small>
                  </div>
                  <div v-else class="text-muted">
                    No route defined
                  </div>
                </td>
                <td>
                  <div>
                    <div class="fw-medium">{{ formatDateTime(flight.total_departure_time) }}</div>
                    <small v-if="getFirstActualDeparture(flight)" class="text-success">
                      Actual: {{ formatDateTime(getFirstActualDeparture(flight)) }}
                    </small>
                  </div>
                </td>
                <td>
                  <div>
                    <div class="fw-medium">{{ formatDateTime(flight.total_arrival_time) }}</div>
                    <small v-if="getLastActualArrival(flight)" class="text-success">
                      Actual: {{ formatDateTime(getLastActualArrival(flight)) }}
                    </small>
                  </div>
                </td>
                <td>
                  <span
                    class="badge px-3 py-2"
                    :style="{ backgroundColor: flight.flight_status?.color || '#6c757d', color: 'white' }"
                  >
                    {{ flight.flight_status?.name || 'Unknown' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <Link
                      :href="route('scheduled-flights.show', flight.id)"
                      class="btn btn-sm btn-outline-primary"
                      title="View Flight"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="route('scheduled-flights.edit', flight.id)"
                      class="btn btn-sm btn-outline-warning"
                      title="Edit Flight"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="confirmDelete(flight)"
                      class="btn btn-sm btn-outline-danger"
                      title="Delete Flight"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="flights.last_page > 1" class="card-footer bg-white">
          <nav aria-label="Flights pagination">
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item" :class="{ disabled: !flights.prev_page_url }">
                <Link
                  class="page-link"
                  :href="flights.prev_page_url || '#'"
                  :only="['flights']"
                  :preserve-state="true"
                >
                  Previous
                </Link>
              </li>

              <li
                v-for="page in paginationPages"
                :key="page"
                class="page-item"
                :class="{ active: page === flights.current_page }"
              >
                <Link
                  v-if="page !== '...'"
                  class="page-link"
                  :href="route('scheduled-flights.index', { ...filters, page })"
                  :only="['flights']"
                  :preserve-state="true"
                >
                  {{ page }}
                </Link>
                <span v-else class="page-link">...</span>
              </li>

              <li class="page-item" :class="{ disabled: !flights.next_page_url }">
                <Link
                  class="page-link"
                  :href="flights.next_page_url || '#'"
                  :only="['flights']"
                  :preserve-state="true"
                >
                  Next
                </Link>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
      @click="cancelDelete"
    >
      <div class="modal-dialog" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button
              type="button"
              class="btn-close"
              @click="cancelDelete"
            ></button>
          </div>
          <div class="modal-body">
            <p>
              Are you sure you want to delete flight
              <strong>{{ flightToDelete?.flight_code }}</strong>?
            </p>
            <p class="text-muted small mb-0">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="cancelDelete"
              :disabled="deleting"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click="deleteFlight"
              :disabled="deleting"
            >
              <span v-if="deleting">
                <i class="fas fa-spinner fa-spin me-1"></i>
                Deleting...
              </span>
              <span v-else>Delete Flight</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'ScheduledFlightIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    flights: Object,
    statuses: Array,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters?.search || '',
        status_id: this.filters?.status_id || '',
        date: this.filters?.date || '',
      },
      showDeleteModal: false,
      flightToDelete: null,
      deleting: false,
      searchTimeout: null,
    }
  },
  computed: {
    todayFlights() {
      return this.flights.data?.filter(flight =>
        new Date(flight.departure_time).toDateString() === new Date().toDateString()
      ).length || 0
    },
    onTimeFlights() {
      return this.flights.data?.filter(flight =>
        flight.flight_status?.code === 'SCHEDULED' || flight.flight_status?.code === 'DEPARTED'
      ).length || 0
    },
    delayedFlights() {
      return this.flights.data?.filter(flight =>
        flight.flight_status?.code === 'DELAYED'
      ).length || 0
    },
    paginationPages() {
      if (!this.flights || !this.flights.current_page || !this.flights.last_page) {
        return []
      }

      const current = this.flights.current_page
      const last = this.flights.last_page
      const delta = 2
      const range = []

      for (let i = Math.max(2, current - delta);
           i <= Math.min(last - 1, current + delta);
           i++) {
        range.push(i)
      }

      if (current - delta > 2) {
        range.unshift('...')
      }
      if (current + delta < last - 1) {
        range.push('...')
      }

      range.unshift(1)
      if (last > 1) {
        range.push(last)
      }

      return range
    }
  },
  methods: {
    search() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }

      this.searchTimeout = setTimeout(() => {
        router.get(route('scheduled-flights.index'), this.form, {
          preserveState: true,
          preserveScroll: true,
          only: ['flights']
        })
      }, 300)
    },
    clearFilters() {
      this.form = {
        search: '',
        status_id: '',
        date: '',
      }
      this.search()
    },
    confirmDelete(flight) {
      this.flightToDelete = flight
      this.showDeleteModal = true
    },
    cancelDelete() {
      this.showDeleteModal = false
      this.flightToDelete = null
      this.deleting = false
    },
    deleteFlight() {
      if (!this.flightToDelete) return

      this.deleting = true
      router.delete(route('scheduled-flights.destroy', this.flightToDelete.id), {
        onSuccess: () => {
          this.cancelDelete()
        },
        onError: () => {
          this.deleting = false
        }
      })
    },
    formatDateTime(datetime) {
      if (!datetime) return 'N/A'
      return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    getRouteDisplay(flight) {
      if (!flight.schedule_routes || flight.schedule_routes.length === 0) {
        return 'No route defined'
      }

      if (flight.schedule_routes.length === 1) {
        const route = flight.schedule_routes[0]
        const origin = route.origin_airport?.iata_code || route.origin_airport?.icao_code || 'N/A'
        const destination = route.destination_airport?.iata_code || route.destination_airport?.icao_code || 'N/A'
        return `${origin} → ${destination}`
      }

      // Multi-segment route
      const segments = []
      flight.schedule_routes.forEach(route => {
        const code = route.origin_airport?.iata_code || route.origin_airport?.icao_code || 'N/A'
        segments.push(code)
      })
      // Add final destination
      const lastRoute = flight.schedule_routes[flight.schedule_routes.length - 1]
      const finalCode = lastRoute.destination_airport?.iata_code || lastRoute.destination_airport?.icao_code || 'N/A'
      segments.push(finalCode)

      return segments.join(' → ')
    },
    getRouteDetails(flight) {
      if (!flight.schedule_routes || flight.schedule_routes.length === 0) {
        return ''
      }

      if (flight.schedule_routes.length === 1) {
        const route = flight.schedule_routes[0]
        const originCity = route.origin_airport?.city || ''
        const destinationCity = route.destination_airport?.city || ''
        return `${originCity} to ${destinationCity}`
      }

      // Multi-segment route
      const firstRoute = flight.schedule_routes[0]
      const lastRoute = flight.schedule_routes[flight.schedule_routes.length - 1]
      const originCity = firstRoute.origin_airport?.city || ''
      const destinationCity = lastRoute.destination_airport?.city || ''

      return `${originCity} to ${destinationCity} (${flight.schedule_routes.length} segments)`
    },
    getFirstActualDeparture(flight) {
      if (!flight.schedule_routes || flight.schedule_routes.length === 0) {
        return null
      }
      return flight.schedule_routes[0]?.actual_departure_time || null
    },
    getLastActualArrival(flight) {
      if (!flight.schedule_routes || flight.schedule_routes.length === 0) {
        return null
      }
      const lastRoute = flight.schedule_routes[flight.schedule_routes.length - 1]
      return lastRoute?.actual_arrival_time || null
    }
  }
}
</script>

<style scoped>
.stat-card {
  transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-card.stat-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
}

.stat-card.stat-success {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.stat-card.stat-info {
  background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
}

.stat-card.stat-warning {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
}

.stat-icon {
  font-size: 1.5rem;
  color: white;
  opacity: 0.9;
}

.stat-value {
  font-size: 1.5rem;
}

.table th {
  font-weight: 600;
  background-color: #f8f9fa !important;
}

.modal.fade.show {
  background-color: rgba(0, 0, 0, 0.5);
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