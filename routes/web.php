<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('pipelines')->group(function () {
        Route::get('/', [PipelineController::class, 'index'])->name('pipelines');
        Route::get('/create', [PipelineController::class, 'create'])->name('pipelines.create');
        Route::post('/store', [PipelineController::class, 'store'])->name('pipelines.store');
        Route::get('/edit/{id}', [PipelineController::class, 'edit'])->name('pipelines.edit');
        Route::post('/update', [PipelineController::class, 'update'])->name('pipelines.update');
        Route::delete('/destroy/{id}', [PipelineController::class, 'destroy'])->name('pipelines.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    Route::get('find-username-email', [UserController::class, 'getUsernameEmail'])->name('users.find_username_email');
});



require __DIR__.'/auth.php';
