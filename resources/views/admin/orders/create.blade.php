@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Nuevo Pedido</h1>
            <p class="text-muted">Complete el formulario para crear un nuevo pedido</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al listado
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
            <form action="{{ route('admin.orders.store') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Datos del Cliente</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Cliente</label>
                                    <select class="form-select searchable" id="customer_id" name="customer_id" required>
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ (old('customer_id') == $customer->id || (isset($selectedCustomerId) && $selectedCustomerId == $customer->id)) ? 'selected' : '' }}>
                                                {{ $customer->first_name }} {{ $customer->last_name }} - {{ $customer->phone }} - {{ $customer->email }}
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
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Fecha de entrega</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" 
                                        value="{{ old('due_date') }}">
                                </div>
                                <!-- Campos ocultos para mantener compatibilidad con el backend -->
                                <input type="hidden" id="order_date" name="order_date" value="{{ date('Y-m-d') }}">
                                <input type="hidden" id="order_state_id" name="order_state_id" value="1"> <!-- Asumiendo que 1 es el estado inicial -->
                                <input type="hidden" id="notes" name="notes" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Servicios</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-service">
                                    <i class="fas fa-plus"></i> Añadir servicio
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="services-container">
                                    <!-- Los servicios se añadirán aquí dinámicamente -->
                                    <div class="service-row row mb-3">
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Categoría</label>
                                            <select class="form-select service-category-select">
                                                <option value="">Seleccione categoría</option>
                                                @foreach($serviceCategories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Servicio</label>
                                            <select class="form-select service-item-select" name="service_item_id[]" disabled>
                                                <option value="">Seleccione servicio</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1 mb-2">
                                            <label class="form-label">Cant.</label>
                                            <input type="number" step="1" min="1" class="form-control service-item-quantity-input" name="service_item_quantity[]" value="1">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Precio €</label>
                                            <input type="number" step="0.01" min="0" class="form-control service-item-price-input" name="service_item_price[]" value="0.00">
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Nota</label>
                                            <input type="text" class="form-control service-note" name="service_description[]" placeholder="Descripción...">
                                        </div>
                                        <div class="col-md-1 mb-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Información de Pago</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="payment_state_id" class="form-label">Estado del pago</label>
                                        <select class="form-select" id="payment_state_id" name="payment_state_id">
                                            @foreach($paymentStates as $state)
                                                <option value="{{ $state->id }}" {{ old('payment_state_id') == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="payment_method" class="form-label">Método de pago</label>
                                        <select class="form-select" id="payment_method" name="payment_method">
                                            <option value="efectivo" {{ old('payment_method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                            <option value="tarjeta" {{ old('payment_method') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                            <option value="transferencia" {{ old('payment_method') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="paid_amount" class="form-label">Cantidad pagada</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="paid_amount" name="paid_amount" 
                                                min="0" step="0.01" value="{{ old('paid_amount', '0.00') }}">
                                            <span class="input-group-text">€</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="print_ticket" name="print_ticket" value="1" 
                                        {{ old('print_ticket') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="print_ticket">
                                        Imprimir ticket al guardar
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                                    <span id="paid">0.00€</span>
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
                        <i class="fas fa-save me-1"></i> Guardar Pedido
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
     

        // Add service row
        const servicesContainer = document.getElementById('services-container');
        const addServiceBtn = document.getElementById('add-service');
        
        addServiceBtn.addEventListener('click', function() {
            const serviceRow = document.querySelector('.service-row').cloneNode(true);
            serviceRow.querySelector('.service-category-select').value = '';
            serviceRow.querySelector('.service-item-select').value = '';
            serviceRow.querySelector('.service-item-select').disabled = true;
            serviceRow.querySelector('.service-item-price-input').value = '0.00';
            serviceRow.querySelector('.service-item-quantity-input').value = '1'; // Establecer cantidad por defecto
            serviceRow.querySelector('.service-note').value = ''; // Resetear el campo de notas
            
            // Set up remove button
            const removeBtn = serviceRow.querySelector('.remove-service');
            removeBtn.addEventListener('click', function() {
                this.closest('.service-row').remove();
                updateTotals();
            });
            
            servicesContainer.appendChild(serviceRow);
            updateServiceListeners();
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
        
        // Handle service category selection to load service items
        function handleCategorySelect(categorySelect) {
            const serviceRow = categorySelect.closest('.service-row');
            const itemSelect = serviceRow.querySelector('.service-item-select');
            const priceInput = serviceRow.querySelector('.service-item-price');
            
            // Reset and disable service item select if no category selected
            if (!categorySelect.value) {
                itemSelect.innerHTML = '<option value="">Seleccione servicio</option>';
                itemSelect.disabled = true;
                priceInput.value = '0';
                updateTotals();
                return;
            }
            
            // Get service items for selected category via AJAX
            fetch(`/admin/servicios-por-categoria/${categorySelect.value}`)
                .then(response => response.json())
                .then(data => {
                    // Enable and populate service item dropdown
                    itemSelect.disabled = false;
                    itemSelect.innerHTML = '<option value="">Seleccione servicio</option>';
                    
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.dataset.price = item.price;
                        option.textContent = `${item.name} - ${parseFloat(item.price).toFixed(2)}€`;
                        itemSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error cargando servicios:', error));
        }
        
        // Handle service item selection to update price
        function handleServiceItemSelect(itemSelect) {
            const serviceRow = itemSelect.closest('.service-row');
            const priceInput = serviceRow.querySelector('.service-item-price-input');
            
            if (itemSelect.value && itemSelect.selectedIndex > 0) {
                const option = itemSelect.options[itemSelect.selectedIndex];
                priceInput.value = option.dataset.price || '0.00';
            } else {
                priceInput.value = '0.00';
            }
            
            updateTotals();
        }

        // Update listeners
        function updateSelectListeners() {
            // Productos eliminados
        }
        
        // Update service category and item listeners
        function updateServiceListeners() {
            // Setup category change listeners
            document.querySelectorAll('.service-category-select').forEach(select => {
                select.addEventListener('change', function() {
                    handleCategorySelect(this);
                });
            });
            
            // Setup service item listeners
            document.querySelectorAll('.service-item-select').forEach(select => {
                select.addEventListener('change', function() {
                    handleServiceItemSelect(this);
                });
            });
            
            // Setup price input change listeners
            document.querySelectorAll('.service-item-price-input').forEach(input => {
                input.addEventListener('input', function() {
                    updateTotals();
                });
            });
            
            // Setup quantity input change listeners
            document.querySelectorAll('.service-item-quantity-input').forEach(input => {
                input.addEventListener('input', function() {
                    updateTotals();
                });
            });
        }

        // Initialize listeners
        updateSelectListeners();
        updateServiceListeners();

        // Update paid amount and pending calculation
        document.getElementById('paid_amount').addEventListener('input', updateTotals);

        // Calculate and update totals
        function updateTotals() {
            let subtotal = 0;
            
            // Productos eliminados
            
            // Calculate services subtotal
            document.querySelectorAll('.service-row').forEach(row => {
                const select = row.querySelector('.service-item-select');
                const priceInput = row.querySelector('.service-item-price-input');
                const quantityInput = row.querySelector('.service-item-quantity-input');
                
                if (select.value && !select.disabled) {
                    const price = parseFloat(priceInput.value) || 0;
                    const quantity = parseInt(quantityInput.value) || 1;
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
