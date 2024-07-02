<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

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
    return redirect('/login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    // Precisa de autenticação
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/cadastro/create', [HomeController::class, 'create'])->name('contas.create');
    Route::post('/cadastro', [HomeController::class, 'store'])->name('contas.store');
    Route::get('/cadastro/{id}', [HomeController::class, 'show'])->name('contas.show');
    Route::get('/cadastro/edit/{id}', [HomeController::class, 'edit'])->name('contas.edit');
    Route::put('/cadastro/update/{id}', [HomeController::class, 'update'])->name('contas.update');
    Route::delete('/cadastro/delete/{id}', [HomeController::class, 'destroy'])->name('contas.destroy');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
});
