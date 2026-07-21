<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function dashboard()
    {
        // Estatísticas para o Dashboard
        $stats = [
            'total_pedidos' => \App\Models\PurchaseRequest::count(),
            'total_encomendas' => \App\Models\PurchaseOrder::count(),
            'total_faturas' => \App\Models\PurchaseInvoice::count(),
            'total_gasto' => \App\Models\PurchaseInvoice::sum('total_amount'),
        ];
        
        $recentOrders = \App\Models\PurchaseOrder::with('supplier')->orderBy('date', 'desc')->take(5)->get();
        $recentInvoices = \App\Models\PurchaseInvoice::with('supplier')->orderBy('date', 'desc')->take(5)->get();

        return view('purchases.dashboard', compact('stats', 'recentOrders', 'recentInvoices'));
    }
}
