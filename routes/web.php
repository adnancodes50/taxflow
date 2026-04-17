<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Models\Upload;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// ADMIN AUTH
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// UPLOAD
Route::get('/upload', function () {
    return view('frontend.upload');
})->name('upload');

Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');

// ANALYZE
Route::get('/analyze/{id}', [UploadController::class, 'analyze'])->name('analyze');
Route::post('/process-analysis/{id}', [UploadController::class, 'processAnalysis'])
    ->name('process.analysis');
// PAYMENT


    Route::get('/lock/{reportId}', [UploadController::class, 'lock'])->middleware(['auth'])
    ->name('lock');


    Route::post('/payment/{reportId}', [PaymentController::class, 'process'])->middleware(['auth'])
    ->name('payment.process');

Route::get('/payment/success/{reportId}', [PaymentController::class, 'success'])->middleware(['auth'])
    ->name('payment.success');

Route::get('/payment/cancel/{reportId}', [PaymentController::class, 'cancel'])->middleware(['auth'])
    ->name('payment.cancel');


    Route::get('/report/{id}', [UploadController::class, 'getReport'])
    ->middleware(['auth'])
    ->name('report');

    Route::get('/report-download/{id}', [UploadController::class, 'downloadReport'])
 ->middleware(['auth'])
    ->name('report.download');

    Route::post('/update-classification/{id}', [UploadController::class, 'updateClassification'])->middleware(['auth']);

// ADMIN
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');

    Route::get('/admin/orders', [AdminDashboardController::class, 'getOrder'])
    ->name('admin.order');

    Route::get('/admin/settings', [SettingsController::class, 'index'])
        ->name('admin.settings');

    Route::post('/admin/settings', [SettingsController::class, 'save'])
        ->name('admin.settings.save');

});



Route::get('/dashboard', function () {

    $uploads = Upload::with('report')
        ->where('user_id', Auth::id())
        ->latest()
        ->get();

    $total = $uploads->count();

    // Paid uploads (based on report.payment_status)
    $paidUploads = $uploads->filter(function ($upload) {
        return optional($upload->report)->payment_status === 'paid';
    });

    $paidCount = $paidUploads->count();

    // Total spend (sum of paid report prices)
    $totalSpend = $paidUploads->sum(function ($upload) {
        return optional($upload->report)->price ?? 0;
    });

    $stats = [
        'total' => $total,
        'paid' => $paidCount,
        'unpaid' => $total - $paidCount,
        'spend' => $totalSpend,
    ];

    return view('dashboard', compact('uploads', 'stats'));

})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/fetch-result/{id}', [UploadController::class, 'fetchResult']);

// PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
