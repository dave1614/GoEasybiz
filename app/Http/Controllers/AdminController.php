<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use App\Models\AccountCreditHistory;
use App\Models\AdminDebitUsersHistory;
use App\Models\AdminFundUsersHistory;
use App\Models\ComboRechargeVtu;
use App\Models\Country;
use App\Models\EasyLoan;
use App\Models\MlmDb;
use App\Models\MlmEarning;
use App\Models\TransferFundsHistory;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Models\WithdrawalRequest;
use App\Rules\CountryRule;
use App\Rules\PhoneEditProfRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    
    public function loadUsersEasyLoanHistoryPage(Request $request, User $user)
    {
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

        if ($history->count() > 0) {
            foreach ($history as $row) {
                $amount = $row->amount;
                $amount_paid = $row->amount_paid;
                $last_date_paid = $row->last_date_paid;
                $paid_off = $row->paid_off;
                $created_at = $row->created_at;
                $row->date = date("j M Y", strtotime($created_at));
                $row->last_date_paid = date("j M Y", strtotime($last_date_paid));

                $row->status = $paid_off == 1 ? 'Cleared' : 'Pending';
                $row->amount_owed = 0;
                if ($paid_off == 0) {
                    $start_date = $created_at;
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


        return Inertia::render("Admin/UsersLoanHistory", $props);
    }

    public function loadUsersEarningsToWalletHistoryPage(Request $request, User $user)
    {
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;

        $history = $user->earningToWalletHist();



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



        return Inertia::render("Admin/UsersEarningsToWalletHist", $props);
    }

    public function loadDownlineMembersPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;

        $history = MlmDb::where('mlm_db.id','!=', 1);


        $history = $history->join('users', 'mlm_db.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');

        

        $history = $history->addSelect('mlm_db.*');

        $history = $request->query('name') ? $history->where('users.name', 'like', '%' . $request->query('name') . '%') : $history;
        $history = $request->query('email') ? $history->where('users.email', 'like', '%' . $request->query('email') . '%') : $history;

        $history = $request->query('level') && $request->query('level') >= 1  ? $history->where('mlm_db.stage', $request->query('level')) : $history;


        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('mlm_db.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('mlm_db.created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("stage", "ASC")
            ->paginate($length)->withQueryString();


        if (is_object($history)) {
            $j = 0;
            foreach ($history as $row) {



                $j++;

                // $index = $j + (($page - 1) * $length);

                $id = $row->id;
                $user_id = $row->user_id;
                

                $date_created = $row->date_created;
                $time_created = $row->time_created;
                $stage = $row->stage;
                $level = $stage;
                $under = $row->under;
                $sponsor = $row->sponsor;
                $positioning = $row->positioning;
                
                $row->level_str = number_format($level);

                
                $placement_user_id = $this->functions->getMlmDbParamById("user_id", $under);
                
                $row->placement_name = $this->functions->getUserParamById("name", $placement_user_id);
                $row->placement_email = $this->functions->getUserParamById("email", $placement_user_id);
                $row->placement_name = $row->placement_name . " (" . $positioning . ")";
                // $row->placement_email = $row->placement_email . " (" . $positioning . ")";


                $sponsor_user_id = $this->functions->getMlmDbParamById("user_id", $sponsor);

                $row->sponsor_name = $this->functions->getUserParamById("name", $sponsor_user_id);
                $row->sponsor_email = $this->functions->getUserParamById("email", $sponsor_user_id);
                
                // $row->index = $index;
            }
        }

        // return $history;



        $props['history'] = $history;
        $props['length'] = $length;

        $props['name'] = $request->query('name') ? $request->query('name') : NULL;

        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        $props['level'] = $request->query('level') ? $request->query('level') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;

        $props['total_downline_num'] = number_format($this->functions->getTotalNumberOfDownlineInMlmSystem());
        $props['total_levels'] = number_format($this->functions->getNumberOfLevelsInMlmSystem());


        
        return Inertia::render('Admin/DownlineMembers', $props);
    }

    public function loadUsersVtuHistoryPage(Request $request, User $user)
    {

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


        return Inertia::render("Admin/UsersVTUHistory", $props);
    }

    public function loadAccountDeditHistoryPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;

        $history = AdminDebitUsersHistory::where('user_id', '!=', '0');


        $history = $history->join('users', 'admin_debit_users_history.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');
        $history = $history->addSelect('admin_debit_users_history.*');

        $history = $request->query('name') ? $history->where('users.name', 'like', '%' . $request->query('name') . '%') : $history;
        $history = $request->query('email') ? $history->where('users.email', 'like', '%' . $request->query('email') . '%') : $history;

        $history = $request->query('amount') ? $history->where('admin_debit_users_history.amount', 'like', '%' . $request->query('amount') . '%') : $history;


        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('admin_debit_users_history.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('admin_debit_users_history.created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();




        // return $history;



        $props['history'] = $history;
        $props['length'] = $length;

        $props['name'] = $request->query('name') ? $request->query('name') : NULL;

        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/AdminAccountDebitHistory", $props);
    }

    public function loadAdminAccountCreditHistoryPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;

        $history = AdminFundUsersHistory::where('user_id','!=','0');


        $history = $history->join('users', 'admin_fund_users_history.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');
        $history = $history->addSelect('admin_fund_users_history.*');

        $history = $request->query('name') ? $history->where('users.name', 'like', '%' . $request->query('name') . '%') : $history;
        $history = $request->query('email') ? $history->where('users.email', 'like', '%' . $request->query('email') . '%') : $history;

        $history = $request->query('amount') ? $history->where('admin_fund_users_history.amount', 'like', '%' . $request->query('amount') . '%') : $history;
        

        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('admin_fund_users_history.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('admin_fund_users_history.created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();




        // return $history;

        

        $props['history'] = $history;
        $props['length'] = $length;

        $props['name'] = $request->query('name') ? $request->query('name') : NULL;

        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/AdminAccountCreditHistory", $props);
    }

    public function loadAccountCreditHistoryPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;

        $history = AccountCreditHistory::where('user_id','!=','0');


        $history = $history->join('users', 'account_credit_history.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');
        $history = $history->addSelect('account_credit_history.*');

        $history = $request->query('name') ? $history->where('users.name', 'like', '%' . $request->query('name') . '%') : $history;
        $history = $request->query('email') ? $history->where('users.email', 'like', '%' . $request->query('email') . '%') : $history;

        $history = $request->query('amount') ? $history->where('account_credit_history.amount', 'like', '%' . $request->query('amount') . '%') : $history;
        $history = $request->query('payment_option') ? $history->where('account_credit_history.payment_option', 'like', '%' . $request->query('payment_option') . '%') : $history;
        $history = $request->query('reference') ? $history->where('account_credit_history.reference', 'like', '%' . $request->query('reference') . '%') : $history;

        $history = !empty($request->query('date')) && $request->query('date') != "" ? $history->where('account_credit_history.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $history;

        $history = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $history->whereBetween('account_credit_history.created_at', [$request->query('start_date'), $request->query('end_date')]) : $history;



        $history = $history->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();




        // return $history;

        

        $props['history'] = $history;
        $props['length'] = $length;

        $props['name'] = $request->query('name') ? $request->query('name') : NULL;

        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['payment_option'] = $request->query('payment_option') ? $request->query('payment_option') : NULL;
        $props['reference'] = $request->query('reference') ? $request->query('reference') : NULL;
        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/AccountCreditHistory", $props);
    }

    public function loadDataComboRechargeHistoryPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;



        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;

        $requests = ComboRechargeVtu::where('credited', 1)->where('amount', 'like', "%GB");


        $requests = $requests->join('users', 'combo_recharge_vtu.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');



        $requests = $requests->addSelect('combo_recharge_vtu.*');


        $requests = $request->query('name') ? $requests->where('users.name', 'like', '%' . $request->query('name') . '%') : $requests;
        $requests = $request->query('email') ? $requests->where('users.email', 'like', '%' . $request->query('email') . '%') : $requests;



        $requests = $request->query('amount') ? $requests->where('combo_recharge_vtu.amount', 'like', '%' . $request->query('amount') . '%') : $requests;
        $requests = $request->query('number') ? $requests->where('combo_recharge_vtu.number', 'like', '%' . $request->query('number') . '%') : $requests;

        $requests = !empty($request->query('date')) && $request->query('date') != "" ? $requests->where('combo_recharge_vtu.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $requests;

        $requests = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $requests->whereBetween('combo_recharge_vtu.created_at', [$request->query('start_date'), $request->query('end_date')]) : $requests;

        $requests = !empty($request->query('credited_date')) && $request->query('credited_date') != "" ? $requests->where('combo_recharge_vtu.credited_date', 'like', date("j M Y", strtotime($request->query('credited_date')))  . '%') : $requests;


        $requests = $requests->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();



        $props['requests'] = $requests;
        $props['length'] = $length;

        $props['name'] = $request->query('name') ? $request->query('name') : NULL;

        $props['email'] = $request->query('email') ? $request->query('email') : NULL;


        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;
        $props['number'] = $request->query('number') ? $request->query('number') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['credited_date'] = $request->query('credited_date') ? $request->query('credited_date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/DataComboHistory", $props);
    }

    public function loadDataComboRequestsPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;



        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;

        $requests = ComboRechargeVtu::where('credited', 0)->where('amount', 'like', "%GB");


        $requests = $requests->join('users', 'combo_recharge_vtu.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');



        $requests = $requests->addSelect('combo_recharge_vtu.*');


        $requests = $request->query('name') ? $requests->where('users.name', 'like', '%' . $request->query('name') . '%') : $requests;
        $requests = $request->query('email') ? $requests->where('users.email', 'like', '%' . $request->query('email') . '%') : $requests;



        $requests = $request->query('amount') ? $requests->where('combo_recharge_vtu.amount', 'like', '%' . $request->query('amount') . '%') : $requests;
        $requests = $request->query('number') ? $requests->where('combo_recharge_vtu.number', 'like', '%' . $request->query('number') . '%') : $requests;

        $requests = !empty($request->query('date')) && $request->query('date') != "" ? $requests->where('combo_recharge_vtu.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $requests;

        $requests = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $requests->whereBetween('combo_recharge_vtu.created_at', [$request->query('start_date'), $request->query('end_date')]) : $requests;



        $requests = $requests->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();



        $props['requests'] = $requests;
        $props['length'] = $length;

        $props['name'] = $request->query('name') ? $request->query('name') : NULL;

        $props['email'] = $request->query('email') ? $request->query('email') : NULL;


        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;
        $props['number'] = $request->query('number') ? $request->query('number') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/DataComboRequests", $props);
    }

    public function loadAirtimeComboRechargeHistoryPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;



        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;

        $requests = ComboRechargeVtu::where('credited', 1)->where('amount', 'not like', "%GB");


        $requests = $requests->join('users', 'combo_recharge_vtu.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');



        $requests = $requests->addSelect('combo_recharge_vtu.*');


        $requests = $request->query('name') ? $requests->where('users.name', 'like', '%' . $request->query('name') . '%') : $requests;
        $requests = $request->query('email') ? $requests->where('users.email', 'like', '%' . $request->query('email') . '%') : $requests;



        $requests = $request->query('amount') ? $requests->where('combo_recharge_vtu.amount', 'like', '%' . $request->query('amount') . '%') : $requests;
        $requests = $request->query('number') ? $requests->where('combo_recharge_vtu.number', 'like', '%' . $request->query('number') . '%') : $requests;

        $requests = !empty($request->query('date')) && $request->query('date') != "" ? $requests->where('combo_recharge_vtu.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $requests;

        $requests = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $requests->whereBetween('combo_recharge_vtu.created_at', [$request->query('start_date'), $request->query('end_date')]) : $requests;

        $requests = !empty($request->query('credited_date')) && $request->query('credited_date') != "" ? $requests->where('combo_recharge_vtu.credited_date', 'like', date("j M Y", strtotime($request->query('credited_date')))  . '%') : $requests;


        $requests = $requests->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();



        $props['requests'] = $requests;
        $props['length'] = $length;

        $props['name'] = $request->query('name') ? $request->query('name') : NULL;

        $props['email'] = $request->query('email') ? $request->query('email') : NULL;


        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;
        $props['number'] = $request->query('number') ? $request->query('number') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['credited_date'] = $request->query('credited_date') ? $request->query('credited_date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/AirtimeComboHistory", $props);
    }


    public function markComboRecordAsRecharged(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        
        $response_arr = ['success' => false];
        if ($request->id) {
            $id = $request->id;

            $combo_recharge = ComboRechargeVtu::find($id);
            $combo_recharge->credited = 1;
            $combo_recharge->credited_date = $date;
            $combo_recharge->credited_time = $time;

            $combo_recharge->save();

            
            $response_arr['success'] = true;
            
        }


        return back()->with('data', $response_arr);
    } 

    public function loadAirtimeComboRequestsPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;

        

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;

        $requests = ComboRechargeVtu::where('credited', 0)->where('amount', 'not like', "%GB");


        $requests = $requests->join('users', 'combo_recharge_vtu.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.email as email');

       

        $requests = $requests->addSelect('combo_recharge_vtu.*');


        $requests = $request->query('name') ? $requests->where('users.name', 'like', '%' . $request->query('name') . '%') : $requests;
        $requests = $request->query('email') ? $requests->where('users.email', 'like', '%' . $request->query('email') . '%') : $requests;



        $requests = $request->query('amount') ? $requests->where('combo_recharge_vtu.amount', 'like', '%' . $request->query('amount') . '%') : $requests;
        $requests = $request->query('number') ? $requests->where('combo_recharge_vtu.number', 'like', '%' . $request->query('number') . '%') : $requests;

        $requests = !empty($request->query('date')) && $request->query('date') != "" ? $requests->where('combo_recharge_vtu.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $requests;

        $requests = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $requests->whereBetween('combo_recharge_vtu.created_at', [$request->query('start_date'), $request->query('end_date')]) : $requests;



        $requests = $requests->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();



        $props['requests'] = $requests;
        $props['length'] = $length;
        
        $props['name'] = $request->query('name') ? $request->query('name') : NULL;
        
        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        

        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;
        $props['number'] = $request->query('number') ? $request->query('number') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/AirtimeComboRequests", $props);
    }

    public function dismissAccountCreditWithdrawal(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        

        $response_arr = ['success' => false];
        if ($request->id) {
            $id = $request->id;

            
            $withdrawal_request = WithdrawalRequest::find($id);

            $withdrawal_request->dismissed = 1;
            $withdrawal_request->dismissed_date_time = $date . " " . $time;
            $withdrawal_request->save();

            $summary = "Refund For Withdrawal Request Dismissal";
            $amount = $withdrawal_request->amount + 100;
            if ($this->functions->creditUser($withdrawal_request->user_id, $amount, $summary)) {
                $response_arr['success'] = true;
            }
            
        }

        return back()->with('data', $response_arr);
    } 

    public function verifyAccountCreditWithdrawal(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        

        $response_arr = ['success' => false];

        if ($request->id) {
            $id = $request->id;
            
            $withdrawal_request = WithdrawalRequest::find($id);
            $user_id = $withdrawal_request->user_id;

            $withdrawal_request->debited = 1;
            $withdrawal_request->debited_date_time = $date . " " . $time;
            $withdrawal_request->save();

            $steps = 24;

            $charge_id = 15;
            $summary = 'Upteam Earning';
            $income = $this->functions->getWithdrawalEarningAmt();



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

            
            if ($this->functions->addWithrawalHistory($withdrawal_request->user_id, $withdrawal_request->amount)) {
                $response_arr['success'] = true;
            }
            
        }


        return back()->with('data', $response_arr);
    } 
    

    public function loadAccountWithdrawalRequestsPage(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;

        $role_prop = $request->query("role");
        $role_prop = empty($role_prop) ? 'pending' : $role_prop;


        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        
        $requests = WithdrawalRequest::where(function ($query) use ($role_prop) {

            if ($role_prop == "dismissed") {
                $query->where('debited', 0)
                ->where('dismissed', 1);
            } else if ($role_prop == "debited") {
                $query->where('debited', 1)
                ->where('dismissed', 0);
            } else if ($role_prop == "pending") {
                $query->where('debited', 0)
                ->where('dismissed', 0);
            }else{
                $query->where('debited', 0)
                    ->where('dismissed', 0);
            }
        });


        $requests = $requests->join('users', 'withdrawal_request.user_id', '=', 'users.id')->addSelect('users.name as name')->addSelect('users.phone as phone')->addSelect('users.country_id as country_id')->addSelect('users.email as email');

        $requests = $requests->join('countries', 'users.country_id', '=', 'countries.id')->addSelect('countries.name as country_name');


        $requests = $requests->addSelect('withdrawal_request.*');


        $requests = $request->query('name') ? $requests->where('users.name', 'like', '%' . $request->query('name') . '%') : $requests;
        $requests = $request->query('phone') ? $requests->where('users.phone', 'like', '%' . $request->query('phone') . '%') : $requests;
        $requests = $request->query('email') ? $requests->where('users.email', 'like', '%' . $request->query('email') . '%') : $requests;
        $requests = $request->query('country_name') ? $requests->where('countries.name', 'like', '%' . $request->query('country_name') . '%') : $requests;




        $requests = $request->query('amount') ? $requests->where('withdrawal_request.amount', 'like', '%' . $request->query('amount') . '%') : $requests;

        $requests = !empty($request->query('date')) && $request->query('date') != "" ? $requests->where('withdrawal_request.date', 'like', date("j M Y", strtotime($request->query('date')))  . '%') : $requests;

        $requests = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $requests->whereBetween('withdrawal_request.created_at', [$request->query('start_date'), $request->query('end_date')]) : $requests;

        $requests = !empty($request->query('debited_date_time')) && $request->query('debited_date_time') != "" ? $requests->where('withdrawal_request.debited_date_time', 'like', date("j M Y", strtotime($request->query('debited_date_time')))  . '%') : $requests;

        $requests = !empty($request->query('dismissed_date_time')) && $request->query('dismissed_date_time') != "" ? $requests->where('withdrawal_request.dismissed_date_time', 'like', date("j M Y", strtotime($request->query('dismissed_date_time')))  . '%') : $requests;



        $requests = $requests->orderBy("id", "DESC")
        ->paginate($length)->withQueryString();



        $props['requests'] = $requests;
        $props['length'] = $length;
        $props['role'] = $role_prop;
        $props['name'] = $request->query('name') ? $request->query('name') : NULL;
        $props['phone'] = $request->query('phone') ? $request->query('phone') : NULL;
        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        $props['country_name'] = $request->query('country_name') ? $request->query('country_name') : NULL;

        $props['amount'] = $request->query('amount') ? $request->query('amount') : NULL;

        $props['date'] = $request->query('date') ? $request->query('date') : NULL;
        $props['debited_date_time'] = $request->query('debited_date_time') ? $request->query('debited_date_time') : NULL;
        $props['dismissed_date_time'] = $request->query('dismissed_date_time') ? $request->query('dismissed_date_time') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;


        // return $history;


        return Inertia::render("Admin/AccountWithdrawalRequests", $props);
    }

    public function loadUsersAdminDebitHistoryPage(Request $request, User $user)
    {

        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $history = $user->adminDebitHistory();

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


        return Inertia::render("Admin/UsersAdminDebitHistory", $props);
    }

    public function loadUsersAdminCreditHistoryPage(Request $request, User $user)
    {

        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $history = $user->adminCreditHistory();

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


        return Inertia::render("Admin/UsersAdminCreditHistory", $props);
    }

    public function loadUsersTransferHistoryPage(Request $request, User $user)
    {

        $props['user'] = $user;

        $role_prop = $request->query("role");
        $role_prop = empty($role_prop) ? 'all' : $role_prop;


        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $user_id = $user->id;
        $history = TransferFundsHistory::where(function ($query) use ($user_id, $role_prop) {

            if ($role_prop == "all") {
                $query->where('sender', $user_id)
                    ->orWhere('recepient', $user_id);
            } else if ($role_prop == "sender") {
                $query->where('sender', $user_id);
            } else if ($role_prop == "recepient") {
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


        return Inertia::render("Admin/UsersTransferHistory", $props);
    }

    public function loadUsersAccountStatementPage(Request $request, User $user){
        
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



        $wallet_statement = $wallet_statement->orderBy("id", "DESC")
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


        return Inertia::render("Admin/UsersAccountStatement", $props);
    }

    public function loadUsersAccountWithdrawalHistoryPage(Request $request, User $user){
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



        return Inertia::render("Admin/UsersWithdrawalHistory", $props);
    }
    public function loadUsersAccountCreditHistoryPage(Request $request, User $user){
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



        $history = $history->orderBy("id","DESC")
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



        return Inertia::render("Admin/UsersAccountCreditHistory", $props);
    }

    public function processDebitUser(Request $request, User $user)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $response_arr = ['success' => false];
        $rules = [
            'amount' => ['required', 'numeric']
        ];

        $request->validate($rules);

        $amount = $request->amount;
        $total_income = $user->total_income;
        $withdrawn = $user->withdrawn;

        // $wallet_balance = $total_income - $withdrawn;

        // if($amount <= $wallet_balance){
        
        $summary = "Admin Debit Of " . $amount;
        if ($this->functions->debitUser($user->id, $amount, $summary)) {
            $this->functions->addAdminDebitHistory($user->id, $amount, $date, $time);
            $response_arr['success'] = true;
            
        }

        return back()->with('data', $response_arr);
    }

    public function processCreditUser(Request $request, User $user){
        $date = date("j M Y");
        $time = date("h:i:sa");
        $response_arr = ['success' => false];
        $rules = [
            'amount' => ['required', 'numeric']
        ];

        $request->validate($rules);

        $amount = $request->amount;
        $summary = "Admin Credit Of " . $amount;

        if ($this->functions->creditUser($user->id, $amount, $summary)) {
            if ($this->functions->debitUser($user->id, 20, "Admin Credit Charge")) {
                $this->functions->addAdminCreditHistory($user->id, $amount, $date, $time);
                $response_arr['success'] = true;
            }
        }

        return back()->with('data', $response_arr);
    }

    public function getUserData(Request $request, User $user){
        
        $user->country_name = Country::find($user->country_id)->name;
        $response_arr = ['success' => true, 'user' => $user];
        return $response_arr;
        // return back()->with('data', $response_arr);
    }
        

    public function processEditUsersProfile(Request $request, User $user){
        
        $response_arr = ['success' => false, 'email_changed' => false];

        $rules = [
            'email' => 'required|string|email|max:255',
            'country' => ['required', new CountryRule],
            'name' => 'required|string|max:255',
            'phone' => ['required', 'numeric', 'min_digits:7', 'max_digits:15', new PhoneEditProfRule($request->country['id'], $user->id)],
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
        

        if ($user->email != $request->email) {
            $response_arr['email_changed'] = true;
            $user->newEmail($request->email);
        }

        $user->save();

        $response_arr['success'] = true;

        return back()->with('data', $response_arr);
    }

    public function loadAdminEditUserProfilePage(Request $request, User $user){
        
        $props = [];
        $real_countries = [];
        
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

            if($id == $user->country_id){
                $props['selected_country'] = $i;
            }
        }
       
        $props['user'] = $user;

        $props['countries'] = $real_countries;
        
        return Inertia::render('Admin/EditUserProfile',$props);
    }

    public function loadMembersListPage(Request $request){
        $user = Auth::user();
        
        $props['user'] = $user;

        $page = empty($page) ? 1 : $page;
        $length = $request->query("length");

        $length = empty($length) ? 10 : $length;


        $j = 0;
        $users = User::where('is_admin',0);

        $users = $users->join('countries', 'users.country_id', '=', 'countries.id')->addSelect('countries.name as country_name');
        $users = $users->addSelect('users.*');

        $users = $request->query('name') ? $users->where('users.name', 'like', '%' . $request->query('name') . '%') : $users;
        $users = $request->query('country') ? $users->where('users.country_id', $request->query('country')['id']) : $users;
        $users = $request->query('phone') ? $users->where('users.phone', 'like', '%' . $request->query('phone') . '%') : $users;
        $users = $request->query('email') ? $users->where('users.email', 'like', '%' . $request->query('email') . '%') : $users;
        $users = !empty($request->query('created_date')) && $request->query('created_date') != "" ? $users->where('users.created_date', 'like', date("j M Y", strtotime($request->query('created_date')))  . '%') : $users;

        $users = !empty($request->query('start_date')) && !empty($request->query('end_date')) != "" ? $users->whereBetween('users.created_at', [$request->query('start_date'), $request->query('end_date')]) : $users;



        $users = $users->orderBy("name", "ASC")
        ->paginate($length)->withQueryString();

        


        // return $users;

        
        $real_countries = [];
        
        $countries = Country::where('id', '!=', '0')->orderBy("name", "ASC")->get();

        $i = -1;
        foreach ($countries as $country) {
            $i++;
            $id = $country->id;
            $name = $country->name;
            $phone_code = $country->phone_code;

            $real_countries[] = [
                'id' => $id,
                'label' => $name . ' (' . $phone_code . ')',
            ];

            // $props['selected_country'] = !isset($props['selected_country']) && $id == $user->country_id = $i;
            if ($id == $user->country_id) {
                $props['selected_country'] = $i;
            }
        }
        

        $props['countries'] = $real_countries;
        $props['users'] = $users;
        $props['length'] = $length;
        $props['name'] = $request->query('name') ? $request->query('name') : NULL;
        $props['country'] = $request->query('country') ? $request->query('country') : $real_countries[$props['selected_country']];
        $props['phone'] = $request->query('phone') ? $request->query('phone') : NULL;
        $props['email'] = $request->query('email') ? $request->query('email') : NULL;
        $props['created_date'] = $request->query('created_date') ? $request->query('created_date') : NULL;
        $props['start_date'] = $request->query('start_date') ? $request->query('start_date') : NULL;
        $props['end_date'] = $request->query('end_date') ? $request->query('end_date') : NULL;



        return Inertia::render("Admin/MembersList", $props);
    }
}
