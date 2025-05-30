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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
         $this->middleware('permission:user.import', ['only' => ['import']]);
         $this->middleware('permission:user.export', ['only' => ['export']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = User::withTrashed()->with('tags')->select('id', 'name', 'email', 'username', 'nik', 'phone', 'gender', 'balance', 'created_at', 'updated_at', 'deleted_at')->orderBy('name')->paginate(20);
  
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
     * @method bool can(string $ability, mixed $arguments = [])
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => "required|string|max:50|regex:/^[A-Za-z'.\s]+$/",
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|same:confirm-password',
            'username' => 'nullable|min:3|max:30|unique:users,username|regex:/^\S*$/',
            'nik' => 'nullable|size:16|regex:/^[0-9]+$/|unique:users,nik',
            'phone' => 'nullable|string|regex:/^\d{10,13}$/',
            'gender' => 'nullable|in:pria,wanita',
            'pin' => 'nullable|string|size:6|regex:/^[0-9]+$/',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
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
            'password.min' => 'Password minimal 6 karakter',
            'password.same' => 'Password dan konfirmasi password tidak cocok.',

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

        if (Auth::check() && Auth::user()->can('user.assign_role')) {
            $roleData = $request->validate([
                'roles' => 'nullable|exists:roles,name',
            ],[
                'roles.exists' => 'Role yang dipilih tidak valid.',
            ]);

            $validatedData['roles'] = $roleData['roles'] ?? null;
        }

        DB::beginTransaction();

        try {
            // Laravel akan otomatis menghash password, jadi tidak perlu bcrypt()
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'username' => $validatedData['username'] ?? '',
                'password' => $validatedData['password'], // Laravel akan otomatis hash
                'nik' => $validatedData['nik'],
                'phone' => $validatedData['phone'],
                'gender' => $validatedData['gender'],
                'pin' => !empty($validatedData['pin']) ? Hash::make($validatedData['pin']) : null,
            ]);

            // Assign role
            if (Auth::check() && Auth::user()->can('user.assign_role') && !empty($validatedData['roles'])) {
                $user->syncRoles($validatedData['roles']);
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

            // Log error
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);

            return redirect()->back()
                ->withInput($request->except(['password', 'pin']))
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user): View
    {
        // Ambil user dengan relasi untuk mengurangi query tambahan
        $user->load(['roles', 'tags']);

        // Ambil roles dalam bentuk array langsung
        $roles = Role::pluck('name', 'name')->toArray();
        $userRole = $user->roles->pluck('name')->toArray();
        $tags = Tag::select('id', 'name')->get();
        $selectedTags = $user->tags->pluck('id')->toArray();

        return view('users.edit', compact('user', 'roles', 'userRole', 'tags', 'selectedTags'));
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
            'name' => "required|string|max:50|regex:/^[A-Za-z'.\s]+$/",
            'email' => 'required|email|unique:users,email'.$id,
            'password' => 'nullable|string|min:6|same:confirm-password',
            'username' => 'nullable|min:3|max:30|regex:/^\S*$/|unique:users,username'.$id,
            'nik' => 'nullable|size:16|regex:/^[0-9]+$/|unique:users,nik'.$id,
            'phone' => 'nullable|string|regex:/^\d{10,13}$/',
            'gender' => 'nullable|in:pria,wanita',
            'pin' => 'nullable|string|size:6|regex:/^[0-9]+$/',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ],[
            // Pesan untuk kolom 'name'
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 50 karakter.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, titik dan petik.',

            // Pesan untuk kolom 'email'
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',

            // Pesan untuk kolom 'password'
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter',
            'password.same' => 'Password dan konfirmasi password tidak cocok.',

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
    
        $input = $request->all();
        if(empty($input['password'])){ 
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

        // Update data user
        $user->update($input);

        // Update Role and Permissions
        if (Auth::check() && Auth::user()->can('user.assign_role') && !empty($validatedData['roles'])) {
            DB::table('model_has_roles')->where('model_id',$id)->delete();
        
            $user->assignRole($request->input('roles'));
        }
        
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

    public function restore($id): RedirectResponse
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        return redirect()->route('user.index')->with('success', 'User restore successfully');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function export() 
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
         
    /**
    * @return \Illuminate\Support\Collection
    */
    public function import(Request $request) 
    {
        // Menetapkan waktu eksekusi maksimum untuk 5 menit (300 detik)
        ini_set('max_execution_time', 300);

        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();

            Excel::import(new UsersImport, $request->file('file'));

            // Melakukan batch insert jika ada data yang tersisa
            if (!empty($users)) {
                User::insert($users);  // Insert data batch terakhir
            }

            DB::commit();
            return back()->with('success', 'Users imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollback();
            return back()->with('failure', $e->failures());
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
