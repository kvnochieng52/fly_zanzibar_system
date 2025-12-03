<template>
  <Head :title="`${staff.full_name} - Staff Profile`" />

  <DashboardLayout>
    <!-- Page Header with Profile and Stats in One Row -->
    <div class="row align-items-center mb-4">
      <!-- Left Side: Profile Photo, Name, and Details -->
      <div class="col-lg-4">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0" style="width: 60px; height: 60px; margin-right: 12px;">
            <img v-if="staff.profile_photo"
                 :src="`/storage/${staff.profile_photo}`"
                 :alt="staff.full_name"
                 class="w-100 h-100 object-cover" />
            <div v-else class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
              <span class="text-white fw-bold">{{ staff.first_name[0] }}{{ staff.last_name[0] }}</span>
            </div>
          </div>
          <div class="flex-grow-1">
            <h1 class="h5 mb-1 text-gray-900">{{ staff.full_name }}</h1>
            <div class="d-flex flex-column gap-1">
              <span class="text-muted small">{{ staff.position?.title || 'N/A' }}</span>
              <span
                class="badge rounded-pill align-self-start small"
                :style="{ backgroundColor: staff.status?.color + '20' || '#6c757d20', color: staff.status?.color || '#6c757d', border: '1px solid ' + (staff.status?.color || '#6c757d') }"
              >
                {{ staff.status?.name || 'N/A' }}
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
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="stat-label text-white-75 small">Years of Service</div>
                  <div class="stat-value text-white fw-bold">{{ staff.years_of_service || 0 }} years</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Age box commented out -->
          <!--
          <div class="col-4">
            <div class="card border-0 shadow-sm h-100 stat-card stat-success">
              <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon me-3">
                  <i class="fas fa-user"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="stat-label text-white-75 small">Age</div>
                  <div class="stat-value text-white fw-bold">{{ staff.age || 0 }} years</div>
                </div>
              </div>
            </div>
          </div>
          -->

          <div class="col-4">
            <div class="card border-0 shadow-sm h-100 stat-card stat-info">
              <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon me-3">
                  <i class="fas fa-building"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="stat-label text-white-75 small">Department</div>
                  <div class="stat-value text-white fw-bold small">{{ staff.department?.name || 'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-4">
            <div class="card border-0 shadow-sm h-100 stat-card stat-warning">
              <div class="card-body d-flex align-items-center p-3">
                <div class="stat-icon me-3">
                  <i class="fas fa-id-badge"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="stat-label text-white-75 small">Staff Number</div>
                  <div class="stat-value text-white fw-bold small">{{ staff.staff_number || 'N/A' }}</div>
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
          <ul class="nav nav-pills" id="staffTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'basic' }]"
              id="basic-tab"
              type="button"
              role="tab"
              @click="activeTab = 'basic'"
            >
              <i class="fas fa-user me-2"></i>Basic Details
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'certification' }]"
              id="certification-tab"
              type="button"
              role="tab"
              @click="activeTab = 'certification'"
            >
              <i class="fas fa-certificate me-2"></i>Professional Certifications
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'medical' }]"
              id="medical-tab"
              type="button"
              role="tab"
              @click="activeTab = 'medical'"
            >
              <i class="fas fa-user-md me-2"></i>Medical
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              :class="['nav-link', { active: activeTab === 'files' }]"
              id="files-tab"
              type="button"
              role="tab"
              @click="activeTab = 'files'"
            >
              <i class="fas fa-file me-2"></i>Files
            </button>
          </li>
        </ul>

          <!-- Action Buttons on the Right -->
          <div class="d-flex gap-2">
            <a :href="`/staff/${staff.id}/edit`" class="btn btn-warning btn-sm">
              <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="/staff" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i>Back
            </a>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="tab-content" id="staffTabsContent">
          <!-- Basic Details Tab -->
          <div v-show="activeTab === 'basic'" id="basic" role="tabpanel">
            <div class="row">
              <!-- Left Column -->
              <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="mb-4">
                  <h5 class="mb-3">Personal Information</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Full Name</label>
                            <div class="info-value">{{ staff.full_name }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Date of Birth</label>
                            <div class="info-value">{{ formatDate(staff.date_of_birth) }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Gender</label>
                            <div class="info-value">{{ staff.gender?.name || 'N/A' }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Nationality</label>
                            <div class="info-value">{{ staff.country?.name || 'N/A' }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Employment Details -->
                <div class="mb-4">
                  <h5 class="mb-3">Employment Details</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Department</label>
                            <div class="info-value">
                              {{ staff.department?.name || 'N/A' }}
                              <span v-if="staff.department?.code" class="badge bg-secondary ms-2">{{ staff.department.code }}</span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Position</label>
                            <div class="info-value">
                              {{ staff.position?.title || 'N/A' }}
                              <span v-if="staff.position?.type" class="badge bg-info ms-2">{{ staff.position.type }}</span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Employment Type</label>
                            <div class="info-value">{{ staff.employment_type?.name || 'N/A' }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Work Location</label>
                            <div class="info-value">{{ staff.work_location?.name || 'N/A' }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Hire Date</label>
                            <div class="info-value">{{ formatDate(staff.hire_date) }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Current Status</label>
                            <div class="info-value">
                              <span
                                class="badge rounded-pill"
                                :style="{ backgroundColor: staff.status?.color + '20' || '#6c757d20', color: staff.status?.color || '#6c757d', border: '1px solid ' + (staff.status?.color || '#6c757d') }"
                              >
                                {{ staff.status?.name || 'N/A' }}
                              </span>
                            </div>
                          </div>
                        </div>
                        <div v-if="staff.current_base" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Current Base</label>
                            <div class="info-value">{{ staff.current_base }}</div>
                          </div>
                        </div>
                        <div v-if="staff.supervisor_name" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Supervisor</label>
                            <div class="info-value">{{ staff.supervisor_name }}</div>
                          </div>
                        </div>
                        <div v-if="staff.contract_start_date" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Contract Start</label>
                            <div class="info-value">{{ formatDate(staff.contract_start_date) }}</div>
                          </div>
                        </div>
                        <div v-if="staff.contract_end_date" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Contract End</label>
                            <div class="info-value">{{ formatDate(staff.contract_end_date) }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Education Details -->
                <div class="mb-4">
                  <h5 class="mb-3">Education Details</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Highest Qualification</label>
                            <div class="info-value">{{ staff.highest_qualification?.name || staff.qualification_name || 'N/A' }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Institution</label>
                            <div class="info-value">{{ staff.institution_name || 'N/A' }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Graduation Year</label>
                            <div class="info-value">{{ staff.graduation_year || 'N/A' }}</div>
                          </div>
                        </div>
                        <div v-if="staff.identification_type || staff.identification_type?.name" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Identification Type</label>
                            <div class="info-value">{{ staff.identification_type?.name || staff.identification_type }}</div>
                          </div>
                        </div>
                        <div v-if="staff.identification_number" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Identification Number</label>
                            <div class="info-value">{{ staff.identification_number }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-4">
                  <h5 class="mb-3">Contact Information</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <div class="row g-3">
                        <div v-if="staff.email" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Email Address</label>
                            <div class="info-value">
                              <a :href="'mailto:' + staff.email" class="text-primary text-decoration-none">
                                <i class="fas fa-envelope me-2"></i>{{ staff.email }}
                              </a>
                            </div>
                          </div>
                        </div>
                        <div v-if="staff.phone_primary" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Primary Phone</label>
                            <div class="info-value">
                              <a :href="'tel:' + staff.phone_primary" class="text-primary text-decoration-none">
                                <i class="fas fa-phone me-2"></i>{{ staff.phone_primary }}
                              </a>
                            </div>
                          </div>
                        </div>
                        <div v-if="staff.phone_secondary" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Secondary Phone</label>
                            <div class="info-value">
                              <a :href="'tel:' + staff.phone_secondary" class="text-primary text-decoration-none">
                                <i class="fas fa-phone me-2"></i>{{ staff.phone_secondary }}
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Address Section -->
                      <div v-if="hasAddress" class="mt-4">
                        <h6 class="mb-3">Address</h6>
                        <div class="bg-white p-3 rounded border">
                          <div v-if="staff.address_line_1">{{ staff.address_line_1 }}</div>
                          <div v-if="staff.address_line_2">{{ staff.address_line_2 }}</div>
                          <div>
                            <span v-if="staff.city">{{ staff.city }}</span><span v-if="staff.city && staff.state_region">, </span><span v-if="staff.state_region">{{ staff.state_region }}</span>
                            <span v-if="staff.postal_code"> {{ staff.postal_code }}</span>
                          </div>
                          <div v-if="staff.country || staff.country?.name" class="fw-medium">{{ typeof staff.country === 'string' ? staff.country : staff.country?.name }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Next of Kin Information -->
                <div v-if="staff.next_of_kin_name" class="mb-4">
                  <h5 class="mb-3">Next of Kin</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Name</label>
                            <div class="info-value">{{ staff.next_of_kin_name }}</div>
                          </div>
                        </div>
                        <div v-if="staff.next_of_kin_relationship" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Relationship</label>
                            <div class="info-value">{{ staff.next_of_kin_relationship }}</div>
                          </div>
                        </div>
                        <div v-if="staff.next_of_kin_phone" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Phone</label>
                            <div class="info-value">
                              <a :href="'tel:' + staff.next_of_kin_phone" class="text-primary text-decoration-none">
                                <i class="fas fa-phone me-2"></i>{{ staff.next_of_kin_phone }}
                              </a>
                            </div>
                          </div>
                        </div>
                        <div v-if="staff.next_of_kin_email" class="col-md-6">
                          <div class="info-item">
                            <label class="info-label">Email</label>
                            <div class="info-value">
                              <a :href="'mailto:' + staff.next_of_kin_email" class="text-primary text-decoration-none">
                                <i class="fas fa-envelope me-2"></i>{{ staff.next_of_kin_email }}
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Additional Notes -->
                <div v-if="staff.notes" class="mb-4">
                  <h5 class="mb-3">Additional Notes</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <p class="mb-0 text-muted">{{ staff.notes }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column - Documents -->
              <div class="col-lg-4">
                <div class="mb-4">
                  <h5 class="mb-3">Documents</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <div v-if="staff.identification_file" class="document-item mb-3">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-id-card text-info me-3 fa-lg"></i>
                          <div>
                            <div class="fw-medium">Identification Document</div>
                            <small class="text-muted">{{ staff.identification_type?.name || staff.identification_type || 'Document' }}</small>
                            <div class="mt-1">
                              <a :href="`/storage/${staff.identification_file}`"
                                 target="_blank"
                                 class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div v-if="!staff.identification_file" class="text-center text-muted py-3">
                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                        <p class="mb-0">No documents uploaded</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Employment Contract Section -->
                <div class="mb-4">
                  <h5 class="mb-3">Signed Employment Contract</h5>
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <!-- Current Contract -->
                      <div v-if="staff.current_contract" class="mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <h6 class="fw-medium mb-0">Current Contract</h6>
                          <span
                            class="badge"
                            :class="`bg-${staff.current_contract.status_color}`"
                          >
                            {{ staff.current_contract.status }}
                          </span>
                        </div>

                        <div class="contract-item p-3 border rounded mb-3">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <small class="text-muted">Contract Type</small>
                              <div class="fw-medium">{{ staff.current_contract.contract_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</div>
                            </div>
                            <div class="col-md-6">
                              <small class="text-muted">Start Date</small>
                              <div class="fw-medium">{{ formatDate(staff.current_contract.start_date) }}</div>
                            </div>
                            <div v-if="staff.current_contract.end_date" class="col-md-6">
                              <small class="text-muted">End Date</small>
                              <div class="fw-medium">{{ formatDate(staff.current_contract.end_date) }}</div>
                            </div>
                            <div v-else class="col-md-6">
                              <small class="text-muted">Contract Type</small>
                              <div class="fw-medium text-success">Permanent</div>
                            </div>
                            <div v-if="staff.current_contract.days_until_expiry !== null" class="col-md-6">
                              <small class="text-muted">Days Until Expiry</small>
                              <div class="fw-medium" :class="{
                                'text-danger': staff.current_contract.days_until_expiry < 30,
                                'text-warning': staff.current_contract.days_until_expiry >= 30 && staff.current_contract.days_until_expiry < 90,
                                'text-success': staff.current_contract.days_until_expiry >= 90
                              }">
                                {{ staff.current_contract.days_until_expiry }} days
                              </div>
                            </div>
                          </div>

                          <div v-if="staff.current_contract.contract_file" class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between">
                              <div>
                                <i class="fas fa-file-contract text-primary me-2"></i>
                                <span class="fw-medium">Contract Document</span>
                              </div>
                              <a :href="`/storage/${staff.current_contract.contract_file}`"
                                 target="_blank"
                                 class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Upload New Contract -->
                      <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <h6 class="fw-medium mb-0">Contract Management</h6>
                          <button class="btn btn-sm btn-primary" @click="showUploadModal = true">
                            <i class="fas fa-plus me-1"></i>Add/Update Contract
                          </button>
                        </div>

                        <!-- Contract History -->
                        <div v-if="staff.employment_contracts && staff.employment_contracts.length > 0">
                          <h6 class="text-muted mb-3">Contract History</h6>
                          <div class="contract-history">
                            <div
                              v-for="contract in staff.employment_contracts"
                              :key="contract.id"
                              class="contract-history-item d-flex justify-content-between align-items-center py-2 px-3 mb-2 bg-white rounded border-start border-3"
                              :class="`border-${contract.status_color}`"
                            >
                              <div>
                                <div class="fw-medium">{{ contract.contract_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</div>
                                <small class="text-muted">
                                  {{ formatDate(contract.start_date) }} -
                                  {{ contract.end_date ? formatDate(contract.end_date) : 'Permanent' }}
                                </small>
                              </div>
                              <div class="text-end">
                                <span class="badge" :class="`bg-${contract.status_color}`">
                                  {{ contract.status }}
                                </span>
                                <div v-if="contract.contract_file" class="mt-1">
                                  <a :href="`/storage/${contract.contract_file}`"
                                     target="_blank"
                                     class="btn btn-xs btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div v-else class="text-center text-muted py-3">
                          <i class="fas fa-file-contract fa-2x mb-2"></i>
                          <p class="mb-0">No contracts uploaded</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Certification Tab -->
          <div v-show="activeTab === 'certification'" id="certification" role="tabpanel">
            <!-- Aviation Licenses Section -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Aviation Licenses</h5>
                <button class="btn btn-primary btn-sm" @click="showLicenseModal = true">
                  <i class="fas fa-plus me-1"></i>Add License
                </button>
              </div>

              <div v-if="staff.licenses && staff.licenses.length > 0" class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>License Type</th>
                      <th>License Number</th>
                      <th>Issuing Authority</th>
                      <th>Issue Date</th>
                      <th>Expiry Date</th>
                      <th>Status</th>
                      <th>Document</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="license in staff.licenses" :key="license.id">
                      <td>
                        <div class="fw-medium">{{ license.license_type?.name || license.license_type }}</div>
                        <small class="text-muted">{{ license.license_type?.code || license.license_type }}</small>
                      </td>
                      <td>
                        <span class="fw-medium">{{ license.license_number }}</span>
                      </td>
                      <td>{{ license.issuing_authority }}</td>
                      <td>
                        <small>{{ formatDate(license.issue_date) }}</small>
                      </td>
                      <td>
                        <small :class="{
                          'text-danger': license.days_until_expiry < 30,
                          'text-warning': license.days_until_expiry >= 30 && license.days_until_expiry < 90
                        }">
                          {{ license.expiry_date ? formatDate(license.expiry_date) : 'No Expiry' }}
                        </small>
                      </td>
                      <td>
                        <span
                          class="badge"
                          :class="`bg-${license.license_status?.color || license.status_color}`"
                        >
                          {{ license.license_status?.name || license.status }}
                        </span>
                      </td>
                      <td>
                        <a v-if="license.document_file"
                           :href="`/storage/${license.document_file}`"
                           target="_blank"
                           class="btn btn-sm btn-outline-secondary">
                          <i class="fas fa-eye"></i>
                        </a>
                        <span v-else class="text-muted">-</span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-outline-primary btn-sm" @click="editLicense(license)" title="Edit">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-outline-danger btn-sm" @click="deleteLicense(license.id)" title="Delete">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div v-else class="text-center py-5 text-muted">
                <i class="fas fa-certificate fa-3x mb-3 opacity-50"></i>
                <p class="mb-0">No aviation licenses recorded</p>
                <small>Click "Add License" to get started</small>
              </div>
            </div>

            <!-- Type Ratings Section -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Type Ratings</h5>
                <button class="btn btn-secondary btn-sm" @click="showTypeRatingModal = true">
                  <i class="fas fa-plus me-1"></i>Add Type Rating
                </button>
              </div>

              <div v-if="staff.type_ratings && staff.type_ratings.length > 0">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>Aircraft Type</th>
                        <th>Rating Type</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="rating in staff.type_ratings" :key="rating.id">
                        <td class="fw-medium">{{ rating.aircraft_type }}</td>
                        <td>{{ rating.rating_type }}</td>
                        <td>{{ formatDate(rating.issue_date) }}</td>
                        <td :class="{
                          'text-danger': rating.days_until_expiry < 30,
                          'text-warning': rating.days_until_expiry >= 30 && rating.days_until_expiry < 90
                        }">
                          {{ rating.expiry_date ? formatDate(rating.expiry_date) : 'No Expiry' }}
                        </td>
                        <td>
                          <span class="badge" :class="`bg-${rating.status_color}`">
                            {{ rating.status }}
                          </span>
                        </td>
                        <td>
                          <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" title="View Details">
                              <i class="fas fa-eye"></i>
                            </button>
                            <a v-if="rating.document_file" :href="`/storage/${rating.document_file}`" target="_blank" class="btn btn-outline-secondary" title="View Document">
                              <i class="fas fa-file-alt"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div v-else class="text-center py-4 text-muted">
                <i class="fas fa-plane fa-2x mb-2"></i>
                <p>No type ratings recorded</p>
              </div>
            </div>

            <!-- Proficiency Section -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Proficiency & Renewals</h5>
                <button class="btn btn-info btn-sm" @click="showProficiencyModal = true">
                  <i class="fas fa-plus me-1"></i>Add Proficiency
                </button>
              </div>

              <div v-if="staff.proficiencies && staff.proficiencies.length > 0">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>Proficiency Type</th>
                        <th>EPL Level</th>
                        <th>Last Completed</th>
                        <th>Next Due Date</th>
                        <th>Training Provider</th>
                        <th>Status</th>
                        <th>Document</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="proficiency in staff.proficiencies" :key="proficiency.id">
                        <td class="fw-medium">{{ proficiency.proficiency_type?.name || getProficiencyDisplayName(proficiency.proficiency_type) }}</td>
                        <td>
                          <span v-if="proficiency.epl_level" class="badge bg-info">
                            Level {{ proficiency.epl_level }}
                          </span>
                          <span v-else class="text-muted">N/A</span>
                        </td>
                        <td>{{ proficiency.last_completion_date ? formatDate(proficiency.last_completion_date) : 'N/A' }}</td>
                        <td :class="{
                          'text-danger': getDaysUntilDue(proficiency.next_due_date) < 0,
                          'text-warning': getDaysUntilDue(proficiency.next_due_date) >= 0 && getDaysUntilDue(proficiency.next_due_date) < 30
                        }">
                          {{ proficiency.next_due_date ? formatDate(proficiency.next_due_date) : 'Not set' }}
                          <div v-if="proficiency.next_due_date" class="small text-muted">
                            {{ getDaysUntilDue(proficiency.next_due_date) < 0
                              ? `${Math.abs(getDaysUntilDue(proficiency.next_due_date))} days overdue`
                              : `${getDaysUntilDue(proficiency.next_due_date)} days remaining` }}
                          </div>
                        </td>
                        <td>{{ proficiency.training_provider || 'Not specified' }}</td>
                        <td>
                          <span class="badge" :class="`bg-${getProficiencyStatusColor(proficiency)}`">
                            {{ proficiency.status }}
                          </span>
                        </td>
                        <td>
                          <a v-if="proficiency.document_file" :href="`/storage/${proficiency.document_file}`" target="_blank" class="btn btn-outline-secondary btn-sm" title="View Document">
                            <i class="fas fa-file-alt"></i>
                          </a>
                          <span v-else class="text-muted">No file</span>
                        </td>
                        <td>
                          <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" title="Edit Proficiency">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" title="Delete Proficiency">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div v-else class="text-center py-4 text-muted">
                <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                <p>No proficiency records found</p>
              </div>
            </div>
          </div>

          <!-- Medical Tab -->
          <div v-show="activeTab === 'medical'" id="medical" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="mb-0">Medical Certificates</h5>
              <button class="btn btn-primary btn-sm" @click="showMedicalModal = true">
                <i class="fas fa-plus me-1"></i>Add Medical Certificate
              </button>
            </div>

            <div v-if="staff.medical_certificates && staff.medical_certificates.length > 0">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Medical Class</th>
                      <th>Certificate Number</th>
                      <th>Issue Date</th>
                      <th>Expiry Date</th>
                      <th>Examining Doctor</th>
                      <th>Examining Facility</th>
                      <th>Status</th>
                      <th>Document</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="medical in staff.medical_certificates" :key="medical.id">
                      <td class="fw-medium">{{ medical.medical_class?.name || medical.medical_class }}</td>
                      <td>{{ medical.certificate_number }}</td>
                      <td>{{ formatDate(medical.issue_date) }}</td>
                      <td :class="{
                        'text-danger': medical.days_until_expiry < 30,
                        'text-warning': medical.days_until_expiry >= 30 && medical.days_until_expiry < 90
                      }">
                        {{ formatDate(medical.expiry_date) }}
                      </td>
                      <td>{{ medical.examining_doctor }}</td>
                      <td>{{ medical.examining_facility }}</td>
                      <td>
                        <span class="badge" :class="`bg-${medical.medical_status?.color || medical.status_color}`">
                          {{ medical.medical_status?.name || medical.medical_status }}
                        </span>
                        <div v-if="medical.limitations" class="mt-1">
                          <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Limitations</small>
                        </div>
                      </td>
                      <td>
                        <a v-if="medical.document_file" :href="`/storage/${medical.document_file}`" target="_blank" class="btn btn-outline-secondary btn-sm" title="View Certificate">
                          <i class="fas fa-file-alt"></i>
                        </a>
                        <span v-else class="text-muted">No file</span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-outline-primary" title="Edit Medical Certificate">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-outline-danger" title="Delete Medical Certificate">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div v-else class="text-center py-5 text-muted">
              <i class="fas fa-user-md fa-3x mb-3"></i>
              <p>No medical certificates recorded</p>
            </div>
          </div>

          <!-- Files Tab -->
          <div v-show="activeTab === 'files'" id="files" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="mb-0">Staff Files</h5>
              <button class="btn btn-primary btn-sm" @click="showFileModal = true">
                <i class="fas fa-upload me-1"></i>Upload File
              </button>
            </div>

            <div v-if="staff.staff_files && staff.staff_files.length > 0">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>File Name</th>
                      <th>Description</th>
                      <th>Category</th>
                      <th>File Type</th>
                      <th>Size</th>
                      <th>Uploaded</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="file in staff.staff_files" :key="file.id">
                      <td>
                        <div class="d-flex align-items-center">
                          <i :class="file.file_icon" class="me-2"></i>
                          <span class="fw-medium">{{ file.original_name }}</span>
                        </div>
                      </td>
                      <td>{{ file.description || 'No description' }}</td>
                      <td>
                        <span class="badge bg-secondary">{{ file.category || 'General' }}</span>
                      </td>
                      <td>{{ file.file_type?.toUpperCase() || 'Unknown' }}</td>
                      <td>{{ file.file_size_human }}</td>
                      <td>{{ formatDate(file.created_at) }}</td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <a :href="file.download_url" class="btn btn-outline-primary" title="Download File">
                            <i class="fas fa-download"></i>
                          </a>
                          <button class="btn btn-outline-danger" @click="deleteFile(file.id)" title="Delete File">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div v-else class="text-center py-5 text-muted">
              <i class="fas fa-file fa-3x mb-3"></i>
              <p>No files uploaded</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Contract Modal -->
    <div v-if="showUploadModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" @click="showUploadModal = false">
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Upload Employment Contract</h5>
            <button type="button" class="btn-close" @click="showUploadModal = false"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="uploadContract">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="contract_type" class="form-label">Contract Type</label>
                  <select v-model="contractForm.contract_type" class="form-select custom-select" id="contract_type" required>
                    <option value="">Select Contract Type</option>
                    <option value="fixed_term">Fixed Term</option>
                    <option value="permanent">Permanent</option>
                    <option value="probation">Probation</option>
                    <option value="temporary">Temporary</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="start_date" class="form-label">Start Date</label>
                  <input v-model="contractForm.start_date" type="date" class="form-control" id="start_date" required>
                </div>
                <div class="col-md-6">
                  <label for="end_date" class="form-label">End Date</label>
                  <input v-model="contractForm.end_date" type="date" class="form-control" id="end_date">
                  <small class="form-text text-muted">Leave empty for permanent contracts</small>
                </div>
                <div class="col-md-6">
                  <label for="contract_file" class="form-label">Contract File</label>
                  <input @change="handleFileUpload" type="file" class="form-control" id="contract_file" accept=".pdf,.doc,.docx" required>
                  <small class="form-text text-muted">PDF, DOC, or DOCX files only</small>
                </div>
                <div class="col-12">
                  <label for="notes" class="form-label">Notes (Optional)</label>
                  <textarea v-model="contractForm.notes" class="form-control" id="notes" rows="3" placeholder="Additional notes about this contract..."></textarea>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input v-model="contractForm.is_current" class="form-check-input" type="checkbox" id="is_current">
                    <label class="form-check-label" for="is_current">
                      Mark as current active contract
                    </label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showUploadModal = false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="uploadContract" :disabled="uploading">
              <span v-if="uploading" class="spinner-border spinner-border-sm me-2"></span>
              {{ uploading ? 'Uploading...' : 'Upload Contract' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add License Modal -->
    <div v-if="showLicenseModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" @click="showLicenseModal = false">
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ licenseForm.id ? 'Edit License' : 'Add New License' }}</h5>
            <button type="button" class="btn-close" @click="closeLicenseModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveLicense">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">License Type</label>
                  <select v-model="licenseForm.license_type_id" class="form-select custom-select" required>
                    <option value="">Select License Type</option>
                    <option v-for="type in licenseTypes" :key="type.id" :value="type.id">
                      {{ type.name }} ({{ type.code }})
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">License Number</label>
                  <input v-model="licenseForm.license_number" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Issuing Authority</label>
                  <input v-model="licenseForm.issuing_authority" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Status</label>
                  <select v-model="licenseForm.license_status_id" class="form-select custom-select" required>
                    <option value="">Select Status</option>
                    <option v-for="status in licenseStatuses" :key="status.id" :value="status.id">
                      {{ status.name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Issue Date</label>
                  <input v-model="licenseForm.issue_date" type="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Expiry Date</label>
                  <input v-model="licenseForm.expiry_date" type="date" class="form-control">
                </div>
                <div class="col-12">
                  <label class="form-label">Restrictions/Limitations</label>
                  <textarea v-model="licenseForm.restrictions" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Notes</label>
                  <textarea v-model="licenseForm.notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Document File</label>
                  <input type="file" class="form-control" @change="handleLicenseFileUpload" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input v-model="licenseForm.is_current" class="form-check-input" type="checkbox" id="license_is_current">
                    <label class="form-check-label" for="license_is_current">
                      Mark as current active license
                    </label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeLicenseModal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveLicense" :disabled="licenseSaving">
              <span v-if="licenseSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ licenseSaving ? 'Saving...' : (licenseForm.id ? 'Update License' : 'Add License') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Medical Certificate Modal -->
    <div v-if="showMedicalModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" @click="showMedicalModal = false">
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ medicalForm.id ? 'Edit Medical Certificate' : 'Add New Medical Certificate' }}</h5>
            <button type="button" class="btn-close" @click="closeMedicalModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveMedicalCertificate">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Medical Class</label>
                  <select v-model="medicalForm.medical_class_id" class="form-select custom-select" required>
                    <option value="">Select Medical Class</option>
                    <option v-for="medClass in medicalClasses" :key="medClass.id" :value="medClass.id">
                      {{ medClass.name }} ({{ medClass.code }})
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Certificate Number</label>
                  <input v-model="medicalForm.certificate_number" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Issue Date</label>
                  <input v-model="medicalForm.issue_date" type="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Expiry Date</label>
                  <input v-model="medicalForm.expiry_date" type="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Examining Doctor</label>
                  <input v-model="medicalForm.examining_doctor" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Examining Facility</label>
                  <input v-model="medicalForm.examining_facility" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Medical Status</label>
                  <select v-model="medicalForm.medical_status_id" class="form-select custom-select" required>
                    <option value="">Select Status</option>
                    <option v-for="status in medicalStatuses" :key="status.id" :value="status.id">
                      {{ status.name }}
                    </option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Limitations/Restrictions</label>
                  <textarea v-model="medicalForm.limitations" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Notes</label>
                  <textarea v-model="medicalForm.notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Document File</label>
                  <input type="file" class="form-control" @change="handleMedicalFileUpload" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input v-model="medicalForm.is_current" class="form-check-input" type="checkbox" id="medical_is_current">
                    <label class="form-check-label" for="medical_is_current">
                      Mark as current medical certificate
                    </label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeMedicalModal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveMedicalCertificate" :disabled="medicalSaving">
              <span v-if="medicalSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ medicalSaving ? 'Saving...' : (medicalForm.id ? 'Update Certificate' : 'Add Certificate') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Type Rating Modal -->
    <div v-if="showTypeRatingModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" @click="showTypeRatingModal = false">
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Type Rating</h5>
            <button type="button" class="btn-close" @click="closeTypeRatingModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveTypeRating">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Aircraft Type</label>
                  <input v-model="typeRatingForm.aircraft_type" type="text" class="form-control" required placeholder="e.g., Boeing 737-800">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Rating Type</label>
                  <select v-model="typeRatingForm.rating_type" class="form-select custom-select" required>
                    <option value="type_rating">Type Rating</option>
                    <option value="class_rating">Class Rating</option>
                    <option value="endorsement">Endorsement</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Issue Date</label>
                  <input v-model="typeRatingForm.issue_date" type="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Expiry Date</label>
                  <input v-model="typeRatingForm.expiry_date" type="date" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Issuing Authority</label>
                  <input v-model="typeRatingForm.issuing_authority" type="text" class="form-control" required placeholder="e.g., FAA, EASA">
                </div>
                <div class="col-12">
                  <label class="form-label">Limitations/Restrictions</label>
                  <textarea v-model="typeRatingForm.limitations" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Notes</label>
                  <textarea v-model="typeRatingForm.notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Document File</label>
                  <input type="file" class="form-control" @change="handleTypeRatingFileUpload" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input v-model="typeRatingForm.is_current" class="form-check-input" type="checkbox" id="rating_is_current">
                    <label class="form-check-label" for="rating_is_current">
                      Mark as current type rating
                    </label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeTypeRatingModal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveTypeRating" :disabled="typeRatingSaving">
              <span v-if="typeRatingSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ typeRatingSaving ? 'Saving...' : (typeRatingForm.id ? 'Update Rating' : 'Add Rating') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Proficiency Modal -->
    <div v-if="showProficiencyModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" @click="showProficiencyModal = false">
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Proficiency & Renewal</h5>
            <button type="button" class="btn-close" @click="closeProficiencyModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveProficiency">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Proficiency Type</label>
                  <select v-model="proficiencyForm.proficiency_type_id" class="form-select custom-select" required>
                    <option value="">Select Proficiency Type</option>
                    <option v-for="type in proficiencyTypes" :key="type.id" :value="type.id">
                      {{ type.name }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Status</label>
                  <select v-model="proficiencyForm.status" class="form-select custom-select" required>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="due_soon">Due Soon</option>
                    <option value="overdue">Overdue</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Last Completed Date</label>
                  <input v-model="proficiencyForm.last_completion_date" type="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Next Due Date</label>
                  <input v-model="proficiencyForm.next_due_date" type="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Training Provider</label>
                  <input v-model="proficiencyForm.training_provider" type="text" class="form-control" placeholder="e.g., FlightSafety International">
                </div>
                <div class="col-md-6">
                  <label class="form-label">EPL Level (if applicable)</label>
                  <select v-model="proficiencyForm.epl_level" class="form-select custom-select">
                    <option value="">N/A</option>
                    <option value="1">Level 1</option>
                    <option value="2">Level 2</option>
                    <option value="3">Level 3</option>
                    <option value="4">Level 4</option>
                    <option value="5">Level 5</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Notes</label>
                  <textarea v-model="proficiencyForm.notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Document File</label>
                  <input type="file" class="form-control" @change="handleProficiencyFileUpload" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input v-model="proficiencyForm.is_current" class="form-check-input" type="checkbox" id="proficiency_is_current">
                    <label class="form-check-label" for="proficiency_is_current">
                      Mark as current proficiency record
                    </label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeProficiencyModal">Cancel</button>
            <button type="button" class="btn btn-info" @click="saveProficiency" :disabled="proficiencySaving">
              <span v-if="proficiencySaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ proficiencySaving ? 'Saving...' : (proficiencyForm.id ? 'Update Proficiency' : 'Add Proficiency') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- File Upload Modal -->
    <div v-if="showFileModal" class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" @click="showFileModal = false">
      <div class="modal-dialog modal-lg" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Upload Staff File</h5>
            <button type="button" class="btn-close" @click="closeFileModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveFile">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">File Name</label>
                  <input v-model="fileForm.file_name" type="text" class="form-control" placeholder="Enter a descriptive name for this file" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">File Category</label>
                  <select v-model="fileForm.category" class="form-select custom-select" required>
                    <option value="">Select Category</option>
                    <option value="general">General</option>
                    <option value="contract">Contract</option>
                    <option value="certificate">Certificate</option>
                    <option value="identification">Identification</option>
                    <option value="resume">Resume</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Visibility</label>
                  <select v-model="fileForm.is_public" class="form-select custom-select" required>
                    <option :value="false">Private (HR Only)</option>
                    <option :value="true">Public (Staff Accessible)</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Description</label>
                  <input v-model="fileForm.description" type="text" class="form-control" placeholder="Brief description of the file">
                </div>
                <div class="col-12">
                  <label class="form-label">Select File</label>
                  <input @change="handleFileSelect" type="file" class="form-control" required>
                  <small class="form-text text-muted">Maximum file size: 10MB. Allowed types: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX</small>
                </div>
                <div class="col-12" v-if="fileForm.file">
                  <div class="alert alert-info">
                    <i class="fas fa-file me-2"></i>
                    <strong>Selected:</strong> {{ fileForm.file.name }} ({{ formatFileSize(fileForm.file.size) }})
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeFileModal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveFile" :disabled="fileSaving">
              <span v-if="fileSaving" class="spinner-border spinner-border-sm me-2"></span>
              {{ fileSaving ? 'Uploading...' : 'Upload File' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  staff: Object,
  licenseTypes: Array,
  licenseStatuses: Array,
  medicalClasses: Array,
  medicalStatuses: Array,
  proficiencyTypes: Array
})

const activeTab = ref('basic')
const showUploadModal = ref(false)
const uploading = ref(false)
const contractForm = ref({
  contract_type: '',
  start_date: '',
  end_date: '',
  contract_file: null,
  notes: '',
  is_current: true
})

// License modal and form
const showLicenseModal = ref(false)
const licenseSaving = ref(false)
const licenseForm = ref({
  id: null,
  license_type_id: '',
  license_number: '',
  issuing_authority: '',
  license_status_id: '',
  issue_date: '',
  expiry_date: '',
  restrictions: '',
  notes: '',
  document_file: null,
  is_current: true
})

// Type rating modal and form
const showTypeRatingModal = ref(false)
const typeRatingSaving = ref(false)
const typeRatingForm = ref({
  id: null,
  aircraft_type: '',
  rating_type: 'type_rating',
  issue_date: '',
  expiry_date: '',
  issuing_authority: '',
  limitations: '',
  notes: '',
  document_file: null,
  is_current: true
})

// Medical certificate modal and form
const showMedicalModal = ref(false)
const medicalSaving = ref(false)
const medicalForm = ref({
  id: null,
  medical_class_id: '',
  certificate_number: '',
  issue_date: '',
  expiry_date: '',
  examining_doctor: '',
  examining_facility: '',
  medical_status_id: '',
  limitations: '',
  notes: '',
  document_file: null,
  is_current: true
})

// Proficiency modal and form
const showProficiencyModal = ref(false)
const proficiencySaving = ref(false)
const proficiencyForm = ref({
  id: null,
  proficiency_type_id: '',
  last_completion_date: '',
  next_due_date: '',
  training_provider: '',
  epl_level: '',
  status: 'active',
  notes: '',
  document_file: null,
  is_current: true
})

// File modal and form
const showFileModal = ref(false)
const fileSaving = ref(false)
const fileForm = ref({
  file_name: '',
  description: '',
  category: '',
  is_public: false,
  file: null
})

// Computed properties
const hasAddress = computed(() => {
  return props.staff.address_line_1 || props.staff.address_line_2 ||
         props.staff.city || props.staff.state_region ||
         props.staff.postal_code || (typeof props.staff.country === 'string' ? props.staff.country : props.staff.country?.name)
})

// Utility methods
const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Proficiency utility methods
const getProficiencyDisplayName = (proficiencyType) => {
  if (typeof proficiencyType === 'object' && proficiencyType?.name) {
    return proficiencyType.name
  }

  const typeMap = {
    'EPL': 'Emergency Procedures & Leadership',
    'IR': 'Instrument Rating',
    'OPC': 'Operator Proficiency Check',
    'LINE_CHECK': 'Line Check',
    'DG': 'Dangerous Goods',
    'SMS': 'Safety Management System',
    'CRM': 'Crew Resource Management'
  }

  return typeMap[proficiencyType] || proficiencyType || 'Unknown'
}

const getProficiencyStatusColor = (proficiency) => {
  if (!proficiency.next_due_date) return 'secondary'

  const daysUntil = getDaysUntilDue(proficiency.next_due_date)

  if (daysUntil < 0) return 'danger'    // Overdue
  if (daysUntil < 30) return 'warning'  // Due soon
  if (daysUntil < 90) return 'info'     // Due within 3 months
  return 'success'                      // Current
}

const getDaysUntilDue = (dueDate) => {
  if (!dueDate) return null
  const today = new Date()
  const due = new Date(dueDate)
  const diffTime = due - today
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24))
}

// Contract upload methods
const handleFileUpload = (event) => {
  contractForm.value.contract_file = event.target.files[0]
}

const uploadContract = async () => {
  if (!contractForm.value.contract_file) {
    alert('Please select a contract file')
    return
  }

  uploading.value = true

  try {
    const formData = new FormData()
    formData.append('contract_file', contractForm.value.contract_file)
    formData.append('contract_type', contractForm.value.contract_type)
    formData.append('start_date', contractForm.value.start_date)
    formData.append('end_date', contractForm.value.end_date)
    formData.append('notes', contractForm.value.notes)
    formData.append('is_current', contractForm.value.is_current ? '1' : '0')
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

    const response = await fetch(`/staff/${props.staff.id}/contracts`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })

    if (response.ok) {
      // Reset form
      contractForm.value = {
        contract_type: '',
        start_date: '',
        end_date: '',
        contract_file: null,
        notes: '',
        is_current: true
      }
      showUploadModal.value = false

      // Reload the page to show the new contract
      window.location.reload()
    } else {
      const errorData = await response.json()
      alert('Error uploading contract: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Upload error:', error)
    alert('Error uploading contract. Please try again.')
  } finally {
    uploading.value = false
  }
}

// License modal methods
const closeLicenseModal = () => {
  showLicenseModal.value = false
  licenseForm.value = {
    id: null,
    license_type_id: '',
    license_number: '',
    issuing_authority: '',
    license_status_id: '',
    issue_date: '',
    expiry_date: '',
    restrictions: '',
    notes: '',
    document_file: null,
    is_current: true
  }
}

const handleLicenseFileUpload = (event) => {
  licenseForm.value.document_file = event.target.files[0]
}

const saveLicense = async () => {
  licenseSaving.value = true

  try {
    const formData = new FormData()
    Object.keys(licenseForm.value).forEach(key => {
      if (key === 'document_file' && licenseForm.value[key]) {
        formData.append(key, licenseForm.value[key])
      } else if (key === 'is_current') {
        formData.append(key, licenseForm.value[key] ? '1' : '0')
      } else if (key !== 'document_file') {
        formData.append(key, licenseForm.value[key] || '')
      }
    })
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

    const url = licenseForm.value.id
      ? `/staff/${props.staff.id}/licenses/${licenseForm.value.id}`
      : `/staff/${props.staff.id}/licenses`

    const method = licenseForm.value.id ? 'PUT' : 'POST'
    if (licenseForm.value.id) {
      formData.append('_method', 'PUT')
    }

    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })

    if (response.ok) {
      closeLicenseModal()
      window.location.reload() // Reload to show updated data
    } else {
      const errorData = await response.json()
      alert('Error saving license: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('License save error:', error)
    alert('Error saving license. Please try again.')
  } finally {
    licenseSaving.value = false
  }
}

// Type rating modal methods
const closeTypeRatingModal = () => {
  showTypeRatingModal.value = false
  typeRatingForm.value = {
    id: null,
    aircraft_type: '',
    rating_type: 'type_rating',
    issue_date: '',
    expiry_date: '',
    issuing_authority: '',
    limitations: '',
    notes: '',
    document_file: null,
    is_current: true
  }
}

const handleTypeRatingFileUpload = (event) => {
  typeRatingForm.value.document_file = event.target.files[0]
}

const saveTypeRating = async () => {
  typeRatingSaving.value = true

  try {
    const formData = new FormData()
    Object.keys(typeRatingForm.value).forEach(key => {
      if (key === 'document_file' && typeRatingForm.value[key]) {
        formData.append(key, typeRatingForm.value[key])
      } else if (key === 'is_current') {
        formData.append(key, typeRatingForm.value[key] ? '1' : '0')
      } else if (key !== 'document_file') {
        formData.append(key, typeRatingForm.value[key] || '')
      }
    })
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

    const url = typeRatingForm.value.id
      ? `/staff/${props.staff.id}/type-ratings/${typeRatingForm.value.id}`
      : `/staff/${props.staff.id}/type-ratings`

    if (typeRatingForm.value.id) {
      formData.append('_method', 'PUT')
    }

    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })

    if (response.ok) {
      closeTypeRatingModal()
      window.location.reload()
    } else {
      const errorData = await response.json()
      alert('Error saving type rating: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Type rating save error:', error)
    alert('Error saving type rating. Please try again.')
  } finally {
    typeRatingSaving.value = false
  }
}

// Medical certificate modal methods
const closeMedicalModal = () => {
  showMedicalModal.value = false
  medicalForm.value = {
    id: null,
    medical_class_id: '',
    certificate_number: '',
    issue_date: '',
    expiry_date: '',
    examining_doctor: '',
    examining_facility: '',
    medical_status_id: '',
    limitations: '',
    notes: '',
    document_file: null,
    is_current: true
  }
}

const handleMedicalFileUpload = (event) => {
  medicalForm.value.document_file = event.target.files[0]
}

const saveMedicalCertificate = async () => {
  medicalSaving.value = true

  try {
    const formData = new FormData()
    Object.keys(medicalForm.value).forEach(key => {
      if (key === 'document_file' && medicalForm.value[key]) {
        formData.append(key, medicalForm.value[key])
      } else if (key === 'is_current') {
        formData.append(key, medicalForm.value[key] ? '1' : '0')
      } else if (key !== 'document_file') {
        formData.append(key, medicalForm.value[key] || '')
      }
    })
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

    const url = medicalForm.value.id
      ? `/staff/${props.staff.id}/medical-certificates/${medicalForm.value.id}`
      : `/staff/${props.staff.id}/medical-certificates`

    const method = medicalForm.value.id ? 'PUT' : 'POST'
    if (medicalForm.value.id) {
      formData.append('_method', 'PUT')
    }

    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })

    if (response.ok) {
      closeMedicalModal()
      window.location.reload() // Reload to show updated data
    } else {
      const errorData = await response.json()
      alert('Error saving medical certificate: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Medical certificate save error:', error)
    alert('Error saving medical certificate. Please try again.')
  } finally {
    medicalSaving.value = false
  }
}

// Proficiency modal methods
const closeProficiencyModal = () => {
  showProficiencyModal.value = false
  proficiencyForm.value = {
    id: null,
    proficiency_type_id: '',
    last_completion_date: '',
    next_due_date: '',
    training_provider: '',
    epl_level: '',
    status: 'active',
    notes: '',
    document_file: null,
    is_current: true
  }
}

const handleProficiencyFileUpload = (event) => {
  proficiencyForm.value.document_file = event.target.files[0]
}

const saveProficiency = async () => {
  proficiencySaving.value = true

  try {
    const formData = new FormData()
    Object.keys(proficiencyForm.value).forEach(key => {
      if (key === 'document_file' && proficiencyForm.value[key]) {
        formData.append(key, proficiencyForm.value[key])
      } else if (key === 'is_current') {
        formData.append(key, proficiencyForm.value[key] ? '1' : '0')
      } else if (key !== 'document_file') {
        formData.append(key, proficiencyForm.value[key] || '')
      }
    })
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

    const url = proficiencyForm.value.id
      ? `/staff/${props.staff.id}/proficiencies/${proficiencyForm.value.id}`
      : `/staff/${props.staff.id}/proficiencies`

    if (proficiencyForm.value.id) {
      formData.append('_method', 'PUT')
    }

    const response = await fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })

    if (response.ok) {
      closeProficiencyModal()
      window.location.reload()
    } else {
      const errorData = await response.json()
      alert('Error saving proficiency: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Proficiency save error:', error)
    alert('Error saving proficiency. Please try again.')
  } finally {
    proficiencySaving.value = false
  }
}

// File methods
const closeFileModal = () => {
  showFileModal.value = false
  fileForm.value = {
    file_name: '',
    description: '',
    category: '',
    is_public: false,
    file: null
  }
}

const handleFileSelect = (event) => {
  fileForm.value.file = event.target.files[0]
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 Bytes'

  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const saveFile = async () => {
  if (!fileForm.value.file) {
    alert('Please select a file')
    return
  }

  fileSaving.value = true

  try {
    const formData = new FormData()
    formData.append('file', fileForm.value.file)
    formData.append('file_name', fileForm.value.file_name)
    formData.append('description', fileForm.value.description)
    formData.append('category', fileForm.value.category)
    formData.append('is_public', fileForm.value.is_public ? '1' : '0')
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

    const response = await fetch(`/staff/${props.staff.id}/files`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })

    if (response.ok) {
      closeFileModal()
      window.location.reload()
    } else {
      const errorData = await response.json()
      alert('Error uploading file: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('File upload error:', error)
    alert('Error uploading file. Please try again.')
  } finally {
    fileSaving.value = false
  }
}

const deleteFile = async (fileId) => {
  if (!confirm('Are you sure you want to delete this file?')) {
    return
  }

  try {
    const response = await fetch(`/staff/${props.staff.id}/files/${fileId}`, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    if (response.ok) {
      window.location.reload()
    } else {
      const errorData = await response.json()
      alert('Error deleting file: ' + (errorData.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('File delete error:', error)
    alert('Error deleting file. Please try again.')
  }
}
</script>

<style scoped>
/* Professional Page Layout */
body {
  background-color: #f8f9fa;
}

/* Header Styling */
.avatar-lg {
  width: 120px;
  height: 120px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  overflow: hidden;
  border: 2px solid #e9ecef;
  background: #f8f9fa;
}

.initials-fallback {
  font-weight: 700;
  font-size: 2.5rem;
  color: #495057;
  text-transform: uppercase;
}

.object-cover {
  object-fit: cover;
}

/* Professional Card Styling */
.card {
  transition: all 0.3s ease;
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 15px rgba(0,0,0,0.08);
  overflow: hidden;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

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

/* Statistics Cards */
.stat-card {
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100%;
  height: 100%;
  opacity: 0.1;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="80" cy="20" r="30" fill="white"/></svg>');
  background-size: 80px;
  background-repeat: no-repeat;
  background-position: top right;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.stat-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
}

.stat-success {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.stat-info {
  background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
}

.stat-warning {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
}

.stat-icon {
  font-size: 1.5rem;
  color: rgba(255, 255, 255, 0.8);
  flex-shrink: 0;
}

.text-white-75 {
  color: rgba(255, 255, 255, 0.85) !important;
}

.stat-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  margin-bottom: 0.25rem;
  line-height: 1;
}

.stat-value {
  font-size: 1.1rem;
  text-shadow: 0 1px 3px rgba(0,0,0,0.1);
  line-height: 1.2;
}

.stat-card .card-body {
  padding: 0.75rem;
}

/* Table Styling */
.table {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.table thead th {
  background: #f8f9fa;
  color: #495057;
  border: none;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.875rem;
  padding: 0.75rem;
  border-bottom: 2px solid #dee2e6;
}

.table tbody tr {
  transition: background-color 0.2s ease;
}

.table tbody tr:hover {
  background-color: rgba(0,123,255,0.04);
}

.table tbody td {
  padding: 0.75rem;
  border-color: #f1f3f4;
  vertical-align: middle;
}

/* Badge Styling */
.badge {
  font-weight: 600;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Button Styling */
.btn {
  border-radius: 10px;
  font-weight: 600;
  padding: 0.75rem 1.5rem;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.875rem;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.btn-primary {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  border: none;
}

.btn-warning {
  background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
  border: none;
}

.btn-outline-secondary {
  border: 2px solid #6c757d;
  color: #6c757d;
  background: white;
}

.btn-outline-secondary:hover {
  background: #6c757d;
  color: white;
}

/* Document and Contract Items */
.document-item, .contract-item {
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  border: 2px solid #f1f3f4;
  transition: all 0.3s ease;
}

.document-item:hover, .contract-item:hover {
  border-color: #007bff;
  box-shadow: 0 6px 25px rgba(0,123,255,0.1);
}

.contract-history-item {
  transition: all 0.3s ease;
  border-radius: 8px;
  margin-bottom: 0.75rem;
}

.contract-history-item:hover {
  transform: translateX(8px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
  .nav-pills {
    flex-direction: column;
    gap: 0.25rem;
  }

  .nav-pills .nav-link {
    text-align: center;
    padding: 0.75rem;
  }

  .tab-content {
    padding: 1rem;
  }

  .avatar-lg {
    width: 100px;
    height: 100px;
  }

  .initials-fallback {
    font-size: 2rem;
  }
}

/* Empty State Styling */
.text-center.text-muted {
  padding: 3rem;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  border-radius: 12px;
  border: 2px dashed #dee2e6;
}

/* Small Button Styling */
.btn-xs {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
  line-height: 1.25;
  border-radius: 6px;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  border-radius: 8px;
}

/* Enhanced Spacing */
.mb-4 {
  margin-bottom: 1.25rem !important;
}

.mb-3 {
  margin-bottom: 1rem !important;
}

/* Professional Typography */
h1, h2, h3, h4, h5, h6 {
  color: #2c3e50;
  font-weight: 700;
}

h5 {
  font-size: 1.25rem;
  margin-bottom: 1rem;
  position: relative;
  padding-bottom: 0.5rem;
}

h5::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 50px;
  height: 3px;
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  border-radius: 2px;
}

/* Global Select Styling - Exact Match to Create Page */
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
</style>