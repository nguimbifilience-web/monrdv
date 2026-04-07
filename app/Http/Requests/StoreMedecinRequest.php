<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedecinRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'specialite_id' => 'required|exists:specialites,id',
            'telephone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'tarif_heure' => 'required|numeric|min:0',
            'heures_mois' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'specialite_id.required' => 'La spécialité est obligatoire.',
            'specialite_id.exists' => 'La spécialité sélectionnée n\'existe pas.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'tarif_heure.required' => 'Le tarif horaire est obligatoire.',
            'heures_mois.required' => 'Les heures mensuelles sont obligatoires.',
        ];
    }
}
