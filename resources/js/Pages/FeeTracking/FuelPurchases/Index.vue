<template>
  <Head title="Fuel Purchases" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-gas-pump icon-gradient bg-success"></i>
        </div>
        <div>
          Fuel Purchases Management
          <div class="page-title-subheading">Track and manage aircraft fuel purchases and inventory</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('fuel-purchases.create')" class="mb-2 mr-2 btn btn-primary">
          <i class="fa fa-plus"></i> Add Fuel Purchase
        </Link>
      </div>
    </div>

    <!-- Content Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-gas-pump icon-gradient bg-success"></i>
        Fuel Purchase Records
      </div>

      <div class="card-body">
        <!-- Filters Section -->
        <div class="row mb-3">
          <div class="col-md-3">
            <label class="form-label">Search Purchase/Aircraft</label>
            <input
              v-model="filters.search"
              type="text"
              class="form-control"
              placeholder="Purchase number or aircraft..."
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

        <!-- Fuel Purchases Table -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="thead-light">
              <tr>
                <th>Purchase #</th>
                <th>Date</th>
                <th>Aircraft</th>
                <th>Airport</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="purchase in filteredFuelPurchases" :key="purchase.id" class="clickable-row">
                <td class="font-weight-bold text-primary">{{ purchase.purchase_number }}</td>
                <td>{{ formatDate(purchase.purchase_date) }}</td>
                <td>
                  <span class="badge badge-info">{{ purchase.aircraft?.registration_number }}</span>
                  <br>
                  <small class="text-muted">{{ purchase.aircraft?.manufacturer?.name }} {{ purchase.aircraft?.model?.name }}</small>
                </td>
                <td>
                  <strong>{{ purchase.airport?.icao_code }}</strong> - {{ purchase.airport?.name }}
                  <br>
                  <small class="text-muted">{{ purchase.airport?.city }}, {{ purchase.airport?.country }}</small>
                </td>
                <td>
                  <span class="badge badge-secondary">{{ purchase.fuel_supplier?.name }}</span>
                  <br>
                  <small class="text-muted">{{ purchase.fuel_grade || 'Jet A-1' }}</small>
                </td>
                <td class="text-right">
                  {{ formatNumber(purchase.quantity) }} {{ purchase.fuel_unit?.symbol || 'L' }}
                </td>
                <td class="text-right">
                  {{ purchase.currency?.symbol }}{{ formatNumber(purchase.unit_price) }}
                </td>
                <td class="text-right">
                  <span class="font-weight-bold">
                    {{ purchase.currency?.symbol }}{{ formatNumber(purchase.total_amount) }}
                  </span>
                </td>
                <td>
                  <span :class="getPaymentStatusClass(purchase.payment_status?.name)">
                    {{ purchase.payment_status?.name }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <Link
                      :href="route('fuel-purchases.show', purchase.id)"
                      class="btn btn-sm btn-outline-info"
                      title="View Details"
                    >
                      <i class="fa fa-eye"></i>
                    </Link>
                    <Link
                      :href="route('fuel-purchases.edit', purchase.id)"
                      class="btn btn-sm btn-outline-primary"
                      title="Edit"
                    >
                      <i class="fa fa-edit"></i>
                    </Link>
                    <button
                      @click="confirmDelete(purchase)"
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
        <div v-if="filteredFuelPurchases.length === 0" class="text-center py-4">
          <div class="text-muted">
            <i class="fa fa-gas-pump fa-3x mb-3"></i>
            <h5>No Fuel Purchases Found</h5>
            <p>{{ filters.search || filters.status ? 'No records match your filters' : 'Start by adding your first fuel purchase record' }}</p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="fuelPurchases.links" class="d-flex justify-content-center mt-4">
          <nav>
            <ul class="pagination">
              <li v-for="link in fuelPurchases.links" :key="link.label"
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
            <p>Are you sure you want to delete this fuel purchase record?</p>
            <p v-if="selectedPurchase" class="font-weight-bold">
              Purchase: {{ selectedPurchase.purchase_number }} - {{ selectedPurchase.fuel_supplier?.name }}
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button @click="deletePurchase" class="btn btn-danger">Delete</button>
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
  name: 'FuelPurchasesIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    fuelPurchases: Object,
  },

  data() {
    return {
      filters: {
        search: '',
        status: '',
        dateFrom: '',
        dateTo: ''
      },
      filteredFuelPurchases: [],
      selectedPurchase: null,
    }
  },

  mounted() {
    this.filteredFuelPurchases = this.fuelPurchases?.data || []
  },

  methods: {
    filterData() {
      let filtered = this.fuelPurchases?.data || []

      if (this.filters.search) {
        const searchTerm = this.filters.search.toLowerCase()
        filtered = filtered.filter(purchase =>
          purchase.purchase_number.toLowerCase().includes(searchTerm) ||
          purchase.aircraft?.registration_number.toLowerCase().includes(searchTerm) ||
          purchase.fuel_supplier?.name.toLowerCase().includes(searchTerm)
        )
      }

      if (this.filters.status) {
        filtered = filtered.filter(purchase =>
          purchase.payment_status?.name.toLowerCase() === this.filters.status.toLowerCase()
        )
      }

      if (this.filters.dateFrom) {
        filtered = filtered.filter(purchase =>
          new Date(purchase.purchase_date) >= new Date(this.filters.dateFrom)
        )
      }

      if (this.filters.dateTo) {
        filtered = filtered.filter(purchase =>
          new Date(purchase.purchase_date) <= new Date(this.filters.dateTo)
        )
      }

      this.filteredFuelPurchases = filtered
    },

    clearFilters() {
      this.filters = {
        search: '',
        status: '',
        dateFrom: '',
        dateTo: ''
      }
      this.filteredFuelPurchases = this.fuelPurchases?.data || []
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

    confirmDelete(purchase) {
      this.selectedPurchase = purchase
      $('#deleteModal').modal('show')
    },

    deletePurchase() {
      if (this.selectedPurchase) {
        router.delete(route('fuel-purchases.destroy', this.selectedPurchase.id), {
          onSuccess: () => {
            $('#deleteModal').modal('hide')
            this.selectedPurchase = null
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

.bg-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
</style>