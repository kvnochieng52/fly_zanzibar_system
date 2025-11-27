<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
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
});

require __DIR__.'/auth.php';
