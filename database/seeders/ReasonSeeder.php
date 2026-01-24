<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            //=============== Ingresos (Type 1)
            [
                "name" => "Ajuste de inventario (Entrada)",
                "type" => "1",
            ],
            [
                "name" => "Devolución de cliente",
                "type" => "1",
            ],
            [
                "name" => "Entrada por producción",
                "type" => "1",
            ],
            [
                "name" => "Corrección de error (Entrada)",
                "type" => "1",
            ],
            [
                "name" => "Compra a proveedor",
                "type" => "1",
            ],

            //=============== Salidas (Type 2)
            [
                "name" => "Ajuste de inventario (Salida)",
                "type" => "2",
            ],
            [
                "name" => "Merma o deterioro",
                "type" => "2",
            ],
            [
                "name" => "Consumo interno",
                "type" => "2",
            ],
            [
                "name" => "Baja por caducidad",
                "type" => "2",
            ],
            [
                "name" => "Garantía a cliente",
                "type" => "2",
            ],
        ];

        foreach ($reasons as $reason) {
            Reason::create($reason);
        }
    }
}
