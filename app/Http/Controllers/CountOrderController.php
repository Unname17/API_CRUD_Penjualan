<?php

namespace App\Http\Controllers;

use App\Models\Order;

class CountOrderController extends Controller
{
    public function count()
    {
        $totalOrder = Order::count();
        return response()->json(['count' => $totalOrder]);
    }
}
