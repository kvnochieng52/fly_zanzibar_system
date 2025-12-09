<template>
  <Head title="Landing Fees" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="lnr-airplane icon-gradient bg-plum-plate"></i>
        </div>
        <div>
          Landing Fees Management
          <div class="page-title-subheading">Track and manage aircraft landing fees by airport and flight</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('landing-fees.create')" class="mb-2 mr-2 btn btn-primary">
          <i class="fa fa-plus"></i> Add Landing Fee
        </Link>
      </div>
    </div>

    <!-- Content Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon lnr-airplane icon-gradient bg-plum-plate"></i>
        Landing Fees Records
      </div>

      <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-3">
          <div class="col-md-3">
            <label class="form-label">Search Flight/Airport</label>
            <input
              v-model="filters.search"
              type="text"
              class="form-control"
              placeholder="Flight number or airport..."
              @input="filterData"
            />
          </div>
          <div class="col-md-2">
            <label class="form-label">Payment Status</label>
            <select v-model="filters.status" @change="filterData" class="form-control">
              <option value="">All Statuses</option>
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
              <option value="overdue">Overdue</option>
            </select>
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
          <div class="col-md-2">
            <label class="form-label">Date To</label>
            <input
              v-model="filters.dateTo"
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

        <!-- Landing Fees Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="thead-light">
              <tr>
                <th>Flight #</th>
                <th>Date</th>
                <th>Aircraft</th>
                <th>Airport</th>
                <th>MTOW (kg)</th>
                <th>Fee Amount</th>
                <th>Payment Status</th>
                <th>Payment Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="fee in filteredLandingFees" :key="fee.id" class="clickable-row">
                <td class="font-weight-bold text-primary">{{ fee.flight_number }}</td>
                <td>{{ formatDate(fee.flight_date) }}</td>
                <td>
                  <span class="badge badge-info">{{ fee.aircraft?.registration_number }}</span>
                  <br>
                  <small class="text-muted">{{ fee.aircraft?.manufacturer?.name }} {{ fee.aircraft?.model?.name }}</small>
                </td>
                <td>
                  <strong>{{ fee.airport?.icao_code }}</strong> - {{ fee.airport?.name }}
                  <br>
                  <small class="text-muted">{{ fee.airport?.city }}, {{ fee.airport?.country }}</small>
                </td>
                <td class="text-right">{{ formatNumber(fee.mtow_used) }}</td>
                <td class="text-right">
                  <span class="font-weight-bold">
                    {{ fee.currency?.symbol }}{{ formatNumber(fee.fee_amount) }}
                  </span>
                </td>
                <td>
                  <span :class="getPaymentStatusClass(fee.payment_status?.name)">
                    {{ fee.payment_status?.name }}
                  </span>
                </td>
                <td>{{ fee.payment_date ? formatDate(fee.payment_date) : '-' }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <Link
                      :href="route('landing-fees.show', fee.id)"
                      class="btn btn-sm btn-outline-info"
                      title="View Details"
                    >
                      <i class="fa fa-eye"></i>
                    </Link>
                    <Link
                      :href="route('landing-fees.edit', fee.id)"
                      class="btn btn-sm btn-outline-primary"
                      title="Edit"
                    >
                      <i class="fa fa-edit"></i>
                    </Link>
                    <button
                      @click="confirmDelete(fee)"
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
        <div v-if="filteredLandingFees.length === 0" class="text-center py-4">
          <div class="text-muted">
            <i class="fa fa-airplane fa-3x mb-3"></i>
            <h5>No Landing Fees Found</h5>
            <p>{{ filters.search || filters.status ? 'No records match your filters' : 'Start by adding your first landing fee record' }}</p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="landingFees.links" class="d-flex justify-content-center mt-4">
          <nav>
            <ul class="pagination">
              <li v-for="link in landingFees.links" :key="link.label"
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
            <p>Are you sure you want to delete this landing fee record?</p>
            <p v-if="selectedFee" class="font-weight-bold">
              Flight: {{ selectedFee.flight_number }} - {{ selectedFee.airport?.name }}
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button @click="deleteFee" class="btn btn-danger">Delete</button>
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
  name: 'LandingFeesIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    landingFees: Object,
  },

  data() {
    return {
      filters: {
        search: '',
        status: '',
        dateFrom: '',
        dateTo: ''
      },
      filteredLandingFees: [],
      selectedFee: null,
    }
  },

  mounted() {
    this.filteredLandingFees = this.landingFees.data
  },

  methods: {
    filterData() {
      let filtered = this.landingFees.data

      if (this.filters.search) {
        const searchTerm = this.filters.search.toLowerCase()
        filtered = filtered.filter(fee =>
          fee.flight_number.toLowerCase().includes(searchTerm) ||
          fee.airport?.name.toLowerCase().includes(searchTerm) ||
          fee.airport?.icao_code.toLowerCase().includes(searchTerm)
        )
      }

      if (this.filters.status) {
        filtered = filtered.filter(fee =>
          fee.payment_status?.name.toLowerCase() === this.filters.status.toLowerCase()
        )
      }

      if (this.filters.dateFrom) {
        filtered = filtered.filter(fee =>
          new Date(fee.flight_date) >= new Date(this.filters.dateFrom)
        )
      }

      if (this.filters.dateTo) {
        filtered = filtered.filter(fee =>
          new Date(fee.flight_date) <= new Date(this.filters.dateTo)
        )
      }

      this.filteredLandingFees = filtered
    },

    clearFilters() {
      this.filters = {
        search: '',
        status: '',
        dateFrom: '',
        dateTo: ''
      }
      this.filteredLandingFees = this.landingFees.data
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
      return new Intl.NumberFormat().format(number)
    },

    getPaymentStatusClass(status) {
      const statusClasses = {
        'paid': 'badge badge-success',
        'pending': 'badge badge-warning',
        'overdue': 'badge badge-danger',
        'partial': 'badge badge-info'
      }
      return statusClasses[status?.toLowerCase()] || 'badge badge-secondary'
    },

    confirmDelete(fee) {
      this.selectedFee = fee
      $('#deleteModal').modal('show')
    },

    deleteFee() {
      if (this.selectedFee) {
        router.delete(route('landing-fees.destroy', this.selectedFee.id), {
          onSuccess: () => {
            $('#deleteModal').modal('hide')
            this.selectedFee = null
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

.bg-plum-plate {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>