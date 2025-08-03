<?php

namespace App\Services;

class PromptTestCasesService
{
    public function getTestCases(): array
    {
        return [
            'Cas débutant' => [
                'question_title' => 'Présentez-vous en anglais',
                'question_success_criteria' => 'Utilisation des phrases simples, présentation basique',
                'question_language' => 'en',
                'site_language' => 'fr',
                'question_support_material' => 'Aucun support requis',
                'question_suggestion' => 'Hello, my name is...',
                'question_cecrl_code' => 'A1',
                'user_input' => 'Hello, my name is John. I am 25 years old. I am from France.'
            ],
            'Cas intermédiaire' => [
                'question_title' => 'Décrivez votre film préféré',
                'question_success_criteria' => 'Vocabulaire varié, utilisation du passé, expression d\'opinions',
                'question_language' => 'en',
                'site_language' => 'fr',
                'question_support_material' => 'Image d\'affiche de film',
                'question_suggestion' => 'My favorite movie is... I enjoyed it because...',
                'question_cecrl_code' => 'B1',
                'user_input' => 'My favorite movie is Inception. I watched it last year and was amazed by its complex plot. The visual effects were stunning, and the story kept me engaged throughout the entire film.'
            ],
            'Cas avancé' => [
                'question_title' => 'Analysez l\'impact des réseaux sociaux',
                'question_success_criteria' => 'Argumentation détaillée, vocabulaire sophistiqué, exemples concrets',
                'question_language' => 'en',
                'site_language' => 'fr',
                'question_support_material' => 'Article de recherche sur les réseaux sociaux',
                'question_suggestion' => 'Consider both positive and negative aspects...',
                'question_cecrl_code' => 'C1',
                'user_input' => 'Social media has revolutionized modern communication, fundamentally altering how we interact and share information. While platforms like Twitter and Instagram have democratized content creation and fostered global connections, they\'ve also raised concerns about privacy, mental health, and the spread of misinformation.'
            ],
            'Cas professionnel' => [
                'question_title' => 'Rédigez un email professionnel',
                'question_success_criteria' => 'Format approprié, ton professionnel, clarté du message',
                'question_language' => 'en',
                'site_language' => 'fr',
                'question_support_material' => 'Exemple de format d\'email professionnel',
                'question_suggestion' => 'Dear [Name], I am writing to...',
                'question_cecrl_code' => 'B2',
                'user_input' => 'Dear Mr. Smith, I hope this email finds you well. I am writing to follow up on our meeting last week regarding the project timeline. I have attached the updated schedule for your review. Please let me know if you have any questions. Best regards, Jane Doe'
            ],
            'Cas créatif' => [
                'question_title' => 'Écrivez une histoire courte',
                'question_success_criteria' => 'Structure narrative, description vivante, dialogue naturel',
                'question_language' => 'en',
                'site_language' => 'fr',
                'question_support_material' => 'Image d\'un paysage mystérieux',
                'question_suggestion' => 'Start with setting the scene...',
                'question_cecrl_code' => 'C2',
                'user_input' => 'The fog rolled in thick that morning, blanketing the ancient lighthouse in an ethereal mist. Sarah stood at the window, her breath creating small patterns on the cold glass. The lighthouse keeper hadn\'t been seen for three days, and the strange lights that danced in the fog at night were beginning to worry the local fishermen.'
            ]
        ];
    }
}
