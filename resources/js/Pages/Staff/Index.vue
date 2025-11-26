<template>
  <Head title="Staff Management" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Staff Management</h1>
        <p class="mb-0 text-muted">
          Manage your crew members and staff personnel
        </p>
      </div>
      <button
        @click="handleAddStaff"
        class="btn btn-primary"
        type="button"
      >
        Add New Staff
      </button>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-card-primary">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="stat-content">
                <h6 class="stat-title mb-1">Total Staff</h6>
                <h2 class="stat-value mb-0">{{ staff.total }}</h2>
              </div>
              <div class="stat-icon-wrapper">
                <i class="fas fa-users stat-icon"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-card-success">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="stat-content">
                <h6 class="stat-title mb-1">Active Staff</h6>
                <h2 class="stat-value mb-0">{{ activeCount }}</h2>
              </div>
              <div class="stat-icon-wrapper">
                <i class="fas fa-user-check stat-icon"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-card-info">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="stat-content">
                <h6 class="stat-title mb-1">Crew Members</h6>
                <h2 class="stat-value mb-0">{{ crewCount }}</h2>
              </div>
              <div class="stat-icon-wrapper">
                <i class="fas fa-plane stat-icon"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100 stat-card stat-card-warning">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
              <div class="stat-content">
                <h6 class="stat-title mb-1">Departments</h6>
                <h2 class="stat-value mb-0">{{ departments.length }}</h2>
              </div>
              <div class="stat-icon-wrapper">
                <i class="fas fa-building stat-icon"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4 filter-card">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <h6 class="card-title mb-0 me-3">
            <i class="fas fa-filter text-primary me-2"></i>Filter Staff
          </h6>
          <div v-if="hasActiveFilters" class="ms-auto">
            <button
              @click="clearFilters"
              class="btn btn-sm btn-outline-danger clear-filters-btn"
            >
              <i class="fas fa-times me-1"></i>Clear All
            </button>
          </div>
        </div>

        <div class="row g-3">
          <!-- Search -->
          <div class="col-md-4">
            <label class="filter-label mb-2">Search</label>
            <div class="input-group custom-input-group">
              <span class="input-group-text">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                v-model="searchForm.search"
                type="text"
                class="form-control custom-input"
                placeholder="Search by name, ID, or email..."
                @input="debounceSearch"
              />
            </div>
          </div>

          <!-- Department Filter -->
          <div class="col-md-3">
            <label class="filter-label mb-2">Department</label>
            <select
              v-model="searchForm.department"
              class="form-select custom-select"
              @change="applyFilters"
            >
              <option value="">All Departments</option>
              <option
                v-for="dept in departments"
                :key="dept.id"
                :value="dept.id"
              >
                {{ dept.name }}
              </option>
            </select>
          </div>

          <!-- Status Filter -->
          <div class="col-md-3">
            <label class="filter-label mb-2">Status</label>
            <select
              v-model="searchForm.status"
              class="form-select custom-select"
              @change="applyFilters"
            >
              <option value="">All Statuses</option>
              <option
                v-for="status in statuses"
                :key="status.id"
                :value="status.id"
              >
                {{ status.name }}
              </option>
            </select>
          </div>

          <!-- Type Filter -->
          <div class="col-md-2">
            <label class="filter-label mb-2">Type</label>
            <select
              v-model="searchForm.type"
              class="form-select custom-select"
              @change="applyFilters"
            >
              <option value="">All Types</option>
              <option value="staff">Staff</option>
              <option value="crew">Crew</option>
            </select>
          </div>
        </div>

        <!-- Active Filters Display -->
        <div v-if="hasActiveFilters" class="mt-4 pt-3 border-top">
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <small class="text-muted fw-semibold me-2">Active Filters:</small>
            <span
              v-if="searchForm.search"
              class="badge bg-primary filter-badge"
            >
              Search: "{{ searchForm.search }}"
              <i
                class="fas fa-times ms-1 cursor-pointer"
                @click="
                  searchForm.search = '';
                  applyFilters();
                "
              ></i>
            </span>
            <span
              v-if="searchForm.department"
              class="badge bg-info filter-badge"
            >
              Dept:
              {{ departments.find((d) => d.id == searchForm.department)?.name }}
              <i
                class="fas fa-times ms-1 cursor-pointer"
                @click="
                  searchForm.department = '';
                  applyFilters();
                "
              ></i>
            </span>
            <span
              v-if="searchForm.status"
              class="badge bg-success filter-badge"
            >
              Status:
              {{ statuses.find((s) => s.id == searchForm.status)?.name }}
              <i
                class="fas fa-times ms-1 cursor-pointer"
                @click="
                  searchForm.status = '';
                  applyFilters();
                "
              ></i>
            </span>
            <span
              v-if="searchForm.type"
              class="badge bg-warning text-dark filter-badge"
            >
              Type: {{ searchForm.type }}
              <i
                class="fas fa-times ms-1 cursor-pointer"
                @click="
                  searchForm.type = '';
                  applyFilters();
                "
              ></i>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Staff Table Card -->
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col">Staff Member</th>
                <th scope="col">Staff Number</th>
                <th scope="col">Department</th>
                <th scope="col">Position</th>
                <th scope="col">Status</th>
                <th scope="col">Contact</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="member in staff.data"
                :key="member.id"
                class="staff-row"
              >
                <td>
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="avatar-circle bg-primary text-white me-3">
                        {{ member.first_name[0] }}{{ member.last_name[0] }}
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-semibold">{{ member.full_name }}</h6>
                      <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Joined {{ formatDate(member.hire_date) }}
                      </small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">{{
                    member.staff_number
                  }}</span>
                </td>
                <td>
                  <div>
                    <span class="fw-medium">{{ member.department.name }}</span>
                    <br />
                    <small class="text-muted">{{
                      member.department.code
                    }}</small>
                  </div>
                </td>
                <td>
                  <div>
                    <span class="fw-medium">{{ member.position.title }}</span>
                    <br />
                    <small class="text-muted">
                      <i class="fas fa-tag me-1"></i>{{ member.position.type }}
                    </small>
                  </div>
                </td>
                <td>
                  <span
                    class="badge rounded-pill"
                    :style="{
                      backgroundColor: member.status.color + '20',
                      color: member.status.color,
                      border: '1px solid ' + member.status.color,
                    }"
                  >
                    {{ member.status.name }}
                  </span>
                </td>
                <td>
                  <div v-if="member.email || member.phone_primary">
                    <div v-if="member.email">
                      <small class="text-muted">
                        <i class="fas fa-envelope me-1"></i>{{ member.email }}
                      </small>
                    </div>
                    <div v-if="member.phone_primary">
                      <small class="text-muted">
                        <i class="fas fa-phone me-1"></i
                        >{{ member.phone_primary }}
                      </small>
                    </div>
                  </div>
                  <span v-else class="text-muted">-</span>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <Link
                      :href="`/staff/${member.id}`"
                      class="btn btn-sm btn-outline-info"
                      data-bs-toggle="tooltip"
                      title="View Details"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="`/staff/${member.id}/edit`"
                      class="btn btn-sm btn-outline-warning"
                      data-bs-toggle="tooltip"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="confirmDelete(member)"
                      class="btn btn-sm btn-outline-danger"
                      data-bs-toggle="tooltip"
                      title="Delete"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="staff.data.length === 0" class="text-center py-5">
          <div class="mb-3">
            <i class="fas fa-users text-muted" style="font-size: 4rem"></i>
          </div>
          <h5 class="text-muted">No staff members found</h5>
          <p class="text-muted">
            {{
              hasActiveFilters
                ? "Try adjusting your filters"
                : "Start by adding your first staff member"
            }}
          </p>
          <Link
            v-if="!hasActiveFilters"
            :href="'/staff/create'"
            class="btn btn-primary"
          >
            <i class="fas fa-plus me-2"></i>Add First Staff Member
          </Link>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="staff.data.length > 0"
        class="card-footer bg-transparent border-top-0"
      >
        <nav aria-label="Staff pagination">
          <ul class="pagination justify-content-center mb-0">
            <!-- Previous Page Link -->
            <li class="page-item" :class="{ disabled: !staff.prev_page_url }">
              <Link
                class="page-link"
                :href="staff.prev_page_url || '#'"
                preserve-state
              >
                <i class="fas fa-chevron-left"></i>
              </Link>
            </li>

            <!-- Page Numbers -->
            <li
              v-for="page in visiblePages"
              :key="page"
              class="page-item"
              :class="{ active: page === staff.current_page }"
            >
              <Link class="page-link" :href="getPageUrl(page)" preserve-state>
                {{ page }}
              </Link>
            </li>

            <!-- Next Page Link -->
            <li class="page-item" :class="{ disabled: !staff.next_page_url }">
              <Link
                class="page-link"
                :href="staff.next_page_url || '#'"
                preserve-state
              >
                <i class="fas fa-chevron-right"></i>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const props = defineProps({
  staff: Object,
  departments: Array,
  statuses: Array,
  filters: Object,
});

