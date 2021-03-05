<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        $rule = '';
        if (isset($request['exp_year']) && $request['exp_year'] == date('Y'))
            $rule = '|gte:' . date('m');

        return [
            'type' => 'required',
            'card.number' => 'required|digits:16',
            'card.exp_month' => 'required|digits:2' . $rule,
            'card.exp_year' => 'required|digits:4|gte:' . date('Y'),
            'card.cvc' => 'required|digits:3'
        ];
    }
}
