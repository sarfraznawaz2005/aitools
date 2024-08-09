<?php

namespace App\LLM;

class OpenAIProvider extends BaseLLMProvider
{
    public function __construct(array $options = [])
    {
        parent::__construct('https://api.openai.com/v1', $options);
    }

    public function embed(string $text): array
    {
        $response = $this->sendRequest('/embeddings', [
            'input' => $text,
            'model' => $this->options['embedding_model'] ?? 'text-embedding-ada-002'
        ]);
        $result = json_decode($response, true);
        return $result['data'][0]['embedding'];
    }

    public function chat(array $messages, bool $stream = false): string
    {
        $data = [
            'messages' => $messages,
            'model' => $this->options['chat_model'] ?? 'gpt-3.5-turbo',
            'stream' => $stream
        ];

        if ($stream) {
            $this->sendRequest('/chat/completions', $data, true);
            return;
        }

        $response = $this->sendRequest('/chat/completions', $data);
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'];
    }

    public function complete(string $prompt, bool $stream = false): string
    {
        $data = [
            'prompt' => $prompt,
            'model' => $this->options['completion_model'] ?? 'text-davinci-003',
            'stream' => $stream
        ];

        if ($stream) {
            return $this->sendRequest('/completions', $data, true);
        }

        $response = $this->sendRequest('/completions', $data);
        $result = json_decode($response, true);
        return $result['choices'][0]['text'];
    }
}



