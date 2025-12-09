<template>
  <Head title="Add Fuel Purchase" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-gas-pump icon-gradient bg-success"></i>
        </div>
        <div>
          Add Fuel Purchase
          <div class="page-title-subheading">Record a new fuel purchase transaction</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('fuel-purchases.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to List
        </Link>
      </div>
    </div>

    <!-- Form Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-gas-pump icon-gradient bg-success"></i>
        Fuel Purchase Details
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <!-- Purchase Information Section -->
          <div class="row">
            <div class="col-md-12">
              <h5 class="card-title">Purchase Information</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="purchase_number" class="form-label required">Purchase Number</label>
                <input
                  id="purchase_number"
                  v-model="form.purchase_number"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.purchase_number }"
                  placeholder="e.g., FP-001"
                  required
                />
                <div v-if="errors.purchase_number" class="invalid-feedback">
                  {{ errors.purchase_number }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="purchase_date" class="form-label required">Purchase Date</label>
                <input
                  id="purchase_date"
                  v-model="form.purchase_date"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.purchase_date }"
                  required
                />
                <div v-if="errors.purchase_date" class="invalid-feedback">
                  {{ errors.purchase_date }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="aircraft_id" class="form-label required">Aircraft</label>
                <select
                  id="aircraft_id"
                  v-model="form.aircraft_id"
                  class="form-control"
                  :class="{ 'is-invalid': errors.aircraft_id }"
                  required
                >
                  <option value="">Select Aircraft</option>
                  <option v-for="aircraft in aircraft" :key="aircraft.id" :value="aircraft.id">
                    {{ aircraft.registration_number }} - {{ aircraft.manufacturer?.name }} {{ aircraft.model?.name }}
                  </option>
                </select>
                <div v-if="errors.aircraft_id" class="invalid-feedback">
                  {{ errors.aircraft_id }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="airport_id" class="form-label required">Airport</label>
                <select
                  id="airport_id"
                  v-model="form.airport_id"
                  class="form-control"
                  :class="{ 'is-invalid': errors.airport_id }"
                  required
                >
                  <option value="">Select Airport</option>
                  <option v-for="airport in airports" :key="airport.id" :value="airport.id">
                    {{ airport.icao_code }} - {{ airport.name }}
                  </option>
                </select>
                <div v-if="errors.airport_id" class="invalid-feedback">
                  {{ errors.airport_id }}
                </div>
              </div>
            </div>
          </div>

          <!-- Fuel Details Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Fuel Details</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="fuel_supplier_id" class="form-label required">Fuel Supplier</label>
                <select
                  id="fuel_supplier_id"
                  v-model="form.fuel_supplier_id"
                  class="form-control"
                  :class="{ 'is-invalid': errors.fuel_supplier_id }"
                  required
                >
                  <option value="">Select Supplier</option>
                  <option v-for="supplier in fuelSuppliers" :key="supplier.id" :value="supplier.id">
                    {{ supplier.name }}
                  </option>
                </select>
                <div v-if="errors.fuel_supplier_id" class="invalid-feedback">
                  {{ errors.fuel_supplier_id }}
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label for="fuel_grade" class="form-label">Fuel Grade</label>
                <select
                  id="fuel_grade"
                  v-model="form.fuel_grade"
                  class="form-control"
                >
                  <option value="">Select Grade</option>
                  <option value="Jet A-1">Jet A-1</option>
                  <option value="AvGas 100LL">AvGas 100LL</option>
                  <option value="AvGas 91/96">AvGas 91/96</option>
                  <option value="Jet A">Jet A</option>
                </select>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label for="fuel_unit_id" class="form-label required">Unit</label>
                <select
                  id="fuel_unit_id"
                  v-model="form.fuel_unit_id"
                  class="form-control"
                  :class="{ 'is-invalid': errors.fuel_unit_id }"
                  required
                >
                  <option value="">Select Unit</option>
                  <option v-for="unit in fuelUnits" :key="unit.id" :value="unit.id">
                    {{ unit.name }} ({{ unit.symbol }})
                  </option>
                </select>
                <div v-if="errors.fuel_unit_id" class="invalid-feedback">
                  {{ errors.fuel_unit_id }}
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label for="quantity" class="form-label required">Quantity</label>
                <input
                  id="quantity"
                  v-model="form.quantity"
                  type="number"
                  step="0.01"
                  class="form-control"
                  :class="{ 'is-invalid': errors.quantity }"
                  placeholder="0.00"
                  required
                  @input="calculateTotal"
                />
                <div v-if="errors.quantity" class="invalid-feedback">
                  {{ errors.quantity }}
                </div>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label for="unit_price" class="form-label required">Unit Price</label>
                <input
                  id="unit_price"
                  v-model="form.unit_price"
                  type="number"
                  step="0.0001"
                  class="form-control"
                  :class="{ 'is-invalid': errors.unit_price }"
                  placeholder="0.0000"
                  required
                  @input="calculateTotal"
                />
                <div v-if="errors.unit_price" class="invalid-feedback">
                  {{ errors.unit_price }}
                </div>
              </div>
            </div>

            <div class="col-md-1">
              <div class="form-group">
                <label for="currency_id" class="form-label required">Currency</label>
                <select
                  id="currency_id"
                  v-model="form.currency_id"
                  class="form-control"
                  :class="{ 'is-invalid': errors.currency_id }"
                  required
                >
                  <option value="">Currency</option>
                  <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                    {{ currency.code }}
                  </option>
                </select>
                <div v-if="errors.currency_id" class="invalid-feedback">
                  {{ errors.currency_id }}
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="total_amount" class="form-label required">Total Amount</label>
                <input
                  id="total_amount"
                  v-model="form.total_amount"
                  type="number"
                  step="0.01"
                  class="form-control bg-light"
                  :class="{ 'is-invalid': errors.total_amount }"
                  readonly
                  required
                />
                <small class="form-text text-muted">Auto-calculated: Quantity × Unit Price</small>
                <div v-if="errors.total_amount" class="invalid-feedback">
                  {{ errors.total_amount }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="density" class="form-label">Fuel Density (kg/L)</label>
                <input
                  id="density"
                  v-model="form.density"
                  type="number"
                  step="0.0001"
                  class="form-control"
                  placeholder="0.8000"
                />
                <small class="form-text text-muted">At 15°C reference temperature</small>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="fuel_quality_rating" class="form-label">Quality Rating</label>
                <input
                  id="fuel_quality_rating"
                  v-model="form.fuel_quality_rating"
                  type="number"
                  step="0.01"
                  min="0"
                  max="10"
                  class="form-control"
                  placeholder="9.50"
                />
                <small class="form-text text-muted">Quality score (0-10)</small>
              </div>
            </div>
          </div>

          <!-- Payment Information Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Payment & Documentation</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="payment_status_id" class="form-label required">Payment Status</label>
                <select
                  id="payment_status_id"
                  v-model="form.payment_status_id"
                  class="form-control"
                  :class="{ 'is-invalid': errors.payment_status_id }"
                  required
                >
                  <option value="">Select Status</option>
                  <option v-for="status in paymentStatuses" :key="status.id" :value="status.id">
                    {{ status.name }}
                  </option>
                </select>
                <div v-if="errors.payment_status_id" class="invalid-feedback">
                  {{ errors.payment_status_id }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input
                  id="payment_date"
                  v-model="form.payment_date"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.payment_date }"
                />
                <div v-if="errors.payment_date" class="invalid-feedback">
                  {{ errors.payment_date }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="invoice_reference" class="form-label">Invoice Reference</label>
                <input
                  id="invoice_reference"
                  v-model="form.invoice_reference"
                  type="text"
                  class="form-control"
                  placeholder="e.g., INV-FP-001"
                />
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="invoice_date" class="form-label">Invoice Date</label>
                <input
                  id="invoice_date"
                  v-model="form.invoice_date"
                  type="date"
                  class="form-control"
                />
              </div>
            </div>
          </div>

          <!-- Additional Information Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Additional Information</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="supplier_document" class="form-label">Supplier Document</label>
                <textarea
                  id="supplier_document"
                  v-model="form.supplier_document"
                  class="form-control"
                  rows="3"
                  placeholder="Reference to supplier document, delivery note, or file path"
                ></textarea>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="notes" class="form-label">Notes</label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  class="form-control"
                  rows="3"
                  placeholder="Additional notes about this fuel purchase"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-between">
                <Link :href="route('fuel-purchases.index')" class="btn btn-secondary">
                  <i class="fa fa-times"></i> Cancel
                </Link>

                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <i class="fa fa-save"></i>
                  <span v-if="processing">Creating...</span>
                  <span v-else">Create Fuel Purchase</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'FuelPurchaseCreate',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    aircraft: Array,
    airports: Array,
    fuelSuppliers: Array,
    fuelUnits: Array,
    currencies: Array,
    paymentStatuses: Array,
  },

  setup(props) {
    const form = useForm({
      purchase_number: '',
      purchase_date: new Date().toISOString().split('T')[0],
      aircraft_id: '',
      airport_id: '',
      fuel_supplier_id: '',
      fuel_unit_id: '',
      quantity: '',
      unit_price: '',
      total_amount: '',
      currency_id: '',
      invoice_reference: '',
      invoice_date: '',
      payment_status_id: '',
      payment_date: '',
      supplier_document: '',
      fuel_grade: 'Jet A-1',
      density: '',
      fuel_quality_rating: '',
      notes: ''
    })

    return {
      form,
      errors: form.errors,
      processing: form.processing
    }
  },

  methods: {
    submit() {
      this.form.post(route('fuel-purchases.store'), {
        onSuccess: () => {
          // Success handled by controller redirect
        },
        onError: (errors) => {
          console.error('Form validation errors:', errors)
        }
      })
    },

    calculateTotal() {
      if (this.form.quantity && this.form.unit_price) {
        const quantity = parseFloat(this.form.quantity)
        const unitPrice = parseFloat(this.form.unit_price)
        this.form.total_amount = (quantity * unitPrice).toFixed(2)
      }
    }
  }
}
</script>

<style scoped>
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

.form-label.required::after {
  content: ' *';
  color: #dc3545;
}

.card-title {
  color: #495057;
  font-weight: 600;
}
</style>