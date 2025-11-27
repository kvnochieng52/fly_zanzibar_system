<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '../Layouts/DashboardLayout.vue';
import StatCard from '../Components/Dashboard/StatCard.vue';
import ChartBox from '../Components/Dashboard/ChartBox.vue';
import TableCard from '../Components/Dashboard/TableCard.vue';

// Get stats from props with fallback defaults for aviation operations
const props = defineProps({
  stats: {
    type: Object,
    default() {
      return {
        activeAircraft: 12,
        crewMembers: 45,
        flightsToday: 8,
        maintenanceDue: 3,
        documentsExpiring: 7,
        revenue: 125000,
        fuelCosts: 45000,
        landingFees: 8500
      };
    }
  }
});

// Format numbers with commas
function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Format currency
function formatCurrency(num) {
  return '$' + formatNumber(num);
}

// Chart data with only professional colors (#008cee, #cc1318)
const flightStatusData = {
  type: 'doughnut',
  data: {
    labels: ['On Time', 'Issues'],
    datasets: [{
      data: [75, 25],
      backgroundColor: ['#008cee', '#cc1318'],
      borderWidth: 0,
      cutout: '60%'
    }]
  }
};

const operationalMetricsData = {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'Flight Operations',
      data: [156, 189, 175, 210, 195, 245],
      borderColor: '#008cee',
      backgroundColor: 'rgba(0, 140, 238, 0.1)',
      borderWidth: 3,
      fill: true,
      tension: 0.4
    }]
  }
};

const maintenanceChartData = {
  type: 'bar',
  data: {
    labels: ['Scheduled', 'Completed', 'Overdue'],
    datasets: [{
      label: 'Maintenance Tasks',
      data: [15, 12, 3],
      backgroundColor: ['#008cee', '#008cee', '#cc1318'],
      borderRadius: 6,
      maxBarThickness: 60
    }]
  }
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      backgroundColor: '#1f2937',
      padding: 12,
      cornerRadius: 8
    }
  }
};

// Table data for recent flights
const flightColumns = [
  { field: 'flightNumber', label: 'Flight' },
  { field: 'aircraft', label: 'Aircraft' },
  { field: 'route', label: 'Route' },
  { field: 'status', label: 'Status' },
  { field: 'departure', label: 'Departure' },
  { field: 'arrival', label: 'Arrival' }
];

const recentFlights = ref([
  { id: 1, flightNumber: 'FZ101', aircraft: '5H-ABC', route: 'ZNZ → DAR', status: 'On Time', departure: '08:30', arrival: '09:45' },
  { id: 2, flightNumber: 'FZ102', aircraft: '5H-DEF', route: 'DAR → ZNZ', status: 'Delayed', departure: '14:15', arrival: '15:30' },
  { id: 3, flightNumber: 'FZ103', aircraft: '5H-GHI', route: 'ZNZ → PWM', status: 'On Time', departure: '11:00', arrival: '11:20' },
  { id: 4, flightNumber: 'FZ104', aircraft: '5H-JKL', route: 'PWM → ZNZ', status: 'In Progress', departure: '16:45', arrival: '17:05' },
  { id: 5, flightNumber: 'FZ105', aircraft: '5H-MNO', route: 'ZNZ → MBA', status: 'On Time', departure: '19:30', arrival: '20:15' }
]);

// Maintenance alerts data
const maintenanceColumns = [
  { field: 'aircraft', label: 'Aircraft' },
  { field: 'type', label: 'Maintenance Type' },
  { field: 'dueDate', label: 'Due Date' },
  { field: 'priority', label: 'Priority' },
  { field: 'status', label: 'Status' }
];

const maintenanceAlerts = ref([
  { id: 1, aircraft: '5H-ABC', type: '100-Hour Inspection', dueDate: '2024-01-15', priority: 'High', status: 'Overdue' },
  { id: 2, aircraft: '5H-DEF', type: 'Annual Inspection', dueDate: '2024-01-20', priority: 'Medium', status: 'Due Soon' },
  { id: 3, aircraft: '5H-GHI', type: 'Engine Service', dueDate: '2024-02-01', priority: 'Low', status: 'Scheduled' }
]);

// Event handlers
function handleStatClick(type) {
  console.log(`Stat clicked: ${type}`);
}

function viewFlight(flight) {
  console.log('View Flight:', flight);
}

function editFlight(flight) {
  console.log('Edit Flight:', flight);
}

function deleteFlight(flight) {
  console.log('Delete Flight:', flight);
}

