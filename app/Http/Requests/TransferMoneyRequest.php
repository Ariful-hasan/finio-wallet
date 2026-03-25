<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferMoneyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'to_wallet_id' => ['required', 'string', 'uuid'],
            'amount'       => ['required', 'integer', 'min:1'],
            'currency'     => ['required', 'string', 'size:3'],
        ];
    }
}
