<template>
  <Head title="Aircraft Management" />

  <DashboardLayout>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
      <div>
        <h1 class="h3 mb-1 text-gray-900">Aircraft Management</h1>
        <p class="mb-0 text-muted">
          Manage your aircraft fleet and compliance documentation
        </p>
      </div>
      <button
        @click="handleAddAircraft"
        class="btn btn-primary"
        type="button"
      >
        <i class="fas fa-plus me-2"></i>Add New Aircraft
      </button>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-2">
      <div class="col-lg-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm h-100 stat-card stat-primary">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-plane"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white-75 small">Total Aircraft</div>
              <div class="stat-value text-white fw-bold">{{ aircraft.total }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm h-100 stat-card stat-success">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white-75 small">In Service</div>
              <div class="stat-value text-white fw-bold">{{ inServiceCount }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm h-100 stat-card stat-warning">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-wrench"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white-75 small">Maintenance</div>
              <div class="stat-value text-white fw-bold">{{ maintenanceCount }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 mb-2">
        <div class="card border-0 shadow-sm h-100 stat-card stat-danger">
          <div class="card-body d-flex align-items-center p-3">
            <div class="stat-icon me-3">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="flex-grow-1">
              <div class="stat-label text-white-75 small">AOG</div>
              <div class="stat-value text-white fw-bold">{{ aogCount }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-3 filter-card">
      <div class="card-body py-3">
        <div class="d-flex align-items-center mb-2">
          <h6 class="card-title mb-0 me-3 small">
            <i class="fas fa-filter text-primary me-2"></i>Filter Aircraft
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

        <div class="row g-2">
          <!-- Search -->
          <div class="col-md-4">
            <label class="filter-label mb-1 small">Search</label>
            <div class="input-group custom-input-group">
              <span class="input-group-text">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                v-model="searchForm.search"
                type="text"
                class="form-control custom-input"
                placeholder="Search by registration, manufacturer, model..."
                @input="debounceSearch"
              />
            </div>
          </div>

          <!-- Status Filter -->
          <div class="col-md-2">
            <label class="filter-label mb-1 small">Status</label>
            <select
              v-model="searchForm.status"
              class="form-select custom-select"
              @change="applyFilters"
            >
              <option value="">All Statuses</option>
              <option
                v-for="status in statuses"
                :key="status"
                :value="status"
              >
                {{ status }}
              </option>
            </select>
          </div>

          <!-- Manufacturer Filter -->
          <div class="col-md-3">
            <label class="filter-label mb-1 small">Manufacturer</label>
            <select
              v-model="searchForm.manufacturer"
              class="form-select custom-select"
              @change="applyFilters"
            >
              <option value="">All Manufacturers</option>
              <option
                v-for="manufacturer in manufacturers"
                :key="manufacturer"
                :value="manufacturer"
              >
                {{ manufacturer }}
              </option>
            </select>
          </div>

          <!-- Home Base Filter -->
          <div class="col-md-3">
            <label class="filter-label mb-1 small">Home Base</label>
            <select
              v-model="searchForm.home_base"
              class="form-select custom-select"
              @change="applyFilters"
            >
              <option value="">All Bases</option>
              <option
                v-for="base in homeBases"
                :key="base"
                :value="base"
              >
                {{ base }}
              </option>
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
                @click="clearSearchFilter"
              ></i>
            </span>
            <span
              v-if="searchForm.status"
              class="badge bg-success filter-badge"
            >
              Status: {{ searchForm.status }}
              <i
                class="fas fa-times ms-1 cursor-pointer"
                @click="clearStatusFilter"
              ></i>
            </span>
            <span
              v-if="searchForm.manufacturer"
              class="badge bg-info filter-badge"
            >
              Manufacturer: {{ searchForm.manufacturer }}
              <i
                class="fas fa-times ms-1 cursor-pointer"
                @click="clearManufacturerFilter"
              ></i>
            </span>
            <span
              v-if="searchForm.home_base"
              class="badge bg-warning text-dark filter-badge"
            >
              Base: {{ searchForm.home_base }}
              <i
                class="fas fa-times ms-1 cursor-pointer"
                @click="clearHomeBaseFilter"
              ></i>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Aircraft Table Card -->
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col">Aircraft</th>
                <th scope="col">Registration</th>
                <th scope="col">Specifications</th>
                <th scope="col">Status</th>
                <th scope="col">Home Base</th>
                <th scope="col">Compliance</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="aircraftItem in aircraft.data"
                :key="aircraftItem.id"
                class="aircraft-row"
              >
                <td>
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="aircraft-avatar me-3">
                        <img
                          v-if="aircraftItem.photos && aircraftItem.photos[0]"
                          :src="`/storage/${aircraftItem.photos[0]}`"
                          :alt="aircraftItem.registration_number"
                          class="w-100 h-100 object-cover"
                        />
                        <div v-else class="bg-primary text-white w-100 h-100 d-flex align-items-center justify-content-center">
                          <i class="fas fa-plane"></i>
                        </div>
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-semibold">{{ aircraftItem.manufacturer?.name }} {{ aircraftItem.model?.name }}</h6>
                      <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ aircraftItem.year_of_manufacture }}
                      </small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border fs-6 fw-bold">{{
                    aircraftItem.registration_number
                  }}</span>
                </td>
                <td>
                  <div>
                    <div class="small">
                      <strong>S/N:</strong> {{ aircraftItem.serial_number }}
                    </div>
                    <div class="small text-muted">
                      <i class="fas fa-clock me-1"></i>{{ formatHours(aircraftItem.total_airframe_hours) }} hrs
                      <span class="mx-1">•</span>
                      <i class="fas fa-sync-alt me-1"></i>{{ aircraftItem.total_cycles }} cycles
                    </div>
                  </div>
                </td>
                <td>
                  <span
                    class="badge rounded-pill"
                    :style="{
                      backgroundColor: aircraftItem.status_color + '20',
                      color: aircraftItem.status_color,
                      border: '1px solid ' + aircraftItem.status_color,
                    }"
                  >
                    {{ aircraftItem.status?.name }}
                  </span>
                </td>
                <td>
                  <span class="fw-medium">{{ aircraftItem.home_base }}</span>
                </td>
                <td>
                  <div class="compliance-indicator">
                    <div
                      class="compliance-circle"
                      :class="getComplianceClass(aircraftItem)"
                      :title="getComplianceTitle(aircraftItem)"
                    >
                      <i
                        :class="getComplianceIcon(aircraftItem)"
                      ></i>
                    </div>
                    <small class="text-muted d-block">
                      {{ getComplianceText(aircraftItem) }}
                    </small>
                  </div>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <Link
                      :href="route('aircraft.show', aircraftItem.id)"
                      class="btn btn-sm btn-outline-info"
                      title="View Details"
                    >
                      <i class="fas fa-eye"></i>
                    </Link>
                    <Link
                      :href="route('aircraft.edit', aircraftItem.id)"
                      class="btn btn-sm btn-outline-warning"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </Link>
                    <button
                      @click="confirmDelete(aircraftItem)"
                      class="btn btn-sm btn-outline-danger"
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
        <div v-if="aircraft.data.length === 0" class="text-center py-5">
          <div class="mb-3">
            <i class="fas fa-plane text-muted" style="font-size: 4rem"></i>
          </div>
          <h5 class="text-muted">No aircraft found</h5>
          <p class="text-muted">
            {{
              hasActiveFilters
                ? "Try adjusting your filters"
                : "Start by adding your first aircraft"
            }}
          </p>
          <Link
            v-if="!hasActiveFilters"
            :href="route('aircraft.create')"
            class="btn btn-primary"
          >
            <i class="fas fa-plus me-2"></i>Add First Aircraft
          </Link>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="aircraft.data.length > 0"
        class="card-footer bg-transparent border-top-0"
      >
        <nav aria-label="Aircraft pagination">
          <ul class="pagination justify-content-center mb-0">
            <!-- Previous Page Link -->
            <li class="page-item" :class="{ disabled: !aircraft.prev_page_url }">
              <Link
                class="page-link"
                :href="aircraft.prev_page_url || '#'"
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
              :class="{ active: page === aircraft.current_page }"
            >
              <Link class="page-link" :href="getPageUrl(page)" preserve-state>
                {{ page }}
              </Link>
            </li>

            <!-- Next Page Link -->
            <li class="page-item" :class="{ disabled: !aircraft.next_page_url }">
              <Link
                class="page-link"
                :href="aircraft.next_page_url || '#'"
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
  aircraft: Object,
  manufacturers: Array,
  homeBases: Array,
  statuses: Array,
  filters: Object,
});

