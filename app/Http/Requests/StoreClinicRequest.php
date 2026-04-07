<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreClinicRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_text_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'admin_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|required_with:admin_name|email|unique:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de la clinique est obligatoire.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.max' => 'Le logo ne doit pas dépasser 2 Mo.',
            'admin_email.unique' => 'Cet email admin est déjà utilisé.',
        ];
    }
}
