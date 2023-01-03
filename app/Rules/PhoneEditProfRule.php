<?php

namespace App\Rules;

use App\Functions\UsefulFunctions;
use App\Models\Country;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class PhoneEditProfRule implements Rule
{
    public $message;
    public $country_id;
    public UsefulFunctions $functions;
    public $user_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($country_id, $user_id)
    {
        
        $this->country_id = $country_id;
        $this->user_id = $user_id;
        $this->functions = new UsefulFunctions();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user_check = User::where('country_id', $this->country_id)->where('phone', $value)->first();
        if(!is_null($user_check)){
            if($user_check->id == $this->user_id){
                return true;
            }else{
                $this->message = 'This phone number has been taken.';
                return false;
            }
        }else{

            
            $phone = $value;
            $phone_code = Country::find($this->country_id)->phone_code;
            $int_num = $this->functions->returnInterNumber($phone_code, $phone);
            $country_code = Country::find($this->country_id)->code;
            $url = "https://topups.reloadly.com/operators/auto-detect/phone/" . $int_num . "/countries/" . $country_code;
            $use_post = false;
            $accept = 'application/com.reloadly.topups-v1+json';

            $response = $this->functions->reloadlyCurl($url, $use_post, $post_data = [], $accept);
            if ($this->functions->isJson($response)) {
                $response = json_decode($response);
                if (is_object($response)) {
                    if (isset($response->operatorId)) {
                        return true;
                    } else {
                        $this->message = 'Phone number entered is invalid.';
                        return false;
                    }
                }
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // return 'This phone number has been taken.';
        return $this->message;
    }
}
