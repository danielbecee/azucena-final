@extends('admin.layouts.app')

@section('title', 'Productos')

@section('header', 'Gestión de Productos')

@section('actions')
<a href="{{ route('admin.products.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nuevo Producto
</a>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header bg-white border-0">
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o descripción..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="ps-3" style="width: 80px;">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-height: 50px;">
                            @else
                                <div class="bg-light text-center p-2 rounded">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ Str::limit($product->description, 50) }}</td>
                        <td>{{ number_format($product->price, 2) }} €</td>
                        <td>{{ $product->stock ?? 'N/A' }}</td>
                        <td class="text-end pe-3">
                            <div class="btn-group">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $product->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="text-muted mb-0">No hay productos registrados</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary mt-3">
                                <i class="fas fa-plus"></i> Añadir primer producto
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($products->hasPages())
    <div class="card-footer bg-white border-0">
        {{ $products->links() }}
    </div>
    @endif
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
        // Configurar botones de eliminación
        const deleteBtns = document.querySelectorAll('.delete-btn');
        const deleteForm = document.getElementById('delete-form');
        
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
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
        });
    });
</script>
@endsection
