<?php

namespace App\Rules;

use App\Models\Region;
use Illuminate\Contracts\Validation\Rule;

class RegionRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $country_id;
    public function __construct($country_id)
    {
        $this->country_id = $country_id;
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

        if (Region::where('id', $value)->where('country_id', $this->country_id)->count() == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The region selected is invalid.';
    }
}
