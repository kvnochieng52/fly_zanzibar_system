<template>
  <Head title="Landing Fee Details" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="lnr-airplane icon-gradient bg-plum-plate"></i>
        </div>
        <div>
          Landing Fee Details
          <div class="page-title-subheading">View landing fee record for {{ landingFee.flight_number }}</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('landing-fees.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to List
        </Link>
        <Link :href="route('landing-fees.edit', landingFee.id)" class="mb-2 mr-2 btn btn-primary">
          <i class="fa fa-edit"></i> Edit
        </Link>
      </div>
    </div>

    <!-- Main Content -->
    <div class="row">
      <!-- Landing Fee Details -->
      <div class="col-lg-8">
        <div class="main-card mb-3 card">
          <div class="card-header">
            <i class="header-icon lnr-airplane icon-gradient bg-plum-plate"></i>
            Flight Information
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <td class="font-weight-bold">Flight Number:</td>
                    <td>{{ landingFee.flight_number }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Flight Date:</td>
                    <td>{{ formatDate(landingFee.flight_date) }}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Aircraft:</td>
                    <td>
                      <span class="badge badge-info">{{ landingFee.aircraft?.registration_number }}</span>
                      <br>
                      <small class="text-muted">{{ landingFee.aircraft?.manufacturer?.name }} {{ landingFee.aircraft?.model?.name }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Airport:</td>
                    <td>
                      <strong>{{ landingFee.airport?.icao_code }}</strong> - {{ landingFee.airport?.name }}
                      <br>
                      <small class="text-muted">{{ landingFee.airport?.city }}, {{ landingFee.airport?.country }}</small>
                    </td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <td class="font-weight-bold">MTOW Used:</td>
                    <td>{{ formatNumber(landingFee.mtow_used) }} kg</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Fee Amount:</td>
                    <td class="h5 text-primary">
                      {{ landingFee.currency?.symbol }}{{ formatNumber(landingFee.fee_amount) }}
                    </td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold">Calculation:</td>
                    <td>
                      <span :class="landingFee.is_calculated_auto ? 'badge badge-success' : 'badge badge-warning'">
                        {{ landingFee.is_calculated_auto ? 'Auto-calculated' : 'Manual Override' }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="landingFee.manual_override_amount">
                    <td class="font-weight-bold">Override Amount:</td>
                    <td>{{ landingFee.currency?.symbol }}{{ formatNumber(landingFee.manual_override_amount) }}</td>
                  </tr>
                  <tr v-if="landingFee.override_reason">
                    <td class="font-weight-bold">Override Reason:</td>
                    <td>{{ landingFee.override_reason }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment & Status -->
      <div class="col-lg-4">
        <div class="main-card mb-3 card">
          <div class="card-header">
            <i class="header-icon fa fa-credit-card"></i>
            Payment Status
          </div>
          <div class="card-body text-center">
            <div class="mb-3">
              <span :class="getPaymentStatusClass(landingFee.payment_status?.name)" class="badge badge-lg">
                {{ landingFee.payment_status?.name }}
              </span>
            </div>

            <div v-if="landingFee.payment_date" class="mb-2">
              <small class="text-muted">Paid on:</small><br>
              <strong>{{ formatDate(landingFee.payment_date) }}</strong>
            </div>

            <div v-if="landingFee.receipt_reference" class="mb-2">
              <small class="text-muted">Receipt:</small><br>
              <strong>{{ landingFee.receipt_reference }}</strong>
            </div>

            <div v-if="landingFee.invoice_reference" class="mb-2">
              <small class="text-muted">Invoice:</small><br>
              <strong>{{ landingFee.invoice_reference }}</strong>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="main-card mb-3 card">
          <div class="card-header">
            <i class="header-icon fa fa-cogs"></i>
            Quick Actions
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <Link :href="route('landing-fees.edit', landingFee.id)" class="btn btn-primary btn-sm">
                <i class="fa fa-edit"></i> Edit Record
              </Link>
              <button @click="printRecord" class="btn btn-info btn-sm">
                <i class="fa fa-print"></i> Print Receipt
              </button>
              <button @click="exportRecord" class="btn btn-success btn-sm">
                <i class="fa fa-download"></i> Export PDF
              </button>
              <button @click="confirmDelete" class="btn btn-danger btn-sm">
                <i class="fa fa-trash"></i> Delete Record
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Information -->
    <div class="row">
      <div class="col-12">
        <div class="main-card mb-3 card">
          <div class="card-header">
            <i class="header-icon fa fa-info-circle"></i>
            Additional Information
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6 class="card-subtitle mb-2">Authority Document</h6>
                <p class="card-text">
                  {{ landingFee.authority_document || 'No authority document specified' }}
                </p>
              </div>
              <div class="col-md-6">
                <h6 class="card-subtitle mb-2">Notes</h6>
                <p class="card-text">
                  {{ landingFee.notes || 'No additional notes' }}
                </p>
              </div>
            </div>

            <hr>

            <!-- Audit Trail -->
            <div class="row">
              <div class="col-md-6">
                <small class="text-muted">
                  <strong>Created:</strong> {{ formatDateTime(landingFee.created_at) }}
                  <span v-if="landingFee.creator">by {{ landingFee.creator.name }}</span>
                </small>
              </div>
              <div class="col-md-6">
                <small class="text-muted" v-if="landingFee.updated_at !== landingFee.created_at">
                  <strong>Last Updated:</strong> {{ formatDateTime(landingFee.updated_at) }}
                  <span v-if="landingFee.updater">by {{ landingFee.updater.name }}</span>
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this landing fee record?</p>
            <p class="font-weight-bold">
              Flight: {{ landingFee.flight_number }} - {{ landingFee.airport?.name }}
            </p>
            <div class="alert alert-warning">
              <i class="fa fa-warning"></i> This action cannot be undone.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button @click="deleteRecord" class="btn btn-danger">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'LandingFeeShow',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    landingFee: Object,
  },

  methods: {
    formatDate(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: '2-digit'
      })
    },

    formatDateTime(date) {
      if (!date) return ''
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    formatNumber(number) {
      return new Intl.NumberFormat().format(number)
    },

    getPaymentStatusClass(status) {
      const statusClasses = {
        'paid': 'badge-success',
        'pending': 'badge-warning',
        'overdue': 'badge-danger',
        'partial': 'badge-info'
      }
      return statusClasses[status?.toLowerCase()] || 'badge-secondary'
    },

    confirmDelete() {
      $('#deleteModal').modal('show')
    },

    deleteRecord() {
      router.delete(route('landing-fees.destroy', this.landingFee.id), {
        onSuccess: () => {
          $('#deleteModal').modal('hide')
          router.visit(route('landing-fees.index'))
        }
      })
    },

    printRecord() {
      window.print()
    },

    exportRecord() {
      // Implement PDF export functionality
      alert('Export functionality will be implemented soon')
    }
  }
}
</script>

<style scoped>
.page-title-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title-heading {
  display: flex;
  align-items: center;
}

.page-title-icon {
  margin-right: 1rem;
  padding: 0.75rem;
  border-radius: 0.5rem;
}

.page-title-icon i {
  font-size: 1.5rem;
  color: white;
}

.bg-plum-plate {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.badge-lg {
  font-size: 1rem;
  padding: 0.5rem 1rem;
}

.table td {
  padding: 0.5rem 0;
  border: none;
}

.d-grid {
  display: grid;
}

.gap-2 {
  gap: 0.5rem;
}

@media print {
  .page-title-actions,
  .card:has(.fa-cogs) {
    display: none;
  }
}
</style>