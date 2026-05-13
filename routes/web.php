<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategorySuggestionController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\IsVerified;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', [CategoryController::class, 'index'])->name('home');
Route::get('/feed', [PostController::class, 'index'])->name('feed');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show')->whereNumber('post');
Route::get('/users/{user}', [\App\Http\Controllers\PublicProfileController::class, 'show'])->name('users.show');

Route::inertia('/about', 'About')->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store']);

Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\Auth\ProviderController::class, 'redirect'])->name('auth.provider.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\Auth\ProviderController::class, 'callback'])->name('auth.provider.callback');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    Route::middleware([IsVerified::class])->group(function () {
        Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        
        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
        
        Route::post('/posts/{post}/message', [MessageController::class, 'store'])->name('posts.message');
    });

    Route::middleware(['role:Super Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/suggestions/{suggestion}/approve', [CategorySuggestionController::class, 'approve'])->name('suggestions.approve');
        Route::post('/suggestions/{suggestion}/reject', [CategorySuggestionController::class, 'reject'])->name('suggestions.reject');
        
        Route::post('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('users.verify');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    });
});

require __DIR__.'/settings.php';
