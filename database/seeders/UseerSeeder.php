<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UseerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            "name"=> "Luis Luna",
            "email"=> "eduarlun4@gmail.com",
            "password"=> bcrypt("Admin123#"),
        ]);
    }
}
