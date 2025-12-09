<template>
  <Head title="Create Permission" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-key icon-gradient bg-warning"></i>
        </div>
        <div>
          Create New Permission
          <div class="page-title-subheading">Define a new system permission</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('permissions.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Permissions
        </Link>
      </div>
    </div>

    <!-- Form Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-key icon-gradient bg-warning"></i>
        Permission Details
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name" class="form-label required">Permission Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="e.g., users.view, posts.create"
                  required
                />
                <div v-if="errors.name" class="invalid-feedback">
                  {{ errors.name }}
                </div>
                <small class="form-text text-muted">
                  Use dot notation: resource.action (e.g., users.view, posts.create)
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

          <!-- Common Permission Patterns -->
          <div class="row">
            <div class="col-md-12">
              <h6 class="mb-3">Common Permission Patterns</h6>
              <div class="row">
                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="card-title text-primary">User Management</h6>
                      <div class="form-check">
                        <input @click="setPermission('users.view')" type="radio" name="pattern" class="form-check-input" id="users-view">
                        <label for="users-view" class="form-check-label">users.view</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('users.create')" type="radio" name="pattern" class="form-check-input" id="users-create">
                        <label for="users-create" class="form-check-label">users.create</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('users.edit')" type="radio" name="pattern" class="form-check-input" id="users-edit">
                        <label for="users-edit" class="form-check-label">users.edit</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('users.delete')" type="radio" name="pattern" class="form-check-input" id="users-delete">
                        <label for="users-delete" class="form-check-label">users.delete</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="card-title text-success">Staff Management</h6>
                      <div class="form-check">
                        <input @click="setPermission('staff.view')" type="radio" name="pattern" class="form-check-input" id="staff-view">
                        <label for="staff-view" class="form-check-label">staff.view</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('staff.create')" type="radio" name="pattern" class="form-check-input" id="staff-create">
                        <label for="staff-create" class="form-check-label">staff.create</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('staff.edit')" type="radio" name="pattern" class="form-check-input" id="staff-edit">
                        <label for="staff-edit" class="form-check-label">staff.edit</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('staff.delete')" type="radio" name="pattern" class="form-check-input" id="staff-delete">
                        <label for="staff-delete" class="form-check-label">staff.delete</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="card-title text-info">Aircraft Management</h6>
                      <div class="form-check">
                        <input @click="setPermission('aircraft.view')" type="radio" name="pattern" class="form-check-input" id="aircraft-view">
                        <label for="aircraft-view" class="form-check-label">aircraft.view</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('aircraft.create')" type="radio" name="pattern" class="form-check-input" id="aircraft-create">
                        <label for="aircraft-create" class="form-check-label">aircraft.create</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('aircraft.edit')" type="radio" name="pattern" class="form-check-input" id="aircraft-edit">
                        <label for="aircraft-edit" class="form-check-label">aircraft.edit</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('aircraft.delete')" type="radio" name="pattern" class="form-check-input" id="aircraft-delete">
                        <label for="aircraft-delete" class="form-check-label">aircraft.delete</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="card-title text-warning">System Admin</h6>
                      <div class="form-check">
                        <input @click="setPermission('admin.dashboard')" type="radio" name="pattern" class="form-check-input" id="admin-dashboard">
                        <label for="admin-dashboard" class="form-check-label">admin.dashboard</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('admin.settings')" type="radio" name="pattern" class="form-check-input" id="admin-settings">
                        <label for="admin-settings" class="form-check-label">admin.settings</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('admin.reports')" type="radio" name="pattern" class="form-check-input" id="admin-reports">
                        <label for="admin-reports" class="form-check-label">admin.reports</label>
                      </div>
                      <div class="form-check">
                        <input @click="setPermission('admin.backup')" type="radio" name="pattern" class="form-check-input" id="admin-backup">
                        <label for="admin-backup" class="form-check-label">admin.backup</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-between">
                <Link :href="route('permissions.index')" class="btn btn-secondary">
                  <i class="fa fa-times"></i> Cancel
                </Link>

                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <i class="fa fa-save"></i>
                  <span v-if="processing">Creating...</span>
                  <span v-else>Create Permission</span>
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
  name: 'PermissionCreate',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  setup() {
    const form = useForm({
      name: '',
      guard_name: '',
      description: '',
      category: ''
    })

    return {
      form,
      errors: form.errors,
      processing: form.processing
    }
  },

  methods: {
    submit() {
      this.form.post(route('permissions.store'), {
        onSuccess: () => {
          // Success handled by controller redirect
        },
        onError: (errors) => {
          console.error('Form validation errors:', errors)
        }
      })
    },

    setPermission(permissionName) {
      this.form.name = permissionName
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
  background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
}

.form-label.required::after {
  content: ' *';
  color: #dc3545;
}

.form-check-input {
  cursor: pointer;
}

.form-check-label {
  cursor: pointer;
  font-size: 0.9rem;
}

.card.bg-light .card-body {
  padding: 1rem 0.75rem;
}

.card.bg-light .card-title {
  font-size: 0.9rem;
  margin-bottom: 0.75rem;
}
</style>