<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Россия', 'code' => 'RU'],
        ];

        DB::table('countries')->insert($data);
    }
}
