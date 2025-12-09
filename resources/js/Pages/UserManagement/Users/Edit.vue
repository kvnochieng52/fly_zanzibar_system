<template>
  <Head title="Edit User" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-user-edit icon-gradient bg-success"></i>
        </div>
        <div>
          Edit User: {{ user.name }}
          <div class="page-title-subheading">Modify user details and role assignments</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('users.index')" class="mb-2 mr-2 btn btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Users
        </Link>
      </div>
    </div>

    <!-- Form Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-user-edit icon-gradient bg-success"></i>
        Edit User Details
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <!-- Basic Information Section -->
          <div class="row">
            <div class="col-md-12">
              <h5 class="card-title">Basic Information</h5>
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name" class="form-label required">Full Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  placeholder="e.g., John Doe"
                  required
                />
                <div v-if="errors.name" class="invalid-feedback">
                  {{ errors.name }}
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="email" class="form-label required">Email Address</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': errors.email }"
                  placeholder="e.g., john@example.com"
                  required
                />
                <div v-if="errors.email" class="invalid-feedback">
                  {{ errors.email }}
                </div>
              </div>
            </div>
          </div>

          <!-- Password Section -->
          <div class="row mt-3">
            <div class="col-md-12">
              <h5 class="card-title">Security (Optional)</h5>
              <hr>
              <p class="text-muted">Leave password fields blank to keep current password</p>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': errors.password }"
                  placeholder="Enter new password (optional)"
                />
                <div v-if="errors.password" class="invalid-feedback">
                  {{ errors.password }}
                </div>
                <small class="form-text text-muted">
                  Password must be at least 8 characters long
                </small>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': errors.password_confirmation }"
                  placeholder="Confirm new password"
                />
                <div v-if="errors.password_confirmation" class="invalid-feedback">
                  {{ errors.password_confirmation }}
                </div>
              </div>
            </div>
          </div>

          <!-- Role Assignment Section -->
          <div class="row mt-4">
            <div class="col-md-12">
              <h5 class="card-title">Role Assignment</h5>
              <hr>
            </div>
          </div>

          <div class="row" v-if="roles.length > 0">
            <div class="col-md-12">
              <div class="mb-3">
                <button
                  type="button"
                  @click="selectAllRoles"
                  class="btn btn-sm btn-outline-primary mr-2"
                >
                  <i class="fa fa-check-square"></i> Select All
                </button>
                <button
                  type="button"
                  @click="deselectAllRoles"
                  class="btn btn-sm btn-outline-secondary"
                >
                  <i class="fa fa-square"></i> Deselect All
                </button>
                <span class="ml-3 text-muted">
                  <strong>{{ form.roles.length }}</strong> of {{ roles.length }} roles selected
                </span>
              </div>

              <div class="roles-grid">
                <div v-for="role in roles" :key="role.id" class="role-item">
                  <div class="form-check">
                    <input
                      :id="`role-${role.id}`"
                      v-model="form.roles"
                      :value="role.id"
                      type="checkbox"
                      class="form-check-input"
                    />
                    <label :for="`role-${role.id}`" class="form-check-label">
                      <div class="role-info">
                        <div class="role-name">{{ role.name }}</div>
                        <div class="role-permissions">
                          <small class="text-muted">
                            {{ role.permissions?.length || 0 }} permissions
                          </small>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>

              <div v-if="errors.roles" class="text-danger mt-2">
                {{ errors.roles }}
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row mt-4">
            <div class="col-md-12">
              <div class="d-flex justify-content-between">
                <Link :href="route('users.index')" class="btn btn-secondary">
                  <i class="fa fa-times"></i> Cancel
                </Link>

                <button type="submit" class="btn btn-primary" :disabled="processing">
                  <i class="fa fa-save"></i>
                  <span v-if="processing">Updating...</span>
                  <span v-else>Update User</span>
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
  name: 'UserEdit',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    user: Object,
    roles: Array,
  },

  setup(props) {
    const form = useForm({
      name: props.user.name,
      email: props.user.email,
      password: '',
      password_confirmation: '',
      roles: props.user.roles.map(role => role.id)
    })

    return {
      form,
      errors: form.errors,
      processing: form.processing
    }
  },

  methods: {
    submit() {
      this.form.put(route('users.update', this.user.id), {
        onSuccess: () => {
          // Success handled by controller redirect
        },
        onError: (errors) => {
          console.error('Form validation errors:', errors)
        }
      })
    },

    selectAllRoles() {
      this.form.roles = this.roles.map(role => role.id)
    },

    deselectAllRoles() {
      this.form.roles = []
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

.bg-success {
  background: linear-gradient(135deg, #28a745 0%, #155724 100%);
}

.form-label.required::after {
  content: ' *';
  color: #dc3545;
}

.card-title {
  color: #495057;
  font-weight: 600;
}

.roles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  max-height: 400px;
  overflow-y: auto;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  padding: 1rem;
  background-color: #f8f9fa;
}

.role-item {
  padding: 0.75rem;
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  background-color: white;
  transition: all 0.2s ease;
}

.role-item:hover {
  border-color: #007bff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-check {
  display: flex;
  align-items: flex-start;
}

.form-check-input {
  margin-top: 0.25rem;
  margin-right: 0.75rem;
}

.form-check-label {
  cursor: pointer;
  flex: 1;
}

.role-info {
  display: flex;
  flex-direction: column;
}

.role-name {
  font-weight: 500;
  color: #495057;
}

.role-permissions {
  margin-top: 0.25rem;
}

.form-check-input:checked + .form-check-label .role-name {
  color: #0056b3;
  font-weight: 600;
}
</style>