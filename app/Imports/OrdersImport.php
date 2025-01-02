<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class OrdersImport implements ToModel, WithHeadingRow
{
    public $count = 0;

    public function model(array $row)
    {
        $this->count++;

        foreach($row as $key => $value){
            $row[$key] = str_replace(['="', '"'],['', ''], $row[$key]);
        }

        return new Order([
            'type' => $row['tipo'] ?? null,
            'order_id' => $row['id_pedido'] ?? null,
            'reference_id' => $row['ref_pedido'] ?? null,
            'sequence_id' => $row['id_sequencial'] ?? null,
            'integrator_id' => $row['id_integrador'],
            'shipping_id' => $row['id_frete'] ?? null,
            'marketplace' => $row['marketplace'] ?? null,
            'account' => $row['conta'] ?? null,
            'invoice_number' => $row['nota_fiscal'] ?? null,
            'invoice_series' => $row['serie_nf'] ?? null,
            'order_date' => $this->transformExcelDate($row['data_pedido']),
            'release_date' => $this->transformExcelDate($row['data_repasse']),
            'sale_value' => $this->transformValue($row['venda']),
            'refund_sale' => $this->transformValue($row['estorno_venda'] ?? '0'),
            'commission' => $this->transformValue($row['comissao'] ?? '0'),
            'refund_commission' => $this->transformValue($row['estorno_comissao'] ?? '0'),
            'shipping_fee' => $this->transformValue($row['frete'] ?? '0'),
            'refund_shipping_fee' => $this->transformValue($row['estorno_frete'] ?? '0'),
            'campaigns' => $this->transformValue($row['campanhas'] ?? '0'),
            'refund_campaigns' => $this->transformValue($row['estorno_campanhas'] ?? '0'),
            'taxes' => $this->transformValue($row['impostos_taxas'] ?? '0'),
            'refund_taxes' => $this->transformValue($row['estorno_impostos_taxas'] ?? '0'),
            'other_credits' => $this->transformValue($row['outros_creditos'] ?? '0'),
            'other_debits' => $this->transformValue($row['outros_debitos'] ?? '0'),
            'net_result' => $this->transformValue($row['resultado_liquido']),
        ]);
    }

    private function transformExcelDate($excelDate)
    {
        $date = Carbon::createFromFormat('Y-m-d', '1899-12-30')->addDays($excelDate)->format('Y-m-d');
        return $date;
    }

    private function transformValue($value)
    {
        return str_replace(',', '.', $value);
    }
}
