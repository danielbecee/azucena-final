@extends('layouts.admin')

@section('title', 'Servicios Disponibles')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="h3">Servicios Disponibles</h1>
            <p class="text-muted">Administre los servicios específicos ofrecidos</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.service_items.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Servicio
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filtros acumulativos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filtros</h5>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                <i class="fas fa-filter"></i> Mostrar/Ocultar
            </button>
        </div>
        <div class="collapse" id="collapseFilters">
            <div class="card-body">
                <form id="filters-form" method="GET" action="{{ route('admin.service_items.index') }}">
                    <div class="row g-3">
                        <!-- Categorías -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Categorías</label>
                            <div class="dropdown mb-3">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownCategory" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-tags me-2"></i> Categorías de servicio
                                    <span class="badge bg-primary ms-1 category-count">{{ count($filters['category']) }}</span>
                                </button>
                                <div class="dropdown-menu p-3 w-100" aria-labelledby="dropdownCategory" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input filter-checkbox" type="checkbox" name="category[]" value="{{ $category->id }}" id="cat{{ $category->id }}" 
                                            {{ in_array($category->id, $filters['category']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Búsqueda por nombre -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Nombre del servicio</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="name" value="{{ $filters['name'] }}" placeholder="Buscar por nombre...">
                            </div>
                        </div>
                        
                        <!-- Rango de precios -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Rango de precio</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" name="price_from" value="{{ $filters['price_from'] }}" placeholder="Min" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="0.01">
                                <span class="input-group-text">a</span>
                                <input type="number" class="form-control" name="price_to" value="{{ $filters['price_to'] }}" placeholder="Max" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="0.01">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        
                        <!-- Ordenación -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Ordenar por</label>
                            <div class="input-group mb-3">
                                <select name="sort_field" class="form-select">
                                    <option value="name" {{ $filters['sort_field'] == 'name' ? 'selected' : '' }}>Nombre</option>
                                    <option value="price" {{ $filters['sort_field'] == 'price' ? 'selected' : '' }}>Precio</option>
                                    <option value="created_at" {{ $filters['sort_field'] == 'created_at' ? 'selected' : '' }}>Fecha de creación</option>
                                </select>
                                <select name="sort_order" class="form-select">
                                    <option value="asc" {{ $filters['sort_order'] == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                    <option value="desc" {{ $filters['sort_order'] == 'desc' ? 'selected' : '' }}>Descendente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de filtro -->
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Aplicar filtros
                        </button>
                        <a href="{{ route('admin.service_items.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eraser me-1"></i> Limpiar filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceItems as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->serviceCategory->name }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ number_format($item->price, 2) }}€</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.service_items.show', $item) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.service_items.edit', $item) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $item->id }}" 
                                    action="{{ route('admin.service_items.destroy', $item) }}" 
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay servicios específicos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/service-items-filters.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar servicio?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
    
    // Asignamos la función al contexto global para que pueda ser llamada desde el onclick
    window.confirmDelete = confirmDelete;
});
</script>
@endpush
