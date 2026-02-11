<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffAdministrasi; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffAdministrasiController extends Controller
{
    public function index()
    {
        $staff = User::with('staffAdministrasi')
        ->where('role', 'staf_administrasi')
        ->latest()
        ->paginate(10);

        return view('admin.staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8',
            'nip' => 'required|unique:staff_administrasi,nip',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'staf_administrasi',
            ]);

            StaffAdministrasi::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
                'address' => $request->address,
                'status' => $request->status ?? 'active',
            ]);
        });

        return back()->with('success', 'Staff Administrasi berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $staff = $user->staffAdministrasi;

        $request->validate([
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'nip' => ['required', Rule::unique('staff_administrasi')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($request, $user, $staff) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $staff->update([
                'nip' => $request->nip,
                'address' => $request->address,
                'status' => $request->status,
            ]);
        });

        return back()->with('success', 'Data staff diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Cascade delete akan menghapus data di tabel staff_administrasi
        return back()->with('success', 'Staff dihapus');
    }
}