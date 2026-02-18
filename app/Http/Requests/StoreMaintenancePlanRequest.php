<?php

namespace App\Http\Requests;

use App\Services\RruleParser;
use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenancePlanRequest extends FormRequest
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
            'rrule' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!RruleParser::isValidRrule($value)) {
                        $fail('La règle de récurrence (RRULE) est invalide. Veuillez utiliser le configurateur.');
                    }
                    
                    try {
                        $parser = new RruleParser($value);
                        
                        // Pour WEEKLY, au moins un jour doit être sélectionné
                        if ($parser->getFrequency() === 'WEEKLY' && empty($parser->getWeekdays())) {
                            $fail('Veuillez sélectionner au moins un jour pour la récurrence hebdomadaire.');
                        }
                    } catch (\Exception $e) {
                        $fail('Erreur lors de la validation de la règle de récurrence: ' . $e->getMessage());
                    }
                },
            ],
            'type' => ['required', 'string', 'in:preventive,corrective'],
            'interval_jours' => 'nullable|integer|min:1|max:3650',
            'statut' => 'required|in:actif,inactif',
            'technicien_id' => 'nullable|exists:users,id',
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
            'rrule.required' => 'Veuillez configurer la règle de récurrence.',
            'type.required' => 'Le type de maintenance est obligatoire.',
            'type.in' => 'Le type doit être préventive ou corrective.',
            'interval_jours.min' => 'L\'intervalle doit être au minimum 1 jour.',
            'interval_jours.max' => 'L\'intervalle ne peut pas dépasser 10 ans.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être actif ou inactif.',
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
