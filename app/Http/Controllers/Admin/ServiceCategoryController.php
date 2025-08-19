<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceCategoryController extends Controller
{
    /**
     * Mostrar listado de categorías de servicio con filtros
     */
    public function index(Request $request)
    {
        // Iniciar la consulta base
        $query = ServiceCategory::withCount('serviceItems');
        
        // Filtrar por nombre
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        
        // Filtrar por descripción
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }
        
        // Filtrar por mínimo de servicios
        if ($request->filled('min_services')) {
            $minServices = (int)$request->input('min_services');
            // Este enfoque es mejor para bases de datos grandes
            $query->has('serviceItems', '>=', $minServices);
        }
        
        // Ordenación
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        
        // Validar los campos de ordenación permitidos
        $validSorts = ['name', 'created_at'];
        
        if ($sort == 'service_count') {
            // Ordenar por el número de servicios (usando el conteo withCount)
            $query->orderBy('service_items_count', $direction);
        } else {
            // Ordenar por otros campos normales
            if (!in_array($sort, $validSorts)) {
                $sort = 'name'; // Valor predeterminado si es inválido
            }
            $query->orderBy($sort, $direction);
        }
        
        // Ejecutar la consulta
        $categories = $query->paginate(15)->withQueryString();
        
        return view('admin.service_categories.index', compact('categories'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.service_categories.create');
    }

    /**
     * Guardar nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|string',
        ]);

        ServiceCategory::create($validated);

        return redirect()->route('admin.service_categories.index')
            ->with('success', 'Categoría de servicio creada correctamente');
    }

    /**
     * Mostrar detalles de categoría
     */
    public function show(ServiceCategory $serviceCategory)
    {
        $serviceCategory->load('serviceItems');
        return view('admin.service_categories.show', compact('serviceCategory'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        return view('admin.service_categories.edit', compact('serviceCategory'));
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|string',
        ]);

        $serviceCategory->update($validated);

        return redirect()->route('admin.service_categories.index')
            ->with('success', 'Categoría de servicio actualizada correctamente');
    }

    /**
     * Eliminar categoría
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        try {
            DB::beginTransaction();
            // La eliminación en cascada está configurada en la migración
            $serviceCategory->delete();
            DB::commit();
            
            return redirect()->route('admin.service_categories.index')
                ->with('success', 'Categoría de servicio eliminada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar categoría de servicio: " . $e->getMessage());
            
            return redirect()->route('admin.service_categories.index')
                ->with('error', 'Error al eliminar la categoría de servicio');
        }
    }
}
