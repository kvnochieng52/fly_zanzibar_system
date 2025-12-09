<template>
  <Head title="Roles Management" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-shield-alt icon-gradient bg-primary"></i>
        </div>
        <div>
          Roles Management
          <div class="page-title-subheading">Manage user roles and their permissions</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('roles.create')" class="mb-2 mr-2 btn btn-primary">
          <i class="fa fa-plus"></i> Create New Role
        </Link>
      </div>
    </div>

    <!-- Roles List Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-shield-alt icon-gradient bg-primary"></i>
        System Roles
        <div class="btn-actions-pane-right">
          <div class="badge badge-secondary">{{ roles.total }} Total</div>
        </div>
      </div>

      <div class="card-body">
        <div v-if="roles.data.length === 0" class="text-center py-5">
          <i class="fa fa-shield-alt fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">No roles found</h5>
          <p class="text-muted">Create your first role to get started</p>
          <Link :href="route('roles.create')" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create Role
          </Link>
        </div>

        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Role Name</th>
                  <th>Guard</th>
                  <th>Permissions</th>
                  <th>Users Count</th>
                  <th>Created</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="role in roles.data" :key="role.id">
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="mr-2">
                        <i class="fa fa-shield-alt text-primary"></i>
                      </div>
                      <div>
                        <div class="font-weight-bold">{{ role.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-info">{{ role.guard_name }}</span>
                  </td>
                  <td>
                    <span class="badge badge-success">{{ role.permissions.length }} permissions</span>
                    <div class="mt-1" v-if="role.permissions.length > 0">
                      <small class="text-muted">
                        {{ role.permissions.slice(0, 3).map(p => p.name).join(', ') }}
                        <span v-if="role.permissions.length > 3">
                          ... +{{ role.permissions.length - 3 }} more
                        </span>
                      </small>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-warning">{{ role.users_count || 0 }} users</span>
                  </td>
                  <td>{{ formatDate(role.created_at) }}</td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <Link
                        :href="route('roles.show', role.id)"
                        class="btn btn-sm btn-info"
                        title="View Role"
                      >
                        <i class="fa fa-eye"></i>
                      </Link>
                      <Link
                        :href="route('roles.edit', role.id)"
                        class="btn btn-sm btn-primary"
                        title="Edit Role"
                      >
                        <i class="fa fa-edit"></i>
                      </Link>
                      <button
                        @click="confirmDelete(role)"
                        class="btn btn-sm btn-danger"
                        title="Delete Role"
                        :disabled="role.users_count > 0"
                      >
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <nav v-if="roles.last_page > 1" class="mt-4">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: !roles.prev_page_url }">
                <Link class="page-link" :href="roles.prev_page_url">Previous</Link>
              </li>

              <li v-for="page in paginationPages" :key="page"
                  class="page-item" :class="{ active: page === roles.current_page }">
                <Link class="page-link" :href="`${roles.path}?page=${page}`">
                  {{ page }}
                </Link>
              </li>

              <li class="page-item" :class="{ disabled: !roles.next_page_url }">
                <Link class="page-link" :href="roles.next_page_url">Next</Link>
              </li>
            </ul>
          </nav>
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
            <p>Are you sure you want to delete the role "<strong>{{ roleToDelete?.name }}</strong>"?</p>
            <div class="alert alert-warning">
              <i class="fa fa-exclamation-triangle"></i>
              This action cannot be undone.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">
              Cancel
            </button>
            <button type="button" class="btn btn-danger" @click="deleteRole" :disabled="deleting">
              <i class="fa fa-trash"></i>
              <span v-if="deleting">Deleting...</span>
              <span v-else>Delete Role</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'RoleIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    roles: Object,
  },

  data() {
    return {
      showDeleteModal: false,
      roleToDelete: null,
      deleting: false,
    }
  },

  computed: {
    paginationPages() {
      const current = this.roles.current_page
      const last = this.roles.last_page
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
    }
  },

  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    },

    confirmDelete(role) {
      this.roleToDelete = role
      this.showDeleteModal = true
    },

    deleteRole() {
      this.deleting = true

      useForm({}).delete(route('roles.destroy', this.roleToDelete.id), {
        onSuccess: () => {
          this.showDeleteModal = false
          this.roleToDelete = null
          this.deleting = false
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

.modal {
  z-index: 1050;
}

.btn-group .btn {
  margin-right: 0;
}

.table th {
  border-top: none;
  font-weight: 600;
  color: #495057;
}
</style>