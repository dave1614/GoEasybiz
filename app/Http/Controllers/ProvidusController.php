<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use App\Models\MlmEarning;
use App\Models\MonnifyLog;
use App\Models\MonnifyPaymentDetail;
use App\Models\User;
use App\Models\UserMonnifyDetail;
use App\Models\VtuTransaction;
use Illuminate\Http\Request;

class ProvidusController extends Controller
{
    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }
    

    public function recieveMonnifyWebhooks(Request $req)
    {
        $json_post = file_get_contents('php://input');

        $post = json_decode($json_post);
        $date = date("j M Y");
        $time = date("h:i:sa");
        

        MonnifyLog::create([
            'log' => $json_post,
        ]);

        if (isset($post->transactionReference) && isset($post->paymentReference) && isset($post->amountPaid) && isset($post->totalPayable) && isset($post->settlementAmount) && isset($post->paidOn) && isset($post->paymentStatus) && isset($post->paymentDescription) && isset($post->transactionHash) && isset($post->currency) && isset($post->paymentMethod) && isset($post->product) && isset($post->product->type) && isset($post->product->reference) && isset($post->accountDetails) && isset($post->accountDetails->accountName) && isset($post->accountDetails->accountNumber) && isset($post->accountDetails->bankCode) && isset($post->accountDetails->amountPaid)) {

            
            $accountReference = $post->product->reference;
            $transactionReference = $post->transactionReference;
            $paymentReference = $post->paymentReference;
            $amountPaid = $post->amountPaid;
            $totalPayable = $post->totalPayable;
            $settlementAmount = $post->settlementAmount;
            $paidOn = $post->paidOn;
            $paymentStatus = $post->paymentStatus;
            $paymentDescription = $post->paymentDescription;
            $transactionHash = $post->transactionHash;
            $currency = $post->currency;
            $paymentMethod = $post->paymentMethod;
            $payment_accountName = $post->accountDetails->accountName;
            $payment_accountNumber = $post->accountDetails->accountNumber;
            $payment_bankCode = $post->accountDetails->bankCode;
            $payment_amountPaid = $post->accountDetails->amountPaid;

  
  
            // echo "string";
            $response_arr = array();

            $headers = getallheaders();
            // $X_Auth_Signature = $headers['X-Patreon-Event'];
            // $X_Patreon_Signature = $headers['X-Patreon-Signature'];

            // return ($this->functions->checkIfMonnifyTransactionIsValid($transactionReference));
            if ($this->functions->checkIfThisMonnifyAccountReferenceIsValid($accountReference) && !$this->functions->checkIfThisMonnifyWebhookIsDuplicate($transactionReference) && $this->functions->checkIfMonnifyTransactionIsValid($transactionReference)) {
                $user_monnify_details = UserMonnifyDetail::where('account_reference', $accountReference)->first();
                // return var_dump($user_monnify_details);
                // $user = $user_monnify_details->getUser()->get();
                $user = User::find($user_monnify_details->user_id);
                // return $user;
                

                

                $new_payment = MonnifyPaymentDetail::create([
                    'user_id' => $user->id,
                    'account_reference' => $accountReference,
                    'transaction_reference' => $transactionReference,
                    'payment_reference' => $paymentReference,
                    'amount_paid' => $amountPaid,
                    'total_payable' => $totalPayable,
                    'settlement_amount' => $settlementAmount,
                    'paid_on' => $paidOn,
                    'payment_status' => $paymentStatus,
                    'payment_description' => $paymentDescription,
                    'transaction_hash' => $transactionHash,
                    'currency' => $currency,
                    'payment_method' => $paymentMethod,
                    'payment_accountName' => $payment_accountName,
                    'payment_accountNumber' => $payment_accountNumber,
                    'payment_bankCode' => $payment_bankCode,
                    'payment_amountPaid' => $payment_amountPaid,
                ]);

                

                $amount_to_credit = $amountPaid;

                $amount_to_credit = ($user->created == 1) ? $amount_to_credit - 30 : $amount_to_credit;
                
                
                $summary = "Direct Credit Of " . $amount_to_credit . " Using Instant Credit";
                if($user->created == 1){
                    $this->functions->creditUser($user->id, $amount_to_credit, $summary);
                }else{
                    $this->functions->creditUsersRegistrationAmount($user->id, $amount_to_credit);
                }

                $form_array = array(
                    'user_id' => $user->id,
                    'amount' => $amount_to_credit,
                    'date' => $date,
                    'time' => $time,
                    'payment_option' => 'monnify',
                    'reference' => $transactionReference
                );
                if ($this->functions->addNewAccountCreditHistory($form_array)) {
                    
                    $new_payment->amount_credited = $amount_to_credit;
                    $new_payment->save();
                }
                
            
            } 
        }
    }

    public function recievePayscribeWebhooks(Request $request)
    {
        $json_post = file_get_contents('php://input');

        $post = json_decode($json_post);
        // $post = (Object) $req->input();

        $date = date("j M Y");
        $time = date("h:i:sa");
        $this->functions->addMinifyAccountWebhookJsonData($json_post, $date, $time);

        if (isset($post->status) && $post->status == true && isset($post->event_type)) {

            $transaction_status = $post->transaction_status;
            $trans_id = $post->trans_id;
            $event_type = $post->event_type;
            $order_id = $trans_id;
            $amount_given = $post->amount;

            if ($event_type == "AIRTIME_TO_CASH_TRANSFER" && $transaction_status == "approve") {


                $date = date("j M Y");
                $time = date("h:i:sa");


                if ($this->functions->checkIfTransactionIdIsValidPayscribeAirtimeToWallet($trans_id)) {
                    $user_id = $this->functions->getVtuTransactionParamByOrderId("user_id", $order_id);
                    
                    $amount_requested = $this->functions->getVtuTransactionParamByOrderId("amount", $order_id);
                    $approved_status = $this->functions->getVtuTransactionParamByOrderId("approved", $order_id);
                    $table_id = $this->functions->getVtuTransactionParamByOrderId("id", $order_id);
                    // echo "string";
                    if ($approved_status == 0) {

                        $amount_to_credit = (0.05 * $amount_requested);
                        $amount_to_credit = $amount_given - $amount_to_credit;

                        $admin_amount = (0.05 * $amount_requested);

                        $summary = " Credit Of " . $amount_to_credit . " For Airtime To Wallet Transfer";
                        if ($this->functions->creditUser($user_id, $amount_to_credit, $summary)) {

                            
                            $vtu_transaction = VtuTransaction::find($table_id);
                            $vtu_transaction->approved = 1;
                            $vtu_transaction->save();
                            
                            $form_array = array(
                                'user_id' => $user_id,
                                
                                'amount_requested' => $amount_requested,
                                'amount_credited' => $amount_to_credit,
                                'admin_amount' => $admin_amount,
                                'date' => $date . " " . $time,
                            );
                            $this->functions->addAirtimeToWalletRecord($form_array);
                            
                        }
                    }
                }
            }
        }
    }

    public function recieveProvidusWebhooks(Request $req)
    {
        $json_post = file_get_contents('php://input');

        $post = json_decode($json_post);
        $date = date("j M Y");
        $time = date("h:i:sa");
        $this->functions->addMinifyAccountWebhookJsonData($json_post, $date, $time);

        if (isset($post->sessionId) && isset($post->accountNumber) && isset($post->tranRemarks) && isset($post->transactionAmount) && isset($post->settledAmount) && isset($post->feeAmount) && isset($post->vatAmount) && isset($post->currency) && isset($post->initiationTranRef) && isset($post->settlementId) && isset($post->sourceAccountNumber) && isset($post->sourceAccountName) && isset($post->sourceBankName) && isset($post->channelId) && isset($post->tranDateTime)) {
            
            $sessionId = $post->sessionId;
            $accountNumber = $post->accountNumber;
            $tranRemarks = $post->tranRemarks;
            $transactionAmount = $post->transactionAmount;
            $settledAmount = $post->settledAmount;
            $feeAmount = $post->feeAmount;
            $vatAmount = $post->vatAmount;
            $currency = $post->currency;
            $initiationTranRef = $post->initiationTranRef;
            $settlementId = $post->settlementId;
            $sourceAccountNumber = $post->sourceAccountNumber;
            $sourceAccountName = $post->sourceAccountName;
            $sourceBankName = $post->sourceBankName;
            $channelId = $post->channelId;
            $tranDateTime = $post->tranDateTime;

            $response_arr = array();

            $headers = getallheaders();
            // $X_Auth_Signature = $headers['X-Patreon-Event'];
            // $X_Patreon_Signature = $headers['X-Patreon-Signature'];
            $client_id = env('PROVIDUS_CLIENTID');
            $client_secret = env('PROVIDUS_CLIENTSECRET');
            $auth_signature = hash("sha512", $client_id . ":" . $client_secret);

            if (isset($headers['X-Auth-Signature']) && strtolower($headers['X-Auth-Signature']) == $auth_signature && $this->functions->checkIfThisProvidusAccountNumberIsValid($accountNumber) && !$this->functions->checkIfThisProvidusWebhookIsDuplicate($settlementId)) {
                $response_arr = array(
                    'requestSuccessful' => true,
                    'sessionId' => $sessionId,
                    'responseMessage' => 'success',
                    'responseCode' => '00'
                );

                $form_array = array(
                    'sessionId' => $sessionId,
                    'accountNumber' => $accountNumber,
                    'tranRemarks' => $tranRemarks,
                    'transactionAmount' => $transactionAmount,
                    'settledAmount' => $settledAmount,
                    'feeAmount' => $feeAmount,
                    'vatAmount' => $vatAmount,
                    'currency' => $currency,
                    'initiationTranRef' => $initiationTranRef,
                    'settlementId' => $settlementId,
                    'sourceAccountNumber' => $sourceAccountNumber,
                    'sourceAccountName' => $sourceAccountName,
                    'sourceBankName' => $sourceBankName,
                    'channelId' => $channelId,
                    'tranDateTime' => $tranDateTime
                );

                $this->functions->addProvidusTransactionRecord($form_array);

                $amount_to_credit = $transactionAmount;

                
                
                

                // echo $amount_to_credit;

                

                $user = User::where('providus_account_number', $accountNumber)->first();

                if (!is_null($user)) {
                    
                    $created = $user->created;
                    $user_id = $user->id;
                        
                    

                    if ($created == 1) {
                        // $amount_to_credit = ($transactionAmount > 5000) ? $transactionAmount - 25 : $transactionAmount - 25;
                        if($transactionAmount <= 9000){
                            $amount_to_credit = $transactionAmount - 25;
                        }else if($transactionAmount > 9000){
                            $amount_to_credit = $transactionAmount - 75;
                        }
                        $summary = "Direct Credit Of " . $amount_to_credit . " Using Instant Credit";
                        if ($this->functions->creditUser($user_id, $amount_to_credit, $summary)) {
                            $form_array = array(
                                'user_id' => $user_id,
                                'amount' => $amount_to_credit,
                                'date' => $date,
                                'time' => $time,
                                'payment_option' => 'providus',
                                'reference' => $settlementId
                            );
                            if ($this->functions->addNewAccountCreditHistory($form_array)) {
                                $form_array = array(
                                    'user_id' => $user_id,
                                    'amount_credited' => $amount_to_credit
                                );

                                $this->functions->updateProvidusTransactionBySettlementId($form_array, $settlementId);
                            }
                        }
                    } else {
                        if ($this->functions->creditUsersRegistrationAmount($user_id, $amount_to_credit)) {
                        }
                    }

                    
                    
                    $steps = 24;
                    
                    $charge_id = 14;
                    $summary = 'Upteam Earning';
                    $income = $this->functions->getFundingEarningAmt();
                    
                    

                    $ids_to_credit = $this->functions->getIdsToCreditUpteam($user_id, $steps);
                    $ids_to_credit_num = count($ids_to_credit);
                    for ($i = 0; $i < count($ids_to_credit); $i++) {
                        $placements_user_id = $ids_to_credit[$i];
                        $placements_mlm_db_id = $this->functions->getUsersFirstMlmDbId($placements_user_id);


                        MlmEarning::create([
                            'user_id' => $placements_user_id,
                            'mlm_db_id' => $placements_mlm_db_id,
                            'type' => $charge_id,
                            'amount' => $income,
                            'date' => $date,
                            'time' => $time,
                        ]);



                        $this->functions->creditUser($placements_user_id, $income, $summary);
                    }
                    
                }
            } else if ($this->functions->checkIfThisProvidusWebhookIsDuplicate($settlementId)) {
                $response_arr = array(
                    'requestSuccessful' => true,
                    'sessionId' => $sessionId,
                    'responseMessage' => 'duplicate transaction',
                    'responseCode' => '01'
                );
            } else if (!isset($headers['X-Auth-Signature']) || $headers['X-Auth-Signature'] != hash("sha512", "dGVzdF9Qcm92aWR1cw==:29A492021F4B709A8D1152C3EF4D32DC5A7092723ECAC4C511781003584B48873CCBFEBDEAE89CF22ED1CB1A836213549BC6638A3B563CA7FC009BEB3BC30CF8") || !$this->functions->checkIfThisProvidusAccountNumberIsValid($accountNumber)) {
                $response_arr = array(
                    'requestSuccessful' => true,
                    'sessionId' => $sessionId,
                    'responseMessage' => 'rejected transaction',
                    'responseCode' => '02'
                );
            }

            echo json_encode($response_arr);
        }
    }
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
