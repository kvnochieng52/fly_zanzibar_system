<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Customer Statements</h1>
              <p class="text-muted">Generate and view customer account statements</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item active">Statements</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <!-- Customer Statements -->
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    Customer Statements
                  </h3>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Customer</th>
                          <th>Type</th>
                          <th width="200">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="customer in customers" :key="customer.id">
                          <td>
                            <div>
                              <strong>
                                {{ customer.company_name || `${customer.first_name} ${customer.last_name}` }}
                              </strong>
                            </div>
                          </td>
                          <td>
                            <span class="badge" :class="customer.type === 'corporate' ? 'badge-primary' : 'badge-info'">
                              {{ customer.type === 'corporate' ? 'Corporate' : 'Individual' }}
                            </span>
                          </td>
                          <td>
                            <div class="btn-group" role="group">
                              <button
                                @click="generateStatement(customer)"
                                class="btn btn-sm btn-outline-primary"
                                title="Generate Statement"
                              >
                                <i class="fas fa-file-alt mr-1"></i>
                                Generate
                              </button>
                              <Link
                                :href="route('statements.show', customer.id)"
                                class="btn btn-sm btn-outline-info"
                                title="View Statement"
                              >
                                <i class="fas fa-eye mr-1"></i>
                                View
                              </Link>
                            </div>
                          </td>
                        </tr>
                        <tr v-if="!customers?.length">
                          <td colspan="3" class="text-center text-muted py-4">
                            <i class="fas fa-file-alt fa-3x mb-3 d-block"></i>
                            No customers with invoices found.
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Reports & Analysis
                  </h3>
                </div>
                <div class="card-body">
                  <div class="d-grid gap-3">
                    <Link
                      :href="route('statements.aging-report')"
                      class="btn btn-outline-warning btn-block"
                    >
                      <i class="fas fa-clock mr-2"></i>
                      Aging Report
                    </Link>

                    <hr>

                    <div class="text-center">
                      <h6 class="text-muted">Quick Statement Generation</h6>
                      <div class="form-group">
                        <label>Select Customer:</label>
                        <select
                          class="form-control"
                          v-model="selectedCustomerId"
                        >
                          <option value="">Choose a customer...</option>
                          <option
                            v-for="customer in customers"
                            :key="customer.id"
                            :value="customer.id"
                          >
                            {{ customer.company_name || `${customer.first_name} ${customer.last_name}` }}
                          </option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label>Date Range:</label>
                        <select class="form-control" v-model="selectedDateRange">
                          <option value="current_month">Current Month</option>
                          <option value="last_month">Last Month</option>
                          <option value="last_3_months">Last 3 Months</option>
                          <option value="last_6_months">Last 6 Months</option>
                          <option value="current_year">Current Year</option>
                          <option value="custom">Custom Range</option>
                        </select>
                      </div>

                      <div v-if="selectedDateRange === 'custom'" class="form-group">
                        <label>Start Date:</label>
                        <input
                          type="date"
                          class="form-control"
                          v-model="customStartDate"
                        >
                        <label class="mt-2">End Date:</label>
                        <input
                          type="date"
                          class="form-control"
                          v-model="customEndDate"
                        >
                      </div>

                      <button
                        @click="generateQuickStatement"
                        :disabled="!selectedCustomerId"
                        class="btn btn-primary btn-block"
                      >
                        <i class="fas fa-file-alt mr-1"></i>
                        Generate Statement
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Statement Generation Modal -->
    <div v-if="showModal" class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Generate Customer Statement
              <small v-if="selectedCustomer" class="text-muted ml-2">
                - {{ selectedCustomer.company_name || `${selectedCustomer.first_name} ${selectedCustomer.last_name}` }}
              </small>
            </h5>
            <button type="button" class="close" @click="showModal = false">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Start Date:</label>
                  <input
                    type="date"
                    class="form-control"
                    v-model="statementForm.start_date"
                  >
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>End Date:</label>
                  <input
                    type="date"
                    class="form-control"
                    v-model="statementForm.end_date"
                  >
                </div>
              </div>
            </div>
            <div class="alert alert-info">
              <i class="fas fa-info-circle mr-2"></i>
              This will generate a statement for all invoices and payments within the selected date range.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showModal = false">
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-primary"
              @click="viewStatement"
            >
              <i class="fas fa-eye mr-1"></i>
              View Statement
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  customers: Array
})

const selectedCustomerId = ref('')
const selectedDateRange = ref('last_3_months')
const customStartDate = ref('')
const customEndDate = ref('')
const selectedCustomer = ref(null)
const showModal = ref(false)

const statementForm = reactive({
  customer_id: null,
  start_date: '',
  end_date: ''
})

const generateStatement = (customer) => {
  selectedCustomer.value = customer
  statementForm.customer_id = customer.id

  // Set default date range to last 3 months
  const endDate = new Date()
  const startDate = new Date()
  startDate.setMonth(startDate.getMonth() - 3)

  statementForm.start_date = startDate.toISOString().split('T')[0]
  statementForm.end_date = endDate.toISOString().split('T')[0]

  // Show modal
  showModal.value = true
}

const generateQuickStatement = () => {
  if (!selectedCustomerId.value) return

  const customer = props.customers.find(c => c.id == selectedCustomerId.value)
  if (!customer) return

  const dates = getDateRange()

  const params = new URLSearchParams({
    start_date: dates.start,
    end_date: dates.end
  })

  router.get(`${route('statements.show', customer.id)}?${params.toString()}`)
}

const viewStatement = () => {
  if (!selectedCustomer.value) {
    alert('Please select a customer first')
    return
  }

  if (!statementForm.start_date || !statementForm.end_date) {
    alert('Please select both start and end dates')
    return
  }

  const params = new URLSearchParams({
    start_date: statementForm.start_date,
    end_date: statementForm.end_date
  })

  router.get(`${route('statements.show', selectedCustomer.value.id)}?${params.toString()}`)
  showModal.value = false
}

const getDateRange = () => {
  const today = new Date()
  let start, end

  switch (selectedDateRange.value) {
    case 'current_month':
      start = new Date(today.getFullYear(), today.getMonth(), 1)
      end = new Date(today.getFullYear(), today.getMonth() + 1, 0)
      break
    case 'last_month':
      start = new Date(today.getFullYear(), today.getMonth() - 1, 1)
      end = new Date(today.getFullYear(), today.getMonth(), 0)
      break
    case 'last_3_months':
      start = new Date(today.getFullYear(), today.getMonth() - 3, 1)
      end = new Date(today.getFullYear(), today.getMonth() + 1, 0)
      break
    case 'last_6_months':
      start = new Date(today.getFullYear(), today.getMonth() - 6, 1)
      end = new Date(today.getFullYear(), today.getMonth() + 1, 0)
      break
    case 'current_year':
      start = new Date(today.getFullYear(), 0, 1)
      end = new Date(today.getFullYear(), 11, 31)
      break
    case 'custom':
      start = new Date(customStartDate.value)
      end = new Date(customEndDate.value)
      break
    default:
      start = new Date(today.getFullYear(), today.getMonth() - 3, 1)
      end = new Date(today.getFullYear(), today.getMonth() + 1, 0)
  }

  return {
    start: start.toISOString().split('T')[0],
    end: end.toISOString().split('T')[0]
  }
}
</script>