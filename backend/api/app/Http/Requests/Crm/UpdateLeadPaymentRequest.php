<?php

namespace App\Http\Requests\Crm;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLeadPaymentRequest extends FormRequest
{
    use ApiResponse;
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
        $paymentCheklist = collect(config('setting.payment_checklist'))->map(function ($item) {
            return $item['code'];
        })->toArray();
        
        foreach ($paymentCheklist as $code) {
            $rules["checklist_$code"] = 'required|boolean';
        }

        return [
            'sp3k_status' => 'required|string|in:pending,approved',
            'sp3k_document' => 'nullable|file|mimetypes:application/pdf|max:10800',
            'sp3k_bank'  => [
                'required_if:payment_type,kpr',
                'uuid',
                'exists:bank_accounts,id',
            ],
            'sp3k_code' => 'nullable|string',
            'sp3k_date' => 'nullable|date',
            'sp3k_number' => 'nullable|string',
            'akad_kredit_status' => 'required|string|in:pending,approved',
            'akad_kredit_penandatanganan_document' => 'nullable|file|mimetypes:image/*,application/pdf|max:10800',
            'notes' => 'nullable|string',
            'proposed_name_1' => 'nullable|string',
            'proposed_name_2' => 'nullable|string',
            ...$rules,
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse( 
                "Validation error",
                $validator->errors(),
                422
            )
        );
    }
}
