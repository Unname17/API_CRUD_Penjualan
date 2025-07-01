<?php

namespace App\Http\Controllers;

use App\Models\Barang;

class CountBarangController extends Controller
{
    public function count()
    {
        $totalBarang = Barang::count();
        return response()->json(['count' => $totalBarang]);
    }
}
