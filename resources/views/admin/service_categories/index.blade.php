@extends('layouts.admin')

@section('title', 'Categorías de Servicio')

@section('content')
<div class="container py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="h3">Categorías de Servicio</h1>
            <p class="text-muted">Administre las categorías generales de servicios ofrecidos</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.service_categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nueva Categoría
            </a>
        </div>
    </div>
    
    <!-- Filtros para categorías de servicio -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filtros</h5>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                <i class="fas fa-filter"></i> Mostrar/Ocultar
            </button>
        </div>
        <div class="collapse" id="collapseFilters">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.service_categories.index') }}">
                    <div class="row g-3">
                        <!-- Búsqueda por nombre -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Nombre</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text" class="form-control" name="name" value="{{ request('name') }}" placeholder="Buscar por nombre...">
                            </div>
                        </div>
                        
                        <!-- Descripción -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Descripción</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                <input type="text" class="form-control" name="description" value="{{ request('description') }}" placeholder="Buscar en descripción...">
                            </div>
                        </div>
                        
                        <!-- Servicios (mínimo) -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Servicios mínimos</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                <input type="number" class="form-control" name="min_services" value="{{ request('min_services') }}" placeholder="Mínimo servicios" min="0">
                            </div>
                        </div>
                        
                        <!-- Ordenación -->
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Ordenar por</label>
                            <div class="input-group mb-3">
                                <select name="sort" class="form-select">
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nombre</option>
                                    <option value="service_count" {{ request('sort') == 'service_count' ? 'selected' : '' }}>Número de servicios</option>
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Fecha de creación</option>
                                </select>
                                <select name="direction" class="form-select">
                                    <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                    <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de filtro -->
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Aplicar filtros
                        </button>
                        <a href="{{ route('admin.service_categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eraser me-1"></i> Limpiar filtros
                        </a>
                    </div>
                </form>
            </div>
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

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Servicios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ Str::limit($category->description, 50) }}</td>
                            <td>{{ $category->service_items_count }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.service_categories.show', $category) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.service_categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete({{ $category->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $category->id }}" 
                                    action="{{ route('admin.service_categories.destroy', $category) }}" 
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay categorías de servicio registradas</td>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar categoría?',
            text: "Esta acción no se puede deshacer y eliminará todos los servicios asociados.",
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
