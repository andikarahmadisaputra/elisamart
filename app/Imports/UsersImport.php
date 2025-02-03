<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Memproses setiap baris dari file Excel.
     */
    public function model(array $row)
    {
        // Mengumpulkan data dalam array
        static $users = [];

        if (!isset($row['email'])) {
            return null;
        }

        // Cek apakah user dengan email lama ada
        // $user = User::withTrashed()->where('email', $row['email'])->first();

        // if ($user) {
        //     $user->update([
        //         'email'    => !empty($row['new_email']) ? $row['email'] : $user->email,
        //         'name'     => $row['name'] ?? $user->name,
        //         'username' => $row['username'] ?? $user->username,
        //         'nik'      => $row['nik'] ?? $user->nik,
        //         'phone'    => $row['phone'] ?? $user->phone,
        //         'gender'   => $row['gender'] ?? $user->gender,
        //         'password' => !empty($row['password']) ? Hash::make($row['password']) : $user->password,
        //         'pin'      => isset($row['pin']) ? Hash::make($row['pin']) : $user->pin,
        //     ]);
        //     return $user;
        // }

        $users[] = [
            'email'    => $row['email'],
            'name'     => $row['name'],
            'username' => $row['username'] ?? null,
            'nik'      => $row['nik'] ?? null,
            'phone'    => $row['phone'] ?? null,
            'gender'   => $row['gender'] ?? null,
            'password' => Hash::make($row['password']),
            'pin'      => isset($row['pin']) ? Hash::make($row['pin']) : null,
        ];

        // Jika sudah lebih dari 500 data, lakukan batch insert
        if (count($users) >= 100) {
            User::insert($users);
            $users = []; // Reset array setelah batch insert
        }

        return null;
    }

    /**
     * Aturan validasi untuk setiap baris import.
     */
    public function rules(): array
    {
        return [
            'name'      => "nullable|string|max:50|regex:/^[a-zA-Z\s.,']+$/u",
            'email'     => 'required|email|unique:users,email',
            'password'  => 'nullable|string|min:5',
            'username'  => 'nullable|min:3|max:30|regex:/^\S*$/|unique:users,username',
            'nik'       => 'nullable|digits:16|unique:users,nik',
            'phone'     => 'nullable|regex:/^\+?\d{9,15}$/',
            'gender'    => 'nullable|in:pria,wanita',
            'pin'       => 'nullable|digits:6',
            'new_email' => 'nullable|email|unique:users,email',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.max' => 'Nama tidak boleh lebih dari 50 karakter.',
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, titik, dan koma.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',

            'password.min' => 'Password minimal harus 5 karakter.',

            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username tidak boleh lebih dari 30 karakter.',
            'username.regex' => 'Username tidak boleh mengandung spasi.',
            'username.unique' => 'Username sudah digunakan.',

            'nik.digits' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK sudah digunakan.',

            'phone.regex' => 'Nomor telepon harus valid dan terdiri dari 9-15 digit.',

            'gender.in' => 'Jenis kelamin hanya boleh diisi dengan pria atau wanita.',

            'pin.digits' => 'PIN harus terdiri dari 6 digit.',

            'new_email.email' => 'Format email baru tidak valid.',
            'new_email.unique' => 'Email baru sudah digunakan.',
        ];
    }
   
}
