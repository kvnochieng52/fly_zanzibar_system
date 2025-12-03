<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Quotations</h1>
              <p class="text-muted">Manage flight quotations and pricing</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item active">Quotations</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Success Message -->
          <div v-if="$page.props.flash && $page.props.flash.success" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ $page.props.flash.success }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <!-- Quick Stats -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>{{ stats?.total || 0 }}</h3>
                  <p>Total Quotations</p>
                </div>
                <div class="icon">
                  <i class="fas fa-file-invoice"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ stats?.accepted || 0 }}</h3>
                  <p>Accepted Quotes</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>{{ stats?.pending || 0 }}</h3>
                  <p>Pending Quotes</p>
                </div>
                <div class="icon">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>{{ stats?.expired || 0 }}</h3>
                  <p>Expired Quotes</p>
                </div>
                <div class="icon">
                  <i class="fas fa-times-circle"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Quotations Table -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Quotations List
              </h3>
              <div class="card-tools">
                <Link
                  :href="route('quotations.create')"
                  class="btn btn-primary btn-sm"
                >
                  <i class="fas fa-plus mr-1"></i>
                  New Quotation
                </Link>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Quote #</th>
                      <th>Customer</th>
                      <th>Route</th>
                      <th>Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Valid Until</th>
                      <th width="150">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="quotation in quotations?.data || []" :key="quotation.id">
                      <td>
                        <strong>{{ quotation.quote_number }}</strong>
                      </td>
                      <td>
                        <div>
                          {{ quotation.customer?.company_name || quotation.customer?.first_name + ' ' + quotation.customer?.last_name }}
                        </div>
                      </td>
                      <td>
                        <div v-if="quotation.departure_airport && quotation.arrival_airport">
                          {{ quotation.departure_airport }} → {{ quotation.arrival_airport }}
                        </div>
                        <small v-if="quotation.route_description" class="text-muted">
                          {{ quotation.route_description }}
                        </small>
                      </td>
                      <td>{{ formatDate(quotation.quote_date) }}</td>
                      <td>
                        <strong>{{ formatCurrency(quotation.total_amount, quotation.currency?.symbol) }}</strong>
                      </td>
                      <td>
                        <span
                          class="badge badge-pill"
                          :style="{ backgroundColor: quotation.status?.color || '#6c757d', color: '#fff' }"
                        >
                          {{ quotation.status?.name }}
                        </span>
                      </td>
                      <td>
                        <div>{{ formatDate(quotation.valid_until) }}</div>
                        <small
                          :class="{
                            'text-danger': isExpired(quotation.valid_until),
                            'text-warning': isExpiringSoon(quotation.valid_until),
                            'text-success': !isExpired(quotation.valid_until) && !isExpiringSoon(quotation.valid_until)
                          }"
                        >
                          {{ getValidityText(quotation.valid_until) }}
                        </small>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <Link
                            :href="route('quotations.show', quotation.id)"
                            class="btn btn-sm btn-outline-info"
                            title="View"
                          >
                            <i class="fas fa-eye"></i>
                          </Link>
                          <Link
                            :href="route('quotations.edit', quotation.id)"
                            class="btn btn-sm btn-outline-primary"
                            title="Edit"
                          >
                            <i class="fas fa-edit"></i>
                          </Link>
                          <button
                            @click="deleteQuotation(quotation)"
                            class="btn btn-sm btn-outline-danger"
                            title="Delete"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="!quotations?.data?.length">
                      <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
                        No quotations found. Create your first quotation to get started.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Pagination -->
            <div v-if="quotations?.links" class="card-footer">
              <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                  Showing {{ quotations.from || 0 }} to {{ quotations.to || 0 }}
                  of {{ quotations.total || 0 }} results
                </div>
                <nav>
                  <ul class="pagination mb-0">
                    <li v-for="link in quotations.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                      <Link
                        v-if="link.url"
                        :href="link.url"
                        class="page-link"
                        v-html="link.label"
                      ></Link>
                      <span v-else class="page-link" v-html="link.label"></span>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  quotations: Object,
  stats: Object
})

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}

const formatCurrency = (amount, symbol = '$') => {
  if (!amount) return symbol + '0.00'
  return symbol + parseFloat(amount).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const isExpired = (date) => {
  if (!date) return false
  return new Date(date) < new Date()
}

const isExpiringSoon = (date) => {
  if (!date || isExpired(date)) return false
  const daysUntilExpiry = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  return daysUntilExpiry <= 7
}

const getValidityText = (date) => {
  if (!date) return ''
  if (isExpired(date)) return 'Expired'

  const daysUntilExpiry = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  if (daysUntilExpiry <= 7) return `${daysUntilExpiry} days left`
  return 'Valid'
}

const deleteQuotation = (quotation) => {
  if (confirm(`Are you sure you want to delete quotation ${quotation.quote_number}?`)) {
    router.delete(route('quotations.destroy', quotation.id), {
      preserveScroll: true,
      onSuccess: () => {
        // Show success message if needed
      }
    })
  }
}
</script>