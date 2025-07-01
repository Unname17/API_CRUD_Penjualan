<?php

namespace App\Http\Controllers;

use App\Models\User;

class CountUserController extends Controller
{
    public function count()
    {
        $totalUsers = User::count();
        return response()->json(['count' => $totalUsers]);
    }
}
