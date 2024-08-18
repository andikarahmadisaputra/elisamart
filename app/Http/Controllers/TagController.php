<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:tag-list|tag-create|tag-edit|tag-delete', ['only' => ['index','show']]);
         $this->middleware('permission:tag-create', ['only' => ['create','store']]);
         $this->middleware('permission:tag-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:tag-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $tags = Tag::select(['id', 'name', 'detail'])->latest()->paginate(5);

        return view('tags.index',compact('tags'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('tags.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        Tag::create($request->all());
    
        return redirect()->route('tags.index')
                        ->with('success','Tag created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag): View
    {
        return view('tags.show',compact('tag'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag): View
    {
        return view('tags.edit',compact('tag'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
         request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        $tag->update($request->all());
    
        return redirect()->route('tags.index')
                        ->with('success','Tag updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();
    
        return redirect()->route('tags.index')
                        ->with('success','Tag deleted successfully');
    }
}
