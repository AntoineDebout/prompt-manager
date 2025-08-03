<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromptTestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $variables = $this->get('variables', []);
        $rules = [
            'prompt' => ['required', 'string'],
            'model' => ['required', 'string', 'in:gpt-4o-mini,gpt-3.5-turbo,gpt-4,gpt-4-turbo'],
            'temperature' => ['required', 'numeric', 'between:0,2'],
            'variables' => ['required', 'array'],
        ];

        // Validation dynamique des variables du prompt
        foreach ($variables as $key => $value) {
            $rules["variables.{$key}"] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'prompt.required' => 'Le prompt est requis.',
            'model.required' => 'Le modèle est requis.',
            'model.in' => 'Le modèle sélectionné n\'est pas valide.',
            'temperature.required' => 'La température est requise.',
            'temperature.between' => 'La température doit être comprise entre 0 et 2.',
            'variables.required' => 'Les variables sont requises.',
            'variables.array' => 'Les variables doivent être un tableau.',
        ];

        // Messages d'erreur personnalisés pour chaque variable
        $variables = $this->get('variables', []);
        foreach ($variables as $key => $value) {
            $messages["variables.{$key}.required"] = "La variable '{$key}' doit être remplie.";
            $messages["variables.{$key}.string"] = "La variable '{$key}' doit être une chaîne de caractères.";
        }

        return $messages;
    }
}
