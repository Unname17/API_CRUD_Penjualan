<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasUlids;
    
    protected $table = 'order';
    
    protected $fillable = [
        'id_customer',
        'id_barang',
        'order_date',
        'jumlah_barang',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'id_customer' => 'string',
            'id_barang' => 'string',
        ];
    }

    public function customer():BelongsTo{
        return $this->belongsTo(Customer::class, 'id_customer', 'id');
    }

    public function barang():BelongsTo{
        return $this->belongsTo(Barang::class, 'id_barang', 'id'); // <- pastikan cari ke `barang.id`
    }
}