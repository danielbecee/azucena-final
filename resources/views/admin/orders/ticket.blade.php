<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            width: 80mm;
            break-inside: avoid;
        }
        .ticket-container {
            padding: 5mm;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .logo {
            max-width: 60mm;
            margin-bottom: 3mm;
        }
        .business-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2mm;
        }
        .business-info {
            margin-bottom: 3mm;
        }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 3mm 0;
        }
        .order-info {
            margin-bottom: 3mm;
        }
        .order-number {
            font-weight: bold;
        }
        .customer-info {
            margin-bottom: 3mm;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }
        .table th,
        .table td {
            text-align: left;
            padding: 1mm;
        }
        .table th:last-child,
        .table td:last-child {
            text-align: right;
        }
        .totals {
            text-align: right;
            margin-bottom: 3mm;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 5mm;
            font-size: 10px;
        }
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
                page-break-inside: avoid;
                page-break-after: avoid;
                page-break-before: avoid;
            }
            body {
                width: 80mm;
            }
            .ticket-container {
                page-break-inside: avoid;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Azucena" class="logo">
            <div class="business-name">Azucena Vidaurre Delgadillo</div>
            <div class="business-info">
                NIF: 54708154-D<br>
                Calle Mallorca 180<br>
                08036 Barcelona<br>
                Tel: 642 10 60 31
            </div>
        </div>

        <div class="divider"></div>

        <div class="order-info">
            <span class="order-number">PEDIDO #{{ $order->id }}</span><br>
            Fecha: {{ $order->order_date->format('d/m/Y H:i') }}<br>
            Entrega: {{ $order->due_date->format('d/m/Y') }}
        </div>

        <div class="customer-info">
            <strong>Cliente:</strong><br>
            {{ $order->customer->first_name }} {{ $order->customer->last_name }}<br>
            {{ $order->customer->email }}<br>
            {{ $order->customer->phone }}
        </div>

        <div class="divider"></div>

        <div class="items">
            <strong>SERVICIOS</strong>
            <table class="table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Cant.</th>
                        <th>Precio</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->services as $service)
                    <tr>
                        <td>
                            {{ $service->service_name }}
                            @if($service->description)
                            <br><small><i>{{ $service->description }}</i></small>
                            @endif
                        </td>
                        <td>{{ $service->quantity }}</td>
                        <td>{{ number_format($service->price, 2) }}€</td>
                        <td>{{ number_format($service->subtotal, 2) }}€</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="divider"></div>

        <div class="totals">
            <div>Subtotal: {{ number_format($order->total_amount / 1.21, 2) }}€</div>
            <div>IVA (21%): {{ number_format($order->total_amount - ($order->total_amount / 1.21), 2) }}€</div>
            <div class="total-row">TOTAL: {{ number_format($order->total_amount, 2) }}€</div>
            <div>Pagado: {{ number_format($order->paid_amount, 2) }}€</div>
            <div>Pendiente: {{ number_format($order->total_amount - $order->paid_amount, 2) }}€</div>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>Estado del pedido: {{ $order->orderState->name }}</p>
            <p>Estado del pago: {{ $order->paymentState->name }}</p>
            @if($order->notes)
            <p>Notas: {{ $order->notes }}</p>
            @endif
            <p>¡Gracias por su compra!</p>
        </div>
    </div>

    <!-- Botones de impresión solo visibles en pantalla -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print();" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; margin-right: 10px;">
            Imprimir
        </button>
        <a href="{{ route('admin.orders.individual_tickets', $order->id) }}" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer; margin-right: 10px; text-decoration: none;">
            Tickets Individuales
        </a>
        <button onclick="window.close();" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer;">
            Cerrar
        </button>
    </div>

    <script>
        // Autoimprime el ticket cuando se carga
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
