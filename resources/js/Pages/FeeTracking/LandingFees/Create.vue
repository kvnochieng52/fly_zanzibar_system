<template>
  <Head title="Add Landing Fee" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="lnr-airplane icon-gradient bg-plum-plate"></i>
        </div>
        <div>
          Add Landing Fee
          <div class="page-title-subheading">Create a new landing fee record for aircraft operations</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('landing-fees.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to List
        </Link>
      </div>
    </div>

    <!-- Form Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon lnr-airplane icon-gradient bg-plum-plate"></i>
        Landing Fee Details
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <!-- Flight Information Section -->
          <div class="row">
            <div class="col-md-12">
              <h5 class="card-title">Flight Information</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="flight_number" class="form-label required">Flight Number</label>
                <input
                  id="flight_number"
                  v-model="form.flight_number"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.flight_number }"
                  placeholder="e.g., ZF001"
                  required
                />
                <div v-if="errors.flight_number" class="invalid-feedback">
                  {{ errors.flight_number }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="flight_date" class="form-label required">Flight Date</label>
                <input
                  id="flight_date"
                  v-model="form.flight_date"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.flight_date }"
                  required
                />
                <div v-if="errors.flight_date" class="invalid-feedback">
                  {{ errors.flight_date }}
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
                  @change="onAircraftChange"
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
                  @change="calculateFee"
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

          <!-- Fee Calculation Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Fee Calculation</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="mtow_used" class="form-label required">MTOW Used (kg)</label>
                <input
                  id="mtow_used"
                  v-model="form.mtow_used"
                  type="number"
                  step="0.01"
                  class="form-control"
                  :class="{ 'is-invalid': errors.mtow_used }"
                  placeholder="e.g., 5500.00"
                  required
                  @input="calculateFee"
                />
                <div v-if="errors.mtow_used" class="invalid-feedback">
                  {{ errors.mtow_used }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="currency_id" class="form-label required">Currency</label>
                <select
                  id="currency_id"
                  v-model="form.currency_id"
                  class="form-control"
                  :class="{ 'is-invalid': errors.currency_id }"
                  required
                >
                  <option value="">Select Currency</option>
                  <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                    {{ currency.code }} - {{ currency.name }}
                  </option>
                </select>
                <div v-if="errors.currency_id" class="invalid-feedback">
                  {{ errors.currency_id }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="fee_amount" class="form-label required">Fee Amount</label>
                <div class="input-group">
                  <input
                    id="fee_amount"
                    v-model="form.fee_amount"
                    type="number"
                    step="0.01"
                    class="form-control"
                    :class="{ 'is-invalid': errors.fee_amount }"
                    placeholder="0.00"
                    required
                    :readonly="form.is_calculated_auto"
                  />
                  <div class="input-group-append">
                    <button
                      @click.prevent="toggleAutoCalculation"
                      type="button"
                      class="btn btn-outline-secondary"
                      :title="form.is_calculated_auto ? 'Switch to Manual' : 'Switch to Auto'"
                    >
                      <i :class="form.is_calculated_auto ? 'fa fa-calculator' : 'fa fa-edit'"></i>
                    </button>
                  </div>
                </div>
                <small class="form-text text-muted">
                  {{ form.is_calculated_auto ? 'Auto-calculated based on MTOW and airport rate' : 'Manual entry mode' }}
                </small>
                <div v-if="errors.fee_amount" class="invalid-feedback">
                  {{ errors.fee_amount }}
                </div>
              </div>
            </div>

            <div class="col-md-3" v-if="!form.is_calculated_auto">
              <div class="form-group">
                <label for="override_reason" class="form-label">Override Reason</label>
                <input
                  id="override_reason"
                  v-model="form.override_reason"
                  type="text"
                  class="form-control"
                  placeholder="Reason for manual override"
                />
              </div>
            </div>
          </div>

          <!-- Payment Information Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Payment Information</h5>
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
                <label for="receipt_reference" class="form-label">Receipt Reference</label>
                <input
                  id="receipt_reference"
                  v-model="form.receipt_reference"
                  type="text"
                  class="form-control"
                  placeholder="e.g., RCP-001"
                />
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
                  placeholder="e.g., INV-001"
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
                <label for="authority_document" class="form-label">Authority Document</label>
                <textarea
                  id="authority_document"
                  v-model="form.authority_document"
                  class="form-control"
                  rows="3"
                  placeholder="Reference to authority document or file path"
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
                  placeholder="Additional notes about this landing fee"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-between">
                <Link :href="route('landing-fees.index')" class="btn btn-secondary">
                  <i class="fa fa-times"></i> Cancel
                </Link>

                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <i class="fa fa-save"></i>
                  <span v-if="processing">Creating...</span>
                  <span v-else>Create Landing Fee</span>
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
  name: 'LandingFeeCreate',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    aircraft: Array,
    airports: Array,
    currencies: Array,
    paymentStatuses: Array,
  },

  setup(props) {
    const form = useForm({
      flight_number: '',
      flight_date: new Date().toISOString().split('T')[0], // Today's date
      aircraft_id: '',
      airport_id: '',
      mtow_used: '',
      fee_amount: '',
      currency_id: '',
      payment_status_id: '',
      payment_date: '',
      receipt_reference: '',
      invoice_reference: '',
      authority_document: '',
      notes: '',
      is_calculated_auto: true,
      manual_override_amount: '',
      override_reason: ''
    })

    return {
      form,
      errors: form.errors,
      processing: form.processing
    }
  },

  data() {
    return {
      selectedAircraft: null,
      selectedAirport: null,
    }
  },

  methods: {
    submit() {
      this.form.post(route('landing-fees.store'), {
        onSuccess: () => {
          // Success handled by controller redirect
        },
        onError: (errors) => {
          console.error('Form validation errors:', errors)
        }
      })
    },

    onAircraftChange() {
      this.selectedAircraft = this.aircraft.find(a => a.id == this.form.aircraft_id)
      this.calculateFee()
    },

    calculateFee() {
      if (!this.form.is_calculated_auto) return

      this.selectedAirport = this.airports.find(a => a.id == this.form.airport_id)

      if (this.form.mtow_used && this.selectedAirport && this.selectedAirport.landing_fee_rate) {
        const mtow = parseFloat(this.form.mtow_used)
        const rate = parseFloat(this.selectedAirport.landing_fee_rate)
        this.form.fee_amount = (mtow * rate).toFixed(2)
      }
    },

    toggleAutoCalculation() {
      this.form.is_calculated_auto = !this.form.is_calculated_auto

      if (this.form.is_calculated_auto) {
        this.form.manual_override_amount = ''
        this.form.override_reason = ''
        this.calculateFee()
      } else {
        this.form.manual_override_amount = this.form.fee_amount
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

.bg-plum-plate {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.form-label.required::after {
  content: ' *';
  color: #dc3545;
}

.card-title {
  color: #495057;
  font-weight: 600;
}

.input-group .btn {
  z-index: auto;
}
</style>