function viewMaintenance(maintenance) {
  console.log('View Maintenance:', maintenance);
}

function editMaintenance(maintenance) {
  console.log('Edit Maintenance:', maintenance);
}

function deleteMaintenance(maintenance) {
  console.log('Delete Maintenance:', maintenance);
}
</script>

<template>
  <Head title="Dashboard" />
  
  <DashboardLayout>
    <!-- Welcome Banner -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #008cee 0%, #0070c0 100%);">
          <div class="card-body d-flex align-items-center p-4">
            <div>
              <h3 class="mb-1 text-white"><i class="fas fa-plane mr-2"></i>Fly Zanzibar Aviation Dashboard</h3>
              <p class="mb-0 text-white opacity-75">Monitor your aviation operations, aircraft, crew, and financial performance</p>
            </div>
            <div class="ml-auto d-flex gap-2">
              <button class="btn btn-light mr-2"><i class="fas fa-calendar-plus mr-2"></i> Schedule Flight</button>
              <button class="btn btn-outline-light"><i class="fas fa-tools mr-2"></i> Maintenance Alert</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Professional Stats Row with only #008cee and #cc1318 -->
    <div class="row mb-4">
      <div class="col-lg-3 col-6">
        <div class="card stat-card" style="border-left: 4px solid #008cee;">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="mb-0" style="color: #008cee; font-weight: 600;">{{ formatNumber(stats.activeAircraft) }}</h3>
                <p class="text-muted mb-0 small">Active Aircraft</p>
              </div>
              <div class="stat-icon">
                <i class="fas fa-plane" style="color: #008cee; font-size: 2rem; opacity: 0.7;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="card stat-card" style="border-left: 4px solid #008cee;">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="mb-0" style="color: #008cee; font-weight: 600;">{{ formatNumber(stats.crewMembers) }}</h3>
                <p class="text-muted mb-0 small">Crew Members</p>
              </div>
              <div class="stat-icon">
                <i class="fas fa-users" style="color: #008cee; font-size: 2rem; opacity: 0.7;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="card stat-card" style="border-left: 4px solid #cc1318;">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="mb-0" style="color: #cc1318; font-weight: 600;">{{ formatNumber(stats.documentsExpiring) }}</h3>
                <p class="text-muted mb-0 small">Documents Expiring</p>
              </div>
              <div class="stat-icon">
                <i class="fas fa-file-alt" style="color: #cc1318; font-size: 2rem; opacity: 0.7;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="card stat-card" style="border-left: 4px solid #cc1318;">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <h3 class="mb-0" style="color: #cc1318; font-weight: 600;">{{ formatNumber(stats.maintenanceDue) }}</h3>
                <p class="text-muted mb-0 small">Maintenance Due</p>
              </div>
              <div class="stat-icon">
                <i class="fas fa-wrench" style="color: #cc1318; font-size: 2rem; opacity: 0.7;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row with aviation metrics -->
    <div class="row mb-4">
      <div class="col-md-4">
        <ChartBox
          title="Flight Status Distribution"
          chart-id="flightStatusChart"
          :chart-data="flightStatusData"
          :chart-options="chartOptions"
        />
      </div>
      <div class="col-md-4">
        <ChartBox
          title="Maintenance Overview"
          chart-id="maintenanceChart"
          :chart-data="maintenanceChartData"
          :chart-options="chartOptions"
        />
      </div>
      <div class="col-md-4">
        <ChartBox
          title="Monthly Flight Operations"
          chart-id="operationalChart"
          :chart-data="operationalMetricsData"
          :chart-options="chartOptions"
        />
      </div>
    </div>

    <!-- Professional Quick Actions Section -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card" style="border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
          <div class="card-header" style="background-color: #f8f9fa; border-bottom: 3px solid #008cee;">
            <h5 class="mb-0 font-weight-600" style="color: #495057;">
              <i class="fas fa-bolt mr-2" style="color: #008cee;"></i>Quick Actions
            </h5>
          </div>
          <div class="card-body" style="background-color: #fdfdfd;">
            <div class="row">
              <div class="col-md-2 col-sm-4 col-6 mb-3">
                <button class="btn btn-block professional-action-btn" style="border: 2px solid #008cee; color: #008cee; background: white;">
                  <i class="fas fa-calendar-plus mb-2 d-block" style="font-size: 20px;"></i>
                  <span style="font-size: 12px; font-weight: 500;">Schedule Flight</span>
                </button>
              </div>
              <div class="col-md-2 col-sm-4 col-6 mb-3">
                <button class="btn btn-block professional-action-btn" style="border: 2px solid #008cee; color: #008cee; background: white;">
                  <i class="fas fa-user-plus mb-2 d-block" style="font-size: 20px;"></i>
                  <span style="font-size: 12px; font-weight: 500;">Add Crew</span>
                </button>
              </div>
              <div class="col-md-2 col-sm-4 col-6 mb-3">
                <button class="btn btn-block professional-action-btn" style="border: 2px solid #cc1318; color: #cc1318; background: white;">
                  <i class="fas fa-tools mb-2 d-block" style="font-size: 20px;"></i>
                  <span style="font-size: 12px; font-weight: 500;">Maintenance</span>
                </button>
              </div>
              <div class="col-md-2 col-sm-4 col-6 mb-3">
                <button class="btn btn-block professional-action-btn" style="border: 2px solid #6c757d; color: #6c757d; background: white;">
                  <i class="fas fa-file-invoice-dollar mb-2 d-block" style="font-size: 20px;"></i>
                  <span style="font-size: 12px; font-weight: 500;">New Invoice</span>
                </button>
              </div>
              <div class="col-md-2 col-sm-4 col-6 mb-3">
                <button class="btn btn-block professional-action-btn" style="border: 2px solid #cc1318; color: #cc1318; background: white;">
                  <i class="fas fa-file-alt mb-2 d-block" style="font-size: 20px;"></i>
                  <span style="font-size: 12px; font-weight: 500;">Documents</span>
                </button>
              </div>
              <div class="col-md-2 col-sm-4 col-6 mb-3">
                <button class="btn btn-block professional-action-btn" style="border: 2px solid #6c757d; color: #6c757d; background: white;">
                  <i class="fas fa-chart-bar mb-2 d-block" style="font-size: 20px;"></i>
                  <span style="font-size: 12px; font-weight: 500;">Reports</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Aviation Tables Section -->
    <div class="row">
      <div class="col-lg-8">
        <TableCard
          title="Today's Flight Schedule"
          :columns="flightColumns"
          :items="recentFlights"
          show-search
          has-pagination
          @view="viewFlight"
          @edit="editFlight"
          @delete="deleteFlight"
        />
      </div>
      <div class="col-lg-4">
        <TableCard
          title="Maintenance Alerts"
          :columns="maintenanceColumns"
          :items="maintenanceAlerts"
          show-search
          @view="viewMaintenance"
          @edit="editMaintenance"
          @delete="deleteMaintenance"
        />
      </div>
    </div>

    <!-- Professional Information Cards with Clean Design -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card" style="border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
          <div class="card-header" style="background: linear-gradient(135deg, #cc1318 0%, #b01015 100%); color: white; border: none;">
            <h5 class="mb-0 font-weight-600">
              <i class="fas fa-exclamation-triangle mr-2"></i>Critical Alerts
            </h5>
          </div>
          <div class="card-body" style="background-color: #fdfdfd;">
            <div class="professional-alert danger mb-3" style="border-left: 4px solid #cc1318; background: #fdf2f2; padding: 12px; border-radius: 4px;">
              <i class="fas fa-exclamation-circle mr-2" style="color: #cc1318;"></i>
              <strong style="color: #cc1318;">Aircraft 5H-ABC:</strong>
              <span style="color: #495057;">100-hour inspection overdue by 2 days</span>
            </div>
            <div class="professional-alert warning mb-3" style="border-left: 4px solid #6c757d; background: #f8f9fa; padding: 12px; border-radius: 4px;">
              <i class="fas fa-file-alt mr-2" style="color: #6c757d;"></i>
              <strong style="color: #6c757d;">Document Alert:</strong>
              <span style="color: #495057;">3 crew licenses expire within 30 days</span>
            </div>
            <div class="professional-alert info mb-0" style="border-left: 4px solid #008cee; background: #f0f8ff; padding: 12px; border-radius: 4px;">
              <i class="fas fa-fuel-pump mr-2" style="color: #008cee;"></i>
              <strong style="color: #008cee;">Fuel:</strong>
              <span style="color: #495057;">Stock level at 65% - Consider restocking</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card" style="border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
          <div class="card-header" style="background: linear-gradient(135deg, #008cee 0%, #0070c0 100%); color: white; border: none;">
            <h5 class="mb-0 font-weight-600">
              <i class="fas fa-clock mr-2"></i>Recent Activity
            </h5>
          </div>
          <div class="card-body" style="background-color: #fdfdfd;">
            <div class="professional-timeline">
              <div class="timeline-item mb-3" style="padding-left: 20px; border-left: 2px solid #f1f3f4; position: relative;">
                <div class="timeline-dot" style="position: absolute; left: -6px; top: 8px; width: 10px; height: 10px; background-color: #008cee; border-radius: 50%;"></div>
                <span class="time" style="font-size: 11px; color: #6c757d; font-weight: 500;">
                  <i class="fas fa-clock mr-1"></i>08:30
                </span>
                <h6 class="timeline-header" style="font-size: 14px; font-weight: 600; margin: 2px 0; color: #495057;">
                  Flight FZ101 departed on time
                </h6>
                <div class="timeline-body" style="font-size: 12px; color: #868e96;">
                  ZNZ → DAR, Aircraft: 5H-ABC
                </div>
              </div>
              <div class="timeline-item mb-3" style="padding-left: 20px; border-left: 2px solid #f1f3f4; position: relative;">
                <div class="timeline-dot" style="position: absolute; left: -6px; top: 8px; width: 10px; height: 10px; background-color: #008cee; border-radius: 50%;"></div>
                <span class="time" style="font-size: 11px; color: #6c757d; font-weight: 500;">
                  <i class="fas fa-clock mr-1"></i>07:45
                </span>
                <h6 class="timeline-header" style="font-size: 14px; font-weight: 600; margin: 2px 0; color: #495057;">
                  Maintenance completed
                </h6>
                <div class="timeline-body" style="font-size: 12px; color: #868e96;">
                  5H-DEF: 50-hour inspection completed successfully
                </div>
              </div>
              <div class="timeline-item mb-0" style="padding-left: 20px; border-left: 2px solid #f1f3f4; position: relative;">
                <div class="timeline-dot" style="position: absolute; left: -6px; top: 8px; width: 10px; height: 10px; background-color: #6c757d; border-radius: 50%;"></div>
                <span class="time" style="font-size: 11px; color: #6c757d; font-weight: 500;">
                  <i class="fas fa-clock mr-1"></i>06:00
                </span>
                <h6 class="timeline-header" style="font-size: 14px; font-weight: 600; margin: 2px 0; color: #495057;">
                  Crew check-in
                </h6>
                <div class="timeline-body" style="font-size: 12px; color: #868e96;">
                  Captain John Smith checked in for FZ101
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>

