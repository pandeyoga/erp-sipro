<?php

namespace App\Http\Requests\Crm;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFinalLegalityRequest extends FormRequest
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
        return [
            'bast_document' => 'nullable|file|max:10800|mimetypes:application/pdf',
            'bast_hanover_photo' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/png',
            'bast_date' => 'nullable|date',
            'retention_document' => 'nullable|file|max:10800|mimetypes:application/pdf',
            'retention_hanover_photo' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/png',
            'retention_start_date' => 'nullable|date',
            'notes' => 'nullable|string',
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
