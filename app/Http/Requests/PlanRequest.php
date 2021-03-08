<?php

namespace App\Http\Requests;

//use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class PlanRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $double_regex = config('constants.validation.double');

        return [
            'name' => 'required|max:255|unique:plans,name,NULL,id,deleted_at,NULL',
            'price' => ['required', $double_regex],
            'interval' => ['required', Rule::in([0, 1, 2, 3])],
            'interval_count' => 'required|integer',
            'currency_code' => 'nullable|max:3',
        ];
    }
}
