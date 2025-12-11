<template>
  <Head :title="`Flight ${flight.flight_code}`" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Flight {{ flight.flight_code }}</h1>
        <p class="mb-0 text-muted">
          Complete flight information and management
        </p>
      </div>
      <div class="d-flex gap-2">
        <Link
          :href="route('scheduled-flights.edit', flight.id)"
          class="btn btn-warning"
        >
          <i class="fas fa-edit me-2"></i>Edit Flight
        </Link>
        <Link
          :href="route('scheduled-flights.index')"
          class="btn btn-outline-secondary"
        >
          <i class="fas fa-arrow-left me-2"></i>Back to Flights
        </Link>
      </div>
    </div>

    <!-- Flight Overview Banner -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body py-3">
        <div class="row align-items-center">
          <div class="col-md-3 text-center">
            <h2 class="text-primary fw-bold mb-1">{{ flight.flight_code }}</h2>
            <span
              class="badge px-3 py-2"
              :style="{
                backgroundColor: flight.flight_status?.color || '#6c757d',
                color: 'white',
              }"
            >
              {{ flight.flight_status?.name || "Unknown" }}
            </span>
          </div>
          <div class="col-md-6 text-center">
            <div class="d-flex align-items-center justify-content-center">
              <div class="text-center">
                <strong class="fs-4">{{ getRouteOverview() }}</strong>
                <br />
                <small class="text-muted">{{ flightDuration }}</small>
              </div>
            </div>
          </div>
          <div class="col-md-3 text-center">
            <div class="row">
              <div class="col-6">
                <div class="text-primary fw-bold fs-5">
                  {{ flight.passenger_count || 0 }}
                </div>
                <small class="text-muted">Passengers</small>
              </div>
              <div class="col-6">
                <div class="text-success fw-bold fs-5">
                  {{ flight.cargo_items_count || 0 }}
                </div>
                <small class="text-muted">Cargo Items</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent">
        <ul class="nav nav-pills" id="flightTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'details' }]"
              id="flight-details-tab"
              type="button"
              role="tab"
              @click="activeTab = 'details'"
            >
              <i class="fas fa-plane me-2"></i>Flight Details
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              :class="[
                'nav-link',
                { active: activeTab === 'cargo-passengers' },
              ]"
              id="cargo-passengers-tab"
              type="button"
              role="tab"
              @click="activeTab = 'cargo-passengers'"
            >
              <i class="fas fa-users me-2"></i>Cargo & Passenger Details
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'flight-fees' }]"
              id="flight-fees-tab"
              type="button"
              role="tab"
              @click="activeTab = 'flight-fees'"
            >
              <i class="fas fa-dollar-sign me-2"></i>Flight Fees
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <div class="tab-content" id="flightTabsContent">
          <!-- Flight Details Tab -->

          <div
            v-show="activeTab === 'flight-fees'"
            id="flight-fees3"
            role="tabpanel"
          >
            <div class="row">
              <!-- Landing Fees Section -->
              <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                  <div
                    class="card-header bg-light border-bottom d-flex align-items-center"
                  >
                    <h5 class="mb-0 flex-grow-1">
                      <i class="fas fa-plane-departure me-2 text-muted"></i
                      >Landing Fees
                    </h5>
                    <button
                      @click="showLandingFeeForm = !showLandingFeeForm"
                      class="btn btn-sm btn-success ms-auto"
                    >
                      <i class="fas fa-plus me-1"></i
                      >{{ showLandingFeeForm ? "Cancel" : "Add Landing Fee" }}
                    </button>
                  </div>
                  <div class="card-body">
                    <!-- Add Landing Fee Form -->
                    <div
                      v-if="showLandingFeeForm"
                      class="mb-4 p-3 border rounded bg-light"
                    >
                      <h6 class="mb-3">Add Landing Fee</h6>
                      <form @submit.prevent="addLandingFee">
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label"
                              >Airport <span class="text-danger">*</span></label
                            >
                            <select
                              v-model="newLandingFee.airport_id"
                              class="form-select custom-select"
                              required
                            >
                              <option value="">Select Airport</option>
                              <option
                                v-for="airport in airports || []"
                                :key="airport.id"
                                :value="airport.id"
                              >
                                {{ airport.name }} ({{ airport.code }})
                              </option>
                            </select>
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label"
                              >MTOW Used (kg)
                              <span class="text-danger">*</span></label
                            >
                            <input
                              v-model="newLandingFee.mtow_used_kg"
                              type="number"
                              step="0.01"
                              class="form-control"
                              placeholder="e.g., 5700.00"
                              required
                            />
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label"
                              >Fee Amount
                              <span class="text-danger">*</span></label
                            >
                            <input
                              v-model="newLandingFee.fee_amount"
                              type="number"
                              step="0.01"
                              class="form-control"
                              placeholder="e.g., 450.00"
                              required
                            />
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label">Currency</label>
                            <select
                              v-model="newLandingFee.currency"
                              class="form-select custom-select"
                            >
                              <option value="USD">USD</option>
                              <option value="EUR">EUR</option>
                              <option value="TZS">TZS</option>
                              <option value="KES">KES</option>
                            </select>
                          </div>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Notes</label>
                          <textarea
                            v-model="newLandingFee.notes"
                            class="form-control"
                            rows="2"
                            placeholder="Additional notes about this landing fee"
                          ></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                          <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            @click="showLandingFeeForm = false"
                          >
                            Cancel
                          </button>
                          <button
                            type="submit"
                            class="btn btn-primary btn-sm"
                            :disabled="addingLandingFee"
                          >
                            <span v-if="addingLandingFee">
                              <i class="fas fa-spinner fa-spin me-1"></i
                              >Adding...
                            </span>
                            <span v-else>Add Fee</span>
                          </button>
                        </div>
                      </form>
                    </div>

                    <!-- Landing Fees List -->
                    <div
                      v-if="
                        flight.landing_fees && flight.landing_fees.length > 0
                      "
                    >
                      <div class="table-responsive">
                        <table class="table table-sm">
                          <thead>
                            <tr>
                              <th>Airport</th>
                              <th>MTOW (kg)</th>
                              <th>Amount</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="fee in flight.landing_fees"
                              :key="fee.id"
                            >
                              <td>
                                <strong>{{
                                  fee.airport?.name || "Unknown"
                                }}</strong>
                                <br />
                                <small class="text-muted">{{
                                  fee.airport?.code || "N/A"
                                }}</small>
                              </td>
                              <td>{{ formatNumber(fee.mtow_used_kg) }}</td>
                              <td>
                                <strong
                                  >{{ fee.currency }}
                                  {{ formatNumber(fee.fee_amount) }}</strong
                                >
                              </td>
                              <td>
                                <button
                                  @click="deleteLandingFee(fee.id)"
                                  class="btn btn-sm btn-outline-danger"
                                  title="Delete fee"
                                >
                                  <i class="fas fa-trash"></i>
                                </button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <!-- Total Landing Fees -->
                      <div class="mt-3 p-2 bg-light rounded">
                        <div
                          class="d-flex justify-content-between align-items-center"
                        >
                          <strong>Total Landing Fees:</strong>
                          <strong class="text-primary">{{
                            calculateTotalLandingFees()
                          }}</strong>
                        </div>
                      </div>
                    </div>

                    <div v-else class="text-center text-muted py-3">
                      <i class="fas fa-info-circle me-2"></i>No landing fees
                      recorded yet
                    </div>
                  </div>
                </div>
              </div>

              <!-- Fuel Consumption Section -->
              <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                  <div
                    class="card-header bg-light border-bottom d-flex align-items-center"
                  >
                    <h5 class="mb-0 flex-grow-1">
                      <i class="fas fa-gas-pump me-2 text-muted"></i>Fuel
                      Consumption
                    </h5>
                    <button
                      @click="
                        showFuelConsumptionForm = !showFuelConsumptionForm
                      "
                      class="btn btn-sm btn-success ms-auto"
                    >
                      <i class="fas fa-plus me-1"></i
                      >{{
                        showFuelConsumptionForm ? "Cancel" : "Add Fuel Data"
                      }}
                    </button>
                  </div>
                  <div class="card-body">
                    <!-- Add Fuel Consumption Form -->
                    <div
                      v-if="showFuelConsumptionForm"
                      class="mb-4 p-3 border rounded bg-light"
                    >
                      <h6 class="mb-3">Add Fuel Consumption Data</h6>
                      <form @submit.prevent="addFuelConsumption">
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label"
                              >Fuel Consumed
                              <span class="text-danger">*</span></label
                            >
                            <input
                              v-model="newFuelConsumption.fuel_consumed"
                              type="number"
                              step="0.01"
                              class="form-control"
                              placeholder="e.g., 850.50"
                              required
                            />
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label">Unit</label>
                            <select
                              v-model="newFuelConsumption.fuel_unit"
                              class="form-select custom-select"
                            >
                              <option value="liters">Liters</option>
                              <option value="gallons">Gallons</option>
                              <option value="kg">Kilograms</option>
                            </select>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label">Distance (km)</label>
                            <input
                              v-model="newFuelConsumption.distance_km"
                              type="number"
                              step="0.01"
                              class="form-control"
                              :placeholder="
                                (calculateTotalDistance() || '0') +
                                ' km (calculated)'
                              "
                              readonly
                            />
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label"
                              >Flight Time (hours)</label
                            >
                            <input
                              v-model="newFuelConsumption.flight_time_hours"
                              type="number"
                              step="0.01"
                              class="form-control"
                              placeholder="e.g., 4.5"
                            />
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label">Bloc Time (hours)</label>
                            <input
                              v-model="newFuelConsumption.bloc_time_hours"
                              type="number"
                              step="0.01"
                              class="form-control"
                              :placeholder="
                                (calculateBlocTime() || '0.0') +
                                ' hours (calculated)'
                              "
                              readonly
                            />
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label">Total Time (hours)</label>
                            <input
                              v-model="newFuelConsumption.total_time_hours"
                              type="number"
                              step="0.01"
                              class="form-control"
                              :placeholder="
                                (calculateTotalTime() || '0.0') +
                                ' hours (calculated)'
                              "
                              readonly
                            />
                          </div>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Notes</label>
                          <textarea
                            v-model="newFuelConsumption.notes"
                            class="form-control"
                            rows="2"
                            placeholder="Additional notes about fuel consumption"
                          ></textarea>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                          <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            @click="showFuelConsumptionForm = false"
                          >
                            Cancel
                          </button>
                          <button
                            type="submit"
                            class="btn btn-success btn-sm"
                            :disabled="addingFuelConsumption"
                          >
                            <span v-if="addingFuelConsumption">
                              <i class="fas fa-spinner fa-spin me-1"></i
                              >Adding...
                            </span>
                            <span v-else>Add Data</span>
                          </button>
                        </div>
                      </form>
                    </div>

                    <!-- Fuel Consumption List -->
                    <div
                      v-if="
                        flight.fuel_consumption &&
                        flight.fuel_consumption.length > 0
                      "
                    >
                      <div
                        v-for="fuel in flight.fuel_consumption"
                        :key="fuel.id"
                        class="border rounded p-3 mb-3"
                      >
                        <div class="d-flex align-items-center">
                          <div class="flex-grow-1">
                            <strong
                              >{{ formatNumber(fuel.fuel_consumed) }}
                              {{ fuel.fuel_unit }}</strong
                            >
                            <br />
                            <small class="text-muted">Fuel Consumed</small>
                          </div>
                          <button
                            @click="deleteFuelConsumption(fuel.id)"
                            class="btn btn-sm btn-outline-danger ms-auto"
                            title="Delete record"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                        <div class="row mt-2">
                          <div class="col-md-4">
                            <small class="text-muted">Distance:</small><br />
                            <span>{{ formatNumber(fuel.distance_km) }} km</span>
                          </div>
                          <div class="col-md-4">
                            <small class="text-muted">Flight Time:</small><br />
                            <span>{{
                              formatTime(fuel.flight_time_hours)
                            }}</span>
                          </div>
                          <div class="col-md-4">
                            <small class="text-muted">Total Time:</small><br />
                            <span>{{ formatTime(fuel.total_time_hours) }}</span>
                          </div>
                        </div>
                        <div class="row mt-2">
                          <div class="col-md-6">
                            <small class="text-muted">Efficiency:</small><br />
                            <span>{{
                              fuel.fuel_efficiency
                                ? formatNumber(fuel.fuel_efficiency) +
                                  " km/" +
                                  fuel.fuel_unit
                                : "N/A"
                            }}</span>
                          </div>
                          <div class="col-md-6">
                            <small class="text-muted">Burn Rate:</small><br />
                            <span>{{
                              fuel.fuel_burn_rate
                                ? formatNumber(fuel.fuel_burn_rate) +
                                  " " +
                                  fuel.fuel_unit +
                                  "/hr"
                                : "N/A"
                            }}</span>
                          </div>
                        </div>
                        <div v-if="fuel.notes" class="mt-2">
                          <small class="text-muted">Notes:</small><br />
                          <span>{{ fuel.notes }}</span>
                        </div>
                      </div>
                    </div>

                    <div v-else class="text-center text-muted py-3">
                      <i class="fas fa-info-circle me-2"></i>No fuel consumption
                      data recorded yet
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Flight Summary Card -->
            <div class="row">
              <div class="col-12">
                <div class="card border-0 shadow-sm">
                  <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">
                      <i class="fas fa-calculator me-2 text-muted"></i>Flight
                      Cost Summary
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="text-center p-3 border-end">
                          <h5 class="text-primary mb-1">
                            {{ calculateTotalDistance() }} km
                          </h5>
                          <p class="text-muted mb-0 small">Total Distance</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="text-center p-3 border-end">
                          <h5 class="text-success mb-1">
                            {{ calculateTotalLandingFees() }}
                          </h5>
                          <p class="text-muted mb-0 small">Total Landing Fees</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="text-center p-3">
                          <h5 class="text-warning mb-1">
                            {{ calculateTotalFlightTime() }}
                          </h5>
                          <p class="text-muted mb-0 small">Total Flight Time</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            v-show="activeTab === 'details'"
            id="flight-details"
            role="tabpanel"
          >
            <div class="row">
              <div class="col-lg-8">
                <!-- Route Information -->
                <div class="card border shadow-sm mb-3">
                  <div class="card-header bg-success text-white py-2">
                    <h5 class="mb-0">
                      <i class="fas fa-route me-2"></i>Route Information
                    </h5>
                  </div>
                  <div class="card-body py-2 px-3">
                    <!-- Single Route Display -->
                    <div
                      v-if="
                        flight.schedule_routes &&
                        flight.schedule_routes.length === 1
                      "
                      class="row text-center"
                    >
                      <div class="col-md-5">
                        <div class="p-3">
                          <h3 class="text-success mb-1">
                            {{
                              getAirportCode(
                                flight.schedule_routes[0].origin_airport
                              )
                            }}
                          </h3>
                          <h5 class="mb-2">
                            {{
                              flight.schedule_routes[0].origin_airport?.name ||
                              "Unknown Airport"
                            }}
                          </h5>
                          <p class="text-muted mb-0">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{
                              flight.schedule_routes[0].origin_airport?.city ||
                              "Unknown"
                            }},
                            {{
                              flight.schedule_routes[0].origin_airport
                                ?.country || "Unknown"
                            }}
                          </p>
                        </div>
                      </div>
                      <div
                        class="col-md-2 d-flex align-items-center justify-content-center"
                      >
                        <div class="text-center">
                          <i class="fas fa-plane fa-2x text-primary mb-2"></i>
                          <br />
                          <small class="text-muted">{{ flightDuration }}</small>
                        </div>
                      </div>
                      <div class="col-md-5">
                        <div class="p-3">
                          <h3 class="text-success mb-1">
                            {{
                              getAirportCode(
                                flight.schedule_routes[0].destination_airport
                              )
                            }}
                          </h3>
                          <h5 class="mb-2">
                            {{
                              flight.schedule_routes[0].destination_airport
                                ?.name || "Unknown Airport"
                            }}
                          </h5>
                          <p class="text-muted mb-0">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{
                              flight.schedule_routes[0].destination_airport
                                ?.city || "Unknown"
                            }},
                            {{
                              flight.schedule_routes[0].destination_airport
                                ?.country || "Unknown"
                            }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Multi-Segment Route Display -->
                    <div
                      v-else-if="
                        flight.schedule_routes &&
                        flight.schedule_routes.length > 1
                      "
                    >
                      <div class="row mb-3">
                        <div class="col-12">
                          <div class="text-center mb-3">
                            <span class="badge bg-info fs-6 px-3 py-2">
                              Multi-Segment Flight ({{
                                flight.schedule_routes.length
                              }}
                              segments)
                            </span>
                          </div>
                        </div>
                      </div>

                      <div class="route-segments">
                        <div
                          v-for="(route, index) in flight.schedule_routes"
                          :key="route.id || index"
                          class="route-segment mb-3 p-3 border rounded"
                        >
                          <div class="row align-items-center">
                            <div class="col-md-1 text-center">
                              <span class="badge bg-primary rounded-circle">
                                {{ index + 1 }}
                              </span>
                            </div>
                            <div class="col-md-4 text-center">
                              <h5 class="text-success mb-1">
                                {{ getAirportCode(route.origin_airport) }}
                              </h5>
                              <small class="text-muted">{{
                                route.origin_airport?.name || "Unknown"
                              }}</small>
                            </div>
                            <div class="col-md-2 text-center">
                              <i class="fas fa-arrow-right text-primary"></i>
                              <br />
                              <small class="text-muted">{{
                                route.formatted_duration || "N/A"
                              }}</small>
                            </div>
                            <div class="col-md-4 text-center">
                              <h5 class="text-success mb-1">
                                {{ getAirportCode(route.destination_airport) }}
                              </h5>
                              <small class="text-muted">{{
                                route.destination_airport?.name || "Unknown"
                              }}</small>
                            </div>
                            <div class="col-md-1 text-center">
                              <small class="text-muted">
                                <div>
                                  {{ formatTime(route.departure_time) }}
                                </div>
                                <div>{{ formatTime(route.arrival_time) }}</div>
                              </small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Schedule Information -->
                <div class="card border shadow-sm mb-3">
                  <div class="card-header bg-light text-dark py-2">
                    <h5 class="mb-0">
                      <i class="fas fa-clock me-2"></i>Schedule Information
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                          <i class="fas fa-calendar-alt me-2"></i>Scheduled
                          Times
                        </h6>
                        <dl class="row">
                          <dt class="col-sm-5">Departure:</dt>
                          <dd class="col-sm-7">
                            {{ formatDateTime(flight.total_departure_time) }}
                          </dd>

                          <dt class="col-sm-5">Arrival:</dt>
                          <dd class="col-sm-7">
                            {{ formatDateTime(flight.total_arrival_time) }}
                          </dd>

                          <dt class="col-sm-5">Duration:</dt>
                          <dd class="col-sm-7">{{ flightDuration }}</dd>

                          <dt class="col-sm-5">Segments:</dt>
                          <dd class="col-sm-7">
                            {{
                              flight.total_segments ||
                              flight.schedule_routes?.length ||
                              "N/A"
                            }}
                          </dd>
                        </dl>
                      </div>

                      <div class="col-md-6">
                        <h6 class="text-success mb-3">
                          <i class="fas fa-check-circle me-2"></i>Actual Times
                        </h6>
                        <dl class="row">
                          <dt class="col-sm-5">Departure:</dt>
                          <dd class="col-sm-7">
                            <span
                              v-if="getFirstActualDeparture()"
                              class="text-success"
                            >
                              {{ formatDateTime(getFirstActualDeparture()) }}
                            </span>
                            <span v-else class="text-muted"
                              >Not departed yet</span
                            >
                          </dd>

                          <dt class="col-sm-5">Arrival:</dt>
                          <dd class="col-sm-7">
                            <span
                              v-if="getLastActualArrival()"
                              class="text-success"
                            >
                              {{ formatDateTime(getLastActualArrival()) }}
                            </span>
                            <span v-else class="text-muted"
                              >Not arrived yet</span
                            >
                          </dd>

                          <dt class="col-sm-5">Status:</dt>
                          <dd class="col-sm-7">
                            <span v-if="isOnTime" class="text-success">
                              <i class="fas fa-check me-1"></i>On Time
                            </span>
                            <span v-else-if="isDelayed" class="text-warning">
                              <i class="fas fa-clock me-1"></i>Delayed
                            </span>
                            <span v-else class="text-muted">
                              <i class="fas fa-minus me-1"></i>TBD
                            </span>
                          </dd>
                        </dl>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Flight Details Sidebar -->
              <div class="col-lg-4">
                <!-- Aircraft Information -->
                <div class="card border shadow-sm mb-3" v-if="flight.aircraft">
                  <div class="card-header bg-light text-dark py-2">
                    <h5 class="mb-0">
                      <i class="fas fa-plane me-2"></i>Aircraft Details
                    </h5>
                  </div>
                  <div class="card-body">
                    <dl class="row mb-0">
                      <dt class="col-sm-6">Registration:</dt>
                      <dd class="col-sm-6">
                        {{ flight.aircraft.registration_number }}
                      </dd>

                      <dt class="col-sm-6">Manufacturer:</dt>
                      <dd class="col-sm-6">
                        {{ flight.aircraft.manufacturer?.name || "N/A" }}
                      </dd>

                      <dt class="col-sm-6">Model:</dt>
                      <dd class="col-sm-6">
                        {{ flight.aircraft.model?.name || "N/A" }}
                      </dd>

                      <dt class="col-sm-6">Capacity:</dt>
                      <dd class="col-sm-6">
                        {{ flight.capacity || "N/A" }} seats
                      </dd>
                    </dl>
                  </div>
                </div>

                <!-- Flight Stats -->
                <div class="card border shadow-sm">
                  <div class="card-header bg-light text-dark py-2">
                    <h5 class="mb-0">
                      <i class="fas fa-chart-bar me-2"></i>Flight Statistics
                    </h5>
                  </div>
                  <div class="card-body">
                    <dl class="row mb-0">
                      <dt class="col-sm-6">Flight Type:</dt>
                      <dd class="col-sm-6">
                        <span class="badge bg-secondary">{{
                          flight.flight_type || "passenger"
                        }}</span>
                      </dd>

                      <dt class="col-sm-6">Passengers:</dt>
                      <dd class="col-sm-6">
                        {{ flight.passenger_count || 0 }}
                      </dd>

                      <dt class="col-sm-6">Available Seats:</dt>
                      <dd class="col-sm-6">
                        {{
                          Math.max(
                            0,
                            (flight.capacity || 0) -
                              (flight.passenger_count || 0)
                          )
                        }}
                      </dd>

                      <dt class="col-sm-6">Cargo Items:</dt>
                      <dd class="col-sm-6">
                        {{ flight.cargo_items_count || 0 }}
                      </dd>

                      <dt class="col-sm-6">Total Weight:</dt>
                      <dd class="col-sm-6">
                        {{ flight.total_cargo_weight_kg || 0 }} kg
                      </dd>

                      <dt class="col-sm-6">Occupancy:</dt>
                      <dd class="col-sm-6">{{ getOccupancyPercentage() }}%</dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Cargo & Passengers Tab -->
          <div
            v-show="activeTab === 'cargo-passengers'"
            id="cargo-passengers"
            role="tabpanel"
          >
            <!-- Passengers Section - Full Width -->
            <div class="mb-3">
              <!-- Passengers Section -->
              <div class="card border shadow-sm mb-3">
                <div
                  class="card-header bg-light text-dark d-flex align-items-center py-2"
                >
                  <h5 class="mb-0 flex-grow-1">
                    <i class="fas fa-users me-2"></i>Passengers ({{
                      flight.passengers?.length || 0
                    }})
                  </h5>
                  <button
                    @click="showPassengerForm = !showPassengerForm"
                    class="btn btn-sm btn-light ms-auto"
                    v-if="flight.flight_type !== 'cargo'"
                  >
                    <i class="fas fa-plus me-1"></i
                    >{{ showPassengerForm ? "Cancel" : "Add Passenger" }}
                  </button>
                </div>
                <div class="card-body py-2 px-3">
                  <!-- Add Passenger Form -->
                  <div
                    v-if="showPassengerForm && flight.flight_type !== 'cargo'"
                    class="mb-4 p-3 border rounded bg-light"
                  >
                    <h6 class="mb-3">
                      <i class="fas fa-user-plus me-2"></i>Add New Passenger
                    </h6>
                    <form @submit.prevent="addPassenger">
                      <!-- Basic Info -->
                      <div class="row mb-3">
                        <div class="col-6">
                          <label class="form-label"
                            >Customer Name
                            <span class="text-danger">*</span></label
                          >
                          <input
                            v-model="newPassenger.customer_name"
                            type="text"
                            class="form-control form-control-sm"
                            required
                          />
                        </div>
                        <div class="col-6">
                          <label class="form-label">ID/Passport Number</label>
                          <input
                            v-model="newPassenger.id_number"
                            type="text"
                            class="form-control form-control-sm"
                          />
                        </div>
                      </div>

                      <div class="row mb-3">
                        <div class="col-4">
                          <label class="form-label"
                            >Passenger Type
                            <span class="text-danger">*</span></label
                          >
                          <select
                            v-model="newPassenger.passenger_type"
                            class="form-select form-select-sm custom-select"
                            required
                          >
                            <option value="adult">Adult</option>
                            <option value="child">Child</option>
                            <option value="infant">Infant</option>
                          </select>
                        </div>
                        <div class="col-4">
                          <label class="form-label"
                            >Nationality
                            <span class="text-danger">*</span></label
                          >
                          <input
                            v-model="newPassenger.nationality"
                            type="text"
                            class="form-control form-control-sm"
                            required
                          />
                        </div>
                        <div class="col-4">
                          <label class="form-label"
                            >ID Type <span class="text-danger">*</span></label
                          >
                          <select
                            v-model="newPassenger.id_type"
                            class="form-select form-select-sm custom-select"
                            required
                          >
                            <option value="passport">Passport</option>
                            <option value="national_id">National ID</option>
                            <option value="drivers_license">
                              Driver's License
                            </option>
                            <option value="other">Other</option>
                          </select>
                        </div>
                      </div>

                      <div class="row mb-3">
                        <div class="col-6">
                          <label class="form-label">Phone Number</label>
                          <input
                            v-model="newPassenger.phone_number"
                            type="text"
                            class="form-control form-control-sm"
                          />
                        </div>
                        <div class="col-6">
                          <label class="form-label">Email</label>
                          <input
                            v-model="newPassenger.email"
                            type="email"
                            class="form-control form-control-sm"
                          />
                        </div>
                      </div>

                      <div class="row mb-3">
                        <div class="col-12">
                          <label class="form-label">Special Requirements</label>
                          <textarea
                            v-model="newPassenger.special_requirements"
                            class="form-control form-control-sm"
                            rows="2"
                            placeholder="Dietary, medical, or other special requirements"
                          ></textarea>
                        </div>
                      </div>

                      <div class="d-flex justify-content-end gap-2">
                        <button
                          type="button"
                          class="btn btn-sm btn-secondary"
                          @click="showPassengerForm = false"
                        >
                          Cancel
                        </button>
                        <button
                          type="submit"
                          class="btn btn-sm btn-primary"
                          :disabled="addingPassenger"
                        >
                          <span v-if="addingPassenger">
                            <i class="fas fa-spinner fa-spin me-1"></i>Adding...
                          </span>
                          <span v-else>
                            <i class="fas fa-plus me-1"></i>Add Passenger
                          </span>
                        </button>
                      </div>
                    </form>
                  </div>

                  <!-- Passengers Table -->
                  <div
                    v-if="!flight.passengers || flight.passengers.length === 0"
                    class="text-center py-4 text-muted"
                  >
                    <i class="fas fa-user-plus fa-2x mb-2"></i>
                    <p class="mb-0">No passengers added yet</p>
                  </div>

                  <div v-else class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th width="18%">Name</th>
                          <th width="10%">Type</th>
                          <th width="13%">ID/Passport</th>
                          <th width="10%">Nationality</th>
                          <th width="12%">Phone</th>
                          <th width="15%">Contact</th>
                          <th width="10%">Status</th>
                          <th width="12%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="passenger in flight.passengers"
                          :key="passenger.id"
                        >
                          <td>
                            <div class="fw-medium">
                              {{
                                passenger.customer_name ||
                                `${passenger.first_name || ""} ${
                                  passenger.last_name || ""
                                }`.trim()
                              }}
                            </div>
                          </td>
                          <td>
                            <span class="badge bg-secondary">
                              {{ passenger.passenger_type || "Passenger" }}
                            </span>
                          </td>
                          <td>
                            <span class="text-muted small">
                              {{ passenger.id_type || "ID" }}: </span
                            ><br />
                            <code class="small">{{
                              passenger.id_number || "N/A"
                            }}</code>
                          </td>
                          <td>{{ passenger.nationality || "N/A" }}</td>
                          <td>
                            <div v-if="passenger.phone_number" class="small">
                              <i class="fas fa-phone me-1"></i>
                              {{ passenger.phone_number }}
                            </div>
                            <span v-else class="text-muted"> No phone </span>
                          </td>
                          <td>
                            <div v-if="passenger.email" class="small">
                              <i class="fas fa-envelope me-1"></i>
                              {{ passenger.email }}
                            </div>
                            <span v-else class="text-muted"> No email </span>
                          </td>
                          <td>
                            <span
                              v-if="passenger.checked_in"
                              class="badge bg-secondary"
                            >
                              <i class="fas fa-check me-1"></i>Checked In
                            </span>
                            <span
                              v-else
                              class="badge bg-light text-dark border"
                            >
                              <i class="fas fa-clock me-1"></i>Pending
                            </span>
                          </td>
                          <td>
                            <div class="btn-group btn-group-sm">
                              <button
                                @click="editPassenger(passenger)"
                                class="btn btn-outline-secondary"
                                title="Edit"
                              >
                                <i class="fas fa-edit"></i>
                              </button>
                              <button
                                @click="checkInPassenger(passenger)"
                                class="btn btn-outline-success"
                                v-if="!passenger.checked_in"
                                title="Check In"
                              >
                                <i class="fas fa-check"></i>
                              </button>
                              <button
                                @click="removePassenger(passenger)"
                                class="btn btn-outline-danger"
                                title="Remove"
                              >
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cargo Section - Full Width -->
            <div class="mb-3">
              <!-- Cargo Section -->
              <div class="card border shadow-sm mb-3">
                <div
                  class="card-header bg-light text-dark d-flex align-items-center py-2"
                >
                  <h5 class="mb-0 flex-grow-1">
                    <i class="fas fa-boxes me-2"></i>Cargo ({{
                      flight.cargo?.length || 0
                    }})
                  </h5>
                  <button
                    @click="showCargoForm = !showCargoForm"
                    class="btn btn-sm btn-light ms-auto"
                    v-if="flight.flight_type !== 'passenger'"
                  >
                    <i class="fas fa-plus me-1"></i
                    >{{ showCargoForm ? "Cancel" : "Add Cargo" }}
                  </button>
                </div>
                <div class="card-body py-2 px-3">
                  <!-- Add Cargo Form -->
                  <div
                    v-if="showCargoForm && flight.flight_type !== 'passenger'"
                    class="mb-4 p-3 border rounded bg-light"
                  >
                    <h6 class="mb-3">
                      <i class="fas fa-box me-2"></i>Add New Cargo
                    </h6>
                    <form @submit.prevent="addCargo">
                      <!-- Cargo Name and Type -->
                      <div class="row mb-3">
                        <div class="col-6">
                          <label class="form-label"
                            >Cargo Name
                            <span class="text-danger">*</span></label
                          >
                          <input
                            v-model="newCargo.cargo_name"
                            type="text"
                            class="form-control form-control-sm"
                            placeholder="Brief name for the cargo"
                            required
                          />
                        </div>
                        <div class="col-6">
                          <label class="form-label"
                            >Cargo Type
                            <span class="text-danger">*</span></label
                          >
                          <select
                            v-model="newCargo.cargo_type"
                            class="form-select form-select-sm custom-select"
                            required
                          >
                            <option value="">Select cargo type</option>
                            <option value="general">General Cargo</option>
                            <option value="perishable">Perishable</option>
                            <option value="medical">Medical Supplies</option>
                            <option value="electronics">Electronics</option>
                            <option value="machinery">Machinery</option>
                            <option value="documents">Documents</option>
                            <option value="other">Other</option>
                          </select>
                        </div>
                      </div>

                      <!-- Description -->
                      <div class="row mb-3">
                        <div class="col-12">
                          <label class="form-label"
                            >Description
                            <span class="text-danger">*</span></label
                          >
                          <textarea
                            v-model="newCargo.description"
                            class="form-control form-control-sm"
                            rows="2"
                            placeholder="Detailed description of the cargo"
                            required
                          ></textarea>
                        </div>
                      </div>

                      <!-- Quantity, Weight, and Packaging -->
                      <div class="row mb-3">
                        <div class="col-3">
                          <label class="form-label"
                            >Quantity <span class="text-danger">*</span></label
                          >
                          <input
                            v-model="newCargo.quantity"
                            type="number"
                            min="1"
                            class="form-control form-control-sm"
                            required
                          />
                        </div>
                        <div class="col-3">
                          <label class="form-label"
                            >Weight (kg)
                            <span class="text-danger">*</span></label
                          >
                          <input
                            v-model="newCargo.weight_kg"
                            type="number"
                            step="0.1"
                            min="0.1"
                            class="form-control form-control-sm"
                            required
                          />
                        </div>
                        <div class="col-3">
                          <label class="form-label">Dimensions</label>
                          <input
                            v-model="newCargo.dimensions"
                            type="text"
                            class="form-control form-control-sm"
                            placeholder="L x W x H"
                          />
                        </div>
                        <div class="col-3">
                          <label class="form-label"
                            >Packaging <span class="text-danger">*</span></label
                          >
                          <select
                            v-model="newCargo.packaging_type"
                            class="form-select form-select-sm custom-select"
                            required
                          >
                            <option value="">Select</option>
                            <option value="carton">Carton</option>
                            <option value="bag">Bag</option>
                            <option value="pallet">Pallet</option>
                            <option value="crate">Crate</option>
                            <option value="container">Container</option>
                            <option value="envelope">Envelope</option>
                            <option value="other">Other</option>
                          </select>
                        </div>
                      </div>

                      <!-- Shipper and Receiver -->
                      <div class="row mb-3">
                        <div class="col-6">
                          <label class="form-label"
                            >Shipper Name
                            <span class="text-danger">*</span></label
                          >
                          <input
                            v-model="newCargo.shipper_name"
                            type="text"
                            class="form-control form-control-sm"
                            required
                          />
                        </div>
                        <div class="col-6">
                          <label class="form-label"
                            >Receiver Name
                            <span class="text-danger">*</span></label
                          >
                          <input
                            v-model="newCargo.consignee_name"
                            type="text"
                            class="form-control form-control-sm"
                            required
                          />
                        </div>
                      </div>

                      <!-- Special Handling Checkboxes -->
                      <div class="row mb-3">
                        <div class="col-4">
                          <div class="form-check">
                            <input
                              v-model="newCargo.fragile"
                              class="form-check-input"
                              type="checkbox"
                              id="inline-fragile"
                            />
                            <label
                              class="form-check-label"
                              for="inline-fragile"
                            >
                              Fragile
                            </label>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-check">
                            <input
                              v-model="newCargo.hazardous"
                              class="form-check-input"
                              type="checkbox"
                              id="inline-hazardous"
                            />
                            <label
                              class="form-check-label"
                              for="inline-hazardous"
                            >
                              Hazardous
                            </label>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-check">
                            <input
                              v-model="newCargo.requires_refrigeration"
                              class="form-check-input"
                              type="checkbox"
                              id="inline-refrigeration"
                            />
                            <label
                              class="form-check-label"
                              for="inline-refrigeration"
                            >
                              Refrigeration
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="d-flex justify-content-end gap-2">
                        <button
                          type="button"
                          class="btn btn-sm btn-secondary"
                          @click="showCargoForm = false"
                        >
                          Cancel
                        </button>
                        <button
                          type="submit"
                          class="btn btn-sm btn-success"
                          :disabled="addingCargo"
                        >
                          <span v-if="addingCargo">
                            <i class="fas fa-spinner fa-spin me-1"></i>Adding...
                          </span>
                          <span v-else>
                            <i class="fas fa-plus me-1"></i>Add Cargo
                          </span>
                        </button>
                      </div>
                    </form>
                  </div>

                  <!-- Cargo Table -->
                  <div
                    v-if="!flight.cargo || flight.cargo.length === 0"
                    class="text-center py-4 text-muted"
                  >
                    <i class="fas fa-box fa-2x mb-2"></i>
                    <p class="mb-0">No cargo added yet</p>
                  </div>

                  <div v-else class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th width="18%">Cargo Name</th>
                          <th width="15%">Description</th>
                          <th width="10%">Weight</th>
                          <th width="10%">Type</th>
                          <th width="15%">Shipper</th>
                          <th width="15%">Consignee</th>
                          <th width="12%">Special Handling</th>
                          <th width="8%">Status</th>
                          <th width="7%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="cargo in flight.cargo" :key="cargo.id">
                          <td>
                            <div class="fw-medium">
                              {{ getCargoName(cargo) }}
                            </div>
                          </td>
                          <td>
                            <span class="text-muted small">
                              {{ cargo.description || "No description" }}
                            </span>
                          </td>
                          <td>
                            <span class="fw-medium">{{ cargo.weight_kg }}</span>
                            <small class="text-muted">kg</small>
                          </td>
                          <td>
                            <span class="badge bg-secondary">
                              {{ cargo.cargo_type }}
                            </span>
                          </td>
                          <td>
                            <div class="small">
                              <i class="fas fa-arrow-up me-1 text-muted"></i>
                              {{ cargo.shipper_name }}
                            </div>
                          </td>
                          <td>
                            <div class="small">
                              <i class="fas fa-arrow-down me-1 text-muted"></i>
                              {{ cargo.consignee_name }}
                            </div>
                          </td>
                          <td>
                            <div
                              v-if="
                                cargo.fragile ||
                                cargo.hazardous ||
                                cargo.requires_refrigeration
                              "
                            >
                              <span
                                v-if="cargo.fragile"
                                class="badge bg-warning text-dark me-1 mb-1"
                                >Fragile</span
                              >
                              <span
                                v-if="cargo.hazardous"
                                class="badge bg-danger me-1 mb-1"
                                >Hazardous</span
                              >
                              <span
                                v-if="cargo.requires_refrigeration"
                                class="badge bg-info me-1 mb-1"
                                >Refrigeration</span
                              >
                            </div>
                            <span v-else class="text-muted small">
                              Standard
                            </span>
                          </td>
                          <td>
                            <span
                              class="badge"
                              :class="`bg-${getStatusColor(cargo.status)}`"
                            >
                              {{ cargo.status }}
                            </span>
                          </td>
                          <td>
                            <div class="btn-group btn-group-sm">
                              <button
                                @click="editCargo(cargo)"
                                class="btn btn-outline-secondary btn-sm"
                                title="Edit"
                              >
                                <i class="fas fa-edit"></i>
                              </button>
                              <button
                                @click="removeCargo(cargo)"
                                class="btn btn-outline-danger btn-sm"
                                title="Remove"
                              >
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Passenger Modal -->
    <div
      v-if="showAddPassengerModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
      @click="showAddPassengerModal = false"
    >
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Passenger</h5>
            <button
              type="button"
              class="btn-close"
              @click="showAddPassengerModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addPassenger">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label"
                    >Customer Name <span class="text-danger">*</span></label
                  >
                  <input
                    v-model="newPassenger.customer_name"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">ID/Passport Number</label>
                  <input
                    v-model="newPassenger.id_number"
                    type="text"
                    class="form-control"
                  />
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label"
                    >Nationality <span class="text-danger">*</span></label
                  >
                  <input
                    v-model="newPassenger.nationality"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label"
                    >ID Type <span class="text-danger">*</span></label
                  >
                  <select
                    v-model="newPassenger.id_type"
                    class="form-select custom-select"
                    required
                  >
                    <option value="passport">Passport</option>
                    <option value="national_id">National ID</option>
                    <option value="drivers_license">Driver's License</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label"
                    >Passenger Type <span class="text-danger">*</span></label
                  >
                  <select
                    v-model="newPassenger.passenger_type"
                    class="form-select custom-select"
                    required
                  >
                    <option value="adult">Adult</option>
                    <option value="child">Child</option>
                    <option value="infant">Infant</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Phone Number</label>
                  <input
                    v-model="newPassenger.phone_number"
                    type="text"
                    class="form-control"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input
                    v-model="newPassenger.email"
                    type="email"
                    class="form-control"
                  />
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input
                    v-model="newPassenger.date_of_birth"
                    type="date"
                    class="form-control"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Gender</label>
                  <select
                    v-model="newPassenger.gender"
                    class="form-select custom-select"
                  >
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Special Requirements</label>
                <textarea
                  v-model="newPassenger.special_requirements"
                  class="form-control"
                  rows="2"
                  placeholder="Dietary, medical, or other special requirements"
                ></textarea>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="showAddPassengerModal = false"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="addingPassenger"
                >
                  <span v-if="addingPassenger">
                    <i class="fas fa-spinner fa-spin me-1"></i>Adding...
                  </span>
                  <span v-else>Add Passenger</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Cargo Modal -->
    <div
      v-if="showAddCargoModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
      @click="showAddCargoModal = false"
    >
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Cargo</h5>
            <button
              type="button"
              class="btn-close"
              @click="showAddCargoModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addCargo">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label"
                    >Cargo Name <span class="text-danger">*</span></label
                  >
                  <input
                    v-model="newCargo.cargo_name"
                    type="text"
                    class="form-control"
                    placeholder="Brief name for the cargo"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label"
                    >Cargo Type <span class="text-danger">*</span></label
                  >
                  <select
                    v-model="newCargo.cargo_type"
                    class="form-select custom-select"
                    required
                  >
                    <option value="">Select cargo type</option>
                    <option value="general">General Cargo</option>
                    <option value="perishable">Perishable</option>
                    <option value="medical">Medical Supplies</option>
                    <option value="electronics">Electronics</option>
                    <option value="machinery">Machinery</option>
                    <option value="documents">Documents</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label"
                  >Description <span class="text-danger">*</span></label
                >
                <textarea
                  v-model="newCargo.description"
                  class="form-control"
                  rows="3"
                  placeholder="Detailed description of the cargo"
                  required
                ></textarea>
              </div>

              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label"
                    >Quantity <span class="text-danger">*</span></label
                  >
                  <input
                    v-model="newCargo.quantity"
                    type="number"
                    min="1"
                    class="form-control"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label"
                    >Total Weight (kg) <span class="text-danger">*</span></label
                  >
                  <input
                    v-model="newCargo.weight_kg"
                    type="number"
                    step="0.1"
                    min="0.1"
                    class="form-control"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label class="form-label"
                    >Packaging Type <span class="text-danger">*</span></label
                  >
                  <select
                    v-model="newCargo.packaging_type"
                    class="form-select custom-select"
                    required
                  >
                    <option value="">Select packaging</option>
                    <option value="carton">Carton/Box</option>
                    <option value="bag">Bag/Sack</option>
                    <option value="pallet">Pallet</option>
                    <option value="crate">Wooden Crate</option>
                    <option value="container">Container</option>
                    <option value="envelope">Envelope</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Dimensions (optional)</label>
                  <input
                    v-model="newCargo.dimensions"
                    type="text"
                    class="form-control"
                    placeholder="L x W x H in cm"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Declared Value</label>
                  <div class="input-group">
                    <select
                      v-model="newCargo.currency"
                      class="form-select custom-select"
                      style="max-width: 80px"
                    >
                      <option value="USD">USD</option>
                      <option value="EUR">EUR</option>
                      <option value="TZS">TZS</option>
                    </select>
                    <input
                      v-model="newCargo.declared_value"
                      type="number"
                      step="0.01"
                      min="0"
                      class="form-control"
                      placeholder="0.00"
                    />
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label"
                    >Shipper Name <span class="text-danger">*</span></label
                  >
                  <input
                    v-model="newCargo.shipper_name"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label"
                    >Receiver Name <span class="text-danger">*</span></label
                  >
                  <input
                    v-model="newCargo.consignee_name"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Shipper Contact</label>
                  <input
                    v-model="newCargo.shipper_contact"
                    type="text"
                    class="form-control"
                    placeholder="Phone or email"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Receiver Contact</label>
                  <input
                    v-model="newCargo.consignee_contact"
                    type="text"
                    class="form-control"
                    placeholder="Phone or email"
                  />
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4">
                  <div class="form-check">
                    <input
                      v-model="newCargo.fragile"
                      class="form-check-input"
                      type="checkbox"
                      id="fragile"
                    />
                    <label class="form-check-label" for="fragile">
                      Fragile - Handle with Care
                    </label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <input
                      v-model="newCargo.hazardous"
                      class="form-check-input"
                      type="checkbox"
                      id="hazardous"
                    />
                    <label class="form-check-label" for="hazardous">
                      Hazardous Material
                    </label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check">
                    <input
                      v-model="newCargo.requires_refrigeration"
                      class="form-check-input"
                      type="checkbox"
                      id="refrigeration"
                    />
                    <label class="form-check-label" for="refrigeration">
                      Requires Refrigeration
                    </label>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Special Handling Instructions</label>
                <textarea
                  v-model="newCargo.special_handling_instructions"
                  class="form-control"
                  rows="2"
                  placeholder="Any special handling requirements"
                ></textarea>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button
                  type="button"
                  class="btn btn-secondary"
                  @click="showAddCargoModal = false"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="btn btn-success"
                  :disabled="addingCargo"
                >
                  <span v-if="addingCargo">
                    <i class="fas fa-spinner fa-spin me-1"></i>Adding...
                  </span>
                  <span v-else>Add Cargo</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal backdrop -->
    <div
      v-if="showAddPassengerModal || showAddCargoModal"
      class="modal-backdrop fade show"
    ></div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, router } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

