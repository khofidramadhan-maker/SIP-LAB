<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'current_password' => ['required_with:password', 'current_password'],
        ];

        if ($user->role === 'mahasiswa') {
            $rules['nim'] = ['nullable', 'string', 'max:20', Rule::unique('users', 'nim')->ignore($user->id)];
            $rules['program_study'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $user->nama = $validated['nama'];
        $user->name = $validated['nama'];
        $user->email = $validated['email'];

        if ($user->role === 'mahasiswa') {
            $user->nim = $validated['nim'] ?? null;
            $user->program_study = $validated['program_study'] ?? null;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
