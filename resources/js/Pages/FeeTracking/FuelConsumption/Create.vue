<template>
  <Head title="Add Fuel Consumption Record" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-tachometer-alt icon-gradient bg-warning"></i>
        </div>
        <div>
          Add Fuel Consumption Record
          <div class="page-title-subheading">Record fuel consumption data for a specific flight</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('fuel-consumption.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to List
        </Link>
      </div>
    </div>

    <!-- Form Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-tachometer-alt icon-gradient bg-warning"></i>
        Fuel Consumption Details
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
                <label for="flight_hours" class="form-label required">Flight Hours</label>
                <input
                  id="flight_hours"
                  v-model="form.flight_hours"
                  type="number"
                  step="0.01"
                  class="form-control"
                  :class="{ 'is-invalid': errors.flight_hours }"
                  placeholder="0.00"
                  required
                  @input="calculateEfficiency"
                />
                <div v-if="errors.flight_hours" class="invalid-feedback">
                  {{ errors.flight_hours }}
                </div>
              </div>
            </div>
          </div>

          <!-- Route Information Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Route Information</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="route_from" class="form-label required">Departure Airport</label>
                <input
                  id="route_from"
                  v-model="form.route_from"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.route_from }"
                  placeholder="ICAO Code (e.g., HTDA)"
                  maxlength="4"
                  style="text-transform: uppercase"
                  required
                />
                <div v-if="errors.route_from" class="invalid-feedback">
                  {{ errors.route_from }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="route_to" class="form-label required">Arrival Airport</label>
                <input
                  id="route_to"
                  v-model="form.route_to"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.route_to }"
                  placeholder="ICAO Code (e.g., HTKJ)"
                  maxlength="4"
                  style="text-transform: uppercase"
                  required
                />
                <div v-if="errors.route_to" class="invalid-feedback">
                  {{ errors.route_to }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="distance_km" class="form-label required">Distance (km)</label>
                <input
                  id="distance_km"
                  v-model="form.distance_km"
                  type="number"
                  step="0.01"
                  class="form-control"
                  :class="{ 'is-invalid': errors.distance_km }"
                  placeholder="0.00"
                  required
                  @input="calculateEfficiency"
                />
                <div v-if="errors.distance_km" class="invalid-feedback">
                  {{ errors.distance_km }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="weather_conditions" class="form-label">Weather Conditions</label>
                <select
                  id="weather_conditions"
                  v-model="form.weather_conditions"
                  class="form-control"
                >
                  <option value="">Select Conditions</option>
                  <option value="Clear">Clear</option>
                  <option value="Cloudy">Cloudy</option>
                  <option value="Rainy">Rainy</option>
                  <option value="Windy">Windy</option>
                  <option value="Turbulent">Turbulent</option>
                  <option value="Severe Weather">Severe Weather</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Fuel Consumption Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Fuel Consumption</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="fuel_consumed" class="form-label required">Fuel Consumed</label>
                <input
                  id="fuel_consumed"
                  v-model="form.fuel_consumed"
                  type="number"
                  step="0.01"
                  class="form-control"
                  :class="{ 'is-invalid': errors.fuel_consumed }"
                  placeholder="0.00"
                  required
                  @input="calculateEfficiency"
                />
                <div v-if="errors.fuel_consumed" class="invalid-feedback">
                  {{ errors.fuel_consumed }}
                </div>
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
                    {{ unit.symbol }}
                  </option>
                </select>
                <div v-if="errors.fuel_unit_id" class="invalid-feedback">
                  {{ errors.fuel_unit_id }}
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="average_fuel_flow" class="form-label">Average Fuel Flow</label>
                <input
                  id="average_fuel_flow"
                  v-model="form.average_fuel_flow"
                  type="number"
                  step="0.01"
                  class="form-control bg-light"
                  placeholder="Auto-calculated"
                  readonly
                />
                <small class="form-text text-muted">Fuel consumed ÷ Flight hours</small>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="fuel_efficiency" class="form-label">Fuel Efficiency</label>
                <input
                  id="fuel_efficiency"
                  v-model="form.fuel_efficiency"
                  type="number"
                  step="0.0001"
                  class="form-control bg-light"
                  placeholder="Auto-calculated"
                  readonly
                />
                <small class="form-text text-muted">Distance ÷ Fuel consumed (km/L or km/gal)</small>
              </div>
            </div>
          </div>

          <!-- Load Information Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Load Information</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="passenger_count" class="form-label">Passenger Count</label>
                <input
                  id="passenger_count"
                  v-model="form.passenger_count"
                  type="number"
                  class="form-control"
                  placeholder="0"
                  min="0"
                />
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label for="cargo_weight" class="form-label">Cargo Weight (kg)</label>
                <input
                  id="cargo_weight"
                  v-model="form.cargo_weight"
                  type="number"
                  step="0.01"
                  class="form-control"
                  placeholder="0.00"
                  min="0"
                />
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
                  placeholder="Additional notes about this flight's fuel consumption"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-between">
                <Link :href="route('fuel-consumption.index')" class="btn btn-secondary">
                  <i class="fa fa-times"></i> Cancel
                </Link>

                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <i class="fa fa-save"></i>
                  <span v-if="processing">Creating...</span>
                  <span v-else>Create Consumption Record</span>
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
  name: 'FuelConsumptionCreate',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    aircraft: Array,
    fuelUnits: Array,
  },

  setup(props) {
    const form = useForm({
      flight_number: '',
      flight_date: new Date().toISOString().split('T')[0],
      aircraft_id: '',
      route_from: '',
      route_to: '',
      fuel_consumed: '',
      fuel_unit_id: '',
      flight_hours: '',
      distance_km: '',
      average_fuel_flow: '',
      fuel_efficiency: '',
      weather_conditions: '',
      passenger_count: '',
      cargo_weight: '',
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
      this.form.post(route('fuel-consumption.store'), {
        onSuccess: () => {
          // Success handled by controller redirect
        },
        onError: (errors) => {
          console.error('Form validation errors:', errors)
        }
      })
    },

    calculateEfficiency() {
      // Calculate average fuel flow (fuel consumed / flight hours)
      if (this.form.fuel_consumed && this.form.flight_hours) {
        const fuelConsumed = parseFloat(this.form.fuel_consumed)
        const flightHours = parseFloat(this.form.flight_hours)
        this.form.average_fuel_flow = (fuelConsumed / flightHours).toFixed(2)
      }

      // Calculate fuel efficiency (distance / fuel consumed)
      if (this.form.distance_km && this.form.fuel_consumed) {
        const distance = parseFloat(this.form.distance_km)
        const fuelConsumed = parseFloat(this.form.fuel_consumed)
        this.form.fuel_efficiency = (distance / fuelConsumed).toFixed(4)
      }
    }
  },

  watch: {
    'form.route_from'(newValue) {
      this.form.route_from = newValue.toUpperCase()
    },
    'form.route_to'(newValue) {
      this.form.route_to = newValue.toUpperCase()
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

.bg-warning {
  background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
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