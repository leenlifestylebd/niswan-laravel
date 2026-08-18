<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'qty'             => 'integer',
        'delivery_charge' => 'integer',
        'total'           => 'integer',
        'created_at'      => 'datetime',
    ];

    public function statusLabel(): string
    {
        return config('store.statuses')[$this->status] ?? $this->status;
    }
}
