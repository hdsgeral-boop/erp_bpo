<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    public function stockLevels()
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $products = Product::where('company_id', $companyId)
            ->where('is_inventory', true)
            ->orderBy('name')
            ->get();

        return view('logistica.stock', compact('products'));
    }

    public function guias(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $search = $request->input('search');
        $docType = $request->input('doc_type');
        $status = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Sale::where('company_id', $companyId)
            ->whereIn('doc_type', ['GT', 'GR'])
            ->with(['customer', 'warehouse', 'items.product']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('doc_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('tax_id', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($docType)) {
            $query->where('doc_type', $docType);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $guias = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(15);

        $mainWarehouse = Warehouse::where('company_id', $companyId)->first();
        $mainWarehouseName = $mainWarehouse ? $mainWarehouse->name : 'Armazém Central';

        $stats = [
            'total_count' => Sale::where('company_id', $companyId)->whereIn('doc_type', ['GT', 'GR'])->count(),
            'valid_count' => Sale::where('company_id', $companyId)->whereIn('doc_type', ['GT', 'GR'])->where('status', '!=', 'CANCELLED')->count(),
            'main_warehouse' => $mainWarehouseName,
        ];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(compact('guias', 'stats'));
        }

        return view('logistica.guias.index', compact('guias', 'stats'));
    }
}
