<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Company Documents</h1>
              <p class="text-muted">Manage company documentation and track expiry dates</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('company-documents.dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item active">Documents</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Filters Card -->
          <div class="card collapsed-card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Filters
              </h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-plus"></i>
                </button>
                <Link
                  :href="route('company-documents.create')"
                  class="btn btn-primary btn-sm ml-2"
                >
                  <i class="fas fa-plus mr-1"></i>
                  Add Document
                </Link>
              </div>
            </div>
            <div class="card-body" style="display: none;">
              <div class="row">
                <!-- Search -->
                <div class="col-md-3 mb-3">
                  <label class="form-label">Search</label>
                  <input
                    v-model="form.search"
                    type="text"
                    class="form-control"
                    placeholder="Search documents..."
                  />
                </div>

                <!-- Document Type -->
                <div class="col-md-3 mb-3">
                  <label class="form-label">Document Type</label>
                  <select
                    v-model="form.document_type_id"
                    class="form-control"
                  >
                    <option value="">All Types</option>
                    <option
                      v-for="type in documentTypes"
                      :key="type.id"
                      :value="type.id"
                    >
                      {{ type.name }}
                    </option>
                  </select>
                </div>

                <!-- Status -->
                <div class="col-md-3 mb-3">
                  <label class="form-label">Status</label>
                  <select
                    v-model="form.status_id"
                    class="form-control"
                  >
                    <option value="">All Statuses</option>
                    <option
                      v-for="status in statuses"
                      :key="status.id"
                      :value="status.id"
                    >
                      {{ status.name }}
                    </option>
                  </select>
                </div>

                <!-- Department -->
                <div class="col-md-3 mb-3">
                  <label class="form-label">Department</label>
                  <select
                    v-model="form.department_id"
                    class="form-control"
                  >
                    <option value="">All Departments</option>
                    <option
                      v-for="dept in departments"
                      :key="dept.id"
                      :value="dept.id"
                    >
                      {{ dept.name }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Quick Filters -->
              <div class="row">
                <div class="col-12">
                  <div class="btn-group mb-3" role="group">
                    <button
                      @click="form.expiring_soon = !form.expiring_soon"
                      type="button"
                      :class="[
                        'btn',
                        form.expiring_soon ? 'btn-warning' : 'btn-outline-warning'
                      ]"
                    >
                      <i class="fas fa-clock mr-1"></i>
                      Expiring Soon
                    </button>
                    <button
                      @click="form.expired = !form.expired"
                      type="button"
                      :class="[
                        'btn',
                        form.expired ? 'btn-danger' : 'btn-outline-danger'
                      ]"
                    >
                      <i class="fas fa-exclamation-circle mr-1"></i>
                      Expired
                    </button>
                  </div>

                  <button
                    @click="clearFilters"
                    class="btn btn-outline-secondary btn-sm"
                  >
                    <i class="fas fa-times mr-1"></i>
                    Clear Filters
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Documents Table -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Documents List</h3>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Document</th>
                      <th>Type</th>
                      <th>Status</th>
                      <th>Expiry Date</th>
                      <th>Department</th>
                      <th width="150">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="document in documents.data" :key="document.id">
                      <td>
                        <div>
                          <strong>{{ document.title }}</strong>
                          <br>
                          <small class="text-muted">{{ document.document_number }}</small>
                        </div>
                      </td>
                      <td>
                        <span class="badge badge-secondary">
                          {{ document.document_type?.name }}
                        </span>
                      </td>
                      <td>
                        <span
                          class="badge badge-pill"
                          :style="{ backgroundColor: document.status?.color, color: '#fff' }"
                        >
                          {{ document.status?.name }}
                        </span>
                      </td>
                      <td>
                        <div v-if="document.expiry_date">
                          {{ formatDate(document.expiry_date) }}
                          <br>
                          <small
                            v-if="document.days_until_expiry !== null"
                            :class="{
                              'text-danger': document.is_expired,
                              'text-warning': document.is_expiring_soon && !document.is_expired,
                              'text-success': !document.is_expiring_soon && !document.is_expired
                            }"
                          >
                            {{ document.is_expired ? 'Expired' : `${document.days_until_expiry} days left` }}
                          </small>
                        </div>
                        <span v-else class="text-muted">No expiry</span>
                      </td>
                      <td>
                        <span class="badge badge-info">
                          {{ document.responsible_department?.name }}
                        </span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <Link
                            :href="route('company-documents.show', document.id)"
                            class="btn btn-sm btn-outline-primary"
                            title="View"
                          >
                            <i class="fas fa-eye"></i>
                          </Link>
                          <Link
                            :href="route('company-documents.edit', document.id)"
                            class="btn btn-sm btn-outline-secondary"
                            title="Edit"
                          >
                            <i class="fas fa-edit"></i>
                          </Link>
                          <button
                            @click="deleteDocument(document)"
                            class="btn btn-sm btn-outline-danger"
                            title="Delete"
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

            <!-- Pagination -->
            <div v-if="documents.data.length > 0" class="card-footer">
              <div class="row align-items-center">
                <div class="col-sm-6">
                  <small class="text-muted">
                    Showing {{ documents.from }} to {{ documents.to }} of {{ documents.total }} results
                  </small>
                </div>
                <div class="col-sm-6">
                  <nav>
                    <ul class="pagination pagination-sm justify-content-end mb-0">
                      <li class="page-item" :class="{ disabled: !documents.prev_page_url }">
                        <Link
                          v-if="documents.prev_page_url"
                          :href="documents.prev_page_url"
                          class="page-link"
                        >
                          Previous
                        </Link>
                        <span v-else class="page-link">Previous</span>
                      </li>
                      <li class="page-item" :class="{ disabled: !documents.next_page_url }">
                        <Link
                          v-if="documents.next_page_url"
                          :href="documents.next_page_url"
                          class="page-link"
                        >
                          Next
                        </Link>
                        <span v-else class="page-link">Next</span>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="documents.data.length === 0" class="card-body text-center py-5">
              <i class="fas fa-file-alt text-muted mb-3" style="font-size: 4rem;"></i>
              <h5 class="text-muted">No Documents Found</h5>
              <p class="text-muted">There are no documents matching your current filters.</p>
              <Link
                :href="route('company-documents.create')"
                class="btn btn-primary"
              >
                <i class="fas fa-plus mr-2"></i>
                Add Your First Document
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  documents: Object,
  documentTypes: Array,
  statuses: Array,
  departments: Array,
  filters: Object
})

const form = ref({
  search: props.filters.search || '',
  document_type_id: props.filters.document_type_id || '',
  status_id: props.filters.status_id || '',
  department_id: props.filters.department_id || '',
  expiring_soon: props.filters.expiring_soon || false,
  expired: props.filters.expired || false
})

// Watch for changes and update URL
watch(form, (newForm) => {
  const params = {}
  Object.keys(newForm).forEach(key => {
    if (newForm[key] !== '' && newForm[key] !== false) {
      params[key] = newForm[key]
    }
  })

  router.get(route('company-documents.index'), params, {
    preserveState: true,
    replace: true
  })
}, { deep: true })

const clearFilters = () => {
  form.value = {
    search: '',
    document_type_id: '',
    status_id: '',
    department_id: '',
    expiring_soon: false,
    expired: false
  }
}

const deleteDocument = (document) => {
  if (confirm('Are you sure you want to delete this document?')) {
    router.delete(route('company-documents.destroy', document.id))
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>