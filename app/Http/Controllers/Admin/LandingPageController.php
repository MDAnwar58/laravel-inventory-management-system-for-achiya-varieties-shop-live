<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use App\Models\Feature;
use App\Models\LandingPage;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['name' => 'Landing Page', 'route' => null, 'icon' => null]
        ];

        $contact_infos = ContactInfo::latest()->get();
        $features = Feature::latest()->get();
        $landing_page = LandingPage::first();

        return view('pages.admin.landing-page.base', compact('breadcrumbs', 'contact_infos', 'features', 'landing_page'));
    }
    public function store_or_update(Request $request)
    {
        $id = $request->id;
        $landing_page = LandingPage::findOrFail(intval($id));
        if ($landing_page) {
            $landing_page->update($request->all());
            $status = 'Updated';
        } else {
            LandingPage::create($request->all());
            $status = 'Created';
        }
        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Landing Page ' . $status . '!'
        ]);
    }



    // contact details store, edit, update, delete.
    public function contact_info_store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'content' => 'required',
        ]);
        ContactInfo::create($request->all());

        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Contact information Added!'
        ]);
    }
    public function contact_info_edit($id)
    {
        return ContactInfo::findOrFail(intval($id));
    }
    public function contact_info_update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'content' => 'required',
        ]);
        $contact_info = ContactInfo::findOrFail(intval($id));
        $contact_info->update($request->all());
        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Contact information Updated!'
        ]);
    }
    public function contact_info_delete($id)
    {
        $contact_info = ContactInfo::findOrFail(intval($id));
        $contact_info->delete();
        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Contact Information Deleted!'
        ]);
    }


    // feature details store, edit, update, delete.
    public function feature_info_store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'content' => 'required',
        ]);
        Feature::create($request->all());

        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Feature Added!'
        ]);
    }
    public function feature_info_edit($id)
    {
        return Feature::findOrFail(intval($id));
    }
    public function feature_info_update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'content' => 'required',
        ]);
        $feature = Feature::findOrFail(intval($id));
        $feature->update($request->all());
        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Feature Updated!'
        ]);
    }
    public function feature_info_delete($id)
    {
        $feature = Feature::findOrFail(intval($id));
        $feature->delete();
        return redirect()->back()->with([
            'status' => 'success',
            'msg' => 'Feature Deleted!'
        ]);
    }
}
