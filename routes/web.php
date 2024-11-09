<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesChartController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('pages.auth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'], function () {
        return view('pages.dashboard');
    })->name('home');

    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    //Route::resource('reports', ReportController::class);
    Route::get('/sales-report', [ReportController::class, 'index'])->name('sales.report');
    Route::post('/sales-report', [ReportController::class, 'generateReport'])->name('sales.report.generate');
    Route::get('/details-report', [ReportController::class, 'details'])->name('details.report');
    Route::post('/details-report', [ReportController::class, 'generateReportDetails'])->name('details.report.generatedetails');
});
