<?php

namespace App\LLM;

class OllamaProvider implements LlmProvider
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.ollama.com/v1';
    protected string $model;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function url(): string
    {
        return $this->baseUrl;
    }

    public function options(): array
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ];
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function embed(string $text, int $retries = 3, int $sleepInterval = 1): array
    {
        $endpoint = $this->url() . '/embeddings';
        $payload = json_encode(['input' => $text, 'model' => $this->model]);

        return $this->retryRequest($endpoint, $payload, $retries
            , $sleepInterval);
    }

    public function chat(string $prompt, bool $stream = false, int $retries = 3, int $sleepInterval = 1): string
    {
        $endpoint = $this->url() . '/chat/completions';
        $payload = json_encode([
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'stream' => $stream,
        ]);

        return $this->retryRequest($endpoint, $payload, $retries, $sleepInterval);
    }

    public function complete(string $prompt, bool $stream = false, int $retries = 3, int $sleepInterval = 1): string
    {
        $endpoint = $this->url() . '/completions';
        $payload = json_encode([
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => $stream,
        ]);

        return $this->retryRequest($endpoint, $payload, $retries, $sleepInterval);
    }

    protected function retryRequest(string $url, string $payload, int $retries, int $sleepInterval): array|string
    {
        while ($retries > 0) {
            $response = $this->makeRequest($url, $payload);
            if (!str_starts_with($response, 'Error')) {
                return $response;
            }

            $retries--;
            if ($retries > 0) {
                sleep($sleepInterval);
            }
        }

        return "Error: Failed after multiple attempts";
    }

    protected function makeRequest(string $url, string $payload): array|string
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->formatHeaders($this->options()['headers']));

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpStatus >= 200 && $httpStatus < 300) {
            return json_decode($response, true);
        } else {
            return "Error: Received status code $httpStatus";
        }
    }

    protected function formatHeaders(array $headers): array
    {
        $formattedHeaders = [];
        foreach ($headers as $key => $value) {
            $formattedHeaders[] = "$key: $value";
        }
        return $formattedHeaders;
    }
}
