<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Order extends Model
{
    use HasFactory;
    protected $table="order";
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'total_amount',
        'status',
        'user_socket_token',
    ];
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
