<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'orders';

    protected $fillable = [
        'type',
        'order_id',
        'reference_id',
        'sequence_id',
        'integrator_id',
        'shipping_id',
        'marketplace',
        'account',
        'invoice_number',
        'invoice_series',
        'order_date',
        'release_date',
        'sale_value',
        'refund_sale',
        'commission',
        'refund_commission',
        'shipping_fee',
        'refund_shipping_fee',
        'campaigns',
        'refund_campaigns',
        'taxes',
        'refund_taxes',
        'other_credits',
        'other_debits',
        'net_result',
        'sync_date',
        'status',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
