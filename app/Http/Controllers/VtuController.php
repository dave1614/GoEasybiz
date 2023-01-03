<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use App\Models\Country;
use App\Models\User;
use App\Models\VtuTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VtuController extends Controller
{
    public UsefulFunctions $functions;
    public $CLUB_USERID;
    public $CLUB_APIKEY;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
        $this->CLUB_USERID = env("CLUB_USERID");
        $this->CLUB_APIKEY = env("CLUB_APIKEY");
    }

    public function trackReloadlyVtuOrder(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;

        $response_arr = ['success' => false, 'messages' => ''];
        if (isset($request->show_records) && isset($request->order_id)) {
            $order_id = $request->order_id;
            $order_id_cut = substr($order_id, 0, 2);

            if ($order_id_cut == "RE") {
                $order_id = substr($order_id, 2);
            }


            
            $url = "https://topups.reloadly.com/topups/". $order_id."/status";
            $use_post = false;

            $accept = 'application/com.reloadly.topups-v1+json';

            $response = $this->functions->reloadlyCurl($url, $use_post, $post_data = [], $accept);
            // return $response;

            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    if ($response->status == "SUCCESSFUL") {


                        // var_dump($response->message->details);
                        
                        $response_arr['success'] = true;




                        $recipientPhone = $response->transaction->recipientPhone;
                        $amount = $response->transaction->requestedAmount;
                        $network = $response->transaction->operatorName;
                        





                        $response_arr['messages'] .= 'Transaction Status: <em class="text-primary">SUCCESSFUL</em><br>';


                       

                        // $response_arr['messages'] .= 'Amount: <em class="text-primary">' . number_format($amount,2) . '</em><br>';

                        $response_arr['messages'] .= 'Description: <em class="text-primary">Airtime worth '.$amount.' bought on '. $recipientPhone.' '. $network .' </em><br>';
                    }
                }
            }
        }


        return back()->with('data', $response_arr);
    }

    
    public function reloadlyRechargeRequest(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'insuffecient_funds' => false, 'order_id' => '', 'invalid_amount' => false, 'invalid_recipient' => false];

        $validationRules = [
            'amount' => 'required|numeric|min:100|max:50000',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);


        $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
        // echo $user_total_amount;
        // echo $amount;
        $amount = $request->amount;
        if ($amount <= $user_total_amount) {

            
            $phone = $request->phone_number;
            
            $amount_to_debit_user = $amount;

            $phone_code = $user->phone_code;
            
            $country = Country::find($user->country_id);
            // return $country;
            $country_id = $country->id;
            $countryCode = $country->code;
            $operatorId = $this->functions->getReloadlyOperatorId($phone_code, $phone, $country_id);
            
            $useLocalAmount = false;
            $phone_number = $this->functions->returnInterNumber($phone_code, $phone);


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

            $response = $this->functions->reloadlyCurl($url, $use_post, $post_data, $accept);
            
            // return $response;


            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    if (isset($response->status)) {
                        if ($response->status && $response->transactionId) {
                            $summary = "Debit Of " . $amount . " For Airtime Recharge";
                            if ($this->functions->debitUser($user_id, $amount, $summary)) {

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
                                if ($this->functions->addTransactionStatus($form_array)) {
                                    $response_arr['success'] = true;
                                    $response_arr['order_id'] = $order_id;
                                }
                            }
                        }
                    }
                }
            }
             
        } else {
            $response_arr['insuffecient_funds'] = true;
        }


        return back()->with('data', $response_arr);
    }
   

    public function buyEminenceEducationalVoucherVtu(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'epins' => '', 'invalid_amount' => false, 'invalid_recipient' => false];

        $url = "https://app.eminencesub.com/api/result-checker";
        $use_post = false;

        $response = $this->functions->eminenceVtuCurl($url, $use_post);

        // return $data;
        // return $response;
        if ($this->functions->isJson($response)) {
            $response = json_decode($response);
            // var_dump($response);
            if (isset($response->data)) {
                if ($response->code == 200) {

                    $voucher_types = $response->data;

                    $voucher_arr = [];
                    foreach ($voucher_types as $row) {
                        $voucher_name = $row->name;
                        $voucher_price = $row->price;

                        $voucher_arr[] = $voucher_name;

                        if ($voucher_name == $request->voucher_type) {
                            $price = $voucher_price + 250;
                        }
                    }

                    $voucher_str = implode(",", $voucher_arr);

                    // return $voucher_str;
                    // return $price;

                    $validationRules = [
                        'voucher_type' => 'required|in:' . $voucher_str,
                        // 'quantity' => 'required|numeric|min:1|max:20',

                    ];

                    $messages = [];

                    $request->validate($validationRules);


                    
                    $type = $request->voucher_type;
                    $quantity = 1;

                    $amount_to_debit_user = $price * $quantity;
                    // return $amount_to_debit_user;



                    $response_arr['amount_to_debit_user'] = $amount_to_debit_user;

                    $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);



                    if ($amount_to_debit_user <= $user_total_amount) {

                        $url = "https://app.eminencesub.com/api/buy-result-checker";

                        $use_post = true;
                        $data = [
                            'type' => $type,

                        ];

                        // return $data;

                        $response = $this->functions->eminenceVtuCurl($url, $use_post, $data);
                        // $response_arr['response'] = $response;
                        // return $response;
                        if ($this->functions->isJson($response)) {
                            $response = json_decode($response);
                            if (is_object($response)) {
                                $code = $response->code;
                                $message = $response->message;

                                if ($code == 201) {



                                    $summary = "Debit Of " . $amount_to_debit_user . " For Vtu " . $type . " E-Pin Generation";
                                    if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                        // $epin = $this->meetglobal_model->generateUnusedEpinForThisNetworkAnAmount($code,$amount);
                                        $order_id = 'TC' . $response->data->reference;
                                        $pin = $response->data->pin;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'e-pin',
                                            'sub_type' => 'educational_voucher_epin',

                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount_to_debit_user,
                                            'order_id' => $order_id
                                        );
                                        if ($this->functions->addTransactionStatus($form_array)) {
                                            $response_arr['success'] = true;
                                            $response_arr['epins'] = $pin;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $response_arr['insuffecient_funds'] = true;
                    }
                    
                }
            }
        }

       
        return back()->with('data',$response_arr);
        
    }

    public function showEducationalVouchersPage(Request $request){
        $user = Auth::user();

        $props['user'] = $user;

        $url = "https://app.eminencesub.com/api/result-checker";
        $use_post = false;

        $response = $this->functions->eminenceVtuCurl($url, $use_post);

        // return $data;
        // return $response;
        if ($this->functions->isJson($response)) {
            $response = json_decode($response);
            // var_dump($response);
            if (isset($response->data)) {
                if ($response->code == 200) {
                    $props['voucher_types'] = $response->data;

                    $i = 0;

                    foreach ($props['voucher_types'] as $row) {

                        $name = $row->name;
                        $price = $row->price;

                        if ($name == "waec") {
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/west-african-examinations-council-waec-logo.jpg";
                            $row->price = $price + 250;
                        } else if ($name == "neco") {
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/neco.png";
                            $row->price = $price + 250;
                        } else if ($name == "nabteb") {
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/nabteb.png";
                            $row->price = $price + 250;
                        } else if ($name == "nbais") {
                            $i++;
                            $row->index = $i;
                            $row->image = "/images/nbais.jpg";
                            $row->price = $price + 250;
                        }
                    }
                    // return $props['voucher_types'];
                    return Inertia::render('Vtu/EducationalVouchers', $props);
                }
            }
        }

        
    }

    public function trackPayscribeEducationalEpin(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;

        $response_arr = ['success' => false, 'messages' => ''];
        if (isset($request->show_records) && isset($request->order_id)) {
            $order_id = $request->order_id;



            $url = "https://www.payscribe.ng/api/v1/epins/retrieve?trans_id=" . $order_id;
            $use_post = false;

            $response = $this->functions->payscribeVtuCurl($url, $use_post, $post_data = []);


            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                $response_arr['response'] = $response;
                // var_dump($response);
                if ($response->status && $response->status_code == 200) {

                    $details = $response->message->details;

                    if (is_array($details)) {


                        $response_arr['success'] = true;

                        $j = 0;
                        $response_arr['messages'] .= "<div class='container'>";
                        for ($i = 0; $i < count($details); $i++) {
                            $j++;
                            $pin = $details[$i]->pin;
                            $date_purchased = $details[$i]->date_purchased;
                            $name = $details[$i]->name;
                            $response_arr['messages'] .= "<div style='margin-bottom: 15px;'>";
                            $response_arr['messages'] .= "<p><b>NAME: </b>" . $name . "</p>";
                            $response_arr['messages'] .= "<p><b>PIN: </b>" . $pin . "</p>";
                            $response_arr['messages'] .= "<p><b>Date Purchased: </b>" . $date_purchased . "</p>";
                            // $response_arr['messages'] .= "<p class='col-6'>".$code."</p>";
                            $response_arr['messages'] .= "</div>";
                        }

                        $response_arr['messages'] .= "</div>";
                    }
                }
            }
        }


        return back()->with('data',$response_arr);
    }

    public function trackPayscribeVtuEpin(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;

        $response_arr = ['success' => false, 'messages' => '', 'amount' => '', 'epins_json' => ''];
        if (isset($request->show_records) && isset($request->order_id)) {
            $order_id = $request->order_id;



            $url = "https://www.payscribe.ng/api/v1/rechargecardpins?trans_id=" . $order_id;
            $use_post = false;

            $response = $this->functions->payscribeVtuCurl($url, $use_post, $post_data = []);


            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                // var_dump($response);
                if ($response->status && $response->status_code == 200) {

                    $details = $response->message->details;
                    $amount = $response->message->amount;

                    if (is_array($details)) {


                        $response_arr['success'] = true;

                        $j = 0;
                        $response_arr['messages'] .= "<div class='container'>";
                        for ($i = 0; $i < count($details); $i++) {
                            $j++;
                            $pin = $details[$i]->pin;
                            $code = $details[$i]->code;
                            $response_arr['messages'] .= "<div class='row' style='margin-bottom: 5px;'>";
                            $response_arr['messages'] .= "<p class='col-1'>" . $j . "</p>";
                            $response_arr['messages'] .= "<p class='col-11'>" . $pin . "</p>";
                            // $response_arr['messages'] .= "<p class='col-6'>".$code."</p>";
                            $response_arr['messages'] .= "</div>";
                        }

                        $response_arr['messages'] .= "</div>";
                        $response_arr['epins_json'] = json_encode($details);
                        $response_arr['amount'] = $amount;
                    }
                }
            }
        }


        return back()->with('data',$response_arr);
    }


    public function trackPayscribeVtuOrderData(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);
        
        $user_id = $user->id;
        
        $response_arr = ['success' => false,'messages' => ''];
        if(isset($request->show_records) && isset($request->order_id)){
            $order_id = $request->order_id;
            
            
            
            $url = "https://www.payscribe.ng/api/v1/report?trans_id=".$order_id;
            $use_post = true;

            $response = $this->functions->payscribeVtuCurl($url,$use_post,$post_data = []);
            // return $response;

            if($this->functions->isJson($response)){
                $response = json_decode($response);
                if(is_object($response)){
                    if($response->status){
                            
                        // var_dump($response->message->details);
                        $details = $response->message->details;
                        $response_arr['success'] = true;

                        $description = $details->description;
                        
                        
                        $amount = $details->amount;
                        $date_initiated = $details->date_initiated;
                        $transaction_status = $details->transaction_status;
                        $amount_paid = $this->functions->getVtuTransactionParamByOrderId("amount",$order_id);
                        $refunded_status = $this->functions->getVtuTransactionParamByOrderId("refunded",$order_id);
                        $table_id = $this->functions->getVtuTransactionParamByOrderId("id",$order_id);



                        // if($transaction_status == "refunded"){
                        //  if($refunded_status == 0){
                        //      $type = $this->meetglobal_model->getVtuTransactionParamByOrderId("type",$order_id);
                        //      $commision_amount = $this->meetglobal_model->getCommissionAmountPaidToUser($type,$amount_paid);
                        //      $summary = "VTU Transaction Refund For ORDER ID: ". $order_id;
                        //      if($this->meetglobal_model->creditUser($user_id,$amount_paid,$summary)){
                        //          $form_array = array(
                        //              "refunded" => 1
                        //          );
                        //          $this->meetglobal_model->updateVtuTable($form_array,$table_id);
                        //      }
                        //  }
                        // }

                        $response_arr['messages'] .= 'Transaction Status: <em class="text-primary">' . $transaction_status . '</em><br>';
                        $data = null;
                        if(isset($details->data)){
                            $data = $details->data;
                        }
                        

                        if(!is_null($data)){
                        
                            $type = $this->functions->getVtuTransactionParamByOrderId("type",$order_id);
                            if($type == "electricity"){
                                if(isset($data->MeterType)){
                                    $MeterType = $data->MeterType;
                                    $response_arr['messages'] .= 'Meter Type: <em class="text-primary">' . $MeterType . '</em><br>';
                                    if($MeterType == "prepaid"){
                                        if(isset($data->Token)){
                                            $Token = $data->Token;
                                            $response_arr['messages'] .= 'Meter Token: <em class="text-primary">' . $Token . '</em><br>';
                                        }
                                    }
                                }
                                
                            }else if($type == "data"){

                            }
                        }

                        
                        // $response_arr['messages'] .= 'Amount: <em class="text-primary">' . number_format($amount,2) . '</em><br>';
                        $response_arr['messages'] .= 'Date Initiated: <em class="text-primary">' .$date_initiated . '</em><br>';
                        $response_arr['messages'] .= 'Description: <em class="text-primary">' . $description . '</em><br>';
                        
                        
                    }
                }
            }   

            
            
        }
            

        
        return back()->with('data',$response_arr);
        
    }


    public function trackEminenceVtuOrder(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;

        $response_arr = ['success' => false, 'messages' => ''];
        if (isset($request->show_records) && isset($request->order_id)) {
            $order_id = $request->order_id;
            $order_id_cut = substr($order_id, 0, 2);

            if ($order_id_cut == "TT" || $order_id_cut == "TC") {
                $order_id = substr($order_id, 2);
            }


            $url = "https://app.eminencesub.com/api/show-transaction/" . $order_id;
            $use_post = false;

            $response = $this->functions->eminenceVtuCurl($url, $use_post, $post_data = []);
            // return $response;

            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    if ($response->status == 200) {


                        // var_dump($response->message->details);
                        $details = $response->data;
                        $response_arr['success'] = true;

                        $description = $details->description;


                        $amount = $details->amount;
                        $description = $details->description;
                        $transaction_status = $details->status;
                        $amount_paid = $this->functions->getVtuTransactionParamByOrderId("amount", $order_id);
                        $refunded_status = $this->functions->getVtuTransactionParamByOrderId("refunded", $order_id);
                        $table_id = $this->functions->getVtuTransactionParamByOrderId("id", $order_id);





                        $response_arr['messages'] .= 'Transaction Status: <em class="text-primary">' . $transaction_status . '</em><br>';


                        if ($order_id_cut == "TC") {
                            $pin = $details->pin;

                            $response_arr['messages'] .= 'Pin: <em class="text-primary">' . $pin . '</em><br>';
                        }

                        // $response_arr['messages'] .= 'Amount: <em class="text-primary">' . number_format($amount,2) . '</em><br>';

                        $response_arr['messages'] .= 'Description: <em class="text-primary">' . $description . '</em><br>';
                    }
                }
            }
        }


        return back()->with('data', $response_arr);
    }

    public function trackClubVtuOrder(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;

        $response_arr = ['success' => false, 'messages' => ''];
        if (isset($request->show_records) && isset($request->order_id)) {
            $order_id = $request->order_id;

            $url = "https://www.nellobytesystems.com/APIQueryV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&OrderID=" . $order_id;
            $use_post = true;

            $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);


            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    // var_dump($response->message->details);

                    $response_arr['success'] = true;
                    $description = "";
                    $date_initiated = "";
                    $status = "";
                    $remark = "";

                    if (isset($response->ordertype)) {
                        $description = $response->ordertype;
                    }

                    if (isset($response->date)) {
                        $date_initiated = $response->date;
                    }

                    if (isset($response->status)) {
                        $status = $response->status;
                    }

                    if (isset($response->remark)) {
                        $remark = $response->remark;
                    }



                    $vtu_type = $this->functions->getVtuTransactionParamByOrderId("type", $order_id);

                    // echo $this->main_model->getVtuTransactionParamByOrderId("refunded",$order_id);

                    if (($status == "ORDER_CANCELLED" || $status == "ORDER_REFUNDED" || $status == "refunded") && $this->functions->getVtuTransactionParamByOrderId("refunded", $order_id) == 0) {
                        if ($vtu_type != "cable tv") {
                            // $this->main_model->refundFundsAndMarkAsRefunded($order_id);
                        }
                    }

                    // $type = $this->main_model->getVtuTransactionParamByOrderId("type",$order_id);
                    // if($type == "data"){
                    //  $amount = round((0.04 * $amount) + $amount,2);
                    // }

                    $response_arr['messages'] .= 'Transaction Status: <em class="text-primary">' . $status . '</em><br>';
                    // $response_arr['messages'] .= 'Amount: <em class="text-primary">' . number_format($amount,2) . '</em><br>';
                    $response_arr['messages'] .= 'Date Initiated: <em class="text-primary">' . $date_initiated . '</em><br>';
                    $response_arr['messages'] .= 'Order Type: <em class="text-primary">' . $description . '</em><br>';
                    $response_arr['messages'] .= 'Remark: <em class="text-primary">' . $remark . '</em>';
                }
            }
        }


        return back()->with('data',$response_arr);
        
    }

    public function userVtuHistoryPage(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

        


        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $user_id = $user->id;
        $history = VtuTransaction::where('user_id', $user_id);


        $history = $request->query('type') ? $history->where('type', 'like', '%' . $request->query('type') . '%') : $history;
        $history = $request->query('sub_type') ? $history->where('sub_type', 'like', '%' . $request->query('sub_type') . '%') : $history;
        $history = $request->query('order_id') ? $history->where('order_id', 'like', '%' . $request->query('order_id') . '%') : $history;
        $history = $request->query('number') ? $history->where('number', 'like', '%' . $request->query('number') . '%') : $history;



        $history = $request->query('amount') ? $history->where('amount', 'like', '%' . $request->query('amount') . '%') : $history;

        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();



        $props['history'] = $history;
        $props['length'] = $length;
        
        $props['type'] = $request->query('type') ? $request->query('type') : NULL;
        $props['sub_type'] = $request->query('sub_type') ? $request->query('sub_type') : NULL;
        $props['order_id'] = $request->query('order_id') ? $request->query('order_id') : NULL;

        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;


        // return $history;


        return Inertia::render("Vtu/VTUHistory", $props);
    }

    public function rechargeRouter(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);
        
        $user_id = $user->id;
        
            
        
        $response_arr = ['success' => false,'messages' => '','insuffecient_funds' => false];
        if(isset($request->selected_plan)){
            $validationRules = [
                'router_service' => 'required|in:smile',
                'router_number' => 'required|numeric',
                
            ];

            $messages = [];

            $request->validate($validationRules);

            $type = $request->router_service;
            $router_number = $request->router_number;

            $vtu_platform = $this->functions->getVtuPlatformToUse('router',$type);
            if($vtu_platform == "payscribe"){ 
                                
                $name = $request->selected_plan['name'];
                $amount = $request->selected_plan['amount'];
                
                $product_code = $request->productCode;
                $code = $request->selected_plan['code'];
                
                $phone = $user->phone;
                $vend_type = "subscription";

                $amount_to_debit_user = $amount;

                            
                if($type == "smile"){
                    

                    $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                    // echo $user_total_amount;
                    // echo $amount;

                    if($amount_to_debit_user <= $user_total_amount){

                        if($type == "smile"){
                            $real_type = "smile";
                            $url = "https://www.payscribe.ng/api/v1/internet/vend";

                            $data = [
                                'service' => 'smile',
                                'vend_type' => $vend_type,
                                'code' => $code,
                                'phone' => $phone,
                                'productCode' => $product_code
                            ];

                            // echo(json_encode($post_data));
                        }

                        $use_post = true;
                        

                        $response = $this->functions->payscribeVtuCurl($url,$use_post,$data);


                        if($this->functions->isJson($response)){


                            $response = json_decode($response);
                            // var_dump($response);

                            if($response->status && $response->status_code == 200 ){
                                

                                $trans_id = $response->message->details->trans_id;
                                
                                $summary = "Debit Of " . $amount_to_debit_user . " For " . $real_type . " Router Recharge";
                                if($this->functions->debitUser($user_id,$amount_to_debit_user,$summary)){
                                    
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'router',
                                        'sub_type' => $real_type,
                                        'number' => $router_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount,
                                        'order_id' => $trans_id
                                    );
                                    if($this->functions->addTransactionStatus($form_array)){
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $trans_id;
                                    }
                                }
                            }
                        }
                    }else{
                        $response_arr['insuffecient_funds'] = true;
                    }
                        
                    
                }
            }else{
                
                
                
                // $phone = "0" . $this->data['phone'];
                $phone = $user->phone;

                $plan = $request->selected_plan['package_id'];
                        

                $package_amount = $this->functions->getPackageAmountForSmileClub("smile",$plan);
                // return $package_amount;
                // echo is_numeric($package_amount);
                if(is_numeric($package_amount)){

                    $amount = $package_amount;

                    $amount_to_debit_user = $amount;
                    $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                    // echo $user_total_amount;

                    // echo $amount;

                    if($amount_to_debit_user <= $user_total_amount){

                        // $url = "https://www.nellobytesystems.com/APICableTVV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&CableTV=".$operator."&Package=".$plan."&SmartCardNo=".$decoder_number;

                        $url = "https://www.nellobytesystems.com/APISmileV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&MobileNetwork=smile-direct&datatplan=".$plan."&MobileNumber=".$router_number."";
                        // return $url;
                        $use_post = true;
                        

                        $response = $this->functions->vtu_curl($url,$use_post,$post_data=[]);
                        // return $response;
                        if($this->functions->isJson($response)){
                            $response = json_decode($response);
                            
                            if(is_object($response)){
                                if($response->status == "ORDER_RECEIVED"){
                                    $real_type = "smile";
                                    $summary = "Debit Of " . $amount_to_debit_user . " For " . $real_type . " Router Recharge";
                                    if($this->functions->debitUser($user_id,$amount_to_debit_user,$summary)){
                                        $order_id = $response->transactionid;
                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'router',
                                            'sub_type' => $real_type,
                                            'number' => $router_number,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount,
                                            'order_id' => $order_id
                                        );
                                        if($this->functions->addTransactionStatus($form_array)){
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                        }
                                    }
                                    
                                }
                            }
                        }
                    }
                        
                }else{
                    $response_arr['insuffecient_funds'] = true;
                }
        
            }
                           
            
        }

        return back()->with('data',$response_arr);
        
    }

    public function loadRouterBundlesAndVerifyNumber(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;


        $response_arr = ['success' => false, 'router_plans' => '', 'incorrect_number' => false, 'customer_name' => '', 'productCode' => '', 'platform' => ''];

        $validationRules = [
            'router_service' => 'required|in:smile',
            'router_number' => 'required|numeric',

        ];  

        $request->validate($validationRules);


        
        $type = $request->router_service;
        $router_number = $request->router_number;

        $vtu_platform = $this->functions->getVtuPlatformToUse('router', $type);
        if ($vtu_platform == "payscribe") {
            $response_arr['platform'] = 'payscribe';
            if ($type == "smile") {
                $real_type = "smile";
                $url = "https://www.payscribe.ng/api/v1/internet/bundles";
            }

            $use_post = true;
            $data = [
                'type' => $real_type,
                'account' => $router_number
            ];

            $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);


            if ($this->functions->isJson($response)) {

                $response = json_decode($response);
                // var_dump($response);

                if ($response->status && $response->status_code == 200 && isset($response->message->details)) {

                    $plans = $response->message->details->bundles;
                    $customer_name = $response->message->details->customer_name;
                    $productCode = $response->message->details->productCode;
                    $response_arr['customer_name'] = $customer_name;
                    $response_arr['productCode'] = $productCode;

                    if (is_array($plans)) {
                        $response_arr['type'] = $real_type;
                        $response_arr['success'] = true;

                        $index = 0;
                        $router_plans = array();

                        for ($i = 0; $i < count($plans); $i++) {

                            $index++;
                            $name = $plans[$i]->name;
                            $code = $plans[$i]->code;
                            $amount = $plans[$i]->amount;
                            $validity = $plans[$i]->validity;
                            $router_plans[] = array(
                                'index' => $index,
                                'name' => $name,
                                'code' => $code,
                                'amount' => $amount,
                                'validity' => $validity
                            );
                        }

                        $response_arr['router_plans'] = $router_plans;
                    }
                }
                // else if($response->description == "There was an error validating SMILE please try again later."){
                else {
                    $response_arr['incorrect_number'] = true;
                }
            }
        } else {
            $response_arr['platform'] = 'club';
            if ($type == "smile") {
                $real_type = "smile";
                $url = "https://www.nellobytesystems.com/APIVerifySmileV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&MobileNetwork=smile-direct&MobileNumber=" . $router_number;
            }

            $use_post = true;
            $data = [
                'type' => $real_type,
                'account' => $router_number
            ];

            $use_post = true;


            $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);

            // return $response;
            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                // var_dump($response);
                if (is_object($response)) {
                    if (isset($response->content->Customer_Name)) {
                        if ($response->content->Customer_Name != "" && $response->content->Customer_Name != "INVALID_SMARTCARDNO") {

                            $customer_name = $response->content->Customer_Name;

                            $response_arr['customer_name'] = $customer_name;


                            // if(is_array($plans)){
                            //     $response_arr['type'] = $real_type;
                            //     $response_arr['success'] = true;

                            //     $index = 0;
                            //     $router_plans = array();

                            //     for($i = 0; $i < count($plans); $i++){

                            //         $index++;
                            //         $name = $plans[$i]->name;
                            //         $code = $plans[$i]->code;
                            //         $amount = $plans[$i]->amount;
                            //         $validity = $plans[$i]->validity;
                            //         $router_plans[] = array(
                            //             'index' => $index,
                            //             'name' => $name,
                            //             'code' => $code,
                            //             'amount' => $amount,
                            //             'validity' => $validity
                            //         );

                            //     }

                            //     $response_arr['router_plans'] = $router_plans;
                            // }

                            $url = "https://www.nellobytesystems.com/APISmilePackagesV2.asp";
                            //                  // echo $url;
                            $use_post = true;

                            $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);

                            if ($this->functions->isJson($response)) {
                                $response = json_decode($response);
                                // var_dump($response);
                                if (is_object($response)) {
                                    // return $response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT;
                                    if (isset($response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT)) {
                                        $response_arr['success'] = true;
                                        $index = 0;
                                        $new_arr = array();


                                        $rows = $response->MOBILE_NETWORK->{'smile-direct'}[0]->PRODUCT;
                                        for ($i = 0; $i < count($rows); $i++) {
                                            $index++;

                                            $package_id = $rows[$i]->PACKAGE_ID;
                                            $package_name = $rows[$i]->PACKAGE_NAME;
                                            $package_amount = $rows[$i]->PACKAGE_AMOUNT;



                                            $new_arr[$i]['index'] = $index;
                                            $new_arr[$i]['package_id'] = $package_id;
                                            $new_arr[$i]['name'] = $package_name;
                                            $new_arr[$i]['amount'] = number_format($package_amount);
                                            $new_arr[$i]['type'] = $type;
                                        }

                                        $response_arr['router_plans'] = $new_arr;
                                    }
                                }
                            }
                        } else {
                            $response_arr['incorrect_number'] = true;
                        }
                    } else {
                        $response_arr['incorrect_number'] = true;
                    }
                }
            }
        }
        
        
        return back()->with('data',$response_arr);
        
    }

    public function showRouterRechargePage(Request $request){
        $user = Auth::user();

        $props['user'] = $user;

        return Inertia::render('Vtu/RouterRecharge',$props);
    }

    public function processAirtimeToWalletTransfer(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        $response_arr = ['success' => false, 'messages' => '', 'network' => ''];

        $validationRules = [
            'network' => 'required|in:mtn,glo,airtel,9mobile',
            'amount' => 'required|numeric|min:1000|max:20000',
            'phone_number' => 'required|numeric|digits_between:5,15',
            'from' => 'required|numeric|digits_between:5,15',

        ];

        $request->validate($validationRules);



        $type = $request->network;
        $phone_number = $request->from;
        $amount = $request->amount;
        $from = $request->phone_number;


        if ($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile") {




            $url = "https://www.payscribe.ng/api/v1/airtime_to_wallet/vend";
            $use_post = true;

            if ($type == "glo") {
                $network = "GLO";

                $network_2 = "glo";
                $perc_charge = 5;
            } else if ($type == "airtel") {
                $network = "AIRTEL";

                $network_2 = "airtel";
                $perc_charge = 5;
            } else if ($type == "9mobile") {
                $network = "9MOBILE";
                $network_2 = "9mobile";
                $perc_charge = 5;
            } else if ($type == "mtn") {
                $network = "MTN";

                $network_2 = "mtn";
                $perc_charge = 5;
            }


            $response_arr['network'] = $network_2;

            $data = [
                'network' => $network_2,
                'phone_number' => $phone_number,
                'amount' => $amount,
                'from' => $from
            ];


            // return $data;
            $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);


            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                // var_dump($response);
                if (is_object($response)) {
                    if ($response->status && $response->status_code == 200) {


                        $order_id = $response->message->trans_id;
                        $form_array = array(
                            'user_id' => $user_id,
                            'type' => 'airtime_to_wallet',
                            'sub_type' => $network_2,
                            'number' => $from,
                            'date' => $date,
                            'time' => $time,
                            'amount' => $amount,
                            'order_id' => $order_id
                        );
                        if ($this->functions->addTransactionStatus($form_array)) {
                            $response_arr['success'] = true;
                            $response_arr['order_id'] = $order_id;
                        }
                    }
                }
            }
        }
        
        return back()->with('data',$response_arr);
        
    }

    public function validateAirtimeToWalletDetails(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        $response_arr = ['success' => false];

        $validationRules = [
            'network' => 'required|in:mtn,glo,airtel,9mobile',
            'amount' => 'required|numeric|min:1000|max:20000',
            'phone_number' => 'required|numeric|digits_between:5,15',

        ];

        

        $request->validate($validationRules);


        
        $response_arr['success'] = true;
        
        
        return back()->with('data', $response_arr);
        
    }

    public function getChargeForAirtimeToWalletTransfer(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        $response_arr = ['success' => false, 'messages' => '', 'network' => ''];


        if (isset($request->network)) {
            $type = $request->network;

            if ($type == "mtn" || $type == "glo" || $type == "airtel" || $type == "9mobile") {


                $url = "https://www.payscribe.ng/api/v1/airtime_to_wallet";
                $use_post = false;

                if ($type == "glo") {
                    $network = "GLO";

                    $network_2 = "glo";
                    $perc_charge = 5;
                } else if ($type == "airtel") {
                    $network = "AIRTEL";

                    $network_2 = "airtel";
                    $perc_charge = 5;
                } else if ($type == "9mobile") {
                    $network = "9MOBILE";
                    $network_2 = "9mobile";
                    $perc_charge = 5;
                } else if ($type == "mtn") {
                    $network = "MTN";

                    $network_2 = "mtn";
                    $perc_charge = 5;
                }


                $response_arr['network'] = $network_2;


                $response = $this->functions->payscribeVtuCurl($url, $use_post);


                if ($this->functions->isJson($response)) {
                    $response = json_decode($response);
                    // var_dump($response);
                    if (is_object($response)) {
                        if ($response->status && $response->status_code == 200) {

                            $response_arr['network'] = $network_2;
                            // var_dump($response->message->details);

                            $details = $response->message->details;


                            if (is_array($details)) {


                                $j = 0;

                                for ($i = 0; $i < count($details); $i++) {
                                    $instruction = $details[$i]->instruction;
                                    $transfer_code = $details[$i]->transfer_code;
                                    $network_name = $details[$i]->network_name;
                                    $status = $details[$i]->status;
                                    $phone_number = $details[$i]->phone_number;
                                    $rate = $details[$i]->rate;


                                    if ($status && $network_name == $network_2) {
                                        $response_arr['success'] = true;
                                        $charge = 100 - $rate;
                                        $charge = $charge - $perc_charge;
                                        $response_arr['charge'] = $charge;
                                        $response_arr['phone_number'] = $phone_number;
                                        $response_arr['transfer_code'] = $transfer_code;
                                        $response_arr['instruction'] = $instruction;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
   
        return back()->with('data',$response_arr);
        
    }

    public function showAirtimeToWalletPage(Request $request){
        $user = Auth::user();

        $props['user'] = $user;

        return Inertia::render('Vtu/AirtimeToWallet',$props);
    }

    public function purchaseCableTvPlan(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'insuffecient_funds' => false, 'order_id' => '', 'invalid_no' => false, 'error_message' => '', 'transaction_pending' => false];


        // return $vtu_platform;
        if (isset($request->operator) && isset($request->decoder_number) && isset($request->selected_plan)) {
            $operator = $request->operator;
            $decoder_number = $request->decoder_number;
            $selected_plan = $request->selected_plan;

            if ($operator == "dstv" || $operator == "gotv" || $operator == "startimes") {
                $vtu_platform = $this->functions->getVtuPlatformToUse('cable', $operator);
                if ($vtu_platform == "payscribe") {

                    if (isset($request->productCode) && isset($request->productToken)) {


                        $productCode = $request->productCode;
                        $productToken = $request->productToken;
                        // $phone = "0" . $this->data['phone'];
                        $phone = $user->phone;




                        if ($operator == "dstv" || $operator == "gotv") {
                            $plan = $selected_plan['package_id'];
                            $url = "https://www.payscribe.ng/api/v1/multichoice/validate";
                            $use_post = true;
                            $data = array(
                                'type' => $operator,
                                'account' => $decoder_number
                            );
                            $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);

                            if ($this->functions->isJson($response)) {
                                $response = json_decode($response);

                                if ($response->status == true && $response->status_code == 200) {
                                    if (isset($response->message->details->customer_name)) {
                                        $customer_name = $response->message->details->customer_name;
                                        $bouquets = $response->message->details->bouquets;
                                        // $productCode = $response->message->details->productCode;
                                        if (is_array($bouquets)) {


                                            for ($i = 0; $i < count($bouquets); $i++) {
                                                $package_id = $bouquets[$i]->plan;
                                                $package_name = $bouquets[$i]->name;
                                                $package_amount = $bouquets[$i]->amount;

                                                if ($package_id == $plan) {
                                                    $amount_to_debit_user = $package_amount;
                                                    $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                                                    // echo $user_total_amount;
                                                    // echo $amount;

                                                    if ($amount_to_debit_user <= $user_total_amount) {

                                                        $url = "https://www.payscribe.ng/api/v1/multichoice/vend";
                                                        $use_post = true;

                                                        // $transaction_id = "PS" . mt_rand(10000000, 99999999);

                                                        $data = array(
                                                            'plan' => $plan,
                                                            'productCode' => $productCode,
                                                            'phone' => $phone,
                                                            'productToken' => $productToken
                                                        );


                                                        $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);

                                                        if ($this->functions->isJson($response)) {
                                                            $response = json_decode($response);
                                                            // var_dump($response);
                                                            if (is_object($response)) {

                                                                $status = $response->status;
                                                                $status_code = $response->status_code;

                                                                if ($status == true && $status_code == 200) {
                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                                    if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {

                                                                        $order_id = $response->message->details->trans_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'cable',
                                                                            'sub_type' => $operator,
                                                                            'number' => $decoder_number,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );

                                                                        if ($this->functions->addTransactionStatus($form_array)) {
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }
                                                                } else if ($status == true && $status_code == 201) {
                                                                    $response_arr['transaction_pending'] = true;
                                                                    $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                                    if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {

                                                                        $order_id = $response->message->details->trans_id;
                                                                        $form_array = array(
                                                                            'user_id' => $user_id,
                                                                            'type' => 'cable',
                                                                            'sub_type' => $operator,
                                                                            'number' => $decoder_number,
                                                                            'date' => $date,
                                                                            'time' => $time,
                                                                            'amount' => $amount_to_debit_user,
                                                                            'order_id' => $order_id
                                                                        );

                                                                        if ($this->functions->addTransactionStatus($form_array)) {
                                                                            $response_arr['success'] = true;
                                                                            $response_arr['order_id'] = $order_id;
                                                                        }
                                                                    }
                                                                } else {
                                                                    if (isset($response->message->description)) {
                                                                        if ($response->message->description != "") {
                                                                            $response_arr['error_message'] = $response->message->description;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $response_arr['insuffecient_funds'] = true;
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $response_arr['invalid_no'] = true;
                                    }
                                }
                            }
                        } else if ($operator == "startimes") {

                            // echo "string";

                            if (isset($selected_plan['cycle'])) {
                                $cycle = $selected_plan['cycle'];
                                $package_name = $selected_plan['package_name'];
                                $amount = $selected_plan['amount'];


                                $url = "https://www.payscribe.ng/api/v1/startimes/validate";
                                $use_post = true;
                                $post_data = array(
                                    'account' => $decoder_number
                                );
                                $response = $this->functions->payscribeVtuCurl($url, $use_post, $post_data);
                                // var_dump($response);
                                if ($this->functions->isJson($response)) {
                                    $response = json_decode($response);

                                    if ($response->status == true && $response->status_code == 200) {
                                        if (isset($response->message->details->customer_name)) {
                                            $customer_name = $response->message->details->customer_name;
                                            $bouquets = $response->message->details->bouquets;
                                            // $productCode = $response->message->details->productCode;

                                            $amount_to_debit_user = $amount;
                                            $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                                            // echo $user_total_amount;
                                            // echo $amount;

                                            if ($amount_to_debit_user <= $user_total_amount) {

                                                $url = "https://www.payscribe.ng/api/v1/startimes/vend";
                                                $use_post = true;

                                                // $reference_id = "PS" . mt_rand(10000000, 99999999);

                                                $post_data = array(
                                                    'plan' => $package_name,
                                                    'cycle' => $cycle,
                                                    'productCode' => $productCode,
                                                    'phone' => $phone,
                                                    'productToken' => $productToken
                                                );

                                                // echo json_encode($post_data);

                                                $response = $this->functions->payscribeVtuCurl($url, $use_post, $post_data);
                                                // var_dump($response);

                                                if ($this->functions->isJson($response)) {
                                                    $response = json_decode($response);
                                                    // var_dump($response);
                                                    if (is_object($response)) {
                                                        $status = $response->status;
                                                        $status_code = $response->status_code;

                                                        if ($status == true && $status_code == 200) {
                                                            $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                            if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {

                                                                $order_id = $response->message->details->trans_id;
                                                                $form_array = array(
                                                                    'user_id' => $user_id,
                                                                    'type' => 'cable',
                                                                    'sub_type' => $operator,
                                                                    'number' => $decoder_number,
                                                                    'date' => $date,
                                                                    'time' => $time,
                                                                    'amount' => $amount_to_debit_user,
                                                                    'order_id' => $order_id
                                                                );

                                                                if ($this->functions->addTransactionStatus($form_array)) {
                                                                    $response_arr['success'] = true;
                                                                    $response_arr['order_id'] = $order_id;
                                                                }
                                                            }
                                                        } else if ($status == true && $status_code == 201) {
                                                            $response_arr['transaction_pending'] = true;
                                                            $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                            if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {

                                                                $order_id = $response->message->details->trans_id;
                                                                $form_array = array(
                                                                    'user_id' => $user_id,
                                                                    'type' => 'cable',
                                                                    'sub_type' => $operator,
                                                                    'number' => $decoder_number,
                                                                    'date' => $date,
                                                                    'time' => $time,
                                                                    'amount' => $amount_to_debit_user,
                                                                    'order_id' => $order_id
                                                                );

                                                                if ($this->functions->addTransactionStatus($form_array)) {
                                                                    $response_arr['success'] = true;
                                                                    $response_arr['order_id'] = $order_id;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                $response_arr['insuffecient_funds'] = true;
                                            }
                                        } else {
                                            $response_arr['invalid_no'] = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {

                    if ($operator == "dstv") {
                        $cable_type = "01";
                        $club_type = "DStv";
                    } else if ($operator == "gotv") {
                        $cable_type = "02";
                        $club_type = "GOtv";
                    } else if ($operator == "startimes") {
                        $cable_type = "03";
                        $club_type = "Startimes";
                    }
                    // $phone = "0" . $this->data['phone'];
                    $phone = $user->phone;





                    $plan = $selected_plan['package_id'];
                    $url = "https://www.nellobytesystems.com/APIVerifyCableTVV1.0.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&CableTV=" . $cable_type . "&SmartCardNo=" . $decoder_number;
                    // return $url;
                    $use_post = true;


                    $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);

                    
                    if ($this->functions->isJson($response)) {
                        $response = json_decode($response);
                        // var_dump($response);
                        if (is_object($response)) {
                            if (isset($response->customer_name)) {
                                if ($response->customer_name != "" && $response->customer_name != "INVALID_SMARTCARDNO") {


                                    $package_amount = $this->functions->getPackageAmountForCableTvClub($club_type, $plan);
                                    // return $package_amount;
                                    // echo is_numeric($package_amount);
                                    if (is_numeric($package_amount)) {

                                        $amount = $package_amount;

                                        $amount_to_debit_user = $amount;
                                        $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                                        // echo $user_total_amount;

                                        // echo $amount;

                                        if ($amount_to_debit_user <= $user_total_amount) {

                                            $url = "https://www.nellobytesystems.com/APICableTVV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&CableTV=" . $operator . "&Package=" . $plan . "&SmartCardNo=" . $decoder_number;
                                            // return $url;
                                            $use_post = true;


                                            $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);
                                            // return $response;
                                            if ($this->functions->isJson($response)) {
                                                $response = json_decode($response);

                                                if (is_object($response)) {
                                                    if ($response->status == "ORDER_RECEIVED") {
                                                        $summary = "Debit Of " . $amount_to_debit_user . " For CableTV Recharge";
                                                        if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                                            $order_id = $response->transactionid;

                                                            $form_array = array(
                                                                'user_id' => $user_id,
                                                                'type' => 'cable',
                                                                'sub_type' => $operator,
                                                                'number' => $decoder_number,
                                                                'date' => $date,
                                                                'time' => $time,
                                                                'amount' => $amount_to_debit_user,
                                                                'order_id' => $order_id
                                                            );

                                                            if ($this->functions->addTransactionStatus($form_array)) {
                                                                $response_arr['success'] = true;
                                                                $response_arr['order_id'] = $order_id;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $response_arr['insuffecient_funds'] = true;
                                    }
                                }
                            } else {
                                $response_arr['invalid_no'] = true;
                            }
                        }
                    }
                }
            }
        }

        return back()->with('data',$response_arr);
        
    }

    public function validateDecoderNumberCablePlans(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);
        
        $user_id = $user->id;
        $response_arr = ['success' => false,'messages' => '','customer_name' => '','invalid_user' => false,'cable_plans' => '','platform' => ''];

        $validationRules = [
            'operator' => 'required|in:dstv,gotv,startimes',
            'decoder_number' => 'required|numeric|digits_between:5,15'
            
        ];

        $request->validate($validationRules);

        
        
        $operator = $request->operator;
        $decoder_number = $request->decoder_number;

        $vtu_platform = $this->functions->getVtuPlatformToUse('cable',$operator);
        // return $vtu_platform;

        if($vtu_platform == "payscribe"){
            

            if($operator == "dstv" || $operator == "gotv"){
                if($operator == "dstv"){
                    $type = "dstv";
                }else if($operator == "gotv"){
                    $type = "gotv";
                }

                

                $url = "https://www.payscribe.ng/api/v1/multichoice/validate";
                $use_post = true;
                $data = array(
                    'type' => $type,
                    'account' => $decoder_number
                );
                $response = $this->functions->payscribeVtuCurl($url,$use_post,$data);
                

                if($this->functions->isJson($response)){
                    $response = json_decode($response);
                    if($response->status == true && $response->status_code == 200){
                        if(isset($response->message->details->customer_name)){
                            $customer_name = $response->message->details->customer_name;
                            $bouquets = $response->message->details->bouquets;
                            $productCode = $response->message->details->productCode;
                            $productToken = $response->message->details->productToken;
                            if(is_array($bouquets)){
                                $response_arr['success'] = true;
                                $response_arr['platform'] = 'payscribe';
                                $response_arr['customer_name'] = $customer_name;
                                $response_arr['productCode'] = $productCode;
                                $response_arr['productToken'] = $productToken;

                                
                                
                                $index = 0;
                                $new_arr = array();
                                

                                
                                for($i = 0; $i < count($bouquets); $i++){
                                    $index++;

                                    $package_id = $bouquets[$i]->plan;
                                    $package_name = $bouquets[$i]->name;
                                    $package_amount = $bouquets[$i]->amount;

                                    $new_arr[$i]['index'] = $index;
                                    $new_arr[$i]['package_id'] = $package_id;
                                    $new_arr[$i]['name'] = $package_name;
                                    $new_arr[$i]['amount'] = number_format($package_amount);
                                    $new_arr[$i]['type'] = $type;
                                    

                                }

                                $response_arr['cable_plans'] = $new_arr;

                            }
                        }else if(isset($response->message->description)){
                            $response_arr['invalid_user'] = true;
                        }
                    }
                }   

            }else if($operator == "startimes"){
                $response_arr = array('success' => false,'messages' => '','customer_name' => '','invalid_user' => false);
                $type = "STARTIMES";
                
                $url = "https://www.payscribe.ng/api/v1/startimes/validate";
                $use_post = true;
                $data = array(
                    'account' => $decoder_number
                );
                $response = $this->functions->payscribeVtuCurl($url,$use_post,$data);
                // return $response;
                if($this->functions->isJson($response)){
                    $response = json_decode($response);
                    if($response->status == true && $response->status_code == 200){
                        if(isset($response->message->details->customer_name)){
                            $customer_name = $response->message->details->customer_name;
                            $bouquets = $response->message->details->bouquets;
                            $productCode = $response->message->details->productCode;
                            $productToken = $response->message->details->productToken;
                            if(is_array($bouquets)){
                                $response_arr['success'] = true;
                                $response_arr['platform'] = 'payscribe';
                                $response_arr['customer_name'] = $customer_name;
                                $response_arr['productCode'] = $productCode;
                                $response_arr['productToken'] = $productToken;

                                $index = 0;
                                    
                                for($i = 0; $i < count($bouquets); $i++){
                                    $index++;

                                    
                                    $package_name = $bouquets[$i]->name;
                                    $cycles = $bouquets[$i]->cycles;
                                    $bouquets[$i]->index = $index;
                                    $bouquets[$i]->package_id = $package_name;
                                }
                                $response_arr['cable_plans'] = $bouquets;
                            }
                        }else if(isset($response->message->description)){
                            $response_arr['invalid_user'] = true;
                        }
                    }
                }   
            }
        }else{
            $response_arr['platform'] = "club";
            if($operator == "dstv" || $operator == "gotv" || $operator == "startimes"){
                
                if($operator == "dstv"){
                    $cable_type = "01";
                    $club_type = "DStv";
                }else if($operator == "gotv"){
                    $cable_type = "02";
                    $club_type = "GOtv";
                }else if($operator == "startimes"){
                    $cable_type = "03";
                    $club_type = "Startimes";
                }

                $url = "https://www.nellobytesystems.com/APIVerifyCableTVV1.0.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&CableTV=".$cable_type."&SmartCardNo=".$decoder_number;
                    // echo $url;
                $use_post = true;

                
                $response = $this->functions->vtu_curl($url,$use_post,$post_data=[]);

                // return $response;
                if($this->functions->isJson($response)){
                    $response = json_decode($response);
                        // var_dump($response);
                    if(is_object($response)){
                        if(isset($response->customer_name)){
                            if($response->customer_name != "" && $response->customer_name != "INVALID_SMARTCARDNO"){
                                
                                $response_arr['customer_name'] = $response->customer_name;

                                $url = "https://www.nellobytesystems.com/APICableTVPackagesV2.asp";
                    //                  // echo $url;
                                $use_post = true;

                                $response = $this->functions->vtu_curl($url,$use_post,$post_data=[]);

                                if($this->functions->isJson($response)){
                                    $response = json_decode($response);
                                    // var_dump($response);
                                    if(is_object($response)){
                                        if(isset($response->TV_ID->$club_type)){
                                            $response_arr['success'] = true;
                                            $index = 0;
                                            $new_arr = array();
                                            

                                            $rows = $response->TV_ID->$club_type[0]->PRODUCT;
                                            for($i = 0; $i < count($rows); $i++){
                                                $index++;

                                                $package_id = $rows[$i]->PACKAGE_ID;
                                                $package_name = $rows[$i]->PACKAGE_NAME;
                                                $package_amount = $rows[$i]->PACKAGE_AMOUNT;



                                                $new_arr[$i]['index'] = $index;
                                                $new_arr[$i]['package_id'] = $package_id;
                                                $new_arr[$i]['name'] = $package_name;
                                                $new_arr[$i]['amount'] = number_format($package_amount);
                                                $new_arr[$i]['type'] = $operator;
                                                
                                            }

                                            $response_arr['cable_plans'] = $new_arr;
                                                    
                                        }
                                    }
                                }
                            }else{
                                $response_arr['invalid_user'] = true;
                            }
                        }
                    }
                }
            }

        }
        
        
        return back()->with('data',$response_arr);
        
    }

    public function loadCableTvPage(Request $request){
        $user = Auth::user();
        $props['user'] = $user;

        return Inertia::render('Vtu/CableTV',$props);
    }

    public function purchaseElectricityWithBuypower(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'order_id' => '', 'invalid_meterno' => false, 'meter_type_not_available' => false, 'metertoken' => '', 'transaction_pending' => false];

        $validationRules = [
            'disco' => 'required|in:eko,ikeja,abuja,ibadan,enugu,phc,kano,kaduna,jos',
            'meter_type' => 'required|in:prepaid,postpaid',
            'meter_number' => 'required|numeric|digits_between:5,15',
            'amount' => 'required|numeric|min:100|max:50000',
            'mobile_number' => 'required|numeric|digits_between:5,15',
            'email' => 'required',

        ];

        $request->validate($validationRules);

        
        $disco = $request->disco;
        $meter_type = $request->meter_type;
        $meter_number = $request->meter_number;
        $amount = $request->amount;
        $mobile_number = $request->mobile_number;
        $email = $request->email;
        $payscribe_disco = "";
        $phone_number = $mobile_number;
        $meter_no = $meter_number;

        if ($disco == "eko") {
            $disco_code = "EKO";
            $payscribe_disco = "ekedc";
        } else if ($disco == "ikeja") {
            $disco_code = "IKEJA";
            $payscribe_disco = "ikedc";
        } else if ($disco == "abuja") {
            $disco_code = "ABUJA";
            $payscribe_disco = "aedc";
        } else if ($disco == "ibadan") {
            $disco_code = "IBADAN";
            $payscribe_disco = "ibedc";
        } else if ($disco == "enugu") {
            $disco_code = "ENUGU";
            $payscribe_disco = "eedc";
        } else if ($disco == "phc") {
            $disco_code = "PH";
            $payscribe_disco = "phedc";
        } else if ($disco == "kano") {
            $disco_code = "KANO";
        } else if ($disco == "kaduna") {
            $disco_code = "KADUNA";
            $payscribe_disco = "kedco";
        } else if ($disco == "jos") {
            $disco_code = "JOS";
        }

        if ($meter_type == "prepaid") {
            $meter_type = "PREPAID";
        } else if ($meter_type == "postpaid") {
            $meter_type = "POSTPAID";
        }

        if ($request->sms_check == true) {
            $amount_deb_user = $amount + 5;
        } else {
            $amount_deb_user = $amount;
        }
        $amount_to_debit_user = $amount;
        $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

        $meter_type = strtolower($meter_type);

        if ($amount_deb_user <= $user_total_amount) {




            $url = "https://api.buypower.ng/v2/vend";
            $use_post = true;

            $order_id = "BP" . mt_rand(10000000, 99999999);

            $data = array(
                'meter' => $meter_no,
                'disco' => $disco_code,
                'vendType' => $meter_type,
                'orderId' => $order_id,
                'phone' => $phone_number,
                'paymentType' => 'ONLINE',
                'amount' => $amount,
                'email' => $email
            );
            // return $data;


            $response = $this->functions->buyPowerVtuCurl($url, $use_post, $data);

            // var_dump($response);
            // return $response;
            $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";
            if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                if ($this->functions->isJson($response)) {
                    $response = json_decode($response);
                    if (is_object($response)) {
                        if (isset($response->status)) {
                            $status = $response->status;

                            $metertoken = "";
                            if ($status == true) {





                                // if(isset($response->message->details->reference_number)){
                                //  $order_id = $response->message->details->reference_number;
                                // }else{
                                //  $order_id = "";
                                // }

                                if (isset($response->data->token)) {
                                    $metertoken = $response->data->token;
                                    $this->functions->sendMeterTokenForPrepaidToUserByNotif($user_id, $email, $date, $time, $order_id, $disco, $meter_no, $amount, $metertoken);
                                }

                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'electricity',
                                    'sub_type' => $disco,
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount,
                                    'number' => $meter_no,
                                    'order_id' => $order_id
                                );
                                if ($this->functions->addTransactionStatus($form_array)) {
                                    $response_arr['success'] = true;
                                    $response_arr['order_id'] = $order_id;
                                    $response_arr['metertoken'] = $metertoken;

                                    if ($request->sms_check == true) {
                                        $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                                        $amount_to_debit_user = 5;
                                        // echo $user_total_amount;
                                        // echo $amount;

                                        if ($amount_to_debit_user <= $user_total_amount) {


                                            if ($meter_type == "prepaid") {
                                                $to = $phone_number;
                                                $message = "Your Meter Token For Meter Number " . $meter_no . " Is " . $metertoken;
                                                $url = "https://www.payscribe.ng/api/v1/sms";

                                                $use_post = true;
                                                $data = [
                                                    'to' => $to,
                                                    'message' => $message
                                                ];


                                                // var_dump($post_data);

                                                $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);


                                                if ($this->functions->isJson($response)) {

                                                    $response = json_decode($response);
                                                    // var_dump($response);

                                                    if ($response->status && $response->status_code == 200) {

                                                        $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                        if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                                            $order_id = $response->message->details->transaction_id;
                                                            $form_array = array(
                                                                'user_id' => $user_id,
                                                                'type' => 'bulk_sms',
                                                                'sub_type' => "",
                                                                'number' => $message,
                                                                'date' => $date,
                                                                'time' => $time,
                                                                'amount' => $amount_to_debit_user,
                                                                'order_id' => $order_id
                                                            );
                                                            if ($this->functions->addTransactionStatus($form_array)) {
                                                                $response_arr['success'] = true;
                                                                $response_arr['order_id'] = $order_id;
                                                            }
                                                        }
                                                    } else if ($response->status && $response->status_code == 201) {


                                                        $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                        if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                                            $order_id = $response->message->details->transaction_id;
                                                            $form_array = array(
                                                                'user_id' => $user_id,
                                                                'type' => 'bulk_sms',
                                                                'sub_type' => "",
                                                                'number' => $message,
                                                                'date' => $date,
                                                                'time' => $time,
                                                                'amount' => $amount_to_debit_user,
                                                                'order_id' => $order_id
                                                            );
                                                            if ($this->functions->addTransactionStatus($form_array)) {
                                                                $response_arr['success'] = true;
                                                                $response_arr['order_id'] = $order_id;
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
                }
            }
        } else {
            $response_arr['insuffecient_funds'] = true;
        }
    
        
        return back()->with('data',$response_arr);
        
    }

    public function purchaseElectricityWithPayscribe(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        if (isset($request->productCode) && isset($request->productToken) && isset($request->use_payscribe)) {
            $productCode = $request->productCode;
            $productToken = $request->productToken;
            $use_payscribe = $request->use_payscribe;
            if ($productCode != "" && $productToken != "" && $use_payscribe) {
                $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'order_id' => '', 'invalid_meterno' => false, 'meter_type_not_available' => false, 'metertoken' => '', 'transaction_pending' => false];

                $validationRules = [
                    'disco' => 'required|in:eko,ikeja,abuja,ibadan,enugu,phc,kano,kaduna,jos',
                    'meter_type' => 'required|in:prepaid,postpaid',
                    'meter_number' => 'required|numeric|digits_between:5,15',
                    'amount' => 'required|numeric|min:100|max:50000',
                    'mobile_number' => 'required|numeric|digits_between:5,15',
                    'email' => 'required',

                ];

                $request->validate($validationRules);


                
                $disco = $request->disco;
                $meter_type = $request->meter_type;
                $meter_number = $request->meter_number;
                $amount = $request->amount;
                $mobile_number = $request->mobile_number;
                $email = $request->email;
                $payscribe_disco = "";
                $phone_number = $mobile_number;
                $meter_no = $meter_number;

                $club_disco = "";

                if ($disco == "eko") {
                    $disco_code = "EKO";
                    $payscribe_disco = "ekedc";
                    $club_disco = "01";
                } else if ($disco == "ikeja") {
                    $disco_code = "IKEJA";
                    $payscribe_disco = "ikedc";
                    $club_disco = "02";
                } else if ($disco == "abuja") {
                    $disco_code = "ABUJA";
                    $payscribe_disco = "aedc";
                    $club_disco = "03";
                } else if ($disco == "ibadan") {
                    $disco_code = "IBADAN";
                    $payscribe_disco = "ibedc";
                    $club_disco = "07";
                } else if ($disco == "enugu") {
                    $disco_code = "ENUGU";
                    $payscribe_disco = "eedc";
                    $club_disco = "09";
                } else if ($disco == "phc") {
                    $disco_code = "PH";
                    $payscribe_disco = "phedc";
                    $club_disco = "05";
                } else if ($disco == "kano") {
                    $disco_code = "KANO";
                    $club_disco = "04";
                } else if ($disco == "kaduna") {
                    $disco_code = "KADUNA";
                    $payscribe_disco = "kedco";
                    $club_disco = "08";
                } else if ($disco == "jos") {
                    $disco_code = "JOS";
                    $club_disco = "06";
                }

                if ($meter_type == "prepaid") {
                    $meter_type = "PREPAID";
                    $club_meter_type = "01";
                } else if ($meter_type == "postpaid") {
                    $meter_type = "POSTPAID";
                    $club_meter_type = "02";
                }



                if ($request->sms_check == true) {
                    $amount_deb_user = $amount + 5;
                } else {
                    $amount_deb_user = $amount;
                }
                $amount_to_debit_user = $amount;
                $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

                $meter_type = strtolower($meter_type);

                if ($amount_deb_user <= $user_total_amount) {

                    $vtu_platform = $this->functions->getVtuPlatformToUse('', 'electricity');
                    // return $vtu_platform;
                    if ($vtu_platform == "payscribe" && $payscribe_disco != "") {


                        $url = "https://www.payscribe.ng/api/v1/electricity/vend";
                        $use_post = true;
                        $data = array(
                            'productCode' => $productCode,
                            'productToken' => $productToken,
                            'phone' => $mobile_number
                        );
                        $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);

                        // return $response;
                        if ($this->functions->isJson($response)) {
                            $response = json_decode($response);
                            if (is_object($response)) {

                                if ($response->status == true && $response->status_code == 200) {
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                    if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {


                                        $order_id = $response->message->details->trans_id;

                                        if (isset($response->message->details->token)) {
                                            if (!is_null($response->message->details->token)) {
                                                $metertoken = $response->message->details->token;
                                                $this->functions->sendMeterTokenForPrepaidToUserByNotif($user_id, $email, $date, $time, $order_id, $disco, $meter_no, $amount, $metertoken);
                                            }
                                        } else {
                                            $metertoken = "";
                                        }

                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'electricity',
                                            'sub_type' => $disco,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount,
                                            'number' => $meter_no,
                                            'order_id' => $order_id
                                        );
                                        if ($this->functions->addTransactionStatus($form_array)) {
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                            $response_arr['metertoken'] = $metertoken;


                                            if ($request->sms_check == true) {
                                                $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                                                $amount_to_debit_user = 5;
                                                // echo $user_total_amount;
                                                // echo $amount;

                                                if ($amount_to_debit_user <= $user_total_amount) {


                                                    if ($meter_type == "prepaid") {
                                                        $to = $phone_number;
                                                        $message = "Your Meter Token For Meter Number " . $meter_no . " Is " . $metertoken;
                                                        $url = "https://www.payscribe.ng/api/v1/sms";

                                                        $use_post = true;
                                                        $post_data = [
                                                            'to' => $to,
                                                            'message' => $message
                                                        ];


                                                        // var_dump($post_data);

                                                        $response = $this->functions->payscribeVtuCurl($url, $use_post, $post_data);


                                                        if ($this->functions->isJson($response)) {

                                                            $response = json_decode($response);
                                                            // var_dump($response);

                                                            if ($response->status && $response->status_code == 200) {

                                                                $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                                                    $order_id = $response->message->details->transaction_id;
                                                                    $form_array = array(
                                                                        'user_id' => $user_id,
                                                                        'type' => 'bulk_sms',
                                                                        'sub_type' => "",
                                                                        'number' => $message,
                                                                        'date' => $date,
                                                                        'time' => $time,
                                                                        'amount' => $amount_to_debit_user,
                                                                        'order_id' => $order_id
                                                                    );
                                                                    if ($this->functions->addTransactionStatus($form_array)) {
                                                                        $response_arr['success'] = true;
                                                                        $response_arr['order_id'] = $order_id;
                                                                    }
                                                                }
                                                            } else if ($response->status && $response->status_code == 201) {


                                                                $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                                                    $order_id = $response->message->details->transaction_id;
                                                                    $form_array = array(
                                                                        'user_id' => $user_id,
                                                                        'type' => 'bulk_sms',
                                                                        'sub_type' => "",
                                                                        'number' => $message,
                                                                        'date' => $date,
                                                                        'time' => $time,
                                                                        'amount' => $amount_to_debit_user,
                                                                        'order_id' => $order_id
                                                                    );
                                                                    if ($this->functions->addTransactionStatus($form_array)) {
                                                                        $response_arr['success'] = true;
                                                                        $response_arr['order_id'] = $order_id;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else if ($response->status == true && $response->status_code == 201) {
                                    $response_arr['transaction_pending'] = true;

                                    $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                    if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {

                                        $order_id = $response->message->details->trans_id;



                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'electricity',
                                            'sub_type' => $disco,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount,
                                            'number' => $meter_no,
                                            'order_id' => $order_id
                                        );
                                        if ($this->functions->addTransactionStatus($form_array)) {
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                            $response_arr['metertoken'] = "";
                                        }
                                    }
                                }
                            }
                        }
                    } else if ($vtu_platform == "clubkonnect" && $club_disco != "") {
                        $url = "https://www.nellobytesystems.com/APIElectricityV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&ElectricCompany=" . $club_disco . "&MeterType=" . $club_meter_type . "&MeterNo=" . $meter_no . "&Amount=" . $amount;
                        $use_post = true;

                        $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);
                        // return $response;
                        if ($this->functions->isJson($response)) {
                            $response = json_decode($response);
                            if (is_object($response)) {
                                $status = $response->status;
                                $metertoken = "";
                                if ($status == "ORDER_RECEIVED") {
                                    $summary = "Debit Of " . $amount_to_debit_user . " For Electricity Recharge";

                                    if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {


                                        if (isset($response->transactionid)) {
                                            $order_id = $response->transactionid;
                                        } else {
                                            $order_id = "";
                                        }

                                        if (isset($response->metertoken)) {
                                            $metertoken = $response->metertoken;
                                            $this->functions->sendMeterTokenForPrepaidToUserByNotif($user_id, $email, $date, $time, $order_id, $disco, $meter_no, $amount, $metertoken);
                                        }

                                        $form_array = array(
                                            'user_id' => $user_id,
                                            'type' => 'electricity',
                                            'sub_type' => $disco,
                                            'date' => $date,
                                            'time' => $time,
                                            'amount' => $amount,
                                            'number' => $meter_no,
                                            'order_id' => $order_id
                                        );
                                        if ($this->functions->addTransactionStatus($form_array, true)) {
                                            $response_arr['success'] = true;
                                            $response_arr['order_id'] = $order_id;
                                            $response_arr['metertoken'] = $metertoken;


                                            if ($request->sms_check == true) {
                                                $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
                                                $amount_to_debit_user = 5;
                                                // echo $user_total_amount;
                                                // echo $amount;

                                                if ($amount_to_debit_user <= $user_total_amount) {


                                                    if ($meter_type == "prepaid") {
                                                        $to = $phone_number;
                                                        $message = "Your Meter Token For Meter Number " . $meter_no . " Is " . $metertoken;
                                                        $url = "https://www.payscribe.ng/api/v1/sms";

                                                        $use_post = true;
                                                        $post_data = [
                                                            'to' => $to,
                                                            'message' => $message
                                                        ];


                                                        // var_dump($post_data);

                                                        $response = $this->functions->payscribeVtuCurl($url, $use_post, $post_data);


                                                        if ($this->functions->isJson($response)) {

                                                            $response = json_decode($response);
                                                            // var_dump($response);

                                                            if ($response->status && $response->status_code == 200) {

                                                                $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                                                    $order_id = $response->message->details->transaction_id;
                                                                    $form_array = array(
                                                                        'user_id' => $user_id,
                                                                        'type' => 'bulk_sms',
                                                                        'sub_type' => "",
                                                                        'number' => $message,
                                                                        'date' => $date,
                                                                        'time' => $time,
                                                                        'amount' => $amount_to_debit_user,
                                                                        'order_id' => $order_id
                                                                    );
                                                                    if ($this->functions->addTransactionStatus($form_array)) {
                                                                        $response_arr['success'] = true;
                                                                        $response_arr['order_id'] = $order_id;
                                                                    }
                                                                }
                                                            } else if ($response->status && $response->status_code == 201) {


                                                                $summary = "Debit Of " . $amount_to_debit_user . " For Bulk SMS";
                                                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                                                    $order_id = $response->message->details->transaction_id;
                                                                    $form_array = array(
                                                                        'user_id' => $user_id,
                                                                        'type' => 'bulk_sms',
                                                                        'sub_type' => "",
                                                                        'number' => $message,
                                                                        'date' => $date,
                                                                        'time' => $time,
                                                                        'amount' => $amount_to_debit_user,
                                                                        'order_id' => $order_id
                                                                    );
                                                                    if ($this->functions->addTransactionStatus($form_array)) {
                                                                        $response_arr['success'] = true;
                                                                        $response_arr['order_id'] = $order_id;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else if ($status == "INVALID_MeterNo") {
                                    $response_arr['invalid_meterno'] = true;
                                } else if ($status == "MeterType_NOT_AVAILABLE") {
                                    $response_arr['meter_type_not_available'] = true;
                                } else if ($status == "INSUFFICIENT_BALANCE") {
                                    // $response_arr['invalid_recipient'] = true;

                                } else if ($status == "INVALID_CREDENTIALS") {
                                    // $response_arr['invalid_recipient'] = true;
                                    // echo "string";

                                }
                            }
                        }
                    }
                } else {
                    $response_arr['insuffecient_funds'] = true;
                }

                return back()->with('data', $response_arr);
            }

        }
        
        
        
    }

    public function validateMeterNumberDisco(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'customer_name' => '', 'invalid_user' => false, 'use_payscribe' => false, 'productCode' => '', 'productToken' => ''];

        $validationRules = [
            'disco' => 'required|in:eko,ikeja,abuja,ibadan,enugu,phc,kano,kaduna,jos',
            'meter_type' => 'required|in:prepaid,postpaid',
            'meter_number' => 'required|numeric|digits_between:5,15',
            'amount' => 'required|numeric|min:100|max:50000',
            'mobile_number' => 'required|numeric|digits_between:5,15',
            'email' => 'required',

        ];

        $request->validate($validationRules);


        
        $disco = $request->disco;
        $meter_type = $request->meter_type;
        $meter_number = $request->meter_number;
        $amount = $request->amount;
        $mobile_number = $request->mobile_number;
        $email = $request->email;
        $payscribe_disco = "";
        $club_disco = "";

        if ($disco == "eko") {
            $disco_code = "EKO";
            $payscribe_disco = "ekedc";
            $club_disco = "01";
        } else if ($disco == "ikeja") {
            $disco_code = "IKEJA";
            $payscribe_disco = "ikedc";
            $club_disco = "02";
        } else if ($disco == "abuja") {
            $disco_code = "ABUJA";
            $payscribe_disco = "aedc";
            $club_disco = "03";
        } else if ($disco == "ibadan") {
            $disco_code = "IBADAN";
            $payscribe_disco = "ibedc";
            $club_disco = "07";
        } else if ($disco == "enugu") {
            $disco_code = "ENUGU";
            $payscribe_disco = "eedc";
            $club_disco = "09";
        } else if ($disco == "phc") {
            $disco_code = "PH";
            $payscribe_disco = "phedc";
            $club_disco = "05";
        } else if ($disco == "kano") {
            $disco_code = "KANO";
            $club_disco = "04";
        } else if ($disco == "kaduna") {
            $disco_code = "KADUNA";
            $payscribe_disco = "kedco";
            $club_disco = "08";
        } else if ($disco == "jos") {
            $disco_code = "JOS";
            $club_disco = "06";
        }

        if ($meter_type == "prepaid") {
            $meter_type = "PREPAID";
            $club_meter_type = "01";
        } else if ($meter_type == "postpaid") {
            $meter_type = "POSTPAID";
            $club_meter_type = "02";
        }



        // // $url = "https://www.nellobytesystems.com/APIVerifyElectricityV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&ElectricCompany=".$disco_code."&meterno=".$meter_no;
        $url = "https://api.buypower.ng/v2/check/meter?meter=" . $meter_number . "&disco=" . $disco_code . "&vendType=" . $meter_type . "&orderId=true";
        // return $url;
        $use_post = false;

        $response = $this->functions->buyPowerVtuCurl($url, $use_post);

        // return($response);

        if ($this->functions->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if (isset($response->name)) {

                    $customer_name = $response->name;
                    // $minVendAmount = $response->minVendAmount;
                    // $maxVendAmount = $response->maxVendAmount;

                    if ($customer_name != "") {
                        $response_arr['success'] = true;
                        $response_arr['customer_name'] = $customer_name;

                        $eko_platform = $this->functions->getVtuPlatformToUse("electricity", "eko");
                        if ($disco == "eko" && $eko_platform == "payscribe") {
                            // return true;
                            $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                            $use_post = true;
                            $data = array(
                                'meter_number' => $meter_number,
                                'meter_type' => strtolower($meter_type),
                                'amount' => $amount,
                                'service' => $payscribe_disco
                            );
                            // return $data;
                            $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);

                            if ($this->functions->isJson($response)) {
                                $response = json_decode($response);
                                // return $response;

                                if ($response->status == true && $response->status_code == 200) {
                                    // if($response->message->details->canVend == true){
                                    $response_arr['use_payscribe'] = true;
                                    $response_arr['productCode'] = $response->message->details->productCode;
                                    $response_arr['productToken'] = $response->message->details->productToken;
                                    // }
                                }
                            }
                        } else {

                            if ($amount < 1000) {
                                $vtu_platform = $this->functions->getVtuPlatformToUse('', 'electricity');
                                // return $vtu_platform;
                                if ($vtu_platform == "payscribe" && $payscribe_disco != "") {
                                    $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                                    $use_post = true;
                                    $data = array(
                                        'meter_number' => $meter_number,
                                        'meter_type' => strtolower($meter_type),
                                        'amount' => $amount,
                                        'service' => $payscribe_disco
                                    );
                                    // return $data;
                                    $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);

                                    if ($this->functions->isJson($response)) {
                                        $response = json_decode($response);
                                        // return $response;

                                        if ($response->status == true && $response->status_code == 200) {
                                            // if($response->message->details->canVend == true){
                                            $response_arr['use_payscribe'] = true;
                                            $response_arr['productCode'] = $response->message->details->productCode;
                                            $response_arr['productToken'] = $response->message->details->productToken;
                                            // }
                                        }
                                    }
                                } else if ($vtu_platform == "clubkonnect" && $club_disco != "") {
                                    // $url = "https://www.payscribe.ng/api/v1/electricity/validate";
                                    $url = "https://www.nellobytesystems.com/APIVerifyElectricityV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&ElectricCompany=" . $club_disco . "&meterno=" . $meter_number;
                                    // return $url;
                                    $use_post = true;

                                    $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);
                                    // return $response;
                                    if ($this->functions->isJson($response)) {
                                        $response = json_decode($response);

                                        if (is_object($response)) {
                                            if ($response->customer_name != "") {
                                                $response_arr['use_payscribe'] = true;
                                                $response_arr['productCode'] = "yeye666399";
                                                $response_arr['productToken'] = "yheye66366";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $response_arr['invalid_user'] = true;
                    }
                } else {
                    $response_arr['invalid_user'] = true;
                }
            } else {
            }
        }









        // $url = "https://www.payscribe.ng/api/v1/electricity/validate";
        // $use_post = true;
        // $data = array(
        //     'meter_number' => $meter_number,
        //     'meter_type' => strtolower($meter_type),
        //     'amount' => $amount,
        //     'service' => $payscribe_disco
        // );
        // // return $data;
        // $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);

        // if($this->main_model->isJson($response)){
        //     $response = json_decode($response);
        //     // return ($response);

        //     if($response->status == true && $response->status_code == 200){
        //         // if($response->message->details->canVend == true){
        //             $customer_name = $response->message->details->customer_name;
        //             $response_arr['success'] = true;
        //             $response_arr['customer_name'] = $customer_name;
        //             $response_arr['use_payscribe'] = true;
        //             $response_arr['productCode'] = $response->message->details->productCode;
        //             $response_arr['productToken'] = $response->message->details->productToken;
        //         // }
        //     }else{
        //         $response_arr['invalid_user'] = true;
        //     }
        // }

        
        return back()->with('data',$response_arr);
        
    }

    public function checkIfDiscoIsAvailable(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false];

        if (isset($request->disco)) {
            $disco = $request->disco;

            if ($disco == "eko" || $disco == "ikeja" || $disco == "abuja" || $disco == "ibadan" || $disco == "enugu" || $disco == "phc" || $disco == "kano" || $disco == "kaduna" || $disco == "jos") {

                if ($disco == "eko") {
                    $disco_code = "EKO";
                } else if ($disco == "ikeja") {
                    $disco_code = "IKEJA";
                } else if ($disco == "abuja") {
                    $disco_code = "ABUJA";
                } else if ($disco == "ibadan") {
                    $disco_code = "IBADAN";
                } else if ($disco == "enugu") {
                    $disco_code = "ENUGU";
                } else if ($disco == "phc") {
                    $disco_code = "PH";
                } else if ($disco == "kano") {
                    $disco_code = "KANO";
                } else if ($disco == "kaduna") {
                    $disco_code = "KADUNA";
                } else if ($disco == "jos") {
                    $disco_code = "JOS";
                }

                // $response_arr['success'] = true;    


                $url = "https://api.buypower.ng/v2/discos/status";
                $use_post = false;


                $response = $this->functions->buyPowerVtuCurl($url, $use_post,true);
                // return $response;

                if ($this->functions->isJson($response)) {
                    $response = json_decode($response);
                    if (is_object($response)) {

                        if (isset($response->$disco_code)) {
                            $status = $response->$disco_code;
                            if ($status) {
                                if ($disco == "eko") {
                                    $response_arr['success'] = true;
                                } else {
                                    $response_arr['success'] = true;
                                }
                            }
                        }
                    }
                }
            }
        }

        
        return back()->with('data',$response_arr);
        
    }

    public function showElectricityPage(Request $request){
        $user  = Auth::user();
        $props['user'] = $user;

        return Inertia::render('Vtu/ElectricityTopup',$props);
    }

    public function purchaseEminenceData(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'order_id' => '', 'transaction_pending' => false];

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);


        
        // return $post_data->selected_plan['product_id'];
        $network = $request->network;
        if (isset($request->selected_plan['product_id'])) {

            $plan = $request->selected_plan['product_id'];
            // $product_code = $post_data->selected_plan['product_code'];
            $product_id = $plan;


            $phone_number = $request->phone_number;
            $url = "https://app.eminencesub.com/api/buy-data";
            $url_2 = "https://app.eminencesub.com/api/data";

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



            $amount = $this->functions->getEminenceVtuDataBundleCostByProductId($url_2, $network, $plan);
            // return $amount;
            // echo $product_id;
            $amount_to_debit_user = $amount;
            if ($amount != 0) {
                // $amount_to_debit_user = round(($perc_disc * $amount) + $amount, 2);
                $amount_to_debit_user += $additional_charge;
            }

            $api_choice = false;

            

            // return $serviceID;
            // return $amount_to_debit_user;

            if ($amount_to_debit_user != 0) {


                $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

                if ($amount_to_debit_user <= $user_total_amount) {


                    $use_post = true;
                    $data = [
                        'plan' => $plan,
                        'network' => $network_id,
                        'phone' => $phone_number,

                    ];


                    $response = $this->functions->eminenceVtuCurl($url, $use_post, $data, $api_choice);

                    // return $data;
                    // return $response;
                    if ($this->functions->isJson($response)) {
                        $response = json_decode($response);
                        // var_dump($response);
                        if (isset($response->message)) {
                            if ($response->code == 201) {

                                $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                    $order_id = "TT" . $response->data->reference;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'data',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount_to_debit_user,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $response_arr['insuffecient_funds'] = true;
                }
            }
        }
        

        return back()->with('data',$response_arr);
        
    }

    public function purchaseGsubzData(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();

        $user_id = $user->id;
        $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'order_id' => '', 'transaction_pending' => false];

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);

        // return $post_data->selected_plan['product_id'];
        $network = $request->network;
        if (isset($request->selected_plan['product_id'])) {

            $plan = $request->selected_plan['product_id'];
            $product_code = $request->selected_plan['product_code'];
            $api = "ap_f92ed8cbd5c5a1afea507051eb7f0975";
            $requestID = md5(mt_rand() . time());
            $phone_number = $request->phone_number;
            $url_2 = "https://gsubz.com/api/plans/?service=";

            $vtu_platform = $this->functions->getVtuPlatformToUse('data', $network);

            $vtu_platform_shrt = substr($vtu_platform, 0, 5);

            $serviceID = substr($vtu_platform, 6);
            if ($vtu_platform == "eminence" && $network == "mtn") {
                $serviceID = "mtncg";
            }

            if ($network == "mtn") {
                if (isset($request->selected_plan['gsubz_type'])) {
                    // code...

                    $gsubz_type = $request->selected_plan['gsubz_type'];
                   
                    $net = "Mtn";
                    $perc_disc = 0.04;
                    $additional_charge = 25;
                    
                }
            } else if ($network == "glo") {
                // $network = "GLO";
                // $serviceID = "glo_data";
                $net = "Glo";
                $perc_disc = 0.04;
                $additional_charge = 25;
            } else if ($network == "airtel") {
                // $network = "AIRTEL";
                // $serviceID = "airtelcg";
                // $serviceID = "airtel_cg";
                $net = "Airtel";
                $perc_disc = 0.04;
                $additional_charge = 25;
            } else if ($network == "9mobile") {
                // $network = "9MOBILE";
                // $serviceID = "etisalat_data";
                $net = "9mobile";
                $perc_disc = 0.04;
                $additional_charge = 25;
            }

            $url_2 .= $serviceID;

            // return $url_2;

            $amount = $this->functions->getGsubzVtuDataBundleCostByProductId($url_2, $network, $plan);
            // return $amount;
            // echo $product_id;
            $amount_to_debit_user = $amount;
            if ($amount != 0) {
                // $amount_to_debit_user = round((0.04 * $amount) + $amount, 2);
                $amount_to_debit_user += $additional_charge;
            }

           

            // return $serviceID;
            // return $amount_to_debit_user;



            if ($amount_to_debit_user != 0) {


                $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

                if ($amount_to_debit_user <= $user_total_amount) {

                    $url = "https://gsubz.com/api/pay/";
                    $use_post = true;
                    $data = [
                        'serviceID' => $serviceID,
                        'plan' => $plan,
                        'amount' => $product_code,
                        'api' => $api,
                        'phone' => $phone_number,
                        'requestID' => $requestID,
                    ];


                    $response = $this->functions->gSubzVtuCurl($url, $use_post, $data);

                    // return $data;
                    // return $response;
                    if ($this->functions->isJson($response)) {
                        $response = json_decode($response);
                        // var_dump($response);
                        if (isset($response->status)) {
                            // if($response->status == "TRANSACTION_SUCCESSFUL" && $response->code == 200){
                            if ($response->code == 200) {

                                $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                    $order_id = "GS" . $response->content->transactionID;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'data',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount_to_debit_user,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $response_arr['insuffecient_funds'] = true;
                }
            }
        }
        
        return back()->with('data',$response_arr);
        
    }


    public function purchaseClubKonnectData(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'order_id' => '', 'transaction_pending' => false];

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);

        
        // return $post_data->selected_plan['product_id'];
        $network = $request->network;
        if (isset($request->selected_plan['product_id'])) {

            $product_id = $request->selected_plan['product_id'];
            if ($network == "mtn") {

                $mobilenetwork_code = "01";
                $perc_disc = 0.04;
                
                
                $amt_to_add = 25;
            } elseif ($network == "glo") {

                $mobilenetwork_code = "02";
                $perc_disc = 0.04;
                $amt_to_add = 25;
            } else if ($network == "9mobile") {

                $mobilenetwork_code = "03";
                $perc_disc = 0.04;
                $amt_to_add = 25;
            } else if ($network == "airtel") {

                $mobilenetwork_code = "04";
                $perc_disc = 0.04;
                $amt_to_add = 25;
            }




            $amount = $this->functions->getVtuDataBundleCostByProductId($network, $product_id);




            if ($amount != 0) {
                $phone_number = $request->phone_number;

                // $amount_to_debit_user = round(($perc_disc * $amount) + $amount,2);

                $amount_to_debit_user = $amount + $amt_to_add;

                
                // return $amount_to_debit_user;



                // return $amount_to_debit_user;
                $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

                if ($amount_to_debit_user <= $user_total_amount) {

                    // $url = "https://www.nellobytesystems.com/APIVerifyElectricityV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&ElectricCompany=".$disco_code."&meterno=".$meter_no;

                    $url = "https://www.nellobytesystems.com/APIDatabundleV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&MobileNetwork=" . $mobilenetwork_code . "&DataPlan=" . $product_id . "&MobileNumber=" . $phone_number;
                    // return $url;
                    $use_post = true;
                    // $post_data = array(
                    //  'network' => $network,
                    //  'plan' => $product_id,
                    //  'recipent' => $mobile_no
                    // );

                    $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);
                    // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '542425'));
                    // return $response;
                    if ($this->functions->isJson($response)) {
                        $response = json_decode($response);
                        // var_dump($response);
                        if ($response->status == "ORDER_RECEIVED") {
                            $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                            if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                $order_id = $response->orderid;
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'data',
                                    'sub_type' => $network,
                                    'number' => $phone_number,
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount_to_debit_user,
                                    'order_id' => $order_id
                                );
                                if ($this->functions->addTransactionStatus($form_array)) {
                                    $response_arr['success'] = true;
                                    $response_arr['order_id'] = $order_id;
                                }
                            }
                        }
                    }
                } else {
                    $response_arr['insuffecient_funds'] = true;
                }
            }
        }
        


       
        return back()->with('data',$response_arr);
        
    }

    public function purchasePayscribeData(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();

        $user_id = $user->id;
        $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'order_id' => '', 'transaction_pending' => false];

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);


        
        // return $post_data->selected_plan['product_id'];
        $network = $request->network;
        if (isset($request->selected_plan['product_id'])) {

            $product_id = $request->selected_plan['product_id'];
            if ($network == "mtn") {
                $amount = $this->functions->getPayscribeVtuDataBundleCostByProductId($network, $product_id);

                if ($amount != 0) {
                    $phone_number = $request->phone_number;

                    $amount_to_debit_user = round((0.04 * $amount) + $amount, 2);
                    $amount_to_debit_user += 25;

                    $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

                    if ($amount_to_debit_user <= $user_total_amount) {

                        $url = "https://www.payscribe.ng/api/v1/data/vend";
                        $use_post = true;
                        $data = array(
                            'network' => $network,
                            'plan' => $product_id,
                            'recipent' => $phone_number
                        );

                        if (isset($request->ported)) {
                            if ($request->ported == true) {
                                $data['ported'] = true;
                            }
                        }

                        // echo json_encode($post_data);

                        $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);


                        if ($this->functions->isJson($response)) {
                            $response = json_decode($response);

                            // var_dump($response);
                            if ($response->status && $response->status_code == 200) {

                                $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                    $order_id = $response->message->details->transaction_id;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'data',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount_to_debit_user,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            } else if ($response->status && $response->status_code == 201) {
                                $response_arr['transaction_pending'] = true;

                                $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                    $order_id = $response->message->details->transaction_id;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'data',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount_to_debit_user,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            }
                        }
                    } else {
                        $response_arr['insuffecient_funds'] = true;
                    }
                }
            } else {

                if ($network == "glo") {
                    // $network = "GLO";

                    $net = "Glo";
                    $perc_disc = 0.04;
                    $additional_charge = 12;
                } else if ($network == "airtel") {
                    // $network = "AIRTEL";

                    $net = "Airtel";
                    $perc_disc = 0.04;
                    $additional_charge = 4;
                } else if ($network == "9mobile") {
                    // $network = "9MOBILE";

                    $net = "9mobile";
                    $perc_disc = 0.04;
                    $additional_charge = 20;
                }

                $phone_number = $request->phone_number;

                $amount = $this->functions->getPayscribeVtuDataBundleCostByProductId($network, $product_id);
                // echo $product_id;
                $amount_to_debit_user = 0;
                if ($amount != 0) {
                    $amount_to_debit_user = round((0.04 * $amount) + $amount, 2);
                    $amount_to_debit_user += $additional_charge;
                }


                if ($amount_to_debit_user != 0) {


                    $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

                    if ($amount_to_debit_user <= $user_total_amount) {

                        $url = "https://www.payscribe.ng/api/v1/data/vend";
                        $use_post = true;
                        $data = array(
                            'network' => $net,
                            'plan' => $product_id,
                            'recipent' => $phone_number
                        );

                        if (isset($request->ported)) {
                            if ($request->ported == true) {
                                $data['ported'] = true;
                            }
                        }


                        $response = $this->functions->payscribeVtuCurl($url, $use_post, $data);


                        if ($this->functions->isJson($response)) {
                            $response = json_decode($response);
                            // var_dump($response);
                            if ($response->status && $response->status_code == 200) {

                                $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                    $order_id = $response->message->details->transaction_id;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'data',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount_to_debit_user,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            } else if ($response->status && $response->status_code == 201) {
                                $response_arr['transaction_pending'] = true;
                                $summary = "Debit Of " . $amount_to_debit_user . " For Data Recharge";
                                if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                    $order_id = $response->message->details->transaction_id;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'data',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount_to_debit_user,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            }
                        }
                    } else {
                        $response_arr['insuffecient_funds'] = true;
                    }
                }
            }
        }
        

        return back()->with('data',$response_arr);
        
    }

    public function purchase9mobileComboData(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'insuffecient_funds' => false, 'order_id' => '', 'transaction_pending' => false];

        $validationRules = [
            'network' => 'required|in:9mobile',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);


        if (isset($request->selected_plan['product_id'])) {

            $product_id = $request->selected_plan['product_id'];
            $amount = $this->functions->get9mobileComboCostByProductId($product_id);
            $phone_number = $request->phone_number;
            $amount_to_debit_user = $amount;

            // echo $amount_to_debit_user;
            $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

            if ($amount_to_debit_user <= $user_total_amount) {

                $data_amount = $this->functions->get9mobileComboDataAmountByProductId($product_id);
                if ($data_amount != "") {
                    $form_array = array(
                        'user_id' => $user_id,
                        'type' => 'data',
                        'sub_type' => '9mobile',
                        'number' => $phone_number,
                        'date' => $date,
                        'time' => $time,
                        'amount' => $amount_to_debit_user,
                        'order_id' => ""
                    );
                    if ($this->functions->addTransactionStatus($form_array)) {
                        $form_array = array(
                            'user_id' => $user_id,
                            'number' => $phone_number,
                            'amount' => $data_amount,
                            'date' => $date,
                            'time' => $time
                        );
                        if ($this->functions->addComboRequest($form_array)) {
                            $summary = "Debit Of " . $amount_to_debit_user . " For 9mobile Data Recharge";
                            if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {
                                $response_arr['success'] = true;
                            }
                        }
                    }
                }
            } else {
                $response_arr['insuffecient_funds'] = true;
            }
        }
        


        return back()->with('data',$response_arr);
        
    }

    public function getDataPlansByNetwork(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'data_plans' => ''];

        if ($request->network) {
            $combo = false;
            $network = $request->network;
            if ($request->combo) {
                if ($request->combo && $network == "9mobile") {
                    $combo = true;
                }
            }
            $response_arr['success'] = true;
            $response_arr['data_plans'] = $this->functions->loadDataPlansForNetwork($network, $combo);
            // return $response_arr['data_plans'];
        }


        return back()->with('data',$response_arr);
    }

    public function showInternetDataPage(Request $request){
        $user = Auth::user();

        $props['user'] = $user;
        
        return Inertia::render('Vtu/InternetData',$props);
    }

    public function generateEpin(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'messages' => '', 'insuffecient_funds' => false, 'epins' => '', 'invalid_amount' => false, 'invalid_recipient' => false, 'epins_json' => '', 'pin' => ''];

        $validationRules = [
            'network' => 'required|in:mtn,airtel,glo,9mobile',
            'amount' => 'required|numeric|in:100,200,500',
            'recharge_type' => 'required|in:epin',
            'quantity' => 'required|numeric|min:1|max:20'

        ];

        $request->validate($validationRules);


        
        $network = $request->network;
        $amount = $request->amount;
        $quantity = $request->quantity;

        if ($network == "mtn") {
            $club_network = "01";
            $discount = 0.01;
        } else if ($network == "glo") {
            $club_network = "02";
            $discount = 0.02;
        } else if ($network == "9mobile") {
            $club_network = "03";
            $discount = 0.02;
        } else if ($network == "airtel") {
            $club_network = "04";
            $discount = 0.02;
        }




        $amount_to_debit_user = $amount - ($discount * $amount);
        $amount_to_debit_user = $amount_to_debit_user * $quantity;

        $response_arr['amount_to_debit_user'] = $amount_to_debit_user;

        $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);

        // return $amount_to_debit_user;

        if ($amount_to_debit_user <= $user_total_amount) {



            // $url = "https://www.payscribe.ng/api/v1/rechargecard";

            $url = "https://www.nellobytesystems.com/APIEPINV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&MobileNetwork=" . $club_network . "&Value=" . $amount . "&Quantity=" . $quantity;

            // return $url;
            $use_post = false;
            // $data = [
            //     'qty' => $quantity,
            //     'amount' => $amount,
            //     'display_name' => "Payscribe"
            // ];

            // $response = $this->main_model->payscribeVtuCurl($url,$use_post,$data);
            // $response = $this->main_model->generateRandomEpinData($quantity);

            // return $response;
            $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);
            // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '5424425'));
            // return $response;

            // $response = '{"TXN_EPIN":[{"transactionid":"6425025665","transactiondate":"2/1/2022 11:20:31 AM","batchno":"726326","mobilenetwork":"GLO","sno":"580129003028638","pin":"929181631685436","amount":"100"}]}';
            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    $transactionid = $response->TXN_EPIN[0]->transactionid;
                    $pin = $response->TXN_EPIN[0]->pin;
                    $order_id = $transactionid;

                    if ($transactionid != "") {

                        $summary = "Debit Of " . $amount_to_debit_user . " For Vtu E-Pin Generation";
                        if ($this->functions->debitUser($user_id, $amount_to_debit_user, $summary)) {

                            $form_array = array(
                                'user_id' => $user_id,
                                'type' => 'e-pin',
                                'sub_type' => 'payscribe_epin',

                                'date' => $date,
                                'time' => $time,
                                'amount' => $amount_to_debit_user,
                                'order_id' => $order_id
                            );
                            if ($this->functions->addTransactionStatus($form_array)) {
                                $response_arr['success'] = true;
                                // $index = 0;
                                // for($i = 0; $i < count($details); $i++){
                                //     $index++;
                                //     $details[$i]->index = $index;
                                // }
                                // $response_arr['epins'] = $details;
                                // $response_arr['epins_json'] = json_encode($details);
                                $response_arr['pin'] = $pin;
                                $response_arr['amount'] = $amount;
                            }
                        }
                    }
                }
            }
        } else {
            $response_arr['insuffecient_funds'] = true;
        }
        


        return back()->with('data',$response_arr);
    }

    public function normalAirtimeRechargeRequest(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;
        $response_arr = ['success' => false, 'insuffecient_funds' => false, 'order_id' => '', 'invalid_amount' => false, 'invalid_recipient' => false];

        $validationRules = [
            'network' => 'required|in:9mobile,mtn,airtel,glo',
            'amount' => 'required|numeric|min:100|max:50000',
            'recharge_type' => 'required|in:normal',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);

        $network = $request->network;


        $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
        // echo $user_total_amount;
        // echo $amount;
        $amount = $request->amount;
        if ($amount <= $user_total_amount) {

            $recharge_type = $request->recharge_type;
            $phone_number = $request->phone_number;
            $airtime_bonus = $request->airtime_bonus;
            $amount_to_debit_user = $amount;

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

            $url = "https://www.nellobytesystems.com/APIAirtimeV1.asp?UserID=".$this->CLUB_USERID."&APIKey=".$this->CLUB_APIKEY."&MobileNetwork=" . $mobilenetwork_code . "&Amount=" . $amount . "&MobileNumber=" . $phone_number;

            $url_2 = "https://app.eminencesub.com/api/buy-airtime";
            $url_3 = "https://gsubz.com/api/pay/";

            if ($network == "mtn" && $airtime_bonus == true) {
                $url .= "&BonusType=01";
            } else if ($network == "glo" && $airtime_bonus == true) {
                $url .= "&BonusType=02";
            }

            // return $url;

            $use_post = false;

            $vtu_platform = $this->functions->getVtuPlatformToUse('airtime', $network);
            
            $vtu_platform_shrt = substr($vtu_platform, 0, 8);
            // dd($vtu_platform_shrt);
            if ($vtu_platform == "gsubz") {

                $api = $this->functions->getGsubzApiKey();
                // return $type;
                $post_data = [
                    "phone" => $phone_number,
                    "amount" => $amount,
                    "serviceID" => $serviceID,
                    "api" => $api,
                ];



                $response = $this->functions->gSubzVtuCurl($url_3, true, $post_data);

                // return $response;
                if ($this->functions->isJson($response)) {
                    $response = json_decode($response);
                    if (is_object($response)) {
                        $code = $response->code;
                        $status = $response->status;

                        if ($code == 200 && $status == "TRANSACTION_SUCCESSFUL") {
                            $summary = "Debit Of " . $amount . " For Airtime Recharge";
                            if ($this->functions->debitUser($user_id, $amount, $summary)) {

                                $order_id = "GS" . $response->content->transactionID;
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'type' => 'airtime',
                                    'sub_type' => $network,
                                    'number' => $phone_number,
                                    'date' => $date,
                                    'time' => $time,
                                    'amount' => $amount,
                                    'order_id' => $order_id
                                );
                                if ($this->functions->addTransactionStatus($form_array)) {
                                    $response_arr['success'] = true;
                                    $response_arr['order_id'] = $order_id;
                                }
                            }
                        }
                    }
                }
            } else {
                if ($vtu_platform_shrt != "eminence") {

                    $response = $this->functions->vtu_curl($url, $use_post, $post_data = []);
                    // $response = json_encode(array('status' => 'ORDER_RECEIVED', 'orderid' => '5424425'));
                    // return $response;

                    if ($this->functions->isJson($response)) {
                        $response = json_decode($response);
                        if (is_object($response)) {
                            $status = $response->status;

                            if ($status == "ORDER_RECEIVED") {
                                $summary = "Debit Of " . $amount . " For Airtime Recharge";
                                if ($this->functions->debitUser($user_id, $amount, $summary)) {
                                    $order_id = $response->orderid;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'airtime',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            } else if ($status == "INVALID_AMOUNT") {
                                $response_arr['invalid_amount'] = true;
                            } else if ($status == "INVALID_RECIPIENT") {
                                $response_arr['invalid_recipient'] = true;
                            } else if ($status == "INSUFFICIENT_BALANCE") {
                                // $response_arr['invalid_recipient'] = true;
                                // echo "string";

                            } else if ($status == "INVALID_CREDENTIALS") {
                                // $response_arr['invalid_recipient'] = true;
                                // echo "string";

                            }
                        }
                    }
                } else {
                    $type = strtoupper(substr($vtu_platform, 9));
                    
                    $post_data = [
                        "phone" => $phone_number,
                        "amount" => $amount,
                        "network" => $eminence_ntwrk,
                        "type" => $type,
                    ];

                    // return $post_data;

                    $response = $this->functions->eminenceVtuCurl($url_2, true, $post_data);

                    // return $response;
                    if ($this->functions->isJson($response)) {
                        $response = json_decode($response);
                        if (is_object($response)) {
                            $status = $response->status;
                            $message = $response->message;

                            if ($status == true) {
                                $summary = "Debit Of " . $amount . " For Airtime Recharge";
                                if ($this->functions->debitUser($user_id, $amount, $summary)) {
                                    $order_id = $response->data->reference;
                                    $form_array = array(
                                        'user_id' => $user_id,
                                        'type' => 'airtime',
                                        'sub_type' => $network,
                                        'number' => $phone_number,
                                        'date' => $date,
                                        'time' => $time,
                                        'amount' => $amount,
                                        'order_id' => $order_id
                                    );
                                    if ($this->functions->addTransactionStatus($form_array)) {
                                        $response_arr['success'] = true;
                                        $response_arr['order_id'] = $order_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $response_arr['insuffecient_funds'] = true;
        }
    

        return back()->with('data',$response_arr);
    }

    public function request9mobileComboRecharge(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        
        $user = Auth::user();


        $user_id = $user->id;
        $response_arr = ['success' => false, 'insuffecient_funds' => false, 'order_id' => '', 'invalid_amount' => false, 'invalid_recipient' => false];

        $validationRules = [
            'network' => 'required|in:9mobile',
            'amount' => 'required|numeric|min:1000|max:50000',
            'recharge_type' => 'required|in:combo',
            'phone_number' => 'required|numeric|digits_between:6,15'

        ];

        $request->validate($validationRules);


        
        $user_total_amount = $this->functions->getUserTotalAmountByUse($user_id);
        $amount = $request->amount;
        if ($amount <= $user_total_amount) {
            $network = $request->network;

            $recharge_type = $request->recharge_type;
            $phone_number = $request->phone_number;

            $form_array = array(
                'user_id' => $user_id,
                'type' => 'airtime',
                'sub_type' => $network,
                'number' => $phone_number,
                'date' => $date,
                'time' => $time,
                'amount' => $amount,
                'order_id' => ""
            );
            if ($this->functions->addTransactionStatus($form_array)) {
                $form_array = array(
                    'user_id' => $user_id,
                    'number' => $phone_number,
                    'amount' => $amount,
                    'date' => $date,
                    'time' => $time
                );
                if ($this->functions->addComboRequest($form_array)) {
                    $summary = "Debit Of " . $amount . " For 9mobile Combo Airtime Recharge";
                    if ($this->functions->debitUser($user_id, $amount, $summary)) {
                        $response_arr['success'] = true;
                    }
                }
            }
        } else {
            $response_arr['insuffecient_funds'] = true;
        }
        

        return back()->with('data',$response_arr);
    }

    public function showAirtimeTopupPage(Request $request){
        $user = Auth::user();

        $props['user'] = $user;

        return Inertia::render('Vtu/AirtimeTopup');
    }
}
