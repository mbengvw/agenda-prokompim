<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['admin', 'sekpri-bupati', 'protokol', 'pendamping'];
        foreach ($roles as $role) {
            User::factory()->create([
                'name' => ucwords(str_replace('-', ' ', $role)),
                'email' => "{$role}@example.com",
                'role' => $role,
            ]);
        }

        $this->call([
            LeaderSeeder::class,
        ]);
    }
}
