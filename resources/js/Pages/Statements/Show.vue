<template>
  <Head title="Customer Statement" />

  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Customer Statement</h1>
              <p class="text-muted">{{ getCustomerName() }} - {{ formatDateRange() }}</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item">
                  <Link :href="route('statements.index')">Statements</Link>
                </li>
                <li class="breadcrumb-item active">View Statement</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Loading State -->
          <div v-if="!statement" class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
              </div>
              <p class="mt-3 text-muted">Loading statement data...</p>
            </div>
          </div>

          <!-- Statement Content -->
          <div v-else>
          <!-- Statement Actions -->
          <div class="row mb-3">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="mb-1">{{ getCustomerName() }}</h5>
                      <p class="text-muted mb-0">
                        Statement Period: {{ formatDate(statement?.period?.start_date) }} to {{ formatDate(statement?.period?.end_date) }}
                      </p>
                    </div>
                    <div class="btn-group">
                      <Link
                        :href="route('statements.index')"
                        class="btn btn-outline-secondary"
                      >
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Statements
                      </Link>
                      <button class="btn btn-outline-primary" @click="printStatement">
                        <i class="fas fa-print mr-1"></i>
                        Print
                      </button>
                      <a
                        :href="getDownloadUrl()"
                        class="btn btn-primary"
                        target="_blank"
                        :class="{ disabled: !statement?.customer?.id }"
                      >
                        <i class="fas fa-download mr-1"></i>
                        Download PDF
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Statement Summary -->
          <div class="row mb-3">
            <div class="col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-info">
                  <i class="fas fa-file-invoice"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Invoices</span>
                  <span class="info-box-number">{{ getInvoiceCount() }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-primary">
                  <i class="fas fa-dollar-sign"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Total Amount</span>
                  <span class="info-box-number">${{ formatCurrency(statement?.summary?.total_invoiced) }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-success">
                  <i class="fas fa-check-circle"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Paid Amount</span>
                  <span class="info-box-number">${{ formatCurrency(statement?.summary?.total_paid) }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-warning">
                  <i class="fas fa-clock"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Outstanding</span>
                  <span class="info-box-number">${{ formatCurrency(statement?.summary?.outstanding_balance) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Statement Details -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Statement Details
              </h3>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Type</th>
                      <th>Reference</th>
                      <th>Description</th>
                      <th class="text-right">Debit</th>
                      <th class="text-right">Credit</th>
                      <th class="text-right">Balance</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Opening Balance -->
                    <tr class="table-info">
                      <td>{{ formatDate(statement?.period?.start_date) }}</td>
                      <td><strong>Opening Balance</strong></td>
                      <td>-</td>
                      <td>Balance brought forward</td>
                      <td class="text-right">-</td>
                      <td class="text-right">-</td>
                      <td class="text-right">
                        <strong>${{ formatCurrency(statement?.summary?.opening_balance) }}</strong>
                      </td>
                    </tr>

                    <!-- Transactions -->
                    <tr v-for="(transaction, index) in statement?.transactions || []" :key="`${transaction.type}-${index}`">
                      <td>{{ formatDate(transaction.date) }}</td>
                      <td>
                        <span class="badge" :class="getTransactionBadgeClass(transaction.type)">
                          {{ getTransactionLabel(transaction.type) }}
                        </span>
                      </td>
                      <td>{{ transaction.reference }}</td>
                      <td>{{ transaction.description }}</td>
                      <td class="text-right">
                        <span v-if="transaction.debit" class="text-danger">
                          ${{ formatCurrency(transaction.debit) }}
                        </span>
                        <span v-else>-</span>
                      </td>
                      <td class="text-right">
                        <span v-if="transaction.credit" class="text-success">
                          ${{ formatCurrency(transaction.credit) }}
                        </span>
                        <span v-else>-</span>
                      </td>
                      <td class="text-right">
                        <strong
                          :class="{
                            'text-success': transaction.running_balance >= 0,
                            'text-danger': transaction.running_balance < 0
                          }"
                        >
                          ${{ formatCurrency(transaction.running_balance) }}
                        </strong>
                      </td>
                    </tr>

                    <!-- Closing Balance -->
                    <tr class="table-warning">
                      <td>{{ formatDate(statement?.period?.end_date) }}</td>
                      <td><strong>Closing Balance</strong></td>
                      <td>-</td>
                      <td>Balance carried forward</td>
                      <td class="text-right">-</td>
                      <td class="text-right">-</td>
                      <td class="text-right">
                        <strong
                          :class="{
                            'text-success': getClosingBalance() >= 0,
                            'text-danger': getClosingBalance() < 0
                          }"
                        >
                          ${{ formatCurrency(getClosingBalance()) }}
                        </strong>
                      </td>
                    </tr>

                    <tr v-if="!statement?.transactions?.length">
                      <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-file-alt fa-3x mb-3 d-block"></i>
                        No transactions found for the selected period.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          </div> <!-- End of Statement Content -->
        </div>
      </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  statement: Object
})

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatCurrency = (amount) => {
  if (!amount && amount !== 0) return '0.00'
  return parseFloat(amount).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDateRange = () => {
  if (!props.statement?.period?.start_date || !props.statement?.period?.end_date) return ''
  return `${formatDate(props.statement.period.start_date)} - ${formatDate(props.statement.period.end_date)}`
}

const getCustomerName = () => {
  if (!props.statement?.customer) return ''
  const customer = props.statement.customer
  return customer.company_name || `${customer.first_name} ${customer.last_name}`
}

const getInvoiceCount = () => {
  if (!props.statement?.transactions) return 0
  return props.statement.transactions.filter(t => t.type === 'invoice').length
}

const getClosingBalance = () => {
  const summary = props.statement?.summary
  if (!summary) return 0
  return summary.opening_balance + summary.total_invoiced - summary.total_paid
}

const getTransactionLabel = (type) => {
  const labels = {
    'invoice': 'Invoice',
    'payment': 'Payment',
    'credit': 'Credit',
    'adjustment': 'Adjustment'
  }
  return labels[type] || type
}

const getTransactionBadgeClass = (type) => {
  const classes = {
    'invoice': 'badge-warning',
    'payment': 'badge-success',
    'credit': 'badge-info',
    'adjustment': 'badge-secondary'
  }
  return classes[type] || 'badge-secondary'
}

const printStatement = () => {
  window.print()
}

const getDownloadUrl = () => {
  if (!props.statement?.customer?.id) return '#'

  const baseUrl = route('statements.download', props.statement.customer.id)
  const params = []

  if (props.statement?.period?.start_date) {
    params.push(`start_date=${props.statement.period.start_date}`)
  }

  if (props.statement?.period?.end_date) {
    params.push(`end_date=${props.statement.period.end_date}`)
  }

  return params.length > 0 ? `${baseUrl}?${params.join('&')}` : baseUrl
}
</script>

<style scoped>
@media print {
  .content-header,
  .breadcrumb,
  .btn,
  .card-header {
    display: none !important;
  }

  .card {
    border: none !important;
    box-shadow: none !important;
  }

  .table {
    font-size: 12px;
  }
}
</style>