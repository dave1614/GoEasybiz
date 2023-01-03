<?php

namespace App\Http\Controllers\Auth;

use App\Functions\UsefulFunctions;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Facility;
use App\Models\FacilityStructure;
use App\Models\Officer;
use App\Models\Patient;
use App\Models\Region;
use App\Models\SubDept;
use App\Models\Test;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\CountryRule;
use App\Rules\PhoneRegistrRule;
use App\Rules\RegionRule;
use App\Rules\SponsorPhoneRegistrRule;
use Database\Seeders\TestSeeder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    public function processRegiPageTwo(Request $request){
        $response_arr = ['success' => false];

        $rules = [
            'email' => 'required|string|email|max:255',
            'country' => ['required', new CountryRule],
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', 'min_digits:7', 'max_digits:15', new PhoneRegistrRule($request->country)],
            
        ];

        $messages = [
            'sponsor_user_name.exists' => 'This user does not exist.',
        ];

        $request->validate($rules, $messages);
            
        $response_arr['success'] = true;

        return back()->with('data', $response_arr);
    }
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        $props = [];

        
        $props['year'] = date('Y');
        $props['countries'] = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();
        $props['regions'] = Region::where('country_id', 1)->orderBy("name", "ASC")->get();
        $props['prop_country'] = $request->has('c') ? $request->query('c') : 151;
        $props['prop_phone'] = $request->query('p');

        return Inertia::render('Auth/Register',$props);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        
        $response_arr = ['success' => false,'terms_unaccepted' => true];
        
            

        if(isset($request->terms) && $request->terms){
            $response_arr['terms_unaccepted'] = false;
            $rules = [
                
                'sponsor_country' => ['required', new CountryRule],
                'sponsor_phone_number' => ['required', 'numeric', new SponsorPhoneRegistrRule($request->sponsor_country)],
                'email' => 'required|string|email|max:255',
                'country' => ['required', new CountryRule],
                // 'region' => ['required', new RegionRule($request->country)],
                'name' => 'required|string|max:255',
                // 'phone' => 'required|numeric|min_digits:7|max_digits:15|unique:users,phone',
                'phone' => ['required', 'numeric', 'min_digits:7', 'max_digits:15', new PhoneRegistrRule($request->country)],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ];

            $messages = [
                'sponsor_user_name.exists' => 'This user does not exist.',
            ];

            $request->validate($rules, $messages);

            $sponsor = User::where('country_id', $request->sponsor_country)->where('phone', $request->sponsor_phone_number)->first();

            $user = User::create([
                'sponsor_user_id' => $sponsor->id,
                'name' => $request->name,
                'email' => $request->email,
                'country_id' => $request->country,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(10),
            ]);


            $user_id = $user->id;

            $now = DB::raw('NOW()');
            $user->last_activity = $now;
            $user->save();





            event(new Registered($user));

            Auth::login($user);

            return redirect(RouteServiceProvider::HOME);
        

        }
        
        return back()->with('data', $response_arr);
    }

    public function getSponsorInfoRegistration(Request $request)
    {

        if ($request->country && $request->sponsor_phone_number) {
            $response_arr = array('success' => false, 'sponsor' => []);

            $user = User::where('country_id', $request->country)->where('phone', $request->sponsor_phone_number)->first();
            
            if (!is_null($user)) {
                $response_arr['success'] = true;
                $response_arr['sponsor'] = $user;
            }
            return back()->with('data', $response_arr);
        }
    }

    public function loadRegionsForCountry(Request $request)
    {
        $request = (object) $request->input();
        $response_arr = array();
        // return $request;
        // return Country::where("id", $request->country)->count();
        // $request->country = 1;
        if (!isset($request->country) || Country::where("id", $request->country)->count() < 1) {
            return back()->with('error', 'something went wrong');
        }

        $regions = Region::where('country_id', $request->country)->orderBy("name", "ASC")->get();
        $first_region_id = Region::where('country_id', $request->country)->orderBy("name", "ASC")->first()->id;

        // $first_region_id = Country::regions()->orderBy("name", "ASC")->first()->id;
        $response_arr['regions'] = $regions;
        $response_arr['first_region_id'] = $first_region_id;

        return back()->with('data', $response_arr);
    }
}
