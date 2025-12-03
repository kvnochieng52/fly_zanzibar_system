<template>
  <Head :title="`Aircraft ${aircraft.registration_number}`" />

  <DashboardLayout>
    <!-- Page Header with Aircraft Details and Stats in One Row -->
    <div class="row align-items-center mb-4">
      <!-- Left Side: Aircraft Photo, Name, and Details -->
      <div class="col-lg-4">
        <div class="d-flex align-items-center">
          <div
            class="flex-shrink-0"
            style="width: 60px; height: 60px; margin-right: 12px"
          >
            <div
              class="w-100 h-100 bg-primary d-flex align-items-center justify-content-center rounded"
            >
              <i class="fas fa-plane text-white fs-4"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <nav aria-label="breadcrumb" class="mb-1">
              <ol class="breadcrumb breadcrumb-sm mb-0">
                <li class="breadcrumb-item">
                  <Link
                    :href="route('aircraft.index')"
                    class="text-decoration-none"
                    >Aircraft</Link
                  >
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                  {{ aircraft.registration_number }}
                </li>
              </ol>
            </nav>
            <h1 class="h5 mb-1 text-gray-900">
              {{ aircraft.manufacturer?.name }} {{ aircraft.model?.name }}
            </h1>
            <div class="d-flex flex-column gap-1">
              <span class="text-muted small"
                >{{ aircraft.registration_number }} • S/N:
                {{ aircraft.serial_number }}</span
              >
              <span
                class="badge rounded-pill align-self-start small"
                :style="{
                  backgroundColor: aircraft.status_color + '20' || '#6c757d20',
                  color: aircraft.status_color || '#6c757d',
                  border: '1px solid ' + (aircraft.status_color || '#6c757d'),
                }"
              >
                {{ aircraft.status?.name || "N/A" }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side: Quick Stats -->
      <div class="col-lg-8">
        <div class="row">
          <div class="col-4">
            <div class="card border-0 shadow-sm h-100 stat-card stat-primary">
              <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon me-3">
                  <i class="fas fa-clock"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="stat-label text-white-75 small">Total Hours</div>
                  <div class="stat-value text-white fw-bold">
                    {{ formatHours(aircraft.total_airframe_hours) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-4">
            <div class="card border-0 shadow-sm h-100 stat-card stat-info">
              <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon me-3">
                  <i class="fas fa-sync-alt"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="stat-label text-white-75 small">Total Cycles</div>
                  <div class="stat-value text-white fw-bold">
                    {{ aircraft.total_cycles || 0 }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-4">
            <div class="card border-0 shadow-sm h-100 stat-card stat-warning">
              <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon me-3">
                  <i class="fas fa-calendar"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="stat-label text-white-75 small">Aircraft Age</div>
                  <div class="stat-value text-white fw-bold small">
                    {{ aircraft.age_in_years || 0 }} years
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs Navigation with Action Buttons -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent">
        <div class="d-flex justify-content-between align-items-center">
          <ul class="nav nav-pills" id="aircraftTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'overview' }]"
              id="overview-tab"
              type="button"
              role="tab"
              @click="activeTab = 'overview'"
            >
              <i class="fas fa-info-circle me-2"></i>Overview
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'maintenance' }]"
              id="maintenance-tab"
              type="button"
              role="tab"
              @click="activeTab = 'maintenance'"
            >
              <i class="fas fa-wrench me-2"></i>Maintenance
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'documents' }]"
              id="documents-tab"
              type="button"
              role="tab"
              @click="activeTab = 'documents'"
            >
              <i class="fas fa-file-alt me-2"></i>Documents
            </button>
          </li>
        </ul>

          <!-- Action Buttons on the Right -->
          <div class="d-flex gap-2">
            <a :href="`/aircraft/${aircraft.id}/edit`" class="btn btn-warning btn-sm">
              <i class="fas fa-edit me-1"></i>Edit
            </a>
            <button
              v-if="activeTab === 'documents'"
              class="btn btn-primary btn-sm"
              type="button"
              @click="showAddDocumentModal = true"
            >
              <i class="fas fa-file-plus me-1"></i>Add Document
            </button>
            <a href="/aircraft" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Back
            </a>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="tab-content" id="aircraftTabsContent">
          <!-- Overview Tab -->
          <div v-show="activeTab === 'overview'" id="overview" role="tabpanel">
            <div class="row">
              <!-- Left Column -->
              <div class="col-lg-8">
                <!-- Aircraft Information -->
                <div class="mb-4">
                  <h5 class="mb-3">Aircraft Information</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Status</label>
                            <div class="info-value">
                              <span
                                class="badge rounded-pill fs-6 px-3 py-2"
                                :style="{
                                  backgroundColor: aircraft.status_color + '20',
                                  color: aircraft.status_color,
                                  border: '1px solid ' + aircraft.status_color,
                                }"
                              >
                                {{ aircraft.status?.name }}
                              </span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Home Base</label>
                            <div class="info-value">
                              {{ aircraft.home_base }}
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label"
                              >Year of Manufacture</label
                            >
                            <div class="info-value">
                              {{ aircraft.year_of_manufacture }} ({{
                                aircraft.age_in_years
                              }}
                              years old)
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label"
                              >Total Hours & Cycles</label
                            >
                            <div class="info-value">
                              {{
                                formatHours(aircraft.total_airframe_hours)
                              }}
                              hours • {{ aircraft.total_cycles }} cycles
                            </div>
                          </div>
                        </div>
                        <div
                          v-if="aircraft.seating_configuration"
                          class="col-md-12"
                        >
                          <div class="info-item">
                            <label class="info-label"
                              >Seating Configuration</label
                            >
                            <div class="info-value">
                              {{ aircraft.seating_configuration }}
                            </div>
                          </div>
                        </div>
                        <div v-if="aircraft.notes" class="col-md-12">
                          <div class="info-item">
                            <label class="info-label">Notes</label>
                            <div class="info-value">{{ aircraft.notes }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column -->
              <div class="col-lg-4">
                <!-- Compliance Status Card -->
                <div class="card border-0 shadow-sm mb-3">
                  <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="mb-0 fw-bold">
                      <i class="fas fa-shield-alt text-success me-2"></i
                      >Compliance Status
                    </h5>
                  </div>
                  <div class="card-body text-center">
                    <div class="compliance-chart mb-3">
                      <div
                        class="progress-circle"
                        :style="`--progress: ${complianceStatus.compliance_percentage}`"
                      >
                        <div class="progress-text">
                          <div class="percentage">
                            {{ complianceStatus.compliance_percentage }}%
                          </div>
                          <div class="label small text-muted">Compliant</div>
                        </div>
                      </div>
                    </div>
                    <div class="row text-center">
                      <div class="col-3">
                        <div class="stat-number text-success">
                          {{ complianceStatus.valid }}
                        </div>
                        <div class="stat-label small text-muted">Valid</div>
                      </div>
                      <div class="col-3">
                        <div class="stat-number text-warning">
                          {{ complianceStatus.expiring }}
                        </div>
                        <div class="stat-label small text-muted">Expiring</div>
                      </div>
                      <div class="col-3">
                        <div class="stat-number text-danger">
                          {{ complianceStatus.expired }}
                        </div>
                        <div class="stat-label small text-muted">Expired</div>
                      </div>
                      <div class="col-3">
                        <div class="stat-number text-info">
                          {{ complianceStatus.pending }}
                        </div>
                        <div class="stat-label small text-muted">Pending</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Photos Card -->
                <div
                  v-if="aircraft.photos && aircraft.photos.length > 0"
                  class="card border-0 shadow-sm"
                >
                  <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="mb-0 fw-bold">
                      <i class="fas fa-images text-info me-2"></i>Photos
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="photo-gallery">
                      <div
                        v-for="(photo, index) in aircraft.photos"
                        :key="index"
                        class="photo-thumbnail mb-2"
                      >
                        <img
                          :src="`/storage/${photo}`"
                          :alt="`Aircraft ${
                            aircraft.registration_number
                          } photo ${index + 1}`"
                          class="img-fluid rounded cursor-pointer"
                          @click="showPhotoModal(photo)"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Maintenance Tab -->
          <div
            v-show="activeTab === 'maintenance'"
            id="maintenance"
            role="tabpanel"
          >
            <!-- Maintenance Summary Cards -->
            <div class="row mb-4">
              <!-- Current Maintenance Status -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div
                  class="card border-0 shadow-sm h-100 stat-card stat-success"
                >
                  <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon me-3">
                      <i class="fas fa-check"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="stat-label text-white-75 small">Current</div>
                      <div class="stat-value text-white fw-bold">8 items</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Due Soon -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div
                  class="card border-0 shadow-sm h-100 stat-card stat-warning"
                >
                  <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon me-3">
                      <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="stat-label text-white-75 small">Due Soon</div>
                      <div class="stat-value text-white fw-bold">2 items</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Overdue -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div
                  class="card border-0 shadow-sm h-100 stat-card stat-danger"
                >
                  <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon me-3">
                      <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="stat-label text-white-75 small">Overdue</div>
                      <div class="stat-value text-white fw-bold">0 items</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Active Work Orders -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div
                  class="card border-0 shadow-sm h-100 stat-card stat-primary"
                >
                  <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon me-3">
                      <i class="fas fa-tools"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="stat-label text-white-75 small">
                        Active Work Orders
                      </div>
                      <div class="stat-value text-white fw-bold">1 item</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Upcoming Maintenance -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0 fw-bold">
                    <i class="fas fa-calendar-alt text-warning me-2"></i
                    >Upcoming Maintenance
                  </h5>
                  <button
                    class="btn btn-sm btn-primary"
                    @click="showScheduleMaintenanceModal = true"
                  >
                    <i class="fas fa-plus me-1"></i>Schedule Maintenance
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div v-if="!maintenanceSchedules || maintenanceSchedules.length === 0" class="text-center py-5">
                  <i
                    class="fas fa-calendar-alt text-muted mb-3"
                    style="font-size: 3rem"
                  ></i>
                  <h6 class="text-muted">No maintenance schedules</h6>
                  <p class="text-muted mb-3">
                    Schedule maintenance tasks to track upcoming requirements.
                  </p>
                  <button
                    class="btn btn-primary"
                    @click="showScheduleMaintenanceModal = true"
                  >
                    <i class="fas fa-plus me-2"></i>Schedule First Maintenance
                  </button>
                </div>
                <div v-else class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Schedule Name</th>
                        <th>Type</th>
                        <th>Due By</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="schedule in maintenanceSchedules" :key="schedule.id">
                        <td>
                          <div class="d-flex align-items-center">
                            <div
                              class="maintenance-type-icon me-2 bg-warning bg-opacity-10 rounded p-2"
                            >
                              <i class="fas fa-wrench text-warning"></i>
                            </div>
                            <div>
                              <div class="fw-medium">{{ schedule.schedule_name }}</div>
                              <div class="small text-muted">
                                {{ schedule.description || 'No description' }}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <span class="badge bg-info">{{ schedule.maintenanceType?.name || schedule.maintenance_type?.name || 'General Maintenance' }}</span>
                        </td>
                        <td>
                          <div class="small">
                            <div v-if="schedule.due_hours">{{ schedule.due_hours }} hours</div>
                            <div v-if="schedule.due_cycles">{{ schedule.due_cycles }} cycles</div>
                            <div v-if="schedule.due_calendar_date">{{ formatDate(schedule.due_calendar_date) }}</div>
                            <div v-if="!schedule.due_hours && !schedule.due_cycles && !schedule.due_calendar_date" class="text-muted">No due criteria</div>
                          </div>
                        </td>
                        <td>
                          <span
                            class="badge"
                            :class="{
                              'bg-success': schedule.compliance_status === 'current',
                              'bg-warning': schedule.compliance_status === 'due_soon',
                              'bg-danger': schedule.compliance_status === 'overdue'
                            }"
                          >
                            {{ schedule.compliance_status?.replace('_', ' ') || 'N/A' }}
                          </span>
                        </td>
                        <td>
                          <span
                            class="badge"
                            :class="{
                              'bg-danger': schedule.priority === 'critical',
                              'bg-warning': schedule.priority === 'high',
                              'bg-info': schedule.priority === 'normal',
                              'bg-secondary': schedule.priority === 'low'
                            }"
                          >
                            {{ schedule.priority?.toUpperCase() || 'N/A' }}
                          </span>
                        </td>
                        <td>
                          <div class="btn-group btn-group-sm">
                            <button
                              class="btn btn-outline-primary"
                              @click="showScheduleMaintenanceModal = true"
                              title="Create new schedule"
                            >
                              <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button
                              class="btn btn-outline-secondary"
                              title="View maintenance details"
                              @click="viewMaintenanceDetails(schedule)"
                            >
                              <i class="fas fa-eye"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Active Work Orders -->
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0 fw-bold">
                    <i class="fas fa-clipboard-list text-primary me-2"></i
                    >Active Work Orders
                  </h5>
                  <button
                    class="btn btn-sm btn-success"
                    @click="showCreateWorkOrderModal = true"
                  >
                    <i class="fas fa-plus me-1"></i>Create Work Order
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div v-if="!workOrders || workOrders.length === 0" class="text-center py-5">
                  <i
                    class="fas fa-clipboard-list text-muted mb-3"
                    style="font-size: 3rem"
                  ></i>
                  <h6 class="text-muted">No work orders</h6>
                  <p class="text-muted mb-3">
                    Create work orders to track maintenance tasks and monitor
                    progress.
                  </p>
                  <button
                    class="btn btn-success"
                    @click="showCreateWorkOrderModal = true"
                  >
                    <i class="fas fa-plus me-2"></i>Create First Work Order
                  </button>
                </div>
                <div v-else class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Work Order</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="workOrder in workOrders" :key="workOrder.id">
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="work-order-icon me-2 bg-primary bg-opacity-10 rounded p-2">
                              <i class="fas fa-tools text-primary"></i>
                            </div>
                            <div>
                              <div class="fw-medium">{{ workOrder.work_order_number }}</div>
                              <div class="small text-muted">{{ workOrder.title || workOrder.description }}</div>
                            </div>
                          </div>
                        </td>
                        <td>
                          <span class="badge bg-secondary">{{ workOrder.type || workOrder.work_scope || 'Corrective' }}</span>
                        </td>
                        <td>
                          <span
                            class="badge"
                            :class="{
                              'bg-success': workOrder.status?.name === 'Completed',
                              'bg-warning': workOrder.status?.name === 'In Progress',
                              'bg-info': workOrder.status?.name === 'Planned',
                              'bg-danger': workOrder.status?.name === 'On Hold',
                              'bg-secondary': !workOrder.status
                            }"
                          >
                            {{ workOrder.status?.name || workOrder.workOrderStatus?.name || 'Open' }}
                          </span>
                        </td>
                        <td>
                          <span
                            class="badge"
                            :class="{
                              'bg-danger': workOrder.priority?.code === 'AOG' || workOrder.priority?.code === 'CRITICAL',
                              'bg-warning': workOrder.priority?.code === 'HIGH',
                              'bg-info': workOrder.priority?.code === 'NORMAL',
                              'bg-secondary': workOrder.priority?.code === 'LOW'
                            }"
                          >
                            {{ workOrder.priority?.name || (typeof workOrder.priority === 'string' ? workOrder.priority.toUpperCase() : 'NORMAL') }}
                          </span>
                        </td>
                        <td>
                          <div class="small">
                            {{ workOrder.assigned_user?.name || workOrder.assignedTo?.name || 'Unassigned' }}
                          </div>
                        </td>
                        <td>
                          <div v-if="workOrder.due_date || workOrder.scheduled_completion" class="small">
                            {{ formatDate(workOrder.due_date || workOrder.scheduled_completion) }}
                          </div>
                          <div v-else class="text-muted small">No due date</div>
                        </td>
                        <td>
                          <div class="btn-group btn-group-sm">
                            <button
                              class="btn btn-outline-primary"
                              @click="openStatusUpdateModal(workOrder)"
                              title="Update Status"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              class="btn btn-outline-secondary"
                              title="View Work Order Details"
                              @click="viewWorkOrderDetails(workOrder)"
                            >
                              <i class="fas fa-eye"></i>
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

          <!-- Documents Tab -->
          <div
            v-show="activeTab === 'documents'"
            id="documents"
            role="tabpanel"
          >
            <div class="card border-0 bg-light">
              <div class="card-body">
                <div
                  v-if="Object.keys(documentsByType).length === 0"
                  class="text-center py-5"
                >
                  <i
                    class="fas fa-file-alt text-muted mb-3"
                    style="font-size: 3rem"
                  ></i>
                  <h6 class="text-muted">No documents uploaded</h6>
                  <p class="text-muted mb-3">
                    Upload compliance documents to track expiry dates and
                    maintain regulatory compliance.
                  </p>
                  <button
                    class="btn btn-primary"
                    @click="showAddDocumentModal = true"
                  >
                    <i class="fas fa-plus me-2"></i>Add First Document
                  </button>
                </div>

                <div v-else>
                  <div
                    v-for="(documents, docType) in documentsByType"
                    :key="docType"
                    class="document-section mb-4"
                  >
                    <h6 class="section-title mb-3">
                      <i class="fas fa-folder-open me-2"></i>{{ docType }}
                      <span class="badge bg-light text-dark ms-2">{{
                        documents.length
                      }}</span>
                    </h6>

                    <div class="row">
                      <div
                        v-for="document in documents"
                        :key="document.id"
                        class="col-lg-6 mb-3"
                      >
                        <div class="document-card p-3 border rounded">
                          <div
                            class="d-flex justify-content-between align-items-start mb-2"
                          >
                            <div class="flex-grow-1">
                              <div class="d-flex align-items-center mb-1">
                                <span
                                  class="compliance-indicator me-2"
                                  :class="getDocumentStatusClass(document)"
                                  :title="getDocumentStatusTitle(document)"
                                ></span>
                                <strong class="document-number">{{
                                  document.document_number || "No number"
                                }}</strong>
                              </div>
                              <div
                                v-if="document.issuing_authority"
                                class="small text-muted mb-1"
                              >
                                <i class="fas fa-building me-1"></i
                                >{{ document.issuing_authority }}
                              </div>
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                type="button"
                                :id="`docDropdown${document.id}`"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                              >
                                <i class="fas fa-ellipsis-v"></i>
                              </button>
                              <ul
                                class="dropdown-menu"
                                :aria-labelledby="`docDropdown${document.id}`"
                              >
                                <li>
                                  <button
                                    class="dropdown-item"
                                    @click="editDocument(document)"
                                  >
                                    <i class="fas fa-edit me-2"></i>Edit
                                  </button>
                                </li>
                                <li>
                                  <button
                                    class="dropdown-item text-danger"
                                    @click="deleteDocument(document)"
                                  >
                                    <i class="fas fa-trash me-2"></i>Delete
                                  </button>
                                </li>
                              </ul>
                            </div>
                          </div>

                          <div class="document-dates">
                            <div
                              v-if="document.issue_date"
                              class="small text-muted mb-1"
                            >
                              <i class="fas fa-calendar me-1"></i>
                              <strong>Issued:</strong>
                              {{ formatDate(document.issue_date) }}
                            </div>
                            <div
                              v-if="document.expiry_date"
                              class="small mb-1"
                              :class="getExpiryDateClass(document)"
                            >
                              <i class="fas fa-calendar-times me-1"></i>
                              <strong>Expires:</strong>
                              {{ formatDate(document.expiry_date) }}
                              <span
                                v-if="document.days_until_expiry !== null"
                                class="ms-1"
                              >
                                ({{
                                  document.days_until_expiry > 0
                                    ? document.days_until_expiry + " days"
                                    : "Expired"
                                }})
                              </span>
                            </div>
                          </div>

                          <div v-if="document.file_path" class="mt-2">
                            <a
                              :href="`/storage/${document.file_path}`"
                              target="_blank"
                              class="btn btn-sm btn-outline-info"
                            >
                              <i class="fas fa-download me-1"></i>View Document
                            </a>
                          </div>

                          <div v-if="document.notes" class="mt-2">
                            <small class="text-muted">{{
                              document.notes
                            }}</small>
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

    <!-- Add Document Modal -->
    <div
      v-if="showAddDocumentModal"
      class="modal fade show d-block"
      style="background-color: rgba(0, 0, 0, 0.5)"
      @click="showAddDocumentModal = false"
    >
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Compliance Document</h5>
            <button
              type="button"
              class="btn-close"
              @click="closeDocumentModal"
            ></button>
          </div>
          <form @submit.prevent="submitDocument">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="documentType" class="form-label"
                    >Document Type</label
                  >
                  <select
                    v-model="documentForm.document_type_id"
                    id="documentType"
                    class="form-select custom-select"
                    required
                  >
                    <option value="">Select Document Type</option>
                    <option
                      v-for="docType in documentTypes"
                      :key="docType.id"
                      :value="docType.id"
                    >
                      {{ docType.name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="documentNumber" class="form-label"
                    >Document Number</label
                  >
                  <input
                    v-model="documentForm.document_number"
                    type="text"
                    id="documentNumber"
                    class="form-control"
                    placeholder="Enter document number"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="issueDate" class="form-label">Issue Date</label>
                  <input
                    v-model="documentForm.issue_date"
                    type="date"
                    id="issueDate"
                    class="form-control"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="expiryDate" class="form-label">Expiry Date</label>
                  <input
                    v-model="documentForm.expiry_date"
                    type="date"
                    id="expiryDate"
                    class="form-control"
                  />
                </div>
                <div class="col-md-12 mb-3">
                  <label for="issuingAuthority" class="form-label"
                    >Issuing Authority</label
                  >
                  <input
                    v-model="documentForm.issuing_authority"
                    type="text"
                    id="issuingAuthority"
                    class="form-control"
                    placeholder="E.g., TCAA, EASA, FAA"
                  />
                </div>
                <div class="col-md-12 mb-3">
                  <label for="documentFile" class="form-label"
                    >Upload Document</label
                  >
                  <input
                    type="file"
                    id="documentFile"
                    class="form-control"
                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    @change="handleFileUpload"
                  />
                </div>
                <div class="col-md-12">
                  <label for="documentNotes" class="form-label">Notes</label>
                  <textarea
                    v-model="documentForm.notes"
                    id="documentNotes"
                    class="form-control"
                    rows="3"
                    placeholder="Additional notes..."
                  ></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="closeDocumentModal"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="isSubmitting"
              >
                <i v-if="isSubmitting" class="fas fa-spinner fa-spin me-1"></i>
                {{ isSubmitting ? "Adding..." : "Add Document" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Schedule Maintenance Modal -->
    <div
      v-if="showScheduleMaintenanceModal"
      class="modal fade show d-block"
      style="background-color: rgba(0, 0, 0, 0.5)"
      @click="showScheduleMaintenanceModal = false"
    >
      <div class="modal-dialog modal-xl" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Schedule Maintenance</h5>
            <button
              type="button"
              class="btn-close"
              @click="showScheduleMaintenanceModal = false"
            ></button>
          </div>
          <form @submit.prevent="submitMaintenanceSchedule">
            <div class="modal-body">
              <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6 mb-3">
                  <label for="maintenanceType" class="form-label">Maintenance Type *</label>
                  <select
                    v-model="maintenanceForm.maintenance_type_id"
                    class="form-select custom-select"
                    id="maintenanceType"
                    required
                  >
                    <option value="">Select Maintenance Type</option>
                    <option
                      v-for="type in maintenanceTypes"
                      :key="type.id"
                      :value="type.id"
                    >
                      {{ type.name }} ({{ type.code }})
                    </option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="scheduleName" class="form-label">Schedule Name *</label>
                  <input
                    v-model="maintenanceForm.schedule_name"
                    type="text"
                    class="form-control"
                    id="scheduleName"
                    placeholder="e.g., A-Check #23"
                    required
                  />
                </div>

                <!-- Due Criteria - At least one must be specified -->
                <div class="col-12 mb-3">
                  <h6 class="text-warning mb-2">
                    <i class="fas fa-calendar-alt me-2"></i>Due Criteria (At least one must be specified)
                  </h6>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="dueHours" class="form-label">Due Hours</label>
                  <input
                    v-model="maintenanceForm.due_hours"
                    type="number"
                    class="form-control"
                    id="dueHours"
                    min="0"
                    step="0.1"
                    placeholder="1500.0"
                  />
                </div>
                <div class="col-md-4 mb-3">
                  <label for="dueCycles" class="form-label">Due Cycles</label>
                  <input
                    v-model="maintenanceForm.due_cycles"
                    type="number"
                    class="form-control"
                    id="dueCycles"
                    min="0"
                    placeholder="750"
                  />
                </div>
                <div class="col-md-4 mb-3">
                  <label for="dueCalendarDate" class="form-label">Due Calendar Date</label>
                  <input
                    v-model="maintenanceForm.due_calendar_date"
                    type="date"
                    class="form-control"
                    id="dueCalendarDate"
                  />
                </div>

                <!-- Current Aircraft Status -->
                <div class="col-md-6 mb-3">
                  <label for="currentHours" class="form-label">Current Aircraft Hours</label>
                  <input
                    v-model="maintenanceForm.current_hours"
                    type="number"
                    class="form-control"
                    id="currentHours"
                    min="0"
                    step="0.1"
                    :placeholder="aircraft.total_airframe_hours || '1450.5'"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="currentCycles" class="form-label">Current Aircraft Cycles</label>
                  <input
                    v-model="maintenanceForm.current_cycles"
                    type="number"
                    class="form-control"
                    id="currentCycles"
                    min="0"
                    :placeholder="aircraft.total_cycles || '725'"
                  />
                </div>

                <!-- Priority and Settings -->
                <div class="col-md-6 mb-3">
                  <label for="maintenancePriority" class="form-label">Priority *</label>
                  <select
                    v-model="maintenanceForm.priority"
                    class="form-select custom-select"
                    id="maintenancePriority"
                    required
                  >
                    <option value="">Select Priority</option>
                    <option
                      v-for="priority in priorities"
                      :key="priority.id"
                      :value="priority.code.toLowerCase()"
                    >
                      {{ priority.name }} ({{ priority.code }})
                    </option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-check mt-4">
                    <input
                      v-model="maintenanceForm.regulatory_required"
                      class="form-check-input"
                      type="checkbox"
                      id="regulatoryRequired"
                    />
                    <label class="form-check-label" for="regulatoryRequired">
                      Regulatory Required
                    </label>
                  </div>
                </div>

                <!-- Description -->
                <div class="col-12 mb-3">
                  <label for="maintenanceDescription" class="form-label">Description</label>
                  <textarea
                    v-model="maintenanceForm.description"
                    class="form-control"
                    id="maintenanceDescription"
                    rows="3"
                    placeholder="Brief description of maintenance work"
                  ></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="showScheduleMaintenanceModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="isSubmitting"
              >
                <i v-if="isSubmitting" class="fas fa-spinner fa-spin me-1"></i>
                {{ isSubmitting ? "Scheduling..." : "Schedule Maintenance" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Create Work Order Modal -->
    <div
      v-if="showCreateWorkOrderModal"
      class="modal fade show d-block"
      style="background-color: rgba(0, 0, 0, 0.5)"
      @click="showCreateWorkOrderModal = false"
    >
      <div class="modal-dialog modal-xl" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Work Order</h5>
            <button
              type="button"
              class="btn-close"
              @click="showCreateWorkOrderModal = false"
            ></button>
          </div>
          <form @submit.prevent="submitWorkOrder">
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
              <div class="row">
                <!-- Basic Work Order Information -->
                <div class="col-12 mb-4">
                  <h6 class="section-title text-primary border-bottom pb-2 mb-3">
                    <i class="fas fa-clipboard me-2"></i>Work Order Details
                  </h6>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="workOrderNumber" class="form-label">Work Order Number</label>
                      <input
                        v-model="workOrderForm.work_order_number"
                        type="text"
                        class="form-control"
                        id="workOrderNumber"
                        placeholder="Auto-generated (e.g., WO-202412-0001)"
                        readonly
                      />
                      <small class="text-muted">Auto-generated when work order is created</small>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="workOrderType" class="form-label">Work Order Type</label>
                      <select
                        v-model="workOrderForm.type"
                        class="form-select custom-select"
                        id="workOrderType"
                      >
                        <option value="corrective">Corrective</option>
                        <option value="preventive">Preventive</option>
                        <option value="modification">Modification</option>
                        <option value="inspection">Inspection</option>
                        <option value="aog">AOG</option>
                      </select>
                    </div>
                    <div class="col-12 mb-3">
                      <label for="workTitle" class="form-label">Work Title *</label>
                      <input
                        v-model="workOrderForm.title"
                        type="text"
                        class="form-control"
                        id="workTitle"
                        placeholder="Brief title for the work order"
                        required
                      />
                    </div>
                    <div class="col-12 mb-3">
                      <label for="workDescription" class="form-label">Description *</label>
                      <textarea
                        v-model="workOrderForm.description"
                        class="form-control"
                        id="workDescription"
                        rows="3"
                        placeholder="Detailed description of work to be performed"
                        required
                      ></textarea>
                    </div>
                  </div>
                </div>

                <!-- Priority and Status -->
                <div class="col-12 mb-4">
                  <h6 class="section-title text-warning border-bottom pb-2 mb-3">
                    <i class="fas fa-flag me-2"></i>Priority and Assignment
                  </h6>
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label for="priority" class="form-label">Priority *</label>
                      <select
                        v-model="workOrderForm.priority_id"
                        class="form-select custom-select"
                        id="priority"
                        required
                      >
                        <option value="">Select Priority</option>
                        <option
                          v-for="priority in priorities"
                          :key="priority.id"
                          :value="priority.id"
                        >
                          {{ priority.name }} ({{ priority.code }})
                        </option>
                      </select>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="workOrderStatus" class="form-label">Initial Status</label>
                      <select
                        v-model="workOrderForm.status_id"
                        class="form-select custom-select"
                        id="workOrderStatus"
                      >
                        <option value="">Select Status</option>
                        <option
                          v-for="status in workOrderStatuses"
                          :key="status.id"
                          :value="status.id"
                        >
                          {{ status.name }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="maintenanceTypeWO" class="form-label">Related Maintenance Type</label>
                      <select
                        v-model="workOrderForm.maintenance_type_id"
                        class="form-select custom-select"
                        id="maintenanceTypeWO"
                      >
                        <option value="">Select Type (Optional)</option>
                        <option
                          v-for="type in maintenanceTypes"
                          :key="type.id"
                          :value="type.id"
                        >
                          {{ type.name }} ({{ type.code }})
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Scheduling and Resources -->
                <div class="col-12 mb-4">
                  <h6 class="section-title text-info border-bottom pb-2 mb-3">
                    <i class="fas fa-calendar-alt me-2"></i>Scheduling and Resources
                  </h6>
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label for="scheduledStartDate" class="form-label">Scheduled Start Date</label>
                      <input
                        v-model="workOrderForm.scheduled_start_date"
                        type="datetime-local"
                        class="form-control"
                        id="scheduledStartDate"
                      />
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="workDueDate" class="form-label">Due Date</label>
                      <input
                        v-model="workOrderForm.due_date"
                        type="datetime-local"
                        class="form-control"
                        id="workDueDate"
                      />
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="estimatedHours" class="form-label">Estimated Hours</label>
                      <input
                        v-model="workOrderForm.estimated_hours"
                        type="number"
                        class="form-control"
                        id="estimatedHours"
                        min="0"
                        step="0.1"
                        placeholder="8.0"
                      />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="assignedUser" class="form-label">Assigned To</label>
                      <select
                        v-model="workOrderForm.assigned_user_id"
                        class="form-select custom-select"
                        id="assignedUser"
                      >
                        <option value="">Select Technician</option>
                        <option
                          v-for="user in users"
                          :key="user.id"
                          :value="user.id"
                        >
                          {{ user.name }} - {{ user.email }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="workOrderOrg" class="form-label">Maintenance Organization</label>
                      <select
                        v-model="workOrderForm.maintenance_organization_id"
                        class="form-select custom-select"
                        id="workOrderOrg"
                      >
                        <option value="">Select Organization</option>
                        <option
                          v-for="org in maintenanceOrganizations"
                          :key="org.id"
                          :value="org.id"
                        >
                          {{ org.name }} ({{ org.code }})
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Regulatory and Quality -->
                <div class="col-12 mb-4">
                  <h6 class="section-title text-success border-bottom pb-2 mb-3">
                    <i class="fas fa-check-circle me-2"></i>Regulatory and Quality
                  </h6>
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <div class="form-check mt-3">
                        <input
                          v-model="workOrderForm.regulatory_required"
                          class="form-check-input"
                          type="checkbox"
                          id="workOrderRegRequired"
                        />
                        <label class="form-check-label" for="workOrderRegRequired">
                          Regulatory Required
                        </label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-3">
                      <div class="form-check mt-3">
                        <input
                          v-model="workOrderForm.requires_inspection"
                          class="form-check-input"
                          type="checkbox"
                          id="requiresInspection"
                        />
                        <label class="form-check-label" for="requiresInspection">
                          Requires QC Inspection
                        </label>
                      </div>
                    </div>
                    <div class="col-md-4 mb-3">
                      <div class="form-check mt-3">
                        <input
                          v-model="workOrderForm.requires_certification"
                          class="form-check-input"
                          type="checkbox"
                          id="requiresCertification"
                        />
                        <label class="form-check-label" for="requiresCertification">
                          Requires Certification
                        </label>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cost Estimation -->
                <div class="col-12 mb-4">
                  <h6 class="section-title text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-dollar-sign me-2"></i>Cost Estimation
                  </h6>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="estimatedLaborCost" class="form-label">Estimated Labor Cost (USD)</label>
                      <input
                        v-model="workOrderForm.estimated_labor_cost"
                        type="number"
                        class="form-control"
                        id="estimatedLaborCost"
                        min="0"
                        step="0.01"
                        placeholder="500.00"
                      />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="estimatedPartsCost" class="form-label">Estimated Parts Cost (USD)</label>
                      <input
                        v-model="workOrderForm.estimated_parts_cost"
                        type="number"
                        class="form-control"
                        id="estimatedPartsCost"
                        min="0"
                        step="0.01"
                        placeholder="1500.00"
                      />
                    </div>
                  </div>
                </div>

                <!-- Additional Information -->
                <div class="col-12 mb-3">
                  <label for="workNotes" class="form-label">Additional Notes</label>
                  <textarea
                    v-model="workOrderForm.notes"
                    class="form-control"
                    id="workNotes"
                    rows="3"
                    placeholder="Any additional information, special instructions, or requirements..."
                  ></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="showCreateWorkOrderModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn btn-success"
                :disabled="isSubmitting"
              >
                <i v-if="isSubmitting" class="fas fa-spinner fa-spin me-1"></i>
                {{ isSubmitting ? "Creating..." : "Create Work Order" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Work Order Status Update Modal -->
    <div
      v-if="showWorkOrderStatusModal"
      class="modal fade show d-block"
      style="background-color: rgba(0, 0, 0, 0.5)"
      @click="showWorkOrderStatusModal = false"
    >
      <div class="modal-dialog" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Update Work Order Status</h5>
            <button
              type="button"
              class="btn-close"
              @click="showWorkOrderStatusModal = false"
            ></button>
          </div>
          <form @submit.prevent="submitStatusUpdate">
            <div class="modal-body">
              <div v-if="selectedWorkOrder" class="mb-3">
                <div class="alert alert-info">
                  <strong>Work Order:</strong> {{ selectedWorkOrder.work_order_number }}<br>
                  <strong>Current Status:</strong> {{ selectedWorkOrder.status?.name || 'N/A' }}<br>
                  <strong>Description:</strong> {{ selectedWorkOrder.description }}
                </div>
              </div>
              <div class="mb-3">
                <label for="newStatus" class="form-label">New Status *</label>
                <select
                  v-model="statusUpdateForm.status_id"
                  class="form-select custom-select"
                  id="newStatus"
                  required
                >
                  <option value="">Select New Status</option>
                  <option
                    v-for="status in workOrderStatuses"
                    :key="status.id"
                    :value="status.id"
                  >
                    {{ status.name }} - {{ status.description }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label for="statusNotes" class="form-label">Status Update Notes</label>
                <textarea
                  v-model="statusUpdateForm.notes"
                  class="form-control"
                  id="statusNotes"
                  rows="3"
                  placeholder="Reason for status change, additional notes..."
                ></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                @click="showWorkOrderStatusModal = false"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="btn btn-primary"
                :disabled="isSubmitting"
              >
                <i v-if="isSubmitting" class="fas fa-spinner fa-spin me-1"></i>
                {{ isSubmitting ? "Updating..." : "Update Status" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Maintenance Details Modal -->
    <div
      v-if="showMaintenanceDetailsModal"
      class="modal fade show d-block"
      style="background-color: rgba(0, 0, 0, 0.5)"
      @click="showMaintenanceDetailsModal = false"
    >
      <div class="modal-dialog modal-xl" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Maintenance Schedule Details</h5>
            <button
              type="button"
              class="btn-close"
              @click="showMaintenanceDetailsModal = false"
            ></button>
          </div>
          <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            <div v-if="selectedMaintenance" class="row">
              <!-- Basic Information -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-primary border-bottom pb-2 mb-3">
                  <i class="fas fa-info-circle me-2"></i>Basic Information
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Schedule Name</label>
                      <div class="info-value">{{ selectedMaintenance.schedule_name || 'N/A' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Maintenance Type</label>
                      <div class="info-value">{{ selectedMaintenance.maintenanceType?.name || 'N/A' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Priority</label>
                      <div class="info-value">
                        <span
                          class="badge"
                          :class="{
                            'bg-danger': selectedMaintenance.priority === 'critical',
                            'bg-warning': selectedMaintenance.priority === 'high',
                            'bg-info': selectedMaintenance.priority === 'normal',
                            'bg-secondary': selectedMaintenance.priority === 'low'
                          }"
                        >
                          {{ selectedMaintenance.priority?.toUpperCase() || 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Compliance Status</label>
                      <div class="info-value">
                        <span
                          class="badge"
                          :class="{
                            'bg-success': selectedMaintenance.compliance_status === 'current',
                            'bg-warning': selectedMaintenance.compliance_status === 'due_soon',
                            'bg-danger': selectedMaintenance.compliance_status === 'overdue'
                          }"
                        >
                          {{ selectedMaintenance.compliance_status?.replace('_', ' ').toUpperCase() || 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="info-item">
                      <label class="info-label">Description</label>
                      <div class="info-value">{{ selectedMaintenance.description || 'No description provided' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Due Criteria -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-warning border-bottom pb-2 mb-3">
                  <i class="fas fa-calendar-alt me-2"></i>Due Criteria
                </h6>
                <div class="row">
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Due Hours</label>
                      <div class="info-value">{{ selectedMaintenance.due_hours || 'Not specified' }}</div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Due Cycles</label>
                      <div class="info-value">{{ selectedMaintenance.due_cycles || 'Not specified' }}</div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Due Calendar Date</label>
                      <div class="info-value">{{ selectedMaintenance.due_calendar_date ? formatDate(selectedMaintenance.due_calendar_date) : 'Not specified' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Current Status -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-info border-bottom pb-2 mb-3">
                  <i class="fas fa-tachometer-alt me-2"></i>Current Aircraft Status
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Current Hours</label>
                      <div class="info-value">{{ selectedMaintenance.current_hours || 'Not tracked' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Current Cycles</label>
                      <div class="info-value">{{ selectedMaintenance.current_cycles || 'Not tracked' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Last Completed -->
              <div class="col-12 mb-4" v-if="selectedMaintenance.last_completed_date">
                <h6 class="section-title text-success border-bottom pb-2 mb-3">
                  <i class="fas fa-check-circle me-2"></i>Last Completed
                </h6>
                <div class="row">
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Last Completed Date</label>
                      <div class="info-value">{{ formatDate(selectedMaintenance.last_completed_date) }}</div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Hours at Completion</label>
                      <div class="info-value">{{ selectedMaintenance.last_completed_hours || 'N/A' }}</div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Cycles at Completion</label>
                      <div class="info-value">{{ selectedMaintenance.last_completed_cycles || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Settings -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-dark border-bottom pb-2 mb-3">
                  <i class="fas fa-cog me-2"></i>Settings
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Regulatory Required</label>
                      <div class="info-value">
                        <span :class="selectedMaintenance.regulatory_required ? 'badge bg-success' : 'badge bg-secondary'">
                          {{ selectedMaintenance.regulatory_required ? 'Yes' : 'No' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Active Schedule</label>
                      <div class="info-value">
                        <span :class="selectedMaintenance.is_active ? 'badge bg-success' : 'badge bg-danger'">
                          {{ selectedMaintenance.is_active ? 'Active' : 'Inactive' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Related Work Orders -->
              <div class="col-12" v-if="selectedMaintenance.workOrders && selectedMaintenance.workOrders.length > 0">
                <h6 class="section-title text-primary border-bottom pb-2 mb-3">
                  <i class="fas fa-clipboard-list me-2"></i>Related Work Orders
                </h6>
                <div class="table-responsive">
                  <table class="table table-sm table-hover">
                    <thead>
                      <tr>
                        <th>Work Order</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="workOrder in selectedMaintenance.workOrders" :key="workOrder.id">
                        <td>{{ workOrder.work_order_number }}</td>
                        <td>
                          <span class="badge bg-info">{{ workOrder.status?.name || 'N/A' }}</span>
                        </td>
                        <td>{{ formatDate(workOrder.created_at) }}</td>
                        <td>
                          <button
                            class="btn btn-sm btn-outline-primary"
                            @click="viewWorkOrderDetails(workOrder)"
                          >
                            <i class="fas fa-eye"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-5">
              <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem"></i>
              <h6 class="text-muted">No maintenance schedule selected</h6>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="showMaintenanceDetailsModal = false"
            >
              Close
            </button>
            <button
              type="button"
              class="btn btn-warning"
              @click="editMaintenance(selectedMaintenance)"
            >
              <i class="fas fa-edit me-1"></i>Edit Schedule
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Work Order Details Modal -->
    <div
      v-if="showWorkOrderDetailsModal"
      class="modal fade show d-block"
      style="background-color: rgba(0, 0, 0, 0.5)"
      @click="showWorkOrderDetailsModal = false"
    >
      <div class="modal-dialog modal-xl" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Work Order Details</h5>
            <button
              type="button"
              class="btn-close"
              @click="showWorkOrderDetailsModal = false"
            ></button>
          </div>
          <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            <div v-if="selectedWorkOrder" class="row">
              <!-- Basic Information -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-primary border-bottom pb-2 mb-3">
                  <i class="fas fa-clipboard me-2"></i>Work Order Information
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Work Order Number</label>
                      <div class="info-value fw-bold">{{ selectedWorkOrder.work_order_number }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Type</label>
                      <div class="info-value">
                        <span class="badge bg-secondary">{{ selectedWorkOrder.type?.toUpperCase() || 'N/A' }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Priority</label>
                      <div class="info-value">
                        <span
                          class="badge"
                          :class="{
                            'bg-danger': selectedWorkOrder.priority?.code === 'AOG' || selectedWorkOrder.priority?.code === 'CRITICAL',
                            'bg-warning': selectedWorkOrder.priority?.code === 'HIGH',
                            'bg-info': selectedWorkOrder.priority?.code === 'NORMAL',
                            'bg-secondary': selectedWorkOrder.priority?.code === 'LOW'
                          }"
                        >
                          {{ selectedWorkOrder.priority?.name || 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Current Status</label>
                      <div class="info-value">
                        <span
                          class="badge"
                          :class="{
                            'bg-success': selectedWorkOrder.status?.name === 'Completed',
                            'bg-warning': selectedWorkOrder.status?.name === 'In Progress',
                            'bg-info': selectedWorkOrder.status?.name === 'Planned',
                            'bg-danger': selectedWorkOrder.status?.name === 'On Hold',
                            'bg-secondary': !selectedWorkOrder.status
                          }"
                        >
                          {{ selectedWorkOrder.status?.name || 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="info-item">
                      <label class="info-label">Title</label>
                      <div class="info-value fw-semibold">{{ selectedWorkOrder.title || selectedWorkOrder.description }}</div>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="info-item">
                      <label class="info-label">Description</label>
                      <div class="info-value">{{ selectedWorkOrder.description || 'No description provided' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Scheduling Information -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-warning border-bottom pb-2 mb-3">
                  <i class="fas fa-calendar-alt me-2"></i>Scheduling
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Scheduled Start</label>
                      <div class="info-value">{{ selectedWorkOrder.scheduled_start_date ? formatDate(selectedWorkOrder.scheduled_start_date) : 'Not scheduled' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Due Date</label>
                      <div class="info-value">{{ selectedWorkOrder.due_date ? formatDate(selectedWorkOrder.due_date) : 'No due date set' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Estimated Hours</label>
                      <div class="info-value">{{ selectedWorkOrder.estimated_hours || 'Not estimated' }} hours</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Assigned To</label>
                      <div class="info-value">{{ selectedWorkOrder.assigned_user?.name || 'Unassigned' }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cost Information -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-success border-bottom pb-2 mb-3">
                  <i class="fas fa-dollar-sign me-2"></i>Cost Information
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Estimated Labor Cost</label>
                      <div class="info-value">${{ selectedWorkOrder.estimated_labor_cost || '0.00' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Estimated Parts Cost</label>
                      <div class="info-value">${{ selectedWorkOrder.estimated_parts_cost || '0.00' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Total Estimated Cost</label>
                      <div class="info-value fw-bold">${{ (parseFloat(selectedWorkOrder.estimated_labor_cost || 0) + parseFloat(selectedWorkOrder.estimated_parts_cost || 0)).toFixed(2) }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quality and Regulatory -->
              <div class="col-12 mb-4">
                <h6 class="section-title text-info border-bottom pb-2 mb-3">
                  <i class="fas fa-check-circle me-2"></i>Quality & Regulatory
                </h6>
                <div class="row">
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Regulatory Required</label>
                      <div class="info-value">
                        <span :class="selectedWorkOrder.regulatory_required ? 'badge bg-success' : 'badge bg-secondary'">
                          {{ selectedWorkOrder.regulatory_required ? 'Yes' : 'No' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">QC Inspection Required</label>
                      <div class="info-value">
                        <span :class="selectedWorkOrder.requires_inspection ? 'badge bg-success' : 'badge bg-secondary'">
                          {{ selectedWorkOrder.requires_inspection ? 'Yes' : 'No' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="info-item">
                      <label class="info-label">Certification Required</label>
                      <div class="info-value">
                        <span :class="selectedWorkOrder.requires_certification ? 'badge bg-success' : 'badge bg-secondary'">
                          {{ selectedWorkOrder.requires_certification ? 'Yes' : 'No' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Additional Notes -->
              <div class="col-12 mb-4" v-if="selectedWorkOrder.notes">
                <h6 class="section-title text-dark border-bottom pb-2 mb-3">
                  <i class="fas fa-sticky-note me-2"></i>Additional Notes
                </h6>
                <div class="bg-light p-3 rounded">
                  {{ selectedWorkOrder.notes }}
                </div>
              </div>

              <!-- System Information -->
              <div class="col-12">
                <h6 class="section-title text-muted border-bottom pb-2 mb-3">
                  <i class="fas fa-info me-2"></i>System Information
                </h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Created By</label>
                      <div class="info-value">{{ selectedWorkOrder.created_by?.name || 'System' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Created Date</label>
                      <div class="info-value">{{ formatDate(selectedWorkOrder.created_at) }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Maintenance Organization</label>
                      <div class="info-value">{{ selectedWorkOrder.maintenance_organization?.name || 'N/A' }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item">
                      <label class="info-label">Related Maintenance Type</label>
                      <div class="info-value">{{ selectedWorkOrder.maintenance_type?.name || 'N/A' }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-5">
              <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem"></i>
              <h6 class="text-muted">No work order selected</h6>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="showWorkOrderDetailsModal = false"
            >
              Close
            </button>
            <button
              type="button"
              class="btn btn-primary"
              @click="openStatusUpdateModal(selectedWorkOrder)"
            >
              <i class="fas fa-edit me-1"></i>Update Status
            </button>
            <button
              type="button"
              class="btn btn-warning"
              @click="editWorkOrder(selectedWorkOrder)"
            >
              <i class="fas fa-edit me-1"></i>Edit Work Order
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" ref="photoModal">
      <!-- Photo modal content -->
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const props = defineProps({
  aircraft: Object,
  complianceStatus: Object,
  documentsByType: Object,
  documentTypes: Object,
  maintenanceSchedules: Array,
  workOrders: Array,
  maintenanceTypes: Array,
  maintenanceOrganizations: Array,
  maintenancePriorities: Array,
  workOrderPriorities: Array,
  priorities: Array,
  workOrderStatuses: Array,
  users: Array,
});

// Refs
const activeTab = ref("overview");
const showAddDocumentModal = ref(false);
const showScheduleMaintenanceModal = ref(false);
const showCreateWorkOrderModal = ref(false);
const showWorkOrderStatusModal = ref(false);
const showMaintenanceDetailsModal = ref(false);
const showWorkOrderDetailsModal = ref(false);
const selectedWorkOrder = ref(null);
const selectedMaintenance = ref(null);
const selectedPhoto = ref(null);
const photoModal = ref(null);
const isSubmitting = ref(false);

// Modal instances
let photoModalInstance = null;

// Document form
const documentForm = ref({
  document_type_id: "",
  document_number: "",
  issue_date: "",
  expiry_date: "",
  issuing_authority: "",
  file_path: "",
  notes: "",
});

// Maintenance form
const maintenanceForm = ref({
  maintenance_type_id: "",
  schedule_name: "",
  description: "",
  due_hours: "",
  due_cycles: "",
  due_calendar_date: "",
  current_hours: "",
  current_cycles: "",
  priority: "",
  regulatory_required: false,
});

// Work Order form
const workOrderForm = ref({
  work_order_number: "",
  title: "",
  description: "",
  type: "corrective",
  priority_id: "",
  status_id: "",
  maintenance_type_id: "",
  scheduled_start_date: "",
  due_date: "",
  estimated_hours: "",
  assigned_user_id: "",
  maintenance_organization_id: "",
  regulatory_required: false,
  requires_inspection: false,
  requires_certification: false,
  estimated_labor_cost: "",
  estimated_parts_cost: "",
  notes: "",
});

// Work Order Status Update form
const statusUpdateForm = ref({
  status_id: "",
  notes: "",
});

// Methods
const formatHours = (hours) => {
  return hours ? parseFloat(hours).toLocaleString() : "0";
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const getDocumentStatusClass = (document) => {
  switch (document.compliance_status) {
    case "Valid":
      return "status-valid";
    case "Expiring Soon":
      return "status-warning";
    case "Expired":
      return "status-expired";
    default:
      return "status-pending";
  }
};

const getDocumentStatusTitle = (document) => {
  switch (document.compliance_status) {
    case "Valid":
      return "Document is valid";
    case "Expiring Soon":
      return "Document expires soon";
    case "Expired":
      return "Document has expired";
    default:
      return "Document pending review";
  }
};

const getExpiryDateClass = (document) => {
  if (!document.expiry_date) return "";
  if (document.is_expired) return "text-danger";
  if (document.is_expiring_soon) return "text-warning";
  return "text-muted";
};

const showPhotoModal = (photo) => {
  selectedPhoto.value = photo;
  if (photoModalInstance) {
    photoModalInstance.show();
  }
};

const handleFileUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("document", file);

  try {
    const response = await fetch(route("aircraft.upload-document"), {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content"),
      },
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      documentForm.value.file_path = result.path;
    } else {
      alert("File upload failed");
    }
  } catch (error) {
    console.error("Upload error:", error);
    alert("File upload failed");
  }
};

const closeDocumentModal = () => {
  showAddDocumentModal.value = false;
  resetDocumentForm();
};

const submitDocument = () => {
  isSubmitting.value = true;

  router.post(
    route("aircraft.documents.store", props.aircraft.id),
    documentForm.value,
    {
      onSuccess: () => {
        closeDocumentModal();
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    }
  );
};

const resetDocumentForm = () => {
  documentForm.value = {
    document_type_id: "",
    document_number: "",
    issue_date: "",
    expiry_date: "",
    issuing_authority: "",
    file_path: "",
    notes: "",
  };
};

const editDocument = (document) => {
  // Implement edit functionality
  console.log("Edit document:", document);
};

const deleteDocument = (document) => {
  if (confirm("Are you sure you want to delete this document?")) {
    router.delete(
      route("aircraft.documents.destroy", [props.aircraft.id, document.id])
    );
  }
};

// Maintenance Schedule Functions
const submitMaintenanceSchedule = () => {
  isSubmitting.value = true;

  router.post(
    route("aircraft.maintenance.store", props.aircraft.id),
    maintenanceForm.value,
    {
      onSuccess: () => {
        closeMaintenanceModal();
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    }
  );
};

const closeMaintenanceModal = () => {
  showScheduleMaintenanceModal.value = false;
  resetMaintenanceForm();
};

const resetMaintenanceForm = () => {
  maintenanceForm.value = {
    maintenance_type_id: "",
    schedule_name: "",
    description: "",
    due_hours: "",
    due_cycles: "",
    due_calendar_date: "",
    current_hours: "",
    current_cycles: "",
    priority: "",
    regulatory_required: false,
  };
};

// Work Order Functions
const submitWorkOrder = () => {
  isSubmitting.value = true;

  router.post(
    route("aircraft.work-orders.store", props.aircraft.id),
    workOrderForm.value,
    {
      onSuccess: () => {
        closeWorkOrderModal();
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    }
  );
};

const closeWorkOrderModal = () => {
  showCreateWorkOrderModal.value = false;
  resetWorkOrderForm();
};

const resetWorkOrderForm = () => {
  workOrderForm.value = {
    work_order_number: "", // Will be auto-generated
    title: "",
    description: "",
    type: "corrective",
    priority_id: "",
    status_id: "",
    maintenance_type_id: "",
    scheduled_start_date: "",
    due_date: "",
    estimated_hours: "",
    assigned_user_id: "",
    maintenance_organization_id: "",
    regulatory_required: false,
    requires_inspection: false,
    requires_certification: false,
    estimated_labor_cost: "",
    estimated_parts_cost: "",
    notes: "",
  };
};

// Work Order Status Functions
const openStatusUpdateModal = (workOrder) => {
  selectedWorkOrder.value = workOrder;
  statusUpdateForm.value.status_id = "";
  statusUpdateForm.value.notes = "";
  showWorkOrderStatusModal.value = true;
};

const submitStatusUpdate = () => {
  if (!selectedWorkOrder.value) return;

  isSubmitting.value = true;

  router.post(
    route("aircraft.work-orders.update-status", [props.aircraft.id, selectedWorkOrder.value.id]),
    statusUpdateForm.value,
    {
      onSuccess: () => {
        closeStatusUpdateModal();
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    }
  );
};

const closeStatusUpdateModal = () => {
  showWorkOrderStatusModal.value = false;
  selectedWorkOrder.value = null;
  statusUpdateForm.value.status_id = "";
  statusUpdateForm.value.notes = "";
};

// View Functions
const viewMaintenanceDetails = (maintenance = null) => {
  selectedMaintenance.value = maintenance;
  showMaintenanceDetailsModal.value = true;
};

const viewWorkOrderDetails = (workOrder) => {
  selectedWorkOrder.value = workOrder;
  showWorkOrderDetailsModal.value = true;
};

// Edit Functions (placeholder for now)
const editMaintenance = (maintenance) => {
  // Close the details modal
  showMaintenanceDetailsModal.value = false;

  // For now, just show an alert. This can be expanded to show an edit modal
  alert('Edit maintenance schedule - Coming soon!\nThis will open an edit modal for the maintenance schedule.');
  console.log('Edit maintenance:', maintenance);
};

const editWorkOrder = (workOrder) => {
  // Close the details modal
  showWorkOrderDetailsModal.value = false;

  // For now, just show an alert. This can be expanded to show an edit modal
  alert('Edit work order - Coming soon!\nThis will open an edit modal for the work order.');
  console.log('Edit work order:', workOrder);
};

// Initialize modals
onMounted(() => {
  // Initialize photo modal only if bootstrap is available
  if (photoModal.value && window.bootstrap && window.bootstrap.Modal) {
    photoModalInstance = new window.bootstrap.Modal(photoModal.value);
  }
});
</script>

<style scoped>
/* Header Styling */
.card-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-bottom: 2px solid #007bff;
  padding: 1rem;
}

/* Information Display */
.info-item {
  margin-bottom: 0.75rem;
  padding: 0.25rem 0;
  border-bottom: 1px solid #f1f3f4;
}
.info-item:last-child {
  border-bottom: none;
}
.info-label {
  font-size: 0.875rem;
  color: #6c757d;
  font-weight: 600;
  display: block;
  margin-bottom: 0.25rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.info-value {
  font-weight: 600;
  color: #2c3e50;
  font-size: 1rem;
}

/* Tab Navigation - Closer Spacing */
.nav-pills {
  gap: 0.25rem;
  padding: 0.5rem;
  background: #f8f9fa;
  border-radius: 8px;
  margin-bottom: 0;
}
.nav-pills .nav-item {
  margin: 0;
}
.nav-pills .nav-link {
  border-radius: 6px;
  font-weight: 600;
  color: #6c757d;
  background: white;
  border: 2px solid transparent;
  padding: 0.5rem 1rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.nav-pills .nav-link.active {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: white;
  border-color: #007bff;
  box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}
.nav-pills .nav-link:hover:not(.active) {
  background: linear-gradient(135deg, rgba(0,123,255,0.1) 0%, rgba(0,123,255,0.05) 100%);
  color: #007bff;
  border-color: rgba(0,123,255,0.3);
  transform: translateY(-1px);
}

/* Content Area */
.tab-content {
  min-height: 400px;
  padding: 1.5rem;
  background: white;
  border-radius: 0 0 12px 12px;
}

.document-card {
  background: #f8f9fa;
  transition: all 0.2s ease;
}

.document-card:hover {
  background: #e9ecef;
  transform: translateY(-1px);
}

.compliance-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}

.status-valid {
  background: #28a745;
}

.status-warning {
  background: #ffc107;
}

.status-expired {
  background: #dc3545;
}

.status-pending {
  background: #007bff;
}

.progress-circle {
  position: relative;
  width: 120px;
  height: 120px;
  margin: 0 auto;
  border-radius: 50%;
  background: conic-gradient(
    #28a745 calc(var(--progress) * 1%),
    #e9ecef calc(var(--progress) * 1%)
  );
  display: flex;
  align-items: center;
  justify-content: center;
}

.progress-circle::before {
  content: "";
  position: absolute;
  width: 90px;
  height: 90px;
  background: white;
  border-radius: 50%;
}

.progress-text {
  position: relative;
  z-index: 2;
  text-align: center;
}

.percentage {
  font-size: 1.5rem;
  font-weight: 700;
  color: #28a745;
}

.stat-number {
  font-size: 1.2rem;
  font-weight: 600;
}

.photo-thumbnail img {
  max-height: 150px;
  object-fit: cover;
  transition: transform 0.2s ease;
}

.photo-thumbnail img:hover {
  transform: scale(1.05);
}

.section-title {
  color: #495057;
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 0.5rem;
}

.breadcrumb-item a {
  color: #007bff;
}

.breadcrumb-item.active {
  color: #6c757d;
}

.cursor-pointer {
  cursor: pointer;
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

/* Stat Cards */
.stat-card {
  border-radius: 15px;
  background: linear-gradient(
    135deg,
    var(--gradient-from) 0%,
    var(--gradient-to) 100%
  );
  position: relative;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  transform: translate(25%, -25%);
}

.stat-icon {
  background: rgba(255, 255, 255, 0.2);
  color: #ffffffbf !important;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}

.stat-card.stat-primary {
  --gradient-from: #4e73df;
  --gradient-to: #224abe;
}

.stat-card.stat-success {
  --gradient-from: #1cc88a;
  --gradient-to: #13855c;
}

.stat-card.stat-info {
  --gradient-from: #36b9cc;
  --gradient-to: #258391;
}

.stat-card.stat-warning {
  --gradient-from: #f6c23e;
  --gradient-to: #dda20a;
}

.stat-card.stat-danger {
  --gradient-from: #e74a3b;
  --gradient-to: #be2617;
}

.stat-value {
  font-size: 1.5rem;
  line-height: 1.2;
  color: #ffffffbf;
  font-weight: 700;
}

.stat-label {
  color: #ffffffbf;
  font-size: 0.875rem;
  font-weight: 600;
}

/* Global Select Styling - Exact Match to Staff Show Page */
.custom-select {
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
}
.custom-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Breadcrumb styling */
.breadcrumb-sm .breadcrumb-item {
  font-size: 0.75rem;
}

.breadcrumb-sm .breadcrumb-item + .breadcrumb-item::before {
  font-size: 0.65rem;
}

/* Work Order Icons */
.work-order-icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.maintenance-type-icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>