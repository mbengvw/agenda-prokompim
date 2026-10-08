<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin',
            'staf_protokol',
            'kabag_protokol',
            'ajudan_bupati',
            'ajudan_wabup',
            'ajudan_sekda',
            'sekpri_bupati',
            'sekpri_wabup',
            'sekpri_sekda',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Buat akun tes untuk masing-masing role
        $testUsers = [
            'admin' => 'Admin Sistem',
            'staf_protokol' => 'Staf Protokol',
            'kabag_protokol' => 'Kabag Protokol',
            'ajudan_bupati' => 'Ajudan Bupati',
            'ajudan_wabup' => 'Ajudan Wabup',
            'ajudan_sekda' => 'Ajudan Sekda',
            'sekpri_bupati' => 'Sekpri Bupati',
            'sekpri_wabup' => 'Sekpri Wabup',
            'sekpri_sekda' => 'Sekpri Sekda',
        ];

        foreach ($testUsers as $role => $name) {
            $user = User::firstOrCreate([
                'email' => $role.'@example.com',
            ], [
                'name' => $name,
                'password' => Hash::make('password'),
            ]);

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
