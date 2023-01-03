<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Functions\UsefulFunctions;
use App\Models\EarningToWalletHistory;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $props = [];
        $user = Auth::user();
        $user = User::find($user->id);
        $user_id = $user->id;
        $last_week_commission = EarningToWalletHistory::where('user_id', $user_id)->whereDate('created_at', '>=', Carbon::now()->subDays(7))->first();
        $props['last_week_commission'] = is_null($last_week_commission) ? 0.00 : $last_week_commission->amount;
        $props['total_upteam_earnings'] = $user->total_upteam_earnings;
        $last_account_statement = $user->accountStatement()->latest('id')->first();
        $props['account_created'] = (!is_null(Session::get('data'))) ? true : false;
        $props['monnify_details'] = $user->monnifyDetails()->first();
        $props['user'] = $user;
        $dummy_statement_arr = [
            'id' => 1,
            'user_id' => $user->id,
            'amount' => 0.00,
            'amount_before' => 0.00,
            'summary' => "",
        ];
        $props['last_account_statement'] = (!is_null($last_account_statement)) ? $last_account_statement : $dummy_statement_arr;
        $mlm_db_id = $this->functions->getUsersFirstMlmDbId($user->id);
        $props['mlm_db_id'] = $mlm_db_id;
        // $downline_arr = $this->functions->getDownlineArr1($mlm_db_id);
        $team_total = $this->functions->getTeamTotalOfUsersUnderMlmdbId($mlm_db_id);
        $left_team_total = $team_total['left'];
        $right_team_total = $team_total['right'];
        $props['left_team_total'] = $left_team_total;
        $props['right_team_total'] = $right_team_total;
        $props['downline_arr_num'] = $left_team_total + $right_team_total;
        
        // $props['total_amount_wthdrawn'] = $this->functions->getTotalAmountWithdrawnByUser($user->id);

        $top_ten_earners = User::where('id','!=',10)->latest("total_earnings")->paginate(10);
        // return $top_ten_earners;
        $props['top_ten_earners'] = $top_ten_earners;

        if($user->is_admin == 1){
            $props['today_registered_users'] = (string) number_format($this->functions->getTotalNumberOfUsersRegisteredToday());
            $props['total_registerd_users'] = (string) number_format($this->functions->getTotalNumberOfRegisteredUsers());
            $props['total_amount_online_payment'] = (string) number_format($this->functions->getTotalAmountForOnlinePaymentMadeToday($user_id), 2);
            $props['total_amount_withdrawn_today'] = (string) number_format($this->functions->getTotalAmountWithdrawnToday($user_id), 2);

            
            
            $props['year'] = date("Y");

            $first_twenty_users = $this->functions->getLastTwentyUsersRegisteredUsers();
            $index = 0;
            $new_twenty_users_arr = array();
            if (is_array($first_twenty_users)) {
                foreach ($first_twenty_users as $row) {
                    $index++;
                    // $user_id = $row->id;

                    $name = $row->name;
                    $email = $row->email;
                    $created_at = $row->created_at;

                    $new_twenty_users_arr[] = array(
                        'index' => $index,
                        'name' => $name,
                        'email' => $email,
                        'time' => date('h:i:sa',strtotime($created_at))
                    );
                }
            }

            $props['first_twenty_users'] = $new_twenty_users_arr;
            return Inertia::render('Admin/AdminHomeView', $props);
        }else{
            return Inertia::render('HomeView', $props);
        }
        

    }

    public function showReferralLink(Request $request){
        $user = Auth::user();
        $props['user'] = $user;
        return Inertia::render('ReferralLink',$props);
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
