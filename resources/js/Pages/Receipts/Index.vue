<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Payment Receipts</h1>
              <p class="text-muted">Manage payment receipts and transaction records</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item active">Receipts</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Receipts Table -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-receipt mr-2"></i>
                Payment Receipts
              </h3>
              <div class="card-tools">
                <Link
                  :href="route('receipts.create')"
                  class="btn btn-primary btn-sm"
                >
                  <i class="fas fa-plus mr-1"></i>
                  Record Payment
                </Link>
              </div>
            </div>

            <!-- Search and Filter -->
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Search receipts..."
                      v-model="searchForm.search"
                      @input="searchReceipts"
                    >
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select
                      class="form-control"
                      v-model="searchForm.customer_id"
                      @change="searchReceipts"
                    >
                      <option value="">All Customers</option>
                      <option
                        v-for="customer in customers"
                        :key="customer.id"
                        :value="customer.id"
                      >
                        {{ customer.company_name || `${customer.first_name} ${customer.last_name}` }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select
                      class="form-control"
                      v-model="searchForm.payment_method_id"
                      @change="searchReceipts"
                    >
                      <option value="">All Payment Methods</option>
                      <option
                        v-for="method in paymentMethods"
                        :key="method.id"
                        :value="method.id"
                      >
                        {{ method.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <button
                    @click="clearFilters"
                    class="btn btn-outline-secondary btn-block"
                  >
                    Clear Filters
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Receipt #</th>
                      <th>Date</th>
                      <th>Customer</th>
                      <th>Invoice #</th>
                      <th>Amount</th>
                      <th>Payment Method</th>
                      <th>Transaction Ref</th>
                      <th width="150">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="receipt in receipts?.data || []" :key="receipt.id">
                      <td>
                        <strong>{{ receipt.receipt_number }}</strong>
                      </td>
                      <td>{{ formatDate(receipt.date) }}</td>
                      <td>
                        <div>
                          {{ receipt.customer?.company_name ||
                             `${receipt.customer?.first_name} ${receipt.customer?.last_name}` }}
                        </div>
                      </td>
                      <td>
                        <Link
                          :href="route('invoices.show', receipt.invoice?.id)"
                          class="text-primary"
                        >
                          {{ receipt.invoice?.invoice_number }}
                        </Link>
                      </td>
                      <td>
                        <strong class="text-success">
                          ${{ formatNumber(receipt.payment_amount) }}
                        </strong>
                      </td>
                      <td>
                        <span class="badge badge-info">
                          {{ receipt.payment_method?.name }}
                        </span>
                      </td>
                      <td>
                        <small class="text-muted">
                          {{ receipt.transaction_reference || 'N/A' }}
                        </small>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <Link
                            :href="route('receipts.show', receipt.id)"
                            class="btn btn-sm btn-outline-info"
                            title="View"
                          >
                            <i class="fas fa-eye"></i>
                          </Link>
                          <a
                            :href="route('receipts.download-pdf', receipt.id)"
                            class="btn btn-sm btn-outline-success"
                            title="Download PDF"
                            target="_blank"
                          >
                            <i class="fas fa-file-pdf"></i>
                          </a>
                          <Link
                            :href="route('receipts.edit', receipt.id)"
                            class="btn btn-sm btn-outline-primary"
                            title="Edit"
                          >
                            <i class="fas fa-edit"></i>
                          </Link>
                          <button
                            @click="deleteReceipt(receipt)"
                            class="btn btn-sm btn-outline-danger"
                            title="Delete"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="!receipts?.data?.length">
                      <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-receipt fa-3x mb-3 d-block"></i>
                        No receipts found.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="receipts?.links" class="card-footer">
              <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                  Showing {{ receipts.from || 0 }} to {{ receipts.to || 0 }}
                  of {{ receipts.total || 0 }} results
                </div>
                <nav>
                  <ul class="pagination mb-0">
                    <li v-for="link in receipts.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                      <Link
                        v-if="link.url"
                        :href="link.url"
                        class="page-link"
                        v-html="link.label"
                        preserve-state
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
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  receipts: Object,
  customers: Array,
  paymentMethods: Array,
  filters: Object
})

const searchForm = reactive({
  search: props.filters?.search || '',
  customer_id: props.filters?.customer_id || '',
  payment_method_id: props.filters?.payment_method_id || ''
})

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}

const formatNumber = (number) => {
  return parseFloat(number || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const searchReceipts = () => {
  router.get(route('receipts.index'), searchForm, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  searchForm.search = ''
  searchForm.customer_id = ''
  searchForm.payment_method_id = ''
  searchReceipts()
}

const deleteReceipt = (receipt) => {
  if (confirm(`Are you sure you want to delete receipt ${receipt.receipt_number}? This will reverse the payment on the associated invoice.`)) {
    router.delete(route('receipts.destroy', receipt.id), {
      preserveScroll: true,
      onSuccess: () => {
        // Show success message if needed
      }
    })
  }
}
</script>