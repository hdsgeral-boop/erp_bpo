<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductCategory;
use Response;

class AnnualInventoryExportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $company = Company::find($companyId) ?? Company::first();

        $year = $request->input('year', date('Y'));
        $warehouseId = $request->input('warehouse_id');
        $categoryId = $request->input('category_id');

        $warehouses = Warehouse::where('company_id', $companyId)->get();
        $categories = ProductCategory::where('company_id', $companyId)->get();

        $query = Product::where('company_id', $companyId)->where('is_inventory', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->with('category')->get();

        $inventoryData = $products->map(function ($product) {
            $qty = floatval($product->stock_qty);
            $unitCost = floatval($product->unit_cost > 0 ? $product->unit_cost : ($product->unit_price * 0.7)); // Custo de aquisição ou estimativa PMP
            $totalValue = $qty * $unitCost;

            return [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'category' => $product->category?->name ?? 'Geral',
                'unit' => 'Uni',
                'stock_qty' => $qty,
                'unit_cost' => $unitCost,
                'total_value' => $totalValue
            ];
        });

        $totalItems = $inventoryData->count();
        $totalStockQty = $inventoryData->sum('stock_qty');
        $totalInventoryValue = $inventoryData->sum('total_value');

        return view('inventory.annual_inventory', compact(
            'company',
            'year',
            'warehouseId',
            'categoryId',
            'warehouses',
            'categories',
            'inventoryData',
            'totalItems',
            'totalStockQty',
            'totalInventoryValue'
        ));
    }

    public function exportXml(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $company = Company::find($companyId) ?? Company::first();
        $year = $request->input('year', date('Y'));

        $products = Product::where('company_id', $companyId)->where('is_inventory', true)->with('category')->get();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><AnnualInventoryAGT></AnnualInventoryAGT>');
        $header = $xml->addChild('Header');
        $header->addChild('TaxRegistrationNumber', $company->nif ?? '999999999');
        $header->addChild('CompanyName', htmlspecialchars($company->name ?? 'Empresa'));
        $header->addChild('FiscalYear', $year);
        $header->addChild('DateCreated', date('Y-m-d'));
        $header->addChild('SoftwareCertificateNumber', '317/AGT/2026');

        $productsNode = $xml->addChild('ProductsStock');
        $grandTotal = 0;

        foreach ($products as $p) {
            $qty = floatval($p->stock_qty);
            $unitCost = floatval($p->unit_cost > 0 ? $p->unit_cost : ($p->unit_price * 0.7));
            $totalVal = $qty * $unitCost;
            $grandTotal += $totalVal;

            $item = $productsNode->addChild('Product');
            $item->addChild('ProductCode', htmlspecialchars($p->code));
            $item->addChild('ProductDescription', htmlspecialchars($p->name));
            $item->addChild('ProductCategory', htmlspecialchars($p->category?->name ?? 'Geral'));
            $item->addChild('ClosingStockQuantity', number_format($qty, 2, '.', ''));
            $item->addChild('UnitCostPrice', number_format($unitCost, 2, '.', ''));
            $item->addChild('TotalInventoryValue', number_format($totalVal, 2, '.', ''));
        }

        $xml->addChild('GrandTotalValue', number_format($grandTotal, 2, '.', ''));

        $filename = "Inventario_Anual_AGT_{$company->nif}_{$year}.xml";
        return Response::make($xml->asXML(), 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportCsv(Request $request)
    {
        $companyId = session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1);
        $company = Company::find($companyId) ?? Company::first();
        $year = $request->input('year', date('Y'));

        $products = Product::where('company_id', $companyId)->where('is_inventory', true)->with('category')->get();

        $filename = "Inventario_Anual_AGT_{$company->nif}_{$year}.csv";
        $handle = fopen('php://output', 'w');

        ob_start();
        // UTF-8 BOM para Excel ler acentos em português
        fputs($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['Codigo Produto', 'Descricao', 'Categoria', 'Unidade', 'Quantidade em Stock', 'Custo Medio (PMP Kz)', 'Valor Total (Kz)'], ';');

        foreach ($products as $p) {
            $qty = floatval($p->stock_qty);
            $unitCost = floatval($p->unit_cost > 0 ? $p->unit_cost : ($p->unit_price * 0.7));
            $totalVal = $qty * $unitCost;

            fputcsv($handle, [
                $p->code,
                $p->name,
                $p->category?->name ?? 'Geral',
                'Uni',
                number_format($qty, 2, ',', '.'),
                number_format($unitCost, 2, ',', '.'),
                number_format($totalVal, 2, ',', '.')
            ], ';');
        }

        fclose($handle);
        $content = ob_get_clean();

        return Response::make($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        return $this->index($request);
    }
}
