<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Create Quotation</h1>
              <p class="text-muted">Generate a new flight quotation</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item">
                  <Link :href="route('quotations.index')">Quotations</Link>
                </li>
                <li class="breadcrumb-item active">Create</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <form @submit.prevent="submit">
            <!-- Quote Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-info-circle mr-2"></i>
                  Quote Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Quote Number <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.quote_number"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.quote_number }"
                      placeholder="Auto-generated"
                      readonly
                    />
                    <div v-if="form.errors.quote_number" class="invalid-feedback">
                      {{ form.errors.quote_number }}
                    </div>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Quote Date <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.quote_date"
                      type="date"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.quote_date }"
                    />
                    <div v-if="form.errors.quote_date" class="invalid-feedback">
                      {{ form.errors.quote_date }}
                    </div>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Valid Until <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.valid_until"
                      type="date"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.valid_until }"
                    />
                    <div v-if="form.errors.valid_until" class="invalid-feedback">
                      {{ form.errors.valid_until }}
                    </div>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Status <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.status_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.status_id }"
                    >
                      <option value="">Select status</option>
                      <option
                        v-for="status in statuses"
                        :key="status.id"
                        :value="status.id"
                      >
                        {{ status.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.status_id" class="invalid-feedback">
                      {{ form.errors.status_id }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Customer Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-user mr-2"></i>
                  Customer Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12 mb-3">
                    <label class="form-label">
                      Customer <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.customer_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.customer_id }"
                    >
                      <option value="">Select customer</option>
                      <option
                        v-for="customer in customers"
                        :key="customer.id"
                        :value="customer.id"
                      >
                        {{ customer.company_name || customer.first_name + ' ' + customer.last_name }}
                      </option>
                    </select>
                    <div v-if="form.errors.customer_id" class="invalid-feedback">
                      {{ form.errors.customer_id }}
                    </div>
                    <small class="text-muted">
                      Don't see the customer?
                      <Link :href="route('customers.create')" class="text-primary">Create a new customer</Link>
                    </small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Flight Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-plane mr-2"></i>
                  Flight Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Departure Airport</label>
                    <input
                      v-model="form.departure_airport"
                      type="text"
                      class="form-control"
                      placeholder="e.g., HTZA"
                      maxlength="4"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Arrival Airport</label>
                    <input
                      v-model="form.arrival_airport"
                      type="text"
                      class="form-control"
                      placeholder="e.g., HTKJ"
                      maxlength="4"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Departure Date</label>
                    <input
                      v-model="form.departure_date"
                      type="date"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Return Date</label>
                    <input
                      v-model="form.return_date"
                      type="date"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Passengers</label>
                    <input
                      v-model="form.passengers"
                      type="number"
                      class="form-control"
                      min="1"
                      max="50"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Aircraft Type</label>
                    <input
                      v-model="form.aircraft_type"
                      type="text"
                      class="form-control"
                      placeholder="e.g., Cessna 172"
                    />
                  </div>

                  <div class="col-12 mb-3">
                    <label class="form-label">Route Description</label>
                    <textarea
                      v-model="form.route_description"
                      class="form-control"
                      rows="3"
                      placeholder="Detailed description of the flight route and requirements"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pricing Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-dollar-sign mr-2"></i>
                  Pricing Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Currency <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.currency_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.currency_id }"
                    >
                      <option value="">Select currency</option>
                      <option
                        v-for="currency in currencies"
                        :key="currency.id"
                        :value="currency.id"
                      >
                        {{ currency.code }} - {{ currency.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.currency_id" class="invalid-feedback">
                      {{ form.errors.currency_id }}
                    </div>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Total Amount <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.total_amount"
                      type="number"
                      step="0.01"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.total_amount }"
                      placeholder="0.00"
                    />
                    <div v-if="form.errors.total_amount" class="invalid-feedback">
                      {{ form.errors.total_amount }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-sticky-note mr-2"></i>
                  Additional Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12 mb-3">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea
                      v-model="form.terms_conditions"
                      class="form-control"
                      rows="4"
                      placeholder="Terms and conditions for this quotation"
                    ></textarea>
                  </div>

                  <div class="col-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea
                      v-model="form.notes"
                      class="form-control"
                      rows="3"
                      placeholder="Internal notes about this quotation"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="card">
              <div class="card-footer">
                <div class="d-flex justify-content-end">
                  <Link
                    :href="route('quotations.index')"
                    class="btn btn-secondary mr-2"
                  >
                    <i class="fas fa-times mr-1"></i>
                    Cancel
                  </Link>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="btn btn-primary"
                  >
                    <i class="fas fa-save mr-1"></i>
                    {{ form.processing ? 'Creating...' : 'Create Quotation' }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  customers: Array,
  currencies: Array,
  statuses: Array
})

// Generate quote number
const generateQuoteNumber = () => {
  const today = new Date()
  const year = today.getFullYear()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
  return `QT-${year}${month}${day}-${random}`
}

// Generate valid until date (30 days from today)
const getValidUntilDate = () => {
  const date = new Date()
  date.setDate(date.getDate() + 30)
  return date.toISOString().split('T')[0]
}

const form = useForm({
  quote_number: generateQuoteNumber(),
  quote_date: new Date().toISOString().split('T')[0],
  valid_until: getValidUntilDate(),
  customer_id: '',
  departure_airport: '',
  arrival_airport: '',
  departure_date: '',
  return_date: '',
  passengers: '',
  aircraft_type: '',
  route_description: '',
  currency_id: '1', // Default to USD
  total_amount: '',
  status_id: '1', // Default to Draft
  terms_conditions: '',
  notes: ''
})

const submit = () => {
  form.post(route('quotations.store'), {
    preserveScroll: true,
    onSuccess: () => {
      // Success message will be shown from the backend
    },
    onError: () => {
      // Validation errors will be displayed automatically
    }
  })
}
</script>