export default {
  name: "ScheduledFlightShow",
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    flight: Object,
    airports: Array,
  },
  data() {
    return {
      activeTab: "details",
      showAddPassengerModal: false,
      showAddCargoModal: false,
      showPassengerForm: false,
      showCargoForm: false,
      addingPassenger: false,
      addingCargo: false,
      newPassenger: this.getEmptyPassenger(),
      newCargo: this.getEmptyCargo(),
      // Flight Fees data
      showLandingFeeForm: false,
      showFuelConsumptionForm: false,
      addingLandingFee: false,
      addingFuelConsumption: false,
      newLandingFee: this.getEmptyLandingFee(),
      newFuelConsumption: this.getEmptyFuelConsumption(),
    };
  },
  computed: {
    aircraftModel() {
      if (
        this.flight.aircraft?.manufacturer?.name &&
        this.flight.aircraft?.model?.name
      ) {
        return `${this.flight.aircraft.manufacturer.name} ${this.flight.aircraft.model.name}`;
      }
      return "N/A";
    },
    flightDuration() {
      if (
        !this.flight.total_departure_time ||
        !this.flight.total_arrival_time
      ) {
        return "N/A";
      }

      const departure = new Date(this.flight.total_departure_time);
      const arrival = new Date(this.flight.total_arrival_time);
      const diff = arrival - departure;

      if (diff <= 0) return "N/A";

      const hours = Math.floor(diff / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

      return `${hours}h ${minutes}m`;
    },
    isOnTime() {
      const firstActual = this.getFirstActualDeparture();
      if (!firstActual || !this.flight.total_departure_time) {
        return false;
      }

      const scheduled = new Date(this.flight.total_departure_time);
      const actual = new Date(firstActual);
      const delayMinutes = (actual - scheduled) / (1000 * 60);

      return delayMinutes <= 15;
    },
    isDelayed() {
      const firstActual = this.getFirstActualDeparture();
      if (!firstActual || !this.flight.total_departure_time) {
        const now = new Date();
        const scheduled = new Date(this.flight.total_departure_time);
        return now > scheduled && !firstActual;
      }

      const scheduled = new Date(this.flight.total_departure_time);
      const actual = new Date(firstActual);
      const delayMinutes = (actual - scheduled) / (1000 * 60);

      return delayMinutes > 15;
    },
  },
  methods: {
    getEmptyPassenger() {
      return {
        scheduled_flight_id: this.flight.id,
        customer_name: "",
        id_number: "",
        id_type: "passport",
        nationality: "",
        date_of_birth: "",
        gender: "",
        phone_number: "",
        email: "",
        special_requirements: "",
        passenger_type: "adult",
        seat_preference: "",
        notes: "",
      };
    },
    getEmptyCargo() {
      return {
        scheduled_flight_id: this.flight.id,
        cargo_name: "",
        description: "",
        cargo_type: "",
        quantity: 1,
        weight_kg: "",
        volume_m3: "",
        dimensions: "",
        packaging_type: "",
        shipper_name: "",
        shipper_contact: "",
        consignee_name: "",
        consignee_contact: "",
        declared_value: "",
        currency: "USD",
        fragile: false,
        hazardous: false,
        requires_refrigeration: false,
        special_handling_instructions: "",
        notes: "",
      };
    },
    async addPassenger() {
      this.addingPassenger = true;
      try {
        const response = await fetch(route("flight-passengers.store"), {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content"),
          },
          body: JSON.stringify(this.newPassenger),
        });

        const data = await response.json();

        if (response.ok) {
          this.showAddPassengerModal = false;
          this.showPassengerForm = false;
          this.newPassenger = this.getEmptyPassenger();
          // Reload the page to show updated data
          router.reload();
        } else {
          alert("Error adding passenger: " + data.message);
        }
      } catch (error) {
        alert("Error adding passenger: " + error.message);
      } finally {
        this.addingPassenger = false;
      }
    },
    async addCargo() {
      this.addingCargo = true;
      try {
        const response = await fetch(route("flight-cargo.store"), {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content"),
          },
          body: JSON.stringify(this.newCargo),
        });

        const data = await response.json();

        if (response.ok) {
          this.showAddCargoModal = false;
          this.showCargoForm = false;
          this.newCargo = this.getEmptyCargo();
          // Reload the page to show updated data
          router.reload();
        } else {
          alert("Error adding cargo: " + data.message);
        }
      } catch (error) {
        alert("Error adding cargo: " + error.message);
      } finally {
        this.addingCargo = false;
      }
    },
    async removePassenger(passenger) {
      if (
        confirm(
          `Are you sure you want to remove ${
            passenger.customer_name ||
            `${passenger.first_name || ""} ${passenger.last_name || ""}`.trim()
          } from this flight?`
        )
      ) {
        try {
          const response = await fetch(
            route("flight-passengers.destroy", passenger.id),
            {
              method: "DELETE",
              headers: {
                "X-CSRF-TOKEN": document
                  .querySelector('meta[name="csrf-token"]')
                  ?.getAttribute("content"),
              },
            }
          );

          if (response.ok) {
            router.reload();
          } else {
            const data = await response.json();
            alert("Error removing passenger: " + data.message);
          }
        } catch (error) {
          alert("Error removing passenger: " + error.message);
        }
      }
    },
    async removeCargo(cargo) {
      if (
        confirm(
          `Are you sure you want to remove this cargo item from the flight?`
        )
      ) {
        try {
          const response = await fetch(
            route("flight-cargo.destroy", cargo.id),
            {
              method: "DELETE",
              headers: {
                "X-CSRF-TOKEN": document
                  .querySelector('meta[name="csrf-token"]')
                  ?.getAttribute("content"),
              },
            }
          );

          if (response.ok) {
            router.reload();
          } else {
            const data = await response.json();
            alert("Error removing cargo: " + data.message);
          }
        } catch (error) {
          alert("Error removing cargo: " + error.message);
        }
      }
    },
    async checkInPassenger(passenger) {
      try {
        const response = await fetch(
          route("flight-passengers.check-in", passenger.id),
          {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content"),
            },
          }
        );

        if (response.ok) {
          router.reload();
        } else {
          const data = await response.json();
          alert("Error checking in passenger: " + data.message);
        }
      } catch (error) {
        alert("Error checking in passenger: " + error.message);
      }
    },
    editPassenger(passenger) {
      // TODO: Implement edit passenger modal
      alert("Edit passenger functionality will be implemented next");
    },
    editCargo(cargo) {
      // TODO: Implement edit cargo modal
      alert("Edit cargo functionality will be implemented next");
    },
    formatDateTime(datetime) {
      if (!datetime) return "N/A";
      return new Date(datetime).toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    formatTime(datetime) {
      if (!datetime) return "N/A";
      return new Date(datetime).toLocaleString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    getAirportCode(airport) {
      if (!airport) return "N/A";
      return airport.iata_code || airport.icao_code || "N/A";
    },
    getRouteOverview() {
      if (
        !this.flight.schedule_routes ||
        this.flight.schedule_routes.length === 0
      ) {
        return "No routes";
      }

      if (this.flight.schedule_routes.length === 1) {
        const route = this.flight.schedule_routes[0];
        const origin = this.getAirportCode(route.origin_airport);
        const destination = this.getAirportCode(route.destination_airport);
        return `${origin} → ${destination}`;
      }

      // Multi-segment route
      const airports = [
        this.getAirportCode(this.flight.schedule_routes[0].origin_airport),
      ];
      this.flight.schedule_routes.forEach((route) => {
        airports.push(this.getAirportCode(route.destination_airport));
      });
      return airports.join(" → ");
    },
    getFirstActualDeparture() {
      if (
        !this.flight.schedule_routes ||
        this.flight.schedule_routes.length === 0
      ) {
        return null;
      }
      return this.flight.schedule_routes[0].actual_departure_time;
    },
    getLastActualArrival() {
      if (
        !this.flight.schedule_routes ||
        this.flight.schedule_routes.length === 0
      ) {
        return null;
      }
      const lastRoute =
        this.flight.schedule_routes[this.flight.schedule_routes.length - 1];
      return lastRoute.actual_arrival_time;
    },
    getOccupancyPercentage() {
      if (!this.flight.capacity) return 0;
      return Math.min(
        100,
        Math.round(
          ((this.flight.passenger_count || 0) / this.flight.capacity) * 100
        )
      );
    },
    getCargoName(cargo) {
      // Extract cargo name from description if it contains the pattern "Name: Description"
      if (cargo.description && cargo.description.includes(":")) {
        return cargo.description.split(":")[0].trim();
      }
      return cargo.cargo_type || "Cargo";
    },
    getStatusColor(status) {
      const colors = {
        pending: "warning",
        loaded: "info",
        in_transit: "primary",
        delivered: "success",
        damaged: "danger",
      };
      return colors[status] || "secondary";
    },
    getCustomerDisplayName(customer) {
      if (!customer) return "Unknown";
      if (customer.type === "corporate") {
        return customer.company_name || "Corporate Customer";
      }
      return (
        `${customer.first_name || ""} ${customer.last_name || ""}`.trim() ||
        "Individual Customer"
      );
    },

    // Flight Fees Methods
    getEmptyLandingFee() {
      return {
        scheduled_flight_id: this.flight.id,
        airport_id: "",
        mtow_used_kg: "",
        fee_amount: "",
        currency: "USD",
        notes: "",
      };
    },

    getEmptyFuelConsumption() {
      return {
        scheduled_flight_id: this.flight.id,
        fuel_consumed: "",
        fuel_unit: "liters",
        distance_km: this.calculateTotalDistanceValue(),
        flight_time_hours: "",
        bloc_time_hours: this.calculateBlocTimeValue(),
        total_time_hours: this.calculateBlocTimeValue(), // Just use bloc time initially
        fuel_burn_rate: "",
        notes: "",
      };
    },

    async addLandingFee() {
      this.addingLandingFee = true;
      try {
        const response = await fetch(route("flight-landing-fees.store"), {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
          },
          body: JSON.stringify(this.newLandingFee),
        });

        const data = await response.json();

        if (response.ok) {
          this.flight.landing_fees = this.flight.landing_fees || [];
          // Add safety check for data structure
          const landingFee = data.data || data.landing_fee || data;
          if (landingFee && landingFee.id) {
            this.flight.landing_fees.push(landingFee);
            this.newLandingFee = this.getEmptyLandingFee();
            this.showLandingFeeForm = false;
          } else {
            console.error("Invalid landing fee data:", data);
            alert("Error: Invalid response data structure");
          }
        } else {
          alert(
            "Error adding landing fee: " + (data.message || "Unknown error")
          );
        }
      } catch (error) {
        console.error("Error:", error);
        alert("Error adding landing fee");
      } finally {
        this.addingLandingFee = false;
      }
    },

    async deleteLandingFee(feeId) {
      if (confirm("Are you sure you want to delete this landing fee?")) {
        try {
          const response = await fetch(
            route("flight-landing-fees.destroy", feeId),
            {
              method: "DELETE",
              headers: {
                "X-CSRF-TOKEN": document
                  .querySelector('meta[name="csrf-token"]')
                  .getAttribute("content"),
              },
            }
          );

          if (response.ok) {
            this.flight.landing_fees = this.flight.landing_fees.filter(
              (fee) => fee.id !== feeId
            );
          } else {
            alert("Error deleting landing fee");
          }
        } catch (error) {
          console.error("Error:", error);
          alert("Error deleting landing fee");
        }
      }
    },

    async addFuelConsumption() {
      this.addingFuelConsumption = true;
      try {
        const response = await fetch(route("flight-fuel-consumption.store"), {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
          },
          body: JSON.stringify(this.newFuelConsumption),
        });

        const data = await response.json();

        if (response.ok) {
          this.flight.fuel_consumption = this.flight.fuel_consumption || [];
          // Add safety check for data structure
          const fuelConsumption = data.data || data.fuel_consumption || data;
          if (fuelConsumption && fuelConsumption.id) {
            this.flight.fuel_consumption.push(fuelConsumption);
            this.newFuelConsumption = this.getEmptyFuelConsumption();
            this.showFuelConsumptionForm = false;
          } else {
            console.error("Invalid fuel consumption data:", data);
            alert("Error: Invalid response data structure");
          }
        } else {
          alert(
            "Error adding fuel consumption: " +
              (data.message || "Unknown error")
          );
        }
      } catch (error) {
        console.error("Error:", error);
        alert("Error adding fuel consumption");
      } finally {
        this.addingFuelConsumption = false;
      }
    },

    async deleteFuelConsumption(fuelId) {
      if (
        confirm("Are you sure you want to delete this fuel consumption record?")
      ) {
        try {
          const response = await fetch(
            route("flight-fuel-consumption.destroy", fuelId),
            {
              method: "DELETE",
              headers: {
                "X-CSRF-TOKEN": document
                  .querySelector('meta[name="csrf-token"]')
                  .getAttribute("content"),
              },
            }
          );

          if (response.ok) {
            this.flight.fuel_consumption = this.flight.fuel_consumption.filter(
              (fuel) => fuel.id !== fuelId
            );
          } else {
            alert("Error deleting fuel consumption record");
          }
        } catch (error) {
          console.error("Error:", error);
          alert("Error deleting fuel consumption record");
        }
      }
    },

    calculateTotalDistanceValue() {
      if (
        !this.flight.schedule_routes ||
        this.flight.schedule_routes.length === 0
      ) {
        return 0;
      }

      let totalDistance = 0;
      const coordinates = [];

      this.flight.schedule_routes.forEach((route) => {
        if (
          route.origin_airport &&
          route.origin_airport.latitude &&
          route.origin_airport.longitude
        ) {
          coordinates.push({
            lat: parseFloat(route.origin_airport.latitude),
            lng: parseFloat(route.origin_airport.longitude),
          });
        }
        if (
          route.destination_airport &&
          route.destination_airport.latitude &&
          route.destination_airport.longitude
        ) {
          coordinates.push({
            lat: parseFloat(route.destination_airport.latitude),
            lng: parseFloat(route.destination_airport.longitude),
          });
        }
      });

      for (let i = 0; i < coordinates.length - 1; i++) {
        totalDistance += this.calculateDistanceBetweenPoints(
          coordinates[i].lat,
          coordinates[i].lng,
          coordinates[i + 1].lat,
          coordinates[i + 1].lng
        );
      }

      return totalDistance;
    },

    calculateTotalDistance() {
      const totalKm = this.calculateTotalDistanceValue();
      return totalKm ? totalKm.toFixed(0) : "0";
    },

    calculateDistanceBetweenPoints(lat1, lon1, lat2, lon2) {
      const EARTH_RADIUS_KM = 6371.0;
      const lat1Rad = (lat1 * Math.PI) / 180;
      const lat2Rad = (lat2 * Math.PI) / 180;
      const deltaLatRad = ((lat2 - lat1) * Math.PI) / 180;
      const deltaLonRad = ((lon2 - lon1) * Math.PI) / 180;

      const a =
        Math.sin(deltaLatRad / 2) * Math.sin(deltaLatRad / 2) +
        Math.cos(lat1Rad) *
          Math.cos(lat2Rad) *
          Math.sin(deltaLonRad / 2) *
          Math.sin(deltaLonRad / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

      return EARTH_RADIUS_KM * c;
    },

    calculateBlocTimeValue() {
      const routeCount = this.flight.schedule_routes
        ? this.flight.schedule_routes.length
        : 0;
      return routeCount * 0.1;
    },

    calculateBlocTime() {
      return this.calculateBlocTimeValue().toFixed(1);
    },

    calculateTotalTimeValue() {
      if (!this.newFuelConsumption) {
        return this.calculateBlocTimeValue();
      }
      return (
        this.calculateBlocTimeValue() +
        parseFloat(this.newFuelConsumption.flight_time_hours || 0)
      );
    },

    calculateTotalTime() {
      return this.calculateTotalTimeValue().toFixed(1);
    },

    calculateTotalLandingFees() {
      if (!this.flight.landing_fees || this.flight.landing_fees.length === 0) {
        return "No fees recorded";
      }

      const totalsByCurrency = {};
      this.flight.landing_fees.forEach((fee) => {
        totalsByCurrency[fee.currency] =
          (totalsByCurrency[fee.currency] || 0) + parseFloat(fee.fee_amount);
      });

      const currencyTotals = Object.entries(totalsByCurrency).map(
        ([curr, total]) => {
          return `${curr} ${this.formatNumber(total)}`;
        }
      );

      return currencyTotals.join(", ");
    },

    calculateTotalFlightTime() {
      if (
        !this.flight.fuel_consumption ||
        this.flight.fuel_consumption.length === 0
      ) {
        return "No data";
      }

      const latestRecord =
        this.flight.fuel_consumption[this.flight.fuel_consumption.length - 1];
      return this.formatTime(latestRecord.total_time_hours) || "N/A";
    },

    formatNumber(value) {
      if (!value) return "0.00";
      return parseFloat(value).toFixed(2);
    },

    formatTime(hours) {
      if (!hours) return "N/A";
      const totalMinutes = Math.round(parseFloat(hours) * 60);
      const hrs = Math.floor(totalMinutes / 60);
      const mins = totalMinutes % 60;
      return `${hrs}:${mins.toString().padStart(2, "0")}`;
    },
  },
};
</script>

