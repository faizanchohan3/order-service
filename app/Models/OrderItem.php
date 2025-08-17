<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'quantity',
        'price',
    ];

    /**
     * Yeh relationship batata hai ke har OrderItem ka talluq ek Order se hai.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
