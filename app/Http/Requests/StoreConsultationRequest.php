<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'est_assure' => 'required|in:0,1',
            'montant_total' => 'required|numeric|min:0',
            'montant_donne' => 'required|numeric|min:0',
            'tarif_specialite' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Le patient est obligatoire.',
            'medecin_id.required' => 'Le médecin est obligatoire.',
            'montant_total.required' => 'Le montant total est obligatoire.',
            'montant_donne.required' => 'Le montant donné est obligatoire.',
        ];
    }
}
