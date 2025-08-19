@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Editar Pedido #{{ $order->id }}</h1>
            <p class="text-muted">Modifique la información del pedido según sea necesario</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver a detalles
            </a>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Datos del Cliente</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Cliente</label>
                                    <select class="form-select" id="customer_id" name="customer_id" required>
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->first_name }} {{ $customer->last_name }} - {{ $customer->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Información del Pedido</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="order_date" class="form-label">Fecha de pedido</label>
                                        <input type="date" class="form-control" id="order_date" name="order_date" 
                                            value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="due_date" class="form-label">Fecha de entrega</label>
                                        <input type="date" class="form-control" id="due_date" name="due_date" 
                                            value="{{ old('due_date', $order->due_date->format('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="order_state_id" class="form-label">Estado del pedido</label>
                                    <select class="form-select" id="order_state_id" name="order_state_id" required>
                                        @foreach($orderStates as $state)
                                            <option value="{{ $state->id }}" 
                                                {{ $order->order_state_id == $state->id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notas</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Sección de productos eliminada -->

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Servicios</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-service">
                                    <i class="fas fa-plus"></i> Añadir servicio
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="services-container">
                                    @forelse($order->services as $service)
                                    <div class="service-row row mb-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Servicio</label>
                                            <select class="form-select service-select" name="service_item_id[]">
                                                <option value="">Seleccione un servicio</option>
                                                @foreach($services as $serv)
                                                    <option value="{{ $serv->id }}" data-price="{{ $serv->price }}"
                                                        {{ $service->service_item_id == $serv->id ? 'selected' : '' }}>
                                                        {{ $serv->name }} - {{ number_format($serv->price, 2) }}€
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" class="form-control service-quantity" name="service_quantity[]" 
                                                min="1" value="{{ $service->quantity }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Precio €</label>
                                            <input type="number" step="0.01" min="0" class="form-control service-price" name="service_price[]" 
                                                value="{{ number_format($service->price, 2, '.', '') }}">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <label class="form-label">Nota específica</label>
                                            <textarea class="form-control service-description" name="service_description[]" rows="2">{{ $service->description }}</textarea>
                                        </div>
                                        <div class="col-md-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="service-row row mb-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Servicio</label>
                                            <select class="form-select service-select" name="service_item_id[]">
                                                <option value="">Seleccione un servicio</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                                        {{ $service->name }} - {{ number_format($service->price, 2) }}€
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" class="form-control service-quantity" name="service_quantity[]" 
                                                min="1" value="1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Precio €</label>
                                            <input type="number" step="0.01" min="0" class="form-control service-price" name="service_price[]" 
                                                value="0.00">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <label class="form-label">Nota específica</label>
                                            <textarea class="form-control service-description" name="service_description[]" rows="2"></textarea>
                                        </div>
                                        <div class="col-md-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Información de Pago</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="payment_state_id" class="form-label">Estado del pago</label>
                                    <select class="form-select" id="payment_state_id" name="payment_state_id" required>
                                        @foreach($paymentStates as $state)
                                            <option value="{{ $state->id }}" 
                                                {{ $order->payment_state_id == $state->id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="paid_amount" class="form-label">Cantidad pagada total</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="paid_amount" name="paid_amount" 
                                            min="0" step="0.01" value="{{ old('paid_amount', $order->paid_amount) }}">
                                        <span class="input-group-text">€</span>
                                    </div>
                                    <small class="form-text text-muted">
                                        Para registrar pagos individuales, utilice la opción "Registrar Pago" en la página de detalles.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Resumen</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">0.00€</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>IVA (21%):</span>
                                    <span id="tax">0.00€</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 fw-bold">
                                    <span>Total:</span>
                                    <span id="total">0.00€</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pagado:</span>
                                    <span id="paid">{{ number_format($order->paid_amount, 2) }}€</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Pendiente:</span>
                                    <span id="pending">0.00€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Actualizar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Productos eliminados

        // Add service row
        const servicesContainer = document.getElementById('services-container');
        const addServiceBtn = document.getElementById('add-service');
        
        addServiceBtn.addEventListener('click', function() {
            const serviceRow = document.querySelector('.service-row').cloneNode(true);
            serviceRow.querySelector('.service-select').value = '';
            serviceRow.querySelector('.service-quantity').value = '1';
            serviceRow.querySelector('.service-price').value = '0.00';
            
            // Limpiar campo de descripción si existe
            if (serviceRow.querySelector('.service-description')) {
                serviceRow.querySelector('.service-description').value = '';
            }
            
            // Set up remove button
            const removeBtn = serviceRow.querySelector('.remove-service');
            removeBtn.addEventListener('click', function() {
                this.closest('.service-row').remove();
                updateTotals();
            });
            
            servicesContainer.appendChild(serviceRow);
            updateSelectListeners();
        });

        // Botones de productos eliminados

        document.querySelectorAll('.remove-service').forEach(btn => {
            btn.addEventListener('click', function() {
                // Only remove if there is more than one service row
                if (document.querySelectorAll('.service-row').length > 1) {
                    this.closest('.service-row').remove();
                    updateTotals();
                }
            });
        });

        // Update select, quantity, and price listeners
        function updateSelectListeners() {
            document.querySelectorAll('.service-select, .service-quantity, .service-price').forEach(el => {
                el.addEventListener('change', updateTotals);
                if (el.classList.contains('service-price')) {
                    el.addEventListener('input', updateTotals);
                }
            });
            
            // Update prices when service changes
            document.querySelectorAll('.service-select').forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value && this.selectedIndex > 0) {
                        const option = this.options[this.selectedIndex];
                        const price = option.dataset.price || '0.00';
                        const priceInput = this.closest('.service-row').querySelector('.service-price');
                        priceInput.value = price;
                    }
                });
            });
        }

        // Initialize listeners
        updateSelectListeners();

        // Update paid amount and pending calculation
        document.getElementById('paid_amount').addEventListener('input', updateTotals);

        // Calculate and update totals
        function updateTotals() {
            let subtotal = 0;
            
            // Calculate services subtotal
            document.querySelectorAll('.service-row').forEach(row => {
                const select = row.querySelector('.service-select');
                const quantity = parseInt(row.querySelector('.service-quantity').value) || 0;
                const priceInput = row.querySelector('.service-price');
                
                if (select.value) {
                    const price = parseFloat(priceInput.value) || 0;
                    subtotal += price * quantity;
                }
            });
            
            const tax = subtotal * 0.21;
            const total = subtotal + tax;
            const paid = parseFloat(document.getElementById('paid_amount').value) || 0;
            const pending = total - paid;
            
            // Update display
            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + '€';
            document.getElementById('tax').textContent = tax.toFixed(2) + '€';
            document.getElementById('total').textContent = total.toFixed(2) + '€';
            document.getElementById('paid').textContent = paid.toFixed(2) + '€';
            document.getElementById('pending').textContent = pending.toFixed(2) + '€';
        }

        // Initial calculation
        updateTotals();
    });
</script>
@endpush
