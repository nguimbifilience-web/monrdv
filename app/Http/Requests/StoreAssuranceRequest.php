<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssuranceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:publique,privée',
            'nom_referent' => 'required|string|max:255',
            'taux_couverture' => 'required|numeric|min:0|max:100',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'document_modele' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'type.required' => 'Le type est obligatoire.',
            'type.in' => 'Le type doit être publique ou privée.',
            'nom_referent.required' => 'Le nom du référent est obligatoire.',
            'taux_couverture.required' => 'Le taux de couverture est obligatoire.',
            'taux_couverture.max' => 'Le taux ne peut pas dépasser 100%.',
        ];
    }
}
