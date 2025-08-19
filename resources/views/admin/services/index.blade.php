@extends('admin.layouts.app')

@section('title', 'Servicios')

@section('header', 'Gestión de Servicios')

@section('actions')
<a href="{{ route('admin.services.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nuevo Servicio
</a>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Filtros</h5>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
            <i class="fas fa-filter"></i> Mostrar/Ocultar
        </button>
    </div>
    <div class="collapse" id="collapseFilters">
        <div class="card-body">
            <form action="{{ route('admin.services.index') }}" method="GET">
                <div class="row g-3">
                    <!-- Búsqueda por nombre o descripción -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label">Nombre o descripción</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o descripción..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <!-- Filtro por categoría -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label">Categoría</label>
                        <select name="category_id" class="form-select mb-3">
                            <option value="">Todas las categorías</option>
                            @foreach($serviceCategories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Rango de precio -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label">Rango de precio</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="price_min" value="{{ request('price_min') }}" placeholder="Min" step="0.01" min="0">
                            <span class="input-group-text">a</span>
                            <input type="number" class="form-control" name="price_max" value="{{ request('price_max') }}" placeholder="Max" step="0.01" min="0">
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label">Estado</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                            <select name="status" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Ordenación -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label">Ordenar por</label>
                        <div class="input-group mb-3">
                            <select name="sort" class="form-select">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nombre</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Precio</option>
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Fecha de creación</option>
                            </select>
                            <select name="direction" class="form-select">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                <option value="desc" {{ request('direction') == 'desc' || !request('direction') ? 'selected' : '' }}>Descendente</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Botones de filtro -->
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Aplicar filtros
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eraser me-1"></i> Limpiar filtros
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Duración</th>
                        <th>Estado</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td class="ps-3">{{ $service->name }}</td>
                        <td>{{ Str::limit($service->description, 50) }}</td>
                        <td>{{ number_format($service->price, 2) }} €</td>
                        <td>{{ $service->duration ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group">
                                <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $service->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <p class="text-muted mb-0">No hay servicios registrados</p>
                            <a href="{{ route('admin.services.create') }}" class="btn btn-sm btn-primary mt-3">
                                <i class="fas fa-plus"></i> Añadir primer servicio
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($services->hasPages())
    <div class="card-footer bg-white border-0">
        {{ $services->links() }}
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
        });
    });
</script>
@endsection
