<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Company Documents Dashboard</h1>
              <p class="text-muted">Overview of all company documentation and alerts</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Statistics Cards -->
          <div class="row">
            <!-- Total Documents -->
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>{{ stats.total }}</h3>
                  <p>Total Documents</p>
                </div>
                <div class="icon">
                  <i class="fas fa-file-alt"></i>
                </div>
                <Link :href="route('company-documents.index')" class="small-box-footer">
                  More info <i class="fas fa-arrow-circle-right"></i>
                </Link>
              </div>
            </div>

            <!-- Active Documents -->
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ stats.active }}</h3>
                  <p>Active Documents</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check-circle"></i>
                </div>
                <Link :href="route('company-documents.index')" class="small-box-footer">
                  More info <i class="fas fa-arrow-circle-right"></i>
                </Link>
              </div>
            </div>

            <!-- Expiring Soon -->
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>{{ stats.expiring_soon }}</h3>
                  <p>Expiring Soon</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <Link :href="route('company-documents.index', { expiring_soon: true })" class="small-box-footer">
                  More info <i class="fas fa-arrow-circle-right"></i>
                </Link>
              </div>
            </div>

            <!-- Expired Documents -->
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>{{ stats.expired }}</h3>
                  <p>Expired</p>
                </div>
                <div class="icon">
                  <i class="fas fa-times-circle"></i>
                </div>
                <Link :href="route('company-documents.index', { expired: true })" class="small-box-footer">
                  More info <i class="fas fa-arrow-circle-right"></i>
                </Link>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-rocket mr-2"></i>
                    Quick Actions
                  </h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3 col-sm-6">
                      <Link :href="route('company-documents.create')" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-plus mr-2"></i>
                        Add Document
                      </Link>
                    </div>
                    <div class="col-md-3 col-sm-6">
                      <Link :href="route('company-documents.index')" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-list mr-2"></i>
                        View All Documents
                      </Link>
                    </div>
                    <div class="col-md-3 col-sm-6">
                      <Link :href="route('company-documents.index', { expiring_soon: true })" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-clock mr-2"></i>
                        Expiring Soon
                      </Link>
                    </div>
                    <div class="col-md-3 col-sm-6">
                      <Link :href="route('company-documents.index', { expired: true })" class="btn btn-danger btn-block mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Expired Documents
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- Recent Documents -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Recent Documents
                  </h3>
                </div>
                <div class="card-body">
                  <div v-if="recentDocuments.length > 0">
                    <div v-for="document in recentDocuments" :key="document.id" class="d-flex justify-content-between align-items-center border-bottom py-2">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt text-muted mr-3"></i>
                        <div>
                          <p class="mb-0 font-weight-bold">{{ document.title }}</p>
                          <small class="text-muted">
                            {{ document.document_type?.name }} • {{ document.responsible_department?.name }}
                          </small>
                        </div>
                      </div>
                      <div class="d-flex align-items-center">
                        <span
                          class="badge badge-pill mr-2"
                          :style="{ backgroundColor: document.status?.color, color: '#fff' }"
                        >
                          {{ document.status?.name }}
                        </span>
                        <Link :href="route('company-documents.show', document.id)" class="btn btn-sm btn-outline-primary">
                          View
                        </Link>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-4">
                    <i class="fas fa-file-alt text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No documents found</p>
                    <Link :href="route('company-documents.create')" class="btn btn-primary btn-sm">
                      Add your first document
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Documents by Type -->
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Documents by Type
                  </h3>
                </div>
                <div class="card-body">
                  <div v-if="documentsByType.length > 0">
                    <div v-for="type in documentsByType" :key="type.id" class="d-flex justify-content-between align-items-center py-2">
                      <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle mr-3" style="width: 8px; height: 8px;"></div>
                        <div>
                          <p class="mb-0 font-weight-bold">{{ type.name }}</p>
                          <small class="text-muted">{{ type.code }}</small>
                        </div>
                      </div>
                      <span class="badge badge-secondary">
                        {{ type.documents_count }} docs
                      </span>
                    </div>
                  </div>
                  <div v-else class="text-center py-4">
                    <i class="fas fa-chart-pie text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No document types with documents</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Alerts Section -->
          <div v-if="stats.expiring_soon > 0 || stats.expired > 0" class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-bell text-warning mr-2"></i>
                    Document Alerts
                  </h3>
                </div>
                <div class="card-body">
                  <div v-if="stats.expired > 0" class="alert alert-danger d-flex align-items-center mb-3">
                    <i class="fas fa-times-circle mr-3"></i>
                    <div class="flex-grow-1">
                      <h6 class="mb-1">
                        {{ stats.expired }} Document{{ stats.expired > 1 ? 's' : '' }} Expired
                      </h6>
                      <p class="mb-0 small">
                        Some documents have passed their expiry date and need immediate attention.
                      </p>
                    </div>
                    <Link :href="route('company-documents.index', { expired: true })" class="btn btn-sm btn-outline-danger">
                      View Expired
                    </Link>
                  </div>

                  <div v-if="stats.expiring_soon > 0" class="alert alert-warning d-flex align-items-center mb-0">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <div class="flex-grow-1">
                      <h6 class="mb-1">
                        {{ stats.expiring_soon }} Document{{ stats.expiring_soon > 1 ? 's' : '' }} Expiring Soon
                      </h6>
                      <p class="mb-0 small">
                        These documents will expire within the next 30 days.
                      </p>
                    </div>
                    <Link :href="route('company-documents.index', { expiring_soon: true })" class="btn btn-sm btn-outline-warning">
                      View Expiring
                    </Link>
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
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  stats: Object,
  recentDocuments: Array,
  documentsByType: Array
})
</script>