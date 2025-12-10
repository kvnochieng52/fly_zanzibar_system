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
              :style="{ backgroundColor: flight.flight_status?.color || '#6c757d', color: 'white' }"
            >
              {{ flight.flight_status?.name || 'Unknown' }}
            </span>
          </div>
          <div class="col-md-6 text-center">
            <div class="d-flex align-items-center justify-content-center">
              <div class="text-center">
                <strong class="fs-4">{{ getRouteOverview() }}</strong>
                <br>
                <small class="text-muted">{{ flightDuration }}</small>
              </div>
            </div>
          </div>
          <div class="col-md-3 text-center">
            <div class="row">
              <div class="col-6">
                <div class="text-primary fw-bold fs-5">{{ flight.passenger_count || 0 }}</div>
                <small class="text-muted">Passengers</small>
              </div>
              <div class="col-6">
                <div class="text-success fw-bold fs-5">{{ flight.cargo_items_count || 0 }}</div>
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
              :class="['nav-link', { active: activeTab === 'cargo-passengers' }]"
              id="cargo-passengers-tab"
              type="button"
              role="tab"
              @click="activeTab = 'cargo-passengers'"
            >
              <i class="fas fa-users me-2"></i>Cargo & Passenger Details
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <div class="tab-content" id="flightTabsContent">
          <!-- Flight Details Tab -->
          <div v-show="activeTab === 'details'" id="flight-details" role="tabpanel">
        <div class="row">
          <div class="col-lg-8">
            <!-- Route Information -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="fas fa-route me-2"></i>Route Information
                </h5>
              </div>
              <div class="card-body">
                <!-- Single Route Display -->
                <div v-if="flight.schedule_routes && flight.schedule_routes.length === 1" class="row text-center">
                  <div class="col-md-5">
                    <div class="p-3">
                      <h3 class="text-success mb-1">{{ getAirportCode(flight.schedule_routes[0].origin_airport) }}</h3>
                      <h5 class="mb-2">{{ flight.schedule_routes[0].origin_airport?.name || 'Unknown Airport' }}</h5>
                      <p class="text-muted mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ flight.schedule_routes[0].origin_airport?.city || 'Unknown' }}, {{ flight.schedule_routes[0].origin_airport?.country || 'Unknown' }}
                      </p>
                    </div>
                  </div>
                  <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                      <i class="fas fa-plane fa-2x text-primary mb-2"></i>
                      <br>
                      <small class="text-muted">{{ flightDuration }}</small>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="p-3">
                      <h3 class="text-success mb-1">{{ getAirportCode(flight.schedule_routes[0].destination_airport) }}</h3>
                      <h5 class="mb-2">{{ flight.schedule_routes[0].destination_airport?.name || 'Unknown Airport' }}</h5>
                      <p class="text-muted mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ flight.schedule_routes[0].destination_airport?.city || 'Unknown' }}, {{ flight.schedule_routes[0].destination_airport?.country || 'Unknown' }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Multi-Segment Route Display -->
                <div v-else-if="flight.schedule_routes && flight.schedule_routes.length > 1">
                  <div class="row mb-3">
                    <div class="col-12">
                      <div class="text-center mb-3">
                        <span class="badge bg-info fs-6 px-3 py-2">
                          Multi-Segment Flight ({{ flight.schedule_routes.length }} segments)
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
                          <h5 class="text-success mb-1">{{ getAirportCode(route.origin_airport) }}</h5>
                          <small class="text-muted">{{ route.origin_airport?.name || 'Unknown' }}</small>
                        </div>
                        <div class="col-md-2 text-center">
                          <i class="fas fa-arrow-right text-primary"></i>
                          <br>
                          <small class="text-muted">{{ route.formatted_duration || 'N/A' }}</small>
                        </div>
                        <div class="col-md-4 text-center">
                          <h5 class="text-success mb-1">{{ getAirportCode(route.destination_airport) }}</h5>
                          <small class="text-muted">{{ route.destination_airport?.name || 'Unknown' }}</small>
                        </div>
                        <div class="col-md-1 text-center">
                          <small class="text-muted">
                            <div>{{ formatTime(route.departure_time) }}</div>
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
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                  <i class="fas fa-clock me-2"></i>Schedule Information
                </h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="text-primary mb-3">
                      <i class="fas fa-calendar-alt me-2"></i>Scheduled Times
                    </h6>
                    <dl class="row">
                      <dt class="col-sm-5">Departure:</dt>
                      <dd class="col-sm-7">{{ formatDateTime(flight.total_departure_time) }}</dd>

                      <dt class="col-sm-5">Arrival:</dt>
                      <dd class="col-sm-7">{{ formatDateTime(flight.total_arrival_time) }}</dd>

                      <dt class="col-sm-5">Duration:</dt>
                      <dd class="col-sm-7">{{ flightDuration }}</dd>

                      <dt class="col-sm-5">Segments:</dt>
                      <dd class="col-sm-7">{{ flight.total_segments || flight.schedule_routes?.length || 'N/A' }}</dd>
                    </dl>
                  </div>

                  <div class="col-md-6">
                    <h6 class="text-success mb-3">
                      <i class="fas fa-check-circle me-2"></i>Actual Times
                    </h6>
                    <dl class="row">
                      <dt class="col-sm-5">Departure:</dt>
                      <dd class="col-sm-7">
                        <span v-if="getFirstActualDeparture()" class="text-success">
                          {{ formatDateTime(getFirstActualDeparture()) }}
                        </span>
                        <span v-else class="text-muted">Not departed yet</span>
                      </dd>

                      <dt class="col-sm-5">Arrival:</dt>
                      <dd class="col-sm-7">
                        <span v-if="getLastActualArrival()" class="text-success">
                          {{ formatDateTime(getLastActualArrival()) }}
                        </span>
                        <span v-else class="text-muted">Not arrived yet</span>
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
            <div class="card border-0 shadow-sm mb-3" v-if="flight.aircraft">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                  <i class="fas fa-plane me-2"></i>Aircraft Details
                </h5>
              </div>
              <div class="card-body">
                <dl class="row mb-0">
                  <dt class="col-sm-6">Registration:</dt>
                  <dd class="col-sm-6">{{ flight.aircraft.registration_number }}</dd>

                  <dt class="col-sm-6">Manufacturer:</dt>
                  <dd class="col-sm-6">{{ flight.aircraft.manufacturer?.name || 'N/A' }}</dd>

                  <dt class="col-sm-6">Model:</dt>
                  <dd class="col-sm-6">{{ flight.aircraft.model?.name || 'N/A' }}</dd>

                  <dt class="col-sm-6">Capacity:</dt>
                  <dd class="col-sm-6">{{ flight.capacity || 'N/A' }} seats</dd>
                </dl>
              </div>
            </div>

            <!-- Flight Stats -->
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="fas fa-chart-bar me-2"></i>Flight Statistics
                </h5>
              </div>
              <div class="card-body">
                <dl class="row mb-0">
                  <dt class="col-sm-6">Flight Type:</dt>
                  <dd class="col-sm-6">
                    <span class="badge bg-secondary">{{ flight.flight_type || 'passenger' }}</span>
                  </dd>

                  <dt class="col-sm-6">Passengers:</dt>
                  <dd class="col-sm-6">{{ flight.passenger_count || 0 }}</dd>

                  <dt class="col-sm-6">Available Seats:</dt>
                  <dd class="col-sm-6">{{ Math.max(0, (flight.capacity || 0) - (flight.passenger_count || 0)) }}</dd>

                  <dt class="col-sm-6">Cargo Items:</dt>
                  <dd class="col-sm-6">{{ flight.cargo_items_count || 0 }}</dd>

                  <dt class="col-sm-6">Total Weight:</dt>
                  <dd class="col-sm-6">{{ flight.total_cargo_weight_kg || 0 }} kg</dd>

                  <dt class="col-sm-6">Occupancy:</dt>
                  <dd class="col-sm-6">{{ getOccupancyPercentage() }}%</dd>
                </dl>
              </div>
            </div>
            </div>
          </div>
        </div>

        <!-- Cargo & Passengers Tab -->
        <div v-show="activeTab === 'cargo-passengers'" id="cargo-passengers" role="tabpanel">
        <div class="row">
          <div class="col-lg-6">
            <!-- Passengers Section -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fas fa-users me-2"></i>Passengers ({{ flight.passengers?.length || 0 }})
                </h5>
                <button
                  @click="showPassengerForm = !showPassengerForm"
                  class="btn btn-sm btn-light"
                  v-if="flight.flight_type !== 'cargo'"
                >
                  <i class="fas fa-plus me-1"></i>{{ showPassengerForm ? 'Cancel' : 'Add Passenger' }}
                </button>
              </div>
              <div class="card-body">
                <!-- Add Passenger Form -->
                <div v-if="showPassengerForm && flight.flight_type !== 'cargo'" class="mb-4 p-3 border rounded bg-light">
                  <h6 class="mb-3">
                    <i class="fas fa-user-plus me-2"></i>Add New Passenger
                  </h6>
                  <form @submit.prevent="addPassenger">

                    <!-- Basic Info -->
                    <div class="row mb-3">
                      <div class="col-6">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input
                          v-model="newPassenger.customer_name"
                          type="text"
                          class="form-control form-control-sm"
                          required
                        >
                      </div>
                      <div class="col-6">
                        <label class="form-label">ID/Passport Number</label>
                        <input
                          v-model="newPassenger.id_number"
                          type="text"
                          class="form-control form-control-sm"
                        >
                      </div>
                    </div>

                    <div class="row mb-3">
                      <div class="col-4">
                        <label class="form-label">Passenger Type <span class="text-danger">*</span></label>
                        <select
                          v-model="newPassenger.passenger_type"
                          class="form-select form-select-sm"
                          required
                        >
                          <option value="adult">Adult</option>
                          <option value="child">Child</option>
                          <option value="infant">Infant</option>
                        </select>
                      </div>
                      <div class="col-4">
                        <label class="form-label">Nationality <span class="text-danger">*</span></label>
                        <input
                          v-model="newPassenger.nationality"
                          type="text"
                          class="form-control form-control-sm"
                          required
                        >
                      </div>
                      <div class="col-4">
                        <label class="form-label">ID Type <span class="text-danger">*</span></label>
                        <select
                          v-model="newPassenger.id_type"
                          class="form-select form-select-sm"
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
                      <div class="col-6">
                        <label class="form-label">Phone Number</label>
                        <input
                          v-model="newPassenger.phone_number"
                          type="text"
                          class="form-control form-control-sm"
                        >
                      </div>
                      <div class="col-6">
                        <label class="form-label">Email</label>
                        <input
                          v-model="newPassenger.email"
                          type="email"
                          class="form-control form-control-sm"
                        >
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
                      <button type="button" class="btn btn-sm btn-secondary" @click="showPassengerForm = false">
                        Cancel
                      </button>
                      <button type="submit" class="btn btn-sm btn-primary" :disabled="addingPassenger">
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

                <!-- Passengers List -->
                <div v-if="!flight.passengers || flight.passengers.length === 0" class="text-center py-4 text-muted">
                  <i class="fas fa-user-plus fa-2x mb-2"></i>
                  <p class="mb-0">No passengers added yet</p>
                </div>

                <div v-else class="list-group list-group-flush">
                  <div
                    v-for="passenger in flight.passengers"
                    :key="passenger.id"
                    class="list-group-item d-flex justify-content-between align-items-center"
                  >
                    <div>
                      <h6 class="mb-1">{{ passenger.customer_name || `${passenger.first_name || ''} ${passenger.last_name || ''}`.trim() }}</h6>
                      <small class="text-muted">
                        {{ passenger.passenger_type }} | {{ passenger.nationality }}
                        <span v-if="passenger.checked_in" class="text-success ms-2">
                          <i class="fas fa-check"></i> Checked In
                        </span>
                      </small>
                    </div>
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
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <!-- Cargo Section -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="fas fa-boxes me-2"></i>Cargo ({{ flight.cargo?.length || 0 }})
                </h5>
                <button
                  @click="showCargoForm = !showCargoForm"
                  class="btn btn-sm btn-light"
                  v-if="flight.flight_type !== 'passenger'"
                >
                  <i class="fas fa-plus me-1"></i>{{ showCargoForm ? 'Cancel' : 'Add Cargo' }}
                </button>
              </div>
              <div class="card-body">
                <!-- Add Cargo Form -->
                <div v-if="showCargoForm && flight.flight_type !== 'passenger'" class="mb-4 p-3 border rounded bg-light">
                  <h6 class="mb-3">
                    <i class="fas fa-box me-2"></i>Add New Cargo
                  </h6>
                  <form @submit.prevent="addCargo">
                    <!-- Cargo Name and Type -->
                    <div class="row mb-3">
                      <div class="col-6">
                        <label class="form-label">Cargo Name <span class="text-danger">*</span></label>
                        <input
                          v-model="newCargo.cargo_name"
                          type="text"
                          class="form-control form-control-sm"
                          placeholder="Brief name for the cargo"
                          required
                        >
                      </div>
                      <div class="col-6">
                        <label class="form-label">Cargo Type <span class="text-danger">*</span></label>
                        <select
                          v-model="newCargo.cargo_type"
                          class="form-select form-select-sm"
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
                        <label class="form-label">Description <span class="text-danger">*</span></label>
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
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input
                          v-model="newCargo.quantity"
                          type="number"
                          min="1"
                          class="form-control form-control-sm"
                          required
                        >
                      </div>
                      <div class="col-3">
                        <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                        <input
                          v-model="newCargo.weight_kg"
                          type="number"
                          step="0.1"
                          min="0.1"
                          class="form-control form-control-sm"
                          required
                        >
                      </div>
                      <div class="col-3">
                        <label class="form-label">Dimensions</label>
                        <input
                          v-model="newCargo.dimensions"
                          type="text"
                          class="form-control form-control-sm"
                          placeholder="L x W x H"
                        >
                      </div>
                      <div class="col-3">
                        <label class="form-label">Packaging <span class="text-danger">*</span></label>
                        <select
                          v-model="newCargo.packaging_type"
                          class="form-select form-select-sm"
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
                        <label class="form-label">Shipper Name <span class="text-danger">*</span></label>
                        <input
                          v-model="newCargo.shipper_name"
                          type="text"
                          class="form-control form-control-sm"
                          required
                        >
                      </div>
                      <div class="col-6">
                        <label class="form-label">Receiver Name <span class="text-danger">*</span></label>
                        <input
                          v-model="newCargo.consignee_name"
                          type="text"
                          class="form-control form-control-sm"
                          required
                        >
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
                          >
                          <label class="form-check-label" for="inline-fragile">
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
                          >
                          <label class="form-check-label" for="inline-hazardous">
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
                          >
                          <label class="form-check-label" for="inline-refrigeration">
                            Refrigeration
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                      <button type="button" class="btn btn-sm btn-secondary" @click="showCargoForm = false">
                        Cancel
                      </button>
                      <button type="submit" class="btn btn-sm btn-success" :disabled="addingCargo">
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

                <!-- Cargo List -->
                <div v-if="!flight.cargo || flight.cargo.length === 0" class="text-center py-4 text-muted">
                  <i class="fas fa-box fa-2x mb-2"></i>
                  <p class="mb-0">No cargo added yet</p>
                </div>

                <div v-else class="list-group list-group-flush">
                  <div
                    v-for="cargo in flight.cargo"
                    :key="cargo.id"
                    class="list-group-item"
                  >
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ getCargoName(cargo) }}</h6>
                        <p class="mb-1 small">{{ cargo.description }}</p>
                        <div class="row small text-muted">
                          <div class="col-6">
                            <strong>Weight:</strong> {{ cargo.weight_kg }} kg
                          </div>
                          <div class="col-6">
                            <strong>Type:</strong> {{ cargo.cargo_type }}
                          </div>
                        </div>
                        <div class="row small text-muted">
                          <div class="col-6">
                            <strong>From:</strong> {{ cargo.shipper_name }}
                          </div>
                          <div class="col-6">
                            <strong>To:</strong> {{ cargo.consignee_name }}
                          </div>
                        </div>
                        <div v-if="cargo.fragile || cargo.hazardous || cargo.requires_refrigeration" class="mt-1">
                          <span v-if="cargo.fragile" class="badge bg-warning text-dark me-1">Fragile</span>
                          <span v-if="cargo.hazardous" class="badge bg-danger me-1">Hazardous</span>
                          <span v-if="cargo.requires_refrigeration" class="badge bg-info me-1">Refrigeration</span>
                        </div>
                      </div>
                      <div class="ms-3">
                        <span
                          class="badge mb-2"
                          :class="`bg-${getStatusColor(cargo.status)}`"
                        >
                          {{ cargo.status }}
                        </span>
                        <div class="btn-group btn-group-sm d-block">
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
                      </div>
                    </div>
                  </div>
                </div>
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
            <button type="button" class="btn-close" @click="showAddPassengerModal = false"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addPassenger">

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                  <input
                    v-model="newPassenger.customer_name"
                    type="text"
                    class="form-control"
                    required
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">ID/Passport Number</label>
                  <input
                    v-model="newPassenger.id_number"
                    type="text"
                    class="form-control"
                  >
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nationality <span class="text-danger">*</span></label>
                  <input
                    v-model="newPassenger.nationality"
                    type="text"
                    class="form-control"
                    required
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">ID Type <span class="text-danger">*</span></label>
                  <select
                    v-model="newPassenger.id_type"
                    class="form-select"
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
                  <label class="form-label">Passenger Type <span class="text-danger">*</span></label>
                  <select
                    v-model="newPassenger.passenger_type"
                    class="form-select"
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
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input
                    v-model="newPassenger.email"
                    type="email"
                    class="form-control"
                  >
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input
                    v-model="newPassenger.date_of_birth"
                    type="date"
                    class="form-control"
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">Gender</label>
                  <select
                    v-model="newPassenger.gender"
                    class="form-select"
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
                <button type="button" class="btn btn-secondary" @click="showAddPassengerModal = false">
                  Cancel
                </button>
                <button type="submit" class="btn btn-primary" :disabled="addingPassenger">
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
            <button type="button" class="btn-close" @click="showAddCargoModal = false"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="addCargo">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Cargo Name <span class="text-danger">*</span></label>
                  <input
                    v-model="newCargo.cargo_name"
                    type="text"
                    class="form-control"
                    placeholder="Brief name for the cargo"
                    required
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">Cargo Type <span class="text-danger">*</span></label>
                  <select
                    v-model="newCargo.cargo_type"
                    class="form-select"
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
                <label class="form-label">Description <span class="text-danger">*</span></label>
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
                  <label class="form-label">Quantity <span class="text-danger">*</span></label>
                  <input
                    v-model="newCargo.quantity"
                    type="number"
                    min="1"
                    class="form-control"
                    required
                  >
                </div>
                <div class="col-md-4">
                  <label class="form-label">Total Weight (kg) <span class="text-danger">*</span></label>
                  <input
                    v-model="newCargo.weight_kg"
                    type="number"
                    step="0.1"
                    min="0.1"
                    class="form-control"
                    required
                  >
                </div>
                <div class="col-md-4">
                  <label class="form-label">Packaging Type <span class="text-danger">*</span></label>
                  <select
                    v-model="newCargo.packaging_type"
                    class="form-select"
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
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">Declared Value</label>
                  <div class="input-group">
                    <select
                      v-model="newCargo.currency"
                      class="form-select"
                      style="max-width: 80px;"
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
                    >
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Shipper Name <span class="text-danger">*</span></label>
                  <input
                    v-model="newCargo.shipper_name"
                    type="text"
                    class="form-control"
                    required
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">Receiver Name <span class="text-danger">*</span></label>
                  <input
                    v-model="newCargo.consignee_name"
                    type="text"
                    class="form-control"
                    required
                  >
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
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">Receiver Contact</label>
                  <input
                    v-model="newCargo.consignee_contact"
                    type="text"
                    class="form-control"
                    placeholder="Phone or email"
                  >
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
                    >
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
                    >
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
                    >
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
                <button type="button" class="btn btn-secondary" @click="showAddCargoModal = false">
                  Cancel
                </button>
                <button type="submit" class="btn btn-success" :disabled="addingCargo">
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
    <div v-if="showAddPassengerModal || showAddCargoModal" class="modal-backdrop fade show"></div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'ScheduledFlightShow',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    flight: Object,
  },
  data() {
    return {
      activeTab: 'details',
      showAddPassengerModal: false,
      showAddCargoModal: false,
      showPassengerForm: false,
      showCargoForm: false,
      addingPassenger: false,
      addingCargo: false,
      newPassenger: this.getEmptyPassenger(),
      newCargo: this.getEmptyCargo(),
    }
  },
  computed: {
    aircraftModel() {
      if (this.flight.aircraft?.manufacturer?.name && this.flight.aircraft?.model?.name) {
        return `${this.flight.aircraft.manufacturer.name} ${this.flight.aircraft.model.name}`
      }
      return 'N/A'
    },
    flightDuration() {
      if (!this.flight.total_departure_time || !this.flight.total_arrival_time) {
        return 'N/A'
      }

      const departure = new Date(this.flight.total_departure_time)
      const arrival = new Date(this.flight.total_arrival_time)
      const diff = arrival - departure

      if (diff <= 0) return 'N/A'

      const hours = Math.floor(diff / (1000 * 60 * 60))
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))

      return `${hours}h ${minutes}m`
    },
    isOnTime() {
      const firstActual = this.getFirstActualDeparture()
      if (!firstActual || !this.flight.total_departure_time) {
        return false
      }

      const scheduled = new Date(this.flight.total_departure_time)
      const actual = new Date(firstActual)
      const delayMinutes = (actual - scheduled) / (1000 * 60)

      return delayMinutes <= 15
    },
    isDelayed() {
      const firstActual = this.getFirstActualDeparture()
      if (!firstActual || !this.flight.total_departure_time) {
        const now = new Date()
        const scheduled = new Date(this.flight.total_departure_time)
        return now > scheduled && !firstActual
      }

      const scheduled = new Date(this.flight.total_departure_time)
      const actual = new Date(firstActual)
      const delayMinutes = (actual - scheduled) / (1000 * 60)

      return delayMinutes > 15
    }
  },
  methods: {
    getEmptyPassenger() {
      return {
        scheduled_flight_id: this.flight.id,
        customer_name: '',
        id_number: '',
        id_type: 'passport',
        nationality: '',
        date_of_birth: '',
        gender: '',
        phone_number: '',
        email: '',
        special_requirements: '',
        passenger_type: 'adult',
        seat_preference: '',
        notes: ''
      }
    },
    getEmptyCargo() {
      return {
        scheduled_flight_id: this.flight.id,
        cargo_name: '',
        description: '',
        cargo_type: '',
        quantity: 1,
        weight_kg: '',
        volume_m3: '',
        dimensions: '',
        packaging_type: '',
        shipper_name: '',
        shipper_contact: '',
        consignee_name: '',
        consignee_contact: '',
        declared_value: '',
        currency: 'USD',
        fragile: false,
        hazardous: false,
        requires_refrigeration: false,
        special_handling_instructions: '',
        notes: ''
      }
    },
    async addPassenger() {
      this.addingPassenger = true
      try {
        const response = await fetch(route('flight-passengers.store'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify(this.newPassenger)
        })

        const data = await response.json()

        if (response.ok) {
          this.showAddPassengerModal = false
          this.showPassengerForm = false
          this.newPassenger = this.getEmptyPassenger()
          // Reload the page to show updated data
          router.reload()
        } else {
          alert('Error adding passenger: ' + data.message)
        }
      } catch (error) {
        alert('Error adding passenger: ' + error.message)
      } finally {
        this.addingPassenger = false
      }
    },
    async addCargo() {
      this.addingCargo = true
      try {
        const response = await fetch(route('flight-cargo.store'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          },
          body: JSON.stringify(this.newCargo)
        })

        const data = await response.json()

        if (response.ok) {
          this.showAddCargoModal = false
          this.showCargoForm = false
          this.newCargo = this.getEmptyCargo()
          // Reload the page to show updated data
          router.reload()
        } else {
          alert('Error adding cargo: ' + data.message)
        }
      } catch (error) {
        alert('Error adding cargo: ' + error.message)
      } finally {
        this.addingCargo = false
      }
    },
    async removePassenger(passenger) {
      if (confirm(`Are you sure you want to remove ${passenger.customer_name || `${passenger.first_name || ''} ${passenger.last_name || ''}`.trim()} from this flight?`)) {
        try {
          const response = await fetch(route('flight-passengers.destroy', passenger.id), {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
          })

          if (response.ok) {
            router.reload()
          } else {
            const data = await response.json()
            alert('Error removing passenger: ' + data.message)
          }
        } catch (error) {
          alert('Error removing passenger: ' + error.message)
        }
      }
    },
    async removeCargo(cargo) {
      if (confirm(`Are you sure you want to remove this cargo item from the flight?`)) {
        try {
          const response = await fetch(route('flight-cargo.destroy', cargo.id), {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
          })

          if (response.ok) {
            router.reload()
          } else {
            const data = await response.json()
            alert('Error removing cargo: ' + data.message)
          }
        } catch (error) {
          alert('Error removing cargo: ' + error.message)
        }
      }
    },
    async checkInPassenger(passenger) {
      try {
        const response = await fetch(route('flight-passengers.check-in', passenger.id), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
          }
        })

        if (response.ok) {
          router.reload()
        } else {
          const data = await response.json()
          alert('Error checking in passenger: ' + data.message)
        }
      } catch (error) {
        alert('Error checking in passenger: ' + error.message)
      }
    },
    editPassenger(passenger) {
      // TODO: Implement edit passenger modal
      alert('Edit passenger functionality will be implemented next')
    },
    editCargo(cargo) {
      // TODO: Implement edit cargo modal
      alert('Edit cargo functionality will be implemented next')
    },
    formatDateTime(datetime) {
      if (!datetime) return 'N/A'
      return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    formatTime(datetime) {
      if (!datetime) return 'N/A'
      return new Date(datetime).toLocaleString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
      })
    },
    getAirportCode(airport) {
      if (!airport) return 'N/A'
      return airport.iata_code || airport.icao_code || 'N/A'
    },
    getRouteOverview() {
      if (!this.flight.schedule_routes || this.flight.schedule_routes.length === 0) {
        return 'No routes'
      }

      if (this.flight.schedule_routes.length === 1) {
        const route = this.flight.schedule_routes[0]
        const origin = this.getAirportCode(route.origin_airport)
        const destination = this.getAirportCode(route.destination_airport)
        return `${origin} → ${destination}`
      }

      // Multi-segment route
      const airports = [this.getAirportCode(this.flight.schedule_routes[0].origin_airport)]
      this.flight.schedule_routes.forEach(route => {
        airports.push(this.getAirportCode(route.destination_airport))
      })
      return airports.join(' → ')
    },
    getFirstActualDeparture() {
      if (!this.flight.schedule_routes || this.flight.schedule_routes.length === 0) {
        return null
      }
      return this.flight.schedule_routes[0].actual_departure_time
    },
    getLastActualArrival() {
      if (!this.flight.schedule_routes || this.flight.schedule_routes.length === 0) {
        return null
      }
      const lastRoute = this.flight.schedule_routes[this.flight.schedule_routes.length - 1]
      return lastRoute.actual_arrival_time
    },
    getOccupancyPercentage() {
      if (!this.flight.capacity) return 0
      return Math.min(100, Math.round(((this.flight.passenger_count || 0) / this.flight.capacity) * 100))
    },
    getCargoName(cargo) {
      // Extract cargo name from description if it contains the pattern "Name: Description"
      if (cargo.description && cargo.description.includes(':')) {
        return cargo.description.split(':')[0].trim()
      }
      return cargo.cargo_type || 'Cargo'
    },
    getStatusColor(status) {
      const colors = {
        'pending': 'warning',
        'loaded': 'info',
        'in_transit': 'primary',
        'delivered': 'success',
        'damaged': 'danger'
      }
      return colors[status] || 'secondary'
    },
    getCustomerDisplayName(customer) {
      if (!customer) return 'Unknown'
      if (customer.type === 'corporate') {
        return customer.company_name || 'Corporate Customer'
      }
      return `${customer.first_name || ''} ${customer.last_name || ''}`.trim() || 'Individual Customer'
    }
  }
}
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
</style>