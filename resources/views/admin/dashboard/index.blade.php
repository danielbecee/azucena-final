@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Resumen de pedidos -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Pedidos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de clientes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Clientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_customers'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos de hoy -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pedidos de Hoy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_orders'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagos pendientes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pagos Pendientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_payment'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtro por fecha y pedidos del día -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pedidos del Día: {{ $selectedDate->format('d/m/Y') }}</h6>
                
                <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex">
                    <div class="input-group me-2">
                        <input type="date" class="form-control" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-sync-alt"></i> Hoy
                    </a>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Servicios</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($todayOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->services->count() }} servicios</span>
                                </td>
                                <td>{{ number_format($order->total_amount, 2) }} €</td>
                                <td>
                                    <span class="badge {{ $order->orderState->id == 1 ? 'bg-warning' : ($order->orderState->id == 2 ? 'bg-info' : 'bg-success') }}">
                                        {{ $order->orderState->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->payment_state_id == 1)
                                        <span class="badge bg-danger">Pendiente</span>
                                    @elseif($order->payment_state_id == 2)
                                        <span class="badge bg-warning">Parcial</span>
                                    @else
                                        <span class="badge bg-success">Pagado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.ticket', $order->id) }}" target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        <!-- Botones de cambio rápido de estado -->
                                        @if($order->orderState->name != 'Listo')
                                            <a href="{{ route('admin.orders.ready', $order->id) }}" class="btn btn-sm btn-success" title="Marcar como Listo">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif
                                        @if($order->orderState->name != 'En taller')
                                            <a href="{{ route('admin.orders.workshop', $order->id) }}" class="btn btn-sm btn-warning" title="Marcar como En taller">
                                                <i class="fas fa-tools"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    No hay pedidos para el día {{ $selectedDate->format('d/m/Y') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .border-left-primary {
        border-left: 4px solid var(--primary-color);
    }
    
    .border-left-success {
        border-left: 4px solid var(--success-color);
    }
    
    .border-left-info {
        border-left: 4px solid var(--info-color);
    }
    
    .border-left-warning {
        border-left: 4px solid var(--warning-color);
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .text-xs {
        font-size: 0.7rem;
    }
</style>
@endsection
