<?php

namespace Database\Seeders;

use App\Models\DressCode;
use Illuminate\Database\Seeder;

class DressCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dressCodes = [
            'PSL',
            'Batik/Lengan panjang',
            'Yang berlaku pada hari itu',
            'PDH Khaki',
            'PDUB',
            'Menyesuaikan',
            'Olah Raga',
            'Muslim',
            'Pakaian Adat',
            'Smart Casual',
        ];

        foreach ($dressCodes as $name) {
            DressCode::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
