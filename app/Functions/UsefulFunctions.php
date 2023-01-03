<?php
namespace App\Functions;

use App\Http\Controllers\VtuController;
use App\Models\AccountCreditHistory;
use App\Models\AccountStatement;
use App\Models\AdminDebitUsersHistory;
use App\Models\AdminFundUsersHistory;
use App\Models\ComboRechargeVtu;
use App\Models\MlmCharge;
use App\Models\MlmDb;
use App\Models\MlmEarning;
use App\Models\MlmWeekly;
use App\Models\Notif;
use App\Models\TransferFundsHistory;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Models\WithdrawalHistory;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;


use App\Mail\NotifyMail;
use App\Models\AirtimeToWalletRecord;
use App\Models\Country;
use App\Models\EarningToWalletHistory;
use App\Models\EasyLoan;
use App\Models\MonnifyPaymentDetail;
use App\Models\MonnifySubToken;
use App\Models\UserMonnifyDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UsefulFunctions {

    public $CLUB_USERID;
    public $CLUB_APIKEY;
    public function __construct()
    {
        $this->CLUB_USERID = env("CLUB_USERID");
        $this->CLUB_APIKEY = env("CLUB_APIKEY");
    }

    public function getAuthUser(){
        return Auth::user();
    }

    public function getPendingLoansNumForUser($user_id){
        $loans = EasyLoan::where('user_id', $user_id)->where('paid_off',0)->get();
        $num = $loans->count() > 0 ? $loans->count() : 0;

        return $num;
    }

    public function checkIfUserEarnedInAtLeast7DaysIsUpTo1000($user_id){
        $earning = EarningToWalletHistory::where('user_id', $user_id)->whereDate('created_at', '>=', Carbon::now()->subDays(7))
        ->first();
        if(!is_null($earning)){
            if($earning->amount >= 1000){
                return true;
            }
        }

        return false;
    }

    public function checkIfUserEarnedInAtLeast7Days($user_id){
        $earning = EarningToWalletHistory::where('user_id', $user_id)->whereDate('created_at', '>=', Carbon::now()->subDays(7))
        ->first();
        return !is_null($earning) ? true : false;
    }

    public function checkIfMonnifyTransactionIsValid($transactionReference){
        $response = $this->getMoonnifyTransactionStatus($transactionReference);
        return $response;
        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if (isset($response->requestSuccessful) && $response->requestSuccessful && isset($response->responseBody)) {
                    $body = $response->responseBody;

                    if($body->paymentStatus == "PAID"){
                        return true;
                    }
                }
            }
        }
        return true;
    }

    public function checkIfThisMonnifyWebhookIsDuplicate($transactionReference){
        $query = MonnifyPaymentDetail::where('transaction_reference', $transactionReference)->get('id');
        return ($query->count() > 0) ? true : false;
    }

    public function checkIfThisMonnifyAccountReferenceIsValid($accountReference){
        $query = UserMonnifyDetail::where('account_reference', $accountReference)->get('id');
        return ($query->count() > 0) ? true : false;
    }

    public function getMoonnifyTransactionStatus($transactionReference)
    {

        $base_url = env("MONNIFY_BASEURL");
        $endpoint = "/api/v2/transactions/". $transactionReference;
        $url = $base_url . "" . $endpoint;

        $use_post = false;

        
        $post_data = [
            
        ];

        $response = $this->monnifyCurl($url, $use_post, $post_data);
        return $response;
        
    }

    public function generateNewMonnifyAcctReferenceForUser(){
        $reference = Str::random(15);

        $query = UserMonnifyDetail::where('account_reference',$reference)->get('id');
        return ($query->count() > 0) ? $this->generateNewMonnifyAcctReferenceForUser() : $reference;
    }

    public function generateMonnifyAcctsForUser($user_id){
        
        
        $user = User::find($user_id);
        $details = $user->monnifyDetails()->get('id');
        if($details->count() == 0){
            $base_url = env("MONNIFY_BASEURL");
            $endpoint = "/api/v2/bank-transfer/reserved-accounts";
            $url = $base_url . "" . $endpoint;

            $use_post = true;

            $accountReference = $this->generateNewMonnifyAcctReferenceForUser();
            $contractCode = env("MONNIFY_CONTRACTCODE");
            $accountName = "DE-MEET(" . $user->name . ")";
            $post_data = [
                "accountReference" => $accountReference,
                "accountName" => $accountName,
                "currencyCode" => "NGN",
                "contractCode" => $contractCode,
                "customerEmail" => $user->email,
                "customerName" => $user->name,
                "getAllAvailableBanks" => true,
            ];

            $response = $this->monnifyCurl($url, $use_post, $post_data);
            // return $response;
            if ($this->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    if (isset($response->requestSuccessful) && $response->requestSuccessful && isset($response->responseBody)) {
                        $body = $response->responseBody;

                        $wema_account_name = null;
                        $wema_account_number = null;
                        $sterling_account_name = null;
                        $sterling_account_number = null;
                        $fidelity_account_name = null;
                        $fidelity_account_number = null;
                        $moniepoint_account_name = null;
                        $moniepoint_account_number = null;

                        if(isset($body->accounts)){
                            if(is_array($body->accounts) && count($body->accounts) > 0){
                                $accounts = $body->accounts;
                                for($i = 0; $i < count($accounts); $i++){
                                    $bankCode = $accounts[$i]->bankCode;
                                    $bankName = $accounts[$i]->bankName;
                                    $accountNumber = $accounts[$i]->accountNumber;
                                    $accountName = $accounts[$i]->accountName;

                                    
                                    $wema_account_name = ($bankCode == "035") ? $accountName : $wema_account_name;
                                    $wema_account_number = ($bankCode == "035") ? $accountNumber : $wema_account_number;

                                    $sterling_account_name = ($bankCode == "232") ? $accountName : $sterling_account_name;
                                    $sterling_account_number = ($bankCode == "232") ? $accountNumber : $sterling_account_number;

                                    $moniepoint_account_name = ($bankCode == "50515") ? $accountName : $moniepoint_account_name;
                                    $moniepoint_account_number = ($bankCode == "50515") ? $accountNumber : $moniepoint_account_number;
                                }
                            }
                        }

                        UserMonnifyDetail::create([
                            "user_id" => $user->id,
                            "account_reference" => $body->accountReference,
                            "acoount_name" => $body->accountName,
                            "currency_code" => $body->currencyCode,
                            "customer_email" => $body->customerEmail,
                            "customer_name" => $body->customerName,
                            "reservation_reference" => $body->reservationReference,
                            "reserved_account_type" => $body->reservedAccountType,
                            "wema_account_name" => $wema_account_name,
                            "wema_account_number" => $wema_account_number,
                            "sterling_account_name" => $sterling_account_name,
                            "sterling_account_number" => $sterling_account_number,
                            "fidelity_account_name" => $fidelity_account_name,
                            "fidelity_account_number" => $fidelity_account_number,
                            "moniepoint_account_name" => $moniepoint_account_name,
                            "moniepoint_account_number" => $moniepoint_account_number,
                        ]);
                    }
                }
            }
        }
    }

    public function getFreshMonnifyAccessToken()
    {
        $base_url = env("MONNIFY_BASEURL");
        $api_key = env("MONNIFY_TESTAPIKEY");
        $secret_key = env("MONNIFY_TESTSECRETKEY");
        $url = $base_url . "/api/v1/auth/login";

        $token = base64_encode($api_key . ":" . $secret_key);

        $headers = [
            'Authorization' => 'Basic '. $token,
            'Accept' => ' application/json',
            'Content-Type' => 'application/json'
        ];

        $post_data = [

        ];


        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders($headers)->post($url, $post_data);


        return $response;
    }

    public function getMonnifyAccessToken()
    {

        

        $bearer_token = "";
        $subToken = MonnifySubToken::find(1);
        if (!is_null($subToken)) {
            
            $token = $subToken->bearer_token;
            $expires_in = date("Y-m-d H:i:s", strtotime($subToken->expires_in));
            

            $date_time = date("Y-m-d H:i:s");
            

            $curr_date = strtotime($date_time);

            $expires_in = strtotime($expires_in);

            

            $date_diff = $expires_in - $curr_date;
            // return $date_diff;
            if ($date_diff <= 0) {
                $token_details = json_decode($this->getFreshMonnifyAccessToken());
                // return $token_details;
                if (isset($token_details->responseBody->accessToken) && isset($token_details->responseBody->expiresIn)) {
                    $bearer_token = $token_details->responseBody->accessToken;
                    $expires_in = $token_details->responseBody->expiresIn;

                    $date_time = date("Y-m-d H:i:s");

                    $dateinsec = strtotime($date_time);
                    $newdate = $dateinsec + $expires_in;
                    $expires_in = date("Y-m-d H:i:s", $newdate);


                    $subToken->bearer_token = $bearer_token;
                    $subToken->expires_in = $expires_in;

                    $subToken->save();

                }
            } else {
                $bearer_token = $token;
            }
        } else  {
            $token_details = json_decode($this->getFreshMonnifyAccessToken());
            if (isset($token_details->responseBody->accessToken) && isset($token_details->responseBody->expiresIn)) {
                $bearer_token = $token_details->responseBody->accessToken;
                $expires_in = $token_details->responseBody->expiresIn;

                $date_time = date("Y-m-d H:i:s");

                $dateinsec = strtotime($date_time);
                $newdate = $dateinsec + $expires_in;
                $expires_in = date("Y-m-d H:i:s", $newdate);

               
                MonnifySubToken::create([
                    'bearer_token' => $bearer_token,
                    'expires_in' => $expires_in
                ]);
            }
        }

        return $bearer_token;
    }

    public function monnifyCurl($url, $use_post, $post_data = [])
    {
        $token = $this->getMonnifyAccessToken();
        $headers = [
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
            
        ];
        if (!$use_post) {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        } else {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }

    public function getTtalReferralNumsByUser($user_id){
        $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
        return MlmDb::where('sponsor', $mlm_db_id)->get()->count();
        
    }

    public function checkIfUserHasEnoughReferralsFrWithdrawal($user_id, $min_referrals){
        $mlm_db_id = $this->getUsersFirstMlmDbId($user_id);
        $query = MlmDb::where('sponsor', $mlm_db_id)->get();
        if($query->count() >= $min_referrals){
            return true;
        }else{
            return false;
        }
    }

    public function getTeamTotalOfUsersUnderMlmdbId($mlm_db_id){
        $ret_arr = [
            'left' => 0,
            'right' => 0,
        ];

        $unders = MlmDb::where('under', $mlm_db_id)->select('id', 'positioning')->get();
        if($unders->count() > 0){
            foreach($unders as $row){
                $id = $row->id;
                $positioning = $row->positioning;

                if($positioning == "left"){
                    $ret_arr['left'] = count($this->getDownlineArr1($id)) + 1;
                }else if($positioning == "right"){
                    $ret_arr['right'] = count($this->getDownlineArr1($id)) + 1;
                }
            }
        }

        return $ret_arr;
    }

    public function getNetworkForNumber($phone_code,$phone){
        $operator_id = $this->getReloadlyOperatorId($phone_code, $phone, 151);
                
        if($operator_id == "344"){
            return "glo";
        } else if ($operator_id == "341") {
            return "mtn";
        } else if ($operator_id == "342") {
            return "airtel";
        } else if ($operator_id == "340") {
            return "9mobile";
        }
                
    }

    public function getReloadlyOperatorId($phone_code, $phone,$country_id){
        $int_num = $this->returnInterNumber($phone_code, $phone);
        $country_code = Country::find($country_id)->code;
        $url = "https://topups.reloadly.com/operators/auto-detect/phone/". $int_num."/countries/". $country_code;
        $use_post = false;
        $accept = 'application/com.reloadly.topups-v1+json';

        $response = $this->reloadlyCurl($url, $use_post, $post_data = [], $accept);
        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if (isset($response->operatorId)) {
                    return $response->operatorId;
                }
            }
        }
    }

    public function reloadlyCurl($url, $use_post, $post_data = [],$accept = null)
    {
        $token = $this->getReloadlyAccessToken();
        $headers = [
            'Authorization' => 'Bearer '. $token,
            'Accept' => is_null($accept) ? 'application/json' : $accept,
            'Content-Type' => 'application/json'
            
        ];
        if (!$use_post) {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        } else {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }


    public function getReloadlyAccessToken()
    {
        
        $table_name = 'reloadly_sub_token';
    
        $bearer_token = "";
        $query = DB::table($table_name)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $token = $row->bearer_token;
                $expires_in = date("Y-m-d", strtotime($row->expires_in));
            }

            $date_time = date("Y-m-d");

            $curr_date = strtotime($date_time);

            $expires_in = strtotime($expires_in);

            $date_diff = ($expires_in - $curr_date) / 86400;
            if ($date_diff <= 0) {
                $token_details = json_decode($this->getFreshReloadlyAccessToken());
                if (isset($token_details->access_token) && isset($token_details->expires_in)) {
                    $bearer_token = $token_details->access_token;
                    $expires_in = $token_details->expires_in;

                    $date_time = date("Y-m-d H:i:s");

                    $dateinsec = strtotime($date_time);
                    $newdate = $dateinsec + $expires_in;
                    $expires_in = date("Y-m-d H:i:s", $newdate);

                    $form_array = array(
                        'bearer_token' => $bearer_token,
                        'expires_in' => $expires_in
                    );
                    DB::table($table_name)->where('id', 1)->update($form_array);
                }
            } else {
                $bearer_token = $token;
            }
        } else if ($query->count() == 0) {
            $token_details = json_decode($this->getFreshReloadlyAccessToken());
            if (isset($token_details->access_token) && isset($token_details->expires_in)) {
                $bearer_token = $token_details->access_token;
                $expires_in = $token_details->expires_in;

                $date_time = date("Y-m-d H:i:s");

                $dateinsec = strtotime($date_time);
                $newdate = $dateinsec + $expires_in;
                $expires_in = date("Y-m-d H:i:s", $newdate);

                $form_array = array(
                    'bearer_token' => $bearer_token,
                    'expires_in' => $expires_in
                );
                DB::table($table_name)->insert($form_array);
            }
        }

        return $bearer_token;
    }

    public function getFreshReloadlyAccessToken(){
        $url = "https://auth.reloadly.com/oauth/token";

        $headers = [
            'Accept' => ' application/json',
            'Content-Type' => 'application/json'
        ];

        $post_data = [
            
            "audience" => "https://topups.reloadly.com",
            "client_id" => "VQRb2qzyI88cuydajOVPgpCPCIrNtflR",
            "client_secret" => "roXBST25t7-g74kzuLR1N4IOvjvxhk-SJyMevNExofbpbyuEy03sbVfbpp0voT1",
            "grant_type" => "client_credentials",
  

        ];
         
           
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders($headers)->post($url, $post_data);
            
        
        return $response;
    }



    public function returnInterNumber($phone_code, $phone){
        if (substr($phone, 0, 1) == 0) {
            $phone = substr($phone, 1);
        }
        $int_num = substr($phone_code, 1) . '' . $phone;
        return $int_num;
    }

    // public function getNetworkForNumber($phone_code,$phone){
    //     $int_num = $this->returnInterNumber($phone_code, $phone);

    //     $url = "https://api.ng.termii.com/api/check/dnd?api_key=TLgEpxwrG9VSmzavnztRMku1NWfz4mDbtzNRh46ovFYdTcMxQS6pR6KWkSv3y1&phone_number=". $int_num;
    //     $use_post = false;

    //     $response = $this->curl($url, $use_post, $post_data = []);
    //     // return $response;

    //     if ($this->isJson($response)) {
    //         $response = json_decode($response);
    //         if (is_object($response)) {
    //             if(isset($response->network)){
    //                 if($response->network == "62150"){
    //                     return "glo";
    //                 } else if ($response->network == "62130") {
    //                     return "mtn";
    //                 } else if ($response->network == "62120") {
    //                     return "airtel";
    //                 } else if ($response->network == "62160") {
    //                     return "9mobile";
    //                 }
    //             }
    //         }
    //     }
    // }

    public function sendAirtime($form_array){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = User::find($form_array['user_id']);
        $myRequest = new \Illuminate\Http\Request();
        $myRequest->setMethod('POST'); //default METHOD

        if($user->country_id == 151){
            $network = $this->getNetworkForNumber($form_array['phone_code'], $form_array['phone']);
            $network = str_replace(' ', '', $network);
            // dd($network);

        

            if ($network == "mtn") {
                $mobilenetwork_code = "01";
                $eminence_ntwrk = "MTN";
                $serviceID = "mtn";
            } else if ($network == "glo") {
                $mobilenetwork_code = "02";
                $eminence_ntwrk = "GLO";
                $serviceID = "glo";
            } else if ($network == "9mobile") {
                $mobilenetwork_code = "03";
                $eminence_ntwrk = "ETISALAT";
                $serviceID = "etisalat";
            } else if ($network == "airtel") {
                $mobilenetwork_code = "04";
                $eminence_ntwrk = "AIRTEL";
                $serviceID = "airtel";
            }

            $vtu_platform = $this->getVtuPlatformToUse('airtime', $network);

            $vtu_platform_shrt = substr($vtu_platform, 0, 8);
            // dd($vtu_platform_shrt);
            if ($vtu_platform == "gsubz") {
                $api = $this->getGsubzApiKey();
                // return $type;
                $post_data = [
                    "phone" => $form_array['phone'],
                    "amount" => $form_array['amount'],
                    "serviceID" => $serviceID,
                    "api" => $api,
                ];
                $url = "https://gsubz.com/api/pay/";


                $response = $this->gSubzVtuCurl($url, true, $post_data);
                // return $response;
                if ($this->isJson($response)) {
                    $response = json_decode($response);
                    if (is_object($response)) {
                        $code = $response->code;
                        $status = $response->status;

                        if ($code == 200 && $status == "TRANSACTION_SUCCESSFUL") {
                            $order_id = "GS" . $response->content->transactionID;
                            $arrrr = array(
                                'user_id' => $form_array['user_id'],
                                'type' => 'airtime',
                                'sub_type' => $network,
                                'number' => $form_array['phone'],
                                'date' => $date,
                                'time' => $time,
                                'amount' => $form_array['amount'],
                                'order_id' => $order_id
                            );
                            $this->addTransactionStatus($arrrr);
                        }
                    }
                }
            }else{
                if ($vtu_platform_shrt != "eminence") {
                    
                    $url = "https://www.nellobytesystems.com/APIAirtimeV1.asp?UserID=".$this->CLUB_USERID."&APIKey=". $this->CLUB_APIKEY."&MobileNetwork=" . $mobilenetwork_code . "&Amount=" . $form_array['amount'] . "&MobileNumber=" . $form_array['phone'];
                    $response = $this->vtu_curl($url, false, $post_data = []);
                    // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '5424425'));
                    // return $response;
                    if ($this->isJson($response)) {
                        $response = json_decode($response);
                        if (is_object($response)) {
                            $status = $response->status;

                            if ($status == "ORDER_RECEIVED") {
                                
                                $order_id = $response->orderid;
                                $arrrr = array(
                                    'user_id' => $form_array['user_id'],
                                    'type' => 'airtime',
                                    'sub_type' => $network,
                                    'number' => $form_array['phone'],
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $form_array['amount'],
                                    'order_id' => $order_id
                                );
                                $this->addTransactionStatus($arrrr);
                                
                            }
                        }
                    }

                    
                } 
                else {
                    $url= "https://app.eminencesub.com/api/buy-airtime";
                    $type = strtoupper(substr($vtu_platform, 9));

                    $post_data = [
                        "phone" => $form_array['phone'],
                        "amount" => $form_array['amount'],
                        "network" => $eminence_ntwrk,
                        "type" => $type,
                    ];

                    // return $post_data;

                    $response = $this->eminenceVtuCurl($url, true, $post_data);

                    // return $response;

                    if ($this->isJson($response)) {
                        $response = json_decode($response);
                        if (is_object($response)) {
                            
                            // $status = $response->status;
                            // $message = $response->message;

                            // if ($status == true) {
                                // var_dump($response);
                                // return;
                                $order_id = $response->data->reference;
                                $arrrr = array(
                                    'user_id' => $form_array['user_id'],
                                    'type' => 'airtime',
                                    'sub_type' => $network,
                                    'number' => $form_array['phone'],
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $form_array['amount'],
                                    'order_id' => $order_id
                                );
                                $this->addTransactionStatus($arrrr);
                                
                            // }
                        }
                    }
                }
            }
        }else{
            $user_id = $form_array['user_id'];
           
            $amount = $form_array['amount'];
            


            $phone = $form_array['phone'];

            $amount_to_debit_user = $amount;

            $phone_code = $user->phone_code;

            $country = Country::find($user->country_id);
            // return $country;
            $country_id = $country->id;
            $countryCode = $country->code;
            $operatorId = $this->getReloadlyOperatorId($phone_code, $phone, $country_id);

            $useLocalAmount = false;
            $phone_number = $this->returnInterNumber($phone_code, $phone);


            $post_data = [
                'operatorId' => $operatorId,
                'amount' => $amount,
                'useLocalAmount' => $useLocalAmount,
                'recipientPhone' => [
                    'countryCode' => strtoupper($countryCode),
                    'number' => $phone_number,
                ],
            ];
            // return $post_data;

            $url = "https://topups.reloadly.com/topups";
            $use_post = true;
            $accept = 'application/com.reloadly.topups-v1+json';

            $response = $this->reloadlyCurl($url, $use_post, $post_data, $accept);

            // return $response;


            if ($this->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    if (isset($response->status)) {
                        if ($response->status && $response->transactionId) {
                            
                            

                            $order_id = "RE" .  $response->transactionId;
                            $form_array = array(
                                'reloadly' => 1,
                                'user_id' => $user_id,
                                'type' => 'airtime',
                                'sub_type' => 'reloadly',
                                'number' => $phone_number,
                                'date' => $date,
                                'time' => $time,
                                'amount' => $amount,
                                'order_id' => $order_id
                            );
                            if ($this->addTransactionStatus($form_array)) {
                                $response_arr['success'] = true;
                                $response_arr['order_id'] = $order_id;
                            }
                            
                        }
                    }
                }
            }
        
        }

    }

    public function getNumberOfLevelsInMlmSystem()
    {

        // $this->db->select("stage");
        // $this->db->from("mlm_db");
        // $this->db->order_by("stage","DESC");
        // $this->db->limit(1);

        // $query = $this->db->get();

        $query = DB::table('mlm_db')->select("stage")->orderBy("stage", "DESC")->limit(1)->get();

        return $query[0]->stage + 1;
    }

    public function getTotalNumberOfDownlineInMlmSystem()
    {
        // $query = $this->db->get("mlm_db");
        $query = DB::table('mlm_db')->select("id")->get();
        return $query->count() - 1;
    }

    public function printSponsorTree($level = 0, $parentID = null, $stage = null, $return_str = "", $j = 0)
    {
        $j++;
        // echo $j;
        // echo $level;

        // echo '<ul>';
        // echo '<li>';
        // echo '<span class="tf-nc">'.$parentID.'</span>';
        // Create the query
        $num_1 = false;
        $query_str = "SELECT * FROM mlm_db WHERE ";
        if ($parentID == null) {
            $query_str .= "sponsor IS NULL";
        } else {
            $query_str .= "`sponsor`=" . intval($parentID);
        }

        $query_str .= " ORDER BY positioning ASC";
        // Execute the query and go through the results.

        $query = DB::select($query_str);
        if (count($query) > 0) {
            if (count($query) == 1) {
                $num_1 = true;
            }

            $return_str .= '<ul>';
            foreach ($query as $row) {


                // Print the current ID;
                $currentID = $row->id;
                $positioning = $row->positioning;
                $user_id = $row->user_id;
                $logo = null;
                
                $full_name = $this->getUserParamById("name", $user_id);
                $date_created = $row->date_created;
                $index = $this->getMlmIdsIndexNumber($currentID);
                $phone_code = $this->getUserParamById("phone_code", $user_id);
                $phone = $this->getUserParamById("phone", $user_id);
                $full_phone_number = $phone_code . "" . $phone;
                $package = 2;
                if ($package == 1) {
                    $package = "basic";
                } else {
                    $package = "business";
                }
                if (is_null($logo)) {
                    $logo = '/images/nophoto.jpg';
                } else {
                    $logo = '/storage/images/' . $logo;
                }
                if ($num_1) {
                    if ($positioning == "left") {
                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                        $return_str .= '<img class="tree_icon" src="' . $logo . '">';
                        $return_str .= '<p class="demo_name_style">';
                        // $return_str .= $user_name;

                        $return_str .= " " . $full_name . "<br>  <span class='text-sm font-bold italic'>(" . $full_phone_number . ")<span>";

                        $return_str .= '</p>';

                        $return_str .= '</div>';


                        // $return_str .= '</li>';



                    } else {


                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


                        $return_str .= '<img class="tree_icon" src="' . $logo . '">';
                        $return_str .= '<p class="demo_name_style">';
                        // $return_str .= $user_name;

                        $return_str .= " " . $full_name . "<br>  <span class='text-sm font-bold italic'>(" . $full_phone_number . ")<span>";

                        $return_str .= '</p>';

                        $return_str .= '</div>';
                    }
                } else {
                    $return_str .= '<li>';
                    $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


                    $return_str .= '<img class="tree_icon" src="' . $logo . '">';
                    $return_str .= '<p class="demo_name_style">';
                    // $return_str .= $user_name;
                    $return_str .= " " . $full_name . "<br>  <span class='text-sm font-bold italic'>(" . $full_phone_number . ")<span>";

                    $return_str .= '</p>';

                    $return_str .= '</div>';
                }

                for ($i = 0; $i < $level; $i++) {
                    $return_str .= " ";
                }

                // echo $currentID . PHP_EOL;
                // Print all children of the current ID
                if ($j < $stage) {
                    // echo $j;
                    $return_str = $this->printSponsorTree($level + 1, $currentID, $stage, $return_str, $j);
                }

                $return_str .= '</li>';
            }
            $return_str .= '</ul>';
        } else {
        }
        // echo '</li>';
        // echo '</ul>';
        return $return_str;
    }

    public function checkIfPhoneExists($phone){
        $query = DB::table('users')->where('phone', $phone)->get('id');
        if($query->count() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function getUserIdByPhone($phone){
        $query = DB::table('users')->where('phone', $phone)->get('id');
        if ($query->count() > 0) {
            return $query[0]->id;
        } else {
            return false;
        }
    }

    public function printTree($package1, $your_mlm_db_id, $level = 0, $parentID = null, $stage = null, $return_str = "", $j = 0)
    {
        $j++;
        // echo $j;
        // echo $level;

        // echo '<ul>';
        // echo '<li>';
        // echo '<span class="tf-nc">'.$parentID.'</span>';
        // Create the query
        $num_1 = false;
        $query_str = "SELECT * FROM mlm_db WHERE ";
        if ($parentID == null) {
            $query_str .= "under IS NULL";
        } else {
            $query_str .= "`under`=" . intval($parentID);
        }

        $query_str .= " ORDER BY positioning ASC";
        // Execute the query and go through the results.

        $query = DB::select($query_str);
        if (count($query) > 0) {
            if (count($query) == 1) {
                $num_1 = true;
            }

            $return_str .= '<ul>';
            foreach ($query as $row) {


                // Print the current ID;
                $currentID = $row->id;
                $positioning = $row->positioning;
                $user_id = $row->user_id;
                $date_created = $row->date_created;
                $logo = null;
                $phone_code = $this->getUserParamById("phone_code", $user_id);
                $phone = $this->getUserParamById("phone", $user_id);
                $full_name = $this->getUserParamById("name", $user_id);
                $package = 2;
                $index = $this->getMlmIdsIndexNumber($currentID);
                $full_phone_number = $phone_code . "" . $phone;

                if ($package == 1) {
                    $package = "basic";
                } else {
                    $package = "business";
                }
                if (is_null($logo)) {
                    $logo = '/images/nophoto.jpg';
                } else {
                    $logo = '/storage/images/' . $logo;
                }
                if ($num_1) {
                    if ($positioning == "left") {
                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                        $return_str .= '<img class="tree_icon" src="' . $logo . '"">';
                        $return_str .= '<div class="demo_name_style grid grid-cols-12 gap-2">';


                        $return_str .= '<i onclick="goUpMlm(this,event,' . $currentID . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-up col-span-2 mt-2" style="cursor:pointer;"></i>';


                        
                        $return_str .= "<p class='col-span-8 text-xs'> " . $full_name . "<span class=' font-bold italic'>(" . $full_phone_number . ")</span></p>";



                        $return_str .= '<i onclick="goDownMlm(this,event,' . $currentID . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-down col-span-2 mt-2" style="cursor:pointer;"></i>';


                        $return_str .= '</div>';
                        $return_str .= '</div>';
                        // $return_str .= '</div>';
                        // $return_str .= '</li>';

                        // $return_str .= '<li>';
                        // $return_str .= '<div style="cursor:pointer;" class="tf-nc register" data-under="'.$parentID .'">';

                        // $return_str .= '<p class="register-text">Register</p>';
                        // // echo '<span class="tf-nc">'.$currentID.'</span>';
                        // $return_str .= '</div>';
                        // $return_str .= '</li>';

                    } else {

                        $return_str .= '<li>';
                        $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';

                        $return_str .= '<img class="tree_icon" src="' . $logo . '">';
                        $return_str .= '<div class="demo_name_style grid grid-cols-12 gap-2">';


                        $return_str .= '<i onclick="goUpMlm(this,event,' . $currentID . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-up col-span-2 mt-2" style="cursor:pointer;"></i>';


                        $return_str .= "<p class='col-span-8 text-xs'>" . $full_name . "<br>  <span class=' font-bold italic'>(" . $full_phone_number . ")</span></p>";



                        $return_str .= '<i onclick="goDownMlm(this,event,' . $currentID . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-down col-span-2 mt-2" style="cursor:pointer;"></i>';

                        $return_str .= '</div>';
                        $return_str .= '</div>';
                    }
                } else {
                    $return_str .= '<li>';
                    $return_str .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


                    $return_str .= '<img class="tree_icon" src="' . $logo . '">';
                    $return_str .= '<div class="demo_name_style grid grid-cols-12 gap-2">';

                    $return_str .= '<i onclick="goUpMlm(this,event,' . $currentID . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-up col-span-2 mt-2" style="cursor:pointer;"></i>';


                    $return_str .= "<p class='col-span-8 text-xs'>" . $full_name . "<br>  <span class=' font-bold italic'>(" . $full_phone_number . ")</span></p>";



                    $return_str .= '<i onclick="goDownMlm(this,event,' . $currentID . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-down col-span-2 mt-2" style="cursor:pointer;"></i>';

                    $return_str .= '</div>';
                    $return_str .= '</div>';
                    // $return_str .= '</div>';

                }

                for ($i = 0; $i < $level; $i++) {
                    $return_str .= " ";
                }

                // echo $currentID . PHP_EOL;
                // Print all children of the current ID
                if ($j < $stage) {
                    // echo $j;
                    $return_str = $this->printTree($package1, $your_mlm_db_id, $level + 1, $currentID, $stage, $return_str, $j);
                }

                $return_str .= '</li>';
            }
            $return_str .= '</ul>';
        } else {
        }

        return $return_str;
    }



    public function getMlmIdsIndexNumber($mlm_db_id)
    {
        $array = array();
        $user_id = $this->getMlmDbParamById("user_id", $mlm_db_id);
        // $this->db->select("id");
        // $this->db->from("mlm_db");
        // $this->db->where("user_id",$user_id);
        // $this->db->order_by("id","ASC");

        // $query = $this->db->get();

        $query = DB::table('mlm_db')->where("user_id", $user_id)->orderBy("id", "ASC")->get('id');
        if ($query->count() > 0) {
            foreach ($query as $row) {
                $id = $row->id;
                $array[] = $id;
            }
        }

        if (count($array) > 0) {
            for ($i = 0; $i < count($array); $i++) {
                if ($mlm_db_id == $array[$i]) {
                    return $i + 1;
                }
            }
        }
    }
    public function checkIfMlmDbIdBelongsToUser($mlm_db_id, $user_id)
    {

        $query = DB::table('mlm_db')->where('user_id', $user_id)->where('id', $mlm_db_id)->get();
        if ($query->count() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getLastTwentyUsersRegisteredUsers()
    {
        $date = date("j M Y");

        $query_str = "SELECT * FROM users WHERE created_date = '" . $date . "' ORDER BY id DESC LIMIT 20";
        $query = DB::select($query_str);
        if (count($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }


    public function getTotalAmountWithdrawnToday()
    {
        $total_amount = 0;
        $date = date("j M Y");

        
        $query = WithdrawalHistory::whereDate('created_at', Carbon::today())->get('amount');
        
        if ($query->count() > 0) {
            foreach ($query as $row) {
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }

        return $total_amount;
    }

    public function getTotalAmountForOnlinePaymentMadeToday()
    {
        $total_amount = 0;
        $date = date("j M Y");

        // $query_str = "SELECT amount FROM account_credit_history WHERE date = '" . $date . "' AND (payment_option = 'monnify' OR payment_option = 'paystack')";
        // $query = DB::select($query_str);
        $query = AccountCreditHistory::whereDate('created_at', Carbon::today())->where('payment_option', 'monnify')->where('payment_option', 'paystack')->get('amount');
        if ($query->count() > 0) {
            foreach ($query as $row) {
                $amount = $row->amount;
                $total_amount += $amount;
            }
        }

        return $total_amount;
    }

    public function getTotalNumberOfRegisteredUsers()
    {
        $query = DB::table('users')->get('id');
        return $query->count();
    }

    public function getTotalNumberOfUsersRegisteredToday()
    {
        $date = date("j M Y");
        $query = DB::table('users')->where("created_at", $date)->get('id');
        return $query->count();
    }

    public function getPackageAmountForSmileClub($club_type, $product_code)
    {
        $amount = false;
        $url = "https://www.nellobytesystems.com/APISmilePackagesV2.asp";
        // echo $url;
        $use_post = true;

        $response = $this->vtu_curl($url, $use_post, $post_data = []);

        if ($this->isJson($response)) {
            $response = json_decode($response);
            // var_dump($response);
            if (is_object($response)) {
                if (isset($response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT)) {
                    $j = 0;

                    $rows = $response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT;
                    for ($i = 0; $i < count($rows); $i++) {
                        $j++;

                        $package_id = $rows[$i]->PACKAGE_ID;
                        $package_name = $rows[$i]->PACKAGE_NAME;
                        $package_amount = $rows[$i]->PACKAGE_AMOUNT;

                        if ($package_id == $product_code) {
                            $amount = $package_amount;
                        }
                    }
                }
            }
        }

        return $amount;
    }

    public function addAirtimeToWalletRecord($form_array){

        AirtimeToWalletRecord::create($form_array);
        return true;

    }

    public function getVtuTransactionParamByOrderId($param, $order_id)
    {

        $query = DB::table('vtu_transactions')->where('order_id', $order_id)->get();
        if ($query->count() == 1) {
            return $query[0]->$param;
        } else {
            return false;
        }
    }

    public function checkIfTransactionIdIsValidPayscribeAirtimeToWallet($order_id)
    {

        $query = DB::table('vtu_transactions')->where('order_id', $order_id)->where('type', 'airtime_to_wallet')->get();
        if ($query->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getPackageAmountForCableTvClub($club_type, $product_code)
    {
        $amount = false;
        $url = "https://www.nellobytesystems.com/APICableTVPackagesV2.asp";
        // echo $url;
        $use_post = true;

        $response = $this->vtu_curl($url, $use_post, $post_data = []);

        if ($this->isJson($response)) {
            $response = json_decode($response);
            // var_dump($response);
            if (is_object($response)) {
                if (isset($response->TV_ID->$club_type)) {
                    $j = 0;

                    $rows = $response->TV_ID->$club_type[0]->PRODUCT;
                    for ($i = 0; $i < count($rows); $i++) {
                        $j++;

                        $package_id = $rows[$i]->PACKAGE_ID;
                        $package_name = $rows[$i]->PACKAGE_NAME;
                        $package_amount = $rows[$i]->PACKAGE_AMOUNT;

                        if ($package_id == $product_code) {
                            $amount = $package_amount;
                        }
                    }
                }
            }
        }

        return $amount;
    }


    public function buyPowerVtuCurl($url, $use_post, $post_data = [],$curl_extr = false)
    {
        $headers = [
            'Authorization' => 'Bearer '. env('BUYPOWER_BEARER'),
            'Content-Type' => 'application/json'
        ];
        if (!$use_post) {
            
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        
        } else {
            
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
            
        }

        // if (!$use_post) {
        //     if($curl_extr){
        //         $response = Http::withOptions([
        //             'verify' => false,
        //         ])->withHeaders($headers)->get($url, ['curl' => [CURLOPT_INTERFACE => "67.225.140.32"]]);
        //     }else{
        //         $response = Http::withOptions([
        //             'verify' => false,
        //         ])->withHeaders($headers)->get($url);
        //     }
        // } else {
        //     if ($curl_extr) {
        //         $response = Http::withOptions([
        //             'verify' => false,
        //         ])->withHeaders($headers)->post($url, $post_data,['curl' => [CURLOPT_INTERFACE => "67.225.140.32"]]);
        //     }else{
        //         $response = Http::withOptions([
        //             'verify' => false,
        //         ])->withHeaders($headers)->post($url, $post_data);
        //     }
        // }
        return $response;
    }

    public function sendMeterTokenForPrepaidToUserByNotif($user_id, $email, $date, $time, $orderid, $disco, $meter_no, $amount, $meter_token)
    {

        $title = "Successful Prepaid Electricity Recharge";
        $message = "Your Prepaid Electricity Recharge Was Successful With The Following Details: <br>";
        $message .= "Order Id: <em class='text-primary'>" . $orderid . "</em><br>";
        $message .= "Meter Token: <em class='text-primary'>" . $meter_token . "</em><br>";
        $message .= "Disco: <em class='text-primary'>" . $disco . "</em><br>";
        $message .= "Meter No.: <em class='text-primary'>" . $meter_no . "</em><br>";
        $message .= "Amount: <em class='text-primary'>" . $amount . "</em><br>";

        $form_array = array(
            'sender' => "System",
            'receiver' => $user_id,
            'title' => $title,
            'message' => $message,
            'date_sent' => $date,
            'time_sent' => $time,
            'type' => 'misc'
        );

     

        $this->sendMessage($form_array);
        $this->sendEmail($email, $title, $message);
    }

    public function sendEmail($email,$title,$message){
        Mail::to($email)->send(new NotifyMail($title,$message));

        // if (Mail::failures()) {
        //     return response()->Fail('Sorry! Please try again latter');
        // } else {
        //     return response()->success('Great! Successfully send in your mail');
        // }
    }

    public function getEminenceVtuDataBundleCostByProductId($url, $type, $product_id)
    {



        $use_post = false;
        $amount = 0;

        $response = $this->eminenceVtuCurl($url, $use_post, $post_data = []);

        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if (is_array($response->data)) {
                    $plans = $response->data;


                    for ($i = 0; $i < count($plans); $i++) {
                        $PRODUCT_ID = $plans[$i]->plan_id;
                        if ($PRODUCT_ID == $product_id) {
                            $amount = $plans[$i]->price;
                            break;
                        }
                    }
                }
            }
        }

        return $amount;
    }

    public function getGsubzVtuDataBundleCostByProductId($url, $type, $product_id)
    {



        $use_post = false;
        $amount = 0;

        $response = $this->gSubzVtuCurl($url, $use_post, $post_data = []);

        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if (is_array($response->plans)) {
                    $plans = $response->plans;


                    for ($i = 0; $i < count($plans); $i++) {
                        $PRODUCT_ID = $plans[$i]->value;
                        if ($PRODUCT_ID == $product_id) {
                            $amount = $plans[$i]->price;
                            break;
                        }
                    }
                }
            }
        }

        return $amount;
    }

    public function getVtuDataBundleCostByProductId($type, $product_id)
    {
        $url = "https://www.nellobytesystems.com/APIQueryV1.asp?UserID=".$this->CLUB_USERID."&APIKey=". $this->CLUB_APIKEY."&OrderID=6322187656";
        $url = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
        $use_post = true;
        $amount = 0;



        $response = $this->vtu_curl($url, $use_post, $post_data = []);

        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if ($type == "mtn") {
                    $network = "MTN";
                    $bundles = $response->MOBILE_NETWORK->MTN[0]->PRODUCT;
                } else if ($type == "glo") {
                    $network = "Glo";
                    $bundles = $response->MOBILE_NETWORK->Glo[0]->PRODUCT;
                } else if ($type == "airtel") {
                    $network = "Airtel";
                    $bundles = $response->MOBILE_NETWORK->Airtel[0]->PRODUCT;
                } else if ($type == "9mobile") {
                    $network = "9mobile";
                    $bundles = $response->MOBILE_NETWORK->{'9mobile'}[0]->PRODUCT;
                }

                for ($i = 0; $i < count($bundles); $i++) {
                    $PRODUCT_ID = $bundles[$i]->PRODUCT_ID;
                    if ($PRODUCT_ID == $product_id) {
                        $amount = $bundles[$i]->PRODUCT_AMOUNT;
                        break;
                    }
                }
            }
        }

        return $amount;
    }

    public function getPayscribeVtuDataBundleCostByProductId($type, $product_id)
    {
        $url = "https://www.payscribe.ng/api/v1/data/lookup";


        $use_post = true;
        $amount = 0;

        $response = $this->payscribeVtuCurl($url, $use_post, $post_data = []);

        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if ($response->status == true) {

                    $out = array_column($response->message->details, null, "network_name");

                    // echo json_encode($out['glo']);
                    if ($type == "mtn") {
                        $network = "Mtn";
                        $plans = $out['mtn']->plans;
                    } elseif ($type == "glo") {
                        $network = "Glo";
                        // $plans = $response->message->details[1]->plans;
                        $plans = $out['glo']->plans;
                    } else if ($type == "airtel") {
                        $network = "Airtel";
                        // $plans = $response->message->details[2]->plans;
                        $plans = $out['airtel']->plans;
                    } else if ($type == "9mobile") {
                        $network = "9mobile";
                        // $plans = $response->message->details[3]->plans;
                        $plans = $out['9mobile']->plans;
                    }

                    for ($i = 0; $i < count($plans); $i++) {
                        $PRODUCT_ID = $plans[$i]->plan_code;
                        if ($PRODUCT_ID == $product_id) {
                            $amount = $plans[$i]->amount;
                            break;
                        }
                    }
                }
            }
        }

        return $amount;
    }

    public function get9mobileComboDataAmountByProductId($product_id)
    {
        $query = DB::table('9mobile_combo_data_plans')->where('id', $product_id)->get();
        if ($query->count() == 1) {
            return $query[0]->data_amount;
        }
    }

    public function get9mobileComboCostByProductId($product_id)
    {

        $query = DB::table('9mobile_combo_data_plans')->where('id', $product_id)->get();
        if ($query->count() == 1) {
            return $query[0]->amount;
        }
    }

    public function get9mobileComboDataBundles()
    {
        $query = DB::table('9mobile_combo_data_plans')->orderBy("amount", "ASC")->get();
        if ($query->count() > 0) {
            return $query;
        } else {
            return false;
        }
    }

    public function generateDataPlansForNetworkPayscribe($network){
        $data_plans = array();
        $url = "https://www.payscribe.ng/api/v1/data/lookup";
        $use_post = true;
        
        
        if($network == "glo"){
            $network = "GLO";
            
            $network_2 = "Glo";
            $perc_disc = 0.04;
            $additional_charge = 10;
        }else if($network == "airtel"){
            $network = "AIRTEL";
            
            $network_2 = "Airtel";
            $perc_disc = 0.04;
            $additional_charge = 10;
        }else if($network == "9mobile"){
            $network = "9MOBILE";
            
            $network_2 = "9mobile";
            $perc_disc = 0.04;
            $additional_charge = 10;

        }
    

        $post_data = array(
            'network' => $network_2
        );

        
        $response = $this->payscribeVtuCurl($url,$use_post,$post_data);


        if($this->isJson($response)){
            $response = json_decode($response);
            // var_dump($response);
            if(is_object($response)){
                if($response->status && $response->status_code == 200){
                
                    
                    // var_dump($response->message->details);
                    
                    $plans_2 = $response->message->details[0]->plans;
                    

                    if(is_array($plans_2)){

                        
                        $j = 0;

                        // echo json_encode($plans);
                        // echo json_encode($plans_2);

                        
                        $plan_2_new_arr = array();
                        

                        for($i = 0; $i < count($plans_2); $i++){
                            
                            $product_id = $plans_2[$i]->plan_code;
                            // $product_id = $plans[$i]->PRODUCT_ID;
                            $product_name = $plans_2[$i]->name;
                            $product_amount = $plans_2[$i]->amount;
                            // $product_amount = $product_amount + 20;
                            
                            // $product_amount = round((0.04 * $product_amount) + $product_amount,2);

                            $product_amount += $additional_charge;
                        
                            

                            $plan_2_new_arr[$i] = [
                                "network" => $network,
                                "sub_type" => "payscribe",
                                "product_name" => $product_name,
                                "amount" => $product_amount,
                                "product_id" => $product_id,
                                "product_code" => ""

                            ];

                        }

                        $all_plans_arr = $plan_2_new_arr;

            
                        $index = 0;
                        for($i = 0; $i < count($all_plans_arr); $i++){
                            $index++;
                            $all_plans_arr[$i]['index'] = $index;
                            $product_amount = $all_plans_arr[$i]['amount'];
                            $all_plans_arr[$i]['amount'] = number_format($product_amount,2);
                            $all_plans_arr[$i]['combo'] = false;
                        }
                        $data_plans = $all_plans_arr;

                    }
                }
            }
        }
        return $data_plans;
    }

    public function generateDataPlansForNetworkEminence($network)
    {
        $data_plans = array();
        $url = "https://app.eminencesub.com/api/data";
        $use_post = false;


        if ($network == "mtn") {
            $network_id = 1;
            $perc_disc = 0.04;
            $additional_charge = 25;
        } else if ($network == "glo") {
            $network_id = 3;
            $perc_disc = 0.04;
            $additional_charge = 25;
        } else if ($network == "airtel") {
            $network_id = 2;
            $perc_disc = 0.04;
            $additional_charge = 25;
        } else if ($network == "9mobile") {
            $network_id = 4;
            $perc_disc = 0.04;
            $additional_charge = 25;
        }


        $response = $this->eminenceVtuCurl($url, $use_post, $post_data = []);
        // dd($response);

        if ($this->isJson($response)) {
            $response = json_decode($response);
            // return $response;

            if (is_object($response)) {
                if ($response->code == 200) {


                    $plans = $response->data;


                    if (is_array($plans)) {

                        $j = 0;

                        $plan_new_arr = [];


                        for ($i = 0; $i < count($plans); $i++) {
                            // $perc_disc = 0;
                            // $additional_charge = 0;

                            $product_id = $plans[$i]->plan_id;
                            $network_id_cmp = $plans[$i]->network_id;
                            // $product_id = $plans[$i]->PRODUCT_ID;
                            $product_name = $plans[$i]->name;
                            $product_amount = $plans[$i]->price;
                            $validity = $plans[$i]->validity;

                            $product_name .= " (" . $validity . ")";

                            if (is_numeric($product_amount)) {

                                
                                

                                // $product_amount = round(($perc_disc * $product_amount) + $product_amount, 2);

                                $product_amount += $additional_charge;

                              


                                if ($network_id_cmp == $network_id) {
                                    $plan_obj = [
                                        "network" => $network,
                                        "sub_type" => "eminence",
                                        "product_name" => $product_name,
                                        "amount" => $product_amount,
                                        "product_id" => $product_id,
                                        "product_code" => ""

                                    ];

                                    
                                    array_push($plan_new_arr, $plan_obj);
                                }
                            }
                        }

                        // return $plan_new_arr;

                        $all_plans_arr = $plan_new_arr;

                        


                        $index = 0;
                        for ($i = 0; $i < count($all_plans_arr); $i++) {
                            $index++;
                            $all_plans_arr[$i]['index'] = $index;
                            $product_amount = $all_plans_arr[$i]['amount'];
                            $all_plans_arr[$i]['amount'] = number_format($product_amount, 2);
                            $all_plans_arr[$i]['combo'] = false;
                        }

                        //Add gsubz mtn data
                        

                        //Add Club Data
                        

                        $data_plans = $all_plans_arr;
                    }
                }
            }
        }
        return $data_plans;
    }

    public function generateDataPlansForNetworkClub($network)
    {
        $data_plans = array();
        $url = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
        $use_post = true;

        $response = $this->vtu_curl($url, $use_post, $post_data = []);


        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {

                if ($network == "mtn") {
                    $network = "MTN";
                    $network_num = 01;
                    $perc_disc = 0.04;
                } elseif ($network == "glo") {
                    $network = "Glo";
                    $network_num = 02;
                    $perc_disc = 0.04;
                } else if ($network == "9mobile") {
                    $network = "9mobile";
                    $network_num = 03;
                    $perc_disc = 0.04;
                } else if ($network == "airtel") {

                    $network = "Airtel";
                    $network_num = 04;
                    $perc_disc = 0.04;
                }



                $plans = $response->MOBILE_NETWORK->$network[0]->PRODUCT;
                // return json_encode($plans);
                // return $network;


                if (is_array($plans)) {
                    $index = 0;
                    $real_plans_arr = array();

                    for ($i = 0; $i < count($plans); $i++) {

                        $product_code = $plans[$i]->PRODUCT_CODE;
                        $product_id = $plans[$i]->PRODUCT_ID;
                        $product_name = $plans[$i]->PRODUCT_NAME;
                        $product_amount = $plans[$i]->PRODUCT_AMOUNT;
                        // $product_amount = $product_amount + 20;
                        if ($network == "MTN") {
                           
                            $amt_to_add = 25;
                            
                        } else if ($network == "Glo") {
                            $amt_to_add = 25;
                        } else if ($network == "9mobile") {
                            $amt_to_add = 25;
                        } else if ($network == "Airtel") {
                            $amt_to_add = 25;
                        }

                        // $product_amount = round((($perc_disc * $product_amount) + $product_amount) + $amt_to_add, 2);
                        $product_amount = $product_amount + $amt_to_add;

                        // if (($product_code != "3000" && $network == "MTN") || $network == "Glo" || $network == "9mobile" || $network == "Airtel") {
                            // if($network == "MTN" || $network == "Glo" || $network == "9mobile" || $network == "Airtel") {
                            $index++;
                            $real_plans_arr[$index - 1]['index'] = $index;
                            $real_plans_arr[$index - 1]['amount'] = number_format($product_amount, 2);
                            $real_plans_arr[$index - 1]['product_code'] = $product_code;
                            $real_plans_arr[$index - 1]['product_id'] = $product_id;
                            $real_plans_arr[$index - 1]['product_name'] = $product_name;
                            $real_plans_arr[$index - 1]['sub_type'] = 'clubkonnect';
                            $real_plans_arr[$index - 1]['network'] = $network;
                            $real_plans_arr[$index - 1]['combo'] = false;
                        // }
                    }

                    $data_plans = $real_plans_arr;
                }
            }
        }
        return $data_plans;
    }

    public function generateDataPlansForNetworkGsubz($network, $serviceID = NULL)
    {
        $data_plans = array();
        if (!is_null($serviceID)) {
            $url = "https://gsubz.com/api/plans/?service=" . $serviceID;
            $use_post = false;


            if ($network == "mtn") {
                $network = "MTN";
                // $url = "https://gsubz.com/api/plans/?service=mtn_sme";
                // $url_2 = "https://gsubz.com/api/plans/?service=mtncg";

                $network_2 = "Mtn";
                $perc_disc = 0.04;
                $additional_charge = 25;
            } else if ($network == "glo") {
                $network = "GLO";
                // $url .= "glo_data";
                $network_2 = "Glo";
                $perc_disc = 0.04;
                $additional_charge = 25;
            } else if ($network == "airtel") {
                $network = "AIRTEL";
                // $url .= "airtelcg";
                // $url .= "airtel_cg";
                $network_2 = "Airtel";
                $perc_disc = 0.04;
                $additional_charge = 25;
            } else if ($network == "9mobile") {
                $network = "9MOBILE";
                // $url .= "etisalat_data";
                $network_2 = "9mobile";
                $perc_disc = 0.04;
                $additional_charge = 25;
            }


            // $post_data = array(
            //     'network' => $network_2
            // );




            $response = $this->gSubzVtuCurl($url, $use_post);
            // return $response;


            if ($this->isJson($response)) {
                $response = json_decode($response);
                // var_dump($response);
                if (is_object($response)) {
                    if (is_array($response->plans)) {


                        $plans = $response->plans;


                        if (is_array($plans)) {

                            if ($network == "MTN") {
                                for ($i = 0; $i < count($plans); $i++) {
                                    if (strpos($url, "sme")) {
                                        $plans[$i]->gsubz_type = "sme";
                                    } else {
                                        $plans[$i]->gsubz_type = "regular";
                                        // $additional_charge = 28;
                                    }
                                }

                               
                            }

                            // return $plans;

                            $j = 0;


                            $plan_new_arr = array();


                            for ($i = 0; $i < count($plans); $i++) {

                                $product_id = $plans[$i]->value;
                                // $product_id = $plans[$i]->PRODUCT_ID;
                                $product_name = $plans[$i]->displayName;
                                $product_amount = $plans[$i]->price;
                                // $product_amount = $product_amount + 20;


                                // $product_amount = round((0.04 * $product_amount) + $product_amount, 2);

                                $product_amount += $additional_charge;

                                if ($network == "MTN") {
                                    $gsubz_type = $plans[$i]->gsubz_type;
                                }



                                $plan_new_arr[$i] = [
                                    "network" => $network,
                                    "sub_type" => "gsubz",
                                    "product_name" => $product_name,
                                    "amount" => $product_amount,
                                    "product_id" => $product_id,
                                    "product_code" => $plans[$i]->price,


                                ];

                                if ($network == "MTN") {
                                    $plan_new_arr[$i]['gsubz_type'] = $gsubz_type;
                                }

                                // if($network == "MTN" && $product_id == "179"){
                                //     $plan_new_arr[$i]['amount'] = $product_amount + 8;
                                // }

                            }

                            // return $plan_new_arr;

                            $price = array_column($plan_new_arr, 'amount');
                            array_multisort($price, SORT_ASC, $plan_new_arr);
                            $all_plans_arr = $plan_new_arr;


                            $index = 0;
                            for ($i = 0; $i < count($all_plans_arr); $i++) {
                                $index++;
                                $all_plans_arr[$i]['index'] = $index;
                                $product_amount = $all_plans_arr[$i]['amount'];
                                $all_plans_arr[$i]['amount'] = number_format($product_amount, 2);
                                $all_plans_arr[$i]['combo'] = false;
                            }
                            // return $all_plans_arr;

                          
                           

                            $data_plans = $all_plans_arr;
                        }
                    }
                }
            }
        }
        return $data_plans;
    }

    public function payscribeVtuCurl($url, $use_post, $post_data = [])
    {
        $headers = [
            'Authorization' => 'Bearer '. env('PAYSCRIBE_BEARER'),
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache'
        ];
        if (!$use_post) {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        } else {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }


    public function generateMtnDataPlans()
    {
        $data_plans = array();
        $network = "mtn";
        $url_1 = "https://www.nellobytesystems.com/APIDatabundlePlansV1.asp";
        $url_2 = "https://www.payscribe.ng/api/v1/data/lookup";
        $use_post = true;

        $response = $this->vtu_curl($url_1, $use_post, $post_data = []);


        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {


                $network = "MTN";
                $network_num = 01;
                $network_2 = "Mtn";
                $perc_disc = 0.04;




                $plans = $response->MOBILE_NETWORK->$network[0]->PRODUCT;


                if (is_array($plans)) {

                    $post_data = array(
                        'network' => $network_2
                    );

                    // dd($post_data);

                    $response = $this->payscribeVtuCurl($url_2, $use_post, $post_data);


                    if ($this->isJson($response)) {
                        $response = json_decode($response);
                        // var_dump($response);
                        if (is_object($response)) {
                            if ($response->status && $response->status_code == 200) {


                                // var_dump($response->message->details[]);

                                $plans_2 = $response->message->details[0]->plans;


                                if (is_array($plans_2)) {


                                    $j = 0;

                                    // echo json_encode($plans);
                                    // echo json_encode($plans_2);

                                    $plan_1_new_arr = array();
                                    $plan_2_new_arr = array();
                                    for ($i = 0; $i < count($plans); $i++) {

                                        $product_code = $plans[$i]->PRODUCT_CODE;
                                        $product_id = $plans[$i]->PRODUCT_ID;
                                        $product_name = $plans[$i]->PRODUCT_NAME;
                                        $product_amount = $plans[$i]->PRODUCT_AMOUNT;
                                        // $product_amount = $product_amount + 20;

                                        $product_amount = round(($perc_disc * $product_amount) + $product_amount, 2);



                                        $product_amount += 25;

                                        // $product_amount += 2;

                                        if ($product_id == "10000") {
                                            $product_amount = 2600;
                                        }
                                        // echo $product_id . "<br>";

                                        if ($product_id != "500" && $product_id != "1000" && $product_id != "1500" && $product_id != "2000" && $product_id != "3000" && $product_id != "4500" && $product_id != "5000" && $product_id != "6000" && $product_id != "10000") {

                                            // echo $product_id . "<br>";
                                            $plan_1_new_arr[$i] = [
                                                "network" => $network,
                                                "sub_type" => "clubkonnect",
                                                "product_name" => $product_name,
                                                "amount" => $product_amount,
                                                "product_id" => $product_id,
                                                "product_code" => $product_code
                                            ];
                                        }
                                    }

                                    for ($i = 0; $i < count($plans_2); $i++) {

                                        $product_id = $plans_2[$i]->plan_code;
                                        // $product_id = $plans[$i]->PRODUCT_ID;
                                        $product_name = $plans_2[$i]->name;
                                        $product_amount = $plans_2[$i]->amount;
                                        // $product_amount = $product_amount + 20;

                                        $product_amount = round((0.04 * $product_amount) + $product_amount, 2);

                                        $product_amount += 25;



                                        $plan_2_new_arr[$i] = [
                                            "network" => $network,
                                            "sub_type" => "payscribe",
                                            "product_name" => $product_name,
                                            "amount" => $product_amount,
                                            "product_id" => $product_id,
                                            "product_code" => ""

                                        ];
                                    }

                                    $all_plans_arr = array_merge($plan_2_new_arr, $plan_1_new_arr);

                                    $index = 0;
                                    for ($i = 0; $i < count($all_plans_arr); $i++) {
                                        $index++;
                                        $all_plans_arr[$i]['index'] = $index;
                                        $product_amount = $all_plans_arr[$i]['amount'];
                                        $all_plans_arr[$i]['amount'] = number_format($product_amount, 2);
                                        $all_plans_arr[$i]['combo'] = false;
                                    }
                                    $data_plans = $all_plans_arr;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data_plans;
    }

    public function loadDataPlansForNetwork($network, $combo = false)
    {
        $data_plans = array();
        if (!$combo) {
            $vtu_platform = $this->getVtuPlatformToUse('data', $network);
            $vtu_platform_shrt = substr($vtu_platform, 0, 5);
            if ($network == "mtn") {

                if ($vtu_platform == "payscribe") {
                    $data_plans = $this->generateMtnDataPlans();
                } else if ($vtu_platform_shrt == "gsubz") {
                    $serviceID = substr($vtu_platform, 6);
                    $data_plans = $this->generateDataPlansForNetworkGsubz($network, $serviceID);
                } else if ($vtu_platform_shrt == "gsubz_mtn_cg") {
                    $data_plans = $this->generateDataPlansForNetworkGsubz($network, "cg");
                } else if ($vtu_platform == "eminence") {
                    $data_plans = $this->generateDataPlansForNetworkEminence($network);
                } else {
                    $data_plans = $this->generateDataPlansForNetworkClub($network);
                }
            } else if ($network == "glo" || $network == "9mobile" || $network == "airtel" || $network == "mtn") {
                if ($vtu_platform == "payscribe") {
                    $data_plans = $this->generateDataPlansForNetworkPayscribe($network);
                } else if ($vtu_platform_shrt == "gsubz") {
                    $serviceID = substr($vtu_platform, 6);
                    $data_plans = $this->generateDataPlansForNetworkGsubz($network, $serviceID);
                } else if ($vtu_platform == "eminence") {

                    $data_plans = $this->generateDataPlansForNetworkEminence($network);
                } else {
                    $data_plans = $this->generateDataPlansForNetworkClub($network);
                }
            }
        } else if ($combo && $network == "9mobile") {
            $bundles = $this->get9mobileComboDataBundles();


            if (is_object($bundles)) {

                $index = 0;
                $j = -1;


                $new_arr = array();
                foreach ($bundles as $row) {
                    $index++;
                    $j++;
                    $product_id = $row->id;
                    $product_name = $row->data_amount;
                    $product_amount = $row->amount;
                    $new_arr[$j]['index'] = $index;
                    $new_arr[$j]['amount'] = number_format($product_amount, 2);
                    $new_arr[$j]['product_name'] = $product_name;
                    $new_arr[$j]['network'] = $network;
                    $new_arr[$j]['product_id'] = $product_id;
                    $new_arr[$j]['sub_type'] = 'combo';
                    $new_arr[$j]['combo'] = true;
                }
                $data_plans = $new_arr;
            }
        }
        return $data_plans;
    }

    public function getFreshEminenceSubToken($second_table = false)
    {
        $url = "https://app.eminencesub.com/api/login";
        if ($second_table) {
            $post_data = [
                'identity' => env('EMINENCE_IDENTITY'),
                'password' => env('EMINENCE_PASSWORD')
            ];
        } else {
            $post_data = [
                'identity' => env('EMINENCE_IDENTITY'),
                'password' => env('EMINENCE_PASSWORD')
            ];
        }


        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders($headers)->post($url, $post_data);

        return $response;
    }

    public function getEminenceSubBearerToken($second_table = false)
    {
        if ($second_table) {
            $table_name = 'eminence_sub_token_2';
        } else {
            $table_name = 'eminence_sub_token';
        }
        $bearer_token = "";
        $query = DB::table($table_name)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $token = $row->bearer_token;
                $expires_in = date("Y-m-d", strtotime($row->expires_in));
            }

            $date_time = date("Y-m-d");

            $curr_date = strtotime($date_time);

            $expires_in = strtotime($expires_in);

            $date_diff = ($expires_in - $curr_date) / 86400;
            if ($date_diff <= 0) {
                $token_details = json_decode($this->getFreshEminenceSubToken($second_table));
                if (isset($token_details->access_token) && isset($token_details->expires_in)) {
                    $bearer_token = $token_details->access_token;
                    $expires_in = $token_details->expires_in;

                    $form_array = array(
                        'bearer_token' => $bearer_token,
                        'expires_in' => $expires_in
                    );
                    DB::table($table_name)->where('id', 1)->update($form_array);
                }
            } else {
                $bearer_token = $token;
            }
        } else if ($query->count() == 0) {
            $token_details = json_decode($this->getFreshEminenceSubToken($second_table));
            if (isset($token_details->access_token) && isset($token_details->expires_in)) {
                $bearer_token = $token_details->access_token;
                $expires_in = $token_details->expires_in;

                $form_array = array(
                    'bearer_token' => $bearer_token,
                    'expires_in' => $expires_in
                );
                DB::table($table_name)->insert($form_array);
            }
        }

        return $bearer_token;
    }

    public function eminenceVtuCurl($url, $use_post, $post_data=[],$second_table = false){
        // return $post_data;
        $bearer_token = $this->getEminenceSubBearerToken($second_table);
        // return $bearer_token;
        $headers = [
            'Authorization' => 'Bearer '.$bearer_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        if(!$use_post){
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        }else{
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    
    }

    public function curl($url, $use_post, $post_data = [])
    {
        if (!$use_post) {
            $response = Http::withOptions([
                'verify' => false,
            ])->get($url);
        } else {
            $response = Http::withOptions([
                'verify' => false,
            ])->post($url,$post_data);
        }

        return $response;
    }

    public function vtu_curl($url, $use_post, $post_data = [])
    {
        if (!$use_post) {
            $response = Http::withOptions([
                'verify' => false,
            ])->get($url);
        } else {
            $response = Http::withOptions([
                'verify' => false,
            ])->post($url,$post_data);
        }

        return $response;
    }

    public function gSubzVtuCurl($url, $use_post, $post_data = [])
    {
        
        $api = $this->getGsubzApiKey();
        $headers = [
            'Authorization' => 'Bearer ' . $api,


        ];
        if (!$use_post) {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        } else {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://gsubz.com/api/pay/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $post_data,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $api,
                    'Cookie: PHPSESSID=6f4bddcae09aa26dace3c6bd726c23f5'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
        }
        return $response;
    }

    public function getGsubzApiKey()
    {
        return env("GSUBZ_APIKEY");
    }

    public function getVtuPlatformToUse($type,$network){
        if($type != "" ){
            $str = $network . '_' . $type;
        }else{
            $str = $network;
        }
        // echo $str;
        $query = DB::table('vtu_platform')->where('name', $str)->get('platform');
        if($query->count() == 1){
            return $query[0]->platform;
        }
    }

    public function addTransactionStatus($form_array)
    {
        $date = $form_array['date'];
        $time = $form_array['time'];
        $type = $form_array['type'];
        if ($type == "airtime" || $type == "data") {
            $user_id = $form_array['user_id'];
            $vtu_steps = $this->getVtuStepsNum();
            $amount = $form_array['amount'];
            if($type == "airtime"){
                $charge_id = 11;
                $summary = 'Upteam Airtime Bonus';
                $airtime_perc = $this->getAirtimeEarningPerc();
                $vtu_income = ($airtime_perc / 100) * $amount;
            }else{
                $charge_id = 12;
                $summary = 'Upteam Data Bonus';
                $data_amt = $this->getDataAmtToShare();
                $vtu_income = $data_amt;
            }

            $ids_to_credit = $this->getIdsToCreditUpteam($user_id, $vtu_steps);
            $ids_to_credit_num = count($ids_to_credit);
            for ($i = 0; $i < count($ids_to_credit); $i++) {
                $placements_user_id = $ids_to_credit[$i];
                $placements_mlm_db_id = $this->getUsersFirstMlmDbId($placements_user_id);


                MlmEarning::create([
                    'user_id' => $placements_user_id,
                    'mlm_db_id' => $placements_mlm_db_id,
                    'type' => $charge_id,
                    'amount' => $vtu_income,
                    'date' => $date,
                    'time' => $time,
                ]);

                
                
                $this->creditUser($placements_user_id, $vtu_income, $summary);
            }
            
        }
        
        VtuTransaction::create($form_array);

        return true;
    }

    public function getUserTotalAmountByUse($user_id)
    {

        $query = DB::table('users')->where('id', $user_id)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $total_income = $row->total_income;
                $withdrawn = $row->withdrawn;
            }
            return $total_income - $withdrawn;
        } else {
            return false;
        }
    }

    public function paystackCurl($url, $use_post, $post_data = [])
    {

        $headers = [
            'Authorization' => 'Bearer sk_live_82b42e494e55a2f3bf8f458ebf01070a50a7e668',
            'Content-Type' => 'application/json'
        ];
        if (!$use_post) {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        } else {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }

    public function addComboRequest($form_array)
    {
        ComboRechargeVtu::create($form_array);
        return true;
    }


    public function addWithrawalHistory($user_id, $amount)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");

        WithdrawalHistory::create([
            'user_id' => $user_id, 
            'amount' => $amount, 
            'date' => $date, 
            'time' => $time
        ]);
        return true;
    }

    public function sendWithrawalRequest($form_array)
    {

        WithdrawalRequest::create($form_array);
        return true;
    }

    public function transferFundsToUser($user_id, $recepient_id, $amount, $date, $time)
    {

        // $sender_full_name = $this->getUserParamById("full_name", $user_id);
        // $sender_slug = $this->getUserParamById("slug", $user_id);
        $summary = "Member To Member Transfer";
        if ($this->debitUser($user_id, $amount, $summary)) {
            if ($this->creditUser($recepient_id, $amount, $summary)) {

                TransferFundsHistory::create([
                    'sender' => $user_id,
                    'recepient' => $recepient_id,
                    'amount' => $amount,
                    'date' => $date,
                    'time' => $time
                ]);
                return true;
            }
        }
    }

    public function addNewAccountCreditHistory($form_array)
    {
        AccountCreditHistory::create($form_array);
        return true;
    }

    public function addAdminDebitHistory($user_id, $amount, $date, $time){
        AdminDebitUsersHistory::create([
            'user_id' => $user_id,
            'amount' => $amount,
            'date' => $date,
            'time' => $time
        ]);
    }


    public function addAdminCreditHistory($user_id, $amount, $date, $time)
    {

        AdminFundUsersHistory::create([
            'user_id' => $user_id, 
            'amount' => $amount, 
            'date' => $date, 
            'time' => $time
        ]);

    }

    public function getDownlineArr1($parentID, $ret_arr = array())
    {


        // Create the query

        $query_str = "SELECT id,date_created FROM mlm_db WHERE ";
        if ($parentID == null) {
            $query_str .= "under IS NULL";
        } else {
            $query_str .= "`under`=" . intval($parentID);
        }

        $query_str .= " ORDER BY positioning ASC";
        // Execute the query and go through the results.

        $query = DB::select($query_str);

        if (count($query) > 0) {

            foreach ($query as $row) {
                $currentID = $row->id;
                $date_created = $row->date_created;
                
                // echo $currentID;
                $ret_arr[] = array($currentID, $date_created);

                $ret_arr = $this->getDownlineArr1($currentID, $ret_arr);
            }
        }

        return $ret_arr;
    }

    public function getTotalAmountWithdrawnByUser($user_id)
    {
        $total_withdrawn = 0;
        $query = DB::table('withdrawal_history')->where("user_id", $user_id)->get("amount");
        if ($query->count() > 0) {
            foreach ($query as $row) {
                $amount = $row->amount;
                $total_withdrawn += $amount;
            }
        }

        return $total_withdrawn;
    }

    public function processUserWeeklyRout($user_id){
        $json_post = '[{"DepartmentId":1,"DepartmentName":"IT"},{"DepartmentId":2,"DepartmentName":"Support"}]';
        $date_time = date("Y-m-d H:i:s");
        $date = date("j M Y");
        $time = date("h:i:sa");
        // $this->addMinifyAccountWebhookJsonData($user_id, $date, $time);
        $weekly_sub_amt = $this->getWeeklySubAmt();
        $amt_to_share = $this->getAmtToShare();
        $airtime_amt = $this->getAirtimeAmt();
        $final_earning_perc = $this->getFinalEarningPerc();
        $upteam_perc = $this->getUpteamPerc();
        $upteam_num = $this->getUpteamNum();
        $admin_perc = $this->getAdminEarningPerc();
        $first_admin_earning = $this->getFirstAdminEarning();
        // dd($weekly_sub_amt);
        $user = User::find($user_id);
        $week_balances = MlmWeekly::where('user_id',$user_id)->first();
        $week_start = $week_balances->week_start;
        $total_sponsor = $week_balances->total_sponsor;
        $total_placement = $week_balances->total_placement;
        $total_upteam = $week_balances->total_upteam;

        $total_income = $total_sponsor + $total_placement + $total_upteam;

        if($total_income >= $weekly_sub_amt){
            $rem_amt = $total_income - $weekly_sub_amt;

            $sponsor_mlm_db_id = $this->getUsersFirstMlmDbId($user->sponsor_user_id);

            $this->creditUserSponsorIncome($user_id, $user->sponsor_user_id, $amt_to_share, $sponsor_mlm_db_id, $date, $time,true);

            $placement_num = $this->getPlacementNumResub();

            $placement_perc = $this->getPlacementPercResub();


            $placement_income = ($placement_perc / 100) * $amt_to_share;

            $mlm_db_id = MlmDb::where('user_id', $user_id)->first()->id;

            $this->creditUserPlacementIncome($mlm_db_id, $placement_income, $placement_num, $date, $time);

            //Then, Credit airtime to users phone num. Remember to do this. You've not done it yet

            
            // $arrrrr = [
            //     'user_id' => $user->id,
            //     'phone_code' => $user->phone_code,
            //     'phone' => $user->phone,
            //     'amount' => $airtime_amt,
            //     // 'amount' => 50,
            // ];
            // $this->sendAirtime($arrrrr);
            
            //Now share 100 naira to admin

            $admin = User::find($this->getAdminId());
            $total_admin_earnings = $admin->total_admin_earnings;
            $admin->total_admin_earnings = $total_admin_earnings + $first_admin_earning;

            $admin->save();


            $user_final_amt = ($final_earning_perc / 100) * $rem_amt;
            $total_earnings = $user->total_earnings;
            $user->total_earnings = $total_earnings + $user_final_amt;
            $user->save();

            $summary = "Weekly Earnings To Main Wallet Transfer";
            $this->creditUser($user_id, $user_final_amt, $summary);
            $this->addEarningTransferHistory($user_id, $user_final_amt);
            //Share for upteam support i.e 24 users registered after him

            
            $upteam_income = ($upteam_perc / 100) * $rem_amt;

            $this->creditUserUpteamIncome($user_id, $upteam_income, $upteam_num, $date, $time);

            $admin_earning = ($admin_perc / 100) * $rem_amt;

            $admin = User::find($this->getAdminId());
            $total_admin_earnings = $admin->total_admin_earnings;
            $admin->total_admin_earnings = $total_admin_earnings + $admin_earning;

            $admin->save();



            $week_balances->week_start = $date_time;
            $week_balances->total_sponsor = 0.00;
            $week_balances->total_placement = 0.00;
            $week_balances->total_upteam = 0.00;
            $week_balances->save();
            
        }else{
            
            $week_balances->week_start = $date_time;
            $week_balances->save();
        }
    }

    public function addEarningTransferHistory($user_id, $amount){
        $date = date("j M Y");
        $time = date("h:i:sa");
        EarningToWalletHistory::create([
            'user_id' => $user_id,
            'amount' => $amount,
            'date' => $date,
            'time' => $time,
        ]);
    }

    public function creditUserUpteamIncome($user_id, $upteam_income, $placement_num, $date, $time)
    {
        $real_upteam_income = $upteam_income / $placement_num;
        
        $ids_to_credit = $this->getIdsToCreditUpteam($user_id, $placement_num);
        $ids_to_credit_num = count($ids_to_credit);
        for ($i = 0; $i < count($ids_to_credit); $i++) {
            $placements_user_id = $ids_to_credit[$i];
            $placements_mlm_db_id = $this->getUsersFirstMlmDbId($placements_user_id);


            MlmEarning::create([
                'user_id' => $placements_user_id,
                'mlm_db_id' => $placements_mlm_db_id,
                'type' => 5,
                'amount' => $real_upteam_income,
                'date' => $date,
                'time' => $time,
            ]);

            $placement_user = User::find($placements_user_id);
            $placement_user->total_upteam_earnings = $placement_user->total_upteam_earnings + $real_upteam_income;
            $placement_user->save();
            $this->creditWeeklyEarning($placements_user_id, $real_upteam_income, 'upteam');
           
        }
    }

    public function getIdsToCreditUpteam($user_id, $placement_num)
    {
        
        $arr = [];
        
        $users = User::where('id', '>', $user_id)->where('created',1)->paginate($placement_num,['id']);
        if($users->total() > 0){
            foreach($users as $user){
                $arr[] = $user->id;
            }
        }

        if(count($arr) < $placement_num){
            $admin_id = $this->getAdminId();
            $rem = $placement_num - count($arr);
            for($i = 0; $i < $rem; $i++){
                $arr[] = $admin_id;
            }
        }

        return $arr;
    }

    public function getEasyLoanAmtToShare(){
        return MlmCharge::find(16)->perc;
    }

    public function getEasyLoanStepsNum(){
        return MlmCharge::find(16)->amount;
    }
    
    public function getWithdrawalEarningAmt(){
        return MlmCharge::find(15)->amount;
    }

    public function getFundingEarningAmt(){
        return MlmCharge::find(14)->amount;
    }

    public function getVtuStepsNum(){
        return MlmCharge::find(13)->amount;
    }
    
    public function getDataAmtToShare(){
        return MlmCharge::find(12)->amount;
    }

    public function getAirtimeEarningPerc(){
        return MlmCharge::find(11)->perc;
    }

    public function getFirstAdminEarning(){
        return MlmCharge::find(10)->amount;
    }

    public function getUpteamNum(){
        return MlmCharge::find(5)->amount;
    }
    

    public function getAdminEarningPerc(){
        return MlmCharge::find(7)->perc;
    }

    public function getUpteamPerc(){
        return MlmCharge::find(5)->perc;
    }

    public function getFinalEarningPerc(){
        return MlmCharge::find(6)->perc;
    }

    public function getAirtimeAmt(){
        return MlmCharge::find(8)->amount;
    }

    public function getAmtToShare(){
        return MlmCharge::find(9)->amount;
    }

    public function getWeeklySubAmt(){
        return MlmCharge::find(2)->amount;
    }

    public function getPlacementNum(){
        return MlmCharge::find(4)->amount;
    }

    public function getPlacementNumResub(){
        return MlmCharge::find(18)->amount;
    }

    public function getPlacementPerc(){
        return MlmCharge::find(4)->perc;
    }

    public function getPlacementPercResub(){
        return MlmCharge::find(18)->perc;
    }

    public function getSponsorPerc(){
        return MlmCharge::find(3)->perc;
    }

    public function getSponsorPercResub(){
        return MlmCharge::find(17)->perc;
    }

    public function getSignUpAmount(){
        return MlmCharge::find(1)->amount;
    }

    public function performMlmRegistrationForFirstTimeUsersWithOutPlacement($user_id, $sponsor_user_id, $date, $time, $sign_up_amt)
    {

        // $sponsor_id = $this->getUserIdByName($sponsor_user_name);
        $sponsor_id = $this->getUsersFirstMlmDbId($sponsor_user_id);


        if ($this->checkIfMlmDbIdIsValid($sponsor_id)) {


            if ($this->registerUserInMlm3($user_id, $sponsor_id, $date, $time, 0, $sign_up_amt)) {
                return true;
            }
        }
    }

    public function registerUserInMlm3($user_id, $sponsor_id, $date, $time, $type, $sign_up_amt)
    {
        if ($this->fixUserInNextAvailableSpaceForMlm(1, $sponsor_id, $user_id, $date, $time, $sign_up_amt)) {
            
            $sponsor_user_id = $this->getMlmDbParamById("user_id", $sponsor_id);
            

            $this->creditUserSponsorIncome($user_id, $sponsor_user_id, $sign_up_amt, $sponsor_id, $date, $time);

            return true;
            
        }
    }

    


    public function creditWeeklyEarning($user_id, $amount,$type){
        $mlm_weekly = MlmWeekly::where('user_id', $user_id)->first();
        if(is_null($mlm_weekly)){
            // $date_time = date("YYYY-MM-DD HH:MM:SS");
            $date_time = date("Y-m-d H:i:s");
            

            MlmWeekly::create([
                'user_id' => $user_id,
                'week_start' => $date_time,
                'total_' . $type => $amount,
            ]);
        }else{
            $real_type = 'total_' . $type;
            $curr_amt = $mlm_weekly->$real_type;
            $mlm_weekly->$real_type = $curr_amt + $amount;
            $mlm_weekly->save();
        }
    }

    public function creditUserSponsorIncome($user_id, $sponsor_id,$amount, $mlm_db_id,$date,$time,$resub = false)
    {
        if($resub){
            $sponsor_perc = $this->getSponsorPercResub();
        }else{
            $sponsor_perc = $this->getSponsorPerc();
        }

        $sponsor_income = ($sponsor_perc / 100) * $amount;

        MlmEarning::create([
            'user_id' => $sponsor_id,
            'mlm_db_id' => $mlm_db_id,
            'type' => 3,
            'amount' => $sponsor_income,
            'date' => $date,
            'time' => $time,
        ]);

        $this->creditWeeklyEarning($sponsor_id, $sponsor_income,'sponsor');
        // $notif = new Notif();
        // $sponsored_business_partner_id = $user_id;
        // $sponsored_business_partner_slug = $sponsored_business_partner_id;
        // $sponsored_business_partner_name = $this->getUserParamById("name", $sponsored_business_partner_id);
        // $sponsored_business_partner_phone_code = $this->getUserParamById("phone_code", $sponsored_business_partner_id);
        // $sponsored_business_partner_phone_num = $this->getUserParamById("phone", $sponsored_business_partner_id);
        // $sponsored_business_partner_phone_num =  $sponsored_business_partner_phone_code . "" . $sponsored_business_partner_phone_num;

        // $notif->title = "Credit Alert";
        // $notif->message = "This Is To Alert You That You Were Credited With Sponsor Income. View Details Below.";
        // $notif->message .= "<div class='container' style='margin-top: 30px;'>";
        // $notif->message .= "<p>Sponsor Income Amount: <em class='text-primary'>" . number_format($sponsor_income, 2) . "</em><p>";
        // $notif->message .= "<h4 class='text-center' style='margin-top: 20px;'>Newly Sponsored  Partner Details<h4>";
        // $notif->message .= "<p>Full Name: <a target='_blank' href='/user/" . $sponsored_business_partner_slug . "'>" . $sponsored_business_partner_name . "</a><p>";

        // $notif->message .= "</div>";

        // $notif->save();
            
        
    }


    

    public function getPositioningOfMlmUserDirect($stage, $sponsor_id)
    {

        $query = DB::table('mlm_db')->where('stage', $stage)->where('under', $sponsor_id)->get();
        if ($query->count() == 1) {
            $query[0]->positioning;
        } else {
            return false;
        }
    }

    public function getPositioningOfImmediateChildOfThisUser($parent_id)
    {
        $query = DB::table('mlm_db')->where('under', $parent_id)->get();
        if ($query->count() == 1) {
            return $query[0]->positioning;
        }
    }

    public function getNumberOfImmediateChildrenOfThisUser($parent_id)
    {

        $query = DB::table('mlm_db')->where('under', $parent_id)->get();
        return $query->count();
    }

    public function checkIfThisUserHasHisNextLevelFull($parent_id)
    {
        
        $query = DB::table('mlm_db')->where("under", $parent_id)->orderBy("id", "ASC")->get("id");
        if ($query->count() >= 2) {
            return true;
        } else {
            return false;
        }
    }

    public function getIdsOfChildren($current_array)
    {
        $ret_arr = array();
        if (is_array($current_array)) {
            for ($i = 0; $i < count($current_array); $i++) {
                $id = $current_array[$i];

                $query = DB::table('mlm_db')->where('under', $id)->get();
                if ($query->count() > 0) {
                    foreach ($query as $row) {
                        $id1 = $row->id;
                        $ret_arr[] = $id1;
                    }
                }
            }
        }
        return $ret_arr;
    }

    public function getChildrenIdsOfParent($sponsors_first_mlm_db_id)
    {
        $ret_arr = array();
        // $query = $this->db->get_where('mlm_db',array('under' => $sponsors_first_mlm_db_id));
        // $this->db->select("id");
        // $this->db->from("mlm_db");
        // $this->db->where("under",$sponsors_first_mlm_db_id);
        // $this->db->order_by("id","ASC");
        // $query = $this->db->get();
        $query = DB::table('mlm_db')->where("under", $sponsors_first_mlm_db_id)->orderBy("id", "ASC")->get('id');
        if ($query->count() > 0) {
            foreach ($query as $row) {
                $id = $row->id;
                $ret_arr[] = $id;
            }
        }
        return $ret_arr;
    }

    public function fixUserInNextAvailableSpaceForMlm($type,$sponsor_id, $user_id, $date, $time, $sign_up_amt)
    {
        //If Type Is Next Available space

        // echo $sponsor_id;
        //Get Sponsors First Mlm Db Id
        if ($type == 0) {
            $sponsors_first_mlm_db_id = $this->getUsersFirstMlmDbId($sponsor_id);
        } else {
            $sponsors_first_mlm_db_id = $sponsor_id;
        }
        // echo $sponsors_first_mlm_db_id . "<br>";
        //Get The Stage Here
        $sponsors_first_mlm_db_stage = $this->getMlmDbParamById("stage", $sponsors_first_mlm_db_id);
        // echo $sponsors_first_mlm_db_stage;
        // echo $sponsors_first_mlm_db_stage;
        //Get The First Generation Under Sponsor Thats Empty

        $query = DB::table('mlm_db')->where('under', $sponsors_first_mlm_db_id)->get();
        $number_under_him = $query->count();
        //If First Level Under Him Is Full
        if ($number_under_him == 2) {
            $i = 1;
            $current_array = array();
            while (true) {
                $i++;
                // echo $i;

                $previous_stage = $i - 1;
                $stage_to_check_for = $sponsors_first_mlm_db_stage + $previous_stage;
                $current_stage = $sponsors_first_mlm_db_stage + $i;



                if ($i == 2) {
                    $parents_ids = $this->getChildrenIdsOfParent($sponsors_first_mlm_db_id);
                    // print_r($parents_ids);
                } else {
                    $parents_ids = $this->getIdsOfChildren($current_array);
                }
                // var_dump($parents_ids);
                if (is_array($parents_ids)) {
                    $current_array = $parents_ids;
                    for ($j = 0; $j < count($parents_ids); $j++) {
                        $parent_id = $parents_ids[$j];
                        if (!$this->checkIfThisUserHasHisNextLevelFull($parent_id)) {

                            $parents_children_num = $this->getNumberOfImmediateChildrenOfThisUser($parent_id);
                            // echo $parents_children_num;
                            if ($parents_children_num == 0) {
                                $positioning = "left";
                            } else if ($parents_children_num == 1) {
                                $other_users_positioning = $this->getPositioningOfImmediateChildOfThisUser($parent_id);
                                if ($other_users_positioning == "right") {
                                    $positioning = "left";
                                } else {
                                    $positioning = "right";
                                }
                            }

                            $mlm_db = MlmDb::create([
                                'user_id' => $user_id,

                                'sponsor' => $sponsors_first_mlm_db_id,
                                'under' => $parent_id,
                                'stage' => $current_stage,
                                'positioning' => $positioning,
                                'date_created' => $date,
                                'time_created' => $time,
                            ]);

                            $mlm_db_id = $mlm_db->id;


                            $placement_num = $this->getPlacementNum();
                            $placement_perc = $this->getPlacementPerc();


                            $placement_income = ($placement_perc / 100) * $sign_up_amt;

                            $this->creditUserPlacementIncome($mlm_db_id, $placement_income, $placement_num, $date, $time);
                            return true;

                            break 2;
                        }
                    }
                }
            }
        } else { //If Not
            $new_stage = $sponsors_first_mlm_db_stage + 1;
            if ($number_under_him == 1) {
                $other_users_positioning = $this->getPositioningOfMlmUserDirect($new_stage, $sponsors_first_mlm_db_id);
                if ($other_users_positioning == "right") {
                    $positioning = "left";
                } else {
                    $positioning = "right";
                }
            } else {
                $positioning = "left";
            }

            $mlm_db = MlmDb::create([
                'user_id' => $user_id,

                'sponsor' => $sponsors_first_mlm_db_id,
                'under' => $sponsors_first_mlm_db_id,
                'stage' => $new_stage,
                'positioning' => $positioning,
                'date_created' => $date,
                'time_created' => $time,
            ]);

            $mlm_db_id = $mlm_db->id;


            $placement_num = $this->getPlacementNum();
            $placement_perc = $this->getPlacementPerc();


            $placement_income = ($placement_perc / 100) * $sign_up_amt;

            $this->creditUserPlacementIncome($mlm_db_id, $placement_income, $placement_num, $date, $time);

            return true;
        }
    }

    public function addMlmIncomeHistory($history_array)
    {
        return DB::table('mlm_income_history')->insert($history_array);
    }

    public function sendMessage($form_array)
    {
        Notif::create($form_array);
        return true;
        // return DB::table('notif')->insert($form_array);
    }
    public function getUserNameById($id)
    {
        $query = DB::table('users')->where('id', $id)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $user_name = $row->user_name;
            }
            // echo $user_name;
            return $user_name;
        } else {
            return "";
        }
    }

    public function updateUserTable($user_array, $id)
    {
        try {
            DB::table('users')->where('id', $id)->update($user_array);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUserParamById($param, $user_id)
    {

        $query = DB::table('users')->where('id', $user_id)->select($param)->get();
        if ($query->count() == 1) {
            return $query[0]->$param;
        } else {
            return false;
        }
    }

    public function getAdminId()
    {

        $query = DB::table('users')->where('is_admin', 1)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $id = $row->id;
            }
            return $id;
        }
    }

    public function getIdsToCreditPlacement($mlm_db_id, $placement_num)
    {
        $ret_arr = array();
        for ($i = 1; $i <= $placement_num; $i++) {

            $query = DB::table('mlm_db')->where('id', $mlm_db_id)->get();
            if ($query->count() == 1) {
                foreach ($query as $row) {
                    $under = $row->under;
                    if (!is_null($under)) {
                        $user_id = $this->getMlmDbParamById("user_id", $under);
                        // $this->getIdsToCreditPlacement($under);
                        $mlm_db_id = $under;
                        $ret_arr[] = array(
                            'mlm_db_id' => $under,
                            'user_id' => $user_id
                        );
                    } else {
                        $ret_arr[] =  array(
                            'mlm_db_id' => 1,
                            'user_id' => $this->getAdminId()
                        );
                    }
                }
            }
        }
        return $ret_arr;
    }

    
    public function creditUserPlacementIncome($mlm_db_id, $placement_income, $placement_num, $date, $time)
    {

        $creditors_user_id = $this->getMlmDbParamById("user_id", $mlm_db_id);
        $ids_to_credit = $this->getIdsToCreditPlacement($mlm_db_id, $placement_num);
        $ids_to_credit_num = count($ids_to_credit);
        for ($i = 0; $i < count($ids_to_credit); $i++) {
            $user_id = $ids_to_credit[$i]['user_id'];
            $placements_mlm_db_id = $ids_to_credit[$i]['mlm_db_id'];


            MlmEarning::create([
                'user_id' => $user_id,
                'mlm_db_id' => $placements_mlm_db_id,
                'type' => 4,
                'amount' => $placement_income,
                'date' => $date, 
                'time' => $time,
            ]);

            $this->creditWeeklyEarning($user_id, $placement_income, 'placement');
            // $notif = new Notif();
            
            // $sponsored_business_partner_id = $creditors_user_id;
            
            
            // $sponsored_business_partner_slug = $sponsored_business_partner_id;
            // $sponsored_business_partner_name = $this->getUserParamById("name", $sponsored_business_partner_id);
            // $sponsored_business_partner_phone_code = $this->getUserParamById("phone_code", $sponsored_business_partner_id);
            // $sponsored_business_partner_phone_num = $this->getUserParamById("phone", $sponsored_business_partner_id);
            // $sponsored_business_partner_phone_num = $sponsored_business_partner_phone_code . "" . $sponsored_business_partner_phone_num;

            // $notif->title = "Credit Alert";
            // $notif->message = "This Is To Alert You That You Were Credited With Placement Income. View Details Below.";
            // $notif->message .= "<div class='container' style='margin-top: 30px;'>";
            // $notif->message .= "<p>Placement Income Amount: <em class='text-primary'>" . number_format($placement_income, 2) . "</em><p>";


            // $notif->message .= "<h4 class='text-center' style='margin-top: 20px;'>Newly Placement Partner Details<h4>";
            // $notif->message .= "<p>Full Name: <a target='_blank' href='/user/" . $sponsored_business_partner_slug . "'>" . $sponsored_business_partner_name . "</a><p>";


            // $notif->message .= "</div>";

            // $notif->save();

           
        }
    }


    public function fixUserInPositionMlm($sponsor_id, $placement_id, $positioning, $user_id, $date, $time, $sign_up_amt)
    {
        if ($this->checkIfThisPlacementPositionIsAvailable($placement_id, $positioning)) {
            $placement_stage =  $this->getMlmDbParamById("stage", $placement_id);
            $new_stage = $placement_stage + 1;

            $mlm_db = MlmDb::create([
                'user_id' => $user_id,
                'sponsor' => $sponsor_id,
                'under' => $placement_id,
                'stage' => $new_stage,
                'positioning' => $positioning,
                'date_created' => $date,
                'time_created' => $time,
            ]);

            $mlm_db_id = $mlm_db->id;

            $placement_num = $this->getPlacementNum();
            $placement_perc = $this->getPlacementPerc();

            
            $placement_income = ($placement_perc / 100) * $sign_up_amt;

            $this->creditUserPlacementIncome($mlm_db_id, $placement_income, $placement_num, $date, $time);
            return true;
        } else {
            if ($this->fixUserInNextAvailableSpaceForMlm(0,$sponsor_id, $user_id, $date, $time, $sign_up_amt)) {
                return true;
            }
        }
    }

    public function registerUserInMlm2($user_id, $sponsor_id, $sign_up_amt, $placement_id, $positioning, $date, $time)
    {
        if ($this->fixUserInPositionMlm($sponsor_id, $placement_id, $positioning, $user_id, $date, $time, $sign_up_amt)) {
            
            $sponsor_user_id = $this->getMlmDbParamById("user_id", $sponsor_id);
            $this->creditUserSponsorIncome($user_id, $sponsor_user_id, $sign_up_amt, $sponsor_id, $date, $time);

            return true;
            
        }
    } 

    public function checkIfThisPlacementPositionIsAvailable($placement_id, $positioning)
    {
        $query = DB::table('mlm_db')->where('under', $placement_id)->where('positioning', $positioning)->get();
        if ($query->count() == 1) {
            return false;
        } else {
            return true;
        }
    }

    public function getUsersFirstMlmDbId($user_id)
    {
        $query = DB::table('mlm_db')->where("user_id", $user_id)->orderBy("id", "ASC")->limit(1)->get();
        if ($query->count() == 1) {
            return $query[0]->id;
        }
    }

    public function performMlmRegistrationForFirstTimeUsersWithPlacement($user_id, $sponsor_user_id, $sign_up_amt, $placement_mlm_db_id, $placement_position, $date, $time)
    {

        $sponsor_id = $this->getUsersFirstMlmDbId($sponsor_user_id);

        if ($placement_position == "left" || $placement_position == "right") {
            if ($this->checkIfMlmDbIdIsValid($sponsor_id) && $this->checkIfMlmDbIdIsValid($placement_mlm_db_id)) {

                if ($placement_position == "left") {
                    $next_available_position = "right";
                } else {
                    $next_available_position = "left";
                }



                if ($this->checkIfThisPlacementPositionIsAvailable($placement_mlm_db_id, $placement_position)) {

                    if ($this->registerUserInMlm2($user_id, $sponsor_id, $sign_up_amt, $placement_mlm_db_id, $placement_position, $date, $time)) {

                        return true;
                    }
                } else if ($this->checkIfThisPlacementPositionIsAvailable($placement_mlm_db_id, $next_available_position)) {
                    if ($this->registerUserInMlm2($user_id, $sponsor_id, $sign_up_amt, $placement_mlm_db_id, $next_available_position, $date, $time)) {
                        // echo "string";
                        return true;
                    }
                } else {
                    if ($this->registerUserInMlm3($user_id, $sponsor_id, $date, $time, 0, $sign_up_amt)) {
                        return true;
                    }
                }
            }
        }
    }

    public function checkIfUserAlreadyHasAnMlmAccountInTheSystem($user_id)
    {

        $query = DB::table('mlm_db')->where('user_id', $user_id)->get();
        if ($query->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getAvailablePositionUnderMlmDbId($mlm_db_id)
    {
        if ($this->checkIfMlmDbIdIsValid($mlm_db_id)) {

            $query = DB::table('mlm_db')->where('under', $mlm_db_id)->get();
            if ($query->count() == 0) {
                return "both";
            } else if ($query->count() == 1) {
                $taken_id = $this->getChildrenOfParent($mlm_db_id)[0]->id;
                $taken_position = $this->getMlmDbParamById("positioning", $taken_id);
                if ($taken_position == "left") {
                    return "right";
                } else {
                    return "left";
                }
            } else {
                return false;
            }
        }
    }

    public function getMlmDbParamById($param, $mlm_db_id)
    {

        $query = DB::table('mlm_db')->where('id', $mlm_db_id)->select($param)->get();
        if ($query->count() == 1) {
            return $query[0]->$param;
        }
    }

    public function getChildrenOfParent($mlm_db_id)
    {

        $query = DB::table('mlm_db')->where("under", $mlm_db_id)->orderBy("id", "ASC")->get();
        if ($query->count() > 0) {
            return $query;
        } else {
            return false;
        }
    }

    public function checkIfMlmDbIdIsValid($mlm_db_id)
    {

        $query = DB::table('mlm_db')->where('id', $mlm_db_id)->get();
        if ($query->count() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfMlmDbIdHasNoAvailablePositionUnderHim($mlm_db_id)
    {

        $query = DB::table('mlm_db')->where('under', $mlm_db_id)->get();
        if ($query->count() == 2) {
            return true;
        } else {
            return false;
        }
    }

    public function addPaymentProofRequest($form_array)
    {
        return DB::table('credit_account_payment_proofs')->insert($form_array);
    }

    public function updateProvidusTransactionBySettlementId($form_array, $settlementId)
    {
        // return $this->db->update('providus_transactions',$form_array,array('settlementId' => $settlementId));
        try {
            DB::table('providus_transactions')->where('settlementId', $settlementId)->update($form_array);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function creditUsersRegistrationAmount($user_id, $amount)
    {
        $query = DB::table('users')->where('id', $user_id)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $registration_amt_paid = $row->registration_amt_paid;
            }
            $new_total_income = $registration_amt_paid + $amount;
            try {
                DB::table('users')->where('id', $user_id)->update(array('registration_amt_paid' => $new_total_income));
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function debitUser($user_id, $amount, $summary = "")
    {
        $date = date("j M Y");
        $time = date("h:i:sa");

        $query = DB::table('users')->where('id', $user_id)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $withdrawn = $row->withdrawn;
                $total_income = $row->total_income;
            }
            $wallet_balance = $total_income - $withdrawn;
            $new_withdrawn = $withdrawn + $amount;
            $amount_after = $wallet_balance - $amount;
            // $query = $this->db->update('users',array('withdrawn' => $new_withdrawn),array('id' => $user_id));
            try {

                DB::table('users')->where('id', $user_id)->update(array('withdrawn' => $new_withdrawn));

                if (DB::table('account_statement')->insert(array('user_id' => $user_id, 'amount' => $amount, 'amount_before' => $wallet_balance, 'amount_after' => $amount_after, 'summary' => $summary, 'date' => $date, 'time' => $time))) {
                    // $this->runTheMainCheckAndDebitingMnthlyServiceCharge($user_id);
                    return true;
                }
            } catch (Exception $e) {
                return false;
            }
        }
    }

    public function creditUser($user_id, $amount, $summary = "")
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $date_time = $date . " " . $time;

        $query = DB::table('users')->where('id', $user_id)->get();
        if ($query->count() == 1) {
            foreach ($query as $row) {
                $total_income = $row->total_income;
                $withdrawn = $row->withdrawn;
                $created = $row->created;
            }
            if ($created == 1) {
                $wallet_balance = $total_income - $withdrawn;
                $new_total_income = $total_income + $amount;
                $amount_after = $wallet_balance + $amount;

                try {
                    DB::table('users')->where('id', $user_id)->update(array('total_income' => $new_total_income));

                    

                    AccountStatement::create([
                        'user_id' => $user_id,
                        'amount' => $amount,
                        'amount_before' => $wallet_balance, 
                        'amount_after' => $amount_after, 
                        'summary' => $summary, 
                        'date' => $date, 
                        'time' => $time
                    ]);

                    // $this->runTheMainCheckAndDebitingMnthlyServiceCharge($user_id);
                    // $query = DB::table('loan_advances')->where("user_id", $user_id)->orderBy("id", "DESC")->limit(1)->get();
                    $pending_loan = EasyLoan::where('user_id', $user_id)->where('paid_off', 0)->whereDate('created_at', '<=', Carbon::now()->subDays(28))->first();
                    // dd($pending_loan);

                    if ($pending_loan) {

                        $id = $pending_loan->id;
                        $created_at = $pending_loan->created_at;
                        $loan_amount = $pending_loan->amount;
                        $amount_paid = $pending_loan->amount_paid;

                        $start_date = $pending_loan->created_at;
                        $start_date = date("j M Y", strtotime($start_date));
                        $start_date = strtotime($start_date);
                        $end_date = strtotime(date("j M Y"));



                        $date_diff = ($end_date - $start_date) / 60 / 60 / 24;

                        $weeks_num = $date_diff / 7;

                        $weeks_num = (int) $weeks_num;
                        $charged_weeks = $weeks_num - 4;
                        if ($charged_weeks < 0) {
                            $charged_weeks = 0;
                        }
                        $total_service_charge = 250 * $charged_weeks;

                        $total_amount_payable = $loan_amount + $total_service_charge;
                        $total_amount_owed = $total_amount_payable - $amount_paid;
                        

                        if ($total_amount_owed > 0) {
                            if ($amount > $total_amount_owed) {
                                $amount_to_debit = $total_amount_owed;
                            } else if ($amount < $total_amount_owed) {
                                $amount_to_debit = $amount;
                            } else if ($amount == $total_amount_owed) {
                                $amount_to_debit = $total_amount_owed;
                            }

                            // dd($amount_to_debit);
                            

                            $amount_paid += $amount_to_debit;
                            $balance = $total_amount_payable - $amount_paid;
                            

                            
                            $summary = "Easy Loan Repayment. Balance: " . number_format(($balance), 2);

                            if ($this->debitUser($user_id, $amount_to_debit, $summary)) {
                                $pending_loan->amount_paid = $amount_paid;
                                $pending_loan->last_date_paid = date("Y-m-d H:i:s");
                                $pending_loan->paid_off = $balance == 0 ? 1 : 0;

                                $pending_loan->save();
                                return true;
                                
                            }
                            
                        } else {
                            return true;
                        }
                        
                    } else {
                        return true;
                    }
                    
                } catch (Exception $e) {
                    return false;
                }
            } else {
                if ($this->creditUsersRegistrationAmount($user_id, $amount)) {
                    return true;
                }
            }
        }
    }

    public function isDecimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

    public function getUserInfoByUserProvidusAccountNumber($providus_account_number)
    {
        // $query = $this->db->get_where('users',array('providus_account_number' => $providus_account_number));
        $query = DB::table('users')->where('providus_account_number', $providus_account_number)->get();
        if ($query->count() == 1) {
            return $query;
        } else {
            return false;
        }
    }

    public function addProvidusTransactionRecord($form_array)
    {

        return DB::table('providus_transactions')->insert($form_array);
    }

    public function checkIfThisProvidusWebhookIsDuplicate($settlementId)
    {

        $query = DB::table('providus_transactions')->where('settlementId', $settlementId)->get();
        if ($query->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfThisProvidusAccountNumberIsValid($accountNumber)
    {

        $query = DB::table('users')->where('providus_account_number', $accountNumber)->get('id');
        if ($query->count() == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function addMinifyAccountWebhookJsonData($json_post, $date, $time)
    {
        return DB::table('test_table')->insert(array('test' => $json_post, 'date' => $date, 'time' => $time));
        
    }

    public function providusVtuCurl($url, $use_post, $post_data = [])
    {
        $client_id = env('PROVIDUS_CLIENTID');
        $client_secret = env('PROVIDUS_CLIENTSECRET');
        $auth_signature = hash("sha512", $client_id . ":" . $client_secret);
        
        // $client_id = "RDMtTSMjdF9HMTBCQGw=";
        $headers = [
            'X-Auth-Signature' => $auth_signature,
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'Client-Id' => $client_id
        ];
        if (!$use_post) {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        } else {
            $response = Http::withOptions([
                'http_errors' => false,
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }

    public function getPhoneCodeByCountry($country){
        $phone_code = "";
        $country_code = $country->code;
        $url = "https://restcountries.com/v3.1/alpha/" . $country_code;
        $use_post = false;


        $more_country_info = $this->httpCurl($url, $use_post);
        // return $more_country_info;

        if ($this->isJson($more_country_info)) {
            $more_country_info = json_decode($more_country_info);



            if (is_array($more_country_info)) {

                $idd = $more_country_info[0]->idd;
                $phone_code = ($country->id != 230) ? $idd->root . '' . $idd->suffixes[0] : '+1';
            }
        }

        return $phone_code;
    }

    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function httpCurl($url, $use_post, $post_data = [])
    {
        // return $post_data;
        
        $headers = [
            // 'Authorization' => 'Bearer ' . $bearer_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        if (!$use_post) {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->get($url);
        } else {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders($headers)->post($url, $post_data);
        }
        return $response;
    }
}