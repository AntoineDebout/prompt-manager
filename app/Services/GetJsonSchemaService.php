<?php
namespace App\Services;

class GetJsonSchemaService
{
    public function getDefaultSchema(): array
    {
        return [
            'name' => 'default-schema',
            'schema' => [
                'type' => 'object',
                'additionalProperties' => false,
                'properties' => [
                    'comment' => [
                        'type' => 'string',
                        'description' => 'Comment on the learner\'s answer based on their performance.'
                    ],
                    'cecrl_level' => [
                        'type' => 'string',
                        'enum' => ['A1', 'A2', 'B1', 'B2', 'C1'],
                        'description' => 'Estimated CECRL level of the learner\'s answer based on their spoken transcript.'
                    ],
                ],
                'required' => [
                    'cecrl_level',
                    'comment',
                ]
            ]
        ];
    }
}