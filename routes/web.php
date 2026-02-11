<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DokterScheduleController;
use App\Http\Controllers\Admin\DokterController as AdminDokterController;
use App\Http\Controllers\Admin\NakesController as AdminNakesController;
use App\Http\Controllers\Admin\StaffAdministrasiController as AdminStaffAdministrasiController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\MedicalVisitController;
use App\Models\HealthVisit;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Dashboard (Role Based)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $role = Auth::user()->role;
    
    return match ($role) {
        'dokter'            => redirect()->route('dokter.dashboard'),
        'nakes'             => redirect()->route('nakes.dashboard'),
        'staf_administrasi' => redirect()->route('staff_administrasi.dashboard'),
        'admin'             => redirect()->route('admin.dashboard'),
        'patient'           => redirect()->route('patient.dashboard'),
        default             => abort(403),
    };
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile (Default Laravel)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route untuk Staff Admin membuat kunjungan
    Route::get('/visits/create', [MedicalVisitController::class, 'create'])->name('visits.create');
    Route::post('/visits', [MedicalVisitController::class, 'store'])->name('visits.store');

    // Route Edit (Bisa diakses Staff Admin, Nakes, Dokter dengan permission view masing-masing)
    Route::get('/visits/{id}/edit', [MedicalVisitController::class, 'edit'])->name('visits.edit');
    Route::put('/visits/{id}', [MedicalVisitController::class, 'update'])->name('visits.update');
});


/*
|--------------------------------------------------------------------------
| STAFF ADMINISTRASI
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staf_administrasi'])
    ->prefix('staff_administrasi')
    ->name('staff_administrasi.')
    ->group(function () {

        // Dashboard Staff Administrasi
        Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');
        
        // CRUD Pasien (Menggunakan Controller yang sama dengan Nakes/Dokter)
        // Pastikan di PatientController method index/store cek role staf juga
        Route::get('/datapasien', [PatientController::class, 'index'])->name('datapasien.index');
        Route::post('/datapasien', [PatientController::class, 'store'])->name('datapasien.store');
        Route::put('/datapasien/{id}', [PatientController::class, 'update'])->name('datapasien.update');
        Route::delete('/datapasien/{id}', [PatientController::class, 'destroy'])->name('datapasien.destroy');
        
        // Monitoring & Detail Pasien (Digabung)
        Route::get('/monitoring/{id}', [MonitoringController::class, 'nakesShow'])->name('monitoring.show'); 
        // Kita reuse 'nakesShow' karena logikanya menampilkan detail pasien + visits
        });
/*
|--------------------------------------------------------------------------
| NAKES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:nakes'])
    ->prefix('nakes')
    ->name('nakes.')
    ->group(function () {

        // Dashboard Nakes
        Route::get('/dashboard', [DashboardController::class, 'nakes'])->name('dashboard');

        // (Monitoring)
        Route::get('/monitoring/{id}', [MonitoringController::class, 'nakesShow'])->name('monitoring.show');

        // Manajemen Pasien (CRUD Pasien yang sudah ada sebelumnya)
        Route::get('/datapasien', [PatientController::class, 'index'])->name('datapasien');
        Route::post('/datapasien', [PatientController::class, 'store'])->name('datapasien.store');
        Route::put('/datapasien/{id}', [PatientController::class, 'update'])->name('datapasien.update');
        Route::delete('/datapasien/{id}', [PatientController::class, 'destroy'])->name('datapasien.destroy');

        Route::post('/visits', function (Illuminate\Http\Request $request) {
            HealthVisit::create([
                'user_id' => $request->user_id,
                'nakes_id' => Auth::id(),
                'visit_date' => $request->visit_date,
                'consultation_notes' => $request->consultation_notes,
                'treatment_notes' => $request->treatment_notes,
            ]);
            return back()->with('success', 'Data kunjungan berhasil dicatat.');
        })->name('visits.store');
        
    });

/*
|--------------------------------------------------------------------------
| Dokter
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:dokter'])
    ->prefix('dokter')
    ->name('dokter.')
    ->group(function () {

        // Dashboard Dokter
        Route::get('/dashboard', [DashboardController::class, 'dokter'])->name('dashboard');

        // (Monitoring)
        Route::get('/monitoring/{id}', [MonitoringController::class, 'dokterShow'])->name('monitoring.show');
        // Manajemen Pasien (CRUD Pasien yang sudah ada sebelumnya)
        Route::get('/datapasien', [PatientController::class, 'index'])->name('datapasien');
        Route::post('/datapasien', [PatientController::class, 'store'])->name('datapasien.store');
        Route::put('/datapasien/{id}', [PatientController::class, 'update'])->name('datapasien.update');
        Route::delete('/datapasien/{id}', [PatientController::class, 'destroy'])->name('datapasien.destroy');

        // Dokter melihat jadwal sendiri
        Route::get('/schedule', [DokterController::class, 'schedule'])->name('schedule');

        Route::post('/visits', function (Illuminate\Http\Request $request) {
            HealthVisit::create([
                'user_id' => $request->user_id,
                'nakes_id' => Auth::id(),
                'visit_date' => $request->visit_date,
                'consultation_notes' => $request->consultation_notes,
                'treatment_notes' => $request->treatment_notes,
            ]);
            return back()->with('success', 'Data kunjungan berhasil dicatat.');
        })->name('visits.store');
    });

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::resource('nakes', AdminNakesController::class);
        Route::resource('dokter', AdminDokterController::class);
        Route::resource('staff', AdminStaffAdministrasiController::class);

        Route::prefix('workers')->name('workers.')->group(function () {
            Route::get('/', [AdminController::class, 'workers'])->name('index');
            Route::post('/storeWorkers', [AdminController::class, 'storeWorkers'])->name('storeWorkers');
            Route::post('/updateWorkers', [AdminController::class, 'updateWorkers'])->name('updateWorkers');
            Route::post('/destroyWorkers', [AdminController::class, 'destroyWorkers'])->name('destroyWorkers');
        });
        
        Route::get('patient', [AdminController::class, 'index'])
            ->name('patient.index');
        Route::get('patient/{user}', [AdminController::class, 'show'])
            ->name('patient.show');
        // Monitoring & Detail Pasien (Digabung)
        Route::get('/monitoring/{id}', [MonitoringController::class, 'nakesShow'])->name('monitoring.show'); 

        // Route khusus jadwal dokter
        Route::get('/dokter/{id}/schedule', [DokterScheduleController::class, 'manage'])->name('dokter.schedule');
        Route::post('/dokter/{id}/schedule/template', [DokterScheduleController::class, 'storeTemplate'])->name('dokter.schedule.template.store');
        Route::delete('/schedule/template/{id}', [DokterScheduleController::class, 'destroyTemplate'])->name('dokter.schedule.template.destroy');
        Route::post('/dokter/{id}/schedule/exception', [DokterScheduleController::class, 'storeException'])->name('dokter.schedule.exception.store');
        Route::delete('/schedule/exception/{id}', [DokterScheduleController::class, 'destroyException'])->name('dokter.schedule.exception.destroy');

    });

/*
|--------------------------------------------------------------------------
| Fallback Route (404)
|--------------------------------------------------------------------------
*/
// Menangani semua URL yang tidak terdefinisi
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

require __DIR__ . '/auth.php';
