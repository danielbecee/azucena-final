@extends('admin.layouts.app')

@section('title', 'Detalles de Servicio')

@section('header', 'Detalles de Servicio')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Editar
    </a>
    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Información del Servicio</h5>
                <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-danger' }}">
                    {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h3 class="mb-1">{{ $service->name }}</h3>
                    @if($service->code)
                    <p class="text-muted">Código: {{ $service->code }}</p>
                    @endif
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">Precio</h6>
                                <h4 class="mb-0 fw-bold text-primary">{{ number_format($service->price, 2) }} €</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">Duración</h6>
                                <h4 class="mb-0 fw-bold">
                                    {{ $service->duration ? $service->duration . ' min.' : 'N/A' }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($service->description)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Descripción</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $service->description }}
                    </div>
                </div>
                @endif
                
                @if($service->category)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Categoría</h6>
                    <p class="mb-0">{{ $service->category }}</p>
                </div>
                @endif
                
                @if($service->notes)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Notas internas</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $service->notes }}
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Fecha de creación</h6>
                        <p>{{ $service->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Última actualización</h6>
                        <p>{{ $service->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pedidos con este servicio</h5>
            </div>
            <div class="card-body p-0">
                @if($service->orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Cantidad</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service->orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>{{ $order->pivot->quantity }}</td>
                                <td class="text-end">
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
                <div class="text-center py-4">
                    <p class="text-muted mb-0">Este servicio no ha sido incluido en ningún pedido todavía.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar Servicio
                    </a>
                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $service->id }}">
                        <i class="fas fa-trash"></i> Eliminar Servicio
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">Estadísticas</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total de pedidos
                        <span class="badge bg-primary rounded-pill">{{ $service->orders->count() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Unidades vendidas
                        <span class="badge bg-primary rounded-pill">{{ $service->orders->sum('pivot.quantity') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Valor total vendido
                        <span class="badge bg-success rounded-pill">{{ number_format($service->orders->sum('pivot.quantity') * $service->price, 2) }} €</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de eliminación oculto -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar botón de eliminación
        const deleteBtn = document.querySelector('.delete-btn');
        const deleteForm = document.getElementById('delete-form');
        
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: '¿Eliminar servicio?',
                    text: "Esta acción no se puede deshacer. Si el servicio está en uso en algún pedido, no se podrá eliminar.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.action = `/admin/services/${serviceId}`;
                        deleteForm.submit();
                    }
                });
            });
        }
    });
</script>
@endsection
