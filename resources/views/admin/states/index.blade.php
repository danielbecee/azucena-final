@extends('layouts.admin')

@section('title', 'Estados')

@section('header', 'Gestión de Estados')

@section('actions')
<a href="{{ route('admin.states.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nuevo Estado
</a>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Nombre</th>
                        <th>Color</th>
                        <th>Descripción</th>
                        <th class="text-center">Pedidos</th>
                        <th class="text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($states as $state)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <span class="color-dot me-2" style="background-color: {{ $state->color }};"></span>
                                {{ $state->name }}
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ $state->color }};">{{ $state->color }}</span>
                        </td>
                        <td>{{ Str::limit($state->description, 50) }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $state->orders_count ?? 0 }}</span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group">
                                <a href="{{ route('admin.states.edit', $state) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $state->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <p class="text-muted mb-0">No hay estados registrados</p>
                            <a href="{{ route('admin.states.create') }}" class="btn btn-sm btn-primary mt-3">
                                <i class="fas fa-plus"></i> Añadir primer estado
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Formulario de eliminación oculto -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('styles')
<style>
    .color-dot {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 50%;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar botones de eliminación
        const deleteBtns = document.querySelectorAll('.delete-btn');
        const deleteForm = document.getElementById('delete-form');
        
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const stateId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: '¿Eliminar estado?',
                    text: "Esta acción no se puede deshacer. Si el estado está en uso en algún pedido, no se podrá eliminar.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.action = `/admin/states/${stateId}`;
                        deleteForm.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
