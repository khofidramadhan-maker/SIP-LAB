<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function home(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('home');
        }

        return redirect()->to($this->redirectToRole($user->role));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            return redirect()->to($this->redirectToRole(Auth::user()->role));
        }

        return back()->withErrors(['email' => 'Email atau password tidak valid.'])->withInput();
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'student_number' => ['nullable', 'string', 'max:50'],
            'program_study' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa',
            'student_number' => $validated['student_number'] ?? null,
            'program_study' => $validated['program_study'] ?? null,
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    protected function redirectToRole(string $role): string
    {
        return match ($role) {
            'mahasiswa' => route('student.dashboard'),
            'staff' => route('staff.dashboard'),
            'kepala_lab' => route('lab-head.dashboard'),
            default => route('student.dashboard'),
        };
    }
}
