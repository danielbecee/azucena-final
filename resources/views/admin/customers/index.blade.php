@extends('layouts.admin')

@section('title', 'Clientes')

@section('header', 'Gestión de Clientes')

@section('actions')
<a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nuevo Cliente
</a>
@endsection

@section('content')
<!-- Filtros para clientes -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Filtros</h5>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
            <i class="fas fa-filter"></i> Mostrar/Ocultar
        </button>
    </div>
    <div class="collapse" id="collapseFilters">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <div class="row g-3">
                    <!-- Búsqueda por nombre/apellido -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Nombre o apellido</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Buscar cliente...">
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Email</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" value="{{ request('email') }}" placeholder="Buscar por email...">
                        </div>
                    </div>
                    
                    <!-- Teléfono -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Teléfono</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" name="phone" value="{{ request('phone') }}" placeholder="Buscar por teléfono...">
                        </div>
                    </div>
                    
                    <!-- CIF/NIF -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">CIF/NIF</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control" name="tax_id" value="{{ request('tax_id') }}" placeholder="Buscar por CIF/NIF...">
                        </div>
                    </div>
                    
                    <!-- Ciudad -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Ciudad</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                            <input type="text" class="form-control" name="city" value="{{ request('city') }}" placeholder="Filtrar por ciudad...">
                        </div>
                    </div>
                    
                    <!-- Ordenación -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Ordenar por</label>
                        <div class="input-group mb-3">
                            <select name="sort" class="form-select">
                                <option value="first_name" {{ request('sort') == 'first_name' ? 'selected' : '' }}>Nombre</option>
                                <option value="last_name" {{ request('sort') == 'last_name' ? 'selected' : '' }}>Apellido</option>
                                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Fecha de registro</option>
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
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
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
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>CIF/NIF</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}">
                                {{ $customer->first_name }} {{ $customer->last_name }}
                            </a>
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td>{{ $customer->tax_id ?? 'N/A' }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger confirm-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay clientes registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ya incluido en el layout principal el código para confirmar eliminación
    });
</script>
@endsection
