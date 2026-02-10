<?php

namespace App\Exports\Products;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{

    public function array(): array
    {
        return [
            [
                'Producto de ejemplo',
                'descripcion',
                'sku123',
                '10.99',
                '1'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Descripción',
            'SKU',
            'Precio',
            'Categoría ID'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Detectar el rango total de datos (para no adivinar hasta qué fila llega)
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $fullRange = 'A1:' . $highestColumn . $highestRow;

        return [
            // --- ESTILOS DEL ENCABEZADO (FILA 1) ---
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['argb' => 'FFFFFFFF'], // Texto blanco
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2A58AD'], // Azul corporativo (puedes cambiarlo)
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // --- ESTILOS DE TODA LA TABLA (BORDES Y ALINEACIÓN GENERAL) ---
            $fullRange => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'], // Borde negro fino
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER, // Centrar verticalmente todo el contenido
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setSelectedCell('A1');
            }
        ];
    }
}
