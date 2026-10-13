<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BestellingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PakketController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

// Publiek
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/info', [PageController::class, 'info'])->name('info');
Route::get('/pakketten', [PakketController::class, 'index'])->name('pakketten.index');

Route::get('/bestellen/{pakket}', [BestellingController::class, 'create'])->name('bestellen.create');
Route::post('/bestellen/{pakket}', [BestellingController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('bestellen.store');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

// Inloggen en registreren (alleen voor niet-ingelogde bezoekers)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/registreren', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registreren', [AuthController::class, 'register']);
});

// Uitloggen met bevestigingspagina
Route::middleware('auth')->group(function () {
    Route::get('/uitloggen', [AuthController::class, 'showLogout'])->name('logout.confirm');
    Route::post('/uitloggen', [AuthController::class, 'logout'])->name('logout');
});

// Alleen voor admins
Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/bestellingen', [AdminController::class, 'bestellingen'])->name('bestellingen');
        Route::patch('/bestellingen/{bestelling}', [AdminController::class, 'bestellingStatus'])->name('bestellingen.status');

        Route::get('/berichten', [AdminController::class, 'berichten'])->name('berichten');
        Route::patch('/berichten/{bericht}', [AdminController::class, 'berichtGelezen'])->name('berichten.gelezen');

        Route::get('/instellingen', [AdminController::class, 'instellingen'])->name('instellingen');

        Route::get('/{pagina}', [AdminController::class, 'binnenkort'])
            ->where('pagina', 'pakketten|producten|gebruikers')
            ->name('binnenkort');
    });