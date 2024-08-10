<?php

namespace App\LLM;

use Exception;

class OpenAiProvider extends BaseLLMProvider
{
    public function __construct(string $apiKey, string $model, array $options = [], int $retries = 1)
    {
        parent::__construct($apiKey, 'https://api.openai.com/v1/', $model, $options, $retries);
    }

    public function chat(string $message, bool $stream = false): string
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

            $text = '';

            if (isset($response['choices'])) {
                foreach ($response['choices'] as $choice) {
                    if (!isset($choice['message'])) {
                        return "No response, please try again!";
                    }

                    $text .= $choice['message']['content'] . (php_sapi_name() === 'cli' ? "\n" : PHP_EOL);
                }
            }

            return $text;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function embed(string $text, string $embeddingModel): array|string
    {
        // openai also has batch embed content which should be used instead for multiple texts

        $url = $this->baseUrl . 'embeddings';

        try {
            $response = $this->makeRequest($url, [
                'input' => $text,
                'model' => $embeddingModel
            ], false, true);

            return $response['data'][0]['embedding'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    protected function getStreamingResponse($data): void
    {
        $data = $this->fixJson($data);

        $parts = explode("\n", $data);
        $parts = array_filter($parts);

        foreach ($parts as $part) {
            $json = json_decode($part, true);

            if (isset($json['choices'])) {
                foreach ($json['choices'] as $choice) {
                    if (isset($choice['delta'])) {
                        if (php_sapi_name() === 'cli') {
                            echo $choice['delta']['content'] ?? '';
                            continue;
                        }

                        echo "event: update\n";
                        echo 'data: ' . json_encode($choice['delta']['content'] ?? '') . "\n\n";
                        ob_flush();
                        flush();
                    }
                }
            }
        }
    }
}
