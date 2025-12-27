<?php

namespace App\Http\Controllers\Userend;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use App\Models\Customer;
use App\Models\Feature;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function welcome()
    {
        $landing_page = LandingPage::first();
        $features = Feature::latest()->take(6)->get();
        $contactInfos = ContactInfo::latest()->get();
        $custmers_count = Customer::count();

        return view('welcome', compact('landing_page', 'contactInfos', 'features', 'custmers_count'));
    }
}
