<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Edit Document</h1>
              <p class="text-muted">Update document information and files</p>
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
                <li class="breadcrumb-item active">Edit</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <form @submit.prevent="submit">
            <!-- Basic Information -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-info-circle mr-2"></i>
                  Basic Information
                </h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <!-- Document Type -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Document Type <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.document_type_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.document_type_id }"
                    >
                      <option value="">Select document type</option>
                      <option
                        v-for="type in documentTypes"
                        :key="type.id"
                        :value="type.id"
                      >
                        {{ type.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.document_type_id" class="invalid-feedback">
                      {{ form.errors.document_type_id }}
                    </div>
                  </div>

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
                      placeholder="Enter document number"
                    />
                    <div v-if="form.errors.document_number" class="invalid-feedback">
                      {{ form.errors.document_number }}
                    </div>
                  </div>

                  <!-- Title -->
                  <div class="col-12 mb-3">
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
                      Issue Date <span class="text-danger">*</span>
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

                  <!-- Expiry Date -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">
                      Expiry Date
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
                      Version/Revision
                    </label>
                    <input
                      v-model="form.version_revision"
                      type="text"
                      class="form-control"
                      placeholder="e.g., v1.0, Rev A"
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

            <!-- Current File Information -->
            <div v-if="document.file_path" class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-file mr-2"></i>
                  Current File
                </h3>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 mr-3">
                    <i class="fas fa-file text-primary" style="font-size: 2rem;"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-1">{{ document.file_name }}</h6>
                    <p class="text-muted small mb-0">
                      Uploaded on {{ formatDate(document.created_at) }}
                    </p>
                  </div>
                  <div class="flex-shrink-0">
                    <button
                      type="button"
                      @click="downloadCurrentFile"
                      class="btn btn-outline-primary btn-sm"
                    >
                      <i class="fas fa-download mr-1"></i>
                      Download
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- File Upload -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-upload mr-2"></i>
                  {{ document.file_path ? 'Replace Document File' : 'Upload Document File' }}
                </h3>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label class="form-label">
                    {{ document.file_path ? 'New Document File' : 'Document File' }}
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
                    {{ document.file_path ? ' - Leave empty to keep current file' : '' }}
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
                  Additional Information
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
                    placeholder="Add any additional notes about this document"
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
                    class="btn btn-primary"
                  >
                    <i class="fas fa-save mr-1"></i>
                    {{ form.processing ? 'Updating...' : 'Update Document' }}
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
  document_type_id: props.document.document_type_id,
  document_number: props.document.document_number,
  title: props.document.title,
  issue_date: props.document.issue_date,
  expiry_date: props.document.expiry_date,
  version_revision: props.document.version_revision,
  issuing_authority_id: props.document.issuing_authority_id,
  responsible_department_id: props.document.responsible_department_id,
  status_id: props.document.status_id,
  notes: props.document.notes,
  file: null
})

const handleFileChange = (event) => {
  form.file = event.target.files[0]
}

const downloadCurrentFile = () => {
  window.open(route('company-documents.download', props.document.id), '_blank')
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}

const submit = () => {
  form.post(route('company-documents.update', props.document.id), {
    forceFormData: true,
    _method: 'put'
  })
}
</script>