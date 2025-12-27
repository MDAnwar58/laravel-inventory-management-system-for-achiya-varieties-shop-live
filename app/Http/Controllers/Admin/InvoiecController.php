<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalesOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\PrintingContent;
use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InvoiecController extends Controller
{
    public function generatePDF($id)
    {
        $data = SalesOrder::with('sales_order_products.product', 'customer')->findOrFail($id);
        $printing_content = PrintingContent::first();
        $pdf = PDF::loadView('invoice.pdf', compact('data', 'printing_content'));

        return $pdf->download('invoice_' . $id . '.pdf'); // or ->stream() to view in browser
    }
    public function showPrint($id)
    {
        $data = SalesOrder::with('sales_order_products.product', 'customer')->findOrFail($id);
        $printing_content = PrintingContent::first();

        return view('invoice.print', compact('data', 'printing_content')); // normal blade file
    }
    public function showServerSidePrint($id)
    {
        $data = SalesOrder::with('sales_order_products.product')->findOrFail($id);
        $printing_content = PrintingContent::first();

        $pdf = Pdf::loadView('invoice.print-d', compact('data', 'printing_content'))
            ->setPaper('a4'); // A4 size, no browser headers/footers

        return $pdf->stream();
    }
    public function generateCsv($id)
    {
        return Excel::download(new SalesOrdersExport($id), 'sales_order.xlsx');
    }
}
