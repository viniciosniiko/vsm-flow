<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlowController;
use App\Http\Controllers\FlowBuilderController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('flows', FlowController::class);

Route::get('flows/{flow}/builder', [FlowBuilderController::class, 'edit'])->name('flows.builder');
Route::post('flows/{flow}/builder/block', [FlowBuilderController::class, 'storeBlock'])->name('flows.builder.block.store');
Route::put('flows/{flow}/builder/block/{block}', [FlowBuilderController::class, 'updateBlock'])->name('flows.builder.block.update');
Route::delete('flows/{flow}/builder/block/{block}', [FlowBuilderController::class, 'destroyBlock'])->name('flows.builder.block.destroy');
Route::post('flows/{flow}/builder/connection', [FlowBuilderController::class, 'storeConnection'])->name('flows.builder.connection.store');
Route::delete('flows/{flow}/builder/connection/{connection}', [FlowBuilderController::class, 'destroyConnection'])->name('flows.builder.connection.destroy');
