<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\User;
use App\Rules\CountryRule;
use App\Rules\PhoneEditProfRule;
use App\Rules\PhoneRegistrRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {   
        $props = [];
        $real_countries = [];
        $user = Auth::user();
        $countries = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();

        $i = -1;
        foreach($countries as $country){
            $i++;
            $id = $country->id;
            $name = $country->name;
            $phone_code = $country->phone_code;

            $real_countries[] = [
                'id' => $id,
                'label' => $name . ' (' . $phone_code . ')',
            ];

            // $props['selected_country'] = !isset($props['selected_country']) && $id == $user->country_id = $i;
            if($id == $user->country_id){
                $props['selected_country'] = $i;
            }
        }
        // dd($user->country_id);
        // dd($props['selected_country']);
        // return $real_countries;
        $props['user'] = $user;

        $props['countries'] = $real_countries;
        // return $user;
        return Inertia::render('EditProfile',$props);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        // return $request->country['id'];

        $response_arr = ['success' => false,'email_changed' => false];

        $rules = [
            'email' => 'required|string|email|max:255',
            'country' => ['required', new CountryRule],
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', 'min_digits:7', 'max_digits:15', new PhoneEditProfRule($request->country['id'],$user->id)],
            'address' => 'required|string|max:1000',

        ];

        $messages = [
            'sponsor_user_name.exists' => 'This user does not exist.',
        ];

        $request->validate($rules, $messages);

        $user->country_id = $request->country['id'];
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if($user->email != $request->email){
            $response_arr['email_changed'] = true;
            $user->newEmail($request->email);
        }

        $user->save();

        $response_arr['success'] = true;

        return back()->with('data', $response_arr);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
