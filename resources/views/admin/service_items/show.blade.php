@extends('layouts.admin')

@section('title', 'Detalles del Servicio')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Servicio: {{ $serviceItem->name }}</h1>
            <p class="text-muted">Categoría: {{ $serviceItem->serviceCategory->name }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('admin.service_items.edit', $serviceItem) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.service_categories.show', $serviceItem->serviceCategory) }}" class="btn btn-info">
                    <i class="fas fa-folder-open"></i> Ver Categoría
                </a>
                <a href="{{ route('admin.service_items.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información del Servicio</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $serviceItem->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $serviceItem->name }}</td>
                        </tr>
                        <tr>
                            <th>Categoría:</th>
                            <td>
                                <a href="{{ route('admin.service_categories.show', $serviceItem->serviceCategory) }}">
                                    {{ $serviceItem->serviceCategory->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Precio:</th>
                            <td><strong>{{ number_format($serviceItem->price, 2) }}€</strong></td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $serviceItem->description ?: 'Sin descripción' }}</td>
                        </tr>
                        <tr>
                            <th>Creado:</th>
                            <td>{{ $serviceItem->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Actualizado:</th>
                            <td>{{ $serviceItem->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.service_items.edit', $serviceItem) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Editar Servicio
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $serviceItem->id }})">
                            <i class="fas fa-trash me-1"></i> Eliminar Servicio
                        </button>
                        <form id="delete-form-{{ $serviceItem->id }}" action="{{ route('admin.service_items.destroy', $serviceItem) }}" 
                            method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.confirmDelete = function(id) {
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
    };
});
</script>
@endpush
