<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\LocalizationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/* Clear application cache: */
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return 'Application cache has been cleared';
});

/* Clear route cache: */
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return 'Routes cache has been cleared';
});

/* Clear config cache: */
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return 'Config cache has been cleared';
});

/* Clear view cache: */
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return 'View cache has been cleared';
});

/* Clear optimize */
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return 'Configuration & Route cache cleared successfully';
});

/* Clear permission cache */
Route::get('/permission-clear', function () {
    $exitCode = Artisan::call('permission:cache-reset');
    return 'Permission cache cleared successfully';
});

Route::get('/', [PagesController::class, 'home']);
Route::get('/about', [PagesController::class, 'about'])->name('about');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::get('/register', [PagesController::class, 'register'])->name('register');
Route::get('/terms', [PagesController::class, 'terms'])->name('terms');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::post('/register', [RegistrationController::class, 'store'])->name('register.submit');
Route::post('/register/supplier', [ContactController::class, 'registerSupplier'])->name('register.supplier');
Route::post('/register/client', [ContactController::class, 'registerClient'])->name('register.client');

// Real-time validation routes
Route::post('/validate/email', [RegistrationController::class, 'validateEmail'])->name('validate.email');
Route::post('/validate/company-reg-no', [RegistrationController::class, 'validateCompanyRegNo'])->name('validate.company-reg-no');

Route::get('lang/{locale}', [LocalizationController::class, 'setLang'])->name('lang.switch');

// PDF Preview Route (for testing)
Route::get('/preview-registration-pdf/{id}', [RegistrationController::class, 'previewPdf'])->name('registration.pdf.preview');


Auth::routes(['register' => false]);

Route::prefix('admin')->group(function () {

    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
        ->name('admin.login')->middleware('guest');

    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])
        ->name('admin.login.submit')->middleware('guest');

    Route::get('/dashboard', [AdminController::class, 'index'])
        ->name('admin.index')->middleware('auth');

    Route::post('/upload', [AdminController::class, 'upload'])
        ->name('admin.upload')->middleware('auth');

    Route::get('/storage/pricelists/{filename}', [AdminController::class, 'showPriceList'])
        ->name('admin.pricelist.show');

    Route::get('/change-password', [AdminController::class, 'changePasswordForm'])
        ->name('admin.change.password')->middleware('auth');

    Route::post('/change-password-update', [AdminController::class, 'changePasswordUpdate'])
        ->name('admin.change.password.update')->middleware('auth');

    Route::get('/product/add', [ProductController::class, 'productAdd'])
        ->name('admin.product.add')->middleware('auth');

    Route::post('/product/store', [ProductController::class, 'productStore'])
        ->name('admin.product.store')->middleware('auth');

    Route::get('/product/edit/{id}', [ProductController::class, 'productEdit'])
        ->name('admin.product.edit');

    Route::post('/product/update/{id}', [ProductController::class, 'productUpdate'])->name('admin.product.update');

    Route::get('/product/delete/{id}', [ProductController::class, 'productDelete'])
        ->name('admin.product.delete')->middleware('auth');

    Route::get('/banner', [BannerController::class, 'index'])
        ->name('admin.banner')->middleware('auth');

    Route::get('/banner/create', [BannerController::class, 'create'])
        ->name('admin.banner.create')->middleware('auth');

    Route::post('/banner/store', [BannerController::class, 'store'])
        ->name('admin.banner.store')->middleware('auth');

    Route::get('/banner/edit/{id}', [BannerController::class, 'edit'])
        ->name('admin.banner.edit');

    Route::post('/banner/update/{id}', [BannerController::class, 'update'])
        ->name('admin.banner.update');

    Route::post('/banner/destroy/{id}', [BannerController::class, 'destroy'])
        ->name('admin.banner.destroy')->middleware('auth');

});



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');





