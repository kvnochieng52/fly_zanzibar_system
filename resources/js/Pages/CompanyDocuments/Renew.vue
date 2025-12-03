<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Renew Document</h1>
              <p class="text-muted">Renew and update document information</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('company-documents.dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item">
                  <Link :href="route('company-documents.index')">Documents</Link>
                </li>
                <li class="breadcrumb-item">
                  <Link :href="route('company-documents.show', document.id)">{{ document.title }}</Link>
                </li>
                <li class="breadcrumb-item active">Renew</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <form @submit.prevent="submit">
            <!-- Document Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-sync-alt mr-2"></i>
                  Document Renewal
                </h3>
                <div class="card-tools">
                  <span class="badge badge-warning">
                    <i class="fas fa-clock mr-1"></i>
                    {{ document.is_expired ? 'Expired' : (document.days_until_expiry + ' days remaining') }}
                  </span>
                </div>
              </div>
              <div class="card-body">
                <!-- Current Document Info -->
                <div class="alert alert-info">
                  <h5><i class="fas fa-info-circle mr-2"></i>Current Document Information</h5>
                  <div class="row">
                    <div class="col-md-6">
                      <strong>Document Number:</strong> {{ document.document_number }}<br>
                      <strong>Title:</strong> {{ document.title }}<br>
                      <strong>Type:</strong> {{ document.document_type?.name }}
                    </div>
                    <div class="col-md-6">
                      <strong>Issue Date:</strong> {{ formatDate(document.issue_date) }}<br>
                      <strong>Expiry Date:</strong> {{ formatDate(document.expiry_date) }}<br>
                      <strong>Version:</strong> {{ document.version_revision }}
                    </div>
                  </div>
                </div>

                <div class="row">
                  <!-- Document Number -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Document Number <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.document_number"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.document_number }"
                      placeholder="Enter new document number"
                    />
                    <div v-if="form.errors.document_number" class="invalid-feedback">
                      {{ form.errors.document_number }}
                    </div>
                  </div>

                  <!-- Title -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Title <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.title"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.title }"
                      placeholder="Enter document title"
                    />
                    <div v-if="form.errors.title" class="invalid-feedback">
                      {{ form.errors.title }}
                    </div>
                  </div>

                  <!-- Issue Date -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      New Issue Date <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.issue_date"
                      type="date"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.issue_date }"
                    />
                    <div v-if="form.errors.issue_date" class="invalid-feedback">
                      {{ form.errors.issue_date }}
                    </div>
                  </div>

                  <!-- New Expiry Date -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      New Expiry Date <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model="form.expiry_date"
                      type="date"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.expiry_date }"
                    />
                    <div v-if="form.errors.expiry_date" class="invalid-feedback">
                      {{ form.errors.expiry_date }}
                    </div>
                  </div>

                  <!-- Version/Revision -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      New Version/Revision
                    </label>
                    <input
                      v-model="form.version_revision"
                      type="text"
                      class="form-control"
                      placeholder="e.g., v2.0, Rev B"
                    />
                  </div>

                  <!-- Status -->
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

            <!-- Responsibility & Authority -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-users mr-2"></i>
                  Responsibility & Authority
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <!-- Issuing Authority -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Issuing Authority <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.issuing_authority_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.issuing_authority_id }"
                    >
                      <option value="">Select issuing authority</option>
                      <option
                        v-for="authority in issuingAuthorities"
                        :key="authority.id"
                        :value="authority.id"
                      >
                        {{ authority.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.issuing_authority_id" class="invalid-feedback">
                      {{ form.errors.issuing_authority_id }}
                    </div>
                  </div>

                  <!-- Responsible Department -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Responsible Department <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.responsible_department_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.responsible_department_id }"
                    >
                      <option value="">Select department</option>
                      <option
                        v-for="dept in departments"
                        :key="dept.id"
                        :value="dept.id"
                      >
                        {{ dept.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.responsible_department_id" class="invalid-feedback">
                      {{ form.errors.responsible_department_id }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- New Document File -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-upload mr-2"></i>
                  Upload Renewed Document
                </h3>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label class="form-label">
                    Document File <span class="text-danger">*</span>
                  </label>
                  <input
                    @change="handleFileChange"
                    type="file"
                    accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.file }"
                  />
                  <small class="form-text text-muted">
                    Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG (Max: 10MB)
                  </small>
                  <div v-if="form.errors.file" class="invalid-feedback">
                    {{ form.errors.file }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-sticky-note mr-2"></i>
                  Renewal Notes
                </h3>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label class="form-label">
                    Notes
                  </label>
                  <textarea
                    v-model="form.notes"
                    rows="4"
                    class="form-control"
                    placeholder="Add any notes about this document renewal"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="card">
              <div class="card-footer">
                <div class="d-flex justify-content-end">
                  <Link
                    :href="route('company-documents.show', document.id)"
                    class="btn btn-secondary mr-2"
                  >
                    <i class="fas fa-times mr-1"></i>
                    Cancel
                  </Link>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="btn btn-success"
                  >
                    <i class="fas fa-sync-alt mr-1"></i>
                    {{ form.processing ? 'Renewing...' : 'Renew Document' }}
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
  document: Object,
  documentTypes: Array,
  issuingAuthorities: Array,
  departments: Array,
  statuses: Array
})

const form = useForm({
  document_number: '',
  title: props.document.title,
  issue_date: new Date().toISOString().split('T')[0], // Today's date
  expiry_date: '',
  version_revision: '',
  status_id: props.document.status_id,
  issuing_authority_id: props.document.issuing_authority_id,
  responsible_department_id: props.document.responsible_department_id,
  notes: '',
  file: null
})

const handleFileChange = (event) => {
  form.file = event.target.files[0]
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}

const submit = () => {
  form.post(route('company-documents.process-renewal', props.document.id), {
    forceFormData: true
  })
}
</script>