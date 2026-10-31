<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nim' => ['required', 'string', 'max:20', 'unique:users,nim'],
            'program_study' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'name' => $validated['nama'],
            'nim' => $validated['nim'],
            'program_study' => $validated['program_study'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'mahasiswa',
            'status_aktif' => 'aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

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
