<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::insert([
            ['name' => 'Kelas 7A', 'type' => 'Kelas'],
            ['name' => 'Kelas 9B', 'type' => 'Kelas'],
            ['name' => 'Lab IPA', 'type' => 'Laboratorium'],
            ['name' => 'Gudang Utama', 'type' => 'Gudang'],
        ]);
    }
}
