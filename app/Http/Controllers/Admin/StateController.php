<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    /**
     * Muestra el listado de estados
     */
    public function index()
    {
        $orderStates = State::where('type', 'order')->orderBy('name')->get();
        $paymentStates = State::where('type', 'payment')->orderBy('name')->get();
        
        return view('admin.states.index', compact('orderStates', 'paymentStates'));
    }

    /**
     * Muestra el formulario para crear un nuevo estado
     */
    public function create()
    {
        return view('admin.states.create');
    }

    /**
     * Almacena un nuevo estado en la base de datos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:order,payment',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string'
        ]);

        $state = State::create($validated);
        
        return redirect()->route('admin.states.index')
            ->with('success', 'Estado creado correctamente');
    }

    /**
     * Muestra el formulario para editar un estado
     */
    public function edit(State $state)
    {
        return view('admin.states.edit', compact('state'));
    }

    /**
     * Actualiza la información de un estado en la base de datos
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:order,payment',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string'
        ]);

        $state->update($validated);
        
        return redirect()->route('admin.states.index')
            ->with('success', 'Estado actualizado correctamente');
    }

    /**
     * Elimina un estado de la base de datos
     */
    public function destroy(State $state)
    {
        DB::beginTransaction();
        try {
            // Verificar si el estado está siendo utilizado
            $inUseCount = 0;
            
            if ($state->type === 'order') {
                $inUseCount = DB::table('orders')->where('order_state_id', $state->id)->count();
            } elseif ($state->type === 'payment') {
                $inUseCount = DB::table('orders')->where('payment_state_id', $state->id)->count();
            }
            
            if ($inUseCount > 0) {
                return redirect()->back()
                    ->with('error', "No se puede eliminar este estado porque está siendo utilizado en {$inUseCount} pedidos");
            }
            
            $state->delete();
            DB::commit();
            
            return redirect()->route('admin.states.index')
                ->with('success', 'Estado eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Devuelve listado de estados para API
     */
    public function getStates()
    {
        $states = [
            'order' => State::where('type', 'order')->orderBy('name')->get(['id', 'name', 'color']),
            'payment' => State::where('type', 'payment')->orderBy('name')->get(['id', 'name', 'color'])
        ];
            
        return response()->json($states);
    }
}
