<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tag;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:user.list|user.create|user.edit|user.delete', ['only' => ['index', 'show']]);
         $this->middleware('permission:user.create', ['only' => ['create','store']]);
         $this->middleware('permission:user.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user.delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = User::latest()->paginate(5);
  
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = Role::pluck('name','name')->all();
        $tags = Tag::all();

        return view('users.create',compact('roles', 'tags'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|same:confirm-password',
            'roles' => 'required',
            'nik' => 'nullable|unique:users,nik',
            'phone' => 'nullable|string|regex:/^\d{10,13}$/',
            'gender' => 'nullable|in:pria,wanita',
            'pin' => 'nullable|string|size:6|regex:/^[0-9]+$/',
            'tags' => 'array',
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        if (!empty($input['pin'])) {
            $input['pin'] = Hash::make($input['pin']);
        }
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        if ($request->has('tags')) {
            $user->tags()->sync($request->tags);
        }
    
        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        $tags = Tag::all();
        $selectedTags = $user->tags->pluck('id')->toArray();
    
        return view('users.edit',compact('user','roles','userRole', 'tags', 'selectedTags'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|same:confirm-password',
            'roles' => 'required',
            'nik' => 'nullable|unique:users,nik,'.$id,
            'phone' => 'nullable|string|regex:/^\d{10,13}$/',
            'gender' => 'nullable|in:pria,wanita',
            'pin' => 'nullable|string|size:6|regex:/^[0-9]+$/',
            'tags' => 'array',
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        if(!empty($input['pin'])){ 
            $input['pin'] = Hash::make($input['pin']);
        }else{
            $input = Arr::except($input,array('pin'));    
        }

        if(empty($input['gender'])){
            $input = Arr::except($input,array('gender'));
        }
    
        $user = User::find($id);

        if (empty($input['nik']) || $input['nik'] == $user->nik) {
            $input = Arr::except($input,array('nik'));
        }

        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
        if ($request->has('tags')) {
            $user->tags()->sync($request->tags);
        }
    
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
}
