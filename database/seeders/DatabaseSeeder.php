<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {

        $this->call([
            UseerSeeder::class,
            CategorySeeder::class,
            IdentitySeeder::class,
            ReasonSeeder::class,

        ]);

        Customer::factory(20)->create();
        Supplier::factory(5)->create();
        Product::factory(100)->create();

    }
}
