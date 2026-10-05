<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'seller_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_amount',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'sold_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
