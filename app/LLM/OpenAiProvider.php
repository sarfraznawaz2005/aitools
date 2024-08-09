<?php

namespace App\LLM;

use Exception;

class OpenAiProvider extends BaseLLMProvider
{
    public function __construct(string $apiKey, string $model, array $options = [], int $retries = 1)
    {
        parent::__construct($apiKey, 'https://api.openai.com/v1/', $model, $options, $retries);
    }

    public function chat(string $message, bool $stream = false): mixed
    {
        $url = $this->baseUrl . 'chat/completions';

        $body = [
            'model' => $this->model,
            'messages' => [[
                'role' => 'user',
                'content' => $message,
            ]],
            'stream' => $stream,
            'max_tokens' => $this->options['max_tokens'] ?? 4096,
            'temperature' => $this->options['temperature'] ?? 1.0,
        ];

        try {
            $response = $this->makeRequest($url, $body, $stream, true);

            if ($stream) return '';

            dd($response);
            $result = json_decode($response, true);

            return $result['choices'][0]['message']['content'];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function complete(string $prompt, bool $stream = false): string
    {
        $data = [
            'prompt' => $prompt,
            'model' => $this->options['completion_model'] ?? 'text-davinci-003',
            'stream' => $stream
        ];

        if ($stream) {
            try {
                return $this->makeRequest('/completions', $data, true);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        try {
            $response = $this->makeRequest('/completions', $data);
            $result = json_decode($response, true);

            return $result['choices'][0]['text'];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function embed(string $text, string $embeddingModel): array|string
    {
        try {
            $response = $this->makeRequest('/embeddings', [
                'input' => $text,
                'model' => $this->options['embedding_model'] ?? 'text-embedding-ada-002'
            ]);

            $result = json_decode($response, true);

            return $result['data'][0]['embedding'];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