// Search and filter form
const searchForm = ref({
  search: props.filters.search || "",
  department: props.filters.department || "",
  status: props.filters.status || "",
  type: props.filters.type || "",
});

// Computed properties
const activeCount = computed(() => {
  return props.staff.data.filter((member) => member.status.allows_access)
    .length;
});

const crewCount = computed(() => {
  return props.staff.data.filter((member) => member.position.type === "crew")
    .length;
});

const hasActiveFilters = computed(() => {
  return (
    searchForm.value.search ||
    searchForm.value.department ||
    searchForm.value.status ||
    searchForm.value.type
  );
});

const visiblePages = computed(() => {
  const current = props.staff.current_page;
  const last = props.staff.last_page;
  const pages = [];

  // Show first page
  if (current > 3) pages.push(1);

  // Show dots if needed
  if (current > 4) pages.push("...");

  // Show pages around current
  for (
    let i = Math.max(1, current - 2);
    i <= Math.min(last, current + 2);
    i++
  ) {
    pages.push(i);
  }

  // Show dots if needed
  if (current < last - 3) pages.push("...");

  // Show last page
  if (current < last - 2) pages.push(last);

  return pages.filter((page, index, arr) => arr.indexOf(page) === index);
});

// Debounced search
let searchTimeout;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 500);
};

