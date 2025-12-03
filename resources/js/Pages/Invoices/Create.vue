<template>
  <Head title="Create Invoice" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">
          Create New Invoice
        </h1>
        <p class="mb-0 text-muted">Create a new invoice for your customers</p>
      </div>
      <Link :href="route('invoices.index')" class="btn btn-outline-secondary">
        Back to Invoices
      </Link>
    </div>

    <!-- Form Card -->
    <form @submit.prevent="submitForm" class="needs-validation" novalidate>
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="row g-3">
            <!-- Invoice Details Section -->
            <div class="col-12 mb-3">
              <h5 class="card-title mb-3">Invoice Details</h5>
            </div>

            <!-- Customer -->
            <div class="col-md-6">
              <label for="customer_id" class="form-label required">Customer</label>
              <select
                v-model="form.customer_id"
                class="form-select"
                :class="{ 'is-invalid': errors.customer_id }"
                id="customer_id"
                required
              >
                <option value="">Select Customer</option>
                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                  {{ customer.company_name || (customer.first_name + ' ' + customer.last_name) }}
                </option>
              </select>
              <div v-if="errors.customer_id" class="invalid-feedback">
                {{ errors.customer_id }}
              </div>
            </div>

            <!-- Currency -->
            <div class="col-md-6">
              <label for="currency_id" class="form-label required">Currency</label>
              <select
                v-model="form.currency_id"
                class="form-select"
                :class="{ 'is-invalid': errors.currency_id }"
                id="currency_id"
                required
              >
                <option value="">Select Currency</option>
                <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                  {{ currency.code }} - {{ currency.name }} ({{ currency.symbol }})
                </option>
              </select>
              <div v-if="errors.currency_id" class="invalid-feedback">
                {{ errors.currency_id }}
              </div>
            </div>

            <!-- Invoice Number -->
            <div class="col-md-4">
              <label for="invoice_number" class="form-label required">Invoice Number</label>
              <input
                v-model="form.invoice_number"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.invoice_number }"
                id="invoice_number"
                placeholder="INV-2024-001"
                required
              >
              <div v-if="errors.invoice_number" class="invalid-feedback">
                {{ errors.invoice_number }}
              </div>
            </div>

            <!-- Invoice Date -->
            <div class="col-md-4">
              <label for="invoice_date" class="form-label required">Invoice Date</label>
              <input
                v-model="form.invoice_date"
                type="date"
                class="form-control"
                :class="{ 'is-invalid': errors.invoice_date }"
                id="invoice_date"
                required
              >
              <div v-if="errors.invoice_date" class="invalid-feedback">
                {{ errors.invoice_date }}
              </div>
            </div>

            <!-- Due Date -->
            <div class="col-md-4">
              <label for="due_date" class="form-label required">Due Date</label>
              <input
                v-model="form.due_date"
                type="date"
                class="form-control"
                :class="{ 'is-invalid': errors.due_date }"
                id="due_date"
                required
              >
              <div v-if="errors.due_date" class="invalid-feedback">
                {{ errors.due_date }}
              </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
              <label for="status" class="form-label required">Payment Status</label>
              <select
                v-model="form.status"
                class="form-select"
                :class="{ 'is-invalid': errors.status }"
                id="status"
                required
              >
                <option value="">Select Status</option>
                <option v-for="status in statuses" :key="status.id" :value="status.code">
                  {{ status.name }}
                </option>
              </select>
              <div v-if="errors.status" class="invalid-feedback">
                {{ errors.status }}
              </div>
            </div>

            <!-- Subject -->
            <div class="col-md-6">
              <label for="subject" class="form-label">Subject</label>
              <input
                v-model="form.subject"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.subject }"
                id="subject"
                placeholder="Invoice for services provided"
              >
              <div v-if="errors.subject" class="invalid-feedback">
                {{ errors.subject }}
              </div>
            </div>

            <!-- Invoice Items Section -->
            <div class="col-12 mt-4">
              <h6 class="text-muted mb-3">Invoice Items</h6>

              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Description</th>
                      <th width="120">Quantity</th>
                      <th width="150">Unit Price</th>
                      <th width="150">Total</th>
                      <th width="50">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in form.items" :key="index">
                      <td>
                        <input
                          v-model="item.description"
                          type="text"
                          class="form-control"
                          placeholder="Item description"
                        >
                      </td>
                      <td>
                        <input
                          v-model="item.quantity"
                          type="number"
                          min="1"
                          step="0.01"
                          class="form-control"
                          @input="calculateItemTotal(index)"
                        >
                      </td>
                      <td>
                        <input
                          v-model="item.unit_price"
                          type="number"
                          step="0.01"
                          class="form-control"
                          @input="calculateItemTotal(index)"
                        >
                      </td>
                      <td>
                        <input
                          v-model="item.total"
                          type="number"
                          step="0.01"
                          class="form-control"
                          readonly
                        >
                      </td>
                      <td>
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-danger"
                          @click="removeItem(index)"
                          :disabled="form.items.length === 1"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <button
                type="button"
                class="btn btn-outline-primary btn-sm"
                @click="addItem"
              >
                <i class="fas fa-plus mr-1"></i>
                Add Item
              </button>
            </div>

            <!-- Financial Summary -->
            <div class="col-12 mt-4">
              <div class="row justify-content-end">
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="row mb-2">
                        <div class="col">Subtotal:</div>
                        <div class="col text-end">
                          <strong>{{ formatCurrency(subtotal) }}</strong>
                        </div>
                      </div>
                      <div class="row mb-2">
                        <div class="col">
                          Tax ({{ form.tax_rate }}%):
                        </div>
                        <div class="col text-end">
                          <strong>{{ formatCurrency(taxAmount) }}</strong>
                        </div>
                      </div>
                      <hr>
                      <div class="row">
                        <div class="col"><h6>Total:</h6></div>
                        <div class="col text-end">
                          <h6>{{ formatCurrency(total) }}</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tax Rate -->
            <div class="col-md-6">
              <label for="tax_rate" class="form-label">Tax Rate (%)</label>
              <input
                v-model="form.tax_rate"
                type="number"
                step="0.01"
                min="0"
                max="100"
                class="form-control"
                :class="{ 'is-invalid': errors.tax_rate }"
                id="tax_rate"
                @input="calculateTotals"
              >
              <div v-if="errors.tax_rate" class="invalid-feedback">
                {{ errors.tax_rate }}
              </div>
            </div>

            <!-- Notes -->
            <div class="col-12">
              <label for="notes" class="form-label">Notes</label>
              <textarea
                v-model="form.notes"
                class="form-control"
                :class="{ 'is-invalid': errors.notes }"
                id="notes"
                rows="3"
                placeholder="Additional notes or terms..."
              ></textarea>
              <div v-if="errors.notes" class="invalid-feedback">
                {{ errors.notes }}
              </div>
            </div>
          </div>
        </div>

        <!-- Card Footer with Actions -->
        <div class="card-footer bg-transparent border-top">
          <div class="d-flex justify-content-end gap-2">
            <Link :href="route('invoices.index')" class="btn btn-outline-secondary">
              Cancel
            </Link>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="processing"
            >
              {{ processing ? 'Creating...' : 'Create Invoice' }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  customers: Array,
  currencies: Array,
  statuses: Array,
  errors: Object
})

