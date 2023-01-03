<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ChangePasswordController extends Controller
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
        $user = Auth::user();
        $props['user'] = $user;
        return Inertia::render('ChangePassword', $props);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);

        $response_arr = ['success' => false];

        $rules = [
            'old_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ];
        $messages = [
            'sponsor_user_name.exists' => 'This user does not exist.',
        ];

        $request->validate($rules, $messages);

        $user_data = array(
            'id'  => $user->id,
            'password' => $request->input('old_password')
        );

        $remember_me  = TRUE;

        if (Auth::attempt($user_data)) {
            
            $user->password = Hash::make($request->new_password);
            $user->remember_token =  Str::random(10);

            $user->save();
            Auth::login($user, $remember_me);
            $response_arr['success'] = true;
            $response_arr['redirect_url'] = RouteServiceProvider::HOME;
        }


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
