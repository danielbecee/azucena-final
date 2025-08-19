<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Prenda #{{ $orderService->id }}</title>
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
        .item-details {
            margin: 5mm 0;
            padding: 2mm;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
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
            <span class="order-number">TICKET PRENDA #{{ $orderService->id }}</span><br>
            <span class="order-number">PEDIDO #{{ $orderService->order->id }}</span><br>
            Fecha: {{ $orderService->order->order_date->format('d/m/Y H:i') }}<br>
            Entrega: {{ $orderService->order->due_date->format('d/m/Y') }}
        </div>

        <div class="customer-info">
            <strong>Cliente:</strong><br>
            {{ $orderService->order->customer->first_name }} {{ $orderService->order->customer->last_name }}<br>
            @if($orderService->order->customer->email)
            {{ $orderService->order->customer->email }}<br>
            @endif
            @if($orderService->order->customer->phone)
            {{ $orderService->order->customer->phone }}
            @endif
        </div>

        <div class="divider"></div>

        <div class="item-details">
            <h4 style="margin-top: 0; text-align: center;">DETALLE DE SERVICIO</h4>
            <p><strong>Servicio:</strong> {{ $orderService->service_name }}</p>
            @if($orderService->quantity > 1)
            <p><strong>Cantidad:</strong> {{ $orderService->quantity }}</p>
            @endif
            <p><strong>Precio:</strong> {{ number_format($orderService->price, 2) }}€</p>
            <p><strong>Total:</strong> {{ number_format($orderService->subtotal, 2) }}€</p>
            @if($orderService->description)
            <p><strong>Notas:</strong> {{ $orderService->description }}</p>
            @endif
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p>Estado del pedido: {{ $orderService->order->orderState->name }}</p>
            @if($orderService->order->notes)
            <p>Notas: {{ $orderService->order->notes }}</p>
            @endif
            <p>¡Gracias por su confianza!</p>
        </div>
    </div>

    <!-- Botones de impresión solo visibles en pantalla -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print();" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; margin-right: 10px;">
            Imprimir
        </button>
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
