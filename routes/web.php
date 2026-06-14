<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceActionController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Registration (Pendaftaran)
    Route::get('/registration', [RegistrationController::class, 'index'])->name('registration.index');
    Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');
    Route::get('/api/patients/search', [RegistrationController::class, 'searchPatients'])->name('api.patients.search');

    // Queue (Antrean)
    Route::get('/queue', [QueueController::class, 'index'])->name('queue.index');
    Route::post('/queue/{id}/call', [QueueController::class, 'call'])->name('queue.call');
    Route::post('/queue/{id}/cancel', [QueueController::class, 'cancel'])->name('queue.cancel');

    // Examination (Pemeriksaan)
    Route::get('/examination', [ExaminationController::class, 'index'])->name('examination.index');
    Route::get('/examination/create/{patientId}', [ExaminationController::class, 'create'])->name('examination.create');
    Route::post('/examination', [ExaminationController::class, 'store'])->name('examination.store');
    Route::get('/examination/{id}', [ExaminationController::class, 'show'])->name('examination.show');

    // Payment (Pembayaran)
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/doctor/process', [PaymentController::class, 'processDoctorPayment'])->name('payment.doctor.process');
    Route::post('/payment/pharmacy/process', [PaymentController::class, 'processPharmacyPayment'])->name('payment.pharmacy.process');
    Route::post('/payment/total/process', [PaymentController::class, 'processTotalPayment'])->name('payment.total.process');
    Route::get('/payment/detail/{id}', [PaymentController::class, 'getDetail'])->name('payment.detail');
    Route::get('/payment/doctor/fee/{id}/edit', [PaymentController::class, 'getEditFee'])->name('payment.fee.edit');
    Route::put('/payment/doctor/fee/{id}', [PaymentController::class, 'updateDoctorPaymentFee'])->name('payment.fee.update');
    Route::get('/payment/{type}/struk/{id}', [PaymentController::class, 'printStruk'])->name('payment.struk');

    // Pharmacy (Apotek)
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy.index');
    Route::post('/pharmacy/{id}/process', [PharmacyController::class, 'process'])->name('pharmacy.process');
    Route::post('/pharmacy/{id}/complete', [PharmacyController::class, 'complete'])->name('pharmacy.complete');

    // Medicine (Obat)
    Route::get('/medicine', [MedicineController::class, 'index'])->name('medicine.index');
    Route::post('/medicine', [MedicineController::class, 'store'])->name('medicine.store');
    Route::get('/medicine/{id}/edit', [MedicineController::class, 'edit'])->name('medicine.edit');
    Route::put('/medicine/{id}', [MedicineController::class, 'update'])->name('medicine.update');
    Route::delete('/medicine/{id}', [MedicineController::class, 'destroy'])->name('medicine.destroy');
    Route::get('/medicine/export', [MedicineController::class, 'export'])->name('medicine.export');

    // Medical Record (Rekam Medis)
    Route::get('/medical-record', [MedicalRecordController::class, 'index'])->name('medical-record.index');
    Route::get('/medical-record/{patientId}', [MedicalRecordController::class, 'show'])->name('medical-record.show');
    Route::get('/medical-record/{patientId}/pdf', [MedicalRecordController::class, 'pdf'])->name('medical-record.pdf');

    // Report (Laporan)
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

    // Service Actions (Jasa/Tindakan)
    Route::get('/service-action', [ServiceActionController::class, 'index'])->name('service-action.index');
    Route::post('/service-action', [ServiceActionController::class, 'store'])->name('service-action.store');
    Route::get('/service-action/{id}/edit', [ServiceActionController::class, 'edit'])->name('service-action.edit');
    Route::put('/service-action/{id}', [ServiceActionController::class, 'update'])->name('service-action.update');
    Route::post('/service-action/{id}/toggle', [ServiceActionController::class, 'toggleActive'])->name('service-action.toggle');
    Route::delete('/service-action/{id}', [ServiceActionController::class, 'destroy'])->name('service-action.destroy');

    // User Management (Pengguna) - Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // Redirect root to dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
});

// Fallback to login
Route::get('/', function () {
    return redirect()->route('login');
})->middleware('guest');