<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialiteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255|unique:specialites',
            'icone' => 'nullable|string|max:50',
            'tarif_consultation' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.unique' => 'Cette spécialité existe déjà.',
            'tarif_consultation.required' => 'Le tarif est obligatoire.',
        ];
    }
}
