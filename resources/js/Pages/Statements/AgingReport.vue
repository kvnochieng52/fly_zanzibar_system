<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Aging Report</h1>
              <p class="text-muted">Customer accounts aging analysis</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item">
                  <Link :href="route('statements.index')">Statements</Link>
                </li>
                <li class="breadcrumb-item active">Aging Report</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Filter Controls -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Report Options
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>As of Date:</label>
                    <input
                      type="date"
                      class="form-control"
                      v-model="asOfDate"
                      @change="refreshReport"
                    >
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button
                      @click="refreshReport"
                      class="btn btn-primary btn-block"
                    >
                      <i class="fas fa-refresh mr-1"></i>
                      Refresh Report
                    </button>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button
                      @click="exportReport"
                      class="btn btn-outline-success btn-block"
                    >
                      <i class="fas fa-download mr-1"></i>
                      Export to Excel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Summary Cards -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3>${{ formatNumber(summaryTotals.current) }}</h3>
                  <p>Current (Not Due)</p>
                </div>
                <div class="icon">
                  <i class="fas fa-clock"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>${{ formatNumber(summaryTotals.days_1_30) }}</h3>
                  <p>1-30 Days</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-orange">
                <div class="inner">
                  <h3>${{ formatNumber(summaryTotals.days_31_60) }}</h3>
                  <p>31-60 Days</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>${{ formatNumber(summaryTotals.over_60) }}</h3>
                  <p>Over 60 Days</p>
                </div>
                <div class="icon">
                  <i class="fas fa-ban"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Aging Report Table -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-table mr-2"></i>
                Customer Aging Details
              </h3>
              <div class="card-tools">
                <span class="badge badge-info">
                  As of: {{ formatDate(asOfDate) }}
                </span>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Customer</th>
                      <th class="text-right">Current</th>
                      <th class="text-right">1-30 Days</th>
                      <th class="text-right">31-60 Days</th>
                      <th class="text-right">61-90 Days</th>
                      <th class="text-right">Over 90 Days</th>
                      <th class="text-right">Total Outstanding</th>
                      <th width="100">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="customer in agingReport" :key="customer.id">
                      <td>
                        <div>
                          <strong>
                            {{ customer.company_name || `${customer.first_name} ${customer.last_name}` }}
                          </strong>
                          <div class="text-muted small">
                            {{ customer.type === 'corporate' ? 'Corporate' : 'Individual' }}
                          </div>
                        </div>
                      </td>
                      <td class="text-right">
                        <span :class="{ 'text-success': customer.aging_analysis.current > 0 }">
                          ${{ formatNumber(customer.aging_analysis.current) }}
                        </span>
                      </td>
                      <td class="text-right">
                        <span :class="{ 'text-warning': customer.aging_analysis['1_30_days'] > 0 }">
                          ${{ formatNumber(customer.aging_analysis['1_30_days']) }}
                        </span>
                      </td>
                      <td class="text-right">
                        <span :class="{ 'text-orange': customer.aging_analysis['31_60_days'] > 0 }">
                          ${{ formatNumber(customer.aging_analysis['31_60_days']) }}
                        </span>
                      </td>
                      <td class="text-right">
                        <span :class="{ 'text-danger': customer.aging_analysis['61_90_days'] > 0 }">
                          ${{ formatNumber(customer.aging_analysis['61_90_days']) }}
                        </span>
                      </td>
                      <td class="text-right">
                        <span :class="{ 'text-danger': customer.aging_analysis.over_90_days > 0 }">
                          ${{ formatNumber(customer.aging_analysis.over_90_days) }}
                        </span>
                      </td>
                      <td class="text-right">
                        <strong :class="getOutstandingClass(customer.total_outstanding)">
                          ${{ formatNumber(customer.total_outstanding) }}
                        </strong>
                      </td>
                      <td>
                        <Link
                          :href="route('statements.show', customer.id)"
                          class="btn btn-sm btn-outline-primary"
                          title="View Statement"
                        >
                          <i class="fas fa-file-alt"></i>
                        </Link>
                      </td>
                    </tr>
                    <tr v-if="!agingReport?.length">
                      <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-chart-bar fa-3x mb-3 d-block"></i>
                        No outstanding balances found.
                      </td>
                    </tr>
                  </tbody>
                  <!-- Totals Row -->
                  <tfoot v-if="agingReport?.length" class="bg-light">
                    <tr>
                      <th>TOTALS</th>
                      <th class="text-right">
                        <strong>${{ formatNumber(summaryTotals.current) }}</strong>
                      </th>
                      <th class="text-right">
                        <strong>${{ formatNumber(summaryTotals.days_1_30) }}</strong>
                      </th>
                      <th class="text-right">
                        <strong>${{ formatNumber(summaryTotals.days_31_60) }}</strong>
                      </th>
                      <th class="text-right">
                        <strong>${{ formatNumber(summaryTotals.days_61_90) }}</strong>
                      </th>
                      <th class="text-right">
                        <strong>${{ formatNumber(summaryTotals.over_90) }}</strong>
                      </th>
                      <th class="text-right">
                        <strong class="text-primary">
                          ${{ formatNumber(summaryTotals.total) }}
                        </strong>
                      </th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  agingReport: Array,
  asOfDate: String
})

const asOfDate = ref(props.asOfDate)

const summaryTotals = computed(() => {
  if (!props.agingReport || !props.agingReport.length) {
    return {
      current: 0,
      days_1_30: 0,
      days_31_60: 0,
      days_61_90: 0,
      over_90: 0,
      total: 0
    }
  }

  return props.agingReport.reduce((totals, customer) => {
    const aging = customer.aging_analysis
    return {
      current: totals.current + aging.current,
      days_1_30: totals.days_1_30 + aging['1_30_days'],
      days_31_60: totals.days_31_60 + aging['31_60_days'],
      days_61_90: totals.days_61_90 + aging['61_90_days'],
      over_90: totals.over_90 + aging.over_90_days,
      total: totals.total + customer.total_outstanding
    }
  }, {
    current: 0,
    days_1_30: 0,
    days_31_60: 0,
    days_61_90: 0,
    over_90: 0,
    total: 0
  })
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

const getOutstandingClass = (amount) => {
  if (amount > 10000) return 'text-danger'
  if (amount > 5000) return 'text-warning'
  if (amount > 1000) return 'text-info'
  return 'text-success'
}

const refreshReport = () => {
  const params = new URLSearchParams({
    as_of_date: asOfDate.value
  })

  router.get(`${route('statements.aging-report')}?${params.toString()}`, {}, {
    preserveState: false
  })
}

const exportReport = () => {
  // This would typically trigger a download of an Excel file
  alert('Export functionality would be implemented here to generate an Excel file of the aging report.')
}
</script>

<style scoped>
.text-orange {
  color: #fd7e14 !important;
}

.bg-orange {
  background-color: #fd7e14 !important;
}
</style>