<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedStatusSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'На модерации',
                'code' => 'pending'
            ],
            [
                'name' => 'Подтвержден',
                'code' => 'success'
            ],
            [
                'name' => 'Отклонен',
                'code' => 'failed'
            ],
        ];

        DB::table('feed_statuses')->insert($data);
    }
}
