<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotizaciones #{{ $model->serie }}-{{ str_pad($model->correlative, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* Configuración general */
        @page {
            margin: 0cm 0cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif; /* Fuentes seguras para PDF */
            font-size: 12px;
            color: #333;
            margin-top: 3cm; /* Espacio para el header si usaras fixed, sino margen normal */
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
            line-height: 1.4;
        }

        /* Utilidades */
        .w-100 { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .gray-text { color: #555; }

        /* Tablas de estructura (Header invisible) */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border: none;
        }
        .header-table td {
            vertical-align: top;
        }

        /* Sección de información (Proveedor/Almacén) */
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px; /* DomPDF soporta border-radius básico */
        }
        .info-title {
            font-size: 10px;
            color: #777;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }

        /* Tabla de Productos */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .products-table th {
            background-color: #2c3e50; /* Color oscuro profesional */
            color: #ffffff;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .products-table td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            vertical-align: middle;
        }
        /* Zebra striping (filas alternas) */
        .tr-even {
            background-color: #f9f9f9;
        }

        /* Sección de Totales */
        .totals-table {
            width: 40%;
            float: right; /* Float funciona bien para bloques pequeños al final */
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .total-row td {
            border-top: 2px solid #333;
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }

        /* Badge de estado (opcional) */
        .badge {
            background-color: #eee;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 20px; font-weight: bold; color: #2c3e50;">Two Brothers</div>
                <div style="font-size: 14px;">Stickers & Design</div>
                <div class="gray-text" style="font-size: 10px; margin-top: 5px;">
                    Dirección de tu negocio<br>
                    Teléfono: 5632220120<br>
                    Email: twobrothers37@gmail.com
                </div>
            </td>

            <td style="width: 40%; text-align: right;">
                <div style="font-size: 18px; font-weight: bold; color: #e74c3c;">COTIZACIONES</div>
                <div style="font-size: 14px; margin-top: 5px;"># {{ $model->serie }}-{{ str_pad($model->correlative, 4, '0', STR_PAD_LEFT) }}</div>
                <div style="margin-top: 10px;">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($model->date)->format('d/m/Y') }}<br>
                    <span style="font-size: 10px; color: #888;">Hora: {{ \Carbon\Carbon::parse($model->created_at)->format('H:i A') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <br>

    <table class="w-100" style="margin-bottom: 20px;">
        <tr>
            <td style="width: 48%; padding-right: 2%;">
                <div class="info-box">
                    <span class="info-title">Cliente</span>
                    <strong style="font-size: 13px;">{{ $model->customer->name ?? 'Público General' }}</strong><br>
                    <span style="font-size: 11px;">Email: {{ $model->customer->email ?? '' }}</span>
                    <span style="font-size: 11px;">Telefono: {{ $model->customer->phone ?? '' }}</span>
                    <span style="font-size: 11px;">{{ $model->customer->address ?? '' }}</span>
                </div>
            </td>
            <td style="width: 48%; padding-left: 2%;">
                <div class="info-box">
                    <span class="info-title">Detalles de Entrega</span>
                    <strong>Almacén:</strong> {{ $model->warehouse->name ?? 'Principal' }}<br>
                    <strong>Obs:</strong> {{ $model->observation ?? 'Ninguna' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="products-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 45%;">Descripción del Producto</th>
                <th style="width: 15%; text-align: right;">Precio Unit.</th>
                <th style="width: 15%; text-align: center;">Cant.</th>
                <th style="width: 20%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($model->products as $index => $product)
                {{-- Usamos $loop->even para la clase cebra --}}
                <tr class="{{ $loop->even ? 'tr-even' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="text-bold">{{ $product->name }}</span>
                        {{-- Si tienes código de producto, ponlo aquí --}}
                        <br><span style="font-size: 9px; color: #666;">Cód: {{ $product->sku ?? 'N/A' }}</span>
                    </td>
                    <td class="text-right">S/ {{ number_format($product->pivot->price, 2) }}</td>
                    <td class="text-center">{{ $product->pivot->quantity }}</td>
                    <td class="text-right text-bold">S/ {{ number_format($product->pivot->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width: 100%; clear: both;">
        <table class="totals-table">
            <tr>
                <td class="text-right gray-text">Subtotal:</td>
                <td class="text-right">S/ {{ number_format($model->total, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right">Total a Pagar:</td>
                <td class="text-right">S/ {{ number_format($model->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 10px;">
        Documento generado electrónicamente por el sistema de Inventarios.<br>
        Two Brothers Stickers and Design
    </div>

</body>
</html>