// Filter methods
const applyFilters = () => {
  router.get("/staff", searchForm.value, {
    preserveState: true,
    replace: true,
  });
};

const clearFilters = () => {
  searchForm.value = {
    search: "",
    department: "",
    status: "",
    type: "",
  };
  applyFilters();
};

const getPageUrl = (page) => {
  const url = new URL(props.staff.first_page_url);
  url.searchParams.set("page", page);
  return url.toString();
};

// Utility methods
const formatDate = (date) => {
  return new Date(date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const handleAddStaff = () => {
  console.log('Add Staff button clicked');
  router.visit('/staff/create');
};

const confirmDelete = (member) => {
  if (confirm(`Are you sure you want to delete ${member.full_name}?`)) {
    router.delete(`/staff/${member.id}`, {
      onSuccess: () => {
        // Success message will be handled by the backend
      },
    });
  }
};

onMounted(() => {
  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(
    (tooltipTriggerEl) => new window.bootstrap.Tooltip(tooltipTriggerEl)
  );
});
</script>

<style scoped>
/* Stats Cards Styling */
.stat-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.stat-card .card-body {
  padding: 1.5rem;
  position: relative;
  overflow: hidden;
}

.stat-content {
  flex: 1;
  z-index: 2;
  position: relative;
}

.stat-title {
  color: #6c757d;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 0;
}

.stat-icon-wrapper {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  flex-shrink: 0;
}

.stat-icon {
  font-size: 1.4rem;
  color: white;
  z-index: 2;
  position: relative;
}

/* Color Variants */
.stat-card .stat-value {
  color: #2c3e50;
}

.stat-card .stat-icon-wrapper {
  background: #007bff;
}

/* Subtle background pattern */
.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  background: radial-gradient(
    circle,
    rgba(255, 255, 255, 0.1) 0%,
    transparent 70%
  );
  transform: translate(30px, -30px);
}

.staff-row {
  transition: all 0.2s ease;
}

.staff-row:hover {
  background-color: rgba(0, 123, 255, 0.05);
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.875rem;
}

.table th {
  border-bottom: 2px solid #e9ecef;
  font-weight: 600;
  color: #495057;
}

.page-link {
  border: none;
  color: #6c757d;
  margin: 0 2px;
  border-radius: 6px;
}

.page-link:hover {
  background-color: #e9ecef;
  color: #495057;
}

.page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
}

.card {
  transition: all 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-group .btn {
  margin-right: 2px;
}

/* Filter Section Styling */
.filter-card {
  border-radius: 8px;
  background: #ffffff;
}

.filter-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.5rem;
}

.custom-input-group {
  border-radius: 6px;
}

.custom-input-group .input-group-text {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  color: #6c757d;
}

.custom-input {
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
}

.custom-input:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.custom-select {
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
}

.custom-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.clear-filters-btn {
  border-radius: 6px;
  padding: 0.375rem 0.75rem;
}

.filter-badge {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 4px;
}

.filter-badge .cursor-pointer {
  cursor: pointer;
}

.cursor-pointer {
  cursor: pointer;
}

.input-group-text {
  background-color: #f8f9fa;
}

.form-control:focus,
.form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>