<?php

namespace App\Http\Requests\Crm;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateReservationRequest extends FormRequest
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
        $validate =   [
            'reservation_date' => 'required|date',
            'property_id' => 'nullable|exists:unit_properties,id',
            'unit_price' => 'required|numeric',
            'reservation_fee' => 'required|numeric',
            'all_in_fee' => 'required|numeric',
            'hook_additional_fee' => 'nullable|numeric',
            'additional_land_area_fee' => 'nullable|numeric',
            'additional_building_specifications_fee' => 'nullable|numeric',
            'reservation_proof' => 'nullable|file|max:10800|mimetypes:image/*',
            'reservation_letter' => 'nullable|file|max:10800|mimes:pdf,jpeg,jpg,png',
            'notes' => 'nullable',
            "construction_notes" => "nullable|string|max:255",
        ];

        if (Auth::user()->hasPermission('lead.confirm_reservation_payment')) {
            $validate['status'] = 'required|in:'.implode(',', config('setting.reservation_statuses'));
        }

        return $validate;
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
