<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return redirect()-> route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'role:admin,user')->group(function () {
    // Rutas perfil (ya existentes)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas clientes (existentes)
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::post('/clientes/{id}/assign', [ClienteController::class, 'assign'])->name('clientes.assign');
    
    // Ruta para mis clientes
    Route::get('/mis-clientes', [ClienteController::class, 'misClientes'])->name('mis-clientes');
    
    // NUEVA RUTA: Para actualizar el estado del cliente
    Route::patch('/mis-clientes/{id}/estado', [ClienteController::class, 'updateEstado'])->name('clientes.updateEstado');
    
    // Opcional: Ruta para finalizar cliente
    Route::post('/mis-clientes/{id}/finalizar', [ClienteController::class, 'finalizar'])->name('clientes.finalizar');
    // Ruta para ver un cliente específico en mis clientes
    Route::get('/mis-clientes/{cliente}', [ClienteController::class, 'showMisClientes'])->name('mis-clientes.show');

});

//Route::post('/clientes/{id}/finalizar', [ClienteController::class, 'finalizar'])->name('clientes.finalizar');

Route::middleware(['auth', 'role:admin'])->group(function () {
     Route::get('/reporte', [ReporteController::class, 'index'])->name('reporte.index');
});

Route::get('/detalle-kpi/{tipo}', [ReporteController::class, 'detalleKpi'])->name('detalle.kpi');


// Ruta duplicada removida (ya está arriba en el grupo middleware)
// Route::post('/clientes/{cliente}/assign', [App\Http\Controllers\ClienteController::class, 'assign'])

require __DIR__.'/auth.php';