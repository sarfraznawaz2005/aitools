<?php

namespace App\LLM;

use App\Traits\OpenAICompatibleTrait;

class OpenAiProvider extends BaseLLMProvider
{
    use OpenAICompatibleTrait;

    public function __construct(string $apiKey, string $model, array $options = [], int $retries = 1)
    {
        parent::__construct($apiKey, 'https://api.openai.com/v1/', $model, $options, $retries);
    }
}
