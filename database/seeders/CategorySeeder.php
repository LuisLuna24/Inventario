<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "name" => "Stickers y Gráficos",
                "description" => "Personaliza tu estilo con stickers de alta calidad y diseños exclusivos para tu moto.",
            ],
            [
                "name" => "Refacciones",
                "description" => "Componentes y piezas mecánicas esenciales para mantener tu moto siempre a punto.",
            ],
            [
                "name" => "Aceites y Lubricantes",
                "description" => "Aceites de alto rendimiento y lubricantes especializados para el cuidado del motor.",
            ],
            [
                "name" => "Accesorios",
                "description" => "Complementos y equipo adicional para mejorar la comodidad y funcionalidad de tu viaje.",
            ],
            [
                "name" => "Wrap y Vinilos",
                "description" => "Vinilos premium para cambiar el color o proteger la pintura original de tu motocicleta.",
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $warehouses = [
            [
                "name" => "Two Brothers",
                "location" => "Cuatro Vientos, Ixtapaluca, EDOMEX",
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
