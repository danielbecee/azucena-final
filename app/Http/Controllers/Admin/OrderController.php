<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
// OrderItem eliminado
use App\Models\OrderService;
use App\Models\Payment;
use App\Models\ServiceCategory;
use App\Models\ServiceItem;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Cambia rápidamente el estado de un pedido
     */
    public function changeState(Request $request, Order $order)
    {
        $validated = $request->validate([
            'state_id' => 'required|exists:states,id',
        ]);
        
        try {
            $oldState = $order->orderState->name;
            $order->order_state_id = $request->state_id;
            $order->save();
            
            $newState = State::find($request->state_id)->name;
            
            // Si el estado es "Listo" y se ha proporcionado un número de WhatsApp, enviar mensaje
            if ($request->has('send_whatsapp') && $request->send_whatsapp && $newState == 'Listo') {
                if ($order->customer && $order->customer->phone) {
                    $phone = preg_replace('/[^0-9]/', '', $order->customer->phone);
                    if (!empty($phone)) {
                        // Formatear el mensaje
                        $message = "Hola {$order->customer->first_name}, tu pedido #{$order->id} ya está listo para recoger. Gracias por confiar en nosotros!";
                        $whatsappUrl = "https://api.whatsapp.com/send?phone={$phone}&text=" . urlencode($message);
                        
                        // Retornar con URL de WhatsApp para que se abra automáticamente
                        return redirect()->route('admin.orders.index')
                            ->with('success', "Estado cambiado de {$oldState} a {$newState}")
                            ->with('whatsapp_url', $whatsappUrl);
                    }
                }
            }
            
            return back()->with('success', "Estado cambiado de {$oldState} a {$newState}");
        } 
        catch (\Exception $e) {
            Log::error('Error al cambiar estado del pedido: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al cambiar el estado del pedido: ' . $e->getMessage()]);
        }
    }
    /**
     * Muestra el listado de pedidos con filtros acumulativos
     */
    public function index(Request $request)
    {
        // Obtener todos los estados de pedido y pago para los filtros
        $orderStates = State::where('type', 'order')->orderBy('name')->get();
        $paymentStates = State::where('type', 'payment')->orderBy('name')->get();
        $customers = Customer::orderBy('first_name')->get();
        $serviceCategories = ServiceCategory::orderBy('name')->get();
        
        // Iniciar la consulta
        $query = Order::with(['customer', 'orderState', 'paymentState', 'services']);
        
        // Aplicar filtros si existen
        // Filtro por ID específico
        if ($request->filled('ticket_id')) {
            $query->where('id', $request->ticket_id);
        }
        
        // Filtro por estado de pedido
        if ($request->filled('order_state')) {
            $query->whereIn('order_state_id', $request->order_state);
        }
        
        // Filtro por estado de pago
        if ($request->filled('payment_state')) {
            $query->whereIn('payment_state_id', $request->payment_state);
        }
        
        // Filtro por cliente
        if ($request->filled('customer')) {
            $query->whereIn('customer_id', $request->customer);
        }
        
        // Filtro por rango de fechas de pedido
        if ($request->filled('date_from')) {
            $query->where('order_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('order_date', '<=', $request->date_to.' 23:59:59');
        }
        
        // Filtro por rango de fechas de entrega
        if ($request->filled('due_date_from')) {
            $query->where('due_date', '>=', $request->due_date_from);
        }
        
        if ($request->filled('due_date_to')) {
            $query->where('due_date', '<=', $request->due_date_to.' 23:59:59');
        }
        
        // Filtro por rango de importes
        if ($request->filled('amount_from')) {
            $query->where('total_amount', '>=', $request->amount_from);
        }
        
        if ($request->filled('amount_to')) {
            $query->where('total_amount', '<=', $request->amount_to);
        }
        
        // Filtro por categoría de servicio
        if ($request->filled('service_category')) {
            $query->whereHas('services', function($q) use ($request) {
                $q->whereIn('service_category_id', $request->service_category);
            });
        }
        
        // Ordenación
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        // Validar que el campo de ordenación es válido
        $validSortFields = ['id', 'order_date', 'due_date', 'total_amount', 'created_at'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'created_at';
        }
        
        $query->orderBy($sortField, $sortOrder);
        
        // Ejecutar la consulta con paginación
        $orders = $query->paginate(10)->withQueryString();
        
        // Obtener el rango de precios para los filtros
        $minAmount = Order::min('total_amount') ?: 0;
        $maxAmount = Order::max('total_amount') ?: 100;
        
        // Datos para los filtros seleccionados actualmente
        $filters = [
            'ticket_id' => $request->ticket_id,
            'order_state' => $request->order_state ?? [],
            'payment_state' => $request->payment_state ?? [],
            'customer' => $request->customer ?? [],
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'due_date_from' => $request->due_date_from,
            'due_date_to' => $request->due_date_to,
            'amount_from' => $request->amount_from,
            'amount_to' => $request->amount_to,
            'service_category' => $request->service_category ?? [],
            'sort_field' => $sortField,
            'sort_order' => $sortOrder,
        ];
            
        return view('admin.orders.index', compact(
            'orders', 
            'orderStates', 
            'paymentStates',
            'customers',
            'serviceCategories',
            'minAmount',
            'maxAmount',
            'filters'
        ));
    }

    /**
     * Muestra el formulario para crear un pedido
     */
    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $serviceCategories = ServiceCategory::with('serviceItems')->orderBy('name')->get();
        $orderStates = State::where('type', 'order')->orderBy('name')->get();
        $paymentStates = State::where('type', 'payment')->orderBy('name')->get();
        
        return view('admin.orders.create', compact('customers', 'serviceCategories', 'orderStates', 'paymentStates'));
    }

    /**
     * Almacena un nuevo pedido
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validated = $request->validate([
            'customer_id' => 'required_without:new_customer',
            'first_name' => 'required_if:new_customer,1',
            'last_name' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'order_date' => 'required|date',
            'due_date' => 'required|date',
            'service_item_id' => 'required|array',
            'service_item_price' => 'nullable|array',
            'payment_state_id' => 'required|exists:states,id',
            'order_state_id' => 'required|exists:states,id',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        // Validar que hay al menos un servicio
        if (empty($request->service_item_id)) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Debe agregar al menos un servicio al pedido.']);
        }

        try {
            // Iniciar transacción
            DB::beginTransaction();
            
            // Crear cliente nuevo si es necesario
            $customerId = $request->customer_id;
            if ($request->has('new_customer') && $request->new_customer == 1) {
                $customer = new Customer();
                $customer->first_name = $request->first_name;
                $customer->last_name = $request->last_name;
                $customer->email = $request->email;
                $customer->phone = $request->phone;
                $customer->save();
                
                $customerId = $customer->id;
            }
            
            // Calcular total
            $total = 0;
            
            // Servicios específicos
            $serviceItems = [];
            if ($request->has('service_item_id')) {
                foreach ($request->service_item_id as $key => $serviceItemId) {
                    if (!empty($serviceItemId)) {
                        $serviceItem = ServiceItem::find($serviceItemId);
                        if ($serviceItem) {
                            $price = $request->service_item_price[$key] ?? $serviceItem->price;
                            $subtotal = $price; // Ya no hay cantidad multiplicada para servicios específicos
                            $total += $subtotal;
                            
                            // Obtener la descripción específica para este servicio
                            $description = isset($request->service_description[$key]) ? $request->service_description[$key] : null;
                            
                            $serviceItems[] = [
                                'service_item_id' => $serviceItemId,
                                'service_category_id' => $serviceItem->service_category_id,
                                'service_name' => $serviceItem->name, // Guardamos el nombre para referencia histórica
                                'price' => $price,
                                'subtotal' => $subtotal,
                                'description' => $description
                            ];
                        }
                    }
                }
            }
            
            // Crear el pedido
            $order = new Order();
            $order->customer_id = $customerId;
            $order->order_date = $request->order_date;
            $order->due_date = $request->due_date;
            $order->order_state_id = $request->order_state_id;
            $order->payment_state_id = $request->payment_state_id;
            $order->total_amount = $total;
            $order->paid_amount = $request->paid_amount ?? 0;
            $order->notes = $request->notes;
            $order->save();
            
            // Guardar servicios específicos del pedido
            foreach ($serviceItems as $item) {
                OrderService::create([
                    'order_id' => $order->id,
                    'service_item_id' => $item['service_item_id'],
                    'service_category_id' => $item['service_category_id'],
                    'service_name' => $item['service_name'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'description' => $item['description'] ?? null
                ]);
            }
            
            // Si hay pago inicial, registrarlo
            if (!empty($request->paid_amount) && $request->paid_amount > 0) {
                $payment = new Payment();
                $payment->order_id = $order->id;
                $payment->amount = $request->paid_amount;
                $payment->payment_date = Carbon::now();
                $payment->payment_method = $request->payment_method ?? 'efectivo';
                $payment->save();
            }
            
            // Confirmar transacción
            DB::commit();
            
            // Redireccionar a imprimir ticket o a índice
            if ($request->has('print_ticket') && $request->print_ticket) {
                return redirect()->route('admin.orders.ticket', $order->id)
                    ->with('success', 'Pedido creado correctamente.');
            }
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Pedido creado correctamente.');
        } 
        catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            Log::error('Error al crear pedido: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al procesar el pedido: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra un pedido específico
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'orderState', 'paymentState', 'services.serviceItem.serviceCategory', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Muestra el formulario para editar un pedido
     */
    public function edit(Order $order)
    {
        $customers = Customer::orderBy('first_name')->get();
        $serviceCategories = ServiceCategory::with('serviceItems')->orderBy('name')->get();
        $orderStates = State::where('type', 'order')->orderBy('name')->get();
        $paymentStates = State::where('type', 'payment')->orderBy('name')->get();
        $services = ServiceItem::orderBy('name')->get();
        
        $order->load(['services.serviceItem.serviceCategory']);
        
        return view('admin.orders.edit', compact('order', 'customers', 'serviceCategories', 'orderStates', 'paymentStates', 'services'));
    }

    /**
     * Actualiza un pedido existente
     */
    public function update(Request $request, Order $order)
    {
        // Validación de datos
        $validated = $request->validate([
            'order_date' => 'required|date',
            'due_date' => 'required|date',
            'service_item_id' => 'nullable|array',
            'service_item_price' => 'nullable|array',
            'payment_state_id' => 'required|exists:states,id',
            'order_state_id' => 'required|exists:states,id',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        // Validar que hay al menos un servicio
        if (empty($request->service_item_id)) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Debe agregar al menos un servicio al pedido.']);
        }
        
        try {
            // Iniciar transacción
            DB::beginTransaction();
            
            // Calcular total
            $total = 0;
            
            // Servicios específicos
            $serviceItems = [];
            if ($request->has('service_item_id')) {
                foreach ($request->service_item_id as $key => $serviceItemId) {
                    if (!empty($serviceItemId)) {
                        $serviceItem = ServiceItem::find($serviceItemId);
                        if ($serviceItem) {
                            $price = $request->service_item_price[$key] ?? $serviceItem->price;
                            $subtotal = $price; // Ya no hay cantidad multiplicada para servicios específicos
                            $total += $subtotal;
                            
                            // Obtener la descripción específica para este servicio
                            $description = isset($request->service_description[$key]) ? $request->service_description[$key] : null;
                            
                            $serviceItems[] = [
                                'service_item_id' => $serviceItemId,
                                'service_category_id' => $serviceItem->service_category_id,
                                'service_name' => $serviceItem->name,
                                'price' => $price,
                                'subtotal' => $subtotal,
                                'description' => $description
                            ];
                        }
                    }
                }
            }
            
            // Actualizar el pedido
            $order->order_date = $request->order_date;
            $order->due_date = $request->due_date;
            $order->order_state_id = $request->order_state_id;
            $order->payment_state_id = $request->payment_state_id;
            $order->total_amount = $total;
            $order->notes = $request->notes;
            $order->save();
            
            // Eliminar servicios existentes
            OrderService::where('order_id', $order->id)->delete();
            
            // Guardar servicios específicos del pedido
            foreach ($serviceItems as $item) {
                OrderService::create([
                    'order_id' => $order->id,
                    'service_item_id' => $item['service_item_id'],
                    'service_category_id' => $item['service_category_id'],
                    'service_name' => $item['service_name'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'description' => $item['description'] ?? null
                ]);
            }
            
            // Confirmar transacción
            DB::commit();
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Pedido actualizado correctamente.');
        } 
        catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            Log::error('Error al actualizar pedido: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el pedido: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina un pedido
     */
    public function destroy(Order $order)
    {
        try {
            // Iniciar transacción
            DB::beginTransaction();
            
            // Eliminar productos y servicios asociados
            OrderService::where('order_id', $order->id)->delete();
            
            // Eliminar pagos asociados
            Payment::where('order_id', $order->id)->delete();
            
            // Eliminar el pedido
            $order->delete();
            
            // Confirmar transacción
            DB::commit();
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Pedido eliminado correctamente.');
        } 
        catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            Log::error('Error al eliminar pedido: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error al eliminar el pedido: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el ticket para imprimir
     */
    public function ticket(Order $order)
    {
        $order->load(['customer', 'orderState', 'paymentState', 'services', 'payments']);
        return view('admin.orders.ticket', compact('order'));
    }
    
    /**
     * Muestra la página con todos los tickets individuales del pedido
     */
    public function individualTickets(Order $order)
    {
        $order->load(['customer', 'orderState', 'paymentState', 'services', 'payments']);
        return view('admin.orders.individual_tickets', compact('order'));
    }
    
    /**
     * Muestra un ticket individual para un servicio específico
     */
    public function serviceTicket(OrderService $orderService)
    {
        $orderService->load(['order.customer', 'order.orderState', 'order.paymentState']);
        return view('admin.orders.service_ticket', compact('orderService'));
    }

    /**
     * Registra un nuevo pago para un pedido
     */
    public function addPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Crear el pago
            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->amount = $request->amount;
            $payment->payment_date = $request->payment_date;
            $payment->payment_method = $request->payment_method;
            $payment->save();
            
            // Actualizar el monto pagado del pedido
            $totalPaid = Payment::where('order_id', $order->id)->sum('amount');
            $order->paid_amount = $totalPaid;
            
            // Actualizar estado de pago si corresponde
            if ($totalPaid >= $order->total_amount) {
                // Buscar estado "Pagado"
                $paidState = State::where('type', 'payment')
                    ->where('name', 'Pagado')
                    ->first();
                
                if ($paidState) {
                    $order->payment_state_id = $paidState->id;
                }
            } 
            else if ($totalPaid > 0) {
                // Buscar estado "Pago parcial"
                $partialState = State::where('type', 'payment')
                    ->where('name', 'Pago parcial')
                    ->first();
                    
                if ($partialState) {
                    $order->payment_state_id = $partialState->id;
                }
            }
            
            $order->save();
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Pago registrado correctamente.');
        } 
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar pago: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Cambia rápidamente el estado de un pedido a "Listo"
     */
    public function markAsReady(Order $order)
    {
        try {
            // Buscar el estado "Listo"
            $readyState = State::where('type', 'order')
                ->where('name', 'Listo')
                ->first();
            
            if (!$readyState) {
                return back()->withErrors(['error' => 'No se encontró el estado "Listo"']);
            }
            
            $oldState = $order->orderState->name;
            $order->order_state_id = $readyState->id;
            $order->save();
            
            return redirect()->back()->with('success', "Pedido marcado como Listo");
        } 
        catch (\Exception $e) {
            Log::error('Error al marcar pedido como listo: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al marcar el pedido como listo: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Cambia rápidamente el estado de un pedido a "En taller"
     */
    public function markAsInWorkshop(Order $order)
    {
        try {
            // Buscar el estado "En taller"
            $workshopState = State::where('type', 'order')
                ->where('name', 'En taller')
                ->first();
            
            if (!$workshopState) {
                return back()->withErrors(['error' => 'No se encontró el estado "En taller"']);
            }
            
            $oldState = $order->orderState->name;
            $order->order_state_id = $workshopState->id;
            $order->save();
            
            return redirect()->back()->with('success', "Estado cambiado de {$oldState} a En taller");
        } 
        catch (\Exception $e) {
            Log::error('Error al marcar pedido en taller: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al marcar el pedido en taller: ' . $e->getMessage()]);
        }
    }
}
