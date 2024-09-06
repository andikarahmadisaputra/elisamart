<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:store.list|store.create|store.edit|store.delete', ['only' => ['index','show']]);
         $this->middleware('permission:store.create', ['only' => ['create','store']]);
         $this->middleware('permission:store.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:store.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $data = Store::latest()->paginate(5);
  
        return view('stores.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('stores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'nullable',
        ]);
    
        Store::create($request->all());
    
        return redirect()->route('stores.index')
                        ->with('success','Store created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store): View
    {
        return view('stores.show',compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store): View
    {
        return view('stores.edit',compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store): RedirectResponse
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'nullable',
        ]);
    
        $store->update($request->all());
    
        return redirect()->route('stores.index')
                        ->with('success','Store updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store): RedirectResponse
    {
        $store->delete();
    
        return redirect()->route('stores.index')
                        ->with('success','Store deleted successfully');
    }
}
