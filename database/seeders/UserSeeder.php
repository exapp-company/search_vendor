<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'first_name' => 'Admin',
                'email' => 'webadmin@poiskzip.ru',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'refresh_token' => Str::uuid(),
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ],
        ];

        DB::table('users')->insert($data);
    }
}
