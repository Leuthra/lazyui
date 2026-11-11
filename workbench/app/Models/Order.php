<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Workbench\App\Enums\OrderStatus;

class Order extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => OrderStatus::class, 'items' => 'array', 'total' => 'decimal:2', 'shipped_at' => 'datetime'];
    }
}
