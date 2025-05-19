<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthOracleController;


route::get('/', function () {
    return view('oracle_login');
});
Route::get('/oracle/login', [AuthOracleController::class, 'showLoginForm'])->name('oracle.login');
Route::post('/oracle/login', [AuthOracleController::class, 'login']);
Route::post('/oracle/logout', [AuthOracleController::class, 'logout'])->name('oracle.logout');
Route::get('/oracle/dashboard', [AuthOracleController::class, 'dashboard'])->name('oracle.dashboard');

