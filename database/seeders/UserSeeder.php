<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Nakes;
use App\Models\Patient;
use App\Models\StaffAdministrasi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat User Admin
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone' => '628523456789',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        // 2. Buat User Dokter
        $dokterUser = User::create([
            'name' => 'Drg. Lia',
            'email' => 'dokter@gmail.com',
            'phone' => '628123456789',
            'password' => Hash::make('password'),
            'role' => 'dokter',
            'whatsapp_verified' => true,
        ]);

        Dokter::create([
            'user_id' => $dokterUser->id,
            'nip' => 'SIP-12345/2026',
            'education' => 'Perguruan Tinggi',
            'specialization' => 'Spesialis Periodonsia',
            'bio' => 'Dokter gigi berpengalaman.',
            'status' => 'active',
        ]);
        
        // 3. Buat User Nakes
        $nakesUser = User::create([
            'name' => 'Sinta',
            'email' => 'nakes@gmail.com',
            'phone' => '628123456780',
            'password' => Hash::make('password'),
            'role' => 'nakes',
            'whatsapp_verified' => true,
        ]);

        Nakes::create([
            'user_id' => $nakesUser->id,
            'nip' => 'SIP-12345/2026',
            'education' => 'Perguruan Tinggi',
            'specialization' => 'Spesialis Periodonsia',
            'position' => 'Apoteker',
            'bio' => 'Dokter gigi berpengalaman.',
            'status' => 'active',
        ]);

        // 4. Buat User Staff Administrasi
        $stafAdminUser = User::create([
            'name' => 'Budi',
            'email' => 'stafadmin@gmail.com',
            'phone' => '628123456781',
            'password' => Hash::make('password'),
            'role' => 'staf_administrasi',
            'whatsapp_verified' => true,
        ]);
        StaffAdministrasi::create([
            'user_id' => $stafAdminUser->id,
            'nip' => 'SIP-54321/2026',
            'address' => 'Jl. Merdeka No. 10',
            'status' => 'active',
        ]);
    }
}