// Search and filter form
const searchForm = ref({
  search: props.filters.search || "",
  status: props.filters.status || "",
  manufacturer: props.filters.manufacturer || "",
  home_base: props.filters.home_base || "",
});

// Computed properties
const inServiceCount = computed(() => {
  return props.aircraft.data.filter((item) => item.status?.name === 'In Service').length;
});

const maintenanceCount = computed(() => {
  return props.aircraft.data.filter((item) => item.status?.name === 'Maintenance').length;
});

const aogCount = computed(() => {
  return props.aircraft.data.filter((item) => item.status?.name === 'AOG').length;
});

const hasActiveFilters = computed(() => {
  return (
    searchForm.value.search ||
    searchForm.value.status ||
    searchForm.value.manufacturer ||
    searchForm.value.home_base
  );
});

const visiblePages = computed(() => {
  const current = props.aircraft.current_page;
  const last = props.aircraft.last_page;
  const pages = [];

  if (current > 3) pages.push(1);
  if (current > 4) pages.push("...");

  for (
    let i = Math.max(1, current - 2);
    i <= Math.min(last, current + 2);
    i++
  ) {
    pages.push(i);
  }

  if (current < last - 3) pages.push("...");
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
  router.get(route("aircraft.index"), searchForm.value, {
    preserveState: true,
    replace: true,
  });
};

const clearFilters = () => {
  searchForm.value = {
    search: "",
    status: "",
    manufacturer: "",
    home_base: "",
  };
  applyFilters();
};

const clearSearchFilter = () => {
  searchForm.value.search = "";
  applyFilters();
};

const clearStatusFilter = () => {
  searchForm.value.status = "";
  applyFilters();
};

const clearManufacturerFilter = () => {
  searchForm.value.manufacturer = "";
  applyFilters();
};

const clearHomeBaseFilter = () => {
  searchForm.value.home_base = "";
  applyFilters();
};

const getPageUrl = (page) => {
  const url = new URL(props.aircraft.first_page_url);
  url.searchParams.set("page", page);
  return url.toString();
};

// Utility methods
const formatHours = (hours) => {
  return hours ? parseFloat(hours).toLocaleString() : '0';
};

const getComplianceClass = (aircraft) => {
  if (!aircraft.compliance_status) {
    return 'compliance-warning'; // Default for missing data
  }

  // If there are expired documents, show as expired
  if (aircraft.compliance_status.expired > 0) {
    return 'compliance-expired';
  }

  // If there are expiring documents, show as warning
  if (aircraft.compliance_status.expiring > 0) {
    return 'compliance-warning';
  }

  // If all documents are valid, show as valid
  if (aircraft.compliance_status.valid > 0 && aircraft.compliance_status.total === aircraft.compliance_status.valid) {
    return 'compliance-valid';
  }

  // If there are pending documents or no documents at all
  return 'compliance-warning';
};

const getComplianceIcon = (aircraft) => {
  const classNames = getComplianceClass(aircraft);
  if (classNames.includes('valid')) return 'fas fa-check';
  if (classNames.includes('warning')) return 'fas fa-exclamation';
  return 'fas fa-times';
};

const getComplianceTitle = (aircraft) => {
  if (!aircraft.compliance_status) {
    return 'No compliance data available';
  }

  const status = aircraft.compliance_status;

  if (status.expired > 0) {
    return `${status.expired} expired document${status.expired > 1 ? 's' : ''}`;
  }

  if (status.expiring > 0) {
    return `${status.expiring} document${status.expiring > 1 ? 's' : ''} expiring soon`;
  }

  if (status.valid > 0 && status.total === status.valid) {
    return `All ${status.total} documents valid (${status.compliance_percentage}% compliant)`;
  }

  if (status.pending > 0) {
    return `${status.pending} document${status.pending > 1 ? 's' : ''} pending review`;
  }

  return status.total > 0 ? 'Mixed compliance status' : 'No documents uploaded';
};

const getComplianceText = (aircraft) => {
  if (!aircraft.compliance_status) {
    return 'Unknown';
  }

  const status = aircraft.compliance_status;

  if (status.expired > 0) {
    return 'Non-compliant';
  }

  if (status.expiring > 0) {
    return 'Expiring soon';
  }

  if (status.valid > 0 && status.total === status.valid) {
    return 'Compliant';
  }

  if (status.pending > 0) {
    return 'Pending';
  }

  return status.total > 0 ? 'Mixed' : 'No docs';
};

const handleAddAircraft = () => {
  router.visit(route('aircraft.create'));
};

const confirmDelete = (aircraft) => {
  if (confirm(`Are you sure you want to delete ${aircraft.registration_number}?`)) {
    router.delete(route('aircraft.destroy', aircraft.id), {
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
/* Stat Cards Styling */
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

.stat-icon {
  font-size: 1.5rem;
  color: rgba(255, 255, 255, 0.8);
}

.stat-label {
  color: rgba(255, 255, 255, 0.75) !important;
  font-size: 0.875rem;
  font-weight: 500;
}

.stat-value {
  color: white !important;
  font-size: 1.5rem;
  font-weight: 700;
}

/* Stat Card Colors */
.stat-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
}

.stat-success {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.stat-warning {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
}

.stat-danger {
  background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

.aircraft-row {
  transition: all 0.2s ease;
}

.aircraft-row:hover {
  background-color: rgba(0, 123, 255, 0.05);
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.aircraft-avatar {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
  overflow: hidden;
  border: 2px solid #e9ecef;
}

.compliance-indicator {
  text-align: center;
}

.compliance-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.8rem;
  margin-bottom: 4px;
}

.compliance-valid {
  background: #28a745;
}

.compliance-warning {
  background: #ffc107;
  color: #212529 !important;
}

.compliance-expired {
  background: #dc3545;
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