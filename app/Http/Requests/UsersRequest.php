<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
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
        return [
            'name' => 'required | regex:/^[a-zA-Z_ ]*$/ | max:191',
            'email' => 'required|max:191|email',
            'password' => 'required |nullable| min:6 | max:191',
        ];
    }
}
