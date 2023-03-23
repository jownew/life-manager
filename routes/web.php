<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::name('paymentTypes.')->prefix('payment-types')->group(function () {
        Route::get('/', [PaymentTypeController::class, 'index'])->name('index');
        Route::get('/{id}', [PaymentTypeController::class, 'show'])->name('show');
        Route::patch('/{id}', [PaymentTypeController::class, 'update'])->name('update');
        Route::post('/', [PaymentTypeController::class, 'store'])->name('store');
        Route::delete('/{id}', [PaymentTypeController::class, 'destroy'])->name('destroy');
    });

    Route::name('expenses.')->prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/{id}', [ExpenseController::class, 'show'])->name('show');
        Route::patch('/{id}', [ExpenseController::class, 'update'])->name('update');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
    });

    Route::name('currencies.')->prefix('currencies')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/{id}', [CurrencyController::class, 'show'])->name('show');
        Route::patch('/{id}', [CurrencyController::class, 'update'])->name('update');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::delete('/{id}', [CurrencyController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('users')->resource('users', UserController::class);
    Route::prefix('events')->resource('events', EventController::class);
});

