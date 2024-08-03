<?php

use App\Http\Controllers\AmoAPIController;
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


Route::get('/form', [AmoAPIController::class, 'index'])->name('form');
Route::post('/form-submit', [AmoAPIController::class, 'formSubmit'])->name('form-submit');

Route::get('/leads', [AmoAPIController::class, 'getAllLeads'])->name('leads');

Route::post('/lead_add', [AmoAPIController::class, 'leadAdd'])->name('lead-add');
Route::post('/contact_add', [AmoAPIController::class, 'contactAdd'])->name('contact-add');
