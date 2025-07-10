<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{

    use HasUlids;
    
    protected $fillable =[
    'nama_barang',
    'jumlah',
    'harga'
    ];

    protected $table = 'barang';

    protected function casts(): array
    {
        return [
            'nama_barang' => 'string',
        ];
    }

    public function stock():HasMany{
        return $this->hasMany(Stock::class,'id_barang');
    }

    public function order():HasMany{
        return $this->hasMany(Order::class,'id_barang');
    }
}