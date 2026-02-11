<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDokterRequest;
use App\Http\Requests\UpdateDokterRequest;
use App\Models\User;
use App\Models\Dokter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dokter = User::with('dokter')
            ->where('role', 'dokter')
            ->latest()
            ->paginate(10);

        return view('admin.dokter.index', compact('dokter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|min:8',

            'nip' => 'required|unique:dokter,nip',
            'education' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'dokter',
            ]);

            Dokter::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'education' => $data['education'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);
        });

        return response()->json([
            'message' => 'Dokter berhasil ditambahkan'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::with('dokter')->findOrFail($id);

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id)
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('users', 'phone')->ignore($user->id)
                ],
                'nip' => [
                    'required',
                    Rule::unique('dokter', 'nip')->ignore($user->dokter->id)
                ],
                'education' => 'nullable|string|max:255',
                'specialization' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);

            DB::transaction(function () use ($user, $data) {
                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ]);

                $user->dokter->update([
                    'nip' => $data['nip'],
                    'education' => $data['education'],
                    'specialization' => $data['specialization'],
                    'bio' => $data['bio'],
                    'status' => $data['status'],
                ]);
            });

            return response()->json([
                'message' => 'Data dokter berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Gagal update dokter: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::where('role', 'dokter')->findOrFail($id);
        $user->delete(); // dokter ikut terhapus (cascade)

        return response()->json([
            'message' => 'Data dokter berhasil dihapus'
        ]);
    }
}
