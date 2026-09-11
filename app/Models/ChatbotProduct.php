<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatbotProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'chatbot_products';

    protected $guarded = ['id'];

    protected $casts = [
        'product_weight' => 'float',
        'product_price' => 'float',
        'fixed_stock_price' => 'float',
        'purchased_price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_modified' => 'datetime',
        'image_Last_modified' => 'datetime',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}