// Form data
const form = useForm({
  customer_id: '',
  currency_id: '',
  invoice_number: '',
  invoice_date: new Date().toISOString().split('T')[0],
  due_date: '',
  status: 'UNPAID',
  subject: '',
  tax_rate: 18.00,
  notes: '',
  items: [
    {
      description: '',
      quantity: 1,
      unit_price: 0,
      total: 0
    }
  ],
  subtotal: 0,
  tax_amount: 0,
  total_amount: 0
})

// Computed values
const subtotal = computed(() => {
  return form.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0)
})

const taxAmount = computed(() => {
  return subtotal.value * (parseFloat(form.tax_rate) || 0) / 100
})

const total = computed(() => {
  return subtotal.value + taxAmount.value
})

// Helper functions
const formatCurrency = (amount) => {
  const selectedCurrency = props.currencies.find(c => c.id == form.currency_id)
  const symbol = selectedCurrency?.symbol || '$'
  return symbol + parseFloat(amount || 0).toFixed(2)
}

const calculateItemTotal = (index) => {
  const item = form.items[index]
  item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)
  calculateTotals()
}

const calculateTotals = () => {
  form.subtotal = subtotal.value
  form.tax_amount = taxAmount.value
  form.total_amount = total.value
}

const addItem = () => {
  form.items.push({
    description: '',
    quantity: 1,
    unit_price: 0,
    total: 0
  })
}

const removeItem = (index) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1)
    calculateTotals()
  }
}

// Submit form
const submitForm = () => {
  calculateTotals()
  form.post(route('invoices.store'), {
    onSuccess: () => {
      // Redirect handled by controller
    }
  })
}

// Set default due date (30 days from invoice date)
onMounted(() => {
  const today = new Date()
  const dueDate = new Date(today)
  dueDate.setDate(today.getDate() + 30)
  form.due_date = dueDate.toISOString().split('T')[0]

  // Set default currency to USD
  const usdCurrency = props.currencies.find(c => c.code === 'USD')
  if (usdCurrency) {
    form.currency_id = usdCurrency.id
  }
})
</script>

<style scoped>
.required::after {
  content: " *";
  color: #dc3545;
}

.form-control:focus,
.form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card {
  border-radius: 12px;
}

.card-footer {
  border-radius: 0 0 12px 12px;
}

.btn {
  border-radius: 8px;
  font-weight: 500;
}

.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  display: block;
}

.table-responsive {
  border-radius: 8px;
  border: 1px solid #dee2e6;
}

.table th {
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
}
</style>