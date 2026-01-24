<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class KardexService
{
    /**
     * Obtiene el último registro con bloqueo para actualización.
     */
    public function getLastRecord($productId, $warehouseId)
    {
        // Usamos lockForUpdate para que otros procesos esperen a que este termine
        return Inventory::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->latest('id')
            ->lockForUpdate()
            ->first();
    }

    public function registerEntry($id, $model, array $product, $warehouseId, $detail)
    {
        return $this->processMovement('in', $id, $model, $product, $warehouseId, $detail);
    }

    public function registerExit($id, $model, array $product, $warehouseId, $detail)
    {
        return $this->processMovement('out', $id, $model, $product, $warehouseId, $detail);
    }

    private function processMovement($type, $id, $model, array $product, $warehouseId, $detail)
    {
        $last = $this->getLastRecord($product['id'], $warehouseId);

        $lastQty = $last->quantity_balance ?? 0;
        $lastTotal = $last->total_balance ?? 0;

        $movementQty = $product['quantity'];
        $movementPrice = $product['price'];
        $movementTotal = $movementQty * $movementPrice;



        if ($type === 'in') {
            $newQty = $lastQty + $movementQty;
            $newTotal = $lastTotal + $movementTotal;

            // Entrada: Aumentamos el stock
            Product::where('id', $product['id'])->increment('stock', $product['quantity']);
        } elseif ($type === 'out') {
            $newQty = $lastQty - $movementQty;
            $newTotal = $lastTotal - $movementTotal;

            if ($newQty < 0) {
                // El return no es necesario aquí porque el throw detiene la ejecución
                throw new Exception("Stock insuficiente para el producto ID: {$product['id']}");
            }

            // Salida: CORREGIDO - Usamos decrement para restar del stock
            Product::where('id', $product['id'])->decrement('stock', $product['quantity']);
        }

        // Costo Promedio Ponderado: Evitar división por cero
        $newCost = ($newQty > 0) ? ($newTotal / $newQty) : 0;

        return Inventory::create([
            'detail'             => $detail,
            'product_id'         => $product['id'],
            'warehouse_id'       => $warehouseId,
            'inventoryable_id'   => $id,
            'inventoryable_type' => $model,

            // Columnas de movimiento
            "quantity_{$type}"   => $movementQty,
            "cost_{$type}"       => $movementPrice,
            "total_{$type}"      => $movementTotal,

            // Columnas de balance (Saldos)
            'quantity_balance'   => $newQty,
            'cost_balance'       => $newCost,
            'total_balance'      => $newTotal,
        ]);
    }
}
