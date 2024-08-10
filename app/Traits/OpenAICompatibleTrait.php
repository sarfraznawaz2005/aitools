<?php

namespace App\Traits;

use Exception;

trait OpenAICompatibleTrait
{
    public function chat(string $message, bool $stream = false, ?callable $callback = null): string
    {
        $url = $this->baseUrl . 'chat/completions';

        $body = [
            'model' => $this->model ?? 'gpt-3.5-turbo',
            'messages' => [[
                'role' => 'user',
                'content' => $message,
            ]],
            'stream' => $stream,
            'max_tokens' => $this->options['max_tokens'] ?? 4096,
            'temperature' => $this->options['temperature'] ?? 1.0,
        ];

        try {

            if ($stream) {
                try {
                    $this->makeRequest($url, $body, $stream, true, $callback);
                } catch (Exception) {
                    // fallback via non-streaming response
                    sleep(1);

                    unset($body['stream']);

                    $response = $this->makeRequest($url, $body, false, true, $callback);
                    $text = $this->getResult($response);

                    if ($callback) {
                        $callback($text);
                    } else {
                        echo "event: update\n";
                        echo 'data: ' . json_encode($text) . "\n\n";
                        ob_flush();
                        flush();
                    }
                }
            } else {
                $response = $this->makeRequest($url, $body, false, true, $callback);
            }

            return isset($response) ? $this->getResult($response) : '';

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

    protected function getResult(array $response): string
    {
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
    }

    /**
     * @throws Exception
     */
    protected function getStreamingResponse($data, ?callable $callback = null): void
    {
        try {

            $data = $this->fixJson($data);

            $parts = explode("\n", $data);
            $parts = array_filter($parts);

            if ($parts) {
                foreach ($parts as $part) {
                    $json = json_decode($part, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Invalid JSON: ' . json_last_error_msg());
                    }

                    if (isset($json['choices'])) {
                        foreach ($json['choices'] as $choice) {
                            if (isset($choice['delta'])) {
                                $text = $choice['delta']['content'] ?? '';

                                if (php_sapi_name() === 'cli') {
                                    if ($callback) {
                                        $callback($text);
                                    } else {
                                        echo $text;
                                    }

                                    continue;
                                }

                                if (!$text) {
                                    continue;
                                }

                                if ($callback) {
                                    $callback($text);
                                } else {
                                    echo "event: update\n";
                                    echo 'data: ' . json_encode($text) . "\n\n";
                                    ob_flush();
                                    flush();
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
