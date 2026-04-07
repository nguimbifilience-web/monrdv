<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreRendezVousRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'date_rv' => 'required|date',
            'heure_rv' => 'nullable|string',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'motif' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'date_rv.required' => 'La date est obligatoire.',
            'date_rv.date' => 'La date n\'est pas valide.',
            'patient_id.required' => 'Le patient est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas.',
            'medecin_id.required' => 'Le médecin est obligatoire.',
            'medecin_id.exists' => 'Le médecin sélectionné n\'existe pas.',
        ];
    }
}
