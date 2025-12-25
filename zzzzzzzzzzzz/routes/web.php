<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::get('/', function () {
    return view('welcome');
});

// Provide a named `login` route so Laravel's unauthenticated handler can redirect.
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/invoice/{invoice}/pdf', [PdfController::class, 'downloadInvoice'])
    ->name('invoice.pdf')
    ->middleware('auth');