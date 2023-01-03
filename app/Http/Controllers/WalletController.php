<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use App\Models\Country;
use App\Models\TransferFundsHistory;
use App\Models\User;
use App\Rules\CountryRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WalletController extends Controller
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }
    

    public function showEarningToWalletPage(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $history = $user->earningToWalletHist();

        $history = $request->query('amount') ? $history->where('amount', 'like', '%' . $request->query('amount') . '%') : $history;

        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();

        $props['history'] = $history;
        $props['length'] = $length;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;
        
        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;


        // return $wallet_statement;


        return Inertia::render("Wallet/EarningToWalletHist", $props);
    }

    

    public function processValidateWithdrawalOtp(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);

        $response_arr = ['success' => false, 'not_bouyant' => true, 'too_small' => true, 'incorrect_otp' => true];

        $rules = [
            'amount' => ['required', 'numeric'],
            'passw' => ['required', 'confirmed']
        ];

        $request->validate($rules);

        $amount = $request->amount;
        
        $password = $request->passw;
        $user_data = [
            'id' => $user->id,
            'password' => $password
        ];
        if (Auth::attempt($user_data)) {
            $response_arr['incorrect_otp'] = false;
            $wallet_balance = $user->total_income - $user->withdrawn;
            if ($amount >= 200) {
                $response_arr['too_small'] = false;
                $amount_with_charge = $amount + 100;
                if ($amount_with_charge <= $wallet_balance) {
                    $response_arr['not_bouyant'] = false;

                    $date = date("j M Y");
                    $time = date("h:i:sa");

                    $user_bank_name = $user->bank_name;
                    $account_number = $user->account_number;

                    $banks_arr = $this->functions->paystackCurl("https://api.paystack.co/bank", FALSE);
                    $banks_arr = json_decode($banks_arr);

                    if ($banks_arr->status && $banks_arr->message == "Banks retrieved") {





                        $bank_names = $banks_arr->data;


                        foreach ($bank_names as $row) {
                            $bank_name = $row->name;
                            $code = $row->code;
                            $long_code = $row->longcode;
                            $active = $row->active;
                            $is_deleted = $row->is_deleted;



                            if ($code == $user_bank_name) {
                                // echo $bank_name;

                                $account_number_test = $this->functions->paystackCurl("https://api.paystack.co/bank/resolve?account_number=" . $account_number . "&bank_code=" . $user_bank_name, FALSE);
                                // echo $account_number_test;

                                $account_number_test = json_decode($account_number_test);
                                if (is_object($account_number_test)) {
                                    // echo "string";
                                    if ($account_number_test->status == "true") {


                                        $account_name = $account_number_test->data->account_name;



                                        $summary = "Withdrawal Of " . $amount;
                                        if ($this->functions->debitUser($user->id, $amount, $summary)) {
                                            $summary = "Withdrawal Charge";
                                            if ($this->functions->debitUser($user->id, 100, $summary)) {
                                                $form_array = array(
                                                    'user_id' => $user->id,
                                                    'amount' => $amount,
                                                    'bank_name' => $user_bank_name,
                                                    'real_bank_name' => $bank_name,
                                                    'account_number' => $account_number,
                                                    'account_name' => $account_name,
                                                    'date' => $date,
                                                    'time' => $time
                                                );
                                                if ($this->functions->sendWithrawalRequest($form_array)) {

                                                    $response_arr['success'] = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return back()->with('data', $response_arr);
    }

    public function processEnterAmountWithdrawFunds(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);

        $response_arr = ['success' => false, 'min_referrals' => 2,'not_referred_enough' => true,'referrals_num' => 0,'not_bouyant' => false, 'too_small' => false];

        if ($request->amount) {

            $amount = $request->amount;
            

            if($this->functions->checkIfUserHasEnoughReferralsFrWithdrawal($user->id,$response_arr['min_referrals'])){
                $response_arr['not_referred_enough'] = false;
                if (is_numeric($amount)) {
                    if ($amount >= 200) {
                        $available_income = $user->total_income - $user->withdrawn;
                        $amount = $amount + 100;
                        if ($amount <= $available_income) {
                            $response_arr['success'] = true;
                            $response_arr['phone_number'] = $user->phone;
                            $response_arr['code'] = $user->phone_code;
                        } else {
                            $response_arr['not_bouyant'] = true;
                        }
                    } else {
                        $response_arr['too_small'] = true;
                    }
                }
            }else{
                $response_arr['referrals_num'] = $this->functions->getTtalReferralNumsByUser($user->id);
            }
        }
        return back()->with('data', $response_arr);
    }

    public function processWithdrawFundsCont(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);

        $response_arr = ['success' => false, 'bank_details' => "", 'account_name' => '', 'invalid_account' => false];

        $rules = [
            'bank_name' => 'required|numeric',
            'account_number' => 'required|numeric',
        ];

        $request->validate($rules);

        $bank_name = $request->bank_name;
        $account_number = $request->account_number;
        
        $wallet_balance = $user->total_income - $user->withdrawn;

        $account_number_test = $this->functions->paystackCurl("https://api.paystack.co/bank/resolve?account_number=" . $account_number . "&bank_code=" . $bank_name, FALSE);

        $account_number_test = json_decode($account_number_test);
        // return $account_number_test;
        if (is_object($account_number_test)) {
            // echo "string";
            if ($account_number_test->status == "true") {
                $data = [
                    "type" =>  "nuban",
                    "name" => $user->name,
                    // "description" => "Payment For ".$health_facility_name,
                    "account_number" => $account_number,
                    "bank_code" => $bank_name,
                    "currency" => "NGN"
                ];

                $create_transfer_recipient = $this->functions->paystackCurl("https://api.paystack.co/transferrecipient", TRUE, $data);
                $create_transfer_recipient = json_decode($create_transfer_recipient);
                if (is_object($create_transfer_recipient)) {

                    if ($create_transfer_recipient->status == TRUE) {

                        $recepient_code = $create_transfer_recipient->data->recipient_code;

                        $user->bank_name = $bank_name;
                        $user->account_number = $account_number;
                        $user->recepient_code = $recepient_code;
                        $user->save();
                        
                        $response_arr['account_name'] = $account_number_test->data->account_name;
                        $response_arr['success'] = true;
                        
                    }
                }
            } else {
                $response_arr['invalid_account'] = true;
            }
        }

        return back()->with('data', $response_arr);
    }

    public function loadFundsWithdrawalPage(Request $request)
    { 
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;
        $props['countries'] = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();

        $banks_arr = $this->functions->paystackCurl("https://api.paystack.co/bank", FALSE);

        $banks_arr = json_decode($banks_arr);
        $props['banks_arr'] = $banks_arr;


        return Inertia::render("Wallet/FundsWithdrawal", $props);
    }

    public function processVerifyTransferOtp(Request $request)
    {
        $user = Auth::user();

        $response_arr = ['success' => false, 'not_bouyant' => true, 'recepient_does_not_exist' => true, 'recepient_is_self' => true, 'incorrect_password' => true];

        $rules = [
            'amount' => ['required', 'numeric'],
            'country' => ['required', new CountryRule],
            'phone' => ['required', 'numeric'],
            'passw' => ['required', 'confirmed']
        ];

        $request->validate($rules);

        $amount = $request->amount;
        $country_id = $request->country;
        $phone = $request->phone;
        $password = $request->passw;
        $wallet_balance = $user->total_income - $user->withdrawn;
        if ($wallet_balance >= $amount) {
            $response_arr['not_bouyant'] = false;


            $recepient_user = User::where('country_id', $country_id)->where('phone', $phone)->first();
            if (!is_null($recepient_user)) {
                $response_arr['recepient_does_not_exist'] = false;

                if ($user->id != $recepient_user->id) {
                    $response_arr['recepient_is_self'] = false;

                    $user_data = [
                        'id' => $user->id,
                        'password' => $password
                    ];
                    if(Auth::attempt($user_data)){
                        $response_arr['incorrect_password'] = false;

                        $date = date("j M Y");
                        $time = date("h:i:sa");
                        if ($this->functions->transferFundsToUser($user->id, $recepient_user->id, $amount, $date, $time)) {
                            
                            $response_arr['success'] = true;
                        }
                    }
                }
            }
        }

        return back()->with('data', $response_arr);
    }

    public function processTransferFundsToUser(Request $request)
    {
        $user = Auth::user();
        
        $response_arr = ['success' => false, 'user_details' => [], 'not_bouyant' => true, 'recepient_does_not_exist' => true, 'recepient_is_self' => true];

        $rules = [
            'amount' => ['required', 'numeric'],
            'country' => ['required', new CountryRule],
            'phone' => ['required', 'numeric'],
        ];

        $request->validate($rules);

        $amount = $request->amount;
        $country_id = $request->country;
        $phone = $request->phone;
        $wallet_balance = $user->total_income - $user->withdrawn;
        if($wallet_balance >= $amount){
            $response_arr['not_bouyant'] = false;


            $recepient_user = User::where('country_id', $country_id)->where('phone', $phone)->first();
            if(!is_null($recepient_user)){
                $response_arr['recepient_does_not_exist'] = false;

                if($user->id != $recepient_user->id){
                    $response_arr['recepient_is_self'] = false;

                    $response_arr['success'] = true;
                    $response_arr['user_details'] = $recepient_user;
                }
            }
        }

        return back()->with('data',$response_arr);
    }

    public function loadFundsTransferPage(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;
        $props['countries'] = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();


        return Inertia::render("Wallet/FundsTransfer", $props);
    }


    public function loadCreditUserWalletPage(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

      

        return Inertia::render("Wallet/CreditWallet", $props);
    }

    public function showTransferHistoryPage(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

        $role_prop = $request->query("role");
        $role_prop = empty($role_prop) ? 'all' : $role_prop;


        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $user_id = $user->id;
        $history = TransferFundsHistory::where(function ($query) use ($user_id, $role_prop) {
            
            if($role_prop == "all"){
                $query->where('sender', $user_id)
                    ->orWhere('recepient', $user_id);
            }else if($role_prop == "sender"){
                $query->where('sender', $user_id);
            }else if ($role_prop == "recepient") {
                $query->where('recepient', $user_id);
            }
        });

        $history = $history->join('users as sender', 'transfer_funds_history.sender', '=', 'sender.id')->addSelect('sender.name as sender_name')->addSelect('sender.phone as sender_phone')->addSelect('sender.country_id as sender_country_id')->addSelect('sender.email as sender_email');

        $history = $history->join('countries as sender_country', 'sender.country_id', '=', 'sender_country.id')->addSelect('sender_country.name as sender_country_name');

        $history = $history->join('users as recepient', 'transfer_funds_history.recepient', '=', 'recepient.id')->addSelect('recepient.name as recepient_name')->addSelect('recepient.phone as recepient_phone')->addSelect('recepient.country_id as recepient_country_id')->addSelect('recepient.email as recepient_email');

        $history = $history->join('countries as recepient_country', 'recepient.country_id', '=', 'recepient_country.id')->addSelect('recepient_country.name as recepient_country_name');
        // $history = $history->join('users', 'transfer_funds_history.sender', '=', 'users.id');

        $history = $history->addSelect('transfer_funds_history.*');


        $history = $request->query('sender_name') ? $history->where('sender.name', 'like', '%' . $request->query('sender_name') . '%') : $history;
        $history = $request->query('sender_phone') ? $history->where('sender.phone', 'like', '%' . $request->query('sender_phone') . '%') : $history;
        $history = $request->query('sender_email') ? $history->where('sender.email', 'like', '%' . $request->query('sender_email') . '%') : $history;
        $history = $request->query('sender_country_name') ? $history->where('sender_country.name', 'like', '%' . $request->query('sender_country_name') . '%') : $history;

        $history = $request->query('recepient_name') ? $history->where('recepient.name', 'like', '%' . $request->query('recepient_name') . '%') : $history;
        $history = $request->query('recepient_phone') ? $history->where('recepient.phone', 'like', '%' . $request->query('recepient_phone') . '%') : $history;
        $history = $request->query('recepient_email') ? $history->where('recepient.email', 'like', '%' . $request->query('recepient_email') . '%') : $history;
        $history = $request->query('recepient_country_name') ? $history->where('recepient_country.name', 'like', '%' . $request->query('recepient_country_name') . '%') : $history;

        

        $history = $request->query('amount') ? $history->where('transfer_funds_history.amount', 'like', '%' . $request->query('amount') . '%') : $history;

        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('transfer_funds_history.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('transfer_funds_history.created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();



        $props['history'] = $history;
        $props['length'] = $length;
        $props['role'] = $role_prop;
        $props['sender_name'] = $request->query('sender_name') ? $request->query('sender_name') : NULL;
        $props['sender_phone'] = $request->query('sender_phone') ? $request->query('sender_phone') : NULL;
        $props['sender_email'] = $request->query('sender_email') ? $request->query('sender_email') : NULL;
        $props['recepient_country_name'] = $request->query('recepient_country_name') ? $request->query('recepient_country_name') : NULL;

        $props['recepient_name'] = $request->query('recepient_name') ? $request->query('recepient_name') : NULL;
        $props['recepient_phone'] = $request->query('recepient_phone') ? $request->query('recepient_phone') : NULL;
        $props['recepient_email'] = $request->query('recepient_email') ? $request->query('recepient_email') : NULL;
        $props['recepient_country_name'] = $request->query('recepient_country_name') ? $request->query('recepient_country_name') : NULL;

        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;


        // return $history;


        return Inertia::render("Wallet/TransferHistory", $props);
    }

    public function showWithdrawalHistoryPage(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;

        $history = $user->withdrawalHistory();



        $history = $request->query('amount') ? $history->where('amount', 'like', '%' . $request->query('amount') . '%') : $history;

        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
            ->paginate($length)->withQueryString();




        // return $history;


        $props['history'] = $history;
        $props['length'] = $length;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Wallet/WithdrawalHistory", $props);
    }

    public function showWalletCreditHistoryPage(Request $request){
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;

        $history = $user->accountCreditHistory();



        $history = $request->query('amount') ? $history->where('amount', 'like', '%' . $request->query('amount') . '%') : $history;
        $history = $request->query('payment_option') ? $history->where('payment_option', 'like', '%' . $request->query('payment_option') . '%') : $history;
        $history = $request->query('reference') ? $history->where('reference', 'like', '%' . $request->query('reference') . '%') : $history;

        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();




        // return $history;


        $props['history'] = $history;
        $props['length'] = $length;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['payment_option'] = $request->query('payment_option') ? $request->query('payment_option') : NULL;
        $props['reference'] = $request->query('reference') ? $request->query('reference') : NULL;
        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Wallet/WalletCreditHistory", $props);
    }

    public function showWalletStatementPage(Request $request){
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;
        

        $j = 0;
        $wallet_statement = $user->accountStatement();

        $wallet_statement = $request->query('amount') ? $wallet_statement->where('amount', 'like', '%' . $request->query('amount') . '%') : $wallet_statement;
        $wallet_statement = $request->query('balance') ? $wallet_statement->where('amount_after', 'like', '%' . $request->query('balance') . '%') : $wallet_statement;
        $wallet_statement = $request->query('summary') ? $wallet_statement->where('summary', 'like', '%' . $request->query('summary') . '%') : $wallet_statement;
        $wallet_statement = !empty($request->query('date')) && $request->query('date') != "" ? $wallet_statement->where('date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $wallet_statement;

        $wallet_statement = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $wallet_statement->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')]) : $wallet_statement;

        

        $wallet_statement = $wallet_statement->orderBy("id","DESC")
                    ->paginate($length)->withQueryString();
        
        $props['wallet_statement'] = $wallet_statement;
        $props['length'] = $length;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;
        $props['balance'] = $request->query('balance') ? $request->query('balance') : NULL;
        $props['summary'] = $request->query('summary') ? $request->query('summary') : NULL;
        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;
        
        
        // return $wallet_statement;
        

        return Inertia::render("Wallet/WalletStatement",$props);
    }
}
