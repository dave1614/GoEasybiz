<?php

namespace App\Rules;

use App\Functions\UsefulFunctions;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class SponsorPhoneRegistrRule implements Rule
{
    public $country_id;
    public UsefulFunctions $functions;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($country_id)
    {
        $this->country_id = $country_id;
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
        // $country = Country::find($this->country_id);

        // $phone_code = $this->functions->getPhoneCodeByCountry($country);

        return (User::where('country_id', $this->country_id)->where('phone', $value)->get()->count() > 0) ? true : false;
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Sponsors details are invalid.';
    }
}
