<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets Individuales - Pedido #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .ticket-container {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }
        .ticket-preview {
            padding: 15px;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background-color: #f1f1f1;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }
        .action-btns {
            margin-top: 30px;
            margin-bottom: 50px;
        }
        @media print {
            .no-print, .no-print * {
                display: none !important;
            }
            .ticket-container {
                page-break-after: always;
                margin: 0;
                border: none;
                box-shadow: none;
                width: 80mm;
                margin: 0 auto;
            }
            .container, .row, .col {
                padding: 0;
                margin: 0;
                width: 100%;
            }
            body {
                width: 80mm;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row mb-4 no-print">
            <div class="col">
                <h1>Tickets Individuales - Pedido #{{ $order->id }}</h1>
                <p>Cliente: {{ $order->customer->first_name }} {{ $order->customer->last_name }}</p>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row action-btns no-print">
            <div class="col">
                <button onclick="printAllTickets()" class="btn btn-primary me-2">
                    <i class="fas fa-print"></i> Imprimir Todos los Tickets
                </button>
                <a href="{{ route('admin.orders.ticket', $order->id) }}" class="btn btn-info me-2">
                    <i class="fas fa-receipt"></i> Ver Ticket Completo
                </a>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Pedido
                </a>
            </div>
        </div>

        <div class="row">
            @foreach($order->services as $service)
            <div class="col-12 mb-4">
                <div class="ticket-container">
                    <div class="ticket-header no-print">
                        <div>
                            <span><strong>Servicio:</strong> {{ $service->service_name }}</span>
                            @if($service->description)
                            <br><small><i>{{ $service->description }}</i></small>
                            @endif
                        </div>
                        <a href="{{ route('admin.orders.service_ticket', $service->id) }}" target="_blank" class="btn btn-sm btn-outline-primary no-print">
                            <i class="fas fa-print"></i> Imprimir este ticket
                        </a>
                    </div>
                    <div class="ticket-preview">
                        <iframe src="{{ route('admin.orders.service_ticket', $service->id) }}" frameborder="0" 
                            style="width: 100%; height: 300px; border: none;" class="no-print"></iframe>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Contenido imprimible de todos los tickets -->
        <div class="d-none">
            @foreach($order->services as $service)
            <div class="printable-ticket" id="printable-ticket-{{ $service->id }}">
                <!-- El contenido real del ticket se inserta aquí mediante JavaScript -->
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous"></script>
    <script>
        // Cargar el contenido real de cada ticket para impresión
        document.addEventListener('DOMContentLoaded', function() {
            const iframes = document.querySelectorAll('iframe');
            
            iframes.forEach(function(iframe) {
                iframe.onload = function() {
                    const serviceId = iframe.src.split('/').pop();
                    const printableTicket = document.getElementById('printable-ticket-' + serviceId);
                    
                    if (printableTicket) {
                        try {
                            const iframeContent = iframe.contentDocument.querySelector('.ticket-container');
                            if (iframeContent) {
                                printableTicket.innerHTML = iframeContent.outerHTML;
                            }
                        } catch (e) {
                            console.error('No se pudo cargar el contenido del iframe', e);
                        }
                    }
                };
            });
        });

        function printAllTickets() {
            // Ocultar todo excepto los tickets
            const printableTickets = document.querySelectorAll('.printable-ticket');
            printableTickets.forEach(function(ticket) {
                ticket.classList.remove('d-none');
            });
            
            // Imprimir y luego restaurar la vista
            setTimeout(function() {
                window.print();
                printableTickets.forEach(function(ticket) {
                    ticket.classList.add('d-none');
                });
            }, 500);
        }
    </script>
</body>
</html>
