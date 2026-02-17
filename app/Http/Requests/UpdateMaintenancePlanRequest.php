<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenancePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'equipement_id' => ['required', 'integer', 'exists:equipements,id'],
            'type' => ['required', 'string', 'in:preventive,corrective'],
            'frequence' => ['nullable', 'string', 'in:mensuelle,trimestrielle,annuelle'],
            'interval_jours' => 'sometimes|integer|min:1',
            'derniere_date' => ['nullable', 'date'],
            'prochaine_date' => 'sometimes|nullable|date',
            'statut' => 'sometimes|in:actif,inactif',
            'technicien_id' => 'sometimes|nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'equipement_id.required' => 'L\'équipement est obligatoire.',
            'equipement_id.exists' => 'L\'équipement sélectionné n\'existe pas.',
            'type.required' => 'Le type de maintenance est obligatoire.',
            'type.in' => 'Le type doit être préventive ou corrective.',
            'frequence.in' => 'La fréquence doit être mensuelle, trimestrielle ou annuelle.',
            'interval_jours.min' => 'L\'intervalle doit être au minimum 1 jour.',
            'interval_jours.max' => 'L\'intervalle ne peut pas dépasser 10 ans.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être actif ou suspendu.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs
        if ($this->has('equipement_id')) {
            $this->merge([
                'equipement_id' => (int) filter_var($this->equipement_id, FILTER_SANITIZE_NUMBER_INT)
            ]);
        }

        if ($this->has('interval_jours')) {
            $this->merge([
                'interval_jours' => (int) filter_var($this->interval_jours, FILTER_SANITIZE_NUMBER_INT)
            ]);
        }
    }
}
