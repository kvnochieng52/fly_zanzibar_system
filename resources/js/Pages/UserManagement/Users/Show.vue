<template>
  <Head :title="`View User: ${user.name}`" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-user icon-gradient bg-info"></i>
        </div>
        <div>
          User Details: {{ user.name }}
          <div class="page-title-subheading">View user information and role assignments</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('users.edit', user.id)" class="mb-2 mr-2 btn btn-warning">
          <i class="fa fa-edit"></i> Edit User
        </Link>
        <Link :href="route('users.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Users
        </Link>
      </div>
    </div>

    <!-- User Information Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-user icon-gradient bg-info"></i>
        User Information
      </div>

      <div class="card-body">
        <!-- Basic Information Section -->
        <div class="row">
          <div class="col-md-12">
            <h5 class="card-title">Basic Information</h5>
            <hr>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="info-group">
              <label class="info-label">Full Name</label>
              <div class="info-value">{{ user.name }}</div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-group">
              <label class="info-label">Email Address</label>
              <div class="info-value">{{ user.email }}</div>
            </div>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-6">
            <div class="info-group">
              <label class="info-label">Account Created</label>
              <div class="info-value">
                {{ formatDate(user.created_at) }}
                <small class="text-muted d-block">{{ formatRelativeTime(user.created_at) }}</small>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-group">
              <label class="info-label">Last Updated</label>
              <div class="info-value">
                {{ formatDate(user.updated_at) }}
                <small class="text-muted d-block">{{ formatRelativeTime(user.updated_at) }}</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Role Assignment Section -->
        <div class="row mt-4">
          <div class="col-md-12">
            <h5 class="card-title">Role Assignments</h5>
            <hr>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div v-if="user.roles && user.roles.length > 0" class="roles-display">
              <div class="mb-3">
                <span class="badge badge-primary mr-2">
                  <i class="fa fa-user-tag"></i>
                  {{ user.roles.length }} role{{ user.roles.length !== 1 ? 's' : '' }} assigned
                </span>
              </div>

              <div class="roles-grid">
                <div v-for="role in user.roles" :key="role.id" class="role-card">
                  <div class="role-header">
                    <h6 class="role-name">{{ role.name }}</h6>
                    <span class="role-guard">{{ role.guard_name }}</span>
                  </div>
                  <div class="role-permissions">
                    <i class="fa fa-key text-muted"></i>
                    <span class="text-muted">
                      {{ role.permissions?.length || 0 }} permission{{ (role.permissions?.length || 0) !== 1 ? 's' : '' }}
                    </span>
                  </div>
                  <div v-if="role.permissions && role.permissions.length > 0" class="mt-2">
                    <small class="text-muted d-block mb-1">Permissions:</small>
                    <div class="permission-tags">
                      <span
                        v-for="permission in role.permissions"
                        :key="permission.id"
                        class="permission-tag"
                      >
                        {{ permission.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="no-roles-message">
              <div class="text-center py-4">
                <i class="fa fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <h5>No Roles Assigned</h5>
                <p class="text-muted">This user has not been assigned any roles yet.</p>
                <Link :href="route('users.edit', user.id)" class="btn btn-warning">
                  <i class="fa fa-edit"></i> Assign Roles
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- All Permissions Section -->
        <div v-if="allPermissions && allPermissions.length > 0" class="row mt-4">
          <div class="col-md-12">
            <h5 class="card-title">All Effective Permissions</h5>
            <hr>
            <p class="text-muted mb-3">
              <i class="fa fa-info-circle"></i>
              This shows all permissions this user has through their assigned roles ({{ allPermissions.length }} total).
            </p>

            <div class="permissions-display">
              <div class="permissions-by-module">
                <div v-for="(modulePermissions, module) in groupedPermissions" :key="module" class="permission-module">
                  <div class="module-header">
                    <h6 class="module-title">{{ formatModuleName(module) }}</h6>
                    <span class="permission-count">{{ modulePermissions.length }} permission{{ modulePermissions.length !== 1 ? 's' : '' }}</span>
                  </div>

                  <div class="permission-tags">
                    <span
                      v-for="permission in modulePermissions"
                      :key="permission.id"
                      class="permission-tag"
                    >
                      {{ formatPermissionName(permission.name, module) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-cogs icon-gradient bg-secondary"></i>
        Quick Actions
      </div>
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
          <Link :href="route('users.edit', user.id)" class="btn btn-warning">
            <i class="fa fa-edit"></i> Edit User
          </Link>
          <button
            @click="confirmDelete"
            class="btn btn-danger"
            :class="{ 'disabled': user.id === currentUserId }"
            :disabled="user.id === currentUserId"
          >
            <i class="fa fa-trash"></i> Delete User
          </button>
          <Link :href="route('users.index')" class="btn btn-secondary">
            <i class="fa fa-list"></i> All Users
          </Link>
        </div>
        <div v-if="user.id === currentUserId" class="mt-2">
          <small class="text-muted">
            <i class="fa fa-info-circle"></i> You cannot delete your own account.
          </small>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'UserShow',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    user: Object,
    currentUserId: Number,
  },

  computed: {
    allPermissions() {
      if (!this.user.roles) return []

      const permissions = new Map()
      this.user.roles.forEach(role => {
        if (role.permissions) {
          role.permissions.forEach(permission => {
            permissions.set(permission.id, permission)
          })
        }
      })

      return Array.from(permissions.values()).sort((a, b) => a.name.localeCompare(b.name))
    },

    groupedPermissions() {
      const groups = {}

      this.allPermissions.forEach(permission => {
        // Extract module from permission name (e.g., 'users.create' -> 'users')
        const parts = permission.name.split('.')
        const module = parts.length > 1 ? parts[0] : 'general'

        if (!groups[module]) {
          groups[module] = []
        }
        groups[module].push(permission)
      })

      // Sort groups by module name and permissions within each group
      const sortedGroups = {}
      Object.keys(groups).sort().forEach(module => {
        sortedGroups[module] = groups[module].sort((a, b) => a.name.localeCompare(b.name))
      })

      return sortedGroups
    }
  },

  methods: {
    formatDate(dateString) {
      if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    formatRelativeTime(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      const now = new Date()
      const diffTime = Math.abs(now - date)
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

      if (diffDays === 1) return '1 day ago'
      if (diffDays < 30) return `${diffDays} days ago`
      if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`
      return `${Math.floor(diffDays / 365)} years ago`
    },

    confirmDelete() {
      if (this.user.id === this.currentUserId) {
        return
      }

      if (confirm(`Are you sure you want to delete user "${this.user.name}"? This action cannot be undone.`)) {
        router.delete(route('users.destroy', this.user.id), {
          onSuccess: () => {
            // Success handled by controller redirect
          },
          onError: (errors) => {
            console.error('Delete error:', errors)
            alert('Failed to delete user. Please try again.')
          }
        })
      }
    },

    formatModuleName(module) {
      if (module === 'general') return 'General Permissions'

      // Convert module names to readable format
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
      // Remove module prefix from permission name for cleaner display
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

.bg-info {
  background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-secondary {
  background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
}

.card-title {
  color: #495057;
  font-weight: 600;
}

.info-group {
  margin-bottom: 1.5rem;
}

.info-label {
  display: block;
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-value {
  font-size: 1rem;
  color: #212529;
  padding: 0.75rem;
  background-color: #f8f9fa;
  border-radius: 0.375rem;
  border: 1px solid #e9ecef;
}

.roles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
}

.role-card {
  padding: 1.5rem;
  border: 2px solid #e9ecef;
  border-radius: 0.5rem;
  background-color: white;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.role-card:hover {
  border-color: #007bff;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  transform: translateY(-1px);
}

.role-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.role-name {
  font-weight: 600;
  color: #007bff;
  margin: 0;
  font-size: 1.1rem;
}

.role-guard {
  font-size: 0.75rem;
  color: #6c757d;
  background-color: #e9ecef;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.role-permissions {
  margin-bottom: 0.75rem;
}

.permission-tags {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.25rem;
  max-height: none;
  overflow-y: visible;
  padding: 1rem;
  background-color: #fdfdfd;
}

.permission-tag {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  background-color: #e7f3ff;
  border: 1px solid #b3d9ff;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  color: #0056b3;
  white-space: nowrap;
}

.permissions-display {
  max-height: 600px;
  overflow-y: auto;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  padding: 0;
  background-color: #f8f9fa;
}

.permissions-by-module {
  max-height: 100%;
  overflow-y: auto;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  padding: 1rem;
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

.no-roles-message {
  background-color: #fff3cd;
  border: 1px solid #ffeaa7;
  border-radius: 0.5rem;
  padding: 2rem;
  margin: 1rem 0;
}

.gap-2 {
  gap: 0.5rem;
}
</style>