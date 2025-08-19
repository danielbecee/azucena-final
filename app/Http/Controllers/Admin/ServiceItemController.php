<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ServiceItemController extends Controller
{
    /**
     * Mostrar listado de servicios específicos con filtros acumulativos
     */
    public function index(Request $request)
    {
        // Obtener categorías para los filtros
        $categories = ServiceCategory::orderBy('name')->get();
        
        // Iniciar la consulta
        $query = ServiceItem::with('serviceCategory');
        
        // Aplicar filtros si existen
        // Filtro por categoría
        if ($request->filled('category')) {
            $query->whereIn('service_category_id', $request->category);
        }
        
        // Filtro por nombre (búsqueda parcial)
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        // Filtro por rango de precios
        if ($request->filled('price_from')) {
            $query->where('price', '>=', $request->price_from);
        }
        
        if ($request->filled('price_to')) {
            $query->where('price', '<=', $request->price_to);
        }
        
        // Ordenación
        $sortField = $request->input('sort_field', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        
        // Validar que el campo de ordenación es válido
        $validSortFields = ['name', 'price', 'created_at'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'name';
        }
        
        $query->orderBy($sortField, $sortOrder);
        
        // Ejecutar la consulta con paginación
        $serviceItems = $query->paginate(15)->withQueryString();
        
        // Obtener rangos de precios para los filtros
        $minPrice = ServiceItem::min('price') ?: 0;
        $maxPrice = ServiceItem::max('price') ?: 100;
        
        // Datos para los filtros seleccionados actualmente
        $filters = [
            'category' => $request->category ?? [],
            'name' => $request->name,
            'price_from' => $request->price_from,
            'price_to' => $request->price_to,
            'sort_field' => $sortField,
            'sort_order' => $sortOrder,
        ];
            
        return view('admin.service_items.index', compact(
            'serviceItems', 
            'categories', 
            'minPrice',
            'maxPrice',
            'filters'
        ));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $categories = ServiceCategory::all();
        return view('admin.service_items.create', compact('categories'));
    }

    /**
     * Guardar nuevo servicio específico
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        ServiceItem::create($validated);

        return redirect()->route('admin.service_items.index')
            ->with('success', 'Servicio específico creado correctamente');
    }

    /**
     * Mostrar detalles de servicio específico
     */
    public function show(ServiceItem $serviceItem)
    {
        $serviceItem->load('serviceCategory');
        return view('admin.service_items.show', compact('serviceItem'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(ServiceItem $serviceItem)
    {
        $categories = ServiceCategory::all();
        return view('admin.service_items.edit', compact('serviceItem', 'categories'));
    }

    /**
     * Actualizar servicio específico
     */
    public function update(Request $request, ServiceItem $serviceItem)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $serviceItem->update($validated);

        return redirect()->route('admin.service_items.index')
            ->with('success', 'Servicio específico actualizado correctamente');
    }

    /**
     * Eliminar servicio específico
     */
    public function destroy(ServiceItem $serviceItem)
    {
        try {
            DB::beginTransaction();
            $serviceItem->delete();
            DB::commit();
            
            return redirect()->route('admin.service_items.index')
                ->with('success', 'Servicio específico eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar servicio específico: " . $e->getMessage());
            
            return redirect()->route('admin.service_items.index')
                ->with('error', 'Error al eliminar el servicio específico');
        }
    }
    
    /**
     * Obtener servicios por categoría (para uso en AJAX/API)
     */
    public function getByCategory(ServiceCategory $category)
    {
        $items = $category->serviceItems;
        return response()->json($items);
    }
}
