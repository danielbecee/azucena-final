@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="h3">Gestión de Pedidos</h1>
            <p class="text-muted">Administra los pedidos de clientes de forma sencilla</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Pedido
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
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
                <form id="filters-form" method="GET" action="{{ route('admin.orders.index') }}">
                    <div class="row g-3">
                        <!-- Búsqueda por ID de ticket -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">ID del ticket</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                <input type="number" class="form-control" name="ticket_id" value="{{ $filters['ticket_id'] }}" placeholder="Buscar por ID..." min="1">
                            </div>
                        </div>
                        
                        <!-- Estados de pedido -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Estados del pedido</label>
                            <div class="dropdown mb-3">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownOrderState" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-tasks me-2"></i> Estados de pedido
                                    <span class="badge bg-primary ms-1 order-state-count">{{ count($filters['order_state']) }}</span>
                                </button>
                                <div class="dropdown-menu p-3 w-100" aria-labelledby="dropdownOrderState" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($orderStates as $state)
                                    <div class="form-check">
                                        <input class="form-check-input filter-checkbox" type="checkbox" name="order_state[]" value="{{ $state->id }}" id="os{{ $state->id }}" 
                                            {{ in_array($state->id, $filters['order_state']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="os{{ $state->id }}">
                                            <span class="badge bg-{{ $state->color }}">{{ $state->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Estados de pago -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Estados de pago</label>
                            <div class="dropdown mb-3">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownPaymentState" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-money-bill me-2"></i> Estados de pago
                                    <span class="badge bg-primary ms-1 payment-state-count">{{ count($filters['payment_state']) }}</span>
                                </button>
                                <div class="dropdown-menu p-3 w-100" aria-labelledby="dropdownPaymentState" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($paymentStates as $state)
                                    <div class="form-check">
                                        <input class="form-check-input filter-checkbox" type="checkbox" name="payment_state[]" value="{{ $state->id }}" id="ps{{ $state->id }}" 
                                            {{ in_array($state->id, $filters['payment_state']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ps{{ $state->id }}">
                                            <span class="badge bg-{{ $state->color }}">{{ $state->name }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Clientes -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Clientes</label>
                            <div class="dropdown mb-3">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownCustomer" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-users me-2"></i> Clientes
                                    <span class="badge bg-primary ms-1 customer-count">{{ count($filters['customer']) }}</span>
                                </button>
                                <div class="dropdown-menu p-3 w-100" aria-labelledby="dropdownCustomer" style="max-height: 300px; overflow-y: auto;">
                                    <input type="text" class="form-control mb-2 search-filter" placeholder="Buscar cliente...">
                                    <div class="customer-filter-options">
                                        @foreach($customers as $customer)
                                        <div class="form-check">
                                            <input class="form-check-input filter-checkbox" type="checkbox" name="customer[]" value="{{ $customer->id }}" id="c{{ $customer->id }}" 
                                                {{ in_array($customer->id, $filters['customer']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="c{{ $customer->id }}">
                                                {{ $customer->first_name }} {{ $customer->last_name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Categorías de servicio -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Categorías de servicio</label>
                            <div class="dropdown mb-3">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dropdownServiceCategory" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-th-list me-2"></i> Categorías
                                    <span class="badge bg-primary ms-1 service-category-count">{{ count($filters['service_category']) }}</span>
                                </button>
                                <div class="dropdown-menu p-3 w-100" aria-labelledby="dropdownServiceCategory" style="max-height: 300px; overflow-y: auto;">
                                    @foreach($serviceCategories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input filter-checkbox" type="checkbox" name="service_category[]" value="{{ $category->id }}" id="sc{{ $category->id }}" 
                                            {{ in_array($category->id, $filters['service_category']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sc{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fecha de pedido -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Fecha de pedido</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] }}" placeholder="Desde">
                                <span class="input-group-text">a</span>
                                <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}" placeholder="Hasta">
                            </div>
                        </div>
                        
                        <!-- Fecha de entrega -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Fecha de entrega</label>
                            <div class="input-group mb-3">
                                <input type="date" class="form-control" name="due_date_from" value="{{ $filters['due_date_from'] }}" placeholder="Desde">
                                <span class="input-group-text">a</span>
                                <input type="date" class="form-control" name="due_date_to" value="{{ $filters['due_date_to'] }}" placeholder="Hasta">
                            </div>
                        </div>
                        
                        <!-- Rango de importe -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Rango de importe</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" name="amount_from" value="{{ $filters['amount_from'] }}" placeholder="Min" min="{{ $minAmount }}" max="{{ $maxAmount }}" step="0.01">
                                <span class="input-group-text">a</span>
                                <input type="number" class="form-control" name="amount_to" value="{{ $filters['amount_to'] }}" placeholder="Max" min="{{ $minAmount }}" max="{{ $maxAmount }}" step="0.01">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        
                        <!-- Ordenación -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Ordenar por</label>
                            <div class="input-group mb-3">
                                <select name="sort_field" class="form-select">
                                    <option value="created_at" {{ $filters['sort_field'] == 'created_at' ? 'selected' : '' }}>Fecha de creación</option>
                                    <option value="order_date" {{ $filters['sort_field'] == 'order_date' ? 'selected' : '' }}>Fecha de pedido</option>
                                    <option value="due_date" {{ $filters['sort_field'] == 'due_date' ? 'selected' : '' }}>Fecha de entrega</option>
                                    <option value="id" {{ $filters['sort_field'] == 'id' ? 'selected' : '' }}>ID</option>
                                    <option value="total_amount" {{ $filters['sort_field'] == 'total_amount' ? 'selected' : '' }}>Importe</option>
                                </select>
                                <select name="sort_order" class="form-select">
                                    <option value="desc" {{ $filters['sort_order'] == 'desc' ? 'selected' : '' }}>Descendente</option>
                                    <option value="asc" {{ $filters['sort_order'] == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de filtro -->
                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Aplicar filtros
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eraser me-1"></i> Limpiar filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Entrega</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                            <td>{{ $order->due_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $order->orderState->color ?? 'secondary' }}">
                                    {{ $order->orderState->name }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->paymentState->color ?? 'secondary' }}">
                                    {{ $order->paymentState->name }}
                                </span>
                            </td>
                            <td>{{ number_format($order->total_amount, 2) }}€</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.ticket', $order) }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete({{ $order->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $order->id }}" 
                                    action="{{ route('admin.orders.destroy', $order) }}" 
                                    method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay pedidos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/orders-filters.js') }}"></script>
<script>
    function confirmDelete(orderId) {
        if (confirm('¿Está seguro de eliminar este pedido? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form-' + orderId).submit();
        }
    }
</script>
@endpush
