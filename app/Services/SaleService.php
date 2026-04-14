<?php

namespace App\Services;

use App\Enums\CreditStatus;
use App\Enums\PaymentMethod;
use App\Enums\SaleKind;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function __construct(
        private readonly InventoryPepsService $peps,
        private readonly SpecialCashService $specialCash,
    ) {}

    /**
     * @param  array<int, array{product_id: int, quantity: string, unit_price: string, sale_unit?: string|null}>  $lines
     * @param  array<int, array{method: PaymentMethod, amount: string}>  $payments  Vacío si es por cobrar sin cobro aún
     */
    public function registerSale(
        SaleKind $kind,
        User $seller,
        array $lines,
        array $payments,
        ?int $customerId,
        ?string $customerName,
        ?string $customerPhone,
        ?string $customerAddress,
        ?string $notes = null,
        ?string $cashChangeDelivered = null,
        ?string $cashChangeNote = null,
    ): Sale {
        if ($kind === SaleKind::Credit && (empty($customerId) && (empty($customerName) || empty($customerPhone)))) {
            throw new InvalidArgumentException('Las ventas por cobrar requieren nombre y teléfono del cliente.');
        }

        if ($lines === []) {
            throw new InvalidArgumentException('La venta debe tener al menos una línea.');
        }

        return DB::transaction(function () use ($kind, $seller, $lines, $payments, $customerId, $customerName, $customerPhone, $customerAddress, $notes, $cashChangeDelivered, $cashChangeNote) {
            $subtotal = '0';
            foreach ($lines as $line) {
                $lineTotal = bcmul((string) $line['quantity'], (string) $line['unit_price'], 2);
                $subtotal = bcadd($subtotal, $lineTotal, 2);
            }

            $sale = Sale::create([
                'sale_kind' => $kind,
                'sold_at' => now(),
                'sold_by_user_id' => $seller->id,
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_address' => $customerAddress,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'credit_status' => $kind === SaleKind::Credit ? CreditStatus::Pending : null,
                'notes' => $notes,
            ]);

            foreach ($lines as $line) {
                $qty = (string) $line['quantity'];
                $unit = (string) $line['unit_price'];
                $lineTotal = bcmul($qty, $unit, 2);

                $saleUnit = $line['sale_unit'] ?? null;
                if ($saleUnit !== null && $saleUnit !== 'pack' && $saleUnit !== 'each') {
                    $saleUnit = null;
                }

                $saleLine = SaleLine::create([
                    'sale_id' => $sale->id,
                    'product_id' => $line['product_id'],
                    'sale_unit' => $saleUnit,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'line_total' => $lineTotal,
                ]);

                $product = $saleLine->product()->firstOrFail();
                $inventoryQty = $this->inventoryQuantityForLine($product, $qty, $saleUnit);
                $this->peps->allocateAndConsume($saleLine, $product, $inventoryQty);
            }

            foreach ($payments as $payment) {
                $sale->payments()->create([
                    'method' => $payment['method'],
                    'amount' => (string) $payment['amount'],
                    'recorded_by_user_id' => $seller->id,
                ]);
            }

            if ($kind === SaleKind::Cash) {
                $paid = '0';
                $qrTotal = '0';
                foreach ($payments as $p) {
                    $amt = (string) $p['amount'];
                    $paid = bcadd($paid, $amt, 2);
                    if ($p['method'] === PaymentMethod::Qr) {
                        $qrTotal = bcadd($qrTotal, $amt, 2);
                    }
                }

                $change = ($cashChangeDelivered !== null && $cashChangeDelivered !== '')
                    ? (string) $cashChangeDelivered
                    : '0';

                if (bccomp($change, '0', 2) < 0) {
                    throw new InvalidArgumentException('El vuelto en efectivo no puede ser negativo.');
                }

                $over = bcsub($paid, $subtotal, 2);

                if (bccomp($over, '0', 2) > 0) {
                    if (bccomp($change, $over, 2) !== 0) {
                        throw new InvalidArgumentException(
                            'Cobró más que el total de la venta. Indique en «Vuelto en efectivo» exactamente la diferencia: '
                            .number_format((float) $over, 2).' Bs (total cobrado '.number_format((float) $paid, 2).' Bs menos venta '.number_format((float) $subtotal, 2).' Bs).'
                        );
                    }
                    if (bccomp($qrTotal, '0', 2) <= 0) {
                        throw new InvalidArgumentException('Para registrar vuelto por exceso de cobro debe haber al menos un pago por QR.');
                    }

                    $descParts = ['Vuelto por exceso en pago QR (venta #'.$sale->id.').'];
                    if ($cashChangeNote !== null && trim($cashChangeNote) !== '') {
                        $descParts[] = trim($cashChangeNote);
                    }

                    $this->specialCash->recordDepositChange(
                        sale: $sale,
                        performedBy: $seller,
                        qrAmount: $qrTotal,
                        cashOut: $change,
                        description: implode(' ', $descParts),
                    );
                } else {
                    if (bccomp($change, '0', 2) > 0) {
                        throw new InvalidArgumentException('Indicó vuelto en efectivo pero el total cobrado no supera el total de la venta.');
                    }
                    if (bccomp($paid, $subtotal, 2) !== 0) {
                        throw new InvalidArgumentException('El total cobrado en efectivo/QR debe coincidir con el total de la venta al contado.');
                    }
                }
            }

            return $sale->fresh(['lines', 'payments']);
        });
    }

    /**
     * Stock en unidad mínima (ej. cigarros). Cajetilla multiplica por units_per_pack.
     */
    private function inventoryQuantityForLine(Product $product, string $lineQuantity, ?string $saleUnit): string
    {
        if (! $product->isDualUnitProduct()) {
            return $lineQuantity;
        }

        if ($saleUnit === 'pack') {
            return bcmul($lineQuantity, (string) $product->units_per_pack, 3);
        }

        if ($saleUnit === 'each') {
            return $lineQuantity;
        }

        throw new InvalidArgumentException(
            'El producto «'.$product->name.'» se vende por cajetilla y por unidad: indique el modo de venta en la línea.'
        );
    }
}
