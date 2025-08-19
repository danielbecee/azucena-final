@extends('admin.layouts.app')

@section('title', 'Detalles de Producto')

@section('header', 'Detalles de Producto')

@section('actions')
<div class="btn-group">
    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Editar
    </a>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Información del Producto</h5>
                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                    {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h3 class="mb-1">{{ $product->name }}</h3>
                    @if($product->sku)
                    <p class="text-muted">SKU: {{ $product->sku }}</p>
                    @endif
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">Precio</h6>
                                <h4 class="mb-0 fw-bold text-primary">{{ number_format($product->price, 2) }} €</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-muted mb-3">Stock Disponible</h6>
                                <h4 class="mb-0 fw-bold {{ $product->stock > 10 ? 'text-success' : ($product->stock > 0 ? 'text-warning' : 'text-danger') }}">
                                    {{ $product->stock ?? 'N/A' }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($product->description)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Descripción</h6>
                    <div class="p-3 bg-light rounded">
                        {{ $product->description }}
                    </div>
                </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Fecha de creación</h6>
                        <p>{{ $product->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Última actualización</h6>
                        <p>{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pedidos con este producto</h5>
            </div>
            <div class="card-body p-0">
                @if($product->orders->count() > 0)
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
                            @foreach($product->orders as $order)
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
                    <p class="text-muted mb-0">Este producto no ha sido incluido en ningún pedido todavía.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Imagen</h5>
            </div>
            <div class="card-body text-center">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                @else
                    <div class="p-5 bg-light rounded">
                        <i class="fas fa-image fa-5x text-muted"></i>
                        <p class="mt-3 text-muted">Sin imagen</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar Producto
                    </a>
                    <button type="button" class="btn btn-danger delete-btn" data-id="{{ $product->id }}">
                        <i class="fas fa-trash"></i> Eliminar Producto
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
                        <span class="badge bg-primary rounded-pill">{{ $product->orders->count() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Unidades vendidas
                        <span class="badge bg-primary rounded-pill">{{ $product->orders->sum('pivot.quantity') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Valor total vendido
                        <span class="badge bg-success rounded-pill">{{ number_format($product->orders->sum('pivot.quantity') * $product->price, 2) }} €</span>
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
                const productId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: '¿Eliminar producto?',
                    text: "Esta acción no se puede deshacer. Si el producto está en uso en algún pedido, no se podrá eliminar.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.action = `/admin/products/${productId}`;
                        deleteForm.submit();
                    }
                });
            });
        }
    });
</script>
@endsection
