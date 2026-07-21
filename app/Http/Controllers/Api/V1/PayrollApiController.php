<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PayrollRun;
use App\Models\PayrollReceipt;
use App\Models\Employee;
use App\Models\PayrollItem;

class PayrollApiController extends Controller
{
    public function getRuns(Request $request)
    {
        $runs = PayrollRun::where('company_id', 1)->orderBy('id', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $runs]);
    }

    public function getReceipts($run_id)
    {
        $receipts = PayrollReceipt::where('payroll_run_id', $run_id)->with('employee')->get();
        return response()->json(['status' => 'success', 'data' => $receipts]);
    }

    public function getPayrollItems()
    {
        $items = PayrollItem::where('company_id', 1)->orderBy('calculation_order')->get();
        return response()->json(['status' => 'success', 'data' => $items]);
    }

    public function getEmployeeReceipts($employee_id)
    {
        $receipts = PayrollReceipt::where('employee_id', $employee_id)->with('payrollRun')->get();
        return response()->json(['status' => 'success', 'data' => $receipts]);
    }
}
