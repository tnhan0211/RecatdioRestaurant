<?php
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\MenuController;
use App\Http\Controllers\FrontController;

// Front-end Routes
Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/about', [FrontController::class, 'about'])->name('front.about');
Route::get('/booking', [FrontController::class, 'booking'])->name('front.booking');
Route::get('/contact', [FrontController::class, 'contact'])->name('front.contact');
Route::get('/service', [FrontController::class, 'service'])->name('front.service');
Route::get('/menu', [FrontController::class, 'menu'])->name('front.menu');
Route::get('/team', [FrontController::class, 'team'])->name('front.team');
Route::get('/testimonial', [FrontController::class, 'testimonial'])->name('front.testimonial');

// User Auth Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('front.home');
    })->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
        
        //category_route
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::resource('categories', CategoryController::class);
        
        //menu_route
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::resource('menu', MenuController::class);
        Route::post('menu/update-position', [MenuController::class, 'updatePosition'])->name('menu.update-position');
        
        Route::get('/settings', [AdminLoginController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [AdminLoginController::class, 'updateSettings'])->name('admin.updateSettings');
        Route::get('/change-password', [AdminLoginController::class, 'changePassword'])->name('admin.changePassword');
        Route::post('/change-password', [AdminLoginController::class, 'updatePassword'])->name('admin.updatePassword');
    });
    
    Route::get('/create', [AdminLoginController::class, 'create'])->name('admin.create');
    Route::post('/store', [AdminLoginController::class, 'store'])->name('admin.store');
    Route::get('/forgot-password', [AdminLoginController::class, 'forgotPassword'])->name('admin.forgotPassword');
    Route::post('/forgot-password', [AdminLoginController::class, 'forgotPasswordProcess'])->name('admin.forgotPasswordProcess');
    Route::get('/reset-password/{token}', [AdminLoginController::class, 'resetPassword'])->name('admin.resetPassword');
    Route::post('/reset-password', [AdminLoginController::class, 'resetPasswordProcess'])->name('admin.resetPasswordProcess');
});

// Authentication Routes
require __DIR__.'/auth.php';