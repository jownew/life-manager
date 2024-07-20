<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DailyTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

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
    if (Auth::check()) {
        return redirect()->route('dashboard.index');
    }
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
    Route::name('dashboard.')->prefix('dashboard')->group(function () {
        Route::get('/{date?}', [DashboardController::class, 'index'])->name('index');
    });

    Route::name('paymentTypes.')->prefix('payment-types')->group(function () {
        Route::get('/', [PaymentTypeController::class, 'index'])->name('index');
        Route::get('/{id}', [PaymentTypeController::class, 'show'])->name('show');
        Route::patch('/{id}', [PaymentTypeController::class, 'update'])->name('update');
        Route::post('/', [PaymentTypeController::class, 'store'])->name('store');
        Route::delete('/{id}', [PaymentTypeController::class, 'destroy'])->name('destroy');
    });

    Route::name('expenses.')->prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/export/', [ExpenseController::class, 'export'])->name('export');
        Route::get('/{id}', [ExpenseController::class, 'show'])->name('show');
        Route::patch('/{id}', [ExpenseController::class, 'update'])->name('update');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::delete('/', [ExpenseController::class, 'destroyMany'])->name('destroyMany');
    });

    Route::name('currencies.')->prefix('currencies')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/{id}', [CurrencyController::class, 'show'])->name('show');
        Route::patch('/{id}', [CurrencyController::class, 'update'])->name('update');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::delete('/{id}', [CurrencyController::class, 'destroy'])->name('destroy');
    });

    Route::name('categories.')->prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::patch('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('users')->resource('users', UserController::class);

    Route::name('daily-tasks.')->prefix('daily-tasks')->group(function () {
        Route::post('/{id}/toggle', [DailyTaskController::class, 'toggle'])->name('toggle');
        Route::post('/{id}/snooze', [DailyTaskController::class, 'snooze'])->name('snooze');
        Route::post('/{dailyTask}/prioritise', [DailyTaskController::class, 'prioritise'])->name('prioritise');
        Route::delete('/', [DailyTaskController::class, 'destroyMany'])->name('destroy-many');
    });

    Route::prefix('daily-tasks')->resource('daily-tasks', DailyTaskController::class);

    Route::name('events.')->prefix('events')->group(function () {
        Route::get('/all', [EventController::class, 'showAll'])->name('showAll');
        Route::post('/{id}/change-status', [EventController::class, 'changeStatus'])->name('changeStatus');
        Route::delete('/', [EventController::class, 'destroyMany'])->name('destroyMany');
    });
    Route::prefix('events')->resource('events', EventController::class);
});
