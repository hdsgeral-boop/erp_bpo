<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('auditable_type', 'like', "%{$search}%")
              ->orWhere('event', 'like', "%{$search}%");
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        $logs = $query->paginate(50);
        
        return view('admin.logs.index', compact('logs'));
    }
}
