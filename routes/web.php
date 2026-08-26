<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FluxoController;
use App\Http\Controllers\ConstrutorFluxoController;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('clientes', ClienteController::class);

Route::get('/clientes/{cliente}/fluxos/create', [FluxoController::class, 'create'])
    ->name('clientes.fluxos.create');

Route::post('/clientes/{cliente}/fluxos', [FluxoController::class, 'store'])
    ->name('clientes.fluxos.store');

Route::resource('fluxos', FluxoController::class)
    ->except(['index', 'create', 'store']);

Route::get('/fluxos/{fluxo}/construtor', [ConstrutorFluxoController::class, 'edit'])
    ->name('fluxos.construtor');

Route::put('/fluxos/{fluxo}/construtor', [ConstrutorFluxoController::class, 'salvar'])
    ->name('fluxos.construtor.salvar');
