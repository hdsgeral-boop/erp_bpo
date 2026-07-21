<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_payments' => \App\Models\TreasuryDocument::where('type', 'PG')->sum('total_value'),
            'total_receipts' => \App\Models\TreasuryDocument::where('type', 'RC')->sum('total_value'),
            'balance' => \App\Models\BankStatementLine::where('type_dc', 'D')->sum('value') - \App\Models\BankStatementLine::where('type_dc', 'C')->sum('value'),
        ];

        $recentDocs = \App\Models\TreasuryDocument::orderBy('doc_date', 'desc')->take(5)->get();
        $recentStatements = \App\Models\BankStatementLine::orderBy('date', 'desc')->take(5)->get();

        return view('treasury.dashboard', compact('stats', 'recentDocs', 'recentStatements'));
    }
}
