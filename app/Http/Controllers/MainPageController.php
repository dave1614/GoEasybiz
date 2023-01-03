<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainPageController extends Controller
{
    
    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }
    

    public function contactUsPage(Request $request)
    {
        return Inertia::render('ContactUs');
    }

    public function compensationPlanPage(Request $request){
        return Inertia::render('CompensationPlan');
    }

    public function mainPage(Request $request){
        return Inertia::render('MainPage');
    }

    public function aboutUs(Request $request){
        return Inertia::render('AboutUs');
    }

    public function ourServices(Request $request){
        return Inertia::render('Services');
    }

    
    public function faqPage(Request $request)
    {
        return Inertia::render('Faqs');
    }

    public function submitContactInfo(Request $request){
        $rules = [
            'name' => 'required|max:100',
            'phone' => 'required|max:15',
            'email' => 'required|max:30',
            'subject' => 'required|max:255',
            'message' => 'required|max:1000',
           
        ];

        $request->validate($rules);

        
        ContactInfo::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('data',['success' => true]);
    }
}

