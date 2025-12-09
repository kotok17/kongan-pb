<?php

namespace App\Libraries;

use OpenAI;

class OpenAIClient
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(getenv('OPENAI_API_KEY'));
    }

    public function ask($prompt)
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4.1-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }
}