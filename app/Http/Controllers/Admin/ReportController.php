<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Muestra la página de reportes de ventas
     */
    public function sales(Request $request)
    {
        // Datos para la gráfica de ventas diarias (últimos 7 días)
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $dailySales = Order::select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('order_date', '>=', $startDate)
            ->whereDate('order_date', '<=', $endDate)
            ->groupBy(DB::raw('DATE(order_date)'))
            ->orderBy('date')
            ->get();
            
        // Asegurar que hay datos para todos los días
        $dailySalesData = [];
        $labels = [];
        
        // Crear un período de 7 días
        $period = CarbonPeriod::create($startDate, $endDate);
        
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $formattedDate = $date->format('d/m');
            $labels[] = $formattedDate;
            
            // Buscar si hay ventas para esta fecha
            $sale = $dailySales->firstWhere('date', $dateString);
            $dailySalesData[] = $sale ? round($sale->total, 2) : 0;
        }
        
        // Datos para la gráfica de ventas mensuales (últimos 12 meses)
        $startMonth = Carbon::now()->subMonths(11)->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();
        
        $monthlySales = Order::select(
                DB::raw('YEAR(order_date) as year'),
                DB::raw('MONTH(order_date) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereDate('order_date', '>=', $startMonth)
            ->whereDate('order_date', '<=', $endMonth)
            ->groupBy(DB::raw('YEAR(order_date)'), DB::raw('MONTH(order_date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        // Asegurar que hay datos para todos los meses
        $monthlySalesData = [];
        $monthLabels = [];
        
        // Crear un período de 12 meses
        $monthPeriod = collect();
        for ($i = 11; $i >= 0; $i--) {
            $monthPeriod->push(Carbon::now()->subMonths($i));
        }
        
        foreach ($monthPeriod as $month) {
            $monthString = $month->format('Y-m');
            $formattedMonth = $month->format('M Y');
            $monthLabels[] = $formattedMonth;
            
            // Buscar si hay ventas para este mes
            $sale = $monthlySales->first(function ($item) use ($month) {
                return $item->year == $month->year && $item->month == $month->month;
            });
            
            $monthlySalesData[] = $sale ? round($sale->total, 2) : 0;
        }
        
        // Top 5 servicios más vendidos
        $topServices = OrderService::select(
                'service_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('service_name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
            
        return view('admin.reports.sales', compact(
            'dailySalesData', 
            'labels', 
            'monthlySalesData', 
            'monthLabels',
            'topServices'
        ));
    }
}
