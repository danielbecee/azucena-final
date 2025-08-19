<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;

use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceItemController;
use App\Http\Controllers\Admin\StateController;

// Ruta principal redirige a login
Route::get('/', fn() => redirect()->route('login'));

// Autenticación unificada en /login y /register
Route::get('/login',    [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showSignupForm'])->name('register');
Route::post('/register',[AuthController::class, 'register']);

// Grupo de rutas para administración
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Reports
    Route::get('/reports/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('admin.reports.sales');

    // Gestión de clientes
    Route::get('/clientes', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/clientes/crear', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/clientes', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/clientes/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/clientes/{customer}/editar', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/clientes/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/clientes/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('/clientes/buscar', [CustomerController::class, 'search'])->name('admin.customers.search');
    

    
   
    // Gestión de estados
    Route::get('/estados', [StateController::class, 'index'])->name('admin.states.index');
    Route::get('/estados/crear', [StateController::class, 'create'])->name('admin.states.create');
    Route::post('/estados', [StateController::class, 'store'])->name('admin.states.store');
    // Rutas específicas antes de las rutas con parámetros
    Route::get('/estados/pedido', [StateController::class, 'getOrderStates'])->name('admin.states.order');
    Route::get('/estados/pago', [StateController::class, 'getPaymentStates'])->name('admin.states.payment');
    // Rutas con parámetros después
    Route::get('/estados/{state}', [StateController::class, 'show'])->name('admin.states.show');
    Route::get('/estados/{state}/editar', [StateController::class, 'edit'])->name('admin.states.edit');
    Route::put('/estados/{state}', [StateController::class, 'update'])->name('admin.states.update');
    Route::delete('/estados/{state}', [StateController::class, 'destroy'])->name('admin.states.destroy');
    
    // Gestión de pedidos
    Route::get('/pedidos', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/pedidos/crear', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/pedidos', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/pedidos/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/pedidos/{order}/editar', [OrderController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/pedidos/{order}', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/pedidos/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::get('/pedidos/{order}/ticket', [OrderController::class, 'ticket'])->name('admin.orders.ticket');
    Route::get('/pedidos/{order}/tickets-individuales', [OrderController::class, 'individualTickets'])->name('admin.orders.individual_tickets');
    Route::get('/pedidos/service/{orderService}/ticket', [OrderController::class, 'serviceTicket'])->name('admin.orders.service_ticket');
    Route::post('/pedidos/{order}/pago', [OrderController::class, 'addPayment'])->name('admin.orders.payment');
    Route::get('/pedidos/{order}/ready', [OrderController::class, 'markAsReady'])->name('admin.orders.ready');
    Route::get('/pedidos/{order}/workshop', [OrderController::class, 'markAsInWorkshop'])->name('admin.orders.workshop');
    
    // Gestión de categorías de servicio
    Route::get('/categorias-servicio', [ServiceCategoryController::class, 'index'])->name('admin.service_categories.index');
    Route::get('/categorias-servicio/crear', [ServiceCategoryController::class, 'create'])->name('admin.service_categories.create');
    Route::post('/categorias-servicio', [ServiceCategoryController::class, 'store'])->name('admin.service_categories.store');
    Route::get('/categorias-servicio/{serviceCategory}', [ServiceCategoryController::class, 'show'])->name('admin.service_categories.show');
    Route::get('/categorias-servicio/{serviceCategory}/editar', [ServiceCategoryController::class, 'edit'])->name('admin.service_categories.edit');
    Route::put('/categorias-servicio/{serviceCategory}', [ServiceCategoryController::class, 'update'])->name('admin.service_categories.update');
    Route::delete('/categorias-servicio/{serviceCategory}', [ServiceCategoryController::class, 'destroy'])->name('admin.service_categories.destroy');
    
    // Gestión de servicios específicos
    Route::get('/servicios', [ServiceItemController::class, 'index'])->name('admin.service_items.index');
    Route::get('/servicios/crear', [ServiceItemController::class, 'create'])->name('admin.service_items.create');
    Route::post('/servicios', [ServiceItemController::class, 'store'])->name('admin.service_items.store');
    Route::get('/servicios/{serviceItem}', [ServiceItemController::class, 'show'])->name('admin.service_items.show');
    Route::get('/servicios/{serviceItem}/editar', [ServiceItemController::class, 'edit'])->name('admin.service_items.edit');
    Route::put('/servicios/{serviceItem}', [ServiceItemController::class, 'update'])->name('admin.service_items.update');
    Route::delete('/servicios/{serviceItem}', [ServiceItemController::class, 'destroy'])->name('admin.service_items.destroy');
    Route::get('/servicios-por-categoria/{category}', [ServiceItemController::class, 'getByCategory'])->name('admin.service_items.by_category');
    
    // API para el sistema de pedidos (AJAX)
    Route::prefix('api')->group(function() {
        Route::get('/clientes', [CustomerController::class, 'getCustomers'])->name('api.customers');

        Route::get('/estados/pedido', [StateController::class, 'getOrderStates'])->name('api.states.order');
        Route::get('/estados/pago', [StateController::class, 'getPaymentStates'])->name('api.states.payment');
    });
    
    // Búsqueda para autocompletado
    Route::get('/clientes/search', [CustomerController::class, 'search'])->name('admin.customers.search');
});
