<template>
  <Head title="Record Payment" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">
          Record Payment
        </h1>
        <p class="mb-0 text-muted">Create a new payment receipt for customer invoices</p>
      </div>
      <Link :href="route('receipts.index')" class="btn btn-outline-secondary">
        Back to Receipts
      </Link>
    </div>

    <!-- Form Card -->
    <form @submit.prevent="submitForm" class="needs-validation" novalidate>
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="row g-3">
            <!-- Payment Details Section -->
            <div class="col-12 mb-3">
              <h5 class="card-title mb-3">Payment Details</h5>
            </div>

            <!-- Invoice -->
            <div class="col-md-6">
              <label for="invoice_id" class="form-label required">Invoice</label>
              <select
                v-model="form.invoice_id"
                class="form-select"
                :class="{ 'is-invalid': errors.invoice_id }"
                id="invoice_id"
                @change="selectInvoice"
                required
              >
                <option value="">Select Invoice</option>
                <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">
                  {{ invoice.invoice_number }} -
                  {{ invoice.customer?.company_name || `${invoice.customer?.first_name} ${invoice.customer?.last_name}` }}
                  (Outstanding: ${{ formatNumber(invoice.outstanding_balance) }})
                </option>
              </select>
              <div v-if="errors.invoice_id" class="invalid-feedback">
                {{ errors.invoice_id }}
              </div>
            </div>

            <!-- Payment Date -->
            <div class="col-md-6">
              <label for="date" class="form-label required">Payment Date</label>
              <input
                v-model="form.date"
                type="date"
                class="form-control"
                :class="{ 'is-invalid': errors.date }"
                id="date"
                required
              >
              <div v-if="errors.date" class="invalid-feedback">
                {{ errors.date }}
              </div>
            </div>

            <!-- Selected Invoice Info (if invoice is selected) -->
            <div v-if="selectedInvoice" class="col-12">
              <div class="alert alert-info">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="mb-0">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Invoice: {{ selectedInvoice.invoice_number }}
                  </h6>
                  <a
                    :href="route('invoices.download-pdf', selectedInvoice.id)"
                    class="btn btn-outline-primary btn-sm"
                    target="_blank"
                  >
                    <i class="fas fa-download mr-1"></i>
                    Download Invoice PDF
                  </a>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <strong>Customer:</strong><br>
                    {{ selectedInvoice.customer?.company_name || `${selectedInvoice.customer?.first_name} ${selectedInvoice.customer?.last_name}` }}
                  </div>
                  <div class="col-md-3">
                    <strong>Total Amount:</strong><br>
                    ${{ formatNumber(selectedInvoice.total_amount) }}
                  </div>
                  <div class="col-md-3">
                    <strong>Paid Amount:</strong><br>
                    ${{ formatNumber(selectedInvoice.paid_amount) }}
                  </div>
                  <div class="col-md-3">
                    <strong>Outstanding Balance:</strong><br>
                    <span class="text-danger font-weight-bold">
                      ${{ formatNumber(selectedInvoice.outstanding_balance) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Amount -->
            <div class="col-md-6">
              <label for="payment_amount" class="form-label required">Payment Amount</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input
                  v-model="form.payment_amount"
                  type="number"
                  step="0.01"
                  min="0.01"
                  :max="selectedInvoice?.outstanding_balance"
                  class="form-control"
                  :class="{ 'is-invalid': errors.payment_amount }"
                  id="payment_amount"
                  placeholder="0.00"
                  required
                >
              </div>
              <div v-if="errors.payment_amount" class="invalid-feedback">
                {{ errors.payment_amount }}
              </div>
              <small v-if="selectedInvoice" class="text-muted">
                Maximum: ${{ formatNumber(selectedInvoice.outstanding_balance) }}
              </small>
            </div>

            <!-- Payment Method -->
            <div class="col-md-6">
              <label for="payment_method_id" class="form-label required">Payment Method</label>
              <select
                v-model="form.payment_method_id"
                class="form-select"
                :class="{ 'is-invalid': errors.payment_method_id }"
                id="payment_method_id"
                required
              >
                <option value="">Select Payment Method</option>
                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                  {{ method.name }}
                </option>
              </select>
              <div v-if="errors.payment_method_id" class="invalid-feedback">
                {{ errors.payment_method_id }}
              </div>
            </div>

            <!-- Transaction Reference -->
            <div class="col-md-6">
              <label for="transaction_reference" class="form-label">Transaction Reference</label>
              <input
                v-model="form.transaction_reference"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.transaction_reference }"
                id="transaction_reference"
                placeholder="e.g., Check #1234, Transfer ID, etc."
              >
              <div v-if="errors.transaction_reference" class="invalid-feedback">
                {{ errors.transaction_reference }}
              </div>
              <small class="text-muted">Optional reference number or ID for this payment</small>
            </div>

            <!-- Received By -->
            <div class="col-md-6">
              <label for="received_by" class="form-label required">Received By</label>
              <input
                v-model="form.received_by"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.received_by }"
                id="received_by"
                placeholder="Person who received the payment"
                required
              >
              <div v-if="errors.received_by" class="invalid-feedback">
                {{ errors.received_by }}
              </div>
            </div>

            <!-- Notes -->
            <div class="col-12">
              <label for="notes" class="form-label">Notes</label>
              <textarea
                v-model="form.notes"
                class="form-control"
                :class="{ 'is-invalid': errors.notes }"
                id="notes"
                rows="3"
                placeholder="Additional notes about this payment..."
              ></textarea>
              <div v-if="errors.notes" class="invalid-feedback">
                {{ errors.notes }}
              </div>
            </div>
          </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-transparent">
          <div class="d-flex justify-content-end gap-2">
            <Link
              :href="route('receipts.index')"
              class="btn btn-outline-secondary"
            >
              Cancel
            </Link>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="processing"
            >
              <i class="fas fa-save mr-1"></i>
              <span v-if="processing">
                <i class="fas fa-spinner fa-spin mr-1"></i>
                Saving...
              </span>
              <span v-else>
                Record Payment
              </span>
            </button>
          </div>
        </div>
      </div>
    </form>
  </DashboardLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps({
  invoices: Array,
  paymentMethods: Array,
  errors: Object,
  preselectedInvoiceId: [String, Number]
})

const processing = ref(false)

const form = reactive({
  date: new Date().toISOString().split('T')[0], // Today's date
  invoice_id: props.preselectedInvoiceId || '',
  payment_amount: '',
  payment_method_id: '',
  transaction_reference: '',
  received_by: '',
  notes: ''
})

const selectedInvoice = computed(() => {
  if (!form.invoice_id) return null
  return props.invoices.find(invoice => invoice.id == form.invoice_id)
})

const selectInvoice = () => {
  if (selectedInvoice.value) {
    // Auto-fill payment amount with outstanding balance
    form.payment_amount = selectedInvoice.value.outstanding_balance
  }
}

const formatNumber = (number) => {
  return parseFloat(number || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const submitForm = () => {
  processing.value = true

  router.post(route('receipts.store'), form, {
    onSuccess: (page) => {
      // Receipt creation was successful - redirect to index with success message
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    },
    onError: () => {
      processing.value = false
    }
  })
}

// Auto-select and auto-fill if preselected invoice is provided
onMounted(() => {
  if (props.preselectedInvoiceId && form.invoice_id) {
    selectInvoice()
  }
})
</script>

<style scoped>
.required::after {
  content: " *";
  color: #dc3545;
}

.form-control:focus,
.form-select:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>