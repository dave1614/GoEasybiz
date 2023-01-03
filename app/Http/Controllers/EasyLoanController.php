<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use App\Models\EarningToWalletHistory;
use App\Models\EasyLoan;
use App\Models\MlmEarning;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EasyLoanController extends Controller
{
    public $functions;
    public $min_req_refs = 5;
    public $min_earned_amt = 1000;
    public $administration_fee = 250;
    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    public function showLoanHitsoryPage(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $history = EasyLoan::where('user_id', $user->id);

        $history = $request->query('amount') ? $history->where('amount', 'like', '%' . $request->query('amount') . '%') : $history;
        $history = $request->query('amount_paid') ? $history->where('amount_paid', 'like', '%' . $request->query('amount_paid') . '%') : $history;
        
        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('last_date_paid')) && $request->query('last_date_paid') != "" ? $history->where('date', 'like', date("Y-m-d", strtotime($request->query('last_date_paid')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();

        if($history->count() > 0){
            foreach($history as $row){
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;
                $last_date_paid = $row->last_date_paid;
                $paid_off = $row->paid_off;
                $created_at = $row->created_at;
                $row->date = date("j M Y", strtotime($created_at));
                $row->last_date_paid = is_null($row->last_date_paid) ? NULL : date("j M Y", strtotime($last_date_paid));

                $row->status = $paid_off == 1 ? 'Cleared' : 'Pending';
                $row->amount_owed = 0;
                if($paid_off == 0){
                    $start_date = $created_at;
                    $start_date = date("j M Y", strtotime($start_date));
                    $start_date = strtotime($start_date);
                    $end_date = strtotime(date("j M Y"));



                    $date_diff = ($end_date - $start_date) / 60 / 60 / 24;

                    $weeks_num = $date_diff / 7;

                    $weeks_num = (int) $weeks_num;
                    $charged_weeks = $weeks_num - 4;
                    if($charged_weeks < 0){
                        $charged_weeks = 0;
                    }
                    $total_service_charge = 250 * $charged_weeks;

                    $total_amount_payable = $amount + $total_service_charge;
                    $row->amount_owed = $total_amount_payable - $amount_paid;


                    

                }
            }
        }

        // return $history;

        $props['history'] = $history;
        $props['length'] = $length;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;
        $props['amount_paid'] = $request->query('amount_paid') ? $request->query('amount_paid') : NULL;
        
        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['last_date_paid'] = $request->query('last_date_paid') ? $request->query('last_date_paid') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;


        // return $wallet_statement;


        return Inertia::render("Loan/LoanHistory", $props);
    }

    public function processApplication(Request $request){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();

        $response_arr = ['success' => false,'eligible' => false,'reasons' => [], 'min_loan_amt' => 0, 'max_loan_amt' => 0, 'normal_range' => false];

        $user_id = $user->id;

        

        $refs_num = $this->functions->getTtalReferralNumsByUser($user_id);


        $earning = EarningToWalletHistory::where('user_id', $user_id)->whereDate('created_at', '>=', Carbon::now()->subDays(7))
        ->first();
        $pending_loans = $this->functions->getPendingLoansNumForUser($user_id);

        $amt_earned = !is_null($earning) ? $earning->amount : 0;

        $pending_loans = $pending_loans == 0 ? false : true;
        $not_enough_referrals = $refs_num >= $this->min_req_refs ? false : true;
        $not_earned_past_week = !is_null($earning) ? false : true;
        $amt_earnd_not_enough = $amt_earned >= $this->min_earned_amt ? false : true;
        $wallet_not_enough = $user->total_income - $user->withdrawn >= $this->administration_fee ? false : true;
        $amt_earned = $amt_earned;


        $reasons = [];

        if ($pending_loans) {
            $reasons[] = "You still have pending loans. You cannot take another one till you've cleared your previous ones.";
        }

        if ($not_enough_referrals) {
            $reasons[] = "You need at least $this->min_req_refs referrals. You have $refs_num currently";
        }

        if ($not_earned_past_week) {
            $reasons[] = "You did not earn last week.";
        }

        if ($amt_earnd_not_enough) {
            $reasons[] = "You earned ₦$amt_earned. You need at least ₦" . number_format($this->min_earned_amt);
        }

        if ($wallet_not_enough) {
            $reasons[] = "You need at least ₦" . number_format($this->administration_fee) . " in your wallet for administration fee";
        }

        $response_arr['reasons'] = $reasons;

        $eligible = (count($reasons) == 0) ? true : false;



        $min_loan_amt = (count($reasons) == 0) ? 1000 : 0;
        $max_loan_amt = 0;
        if (!$amt_earnd_not_enough) {
            $max_amt = (int) ($amt_earned / 1000);
            $max_loan_amt = $max_amt * 1000;
        }

        $max_loan_amt = $max_loan_amt > 200000 ? 200000 : $max_loan_amt;
        $response_arr['min_loan_amt'] = $min_loan_amt;
        $response_arr['max_loan_amt'] = $max_loan_amt;

        $validationRules = [

            'amount' => 'required|numeric'
        ];

        $request->validate($validationRules);

        if($eligible){
            $response_arr['eligible'] = true;
            $amount = $request->amount;
            if($amount >= $min_loan_amt && $amount <= $max_loan_amt){
                $response_arr['normal_range'] = true;

                EasyLoan::create([
                    'user_id' => $user_id,
                    'amount' => $amount,
                    
                ]);
                $summary = "Easy loan administration fee";
                $this->functions->debitUser($user_id,$this->administration_fee,$summary);
                $summary = "Easy loan credit";
                $this->functions->creditUser($user_id,$amount,$summary);

                $steps = $this->functions->getEasyLoanStepsNum();
                
                
                $charge_id = 16;
                $summary = 'Upteam Income';
                $amt_to_share = $this->functions->getEasyLoanAmtToShare();
                
                

                $ids_to_credit = $this->functions->getIdsToCreditUpteam($user_id, $steps);
                $ids_to_credit_num = count($ids_to_credit);
                for ($i = 0; $i < count($ids_to_credit); $i++) {
                    $placements_user_id = $ids_to_credit[$i];
                    $placements_mlm_db_id = $this->functions->getUsersFirstMlmDbId($placements_user_id);


                    MlmEarning::create([
                        'user_id' => $placements_user_id,
                        'mlm_db_id' => $placements_mlm_db_id,
                        'type' => $charge_id,
                        'amount' => $amt_to_share,
                        'date' => $date,
                        'time' => $time,
                    ]);

                    $this->functions->creditWeeklyEarning($placements_user_id, $amt_to_share, 'upteam');
                }
                $response_arr['success'] = true;
            }
        }

        


        return back()->with('data',$response_arr);
    }

    public function showApplicationPage(Request $request){
        $user = Auth::user();
        $props = [];

        $user_id = $user->id;

        $refs_num = $this->functions->getTtalReferralNumsByUser($user_id);
        

        $earning = EarningToWalletHistory::where('user_id', $user_id)->whereDate('created_at', '>=', Carbon::now()->subDays(7))
        ->first();
        $pending_loans = $this->functions->getPendingLoansNumForUser($user_id);

        $amt_earned = !is_null($earning) ? $earning->amount : 0;

        $props['pending_loans'] = $pending_loans == 0 ? false : true;
        $props['not_enough_referrals'] = $refs_num >= $this->min_req_refs ? false : true;
        $props['not_earned_past_week'] = !is_null($earning) ? false : true;
        $props['amt_earnd_not_enough'] = $amt_earned >= $this->min_earned_amt ? false : true;
        $props['wallet_not_enough'] = $user->total_income - $user->withdrawn >= $this->administration_fee ? false : true;
        $props['amt_earned'] = $amt_earned;


        $reasons = [];

        if ($props['pending_loans']) {
            $reasons[] = "You still have pending loans. You cannot take another one till you've cleared your previous ones.";
        }

        if($props['not_enough_referrals']){
            $reasons[] = "You need at least $this->min_req_refs referrals. You have $refs_num currently";
        }

        if($props['not_earned_past_week']){
            $reasons[] = "You did not earn last week.";
        }

        if ($props['amt_earnd_not_enough']) {
            $reasons[] = "You earned ₦$amt_earned. You need at least ₦" . number_format($this->min_earned_amt);
        }

        if ($props['wallet_not_enough']) {
            $reasons[] = "You need at least ₦" . number_format($this->administration_fee) . " in your wallet for administration fee";
        }

        $props['eligible'] = (count($reasons) == 0) ? true : false;

        

        $props['min_loan_amt'] = (count($reasons) == 0) ? 1000 : 0;
        $props['max_loan_amt'] = 0;
        if(!$props['amt_earnd_not_enough']){
            $max_amt = (int) ($amt_earned / 1000);
            $props['max_loan_amt'] = $max_amt * 1000;
        }

        $props['max_loan_amt'] = $props['max_loan_amt'] > 200000 ? 200000 : $props['max_loan_amt'];

        // return $props['max_loan_amt'];
       

        $props['reasons'] = $reasons;

        return Inertia::render('Loan/ApplyPage',$props);
    }
}
