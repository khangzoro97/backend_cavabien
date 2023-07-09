<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $tablePasswordReset = config('auth.passwords.users.table');
        $token = $this->request->get('token');
        $password = $this->request->get('password');

        return [
            'token' => "bail|exists:$tablePasswordReset,token",
            'password' => 'bail|required|min:8|max:20|alpha_num|confirmed',
        ];
    }
}
