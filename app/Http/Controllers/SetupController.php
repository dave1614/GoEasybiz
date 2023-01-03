<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\Facility;
use App\Models\LabFirstOfficer;
use App\Models\Officer;
use App\Models\Personnel;
use App\Models\SubDept;
use App\Models\User;
use Illuminate\Http\Request;
use Faker\Factory;
use App\Functions\UsefulFunctions;
use App\Models\Country;
use App\Models\EarningToWalletHistory;
use App\Models\EasyLoan;
use App\Models\MlmWeekly;
use App\Models\WithdrawalHistory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SetupController extends Controller
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    public function testMonnify(){
        //    return $this->functions->getMonnifyAccessToken();
        // return $this->functions->generateMonnifyAcctsForUser(11);
        
        $pending_loan = EasyLoan::where('user_id', 11)->where('paid_off', 0)->whereDate('created_at', '<=', Carbon::now()->subDays(28))
            ->first();

        
        // return $pending_loan;

        if($pending_loan){
            $created_at = $pending_loan->created_at;
            $amount = $pending_loan->amount;
            $amount_paid = $pending_loan->amount_paid;

            $start_date = $pending_loan->created_at;
            $start_date = date("j M Y", strtotime($start_date));
            $start_date = strtotime($start_date);
            $end_date = strtotime(date("j M Y"));



            $date_diff = ($end_date - $start_date) / 60 / 60 / 24;

            $weeks_num = $date_diff / 7;

            $weeks_num = (int) $weeks_num;
            $charged_weeks = $weeks_num - 4;
            $total_service_charge = 250 * $charged_weeks;

            $total_amount_payable = $amount + $total_service_charge;
            $total_amount_owed = $total_amount_payable - $amount_paid;
            echo $total_amount_owed;
        }
        // return $this->functions->isDecimal($weeks_num) ? 'decimal' : 'not decimal';
    }

    public function testReloadly(Request $request){
        // {
// 	"operatorId": "535",
// 	"amount": "5.00",
// 	"useLocalAmount": true,
// 	"customIdentifier": "This is example identifier 130",
// 	"recipientEmail": "peter@nauta.com.cu",
// 	"recipientPhone": {
// 		"countryCode": "GB",
// 		"number": "447951731337"
// 	},
// 	"senderPhone": {
// 		"countryCode": "CA",
// 		"number": "11231231231"
// 	}
// }

        $user = User::find(11);

        $phone_code = $user->phone_code;
        $phone = "07051942325";
        $country = Country::find($user->country_id);
        // return $country;
        $country_id = $country->id;
        $countryCode = $country->code;
        $operatorId = $this->functions->getReloadlyOperatorId($phone_code, $phone, $country_id);
        $amount = "100";
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

        $url = "https://topups.reloadly.com/topups";
        $use_post = true;
        $accept = 'application/com.reloadly.topups-v1+json';

        $response = $this->functions->reloadlyCurl($url, $use_post, $post_data, $accept);
        // return $response;
        if ($this->isJson($response)) {
            $response = json_decode($response);
            if (is_object($response)) {
                if (isset($response->status)) {
                    if($response->status && $response->transactionId){
                        
                    }
                }
            }
        }

        
    }

    public function testAirtime(Request $request){
        // $phone_code = "+234";
        // $phone = "08023216456";
        // return $this->functions->getNetworkForNumber($phone_code, $phone);
        $user = User::find(11);
        $form_array = [
            'phone_code' => $user->phone_code,
            'phone' => $user->phone,
            'amount' => 100,
            'user_id' => $user->id,
        ];
        // return $this->functions->sendAirtime($form_array);
    }

    public function testEmail(Request $request){
        $title = "Successful Prepaid Electricity Recharge";
        $message = "Your Prepaid Electricity Recharge Was Successful With The Following Details: <br>";
        $message .= "Order Id: <em class='text-primary'>TT4g3g3g</em><br>";
        $message .= "Meter Token: <em class='text-primary'>1111-2222-3333-4444-5555</em><br>";
        $message .= "Disco: <em class='text-primary'>Ikeja Electric</em><br>";
        $message .= "Meter No.: <em class='text-primary'>45062872259</em><br>";
        $message .= "Amount: <em class='text-primary'>4,000</em><br>";

        $this->functions->sendEmail("jagajaga@gmail.com", $title, $message);
    }

    public function addDataComboRequests(Request $request)
    {
        $faker = Factory::create();
        $users = User::all();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach ($users as $user) {
            
            for ($i = 0; $i < 100; $i++) {
                $amount = rand(10, 1000);
                $phone_number = rand(1000000, 9999999);

                $amt_arr = [
                    '1GB',
                    '2GB',
                    '3GB',
                    '4GB',
                    '5GB',
                    '6GB',
                    '7GB'
                ];
                // $summary = $faker->paragraph();

                $form_array = array(
                    'user_id' => $user->id,
                    'number' => $phone_number,
                    'amount' => $amt_arr[rand(0,6)],
                    'date' => $date,
                    'time' => $time
                );
                if ($this->functions->addComboRequest($form_array)) {
                    
                }
            }
        }
    }
    
    public function addairtimeComboRequests(Request $request)
    {
        $faker = Factory::create();
        $users = User::all();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach ($users as $user) {
            
            for ($i = 0; $i < 100; $i++) {
                $amount = rand(10, 1000);
                $phone_number = rand(1000000, 9999999);

                $amt_arr = [
                    '1000',
                    '1500',
                    '2000',
                    '2500',
                    '3000',
                    '3500',
                    '4000'
                ];
                // $summary = $faker->paragraph();

                $form_array = array(
                    'user_id' => $user->id,
                    'number' => $phone_number,
                    'amount' => $amt_arr[rand(0,6)],
                    'date' => $date,
                    'time' => $time
                );
                if ($this->functions->addComboRequest($form_array)) {
                    
                }
            }
        }
    }

    public function addWithdrawalRequesttHistory(Request $request)
    {
        $faker = Factory::create();
        $users = User::all();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach ($users as $user) {
            
            for ($i = 0; $i < 100; $i++) {
                $amount = rand(10, 1000);

                // $summary = $faker->paragraph();

                $banks_arr = [
                    'First Bank of Nigeria',
                    'United Bank For Africa',
                    'Stanbic IBTC',
                    'Fin Bank',
                    'Zenith Bank',
                    'Guarantee Trust Bank',
                ];

                $summary = "Withdrawal Of " . $amount;
                if ($this->functions->debitUser($user->id, $amount, $summary)) {
                    $summary = "Withdrawal Charge";
                    if ($this->functions->debitUser($user->id, 100, $summary)) {
                        $form_array = array(
                            'user_id' => $user->id,
                            'amount' => $amount,
                            'bank_name' => "011",
                            'real_bank_name' => $banks_arr[rand(0,5)],
                            'account_number' => rand(1000000, 9999999),
                            'account_name' => $faker->name(),
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

    public function addAdminDebitHistory(Request $request)
    {
        $faker = Factory::create();
        $users = User::all();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach ($users as $user) {
            for ($i = 0; $i < 100; $i++) {
                $amount = rand(10, 1000);

                // $summary = $faker->paragraph();

                $summary = "Admin Debit Of " . $amount;
                if ($this->functions->debitUser($user->id, $amount, $summary)) {
                    $this->functions->addAdminDebitHistory($user->id, $amount, $date, $time);
                    $response_arr['success'] = true;
                }
            }
        }
    }

    public function addAdminCreditHistory(Request $request)
    {
        $faker = Factory::create();
        $users = User::all();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach ($users as $user) {
            for ($i = 0; $i < 100; $i++) {
                $amount = rand(10, 1000);
                
                // $summary = $faker->paragraph();
                $summary = "Admin Credit Of " . $amount;

                if ($this->functions->creditUser($user->id, $amount, $summary)) {
                    if ($this->functions->debitUser($user->id, 20, "Admin Credit Charge")) {
                        $this->functions->addAdminCreditHistory($user->id, $amount, $date, $time);
                        $response_arr['success'] = true;
                    }
                }
            }
        }
    }


    public function addTransferHistory(Request $request)
    {
        $faker = Factory::create();
        $users = User::where('id','!=',10)->get();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach ($users as $user) {
            for ($i = 0; $i < 100; $i++) {
                $amount = rand(1, 100);
                $recepient_id = 10;
                // $summary = $faker->paragraph();
                // $this->functions->creditUser($user->id, $amount, $summary);

                // $this->functions->addNewAccountCreditHistory($form_array);
                $this->functions->transferFundsToUser($user->id, $recepient_id, $amount, $date, $time);
            }
        }
    }

    public function addWithdrawalHistory(Request $request)
    {
        $faker = Factory::create();
        $users = User::all();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach ($users as $user) {
            for ($i = 0; $i < 100; $i++) {
                $amount = rand(100, 10000);
                // $summary = $faker->paragraph();
                // $this->functions->creditUser($user->id, $amount, $summary);

                // $this->functions->addNewAccountCreditHistory($form_array);
                WithdrawalHistory::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'date' => $date,
                    'time' => $time,
                ]);
            }
        }
    }

    public function creditAllUsers(Request $request){
        $faker = Factory::create();
        $users = User::all();
        $date = date("j M Y");
        $time = date("h:i:sa");

        foreach($users as $user){
            for($i = 0; $i < 100; $i++){
                $amount = rand(100,10000);
                // $summary = $faker->paragraph();
                // $this->functions->creditUser($user->id, $amount, $summary);
                
                // $this->functions->addNewAccountCreditHistory($form_array);
                $summary = "Direct Credit Of " . $amount . " Using Instant Credit";
                if ($this->functions->creditUser($user->id, $amount, $summary)) {
                    $form_array = array(
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'date' => $date,
                        'time' => $time,
                        'payment_option' => 'providus',
                        'reference' => Str::random(10)
                    );
                    if ($this->functions->addNewAccountCreditHistory($form_array)) {
                        
                    }
                }
            }   
        }
    }

    public function setPhoneCodes(Request $request){
        // $ids_to_credit = $this->functions->getIdsToCreditUpteam(11, 24);
        // return var_dump($ids_to_credit);
        // for ($i = 0; $i < count($ids_to_credit); $i++) {
        //     echo $ids_to_credit[$i] . ',';
        // }

        // return true;
        $users = MlmWeekly::where('user_id','!=',10)->whereDate('week_start','<=', Carbon::now()->subDays(7))
            ->get();
        // return $users;
        if($users->count() > 0){
            foreach($users as $row){
                
                $this->functions->processUserWeeklyRout($row->user_id);
            }
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
        // $user_name = str_replace(".", "_", fake()->userName());
        // return $user_name;
        //100 facilities
        for($i = 0; $i < 50; $i++){
            $faker = Factory::create();
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST'); //default METHOD

            $structure = fake()->randomElement(['Laboratory', 'Hospital']);
            $user_name = str_replace(".", "_", fake()->userName());
            $myRequest->request->add([
                'type' => 'facility',
                'terms' => true,
                'email' => fake()->unique()->safeEmail(),
                'country' => 151,
                'region' => 2516,
                'name' => fake()->name(),
                'user_name' => $user_name,
                'password' => 'Dave1614..',
                'password_confirmation' => 'Dave1614..',
                'facility_name' => fake()->name() . ' ' . $structure,
                'structure' => $structure,
            ]);

            $register_contr = new RegisteredUserController;
            $register_contr->store($myRequest); 
        }

        //100 patients
        for ($j = 0; $j < 50; $j++) {
            $faker = Factory::create();
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST'); //default METHOD

            
            $user_name = str_replace(".", "_", fake()->userName());
            $myRequest->request->add([
                'type' => 'patient',
                'terms' => true,
                'email' => fake()->unique()->safeEmail(),
                'country' => 151,
                'region' => 2516,
                'name' => fake()->name(),
                'user_name' => $user_name,
                'password' => 'Dave1614..',
                'password_confirmation' => 'Dave1614..',
            ]);

            $register_contr = new RegisteredUserController;
            $register_contr->store($myRequest);
        }

        $users = User::all();
        $facilities = Facility::all();
        foreach($users as $user){
            $user_id = $user->id;
            $sub_dept_id = NULL;
            $personnel_id = NULL;
            $sub_dept_id = SubDept::where('dept_id', 1)->inRandomOrder()->first()->id;
            $type = '';
            if($user_id <= 25){
                $role = "sub_admin";
            }else if($user_id <= 50){
                $personnel_id = LabFirstOfficer::inRandomOrder()->first()->id;
                $type = "lab_first_officer";
                $role = "personnel";
            }else{
                $type = '';
                $personnel_id = Personnel::where('sub_dept_id', $sub_dept_id)->inRandomOrder()->first()->id;
                $role = "personnel";
            }

            foreach ($facilities as $facility) {
                $facility_id = $facility->id;

                Officer::create([
                    'facility_id' => $facility_id,
                    'user_id' => $user_id,
                    'dept_id' => 1,
                    'sub_dept_id' => ($type == '') ? $sub_dept_id : NULL,
                    'personnel_id' => $personnel_id,
                    'role' => $role,
                    'type' => $type,
                ]);
            }
        }
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
