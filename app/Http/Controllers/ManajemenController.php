<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManajemenController extends Controller
{
    // ─── Helper: Log aktivitas ─────────────────────────────────────────────
    private function log(string $aktivitas, string $jenisData, string $kodeIdentitas, string $jenisAksi, ?array $sebelum, ?array $sesudah): void
    {
        LogAktivitas::create([
            'user_id'        => Auth::id(),
            'aktivitas'      => $aktivitas,
            'waktu'          => now(),
            'jenis_data'     => $jenisData,
            'kode_identitas' => $kodeIdentitas,
            'jenis_aksi'     => $jenisAksi,
            'data_sebelum'   => $sebelum,
            'data_sesudah'   => $sesudah,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════
    //  RUANGAN
    // ══════════════════════════════════════════════════════════════════════

    public function ruanganIndex()
    {
        $ruangan = Ruangan::orderBy('kode_ruangan')->paginate(15);
        return view('manajemen.ruangan.index', compact('ruangan'));
    }

    public function ruanganCreate()
    {
        return view('manajemen.ruangan.create');
    }

    public function ruanganStore(Request $request)
    {
        $data = $request->validate([
            'kode_ruangan'  => 'required|string|max:30|unique:ruangan,kode_ruangan',
            'nama_ruangan'  => 'required|string|max:100',
            'kapasitas'     => 'required|integer|min:1',
            'lokasi'        => 'required|string|max:150',
            'fasilitas'     => 'nullable|string',
            'status'        => 'required|in:tersedia,terpakai,perbaikan',
        ]);

        $ruangan = Ruangan::create($data);

        $this->log(
            "Tambah ruangan {$ruangan->kode_ruangan} – {$ruangan->nama_ruangan}",
            'ruangan', $ruangan->kode_ruangan, 'tambah', null, $data
        );

        return redirect()->route('staff.ruangan.index')->with('success', "Ruangan {$ruangan->nama_ruangan} berhasil ditambahkan.");
    }

    public function ruanganShow(Ruangan $ruangan)
    {
        $riwayat = LogAktivitas::where('jenis_data', 'ruangan')
            ->where('kode_identitas', $ruangan->kode_ruangan)
            ->with('user')
            ->orderByDesc('waktu')
            ->get();

        return view('manajemen.ruangan.show', compact('ruangan', 'riwayat'));
    }

    public function ruanganEdit(Ruangan $ruangan)
    {
        return view('manajemen.ruangan.edit', compact('ruangan'));
    }

    public function ruanganUpdate(Request $request, Ruangan $ruangan)
    {
        $data = $request->validate([
            'kode_ruangan'  => ['required', 'string', 'max:30', Rule::unique('ruangan')->ignore($ruangan->id)],
            'nama_ruangan'  => 'required|string|max:100',
            'kapasitas'     => 'required|integer|min:1',
            'lokasi'        => 'required|string|max:150',
            'fasilitas'     => 'nullable|string',
            'status'        => 'required|in:tersedia,terpakai,perbaikan',
        ]);

        $sebelum = $ruangan->only(['kode_ruangan', 'nama_ruangan', 'kapasitas', 'lokasi', 'fasilitas', 'status']);
        $ruangan->update($data);

        $this->log(
            "Ubah ruangan {$ruangan->kode_ruangan} – {$ruangan->nama_ruangan}",
            'ruangan', $ruangan->kode_ruangan, 'ubah', $sebelum, $data
        );

        return redirect()->route('staff.ruangan.index')->with('success', "Ruangan {$ruangan->nama_ruangan} berhasil diperbarui.");
    }

    // ══════════════════════════════════════════════════════════════════════
    //  PENGGUNA
    // ══════════════════════════════════════════════════════════════════════

    /** Roles that staff may NOT assign or manage */
    private const PROTECTED_ROLES = ['kepala_lab'];

    public function penggunaIndex()
    {
        $users = User::orderBy('nama')->paginate(15);
        return view('manajemen.pengguna.index', compact('users'));
    }

    public function penggunaCreate()
    {
        return view('manajemen.pengguna.create');
    }

    public function penggunaStore(Request $request)
    {
        $data = $request->validate([
            'nama'        => 'required|string|max:100',
            'nim'         => 'nullable|string|max:20|unique:users,nim',
            'username'    => 'required|string|max:50|unique:users,username',
            'email'       => 'required|email|max:100|unique:users,email',
            'role'        => ['required', 'string', Rule::notIn(self::PROTECTED_ROLES)],
            'status_aktif'=> 'required|in:aktif,nonaktif',
            'password'    => 'required|string|min:6',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $logData = collect($data)->except('password')->toArray();
        $this->log(
            "Tambah pengguna {$user->username} (role: {$user->role})",
            'pengguna', $user->username, 'tambah', null, $logData
        );

        return redirect()->route('staff.pengguna.index')->with('success', "Pengguna {$user->nama} berhasil ditambahkan.");
    }

    public function penggunaShow(User $user)
    {
        $riwayat = LogAktivitas::where('jenis_data', 'pengguna')
            ->where('kode_identitas', $user->username)
            ->with('user')
            ->orderByDesc('waktu')
            ->get();

        return view('manajemen.pengguna.show', compact('user', 'riwayat'));
    }

    public function penggunaEdit(User $user)
    {
        return view('manajemen.pengguna.edit', compact('user'));
    }

    public function penggunaUpdate(Request $request, User $user)
    {
        $rules = [
            'nama'        => 'required|string|max:100',
            'nim'         => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'username'    => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'       => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'status_aktif'=> 'required|in:aktif,nonaktif',
            'password'    => 'nullable|string|min:6',
        ];

        // Staff may not escalate role to protected values, and may not change role if user is kepala_lab
        if (!in_array($user->role, self::PROTECTED_ROLES)) {
            $rules['role'] = ['required', 'string', Rule::notIn(self::PROTECTED_ROLES)];
        }

        $validated = $request->validate($rules);

        $sebelum = $user->only(['nama', 'nim', 'username', 'email', 'role', 'status_aktif']);

        $updateData = collect($validated)->except('password')->toArray();
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // If the user is kepala_lab, don't allow role change
        if (in_array($user->role, self::PROTECTED_ROLES)) {
            unset($updateData['role']);
        }

        $user->update($updateData);

        $sesudah = collect($updateData)->except('password')->toArray();
        $this->log(
            "Ubah pengguna {$user->username}",
            'pengguna', $user->username, 'ubah', $sebelum, $sesudah
        );

        return redirect()->route('staff.pengguna.index')->with('success', "Data pengguna {$user->nama} berhasil diperbarui.");
    }

    // ══════════════════════════════════════════════════════════════════════
    //  ALAT
    // ══════════════════════════════════════════════════════════════════════

    public function alatIndex()
    {
        $alat = Alat::orderBy('kode_inventaris')->paginate(15);
        return view('manajemen.alat.index', compact('alat'));
    }

    public function alatCreate()
    {
        return view('manajemen.alat.create');
    }

    public function alatStore(Request $request)
    {
        $data = $request->validate([
            'kode_inventaris' => 'required|string|max:30|unique:alat,kode_inventaris',
            'nama_alat'       => 'required|string|max:100',
            'kategori'        => 'required|string|max:100',
            'jumlah_total'    => 'required|integer|min:0',
            'kondisi'         => 'required|in:baik,rusak ringan,rusak berat',
            'status'          => 'required|in:tersedia,dipinjam,perbaikan',
        ]);

        $alat = Alat::create($data);

        $this->log(
            "Tambah alat {$alat->kode_inventaris} – {$alat->nama_alat}",
            'alat', $alat->kode_inventaris, 'tambah', null, $data
        );

        return redirect()->route('staff.alat.index')->with('success', "Alat {$alat->nama_alat} berhasil ditambahkan.");
    }

    public function alatShow(Alat $alat)
    {
        $riwayat = LogAktivitas::where('jenis_data', 'alat')
            ->where('kode_identitas', $alat->kode_inventaris)
            ->with('user')
            ->orderByDesc('waktu')
            ->get();

        return view('manajemen.alat.show', compact('alat', 'riwayat'));
    }

    public function alatEdit(Alat $alat)
    {
        return view('manajemen.alat.edit', compact('alat'));
    }

    public function alatUpdate(Request $request, Alat $alat)
    {
        $data = $request->validate([
            'kode_inventaris' => ['required', 'string', 'max:30', Rule::unique('alat')->ignore($alat->id)],
            'nama_alat'       => 'required|string|max:100',
            'kategori'        => 'required|string|max:100',
            'jumlah_total'    => 'required|integer|min:0',
            'kondisi'         => 'required|in:baik,rusak ringan,rusak berat',
            'status'          => 'required|in:tersedia,dipinjam,perbaikan',
        ]);

        $sebelum = $alat->only(['kode_inventaris', 'nama_alat', 'kategori', 'jumlah_total', 'kondisi', 'status']);
        $alat->update($data);

        $this->log(
            "Ubah alat {$alat->kode_inventaris} – {$alat->nama_alat}",
            'alat', $alat->kode_inventaris, 'ubah', $sebelum, $data
        );

        return redirect()->route('staff.alat.index')->with('success', "Alat {$alat->nama_alat} berhasil diperbarui.");
    }

    // ══════════════════════════════════════════════════════════════════════
    //  RIWAYAT PERUBAHAN
    // ══════════════════════════════════════════════════════════════════════

    public function riwayat(Request $request)
    {
        $jenisData = $request->query('jenis_data');

        $query = LogAktivitas::with('user')
            ->whereNotNull('jenis_data')
            ->orderByDesc('waktu');

        if ($jenisData) {
            $query->where('jenis_data', $jenisData);
        }

        $logs = $query->paginate(20)->appends($request->query());

        return view('manajemen.riwayat', compact('logs', 'jenisData'));
    }
}

