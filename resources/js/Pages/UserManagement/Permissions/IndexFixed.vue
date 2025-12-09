<template>
  <Head title="Permissions Management" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-key icon-gradient bg-warning"></i>
        </div>
        <div>
          Permissions Management
          <div class="page-title-subheading">Manage system permissions and access control</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('permissions.create')" class="mb-2 mr-2 btn btn-primary">
          <i class="fa fa-plus"></i> Create Permission
        </Link>
      </div>
    </div>

    <!-- Permissions List Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-key icon-gradient bg-warning"></i>
        Permissions
        <div class="btn-actions-pane-right">
          <div class="btn-group mr-3" role="group">
            <button
              @click="viewMode = 'table'"
              class="btn btn-sm"
              :class="viewMode === 'table' ? 'btn-primary' : 'btn-outline-primary'"
            >
              <i class="fa fa-list"></i> Table
            </button>
            <button
              @click="viewMode = 'grouped'"
              class="btn btn-sm"
              :class="viewMode === 'grouped' ? 'btn-primary' : 'btn-outline-primary'"
            >
              <i class="fa fa-layer-group"></i> Grouped
            </button>
          </div>
          <div class="badge badge-secondary">{{ permissions?.total || 0 }} Total</div>
        </div>
      </div>

      <div class="card-body">
        <div v-if="!permissions?.data || permissions.data.length === 0" class="text-center py-5">
          <i class="fa fa-key fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">No permissions found</h5>
          <p class="text-muted">Create your first permission to get started</p>
          <Link :href="route('permissions.create')" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create Permission
          </Link>
        </div>

        <div v-else>
          <!-- Table View -->
          <div v-if="viewMode === 'table'" class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Permission Name</th>
                  <th>Category</th>
                  <th>Guard</th>
                  <th>Assigned Roles</th>
                  <th>Created</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="permission in (permissions?.data || [])" :key="permission.id">
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="mr-2">
                        <i class="fa fa-key text-warning"></i>
                      </div>
                      <div>
                        <div class="font-weight-bold">{{ permission.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-info">{{ getPermissionCategory(permission.name) }}</span>
                  </td>
                  <td>{{ permission.guard_name }}</td>
                  <td>
                    <span class="badge badge-success">{{ permission.roles?.length || 0 }} roles</span>
                  </td>
                  <td>{{ formatDate(permission.created_at) }}</td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <Link
                        v-if="permission.id"
                        :href="route('permissions.show', permission.id)"
                        class="btn btn-sm btn-info"
                        title="View Permission"
                      >
                        <i class="fa fa-eye"></i>
                      </Link>
                      <Link
                        v-if="permission.id"
                        :href="route('permissions.edit', permission.id)"
                        class="btn btn-sm btn-primary"
                        title="Edit Permission"
                      >
                        <i class="fa fa-edit"></i>
                      </Link>
                      <button
                        v-if="permission.id"
                        @click="confirmDelete(permission)"
                        class="btn btn-sm btn-danger"
                        title="Delete Permission"
                        :disabled="permission.roles?.length > 0"
                      >
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination for Table View -->
          <nav v-if="permissions?.last_page > 1 && viewMode === 'table'" class="mt-4">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: !permissions.prev_page_url }">
                <Link class="page-link" :href="permissions.prev_page_url">Previous</Link>
              </li>

              <li v-for="page in paginationPages" :key="page"
                  class="page-item" :class="{ active: page === permissions.current_page }">
                <Link class="page-link" :href="`${permissions.path}?page=${page}`">
                  {{ page }}
                </Link>
              </li>

              <li class="page-item" :class="{ disabled: !permissions.next_page_url }">
                <Link class="page-link" :href="permissions.next_page_url">Next</Link>
              </li>
            </ul>
          </nav>

          <!-- Grouped View -->
          <div v-if="viewMode === 'grouped'" class="permissions-grouped-view">
            <div class="permissions-by-module">
              <div v-for="(modulePermissions, module) in groupedAllPermissions" :key="module" class="permission-module">
                <div class="module-header">
                  <h6 class="module-title">{{ formatModuleName(module) }}</h6>
                  <span class="permission-count">{{ modulePermissions.length }} permission{{ modulePermissions.length !== 1 ? 's' : '' }}</span>
                </div>

                <div class="permissions-list">
                  <div v-for="permission in modulePermissions" :key="permission.id" class="permission-item-card">
                    <div class="permission-info">
                      <div class="permission-name">
                        <i class="fa fa-key text-warning mr-2"></i>
                        {{ formatPermissionName(permission.name, module) }}
                      </div>
                      <div class="permission-details">
                        <span class="badge badge-info mr-1">{{ permission.guard_name }}</span>
                        <span class="badge badge-success">{{ permission.roles?.length || 0 }} roles</span>
                      </div>
                      <div class="permission-actions mt-2">
                        <Link
                          v-if="permission.id"
                          :href="route('permissions.show', permission.id)"
                          class="btn btn-xs btn-info mr-1"
                          title="View Permission"
                        >
                          <i class="fa fa-eye"></i>
                        </Link>
                        <Link
                          v-if="permission.id"
                          :href="route('permissions.edit', permission.id)"
                          class="btn btn-xs btn-warning mr-1"
                          title="Edit Permission"
                        >
                          <i class="fa fa-edit"></i>
                        </Link>
                        <button
                          v-if="permission.id"
                          @click="confirmDelete(permission)"
                          class="btn btn-xs btn-danger"
                          title="Delete Permission"
                        >
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal d-block" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the permission "<strong>{{ permissionToDelete?.name }}</strong>"?</p>
            <div class="alert alert-warning">
              <i class="fa fa-exclamation-triangle"></i>
              This action cannot be undone.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">
              Cancel
            </button>
            <button type="button" class="btn btn-danger" @click="deletePermission" :disabled="deleting">
              <span v-if="deleting">Deleting...</span>
              <span v-else>Delete</span>
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
  name: 'PermissionIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    permissions: Object,
  },

  data() {
    return {
      viewMode: 'table',
      showDeleteModal: false,
      permissionToDelete: null,
      deleting: false
    }
  },

  computed: {
    paginationPages() {
      if (!this.permissions || !this.permissions.current_page || !this.permissions.last_page) {
        return []
      }

      const current = this.permissions.current_page
      const last = this.permissions.last_page
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
      if (last !== 1) range.push(last)

      return range
    },

    groupedAllPermissions() {
      if (!this.permissions || !this.permissions.data || !Array.isArray(this.permissions.data)) {
        return {}
      }

      const groups = {}

      this.permissions.data.forEach(permission => {
        if (!permission || !permission.name) return

        const parts = permission.name.split('.')
        const module = parts.length > 1 ? parts[0] : 'general'

        if (!groups[module]) {
          groups[module] = []
        }
        groups[module].push(permission)
      })

      const sortedGroups = {}
      Object.keys(groups).sort().forEach(module => {
        sortedGroups[module] = groups[module].sort((a, b) => a.name.localeCompare(b.name))
      })

      return sortedGroups
    }
  },

  methods: {
    formatDate(date) {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },

    getPermissionCategory(permissionName) {
      if (!permissionName) return 'general'
      const parts = permissionName.split('.')
      return parts[0] || 'general'
    },

    confirmDelete(permission) {
      if (!permission || !permission.id) {
        return
      }
      this.permissionToDelete = permission
      this.showDeleteModal = true
    },

    deletePermission() {
      if (!this.permissionToDelete || !this.permissionToDelete.id) {
        this.showDeleteModal = false
        this.deleting = false
        return
      }

      this.deleting = true

      router.delete(route('permissions.destroy', this.permissionToDelete.id), {
        onSuccess: () => {
          this.showDeleteModal = false
          this.permissionToDelete = null
          this.deleting = false
        },
        onError: () => {
          this.deleting = false
        }
      })
    },

    formatModuleName(module) {
      if (module === 'general') return 'General Permissions'

      const moduleNames = {
        'users': 'User Management',
        'roles': 'Role Management',
        'permissions': 'Permission Management',
        'invoices': 'Invoice Management',
        'customers': 'Customer Management',
        'aircraft': 'Aircraft Management',
        'staff': 'Staff Management',
        'quotations': 'Quotation Management',
        'receipts': 'Receipt Management',
        'statements': 'Statement Management',
        'landing_fees': 'Landing Fees',
        'navigation_fees': 'Navigation Fees',
        'fuel_consumption': 'Fuel Consumption',
        'fuel_purchases': 'Fuel Purchases',
        'reports': 'Reports & Analytics'
      }

      return moduleNames[module] || module.charAt(0).toUpperCase() + module.slice(1).replace(/_/g, ' ')
    },

    formatPermissionName(permissionName, module) {
      const parts = permissionName.split('.')
      if (parts.length > 1 && parts[0] === module) {
        return parts.slice(1).join('.').charAt(0).toUpperCase() + parts.slice(1).join('.').slice(1)
      }
      return permissionName
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

.bg-warning {
  background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
}

.permissions-grouped-view {
  padding: 1rem 0;
}

.permissions-by-module {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.permission-module {
  border: 1px solid #dee2e6;
  border-radius: 0.5rem;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  margin: 0;
  height: fit-content;
}

.module-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  border-radius: 0.5rem 0.5rem 0 0;
}

.module-title {
  margin: 0;
  font-weight: 600;
  color: #495057;
  font-size: 1rem;
}

.permission-count {
  font-size: 0.75rem;
  color: #6c757d;
  background-color: #e9ecef;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
}

.permissions-list {
  padding: 1rem;
  background-color: #fdfdfd;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
}

.permission-item-card {
  border: 1px solid #e9ecef;
  border-radius: 0.375rem;
  padding: 0.75rem;
  background-color: white;
  transition: all 0.2s ease;
}

.permission-item-card:hover {
  border-color: #007bff;
  box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
}

.permission-name {
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.5rem;
}

.permission-details {
  margin-bottom: 0.5rem;
}

.btn-xs {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  line-height: 1.2;
}

.permission-actions .btn {
  margin-right: 0.25rem;
}
</style>