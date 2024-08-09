<?php

namespace App\LLM;

use Exception;

class GeminiProvider extends BaseLLMProvider
{
    public function __construct(string $apiKey, string $model, array $options = [], int $retries = 1)
    {
        parent::__construct($apiKey, 'https://generativelanguage.googleapis.com/v1/', $model, $options, $retries);
    }

    public function chat(string $message, bool $stream = false): mixed
    {
        $responseType = $stream ? 'streamGenerateContent' : 'generateContent';

        $url = $this->baseUrl . 'models/' . $this->model . ":$responseType?key=" . $this->apiKey;

        $body = [
            'contents' => [
                'role' => 'user',
                'parts' => [
                    ['text' => $message],
                ],
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_NONE',
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_NONE',
                ],
            ],
            'generationConfig' => $this->options,
        ];

        try {

            $response = $this->makeRequest($url, $body, $stream);

            if ($stream) return '';

            $text = '';

            foreach ($response['candidates'] as $candidate) {
                if (!isset($candidate['content'])) {
                    return "No response, please try again!";
                }

                foreach ($candidate['content']['parts'] as $part) {
                    $text .= $part['text'] . "\n";
                }
            }

            return $text;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function complete(string $prompt, bool $stream = false): mixed
    {
        return $this->chat($prompt, $stream);
    }

    public function embed(string $text, string $embeddingModel): array|string
    {
        // google also has batch embed content which should be used instead for multiple texts

        $url = $this->baseUrl . 'models/' . $embeddingModel . ":embedContent?key=" . $this->apiKey;

        $body = [
            "model" => "models/$embeddingModel",
            "content" => [
                "parts" => [
                    [
                        "text" => $text
                    ]
                ]
            ]
        ];

        try {
            $response = $this->makeRequest($url, $body);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $response['embedding']['values'] ?? [];
    }
}
