<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Muestra el listado de clientes con filtros
     */
    public function index(Request $request)
    {
        // Iniciar la consulta base
        $query = Customer::with('user');
        
        // Filtrar por búsqueda general (nombre o apellido)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }
        
        // Filtrar por email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }
        
        // Filtrar por teléfono
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }
        
        // Filtrar por CIF/NIF
        if ($request->filled('tax_id')) {
            $query->where('tax_id', 'like', '%' . $request->input('tax_id') . '%');
        }
        
        // Filtrar por ciudad
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }
        
        // Ordenación
        $sort = $request->input('sort', 'first_name');
        $direction = $request->input('direction', 'asc');
        
        // Verificar que el campo de ordenación existe en la tabla
        $validSortFields = ['first_name', 'last_name', 'email', 'created_at'];
        if (!in_array($sort, $validSortFields)) {
            $sort = 'first_name';
        }
        
        $query->orderBy($sort, $direction);
        
        // Paginación
        $customers = $query->paginate(15)->withQueryString();
        
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Muestra el formulario para crear un nuevo cliente
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Almacena un nuevo cliente en la base de datos
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'nullable|email|unique:customers,email|max:100',
            'phone' => 'required|string|max:20',
            'tax_id' => 'nullable|string|unique:customers,tax_id|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
        ]);

        // Crear usuario asociado
        DB::beginTransaction();
        try {
            // Crear usuario con nombre+apellido o un valor por defecto si no hay email
            $userName = $validated['first_name'] . ' ' . $validated['last_name'];
            $userEmail = $validated['email'] ?? $userName . rand(1000, 9999) . '@ejemplo.com';
            
            $user = User::create([
                'name' => $userName,
                'email' => $userEmail,
                'password' => Hash::make('cliente'.rand(1000, 9999)), // Contraseña temporal
                'role_id' => 2, // Asumiendo que rol 2 es para clientes
            ]);
            
            // Aseguramos que el email del cliente sea el mismo que el del usuario
            $validated['email'] = $userEmail;

            // Crear el cliente asociado al usuario
            $validated['user_id'] = $user->id;
            $customer = Customer::create($validated);

            DB::commit();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Cliente creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la información de un cliente específico
     */
    public function show(Customer $customer)
    {
        $customer->load(['user', 'orders']);
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Muestra el formulario para editar un cliente existente
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Actualiza la información de un cliente en la base de datos
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('customers')->ignore($customer->id)],
            'phone' => 'required|string|max:20',
            'tax_id' => ['nullable', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Si no hay email, generamos uno basado en el nombre
            if (empty($validated['email'])) {
                $userName = $validated['first_name'] . ' ' . $validated['last_name'];
                $validated['email'] = $userName . rand(1000, 9999) . '@ejemplo.com';
            }
            
            // Actualizar el usuario asociado si el email cambia
            if ($customer->email !== $validated['email']) {
                $customer->user->update([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                ]);
            } else {
                // Actualizar el nombre del usuario aunque el email no cambie
                $customer->user->update([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name']
                ]);
            }

            // Actualizar el cliente
            $customer->update($validated);
            
            DB::commit();
            
            return redirect()->route('admin.customers.index')
                ->with('success', 'Cliente actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un cliente de la base de datos
     */
    public function destroy(Customer $customer)
    {
        DB::beginTransaction();
        try {
            // Verificar si tiene pedidos
            if ($customer->orders()->exists()) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados');
            }
            
            // Eliminar usuario asociado
            $user = $customer->user;
            $customer->delete();
            
            if ($user) {
                $user->delete();
            }
            
            DB::commit();
            
            return redirect()->route('admin.customers.index')
                ->with('success', 'Cliente eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Busca clientes por nombre, email o teléfono
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $customers = Customer::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orderBy('first_name')
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone']);
            
        return response()->json($customers);
    }

    /**
     * Devuelve listado de clientes para API
     */
    public function getCustomers()
    {
        $customers = Customer::select('id', DB::raw("CONCAT(first_name, ' ', IFNULL(last_name, '')) as name"))
            ->orderBy('first_name')
            ->get();
            
        return response()->json($customers);
    }
}
