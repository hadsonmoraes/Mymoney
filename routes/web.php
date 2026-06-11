<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

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

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    // Precisa de autenticação
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/cadastro/create', [HomeController::class, 'create'])->name('contas.create');
    Route::post('/cadastro', [HomeController::class, 'store'])->name('contas.store');
    Route::get('/contas/{id}', [HomeController::class, 'show'])->name('contas.show');
    Route::get('/contas/edit/{id}', [HomeController::class, 'edit'])->name('contas.edit');
    Route::put('/contas/update/{id}', [HomeController::class, 'update'])->name('contas.update');
    Route::delete('/contas/delete/{id}', [HomeController::class, 'destroy'])->name('contas.destroy');
    Route::get('/contas/situacao/alterar/{id}', [HomeController::class, 'changeSituation'])->name('situacao.alterar');
    Route::post('/user/sidebar-toggle', [ProfileController::class, 'toggleSidebar'])->name('usuario.sidebar.toggle');
    Route::post('/user/dark-mode', [ProfileController::class, 'darkMode'])->name('usuario.darkmode');

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/delete/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

    Route::get('/profile/edit/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/gerar-csv', [HomeController::class, 'gerarCsv'])->name('contas.gerar-csv');
});

require __DIR__.'/auth.php';
