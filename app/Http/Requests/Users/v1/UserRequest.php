<?php

namespace App\Http\Requests\Users\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HttpResonse;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UserRequest extends FormRequest
{
    use HttpResonse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $method = $this->method();

        return [
            'name' => 'required|alpha_spaces',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|alpha_num'
        ];
    }

    public function messages(){
        return [
            'alpha_spaces' => 'The :attribute may only contain letters and spaces.',
        ];
    }

    // Customize the validation failure behavior
    protected function failedValidation(Validator $validator){
        if ($this->wantsJson()) {
            throw new HttpResponseException(
                $this->validation('Validation error', $validator->errors())
            );
        } else {
            throw new HttpResponseException(
                redirect()->back()
                    ->withInput($this->input())
                    ->withErrors($validator)
            );
        }
    }
}
