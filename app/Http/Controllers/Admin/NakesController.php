<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNakesRequest;
use App\Http\Requests\UpdateNakesRequest;
use App\Models\User;
use App\Models\Nakes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class NakesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nakes = User::with('nakes')
            ->where('role', 'nakes')
            ->latest()
            ->paginate(10);

        return view('admin.nakes.index', compact('nakes'));
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

            'nip' => 'required|unique:nakes,nip',
            'education' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'nakes',
            ]);

            Nakes::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'education' => $data['education'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'position' => $data['position'] ?? null,
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);
        });

        return response()->json([
            'message' => 'Tenaga kesehatan berhasil ditambahkan'
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::with('nakes')->findOrFail($id);

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
                    Rule::unique('nakes', 'nip')->ignore($user->nakes->id)
                ],
                'education' => 'nullable|string|max:255',
                'specialization' => 'nullable|string|max:255',
                'position' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ]);

            DB::transaction(function () use ($user, $data) {
                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ]);

                $user->nakes->update([
                    'nip' => $data['nip'],
                    'education' => $data['education'],
                    'specialization' => $data['specialization'],
                    'position' => $data['position'],
                    'bio' => $data['bio'],
                    'status' => $data['status'],
                ]);
            });

            return response()->json([
                'message' => 'Data nakes berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Gagal update nakes: ' . $e->getMessage());
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
        $user = User::where('role', 'nakes')->findOrFail($id);
        $user->delete(); // nakes ikut terhapus (cascade)

        return response()->json([
            'message' => 'Data nakes berhasil dihapus'
        ]);
    }
}
