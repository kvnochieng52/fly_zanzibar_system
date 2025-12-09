<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Invoices</h1>
              <p class="text-muted">Manage invoices and payment tracking</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item active">Invoices</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Quick Stats -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>{{ stats?.total || 0 }}</h3>
                  <p>Total Invoices</p>
                </div>
                <div class="icon">
                  <i class="fas fa-file-invoice-dollar"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ stats?.paid || 0 }}</h3>
                  <p>Paid Invoices</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check-circle"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>{{ stats?.unpaid || 0 }}</h3>
                  <p>Unpaid Invoices</p>
                </div>
                <div class="icon">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>{{ stats?.overdue || 0 }}</h3>
                  <p>Overdue Invoices</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Financial Summary -->
          <div class="row">
            <div class="col-md-4">
              <div class="info-box">
                <span class="info-box-icon bg-primary">
                  <i class="fas fa-dollar-sign"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Amount</span>
                  <span class="info-box-number">${{ formatNumber(stats?.total_amount || 0) }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-box">
                <span class="info-box-icon bg-success">
                  <i class="fas fa-check"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Paid Amount</span>
                  <span class="info-box-number">${{ formatNumber(stats?.paid_amount || 0) }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-box">
                <span class="info-box-icon bg-warning">
                  <i class="fas fa-hourglass-half"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Outstanding</span>
                  <span class="info-box-number">${{ formatNumber(stats?.outstanding_amount || 0) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Invoices Table -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Invoices List
              </h3>
              <div class="card-tools">
                <Link
                  :href="route('invoices.create')"
                  class="btn btn-primary btn-sm"
                >
                  <i class="fas fa-plus mr-1"></i>
                  New Invoice
                </Link>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Invoice #</th>
                      <th>Customer</th>
                      <th>Date</th>
                      <th>Due Date</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Outstanding</th>
                      <th width="150">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="invoice in invoices?.data || []" :key="invoice.id">
                      <td>
                        <strong>{{ invoice.invoice_number }}</strong>
                      </td>
                      <td>
                        <div>
                          {{ invoice.customer?.company_name || invoice.customer?.first_name + ' ' + invoice.customer?.last_name }}
                        </div>
                      </td>
                      <td>{{ formatDate(invoice.invoice_date) }}</td>
                      <td>
                        <div>{{ formatDate(invoice.due_date) }}</div>
                        <small
                          :class="{
                            'text-danger': isOverdue(invoice.due_date),
                            'text-warning': isDueSoon(invoice.due_date),
                            'text-success': !isOverdue(invoice.due_date) && !isDueSoon(invoice.due_date)
                          }"
                        >
                          {{ getDueDateText(invoice.due_date) }}
                        </small>
                      </td>
                      <td>
                        <strong>{{ formatCurrency(invoice.total_amount, invoice.currency?.symbol) }}</strong>
                      </td>
                      <td>
                        <span
                          class="badge badge-pill"
                          :class="getStatusBadgeClass(invoice.payment_status)"
                        >
                          {{ invoice.payment_status?.replace('_', ' ') }}
                        </span>
                      </td>
                      <td>
                        <strong
                          :class="{
                            'text-danger': invoice.outstanding_balance > 0,
                            'text-success': invoice.outstanding_balance === 0
                          }"
                        >
                          {{ formatCurrency(invoice.outstanding_balance, invoice.currency?.symbol) }}
                        </strong>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <Link
                            :href="route('invoices.show', invoice.id)"
                            class="btn btn-sm btn-outline-info"
                            title="View"
                          >
                            <i class="fas fa-eye"></i>
                          </Link>
                          <a
                            :href="route('invoices.download-pdf', invoice.id)"
                            class="btn btn-sm btn-outline-secondary"
                            title="Download PDF"
                            target="_blank"
                          >
                            <i class="fas fa-file-pdf"></i>
                          </a>
                          <Link
                            :href="route('invoices.edit', invoice.id)"
                            class="btn btn-sm btn-outline-primary"
                            title="Edit"
                          >
                            <i class="fas fa-edit"></i>
                          </Link>
                          <button
                            class="btn btn-sm btn-outline-success"
                            title="Record Payment"
                            @click="recordPayment(invoice)"
                          >
                            <i class="fas fa-dollar-sign"></i>
                          </button>
                          <button
                            @click="deleteInvoice(invoice)"
                            class="btn btn-sm btn-outline-danger"
                            title="Delete"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="!invoices?.data?.length">
                      <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-file-invoice-dollar fa-3x mb-3 d-block"></i>
                        No invoices found. Create your first invoice to get started.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Pagination -->
            <div v-if="invoices?.links" class="card-footer">
              <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                  Showing {{ invoices.from || 0 }} to {{ invoices.to || 0 }}
                  of {{ invoices.total || 0 }} results
                </div>
                <nav>
                  <ul class="pagination mb-0">
                    <li v-for="link in invoices.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
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
  invoices: Object,
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

const formatNumber = (number) => {
  return parseFloat(number || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const isOverdue = (date) => {
  if (!date) return false
  return new Date(date) < new Date()
}

const isDueSoon = (date) => {
  if (!date || isOverdue(date)) return false
  const daysUntilDue = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  return daysUntilDue <= 7
}

const getDueDateText = (date) => {
  if (!date) return ''
  if (isOverdue(date)) return 'Overdue'

  const daysUntilDue = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
  if (daysUntilDue <= 7) return `${daysUntilDue} days left`
  return 'On time'
}

const getStatusBadgeClass = (status) => {
  const statusClasses = {
    'unpaid': 'badge-warning',
    'partially_paid': 'badge-info',
    'paid': 'badge-success',
    'overdue': 'badge-danger'
  }
  return statusClasses[status] || 'badge-secondary'
}

const recordPayment = (invoice) => {
  // Navigate to receipts creation page
  router.visit(route('receipts.create'), {
    data: { invoice_id: invoice.id },
    method: 'get'
  })
}

const deleteInvoice = (invoice) => {
  if (confirm(`Are you sure you want to delete invoice ${invoice.invoice_number}?`)) {
    router.delete(route('invoices.destroy', invoice.id), {
      preserveScroll: true,
      onSuccess: () => {
        // Show success message if needed
      }
    })
  }
}
</script>