<style scoped>
.modal.fade.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.nav-pills .nav-link {
  color: #6c757d;
  font-weight: 500;
  border-radius: 0.375rem;
  margin-right: 0.5rem;
}

.nav-pills .nav-link.active {
  color: #fff;
  background-color: #0d6efd;
}

.nav-pills .nav-link:hover {
  color: #495057;
  background-color: #f8f9fa;
}

.route-segment {
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.route-segment:hover {
  background: #e9ecef;
}

.list-group-item {
  border-left: none;
  border-right: none;
}

.list-group-item:first-child {
  border-top: none;
}

.list-group-item:last-child {
  border-bottom: none;
}

dl.row dt {
  font-size: 0.875rem;
  font-weight: 600;
}

dl.row dd {
  font-size: 0.875rem;
}

.btn-group-sm .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.custom-select {
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
}

.custom-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-select:disabled {
  background-color: #e9ecef;
}

/* Compact table styling */
.table td,
.table th {
  padding: 0.5rem 0.75rem;
  vertical-align: middle;
}

.table-responsive {
  border-radius: 0.375rem;
}

/* Compact card styling */
.card-body {
  padding: 1rem;
}

/* Add button styling - green background for visibility */
.btn-sm.btn-light {
  background-color: #28a745 !important;
  color: white !important;
  border-color: #28a745 !important;
}

.btn-sm.btn-light:hover {
  background-color: #218838 !important;
  border-color: #1e7e34 !important;
}
</style>