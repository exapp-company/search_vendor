<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['country_id' => 1, 'name' => 'Москва'],
            ['country_id' => 1, 'name' => 'Санкт-Петербург'],
            ['country_id' => 1, 'name' => 'Новосибирск'],
            ['country_id' => 1, 'name' => 'Екатеринбург'],
            ['country_id' => 1, 'name' => 'Нижний Новгород'],
            ['country_id' => 1, 'name' => 'Казань'],
            ['country_id' => 1, 'name' => 'Челябинск'],
            ['country_id' => 1, 'name' => 'Омск'],
            ['country_id' => 1, 'name' => 'Ростов-на-Дону'],
        ];

        DB::table('cities')->insert($data);
    }
}
