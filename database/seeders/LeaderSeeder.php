<?php

namespace Database\Seeders;

use App\Models\Leader;
use Illuminate\Database\Seeder;

class LeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaders = [
            [
                'name' => 'Dr. H. Dian Rachmat Yanuar, M.Si.',
                'position' => 'bupati',
                'short_name' => 'Dian',
            ],
            [
                'name' => 'Hj. Tuti Andriani, S.H., M.Kn.',
                'position' => 'wakil bupati',
                'short_name' => 'Tuti',
            ],
            [
                'name' => 'H. Uu Kusmana, S.Sos., M.Si.',
                'position' => 'sekda',
                'short_name' => 'Uu',
            ],
        ];

        foreach ($leaders as $leader) {
            Leader::create($leader);
        }
    }
}
