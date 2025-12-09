<template>
  <Head title="Fuel Consumption" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-tachometer-alt icon-gradient bg-warning"></i>
        </div>
        <div>
          Fuel Consumption Tracking
          <div class="page-title-subheading">Monitor and analyze aircraft fuel consumption by flight</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('fuel-consumption.create')" class="mb-2 mr-2 btn btn-primary">
          <i class="fa fa-plus"></i> Add Consumption Record
        </Link>
      </div>
    </div>

    <!-- Content Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-tachometer-alt icon-gradient bg-warning"></i>
        Fuel Consumption Records
      </div>

      <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-3">
          <div class="col-md-3">
            <label class="form-label">Search Flight/Aircraft</label>
            <input
              v-model="filters.search"
              type="text"
              class="form-control"
              placeholder="Flight number or aircraft..."
              @input="filterData"
            />
          </div>
          <div class="col-md-2">
            <label class="form-label">Route From</label>
            <input
              v-model="filters.routeFrom"
              type="text"
              class="form-control"
              placeholder="ICAO code..."
              @input="filterData"
            />
          </div>
          <div class="col-md-2">
            <label class="form-label">Route To</label>
            <input
              v-model="filters.routeTo"
              type="text"
              class="form-control"
              placeholder="ICAO code..."
              @input="filterData"
            />
          </div>
          <div class="col-md-2">
            <label class="form-label">Date From</label>
            <input
              v-model="filters.dateFrom"
              type="date"
              class="form-control"
              @change="filterData"
            />
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button @click="clearFilters" class="btn btn-outline-secondary">
              <i class="fa fa-refresh"></i> Clear Filters
            </button>
          </div>
        </div>

        <!-- Fuel Consumption Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="thead-light">
              <tr>
                <th>Flight #</th>
                <th>Date</th>
                <th>Aircraft</th>
                <th>Route</th>
                <th>Distance</th>
                <th>Flight Hours</th>
                <th>Fuel Consumed</th>
                <th>Efficiency</th>
                <th>Passengers</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="consumption in filteredFuelConsumption" :key="consumption.id" class="clickable-row">
                <td class="font-weight-bold text-primary">{{ consumption.flight_number }}</td>
                <td>{{ formatDate(consumption.flight_date) }}</td>
                <td>
                  <span class="badge badge-info">{{ consumption.aircraft?.registration_number }}</span>
                  <br>
                  <small class="text-muted">{{ consumption.aircraft?.manufacturer?.name }} {{ consumption.aircraft?.model?.name }}</small>
                </td>
                <td>
                  <strong>{{ consumption.route_from }}</strong> → <strong>{{ consumption.route_to }}</strong>
                  <br>
                  <small class="text-muted">{{ formatNumber(consumption.distance_km) }} km</small>
                </td>
                <td class="text-right">{{ formatNumber(consumption.distance_km) }} km</td>
                <td class="text-right">{{ formatNumber(consumption.flight_hours) }} hrs</td>
                <td class="text-right">
                  <span class="font-weight-bold">
                    {{ formatNumber(consumption.fuel_consumed) }} {{ consumption.fuel_unit?.symbol || 'L' }}
                  </span>
                  <br>
                  <small class="text-muted">{{ formatNumber(consumption.average_fuel_flow) }} {{ consumption.fuel_unit?.symbol || 'L' }}/hr</small>
                </td>
                <td class="text-right">
                  <span class="badge badge-info">
                    {{ formatNumber(consumption.fuel_efficiency) }} km/{{ consumption.fuel_unit?.symbol || 'L' }}
                  </span>
                </td>
                <td class="text-center">
                  {{ consumption.passenger_count || '-' }}
                  <br>
                  <small class="text-muted">{{ formatNumber(consumption.cargo_weight) }} kg</small>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <Link
                      :href="route('fuel-consumption.show', consumption.id)"
                      class="btn btn-sm btn-outline-info"
                      title="View Details"
                    >
                      <i class="fa fa-eye"></i>
                    </Link>
                    <Link
                      :href="route('fuel-consumption.edit', consumption.id)"
                      class="btn btn-sm btn-outline-primary"
                      title="Edit"
                    >
                      <i class="fa fa-edit"></i>
                    </Link>
                    <button
                      @click="confirmDelete(consumption)"
                      class="btn btn-sm btn-outline-danger"
                      title="Delete"
                    >
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredFuelConsumption.length === 0" class="text-center py-4">
          <div class="text-muted">
            <i class="fa fa-tachometer-alt fa-3x mb-3"></i>
            <h5>No Fuel Consumption Records Found</h5>
            <p>{{ filters.search || filters.routeFrom ? 'No records match your filters' : 'Start by adding your first fuel consumption record' }}</p>
          </div>
        </div>

        <!-- Summary Statistics -->
        <div v-if="filteredFuelConsumption.length > 0" class="row mt-4">
          <div class="col-md-3">
            <div class="card bg-primary text-white">
              <div class="card-body text-center">
                <h4>{{ filteredFuelConsumption.length }}</h4>
                <p class="mb-0">Total Flights</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-success text-white">
              <div class="card-body text-center">
                <h4>{{ formatNumber(totalFuelConsumed) }}</h4>
                <p class="mb-0">Total Fuel (L)</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-info text-white">
              <div class="card-body text-center">
                <h4>{{ formatNumber(totalFlightHours) }}</h4>
                <p class="mb-0">Flight Hours</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-warning text-white">
              <div class="card-body text-center">
                <h4>{{ formatNumber(averageEfficiency) }}</h4>
                <p class="mb-0">Avg Efficiency (km/L)</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="fuelConsumption.links" class="d-flex justify-content-center mt-4">
          <nav>
            <ul class="pagination">
              <li v-for="link in fuelConsumption.links" :key="link.label"
                  :class="['page-item', { active: link.active, disabled: !link.url }]">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  class="page-link"
                  v-html="link.label"
                ></Link>
                <span v-else class="page-link" v-html="link.label"></span>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this fuel consumption record?</p>
            <p v-if="selectedConsumption" class="font-weight-bold">
              Flight: {{ selectedConsumption.flight_number }} ({{ selectedConsumption.route_from }} → {{ selectedConsumption.route_to }})
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button @click="deleteConsumption" class="btn btn-danger">Delete</button>
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
  name: 'FuelConsumptionIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    fuelConsumption: Object,
  },

  data() {
    return {
      filters: {
        search: '',
        routeFrom: '',
        routeTo: '',
        dateFrom: ''
      },
      filteredFuelConsumption: [],
      selectedConsumption: null,
    }
  },

  computed: {
    totalFuelConsumed() {
      return this.filteredFuelConsumption.reduce((total, item) => total + parseFloat(item.fuel_consumed || 0), 0)
    },

    totalFlightHours() {
      return this.filteredFuelConsumption.reduce((total, item) => total + parseFloat(item.flight_hours || 0), 0)
    },

    averageEfficiency() {
      const validItems = this.filteredFuelConsumption.filter(item => item.fuel_efficiency > 0)
      if (validItems.length === 0) return 0
      const total = validItems.reduce((sum, item) => sum + parseFloat(item.fuel_efficiency || 0), 0)
      return total / validItems.length
    }
  },

  mounted() {
    this.filteredFuelConsumption = this.fuelConsumption?.data || []
  },

  methods: {
    filterData() {
      let filtered = this.fuelConsumption?.data || []

      if (this.filters.search) {
        const searchTerm = this.filters.search.toLowerCase()
        filtered = filtered.filter(consumption =>
          consumption.flight_number.toLowerCase().includes(searchTerm) ||
          consumption.aircraft?.registration_number.toLowerCase().includes(searchTerm)
        )
      }

      if (this.filters.routeFrom) {
        filtered = filtered.filter(consumption =>
          consumption.route_from.toLowerCase().includes(this.filters.routeFrom.toLowerCase())
        )
      }

      if (this.filters.routeTo) {
        filtered = filtered.filter(consumption =>
          consumption.route_to.toLowerCase().includes(this.filters.routeTo.toLowerCase())
        )
      }

      if (this.filters.dateFrom) {
        filtered = filtered.filter(consumption =>
          new Date(consumption.flight_date) >= new Date(this.filters.dateFrom)
        )
      }

      this.filteredFuelConsumption = filtered
    },

    clearFilters() {
      this.filters = {
        search: '',
        routeFrom: '',
        routeTo: '',
        dateFrom: ''
      }
      this.filteredFuelConsumption = this.fuelConsumption?.data || []
    },

    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
      })
    },

    formatNumber(number) {
      if (!number) return '0'
      return new Intl.NumberFormat().format(parseFloat(number))
    },

    confirmDelete(consumption) {
      this.selectedConsumption = consumption
      $('#deleteModal').modal('show')
    },

    deleteConsumption() {
      if (this.selectedConsumption) {
        router.delete(route('fuel-consumption.destroy', this.selectedConsumption.id), {
          onSuccess: () => {
            $('#deleteModal').modal('hide')
            this.selectedConsumption = null
          }
        })
      }
    }
  }
}
</script>

<style scoped>
.clickable-row:hover {
  background-color: #f8f9fa;
}

.table td {
  vertical-align: middle;
}

.page-title-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title-heading {
  display: flex;
  align-items: center;
}

.page-title-icon {
  margin-right: 1rem;
  padding: 0.75rem;
  border-radius: 0.5rem;
}

.page-title-icon i {
  font-size: 1.5rem;
  color: white;
}

.bg-warning {
  background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
}
</style>