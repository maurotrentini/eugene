<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ClinicController;

Route::get('/', function() {
    return redirect()->route('doctors.index');
});

Route::resource('doctors', DoctorController::class);
Route::resource('tests', TestController::class);
Route::resource('clinics', ClinicController::class);

Route::post('/doctors/merge', [DoctorController::class, 'merge'])->name('doctors.merge');
Route::post('/clinics/merge', [ClinicController::class, 'merge'])->name('clinics.merge');

