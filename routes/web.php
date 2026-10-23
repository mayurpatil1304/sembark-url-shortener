<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/{code}', [ShortUrlController::class, 'redirect'])->where('code', '[A-Za-z0-9]{6,12}')->name('short-urls.redirect');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::middleware('role:Admin,Member')->group(function () {
        Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
    });
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::get('/admin/short-urls', [ShortUrlController::class,'indexAll',])->name('admin.short-urls.index');
    });
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::post('/admin/invitations', [InvitationController::class,'storeForNewCompany',])->name('admin.invitations.store');
    });
    Route::middleware('role:Admin')->group(function () {
        Route::post('/company/invitations', [InvitationController::class,'storeForCompany',])->name('company.invitations.store');
    });

    Route::get('/short-urls/create', function () {
        return view('short-urls.create');
    })->middleware('role:Admin,Member')->name('short-urls.create');
});

Route::get('/invitations/{token}', [InvitationController::class,'showAcceptForm',])->name('invitations.accept.form');
Route::post('/invitations/{token}', [InvitationController::class,'accept',])->name('invitations.accept');

require __DIR__.'/auth.php';
