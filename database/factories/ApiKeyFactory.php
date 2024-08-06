<?php

namespace Database\Factories;

use App\Enums\ApiKeyTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiKey>
 */
class ApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $llmType = $this->faker->randomElement(ApiKeyTypeEnum::values());

        $data = [
            'api_key' => $this->faker->uuid,
            'model_name' => $this->faker->word,
            'llm_type' => $llmType,
        ];

        if ($llmType === 'Ollama') {
            $data['base_url'] = $this->faker->url();
        }

        return $data;
    }
}
