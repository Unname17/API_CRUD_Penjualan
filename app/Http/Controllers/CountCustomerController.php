<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class CountCustomerController extends Controller
{
    public function count()
    {
        $totalCustomers = Customer::count();
        return response()->json(['count' => $totalCustomers]);
    }
}
