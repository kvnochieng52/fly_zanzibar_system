<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Create Customer</h1>
              <p class="text-muted">Add a new customer to the database</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item">
                  <Link :href="route('customers.index')">Customers</Link>
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
            <!-- Customer Type -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-user-tag mr-2"></i>
                  Customer Type
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12 mb-3">
                    <label class="form-label">
                      Customer Type <span class="text-danger">*</span>
                    </label>
                    <div class="form-check">
                      <input
                        v-model="form.type"
                        class="form-check-input"
                        type="radio"
                        value="individual"
                        id="individual"
                      />
                      <label class="form-check-label" for="individual">
                        <i class="fas fa-user mr-1"></i>
                        Individual Customer
                      </label>
                    </div>
                    <div class="form-check">
                      <input
                        v-model="form.type"
                        class="form-check-input"
                        type="radio"
                        value="corporate"
                        id="corporate"
                      />
                      <label class="form-check-label" for="corporate">
                        <i class="fas fa-building mr-1"></i>
                        Corporate Customer
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Individual Customer Information -->
            <div v-if="form.type === 'individual'" class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-user mr-2"></i>
                  Personal Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <label class="form-label">Title</label>
                    <select
                      v-model="form.title"
                      class="form-control"
                    >
                      <option value="">Select</option>
                      <option value="Mr">Mr.</option>
                      <option value="Mrs">Mrs.</option>
                      <option value="Ms">Ms.</option>
                      <option value="Dr">Dr.</option>
                      <option value="Prof">Prof.</option>
                    </select>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">
                      First Name <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.first_name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.first_name }"
                    />
                    <div v-if="form.errors.first_name" class="invalid-feedback">
                      {{ form.errors.first_name }}
                    </div>
                  </div>

                  <div class="col-md-5 mb-3">
                    <label class="form-label">
                      Last Name <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.last_name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.last_name }"
                    />
                    <div v-if="form.errors.last_name" class="invalid-feedback">
                      {{ form.errors.last_name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Corporate Customer Information -->
            <div v-if="form.type === 'corporate'" class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-building mr-2"></i>
                  Company Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Company Name <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.company_name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.company_name }"
                    />
                    <div v-if="form.errors.company_name" class="invalid-feedback">
                      {{ form.errors.company_name }}
                    </div>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Person</label>
                    <input
                      v-model="form.contact_person"
                      type="text"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Tax Number</label>
                    <input
                      v-model="form.tax_number"
                      type="text"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Registration Number</label>
                    <input
                      v-model="form.registration_number"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-phone mr-2"></i>
                  Contact Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Email <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.email"
                      type="email"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.email }"
                    />
                    <div v-if="form.errors.email" class="invalid-feedback">
                      {{ form.errors.email }}
                    </div>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input
                      v-model="form.phone"
                      type="tel"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Mobile</label>
                    <input
                      v-model="form.mobile"
                      type="tel"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Fax</label>
                    <input
                      v-model="form.fax"
                      type="tel"
                      class="form-control"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Address Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-map-marker-alt mr-2"></i>
                  Address Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12 mb-3">
                    <label class="form-label">Address Line 1</label>
                    <input
                      v-model="form.address_line_1"
                      type="text"
                      class="form-control"
                    />
                  </div>

                  <div class="col-12 mb-3">
                    <label class="form-label">Address Line 2</label>
                    <input
                      v-model="form.address_line_2"
                      type="text"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input
                      v-model="form.city"
                      type="text"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">State/Province</label>
                    <input
                      v-model="form.state_province"
                      type="text"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Postal Code</label>
                    <input
                      v-model="form.postal_code"
                      type="text"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6 mb-3">
                    <label class="form-label">Country</label>
                    <input
                      v-model="form.country"
                      type="text"
                      class="form-control"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Business Settings -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-cog mr-2"></i>
                  Business Settings
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Terms</label>
                    <select
                      v-model="form.payment_terms"
                      class="form-control"
                    >
                      <option value="immediate">Immediate</option>
                      <option value="net_7">Net 7 Days</option>
                      <option value="net_15">Net 15 Days</option>
                      <option value="net_30">Net 30 Days</option>
                      <option value="net_45">Net 45 Days</option>
                      <option value="net_60">Net 60 Days</option>
                    </select>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Credit Limit</label>
                    <input
                      v-model="form.credit_limit"
                      type="number"
                      step="0.01"
                      class="form-control"
                      placeholder="0.00"
                    />
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Default Currency</label>
                    <select
                      v-model="form.default_currency_id"
                      class="form-control"
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
                  </div>

                  <div class="col-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea
                      v-model="form.notes"
                      class="form-control"
                      rows="3"
                      placeholder="Internal notes about this customer"
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
                    :href="route('customers.index')"
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
                    {{ form.processing ? 'Creating...' : 'Create Customer' }}
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
  currencies: Array
})

// Generate customer code
const generateCustomerCode = () => {
  const timestamp = Date.now().toString().slice(-6)
  return `CUS-${timestamp}`
}

const form = useForm({
  customer_code: generateCustomerCode(),
  type: 'individual',
  title: '',
  first_name: '',
  last_name: '',
  company_name: '',
  contact_person: '',
  tax_number: '',
  registration_number: '',
  email: '',
  phone: '',
  mobile: '',
  fax: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state_province: '',
  postal_code: '',
  country: '',
  payment_terms: 'net_30',
  credit_limit: '',
  default_currency_id: '1',
  notes: ''
})

const submit = () => {
  form.post(route('customers.store'), {
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