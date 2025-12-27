<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PrintingStoreOrUpdateRequest;
use App\Models\PrintingContent;
use Illuminate\Http\Request;

class PrintingContentController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['name' => 'Printing Content', 'route' => null, 'icon' => null]
        ];
        $printing_content = PrintingContent::first();
        return view('pages.admin.printing-content.base', compact('breadcrumbs', 'printing_content'));
    }
    public function store_or_update(PrintingStoreOrUpdateRequest $request)
    {
        $data = $request->validated();
        $id = $request->input('id');
        $printingContent = PrintingContent::updateOrCreate(
            ['id' => $id], // <- use request->input() not $data->id
            [
                'phone_number' => $data['phone_number'],
                'phone_number2' => $data['phone_number2'],
                'location' => $data['location'],
                'short_desc' => $data['short_desc'],
            ]
        );

        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Printing Content Save!'
        ]);
    }
}
