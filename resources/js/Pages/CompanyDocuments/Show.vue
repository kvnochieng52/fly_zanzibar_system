<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">{{ document.title }}</h1>
              <p class="text-muted">{{ document.document_type?.name }} • {{ document.document_number }}</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('company-documents.dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item">
                  <Link :href="route('company-documents.index')">Documents</Link>
                </li>
                <li class="breadcrumb-item active">{{ document.title }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
              <!-- Document Details -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Document Information
                  </h3>
                  <div class="card-tools">
                    <Link
                      :href="route('company-documents.edit', document.id)"
                      class="btn btn-outline-primary btn-sm"
                    >
                      <i class="fas fa-edit mr-1"></i>
                      Edit
                    </Link>
                    <Link
                      :href="route('company-documents.renew', document.id)"
                      class="btn btn-warning btn-sm ml-1"
                      :class="{ 'btn-danger': document.is_expired }"
                    >
                      <i class="fas fa-sync-alt mr-1"></i>
                      {{ document.is_expired ? 'Renew Expired' : 'Renew' }}
                    </Link>
                    <button
                      v-if="document.file_path"
                      @click="downloadDocument"
                      class="btn btn-primary btn-sm ml-1"
                    >
                      <i class="fas fa-download mr-1"></i>
                      Download
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <strong class="text-muted">Document Number:</strong><br>
                      <span class="h6">{{ document.document_number }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                      <strong class="text-muted">Type:</strong><br>
                      <span class="badge badge-secondary">{{ document.document_type?.name }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                      <strong class="text-muted">Issue Date:</strong><br>
                      <span>{{ formatDate(document.issue_date) }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                      <strong class="text-muted">Expiry Date:</strong><br>
                      <span v-if="document.expiry_date">
                        {{ formatDate(document.expiry_date) }}
                        <span
                          v-if="document.days_until_expiry !== null"
                          :class="{
                            'text-danger': document.is_expired,
                            'text-warning': document.is_expiring_soon && !document.is_expired,
                            'text-success': !document.is_expiring_soon && !document.is_expired
                          }"
                          class="ml-2 small font-weight-bold"
                        >
                          ({{ document.is_expired ? 'Expired' : `${document.days_until_expiry} days left` }})
                        </span>
                      </span>
                      <span v-else class="text-muted">No expiry date</span>
                    </div>
                    <div v-if="document.version_revision" class="col-md-6 mb-3">
                      <strong class="text-muted">Version/Revision:</strong><br>
                      <span>{{ document.version_revision }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                      <strong class="text-muted">Status:</strong><br>
                      <span
                        class="badge badge-pill"
                        :style="{ backgroundColor: document.status?.color, color: '#fff' }"
                      >
                        {{ document.status?.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Authority and Responsibility -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Authority & Responsibility
                  </h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <strong class="text-muted">Issuing Authority:</strong><br>
                      <span>{{ document.issuing_authority?.name }}</span>
                      <span v-if="document.issuing_authority?.country" class="text-muted">
                        ({{ document.issuing_authority.country }})
                      </span>
                    </div>
                    <div class="col-md-6 mb-3">
                      <strong class="text-muted">Responsible Department:</strong><br>
                      <span class="badge badge-info">{{ document.responsible_department?.name }}</span>
                    </div>
                    <div v-if="document.issuing_authority?.contact_email" class="col-md-6 mb-3">
                      <strong class="text-muted">Authority Contact:</strong><br>
                      <a :href="`mailto:${document.issuing_authority.contact_email}`" class="text-primary">
                        <i class="fas fa-envelope mr-1"></i>
                        {{ document.issuing_authority.contact_email }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div v-if="document.notes" class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-sticky-note mr-2"></i>
                    Notes
                  </h3>
                </div>
                <div class="card-body">
                  <p class="mb-0">{{ document.notes }}</p>
                </div>
              </div>

              <!-- Document Versions -->
              <div v-if="document.versions && document.versions.length > 0" class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Version History
                  </h3>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th>Version</th>
                          <th>Issue Date</th>
                          <th>Created By</th>
                          <th>Notes</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="version in document.versions" :key="version.id">
                          <td>
                            <strong>{{ version.version_revision }}</strong>
                          </td>
                          <td>{{ formatDate(version.issue_date) }}</td>
                          <td>{{ version.creator?.name }}</td>
                          <td>
                            <span v-if="version.change_notes" class="small text-muted">
                              {{ version.change_notes }}
                            </span>
                            <span v-else class="text-muted">-</span>
                          </td>
                          <td>
                            <button v-if="version.file_path" class="btn btn-sm btn-outline-primary">
                              <i class="fas fa-download mr-1"></i>
                              Download
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
              <!-- File Information -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-file mr-2"></i>
                    File Information
                  </h3>
                </div>
                <div class="card-body">
                  <div v-if="document.file_path" class="text-center">
                    <div class="mb-3">
                      <i class="fas fa-file text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="mb-1">{{ document.file_name }}</h6>
                    <p class="text-muted small mb-3">{{ formatFileSize(document.file_size) }}</p>
                    <button
                      @click="downloadDocument"
                      class="btn btn-primary btn-block"
                    >
                      <i class="fas fa-download mr-2"></i>
                      Download File
                    </button>
                  </div>
                  <div v-else class="text-center py-4">
                    <i class="fas fa-file-slash text-muted mb-3" style="font-size: 3rem;"></i>
                    <p class="text-muted">No file uploaded</p>
                  </div>
                </div>
              </div>

              <!-- Audit Trail -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Audit Trail
                  </h3>
                </div>
                <div class="card-body">
                  <div class="timeline">
                    <div class="time-label">
                      <span class="bg-info">Created</span>
                    </div>
                    <div>
                      <i class="fas fa-user bg-success"></i>
                      <div class="timeline-item">
                        <span class="time">
                          <i class="fas fa-clock"></i> {{ formatDateTime(document.created_at) }}
                        </span>
                        <h3 class="timeline-header">Document Created</h3>
                        <div class="timeline-body">
                          Created by <strong>{{ document.creator?.name }}</strong>
                        </div>
                      </div>
                    </div>

                    <div v-if="document.updated_at !== document.created_at">
                      <div class="time-label">
                        <span class="bg-warning">Updated</span>
                      </div>
                      <div>
                        <i class="fas fa-edit bg-warning"></i>
                        <div class="timeline-item">
                          <span class="time">
                            <i class="fas fa-clock"></i> {{ formatDateTime(document.updated_at) }}
                          </span>
                          <h3 class="timeline-header">Document Updated</h3>
                          <div class="timeline-body" v-if="document.updater">
                            Updated by <strong>{{ document.updater?.name }}</strong>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div>
                      <i class="fas fa-clock bg-gray"></i>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Actions -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Actions
                  </h3>
                </div>
                <div class="card-body">
                  <div class="d-grid gap-2">
                    <Link
                      :href="route('company-documents.edit', document.id)"
                      class="btn btn-outline-primary btn-block"
                    >
                      <i class="fas fa-edit mr-2"></i>
                      Edit Document
                    </Link>
                    <Link
                      :href="route('company-documents.renew', document.id)"
                      class="btn btn-warning btn-block mb-2"
                      :class="{ 'btn-danger': document.is_expired }"
                    >
                      <i class="fas fa-sync-alt mr-2"></i>
                      {{ document.is_expired ? 'Renew Expired Document' : 'Renew Document' }}
                    </Link>
                    <Link
                      :href="route('company-documents.index')"
                      class="btn btn-outline-secondary btn-block"
                    >
                      <i class="fas fa-list mr-2"></i>
                      View All Documents
                    </Link>
                    <button
                      @click="deleteDocument"
                      class="btn btn-outline-danger btn-block"
                    >
                      <i class="fas fa-trash mr-2"></i>
                      Delete Document
                    </button>
                  </div>
                </div>
              </div>

              <!-- Document Status Alert -->
              <div v-if="document.is_expired || document.is_expiring_soon" class="card">
                <div class="card-header" :class="{
                  'bg-danger': document.is_expired,
                  'bg-warning': document.is_expiring_soon && !document.is_expired
                }">
                  <h3 class="card-title text-white">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Document Alert
                  </h3>
                </div>
                <div class="card-body">
                  <div v-if="document.is_expired" class="text-danger">
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong>This document has expired!</strong>
                    <p class="mt-2 mb-0 small">Immediate action required for renewal.</p>
                  </div>
                  <div v-else-if="document.is_expiring_soon" class="text-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Document expires in {{ document.days_until_expiry }} days</strong>
                    <p class="mt-2 mb-0 small">Please plan for renewal process.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  document: Object
})

const downloadDocument = () => {
  window.open(route('company-documents.download', props.document.id), '_blank')
}

const deleteDocument = () => {
  if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
    router.delete(route('company-documents.destroy', props.document.id))
  }
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}

const formatDateTime = (datetime) => {
  if (!datetime) return ''
  return new Date(datetime).toLocaleString()
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}
</script>