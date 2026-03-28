<?php

namespace App\Http\Requests\Crm;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLeadDocumentRequest extends FormRequest
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
     * 
     */

    public function rules(): array
    {
        return [
            'lead_id' => 'required|exists:leads,id',
            'doc_ktp_applicant' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'doc_ktp_partner' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'doc_npwp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'doc_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'doc_marriage_or_divorce_certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'doc_applicant_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:10800',
            'doc_partner_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:10800',
            'doc_house_ownership_certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'doc_domisili_certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'doc_spr_bank' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10800',
            'check_cash' => 'required|boolean',
            'pekerja_materai_60_lembar' => 'required|boolean',
            'pekerja_rekening_koran_3_bulan' => 'required|boolean',
            'pekerja_no_telp_dan_nama_atasan' => 'required|boolean',
            'pekerja_foto_tempat_kerja_dan_serlok' => 'required|boolean',
            'pekerja_slip_gaji_3_bulan' => 'required|boolean',
            'pekerja_formulir_bank_dan_flpp' => 'required|boolean',
            'wirausaha_materai_60_lembar' => 'required|boolean',
            'wirausaha_rekening_koran_6_bulan' => 'required|boolean',
            'wirausaha_sk_usaha_atau_nomor_usaha' => 'required|boolean',
            'wirausaha_foto_tempat_usaha' => 'required|boolean',
            'wirausaha_foto_tempat_usaha_dan_serlok' => 'required|boolean',
            'wirausaha_neraca_penghasilan_6_bulan' => 'required|boolean',
            'wirausaha_formulir_bank_dan_flpp' => 'required|boolean',
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
