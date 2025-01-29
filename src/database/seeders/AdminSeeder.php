<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['school_id' => 'admin001'],
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('administrator'),
                'role' => 'admin',
            ]
        );
    }
}
