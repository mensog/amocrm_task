<?php

use App\Http\Controllers\AmoAPIController;
use App\Http\Controllers\AmoCRMController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/amocrm-form', [AmoCRMController::class, 'showForm'])->name('amocrm.form');
Route::post('/amocrm-submit', [AmoCRMController::class, 'submitForm'])->name('amocrm.submit');
Route::get('/amocrm-callback', [AmoCRMController::class, 'handleAmoCRMCb'])->name('amocrm.callback');
Route::get('/amocrm-authorize', [AmoCRMController::class, 'auth'])->name('amocrm.authorize');

// webhooks
Route::post('/lead_add', [AmoCRMController::class, 'leadAdd'])->name('lead-add');
Route::post('/contact_add', [AmoCRMController::class, 'contactAdd'])->name('contact-add');
