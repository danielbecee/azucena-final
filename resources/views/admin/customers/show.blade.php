@extends('layouts.admin')

@section('title', 'Detalles del Cliente')

@section('header', 'Detalles del Cliente')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Editar
    </a>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Información del cliente -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Información Personal</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th style="width: 30%">ID:</th>
                        <td>{{ $customer->id }}</td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $customer->email }}</td>
                    </tr>
                    <tr>
                        <th>Teléfono:</th>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>CIF/NIF:</th>
                        <td>{{ $customer->tax_id ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dirección</h5>
            </div>
            <div class="card-body">
                <address>
                    {{ $customer->address ?? 'Sin dirección' }}<br>
                    @if($customer->postal_code || $customer->city)
                        {{ $customer->postal_code ?? '' }} {{ $customer->city ?? '' }}<br>
                    @endif
                    @if($customer->province)
                        {{ $customer->province }}<br>
                    @endif
                    {{ $customer->country ?? 'España' }}
                </address>
            </div>
        </div>
    </div>

    <!-- Pedidos del cliente -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pedidos</h5>
                <a href="{{ route('admin.orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Pedido
                </a>
            </div>
            <div class="card-body">
                @if($customer->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} €</td>
                                    <td>
                                        <span class="badge bg-{{ $order->orderState->color ?? 'primary' }}">
                                            {{ $order->orderState->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center my-3">Este cliente no tiene pedidos</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Información adicional -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Resumen de Actividad</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h6>Total Pedidos</h6>
                            <h3>{{ $customer->orders->count() }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h6>Total Facturado</h6>
                            <h3>{{ number_format($customer->orders->sum('total_amount'), 2) }} €</h3>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h6>Pendiente de Pago</h6>
                            <h3>{{ number_format($customer->orders->sum('total_amount') - $customer->orders->sum('paid_amount'), 2) }} €</h3>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 text-center">
                            <h6>Último Pedido</h6>
                            <h3>{{ $customer->orders->sortByDesc('order_date')->first() ? $customer->orders->sortByDesc('order_date')->first()->order_date->format('d/m/Y') : 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de eliminación oculto -->
<form id="delete-form" action="{{ route('admin.customers.destroy', $customer) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Si hay un botón de eliminación, configurarlo
        const deleteBtn = document.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Eliminar cliente?',
                    text: "Esta acción no se puede deshacer. Si el cliente tiene pedidos, no se podrá eliminar.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form').submit();
                    }
                });
            });
        }
    });
</script>
@endsection
