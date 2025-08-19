<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\ServiceItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de administración principal
     */
    public function index(Request $request)
    {
        // Obtener la fecha seleccionada o usar la fecha actual
        $selectedDate = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        
        // Consulta base para los pedidos
        $ordersQuery = Order::with(['customer', 'orderState', 'services'])
            ->orderBy('created_at', 'desc');
        
        // Filtrar por fecha seleccionada
        $todayOrders = clone $ordersQuery;
        $todayOrders = $todayOrders->whereDate('order_date', $selectedDate)->get();
        
        // Estadísticas para el dashboard
        $stats = [
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
            'total_services' => ServiceItem::count(),
            'recent_orders' => $ordersQuery->take(5)->get(),
            'today_orders' => Order::whereDate('order_date', Carbon::today())->count(),
            'pending_payment' => Order::where('payment_state_id', 1)->count(), // Asumiendo que id=1 es pendiente
        ];

        return view('admin.dashboard.index', compact('stats', 'todayOrders', 'selectedDate'));
    }
}