<style scoped>
/* Professional Aviation Dashboard Styles */

/* System Color Variables */
:root {
  --primary-blue: #008cee;
  --danger-red: #cc1318;
  --professional-grey: #6c757d;
  --light-grey: #f8f9fa;
  --card-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/* Stats Cards */
.stat-card {
  border: none;
  box-shadow: var(--card-shadow);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

/* Professional Action Buttons */
.professional-action-btn {
  height: 80px;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  position: relative;
  overflow: hidden;
}

.professional-action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Blue button hover effect */
.professional-action-btn[style*="#008cee"]:hover {
  background-color: #008cee !important;
  color: white !important;
}

/* Red button hover effect */
.professional-action-btn[style*="#cc1318"]:hover {
  background-color: #cc1318 !important;
  color: white !important;
}

/* Grey button hover effect */
.professional-action-btn[style*="#6c757d"]:hover {
  background-color: #6c757d !important;
  color: white !important;
}

/* Professional Alerts */
.professional-alert {
  transition: all 0.2s ease;
}

.professional-alert:hover {
  transform: translateX(2px);
}

/* Timeline Enhancement */
.professional-timeline .timeline-item:last-child {
  border-left-color: transparent !important;
}

/* Card Headers */
.card-header {
  font-weight: 600;
  border-radius: 0.5rem 0.5rem 0 0 !important;
}

/* Clean backgrounds */
.card-body {
  padding: 1.5rem;
}

/* Typography */
.font-weight-600 {
  font-weight: 600 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .professional-action-btn {
    height: 70px;
    margin-bottom: 10px;
  }

  .stat-card {
    margin-bottom: 15px;
  }
}

/* Remove default Bootstrap colors, keep professional palette */
.bg-primary { background-color: var(--primary-blue) !important; }
.bg-danger { background-color: var(--danger-red) !important; }
.text-primary { color: var(--primary-blue) !important; }
.text-danger { color: var(--danger-red) !important; }

/* Clean, minimal design focus */
.card {
  border-radius: 0.5rem;
}

/* Professional hover effects */
.card:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
</style>
