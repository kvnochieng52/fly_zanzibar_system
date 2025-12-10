<template>
  <Head title="Flight Routes" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Flight Routes</h1>
        <p class="mb-0 text-muted">
          Manage reusable flight routes between airports
        </p>
      </div>
      <Link
        :href="route('flight-routes.create')"
        class="btn btn-primary"
      >
        <i class="fas fa-plus me-2"></i>Add New Route
      </Link>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Search Routes</label>
            <input
              v-model="form.search"
              type="text"
              class="form-control"
              placeholder="Route name, code, or airport..."
              @input="search"
            />
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select v-model="form.is_active" class="form-select" @change="search">
              <option value="">All</option>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button @click="clearFilters" class="btn btn-outline-secondary">
              <i class="fas fa-times me-1"></i>Clear
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Routes Table -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
          <i class="fas fa-route me-2 text-primary"></i>Flight Routes
          <span class="badge bg-secondary ms-2">{{ routes?.total || 0 }} total</span>
        </h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Route Name</th>
                <th>Code</th>
                <th>Origin → Destination</th>
                <th>Distance</th>
                <th>Duration</th>
                <th>Base Price</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="routes.data && routes.data.length === 0">
                <td colspan="8" class="text-center py-4 text-muted">
                  <i class="fas fa-route fa-2x mb-3 d-block"></i>
                  No flight routes found
                </td>
              </tr>
              <tr v-for="route in routes.data" :key="route.id">
                <td>
                  <Link
                    :href="route('flight-routes.show', route.id)"
                    class="fw-bold text-primary text-decoration-none"
                  >
                    {{ route.name }}
                  </Link>
                </td>
                <td>
                  <span class="badge bg-info">{{ route.code }}</span>
                </td>
                <td>
                  <div>
                    <span class="fw-medium">{{ route.origin_airport?.code }} → {{ route.destination_airport?.code }}</span>
                    <br>
                    <small class="text-muted">
                      {{ route.origin_airport?.city }} to {{ route.destination_airport?.city }}
                    </small>
                  </div>
                </td>
                <td>
                  {{ route.distance_km ? route.distance_km + ' km' : 'N/A' }}
                </td>
                <td>
                  {{ route.duration_minutes ? Math.floor(route.duration_minutes / 60) + 'h ' + (route.duration_minutes % 60) + 'm' : 'N/A' }}
                </td>
                <td>
                  {{ route.base_price ? '$' + Number(route.base_price).toFixed(2) : 'N/A' }}
                </td>
                <td>
                  <span
                    class="badge"
                    :class="route.is_active ? 'bg-success' : 'bg-secondary'"
                  >
                    {{ route.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <Link
                      :href="route('flight-routes.show', route.id)"
                      class="btn btn-sm btn-outline-primary"
                      title="View Route"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="route('flight-routes.edit', route.id)"
                      class="btn btn-sm btn-outline-warning"
                      title="Edit Route"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="confirmDelete(route)"
                      class="btn btn-sm btn-outline-danger"
                      title="Delete Route"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="routes.last_page > 1" class="card-footer bg-white">
          <nav aria-label="Routes pagination">
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item" :class="{ disabled: !routes.prev_page_url }">
                <Link
                  class="page-link"
                  :href="routes.prev_page_url || '#'"
                  :only="['routes']"
                  :preserve-state="true"
                >
                  Previous
                </Link>
              </li>

              <li
                v-for="page in paginationPages"
                :key="page"
                class="page-item"
                :class="{ active: page === routes.current_page }"
              >
                <Link
                  v-if="page !== '...'"
                  class="page-link"
                  :href="route('flight-routes.index', { ...filters, page })"
                  :only="['routes']"
                  :preserve-state="true"
                >
                  {{ page }}
                </Link>
                <span v-else class="page-link">...</span>
              </li>

              <li class="page-item" :class="{ disabled: !routes.next_page_url }">
                <Link
                  class="page-link"
                  :href="routes.next_page_url || '#'"
                  :only="['routes']"
                  :preserve-state="true"
                >
                  Next
                </Link>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="modal fade show"
      style="display: block"
      tabindex="-1"
      @click="cancelDelete"
    >
      <div class="modal-dialog" @click.stop>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button
              type="button"
              class="btn-close"
              @click="cancelDelete"
            ></button>
          </div>
          <div class="modal-body">
            <p>
              Are you sure you want to delete route
              <strong>{{ routeToDelete?.name }}</strong>?
            </p>
            <p class="text-muted small mb-0">This action cannot be undone.</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              @click="cancelDelete"
              :disabled="deleting"
            >
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-danger"
              @click="deleteRoute"
              :disabled="deleting"
            >
              <span v-if="deleting">
                <i class="fas fa-spinner fa-spin me-1"></i>
                Deleting...
              </span>
              <span v-else>Delete Route</span>
            </button>
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
  name: 'FlightRouteIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },
  props: {
    routes: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters?.search || '',
        is_active: this.filters?.is_active || '',
      },
      showDeleteModal: false,
      routeToDelete: null,
      deleting: false,
      searchTimeout: null,
    }
  },
  computed: {
    paginationPages() {
      if (!this.routes || !this.routes.current_page || !this.routes.last_page) {
        return []
      }

      const current = this.routes.current_page
      const last = this.routes.last_page
      const delta = 2
      const range = []

      for (let i = Math.max(2, current - delta);
           i <= Math.min(last - 1, current + delta);
           i++) {
        range.push(i)
      }

      if (current - delta > 2) {
        range.unshift('...')
      }
      if (current + delta < last - 1) {
        range.push('...')
      }

      range.unshift(1)
      if (last > 1) {
        range.push(last)
      }

      return range
    }
  },
  methods: {
    search() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }

      this.searchTimeout = setTimeout(() => {
        router.get(route('flight-routes.index'), this.form, {
          preserveState: true,
          preserveScroll: true,
          only: ['routes']
        })
      }, 300)
    },
    clearFilters() {
      this.form = {
        search: '',
        is_active: '',
      }
      this.search()
    },
    confirmDelete(route) {
      this.routeToDelete = route
      this.showDeleteModal = true
    },
    cancelDelete() {
      this.showDeleteModal = false
      this.routeToDelete = null
      this.deleting = false
    },
    deleteRoute() {
      if (!this.routeToDelete) return

      this.deleting = true
      router.delete(route('flight-routes.destroy', this.routeToDelete.id), {
        onSuccess: () => {
          this.cancelDelete()
        },
        onError: () => {
          this.deleting = false
        }
      })
    }
  }
}
</script>

<style scoped>
.table th {
  font-weight: 600;
  background-color: #f8f9fa !important;
}

.modal.fade.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.badge {
  font-size: 0.75rem;
}
</style>