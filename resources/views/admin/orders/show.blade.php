@extends('layouts.admin')

@section('title', 'Detalles del Pedido')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Detalles del Pedido #{{ $order->id }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#pagoModal" data-order-id="{{ $order->id }}">
                    <i class="fas fa-euro-sign"></i> Registrar Pago
                </button>
                <button type="button" class="btn btn-primary print-ticket-btn" data-order-id="{{ $order->id }}">
                    <i class="fas fa-print"></i> Imprimir Ticket
                </button>
                <a href="{{ route('admin.orders.individual_tickets', $order->id) }}" class="btn btn-info">
                    <i class="fas fa-tags"></i> Tickets Prendas
                </a>
                <button type="button" class="btn btn-danger delete-btn">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
    <!-- Información del pedido -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Información del Pedido</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th style="width: 30%">ID:</th>
                        <td>{{ $order->id }}</td>
                    </tr>
                    <tr>
                        <th>Concepto:</th>
                        <td>{{ $order->concept }}</td>
                    </tr>
                    <tr>
                        <th>Fecha del pedido:</th>
                        <td>{{ $order->order_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Fecha de entrega:</th>
                        <td>{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : 'No especificada' }}</td>
                    </tr>
                    <tr>
                        <th>Estado del pedido:</th>
                        <td>
                            <span class="badge bg-{{ $order->orderState->color ?? 'secondary' }}">
                                {{ $order->orderState->name ?? 'N/A' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Estado del pago:</th>
                        <td>
                            <span class="badge bg-{{ $order->paymentState->color ?? 'secondary' }}">
                                {{ $order->paymentState->name ?? 'N/A' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td>{{ number_format($order->total_amount, 2) }} €</td>
                    </tr>
                    <tr>
                        <th>Pagado:</th>
                        <td>{{ number_format($order->paid_amount, 2) }} € 
                            @if($order->total_amount > $order->paid_amount)
                                <span class="badge bg-danger ms-2">Pendiente: {{ number_format($order->total_amount - $order->paid_amount, 2) }} €</span>
                            @endif
                        </td>
                    </tr>
                </table>
                
                @if(!empty($order->notes))
                    <div class="mt-3">
                        <h6>Notas:</h6>
                        <p class="p-2 bg-light rounded">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Información del cliente -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cliente</h5>
                <a href="{{ route('admin.customers.show', $order->customer) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-external-link-alt"></i> Ver Perfil
                </a>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th style="width: 30%">ID:</th>
                        <td>{{ $order->customer->id }}</td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $order->customer->email }}</td>
                    </tr>
                    <tr>
                        <th>Teléfono:</th>
                        <td>{{ $order->customer->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Dirección:</th>
                        <td>
                            @if($order->customer->address)
                                {{ $order->customer->address }}<br>
                                {{ $order->customer->postal_code }} {{ $order->customer->city }}<br>
                                {{ $order->customer->province }}
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- La sección de productos ha sido eliminada -->
    
    <!-- Servicios del pedido -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Servicios</h5>
            </div>
            <div class="card-body">
                @if($order->services->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Servicio</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->services as $service)
                                <tr>
                                    <td>{{ $service->service_name }}</td>
                                    <td>{{ number_format($service->price, 2) }} €</td>
                                    <td>{{ $service->quantity }}</td>
                                    <td>{{ number_format($service->subtotal, 2) }} €</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center my-3">No hay servicios en este pedido</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Historial de pagos -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Historial de Pagos</h5>
            </div>
            <div class="card-body">
                @if($order->payments && $order->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Método</th>
                                    <th>Cantidad</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>{{ number_format($payment->amount, 2) }} €</td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total pagado:</th>
                                    <th>{{ number_format($order->payments->sum('amount'), 2) }} €</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">Pendiente:</th>
                                    <th>{{ number_format($order->total_amount - $order->payments->sum('amount'), 2) }} €</th>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-center my-3">No hay pagos registrados</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Formulario de eliminación oculto -->
<form id="delete-form" action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal para registrar pagos -->
@include('admin.orders.partials.payment-modal')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal de pago cuando se abre
    const pagoModal = document.getElementById('pagoModal');
    if (pagoModal) {
        pagoModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const orderId = button.getAttribute('data-order-id');
            
            // Configurar el formulario con los datos del pedido
            const form = document.getElementById('registrarPagoForm');
            form.action = `/admin/orders/${orderId}/payments`;
            
            // Establecer los valores en el modal
            document.getElementById('pago_cliente_nombre').textContent = '{{ $order->customer->first_name }} {{ $order->customer->last_name }}';
            document.getElementById('pago_pedido_concepto').textContent = '{{ $order->concept }}';
            document.getElementById('pago_pedido_total').textContent = '{{ number_format($order->total_amount, 2) }} €';
            document.getElementById('pago_pedido_pendiente').textContent = '{{ number_format($order->total_amount - $order->paid_amount, 2) }} €';
            
            // Establecer el monto pendiente como valor por defecto
            document.getElementById('payment_amount').value = {{ $order->total_amount - $order->paid_amount }};
            document.getElementById('payment_amount').max = {{ $order->total_amount - $order->paid_amount }};
        });
    }
    
    // Configurar botón de impresión
    const printButtons = document.querySelectorAll('.print-ticket-btn');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            window.open(`/admin/orders/${orderId}/ticket`, '_blank');
        });
    });
    
    // Configurar botón de eliminación si existe
    const deleteBtn = document.querySelector('.delete-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: '¿Eliminar pedido?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });
    }
});
</script>
@endpush

</div>
@endsection
