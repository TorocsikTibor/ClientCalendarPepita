<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('calendar')->insert([
            'start_date' => '2024-09-08',
            'end_date' => null,
            'repeat' => 0,
            'day' => 6,
            'start_time' => '8:00:00',
            'end_time' => '12:00:00',
            'client_name' => "test user"
        ]);

        DB::table('calendar')->insert([
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-30',
            'repeat' => 2,
            'day' => 0,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'client_name' => "test even"
        ]);

        DB::table('calendar')->insert([
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-30',
            'repeat' => 3,
            'day' => 2,
            'start_time' => '12:00:00',
            'end_time' => '16:00:00',
            'client_name' => "test odd"
        ]);

        DB::table('calendar')->insert([
            'start_date' => '2024-01-01',
            'end_date' => null,
            'repeat' => 1,
            'day' => 4,
            'start_time' => '10:00:00',
            'end_time' => '16:00:00',
            'client_name' => "test every"
        ]);

        DB::table('calendar')->insert([
            'start_date' => '2024-06-01',
            'end_date' => '2024-11-30',
            'repeat' => 1,
            'day' => 3,
            'start_time' => '16:00:00',
            'end_time' => '20:00:00',
            'client_name' => "test every"
        ]);
    }
}
