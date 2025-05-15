<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%$search%")
                  ->orWhere('contact_first_name', 'like', "%$search%")
                  ->orWhere('contact_last_name', 'like', "%$search%")
                  ->orWhere('contact_email', 'like', "%$search%")
                  ->orWhere('contact_phone', 'like', "%$search%");
            });
        }

        $suppliers = $query->paginate(6)->appends($request->only('search'));
        return view('purchase.suppliers.index', compact('suppliers', 'currentUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Supplier::class);
        return view('purchase.suppliers.create');
    }  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Supplier::class);
        try{
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_first_name' => 'required|string|max:255',
            'contact_last_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255|unique:suppliers,contact_email',
            'contact_phone' => 'required|string|max:30|unique:suppliers,contact_phone',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        Supplier::create($validated);

        return redirect()->route('purchase.suppliers.index')->with('success', 'Fournisseur créé avec succès.');
        }catch(\Exception $e){
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);
        return view('purchase.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        return view('purchase.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        try{
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'contact_first_name' => 'required|string|max:255',
                'contact_last_name' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255|unique:suppliers,contact_email,'.$supplier->id,
                'contact_phone' => 'required|string|max:30|unique:suppliers,contact_phone,'.$supplier->id,
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
            ]);
            $supplier->update($validated);
            $redirectUrl = $request->input('return_url') ?? route('purchase.suppliers.show', $supplier->id);
            return redirect($redirectUrl)->with('success', 'Fournisseur mis à jour avec succès');
        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);
        try{
            $supplier->delete();
            return redirect()->route('purchase.suppliers.index')->with('success', 'Fournisseur supprimé avec succès.');
        }catch(\Exception $e){
            return redirect()->route('purchase.suppliers.index')->with('error', 'Une erreur est survenue lors de la suppression du fournisseur.');
        }
    }
}
