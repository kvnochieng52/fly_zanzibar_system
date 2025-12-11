<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AircraftController;
use App\Http\Controllers\CompanyDocumentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\LandingFeeController;
use App\Http\Controllers\NavigationFeeController;
use App\Http\Controllers\FuelPurchaseController;
use App\Http\Controllers\FuelConsumptionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ScheduledFlightController;
use App\Http\Controllers\FlightRouteController;
use App\Http\Controllers\FlightLandingFeeController;
use App\Http\Controllers\FlightFuelConsumptionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('home.dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Staff Management routes
    Route::resource('staff', StaffController::class);
    Route::post('/staff/upload-photo', [StaffController::class, 'uploadPhoto'])->name('staff.upload-photo');
    Route::post('/staff/{staff}/contracts', [StaffController::class, 'uploadContract'])->name('staff.upload-contract');

    // Staff License Management
    Route::post('/staff/{staff}/licenses', [StaffController::class, 'storeLicense'])->name('staff.licenses.store');
    Route::put('/staff/{staff}/licenses/{license}', [StaffController::class, 'updateLicense'])->name('staff.licenses.update');
    Route::delete('/staff/{staff}/licenses/{license}', [StaffController::class, 'destroyLicense'])->name('staff.licenses.destroy');

    // Staff Medical Certificates Management
    Route::post('/staff/{staff}/medical-certificates', [StaffController::class, 'storeMedicalCertificate'])->name('staff.medical-certificates.store');
    Route::put('/staff/{staff}/medical-certificates/{medicalCertificate}', [StaffController::class, 'updateMedicalCertificate'])->name('staff.medical-certificates.update');
    Route::delete('/staff/{staff}/medical-certificates/{medicalCertificate}', [StaffController::class, 'destroyMedicalCertificate'])->name('staff.medical-certificates.destroy');

    // Staff Type Ratings Management
    Route::post('/staff/{staff}/type-ratings', [StaffController::class, 'storeTypeRating'])->name('staff.type-ratings.store');
    Route::put('/staff/{staff}/type-ratings/{typeRating}', [StaffController::class, 'updateTypeRating'])->name('staff.type-ratings.update');
    Route::delete('/staff/{staff}/type-ratings/{typeRating}', [StaffController::class, 'destroyTypeRating'])->name('staff.type-ratings.destroy');

    // Staff Proficiencies Management
    Route::post('/staff/{staff}/proficiencies', [StaffController::class, 'storeProficiency'])->name('staff.proficiencies.store');
    Route::put('/staff/{staff}/proficiencies/{proficiency}', [StaffController::class, 'updateProficiency'])->name('staff.proficiencies.update');
    Route::delete('/staff/{staff}/proficiencies/{proficiency}', [StaffController::class, 'destroyProficiency'])->name('staff.proficiencies.destroy');

    // Staff Files Management
    Route::post('/staff/{staff}/files', [StaffController::class, 'storeFile'])->name('staff.files.store');
    Route::get('/staff/{staff}/files/{file}/download', [StaffController::class, 'downloadFile'])->name('staff.files.download');
    Route::delete('/staff/{staff}/files/{file}', [StaffController::class, 'destroyFile'])->name('staff.files.destroy');

    // Aircraft Management routes
    Route::resource('aircraft', AircraftController::class);
    Route::post('/aircraft/upload-photo', [AircraftController::class, 'uploadPhoto'])->name('aircraft.upload-photo');
    Route::post('/aircraft/upload-document', [AircraftController::class, 'uploadDocument'])->name('aircraft.upload-document');

    // Aircraft Document Management
    Route::post('/aircraft/{aircraft}/documents', [AircraftController::class, 'storeDocument'])->name('aircraft.documents.store');
    Route::put('/aircraft/{aircraft}/documents/{document}', [AircraftController::class, 'updateDocument'])->name('aircraft.documents.update');
    Route::delete('/aircraft/{aircraft}/documents/{document}', [AircraftController::class, 'destroyDocument'])->name('aircraft.documents.destroy');

    // Aircraft API endpoints
    Route::get('/aircraft/api/models-by-manufacturer', [AircraftController::class, 'getModelsByManufacturer'])->name('aircraft.models-by-manufacturer');

    // Aircraft Maintenance routes
    Route::post('/aircraft/{aircraft}/maintenance/schedule', [\App\Http\Controllers\AircraftMaintenanceController::class, 'storeSchedule'])->name('aircraft.maintenance.store');
    Route::post('/aircraft/{aircraft}/work-orders', [\App\Http\Controllers\AircraftMaintenanceController::class, 'storeWorkOrder'])->name('aircraft.work-orders.store');
    Route::get('/api/maintenance-types', [\App\Http\Controllers\AircraftMaintenanceController::class, 'getMaintenanceTypes'])->name('api.maintenance-types');
    Route::get('/api/maintenance-organizations', [\App\Http\Controllers\AircraftMaintenanceController::class, 'getMaintenanceOrganizations'])->name('api.maintenance-organizations');
    Route::get('/api/priorities/{context?}', [\App\Http\Controllers\AircraftMaintenanceController::class, 'getPriorities'])->name('api.priorities');
    Route::delete('/aircraft/maintenance/{schedule}', [\App\Http\Controllers\AircraftMaintenanceController::class, 'deleteSchedule'])->name('aircraft.maintenance.delete');
    Route::delete('/aircraft/work-orders/{workOrder}', [\App\Http\Controllers\AircraftMaintenanceController::class, 'deleteWorkOrder'])->name('aircraft.work-orders.delete');
    Route::patch('/aircraft/work-orders/{workOrder}/status', [\App\Http\Controllers\AircraftMaintenanceController::class, 'updateWorkOrderStatus'])->name('aircraft.work-orders.status');

    // Company Documents Management routes
    Route::resource('company-documents', CompanyDocumentController::class);
    Route::get('/company-documents/{companyDocument}/download', [CompanyDocumentController::class, 'download'])->name('company-documents.download');
    Route::get('/documents-dashboard', [CompanyDocumentController::class, 'dashboard'])->name('company-documents.dashboard');
    Route::get('/company-documents/{companyDocument}/renew', [CompanyDocumentController::class, 'showRenew'])->name('company-documents.renew');
    Route::post('/company-documents/{companyDocument}/renew', [CompanyDocumentController::class, 'processRenewal'])->name('company-documents.process-renewal');

    // Accounting Module routes
    Route::resource('quotations', \App\Http\Controllers\QuotationController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::get('/invoices/{invoice}/download-pdf', [\App\Http\Controllers\InvoiceController::class, 'downloadPdf'])->name('invoices.download-pdf');
    Route::post('/quotations/{quotation}/convert-to-invoice', [\App\Http\Controllers\InvoiceController::class, 'convertFromQuotation'])->name('quotations.convert-to-invoice');

    // Receipt Management routes
    Route::resource('receipts', ReceiptController::class);
    Route::get('/receipts/{receipt}/download-pdf', [ReceiptController::class, 'downloadPdf'])->name('receipts.download-pdf');

    // Statement Generation routes
    Route::get('/statements', [StatementController::class, 'index'])->name('statements.index');
    Route::get('/statements/{customer}', [StatementController::class, 'show'])->name('statements.show');
    Route::get('/statements/{customer}/download', [StatementController::class, 'download'])->name('statements.download');
    Route::get('/aging-report', [StatementController::class, 'agingReport'])->name('statements.aging-report');

    // Fee Tracking Module routes
    Route::resource('landing-fees', LandingFeeController::class);
    Route::resource('navigation-fees', NavigationFeeController::class);
    Route::resource('fuel-purchases', FuelPurchaseController::class);
    Route::resource('fuel-consumption', FuelConsumptionController::class);

    // User Management Module routes
    Route::resource('roles', RoleController::class);
    Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'removePermission'])->name('roles.remove-permission');

    Route::resource('permissions', PermissionController::class);
    Route::get('/api/permissions/by-category', [PermissionController::class, 'getPermissionsByCategory'])->name('permissions.by-category');
    Route::post('/api/permissions/bulk-create', [PermissionController::class, 'bulkCreate'])->name('permissions.bulk-create');

    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/assign-roles', [UserManagementController::class, 'assignRoles'])->name('users.assign-roles');
    Route::delete('/users/{user}/roles/{role}', [UserManagementController::class, 'removeRole'])->name('users.remove-role');
    Route::post('/users/{user}/assign-permissions', [UserManagementController::class, 'assignPermissions'])->name('users.assign-permissions');
    Route::get('/users/{user}/effective-permissions', [UserManagementController::class, 'getEffectivePermissions'])->name('users.effective-permissions');

    // Scheduled Flights Module routes
    Route::resource('flight-routes', FlightRouteController::class);
    Route::resource('scheduled-flights', ScheduledFlightController::class);

    // Flight Passengers & Cargo routes
    Route::resource('flight-passengers', \App\Http\Controllers\FlightPassengerController::class)->except(['index', 'show', 'create', 'edit']);
    Route::post('flight-passengers/{passenger}/check-in', [\App\Http\Controllers\FlightPassengerController::class, 'checkIn'])->name('flight-passengers.check-in');

    Route::resource('flight-cargo', \App\Http\Controllers\FlightCargoController::class)->except(['index', 'show', 'create', 'edit']);
    Route::patch('flight-cargo/{cargo}/status', [\App\Http\Controllers\FlightCargoController::class, 'updateStatus'])->name('flight-cargo.update-status');

    // Flight Fees Module routes
    Route::resource('flight-landing-fees', FlightLandingFeeController::class);
    Route::resource('flight-fuel-consumption', FlightFuelConsumptionController::class);
    Route::post('/flight-fuel-consumption/calculate-metrics', [FlightFuelConsumptionController::class, 'calculateMetrics'])->name('flight-fuel-consumption.calculate-metrics');
});

require __DIR__.'/auth.php';
