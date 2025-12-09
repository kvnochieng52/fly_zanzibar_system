<template>
  <Head title="Users Management" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="page-title-wrapper">
      <div class="page-title-heading">
        <div class="page-title-icon">
          <i class="fa fa-users icon-gradient bg-success"></i>
        </div>
        <div>
          Users Management
          <div class="page-title-subheading">Manage system users and their role assignments</div>
        </div>
      </div>
      <div class="page-title-actions">
        <Link :href="route('users.create')" class="mb-2 mr-2 btn btn-primary">
          <i class="fa fa-plus"></i> Create User
        </Link>
      </div>
    </div>

    <!-- Users List Card -->
    <div class="main-card mb-3 card">
      <div class="card-header">
        <i class="header-icon fa fa-users icon-gradient bg-success"></i>
        System Users
        <div class="btn-actions-pane-right">
          <div class="badge badge-secondary">{{ users.total }} Total</div>
        </div>
      </div>

      <div class="card-body">
        <div v-if="users.data.length === 0" class="text-center py-5">
          <i class="fa fa-users fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">No users found</h5>
          <p class="text-muted">Create your first user to get started</p>
          <Link :href="route('users.create')" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create User
          </Link>
        </div>

        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Email</th>
                  <th>Roles</th>
                  <th>Status</th>
                  <th>Joined</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users.data" :key="user.id">
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="mr-3">
                        <div class="avatar-wrapper">
                          <i class="fa fa-user-circle fa-2x text-primary"></i>
                        </div>
                      </div>
                      <div>
                        <div class="font-weight-bold">{{ user.name }}</div>
                        <small class="text-muted">ID: {{ user.id }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div>{{ user.email }}</div>
                    <div v-if="user.email_verified_at" class="text-success">
                      <i class="fa fa-check-circle"></i>
                      <small>Verified</small>
                    </div>
                    <div v-else class="text-warning">
                      <i class="fa fa-exclamation-circle"></i>
                      <small>Unverified</small>
                    </div>
                  </td>
                  <td>
                    <div v-if="user.roles?.length > 0" class="role-badges">
                      <span
                        v-for="role in user.roles.slice(0, 2)"
                        :key="role.id"
                        class="badge badge-primary mr-1"
                      >
                        {{ role.name }}
                      </span>
                      <span v-if="user.roles.length > 2" class="badge badge-secondary">
                        +{{ user.roles.length - 2 }} more
                      </span>
                    </div>
                    <span v-else class="badge badge-light">No roles</span>
                  </td>
                  <td>
                    <span class="badge badge-success">Active</span>
                  </td>
                  <td>{{ formatDate(user.created_at) }}</td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <Link
                        :href="route('users.show', user.id)"
                        class="btn btn-sm btn-info"
                        title="View User"
                      >
                        <i class="fa fa-eye"></i>
                      </Link>
                      <Link
                        :href="route('users.edit', user.id)"
                        class="btn btn-sm btn-primary"
                        title="Edit User"
                      >
                        <i class="fa fa-edit"></i>
                      </Link>
                      <button
                        @click="confirmDelete(user)"
                        class="btn btn-sm btn-danger"
                        title="Delete User"
                        :disabled="isCurrentUser(user.id)"
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
          <nav v-if="users.last_page > 1" class="mt-4">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: !users.prev_page_url }">
                <Link class="page-link" :href="users.prev_page_url">Previous</Link>
              </li>

              <li v-for="page in paginationPages" :key="page"
                  class="page-item" :class="{ active: page === users.current_page }">
                <Link class="page-link" :href="`${users.path}?page=${page}`">
                  {{ page }}
                </Link>
              </li>

              <li class="page-item" :class="{ disabled: !users.next_page_url }">
                <Link class="page-link" :href="users.next_page_url">Next</Link>
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
            <p>Are you sure you want to delete the user "<strong>{{ userToDelete?.name }}</strong>"?</p>
            <div class="alert alert-warning">
              <i class="fa fa-exclamation-triangle"></i>
              This action cannot be undone. All user data will be permanently removed.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">
              Cancel
            </button>
            <button type="button" class="btn btn-danger" @click="deleteUser" :disabled="deleting">
              <i class="fa fa-trash"></i>
              <span v-if="deleting">Deleting...</span>
              <span v-else>Delete User</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<script>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

export default {
  name: 'UserIndex',
  components: {
    Head,
    Link,
    DashboardLayout,
  },

  props: {
    users: Object,
  },

  data() {
    return {
      showDeleteModal: false,
      userToDelete: null,
      deleting: false,
    }
  },

  computed: {
    currentUser() {
      return usePage().props.auth?.user
    },

    paginationPages() {
      const current = this.users.current_page
      const last = this.users.last_page
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

    isCurrentUser(userId) {
      return this.currentUser?.id === userId
    },

    confirmDelete(user) {
      this.userToDelete = user
      this.showDeleteModal = true
    },

    deleteUser() {
      this.deleting = true

      useForm({}).delete(route('users.destroy', this.userToDelete.id), {
        onSuccess: () => {
          this.showDeleteModal = false
          this.userToDelete = null
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

.bg-success {
  background: linear-gradient(135deg, #28a745 0%, #155724 100%);
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

.avatar-wrapper {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.role-badges .badge {
  font-size: 0.75rem;
}
</style>