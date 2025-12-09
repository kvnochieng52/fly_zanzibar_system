<template>
  <Head title="Create Role" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-shield-alt icon-gradient bg-primary"></i>
        </div>
        <div>
          Create New Role
          <div class="page-title-subheading">Define a new role with specific permissions</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('roles.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Roles
        </Link>
      </div>
    </div>

    <!-- Form Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-shield-alt icon-gradient bg-primary"></i>
        Role Details
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <!-- Basic Information -->
          <div class="row">
            <div class="col-md-12">
              <h5 class="card-title">Basic Information</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name" class="form-label required">Role Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="e.g., Admin, Manager, User"
                  required
                />
                <div v-if="errors.name" class="invalid-feedback">
                  {{ errors.name }}
                </div>
                <small class="form-text text-muted">
                  Choose a descriptive name for this role
                </small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="guard_name" class="form-label">Guard Name</label>
                <input
                  id="guard_name"
                  v-model="form.guard_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.guard_name }"
                  placeholder="web"
                />
                <div v-if="errors.guard_name" class="invalid-feedback">
                  {{ errors.guard_name }}
                </div>
                <small class="form-text text-muted">
                  Leave blank for default 'web' guard
                </small>
              </div>
            </div>
          </div>

          <!-- Permissions Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Assign Permissions</h5>
              <hr>
            </div>
          </div>

          <div class="row" v-if="permissions.length > 0">
            <div class="col-md-12">
              <div class="mb-3">
                <button
                  type="button"
                  @click="selectAllPermissions"
                  class="btn btn-sm btn-outline-primary mr-2"
                >
                  <i class="fa fa-check-square"></i> Select All
                </button>
                <button
                  type="button"
                  @click="deselectAllPermissions"
                  class="btn btn-sm btn-outline-secondary"
                >
                  <i class="fa fa-square"></i> Deselect All
                </button>
              </div>

              <div class="permissions-by-module">
                <div v-for="(modulePermissions, module) in groupedPermissions" :key="module" class="permission-module">
                  <div class="module-header">
                    <h6 class="module-title">{{ formatModuleName(module) }}</h6>
                    <div class="module-actions">
                      <button
                        type="button"
                        @click="selectModulePermissions(module)"
                        class="btn btn-xs btn-outline-primary mr-1"
                      >
                        Select All
                      </button>
                      <button
                        type="button"
                        @click="deselectModulePermissions(module)"
                        class="btn btn-xs btn-outline-secondary"
                      >
                        Deselect All
                      </button>
                    </div>
                  </div>

                  <div class="permissions-grid">
                    <div v-for="permission in modulePermissions" :key="permission.id" class="permission-item">
                      <div class="form-check">
                        <input
                          :id="`permission-${permission.id}`"
                          v-model="form.permissions"
                          :value="permission.id"
                          type="checkbox"
                          class="form-check-input"
                        />
                        <label :for="`permission-${permission.id}`" class="form-check-label">
                          {{ formatPermissionName(permission.name, module) }}
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="errors.permissions" class="text-danger mt-2">
                {{ errors.permissions }}
              </div>
            </div>
          </div>

          <div v-else class="row">
            <div class="col-md-12">
              <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                No permissions available. Create permissions first to assign them to roles.
                <Link :href="route('permissions.create')" class="btn btn-sm btn-primary ml-2">
                  Create Permission
                </Link>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-between">
                <Link :href="route('roles.index')" class="btn btn-secondary">
                  <i class="fa fa-times"></i> Cancel
                </Link>

                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <i class="fa fa-save"></i>
                  <span v-if="processing">Creating...</span>
                  <span v-else>Create Role</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'RoleCreate',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    permissions: Array,
  },

  setup(props) {
    const form = useForm({
      name: '',
      guard_name: '',
      permissions: []
    })

    return {
      form,
      errors: form.errors,
      processing: form.processing
    }
  },

  computed: {
    groupedPermissions() {
      const groups = {}

      this.permissions.forEach(permission => {
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
    submit() {
      this.form.post(route('roles.store'), {
        onSuccess: () => {
          // Success handled by controller redirect
        },
        onError: (errors) => {
          console.error('Form validation errors:', errors)
        }
      })
    },

    selectAllPermissions() {
      this.form.permissions = this.permissions.map(permission => permission.id)
    },

    deselectAllPermissions() {
      this.form.permissions = []
    },

    selectModulePermissions(module) {
      const modulePermissionIds = this.groupedPermissions[module].map(permission => permission.id)

      // Add module permissions to current selection (avoiding duplicates)
      modulePermissionIds.forEach(id => {
        if (!this.form.permissions.includes(id)) {
          this.form.permissions.push(id)
        }
      })
    },

    deselectModulePermissions(module) {
      const modulePermissionIds = this.groupedPermissions[module].map(permission => permission.id)

      // Remove module permissions from current selection
      this.form.permissions = this.form.permissions.filter(id => !modulePermissionIds.includes(id))
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

.bg-primary {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.form-label.required::after {
  content: ' *';
  color: #dc3545;
}

.card-title {
  color: #495057;
  font-weight: 600;
}

.permissions-by-module {
  max-height: 600px;
  overflow-y: auto;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.permission-module {
  border: 1px solid #dee2e6;
  border-radius: 0.5rem;
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin: 0;
  height: fit-content;
}

.module-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  border-radius: 0.5rem 0.5rem 0 0;
}

.module-title {
  margin: 0;
  font-weight: 600;
  color: #495057;
  font-size: 1.1rem;
}

.module-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-xs {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  line-height: 1.2;
}

.permissions-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  padding: 1rem;
  background-color: #fdfdfd;
}

.permission-item {
  padding: 0.25rem 0;
}

.form-check-label {
  font-size: 0.9rem;
  cursor: pointer;
}

.form-check-input:checked + .form-check-label {
  font-weight: 500;
  color: #0056b3;
}
</style>