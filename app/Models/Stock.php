<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasUlids;
    
    protected $table = 'stock';
    
    protected $fillable = [
        'id_barang',
        'limit',
    ];

    protected function casts(): array
    {
        return [
            'limit' => 'integer',
        ];
    }

    public function barang():BelongsTo{
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}