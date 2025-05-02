<?php

use App\Http\Controllers\ContractController;
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
    Route::get('/dashboard/get-monthly-report', [DashboardController::class, 'getMonthlyReport'])->name('dashboard.get_monthly_report');
    Route::get('/dashboard/get-yearly-report', [DashboardController::class, 'getYearlyReport'])->name('dashboard.get_yearly_report');
    Route::post('/dashboard/generate-share-url', [DashboardController::class, 'generateShareUrl'])->name('dashboard.generate_share_url');
    Route::post('/dashboard/ai-prompt', [DashboardController::class, 'handleAIPrompt'])->name('dashboard.ai_prompt');

    // Contract Routes
    Route::prefix('contract')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('contract.index');
        Route::get('/create', [ContractController::class, 'create'])->name('contract.create');
        Route::post('/store', [ContractController::class, 'store'])->name('contract.store');
        Route::get('/edit/{id}', [ContractController::class, 'edit'])->name('contract.edit');
        Route::post('/update/{id}', [ContractController::class, 'update'])->name('contract.update');
        Route::delete('/destroy/{id}', [ContractController::class, 'destroy'])->name('contract.destroy');
        Route::post('/generate-contract-link/{id}', [ContractController::class, 'generateContractLink'])->name('contract.generate_contract_link');
        Route::get('/get-by-pipeline/{pipeline_id}', [ContractController::class, 'getByPipeline'])->name('contract.get_by_pipeline');
        Route::post('/send-contract/{pipeline_id}', [ContractController::class, 'sendContract'])->name('contract.send_contract');
    });

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

Route::prefix('contract')->group(function () {
    Route::get('/view/{id}', [ContractController::class, 'view'])->name('contract.view');
    Route::get('/sign/{token}', [ContractController::class, 'sign'])->name('contract.sign');
    Route::post('/sign/{token}', [ContractController::class, 'processSign'])->name('contract.process_sign');
});



require __DIR__.'/auth.php';
