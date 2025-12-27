<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DailyProfitExport;
use App\Exports\DailySalesExport;
use App\Exports\InventoryExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function sales_export(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return Excel::download(new DailySalesExport($start_date, $end_date), 'daily-sales-report.xlsx');
    }
    public function profit_export(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        return Excel::download(new DailyProfitExport($start_date, $end_date), 'daily-profit-report.xlsx');
    }
    public function inventory_export(Request $request)
    {
        return Excel::download(new InventoryExport, 'inventory-report.xlsx');
    }
}
