<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    public function stockLevels()
    {
        $products = \App\Models\Product::where('is_inventory', true)
                        ->orderBy('name')
                        ->get();
        return view('logistica.stock', compact('products'));
    }
}
