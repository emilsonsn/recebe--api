<?php

namespace App\Services\Order;

use App\Imports\OrdersImport;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OrderService
{
    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $searchTerm = $request->input('search_term');
            $userId = $request->input('user_id');
            $orderDate = $request->input('order_date');

            $orders = Order::query();

            if ($searchTerm) {
                $orders->where(function($query) use ($searchTerm) {
                    $query->where('order_id', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('reference_id', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('invoice_number', 'LIKE', "%{$searchTerm}%");
                });
            }

            if ($userId) {
                $orders->where('user_id', $userId);
            }

            if ($orderDate) {
                $orders->whereDate('order_date', $orderDate);
            }

            return $orders->paginate($perPage);
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function import($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls,ods,csv',
            ]);
    
            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }

            $import = new OrdersImport;

            Excel::import(new $import, $request->file('file'));

            return [ 'status' => true, 'data' => $import->count];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'type' => 'required|string',
                'order_id' => 'required|string',
                'reference_id' => 'required|string',
                'sequence_id' => 'nullable|string',
                'integrator_id' => 'nullable|string',
                'shipping_id' => 'nullable|string',
                'marketplace' => 'nullable|string',
                'account' => 'nullable|string',
                'invoice_number' => 'nullable|string',
                'invoice_series' => 'nullable|string',
                'order_date' => 'required|date',
                'release_date' => 'nullable|date',
                'sale_value' => 'required|numeric',
                'refund_sale' => 'nullable|numeric',
                'commission' => 'nullable|numeric',
                'refund_commission' => 'nullable|numeric',
                'shipping_fee' => 'nullable|numeric',
                'refund_shipping_fee' => 'nullable|numeric',
                'campaigns' => 'nullable|numeric',
                'refund_campaigns' => 'nullable|numeric',
                'taxes' => 'nullable|numeric',
                'refund_taxes' => 'nullable|numeric',
                'other_credits' => 'nullable|numeric',
                'other_debits' => 'nullable|numeric',
                'net_result' => 'nullable|numeric',
                'sync_date' => 'nullable|date',
                'status' => 'nullable|boolean',
                'user_id' => 'nullable|integer|exists:users,id',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }

            $order = Order::create($validator->validated());

            return ['status' => true, 'data' => $order];
        } catch (Exception $error) {
            DB::rollBack();
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $order_id)
    {
        try {
            $rules = [
                'type' => 'required|string',
                'order_id' => 'required|string',
                'reference_id' => 'required|string',
                'sequence_id' => 'nullable|string',
                'integrator_id' => 'nullable|string',
                'shipping_id' => 'nullable|string',
                'marketplace' => 'nullable|string',
                'account' => 'nullable|string',
                'invoice_number' => 'nullable|string',
                'invoice_series' => 'nullable|string',
                'order_date' => 'required|date',
                'release_date' => 'nullable|date',
                'sale_value' => 'required|numeric',
                'refund_sale' => 'nullable|numeric',
                'commission' => 'nullable|numeric',
                'refund_commission' => 'nullable|numeric',
                'shipping_fee' => 'nullable|numeric',
                'refund_shipping_fee' => 'nullable|numeric',
                'campaigns' => 'nullable|numeric',
                'refund_campaigns' => 'nullable|numeric',
                'taxes' => 'nullable|numeric',
                'refund_taxes' => 'nullable|numeric',
                'other_credits' => 'nullable|numeric',
                'other_debits' => 'nullable|numeric',
                'net_result' => 'nullable|numeric',
                'sync_date' => 'nullable|date',
                'status' => 'nullable|boolean',
                'user_id' => 'nullable|integer|exists:users,id',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }

            $order = Order::find($order_id);

            if (!$order) {
                throw new Exception('Pedido não encontrado', 404);
            }

            $order->update($validator->validated());

            return ['status' => true, 'data' => $order];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function delete($order_id)
    {
        try {
            $order = Order::find($order_id);

            if (!$order) {
                throw new Exception('Pedido não encontrado', 404);
            }
            
            $orderId = $order->id;
            $order->delete();

            return ['status' => true, 'data' => ['order_id' => $orderId]];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}