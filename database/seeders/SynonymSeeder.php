<?php

namespace Database\Seeders;

use App\Models\Synonym;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SynonymSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Synonym::create([
            'title' => 'Синонимы для iPhone',
            'synonyms' => ['айфон', "iphone"]
        ]);
    }
}
