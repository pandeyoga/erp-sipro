<?php

namespace App\Http\Requests\Property;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateConstructionRequest extends FormRequest
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
            'status_pondasi' => 'required|string|in:not_started,in_progress,completed',
            'dokumentasi_pondasi' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/jpg,image/png,application/pdf',
            'status_naik_bata' => 'required|string|in:not_started,in_progress,completed',
            'dokumentasi_naik_bata' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/jpg,image/png,application/pdf',
            'status_naik_atap' => 'required|string|in:not_started,in_progress,completed',
            'dokumentasi_naik_atap' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/jpg,image/png,application/pdf',
            'status_plester_aci' => 'required|string|in:not_started,in_progress,completed',
            'dokumentasi_plester_aci' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/jpg,image/png,application/pdf',
            'status_keramik_cat' => 'required|string|in:not_started,in_progress,completed',
            'dokumentasi_keramik_cat' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/jpg,image/png,application/pdf',
            'status_finishing' => 'required|string|in:not_started,in_progress,completed',
            'dokumentasi_finishing' => 'nullable|file|max:10800|mimetypes:image/jpeg,image/jpg,image/png,application/pdf',
            'notes' => 'nullable|string|max:255',
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
