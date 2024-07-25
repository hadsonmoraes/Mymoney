<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Profiler\Profile;

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
    Route::get('/contas/{id}', [HomeController::class, 'show'])->name('contas.show');
    Route::get('/contas/edit/{id}', [HomeController::class, 'edit'])->name('contas.edit');
    Route::put('/contas/update/{id}', [HomeController::class, 'update'])->name('contas.update');
    Route::delete('/contas/delete/{id}', [HomeController::class, 'destroy'])->name('contas.destroy');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');

    Route::get('/profile/edit/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
});
