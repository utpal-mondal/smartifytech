<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Price_list;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; 

    protected $fillable = [
    	'price_list_id',
        'pdf_name',
        'quantity',
        'model',
        'type',
        'price'
    ];

    public function priceList()
	{
	    return $this->belongsTo(Price_list::class, 'price_list_id');
	}

}
