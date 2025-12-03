<template>
  <DashboardLayout>
    <div class="container-fluid">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Customers</h1>
              <p class="text-muted">Manage customer database and information</p>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <Link :href="route('dashboard')">Dashboard</Link>
                </li>
                <li class="breadcrumb-item active">Customers</li>
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
                  <p>Total Customers</p>
                </div>
                <div class="icon">
                  <i class="fas fa-users"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3>{{ stats?.active || 0 }}</h3>
                  <p>Active Customers</p>
                </div>
                <div class="icon">
                  <i class="fas fa-user-check"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>{{ stats?.individual || 0 }}</h3>
                  <p>Individual</p>
                </div>
                <div class="icon">
                  <i class="fas fa-user"></i>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-purple">
                <div class="inner">
                  <h3>{{ stats?.corporate || 0 }}</h3>
                  <p>Corporate</p>
                </div>
                <div class="icon">
                  <i class="fas fa-building"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Customers Table -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Customers List
              </h3>
              <div class="card-tools">
                <Link
                  :href="route('customers.create')"
                  class="btn btn-primary btn-sm"
                >
                  <i class="fas fa-plus mr-1"></i>
                  New Customer
                </Link>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Customer</th>
                      <th>Type</th>
                      <th>Contact</th>
                      <th>Payment Terms</th>
                      <th>Status</th>
                      <th width="150">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="customer in customers?.data || []" :key="customer.id">
                      <td>
                        <div>
                          <strong>{{ customer.company_name || customer.first_name + ' ' + customer.last_name }}</strong>
                          <br>
                          <small class="text-muted">{{ customer.customer_code }}</small>
                        </div>
                      </td>
                      <td>
                        <span class="badge" :class="customer.type === 'corporate' ? 'badge-info' : 'badge-secondary'">
                          {{ customer.type === 'corporate' ? 'Corporate' : 'Individual' }}
                        </span>
                      </td>
                      <td>
                        <div v-if="customer.email">
                          <i class="fas fa-envelope text-muted mr-1"></i>{{ customer.email }}
                        </div>
                        <div v-if="customer.phone">
                          <i class="fas fa-phone text-muted mr-1"></i>{{ customer.phone }}
                        </div>
                      </td>
                      <td>
                        <span class="badge badge-outline-secondary">{{ formatPaymentTerms(customer.payment_terms) }}</span>
                      </td>
                      <td>
                        <span
                          class="badge badge-pill"
                          :class="customer.is_active ? 'badge-success' : 'badge-danger'"
                        >
                          {{ customer.is_active ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <Link
                            :href="route('customers.show', customer.id)"
                            class="btn btn-sm btn-outline-info"
                            title="View"
                          >
                            <i class="fas fa-eye"></i>
                          </Link>
                          <Link
                            :href="route('customers.edit', customer.id)"
                            class="btn btn-sm btn-outline-primary"
                            title="Edit"
                          >
                            <i class="fas fa-edit"></i>
                          </Link>
                          <button
                            @click="deleteCustomer(customer)"
                            class="btn btn-sm btn-outline-danger"
                            title="Delete"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="!customers?.data?.length">
                      <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                        No customers found. Create your first customer to get started.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Pagination -->
            <div v-if="customers?.links" class="card-footer">
              <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                  Showing {{ customers.from || 0 }} to {{ customers.to || 0 }}
                  of {{ customers.total || 0 }} results
                </div>
                <nav>
                  <ul class="pagination mb-0">
                    <li v-for="link in customers.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
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
  customers: Object,
  stats: Object
})

const formatPaymentTerms = (terms) => {
  const termMap = {
    'immediate': 'Immediate',
    'net_7': 'Net 7 Days',
    'net_15': 'Net 15 Days',
    'net_30': 'Net 30 Days',
    'net_45': 'Net 45 Days',
    'net_60': 'Net 60 Days'
  }
  return termMap[terms] || terms
}

const deleteCustomer = (customer) => {
  const customerName = customer.company_name || `${customer.first_name} ${customer.last_name}`
  if (confirm(`Are you sure you want to delete customer ${customerName}?`)) {
    router.delete(route('customers.destroy', customer.id), {
      preserveScroll: true,
      onSuccess: () => {
        // Show success message if needed
      }
    })
  }
}
</script>