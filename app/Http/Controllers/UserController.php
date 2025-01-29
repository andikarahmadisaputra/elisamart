<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tag;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
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
        $data = User::with('tags')->select('id', 'name', 'email', 'username', 'nik', 'phone', 'gender', 'balance')->orderBy('name')->paginate(20);
  
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = Role::pluck('name','name')->all();
        $tags = Tag::select('id', 'name')->get();

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
        $validatedData = $request->validate([
            'name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|same:confirm-password',
            'roles' => 'nullable|exists:roles,name',
            'username' => 'nullable|min:3|max:30|unique:users,username|regex:/^\S*$/',
            'nik' => 'nullable|size:16|regex:/^[0-9]+$/|unique:users,nik',
            'phone' => 'nullable|string|regex:/^\d{10,13}$/',
            'gender' => 'nullable|in:pria,wanita',
            'pin' => 'nullable|string|size:6|regex:/^[0-9]+$/',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ],[
            // Pesan untuk kolom 'name'
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 50 karakter.',
            'name.regex' => 'Nama hanya boleh mengandung huruf dan spasi.',

            // Pesan untuk kolom 'email'
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',

            // Pesan untuk kolom 'password'
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.same' => 'Password dan konfirmasi password tidak cocok.',

            // Pesan untuk kolom 'roles'
            'roles.exists' => 'Role yang dipilih tidak valid.',

            // Pesan untuk kolom 'username'
            'username.min' => 'Username harus memiliki minimal 3 karakter.',
            'username.max' => 'Username tidak boleh lebih dari 30 karakter.',
            'username.unique' => 'Username sudah digunakan.',
            'username.regex' => 'Username tidak boleh mengandung spasi.',

            // Pesan untuk kolom 'nik'
            'nik.size' => 'NIK harus terdiri dari 16 angka.',
            'nik.regex' => 'NIK hanya boleh berupa angka.',
            'nik.unique' => 'NIK sudah terdaftar.',

            // Pesan untuk kolom 'phone'
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.regex' => 'Nomor telepon harus terdiri dari 10 hingga 13 angka.',

            // Pesan untuk kolom 'gender'
            'gender.in' => 'Jenis kelamin hanya boleh "pria" atau "wanita".',

            // Pesan untuk kolom 'pin'
            'pin.string' => 'PIN harus berupa teks.',
            'pin.size' => 'PIN harus terdiri dari 6 angka.',
            'pin.regex' => 'PIN hanya boleh berupa angka.',

            // Pesan untuk kolom 'tags'
            'tags.array' => 'Tags harus berupa array.',
            'tags.*.exists' => 'Tag yang dipilih tidak valid.',
        ]);

        DB::beginTransaction();

        try {
            // Encrypt sensitive fields
            $validatedData['password'] = bcrypt($validatedData['password']);
            if (!empty($validatedData['pin'])) {
                $validatedData['pin'] = bcrypt($validatedData['pin']);
            }
        
            // Create user and assign role
            $user = User::create($validatedData);
            if (!empty($validatedData['roles'])) {
                $user->assignRole($validatedData['roles']);
            }

            // Sync tags if provided
            if (!empty($validatedData['tags'])) {
                $user->tags()->sync($validatedData['tags']);
            }

            DB::commit();
    
            return redirect()->route('users.index')
                ->with('success','User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput($request->all()) // Return input values
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
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
            $input['password'] = bcrypt($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        if(!empty($input['pin'])){ 
            $input['pin'] = bcrypt($input['pin']);
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
