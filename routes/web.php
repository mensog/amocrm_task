<?php

use App\Http\Controllers\AmoCrm\AmoCRMController;
use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::post('lead', [LeadController::class, 'store'])->name('lead.store');
Route::view('lead', 'form')->name('lead.create');

Route::prefix('amocrm')->group(function () {
    Route::get('auth', [AmoCRMController::class, 'redirectToOAuth'])->name('amocrm.redirectoauth');
    Route::get('callback', [AmoCRMController::class, 'authorizationOAuth'])->name('amocrm.authorizationoauth');
});
