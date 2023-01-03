<?php

namespace App\Http\Middleware;

use App\Functions\UsefulFunctions;
use App\Models\MlmWeekly;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProvidusMiddleware
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $sign_up_amt = $this->functions->getSignUpAmount();
        if (Auth::check()) {
            $user = Auth::user();
            $user = User::find($user->id);
            // $this->functions->generateMonnifyAcctsForUser($user->id);


            if (is_null($user->providus_account_number)) {
                $full_name = $user->name;
                $base_url = env('PROVIDUS_BASEURL');

                // $url = "https://vps.providusbank.com/vps/api/PiPCreateReservedAccountNumber";
                $url = $base_url . "/PiPCreateReservedAccountNumber";

                $post_data = [
                    "account_name" => $full_name,
                    "bvn" => ""
                ];

                $use_post = true;
                $response = $this->functions->providusVtuCurl($url, $use_post, $post_data);

                if ($this->functions->isJson($response)) {
                    $response = json_decode($response);
                    if ($response->responseCode == "00" && $response->requestSuccessful == true) {

                        $user->providus_account_number = $response->account_number;
                        $user->providus_account_name = $response->account_name;

                        $user->save();
                    }
                }
            }

            $registration_amt_paid = $user->registration_amt_paid;
            if ($registration_amt_paid > $sign_up_amt && $user->created == 1) {
                
                $amount = $registration_amt_paid - $sign_up_amt;
                $summary = "Account Credit Of " . $amount;
                
                $this->functions->creditUser($user->id, $amount, $summary);
                $user->registration_amt_paid = $sign_up_amt;
                $user->save();
                 
            }

            if($user->created == 1 && $user->is_admin != 1){
                $users_weekly = MlmWeekly::where('user_id', $user->id)->whereDate('week_start', '<=', Carbon::now()->subDays(7))
                ->first();
                // dd($users_weekly);
                if (!is_null($users_weekly)) {
                    
                    $this->functions->processUserWeeklyRout($user->id);
                    
                }
            }

            if($user->is_admin == 1){
                $role = "admin";
            }
        }
        return $next($request);
    }
}
