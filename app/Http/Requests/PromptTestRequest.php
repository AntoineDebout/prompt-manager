<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromptTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variables = $this->get('variables', []);
        $rules = [
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['required', 'string', 'in:system,user,developer'],
            'messages.*.content' => ['required', 'string'],
            'model' => ['required', 'string', 'in:gpt-4o-mini,gpt-3.5-turbo,gpt-4,gpt-4-turbo,gpt-4-turbo-preview'],
            'temperature' => ['required', 'numeric', 'between:0,2'],
            'variables' => ['required', 'array'],
            'schema' => ['array'],
        ];

        // Validation dynamique des variables du prompt
        foreach ($variables as $key => $value) {
            $rules["variables.{$key}"] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'messages.required' => 'Les messages sont requis.',
            'messages.array' => 'Les messages doivent être un tableau.',
            'messages.min' => 'Au moins un message est requis.',
            'messages.*.role.required' => 'Chaque message doit avoir un rôle.',
            'messages.*.role.in' => 'Les rôles autorisés sont : system, user, assistant.',
            'messages.*.content.required' => 'Chaque message doit avoir un contenu.',
            'messages.*.content.string' => 'Le contenu du message doit être une chaîne de caractères.',
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
