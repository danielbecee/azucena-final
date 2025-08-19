@extends('layouts.admin')

@section('title', 'Detalles de Categoría de Servicio')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Categoría: {{ $serviceCategory->name }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('admin.service_categories.edit', $serviceCategory) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.service_items.create') }}?category={{ $serviceCategory->id }}" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Añadir Servicio
                </a>
                <a href="{{ route('admin.service_categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información de la Categoría</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $serviceCategory->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $serviceCategory->name }}</td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $serviceCategory->description ?: 'Sin descripción' }}</td>
                        </tr>
                        <tr>
                            <th>Creado:</th>
                            <td>{{ $serviceCategory->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Actualizado:</th>
                            <td>{{ $serviceCategory->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Servicios en esta Categoría</h5>
                </div>
                <div class="card-body">
                    @if($serviceCategory->serviceItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceCategory->serviceItems as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
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
                                                    onclick="confirmDeleteItem({{ $item->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-item-form-{{ $item->id }}" 
                                                action="{{ route('admin.service_items.destroy', $item) }}" 
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No hay servicios asociados a esta categoría.
                            <a href="{{ route('admin.service_items.create') }}?category={{ $serviceCategory->id }}" class="alert-link">
                                Añadir un nuevo servicio
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.confirmDeleteItem = function(id) {
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
                document.getElementById('delete-item-form-' + id).submit();
            }
        });
    };
});
</script>
@endpush
