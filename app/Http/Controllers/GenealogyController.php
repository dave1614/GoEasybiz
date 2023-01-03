<?php

namespace App\Http\Controllers;

use App\Functions\UsefulFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GenealogyController extends Controller
{
    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    

    public function showSponsorTree(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;
        $user_id = $user->id;

        
        $mlm_db_id = $this->functions->getUsersFirstMlmDbId($user_id);
        $package1 = 2;
        $downline_html = "";

        if ($this->functions->checkIfMlmDbIdBelongsToUser($mlm_db_id, $user_id)) {


            $downline_html .= '<div class="tf-tree example">';

            $level = 20;
            $parentID = $mlm_db_id;
            $stage = 1;

            // $left_num = $this->meetglobal_model->getTotalNoOfMlmAccountsUnderUserLeft($mlm_db_id);

            $user_id = $this->functions->getMlmDbParamById("user_id", $parentID);
            $logo = null;
            
            $full_name = $this->functions->getUserParamById("name", $user_id);
            $package = 2;
            $date_created = $this->functions->getMlmDbParamById("date_created", $parentID);
            $index = $this->functions->getMlmIdsIndexNumber($mlm_db_id);
            $phone_code = $this->functions->getUserParamById("phone_code", $user_id);
            $phone = $this->functions->getUserParamById("phone", $user_id);
            $full_phone_number = $phone_code . "" . $phone;
            if (is_null($logo)) {
                $logo = '/images/nophoto.jpg';
            } else {
                $logo = '/storage/images/' . $logo;
            }

            if ($package == 1) {
                $package = "basic";
            } else {
                $package = "business";
            }

            $downline_html .= '<ul>';
            $downline_html .= '<li>';
            $downline_html .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


            $downline_html .= '<img class="tree_icon" src="' . $logo . '">';
            $downline_html .= '<p class="demo_name_style">';
            
            $downline_html .= $full_name . "<br>  <span class='text-sm font-bold italic'>(" . $full_phone_number . ")<span>";



            $downline_html .= '</p>';

            $downline_html .= '</div>';





            $downline_html .= $this->functions->printSponsorTree($level, $parentID, $stage);
            $downline_html .= '</li>';
            $downline_html .= '</ul>';

            $downline_html .= '</div>';
        }

        $props['downline_html'] = $downline_html;


        return Inertia::render('Trees/SponsorTree', $props);
    }

    public function searchUsersGenealogy(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        if (isset($request->show_records) && isset($request->phone)) {
            $response_arr = array('success' => false, 'messages' => '', 'invalid_user' => true);

            $sear_phone = $request->phone;
            if ($this->functions->checkIfPhoneExists($sear_phone)) {
                $response_arr['invalid_user'] = false;
                $user_id = $this->functions->getUserIdByPhone($sear_phone);
                $mlm_db_id = $this->functions->getUsersFirstMlmDbId($user_id);
                $package1 = 2;


                if ($this->functions->checkIfMlmDbIdBelongsToUser($mlm_db_id, $user_id)) {
                    $response_arr['success'] = true;

                    $response_arr['messages'] .= '<div class="tf-tree example">';

                    $level = 20;
                    $parentID = $mlm_db_id;
                    $stage = 3;

                    // $left_num = $this->meetglobal_model->getTotalNoOfMlmAccountsUnderUserLeft($mlm_db_id);

                    $user_id = $this->functions->getMlmDbParamById("user_id", $parentID);
                    $logo = null;
                    
                    $full_name = $this->functions->getUserParamById("name", $user_id);
                    $package = 2;
                    $date_created = $this->functions->getMlmDbParamById("date_created", $parentID);
                    $index = $this->functions->getMlmIdsIndexNumber($mlm_db_id);
                    $phone_code = $this->functions->getUserParamById("phone_code", $user_id);
                    $phone = $this->functions->getUserParamById("phone", $user_id);
                    $full_phone_number = $phone_code . "" . $phone;
                    if (is_null($logo)) {
                        $logo = '/images/nophoto.jpg';
                    } else {
                        $logo = '/storage/images/' . $logo;
                    }

                    if ($package == 1) {
                        $package = "basic";
                    } else {
                        $package = "business";
                    }

                    $response_arr['messages'] .= '<ul>';
                    $response_arr['messages'] .= '<li>';
                    $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                    $response_arr['messages'] .= '<img class="tree_icon" src="' . $logo . '">';
                    $response_arr['messages'] .= '<div class="demo_name_style grid grid-cols-12 gap-2">';

                    $response_arr['messages'] .= "<div class='col-span-2'></div>";

                    $response_arr['messages'] .= "<p class='col-span-8 text-xs'>" . $full_name . "<br><span class='text-sm font-bold italic'>(" . $full_phone_number . ")</span></p>";



                    $response_arr['messages'] .= '<i onclick="goDownMlm(this,event,' . $mlm_db_id . ',' . $mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-down col-span-2 mt-2" style="cursor:pointer;"></i>';


                    $response_arr['messages'] .= '</div>';

                    $response_arr['messages'] .= '</div>';




                    $response_arr['messages'] .= $this->functions->printTree($package1, $mlm_db_id, $level, $parentID, $stage);
                    $response_arr['messages'] .= '</li>';
                    $response_arr['messages'] .= '</ul>';

                    $response_arr['messages'] .= '</div>';
                }
            }
            return json_encode($response_arr);
        }
    } 


    public function viewYourGenealogyTree(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        if (isset($request->show_records) && isset($request->mlm_db_id) && isset($request->package)) {
            $mlm_db_id = $request->mlm_db_id;
            $package1 = $request->package;
            $response_arr = array('success' => false, 'messages' => '');

            if ($this->functions->checkIfMlmDbIdBelongsToUser($mlm_db_id, $user_id)) {
                $response_arr['success'] = true;

                $response_arr['messages'] .= '<div class="tf-tree example">';

                $level = 20;
                $parentID = $mlm_db_id;
                $stage = 3;

                // $left_num = $this->meetglobal_model->getTotalNoOfMlmAccountsUnderUserLeft($mlm_db_id);

                $user_id = $this->functions->getMlmDbParamById("user_id", $parentID);
                $logo = null;
                
                $full_name = $this->functions->getUserParamById("name", $user_id);
                $package = 2;
                $date_created = $this->functions->getMlmDbParamById("date_created", $parentID);
                $index = $this->functions->getMlmIdsIndexNumber($mlm_db_id);
                $phone_code = $this->functions->getUserParamById("phone_code", $user_id);
                $phone = $this->functions->getUserParamById("phone", $user_id);
                $full_phone_number = $phone_code . "" . $phone;
                if (is_null($logo)) {
                    $logo = '/images/nophoto.jpg';
                } else {
                    $logo = '/storage/images/' . $logo;
                }

                if ($package == 1) {
                    $package = "basic";
                } else {
                    $package = "business";
                }

                $response_arr['messages'] .= '<ul>';
                $response_arr['messages'] .= '<li>';
                $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                $response_arr['messages'] .= '<img class="tree_icon" src="' . $logo . '">';
                $response_arr['messages'] .= '<div class="demo_name_style grid grid-cols-12 gap-2">';

                $response_arr['messages'] .= "<div class='col-span-2'></div>";

                $response_arr['messages'] .= "<p class='col-span-8 text-xs'>" . $full_name . "<br><span class='text-sm font-bold italic'>(" . $full_phone_number . ")</span></p>";

               


                $response_arr['messages'] .= '<i onclick="goDownMlm(this,event,' . $mlm_db_id . ',' . $mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-down col-span-2 mt-2" style="cursor:pointer;"></i>';


                $response_arr['messages'] .= '</div>';

                $response_arr['messages'] .= '</div>';




                $response_arr['messages'] .= $this->functions->printTree($package1, $mlm_db_id, $level, $parentID, $stage);
                $response_arr['messages'] .= '</li>';
                $response_arr['messages'] .= '</ul>';

                $response_arr['messages'] .= '</div>';
            }
            $response_arr = json_encode($response_arr);
            return $response_arr;
        }
    }  

    public function viewYourGenealogyTreeDown(Request $request)
    {
        $date = date("j M Y");
        $time = date("h:i:sa");
        $user = Auth::user();
        // return json_encode($post_data);

        $user_id = $user->id;



        if (isset($request->mlm_db_id) && isset($request->your_mlm_db_id) && isset($request->package)) {

            $mlm_db_id = $request->mlm_db_id;
            $package1 = $request->package;
            $your_mlm_db_id = $request->your_mlm_db_id;
            if ($this->functions->checkIfMlmDbIdBelongsToUser($your_mlm_db_id, $user_id)) {
                $response_arr = array('success' => false, 'messages' => '');


                $response_arr['success'] = true;

                $response_arr['messages'] .= '<div class="tf-tree example">';

                $level = 20;
                $parentID = $mlm_db_id;
                $stage = 3;
                $user_id = $this->functions->getMlmDbParamById("user_id", $parentID);
                $logo = null;
                
                $full_name = $this->functions->getUserParamById("name", $user_id);
                $package = 2;
                $index = $this->functions->getMlmIdsIndexNumber($mlm_db_id);
                $date_created = $this->functions->getMlmDbParamById("date_created", $parentID);
                $phone_code = $this->functions->getUserParamById("phone_code", $user_id);
                $phone = $this->functions->getUserParamById("phone", $user_id);
                $full_phone_number = $phone_code . "" . $phone;
                if (is_null($logo)) {
                    $logo = '/images/nophoto.jpg';
                } else {
                    $logo = '/storage/images/' . $logo;
                }

                if ($package == 1) {
                    $package = "basic";
                } else {
                    $package = "business";
                }

                $response_arr['messages'] .= '<ul>';
                $response_arr['messages'] .= '<li>';
                $response_arr['messages'] .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';



                $response_arr['messages'] .= '<img class="tree_icon" src="' . $logo . '">';
                $response_arr['messages'] .= '<div class="demo_name_style grid grid-cols-12 gap-2">';

                $response_arr['messages'] .= '<i onclick="goUpMlm(this,event,' . $mlm_db_id . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-up col-span-2 mt-2" style="cursor:pointer;"></i>';

                

                $response_arr['messages'] .= "<p class='col-span-8 text-xs'>" . $full_name . "<br><span class='text-sm font-bold italic'>(" . $full_phone_number . ")</span></p>";



                $response_arr['messages'] .= '<i onclick="goDownMlm(this,event,' . $mlm_db_id . ',' . $your_mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-down col-span-2 mt-2" style="cursor:pointer;"></i>';


                $response_arr['messages'] .= '</div>';


                $response_arr['messages'] .= '</div>';




                $response_arr['messages'] .= $this->functions->printTree($package1, $your_mlm_db_id, $level, $parentID, $stage);
                $response_arr['messages'] .= '</li>';
                $response_arr['messages'] .= '</ul>';

                $response_arr['messages'] .= '</div>';

                $response_arr = json_encode($response_arr);
                return $response_arr;
            }
        }
    } 

    public function showGenealogyTree(Request $request){
        $user = Auth::user();
        $props['user'] = $user;
        $user_id = $user->id;
        $mlm_db_id = $this->functions->getUsersFirstMlmDbId($user_id);
        $package1 = 2;
        $downline_html = "";

        if ($this->functions->checkIfMlmDbIdBelongsToUser($mlm_db_id, $user_id)) {


            $downline_html .= '<div class="tf-tree example">';

            $level = 20;
            $parentID = $mlm_db_id;
            $stage = 3;

            $user_id = $this->functions->getMlmDbParamById("user_id", $parentID);
            $logo = null;
            // $user_name = $this->functions->getUserParamById("user_name", $user_id);
            $full_name = $this->functions->getUserParamById("name", $user_id);
            $phone_code = $this->functions->getUserParamById("phone_code", $user_id);
            $phone = $this->functions->getUserParamById("phone", $user_id);
            $package = 2;
            $date_created = $this->functions->getMlmDbParamById("date_created", $parentID);
            $index = $this->functions->getMlmIdsIndexNumber($mlm_db_id);


            $full_phone_number = $phone_code . "" . $phone;
            if (is_null($logo)) {
                $logo = '/images/nophoto.jpg';
            } else {
                $logo = '/storage/images/' . $logo;
            }

            if ($package == 1) {
                $package = "basic";
            } else {
                $package = "business";
            }

            $downline_html .= '<ul>';
            $downline_html .= '<li>';
            $downline_html .= '<div class="tf-nc ' . $package . '" data-toggle="tooltip" data-html="true" title="">';


            $downline_html .= '<img class="tree_icon" src="' . $logo . '">';
            $downline_html .= '<div class="demo_name_style grid grid-cols-12 gap-2">';

            $downline_html .= "<div class='col-span-2'></div>";

            $downline_html .= "<p class='col-span-8 text-xs'>" . $full_name . "<br><span class='text-sm font-bold italic'>(" . $full_phone_number . ")</span></p>";



            $downline_html .= '<i onclick="goDownMlm(this,event,' . $mlm_db_id . ',' . $mlm_db_id . ')" data-package="' . $package1 . '" class="fas fa-arrow-down col-span-2 mt-2" style="cursor:pointer;"></i>';


            $downline_html .= '</div>';

            $downline_html .= '</div>';





            $downline_html .= $this->functions->printTree($package1, $mlm_db_id, $level, $parentID, $stage);
            $downline_html .= '</li>';
            $downline_html .= '</ul>';

            $downline_html .= '</div>';
        }

        $props['downline_html'] = $downline_html;


        return Inertia::render('Trees/GenealogyTree', $props);
    }

   
}
