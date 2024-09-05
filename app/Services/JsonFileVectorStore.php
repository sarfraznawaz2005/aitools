<?php

namespace App\Services;

use App\Constants;
use App\Enums\ApiKeyTypeEnum;
use App\LLM\GeminiProvider;
use App\LLM\LlmProvider;
use App\LLM\OpenAiProvider;
use Exception;

class JsonFileVectorStore
{
    private static ?JsonFileVectorStore $instance = null;

    private array $embeddingsCache = [];
    protected array $textSplits = [];

    private function __construct(protected LlmProvider $llm,
                                 protected int         $chunkSize,
                                 protected int         $maxResults)
    {
    }

    public static function getInstance(
        LlmProvider $llm,
        int         $chunkSize = 1000,
        int         $maxResults = 2
    ): JsonFileVectorStore
    {
        if (self::$instance === null) {
            self::$instance = new self($llm, $chunkSize, $maxResults);
        }

        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function searchTexts(array $notes, string $query): array
    {
        $results = $this->performCosineSimilaritySearch($notes, $query);

        if (!empty($results)) {
            if (app()->environment('local')) {
                info('Resutls found via cosine similarity');
            }

            return $this->getTopResults($results);
        }

        $results = $this->performTextSearch($query);

        if (!empty($results)) {
            if (app()->environment('local')) {
                info('Resutls found via text search');
            }
        }

        return $this->getTopResults($results);
    }

    /**
     * @throws Exception
     */
    protected function performCosineSimilaritySearch(array $texts, string $query): array
    {
        $results = [];

        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query, true)], $this->getEmbdeddingModel());

        $this->setTextEmbeddingsFromTexts($texts);

        $results = array_merge($results, $this->compareEmbeddings($queryEmbeddings));

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $results;
    }

    /**
     * @throws Exception
     */
    protected function performTextSearch(string $query): array
    {
        $results = [];
        $cleanedQuery = $this->getCleanedText($query, true);

        foreach ($this->textSplits as $index => $chunk) {
            $exactMatchScore = $this->calculateExactMatchScore($cleanedQuery, $chunk['text']);
            $fuzzyMatchScore = $this->calculateFuzzyMatchScore($cleanedQuery, $chunk['text']);

            $maxScore = max($exactMatchScore, $fuzzyMatchScore);

            if ($maxScore >= $this->getSimiliarityThreashold()) {
                $results[] = [
                    'similarity' => $maxScore,
                    'index' => $index,
                    'matchedChunk' => ['text' => $chunk['text'], 'metadata' => $chunk['metadata']],
                ];
            }
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $results;
    }

    protected function getTopResults(array $results): array
    {
        $topResults = array_slice($results, 0, $this->maxResults);

        foreach ($topResults as &$result) {
            if (isset($result['matchedChunk']['embeddings'])) {
                unset($result['matchedChunk']['embeddings']);
            }
        }

        return $topResults;
    }

    protected function setTextEmbeddingsFromTexts(array $texts): void
    {
        // filter out bad stuff
        $texts = array_map(fn($item) => ['text' => $this->getCleanedText($item['text']), 'source' => $item['source']], $texts);
        $texts = array_filter($texts, fn($item) => !empty(trim($item['text'])) && strlen(trim($item['text'])) > 2);

        $splits = $this->splitTextIntoChunks($texts);

        $this->textSplits = $this->getEmbeddingsOrLoadFromCache($splits);
    }

    //file_put_contents(storage_path('app/dump.json'), json_encode($textWithMetadata, JSON_PRETTY_PRINT));
    protected function splitTextIntoChunks(array $textWithMetadata): array
    {
        $chunks = [];
        $overlapPercentage = 3; // 30% overlap
        $overlapSize = max(1, (int)($this->chunkSize * ($overlapPercentage / 100)));

        foreach ($textWithMetadata as $item) {
            $fullText = $item['text'];
            $totalLength = strlen($fullText);

            $chunkStart = 0;
            while ($chunkStart < $totalLength) {
                $chunkEnd = min($chunkStart + $this->chunkSize, $totalLength);
                $chunk = substr($fullText, $chunkStart, $chunkEnd - $chunkStart);

                // Append the chunk
                $chunks[] = [
                    'text' => trim($chunk),
                    'metadata' => $item['source']
                ];

                if ($chunkEnd == $totalLength) {
                    break;
                }

                $nextChunkStart = max(0, $chunkEnd - $overlapSize);
                $chunkStart = $nextChunkStart;
            }
        }

        return $chunks;
    }

    protected function getEmbeddingsOrLoadFromCache(array $splits): array
    {
        $path = storage_path('app/data.json');

        $cacheKey = 'notes_cache_' . md5(json_encode($splits));
        if (array_key_exists($cacheKey, $this->embeddingsCache)) {
            //info("Loaded embeddings from cache for $fileName");
            return $this->embeddingsCache[$cacheKey];
        }

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            //info("Loaded embeddings from file for $path");
            return $data;
        }

        $textSplits = [];
        foreach ($splits as $split) {
            $textSplits[] = $split['text'];
        }

        $chunks = array_chunk($textSplits, $this->getEmbdeddingBatchSize());

        foreach ($chunks as $chunk) {
            $embeddings = $this->llm->embed($chunk, $this->getEmbdeddingModel());
            //file_put_contents(storage_path('app/dump.json'), json_encode($embeddings, JSON_PRETTY_PRINT));

            foreach ($embeddings['embeddings'] as $embeddingIndex => $embeddingData) {
                // Map the embedding back to the correct split in the original $splits array
                if (isset($splits[$embeddingIndex])) {
                    $splits[$embeddingIndex]['embeddings'] = $embeddingData['values'];
                }
            }
        }

        // Store in cache and save to file
        $this->embeddingsCache[$cacheKey] = $splits;
        file_put_contents($path, json_encode($splits, JSON_PRETTY_PRINT));
        info("notes indexing file saved at: $path");

        return $splits;
    }


    /**
     * @throws Exception
     */
    protected function compareEmbeddings(array $queryEmbeddings): array
    {
        $results = [];
        $alreadyAdded = [];

        if (!$queryEmbeddings) {
            return $results;
        }

        // Standardize the query embeddings
        if (isset($queryEmbeddings['embeddings'])) {
            $queryEmbeddingValues = $queryEmbeddings['embeddings'][0]['values'];
        } elseif (isset($queryEmbeddings[0]['embedding'])) {
            $queryEmbeddingValues = $queryEmbeddings[0]['embedding'];
        } else {
            throw new Exception("Unknown query embeddings format.");
        }

        $iterations = 0;

        foreach ($this->textSplits as $index => $split) {
            if (isset($split['embeddings'])) {
                $embeddingValues = $split['embeddings'];

                $iterations++;
                $this->processEmbedding($embeddingValues, $queryEmbeddingValues, $index, $iterations, $results, $alreadyAdded);
            } else {
                throw new Exception("Unknown embedding format!.");
            }
        }

        return $results;
    }

    protected function processEmbedding(
        array $embeddingValues,
        array $queryEmbeddingValues,
        int   $index,
        int   $iterations,
        array &$results,
        array &$alreadyAdded): void
    {
        // Calculate cosine similarity
        $similarity = $this->cosineSimilarity($embeddingValues, $queryEmbeddingValues);

        // Log similarity and iteration for debugging
        //info("Iteration #: $iterations, Similarity: $similarity");

        if ($similarity >= $this->getSimiliarityThreashold()) {
            if (isset($this->textSplits[$index])) {
                $matchedText = $this->textSplits[$index];
                $hash = md5($matchedText['text']);

                if (!isset($alreadyAdded[$hash])) {
                    $alreadyAdded[$hash] = true;

                    $results[] = [
                        'similarity' => $similarity,
                        'index' => $index,
                        'matchedChunk' => $matchedText,
                    ];
                }
            }
        } else {
            //info("NOT FOUND at #: $iterations, Similarity: $similarity");
        }
    }

    protected function getEmbdeddingModel(): string
    {
        if ($this->llm instanceof OpenAiProvider) {
            $llmType = ApiKeyTypeEnum::OPENAI->value;
        } elseif ($this->llm instanceof GeminiProvider) {
            $llmType = ApiKeyTypeEnum::GEMINI->value;
        } else {
            $llmType = ApiKeyTypeEnum::OLLAMA->value;
        }

        return match ($llmType) {
            ApiKeyTypeEnum::GEMINI->value => Constants::GEMINI_EMBEDDING_MODEL,
            ApiKeyTypeEnum::OPENAI->value => Constants::OPENAI_EMBEDDING_MODEL,
            default => $this->llm->model,
        };
    }

    protected function getSimiliarityThreashold(): float
    {
        // because there is difference in the cosine similarity values between OpenAI and Gemini
        if ($this->llm instanceof OpenAiProvider) {
            return 0.75;
        } else {
            return 0.6;
        }
    }

    protected function calculateExactMatchScore(string $query, string $text): float
    {
        return stripos($text, $query) !== false ? $this->getSimiliarityThreashold() : 0.0;
    }

    protected function calculateFuzzyMatchScore(string $query, string $text): float
    {
        $distance = levenshtein($query, $text);
        $maxLength = max(strlen($query), strlen($text));

        return $maxLength === 0 ? $this->getSimiliarityThreashold() : 1 - ($distance / $maxLength);
    }

    protected function cosineSimilarity(array $u, array $v): float
    {
        try {
            $dotProduct = 0.0;
            $uLength = 0.0;
            $vLength = 0.0;

            foreach ($u as $i => $value) {
                $dotProduct += $value * $v[$i];
                $uLength += $value * $value;
                $vLength += $v[$i] * $v[$i];
            }

            return $dotProduct / (sqrt($uLength) * sqrt($vLength));
        } catch (Exception) {
            return 0;
        }
    }

    protected function getEmbdeddingBatchSize(): string
    {
        if ($this->llm instanceof OpenAiProvider) {
            $llmType = ApiKeyTypeEnum::OPENAI->value;
        } elseif ($this->llm instanceof GeminiProvider) {
            $llmType = ApiKeyTypeEnum::GEMINI->value;
        } else {
            $llmType = ApiKeyTypeEnum::OLLAMA->value;
        }

        return match ($llmType) {
            ApiKeyTypeEnum::GEMINI->value => Constants::GEMINI_EMBEDDING_BATCHSIZE,
            default => Constants::OPENAI_EMBEDDING_BATCHSIZE,
        };
    }

    protected function getCleanedText(string $text, bool $removeStopWords = false): string
    {
        $text = strtolower($text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);

        // we mean literal stings here not actual new lines, so do not use " characters
        $text = str_replace(['\n', '\r', '\r\n'], ' ', $text);

        // Add spaces around Unicode sequences and convert them to actual characters
        $text = preg_replace_callback(
            '/\\\\u([0-9A-Fa-f]{4})/',
            function ($matches) {
                return ' ' . mb_convert_encoding(pack('H*', $matches[1]), 'UTF-8', 'UCS-2BE') . ' ';
            },
            $text
        );

        // Replace unwanted characters and clean the text
        $text = preg_replace(
            [
                '/\r\n|\r/',                     // Handle different newline characters
                '/(\s*\n\s*){3,}/',              // Replace multiple newlines with double newlines
                '/\s+/',                         // Replace multiple spaces with single space
                '/[^\w\s\-$%_.\/]/',            // Allow only letters, numbers, $, -, _, %, /, ., and space
                '/(\$|%|_|-|\\|.|\/| )\1+/',     // Remove duplicate special characters
            ],
            [
                "\n",
                "\n\n",
                ' ',
                ' ',
                '$1',
            ],
            $text
        );

        if ($removeStopWords) {
            $text = $this->removeStopwords($text);
        }

        return trim($text);
    }

    protected function removeStopwords(string $text): string
    {
        $stopwords = [
            'the', 'a', 'an', 'and', 'but', 'if', 'or', 'because', 'as', 'until',
            'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between',
            'into', 'through', 'during', 'before', 'after', 'above', 'below', 'to',
            'from', 'up', 'down', 'in', 'out', 'on', 'off', 'over', 'under', 'again',
            'further', 'then', 'once', 'here', 'there', 'when', 'where', 'why',
            'how', 'all', 'any', 'both', 'each', 'few', 'more', 'most', 'other',
            'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than',
            'too', 'very', 'can', 'will', 'just', 'don', 'should', 'now', 'what',
            'is', 'am', 'are', 'was', 'were', 'be', 'been', 'being', 'has', 'have',
            'had', 'do', 'does', 'did', 'having', 'he', 'she', 'it', 'they', 'them',
            'his', 'her', 'its', 'their', 'my', 'your', 'our', 'we', 'you', 'who',
            'whom', 'which', 'this', 'that', 'these', 'those', 'I', 'me', 'mine',
            'yours', 'ours', 'himself', 'herself', 'itself', 'themselves'
        ];

        $words = explode(' ', $text);
        $filteredWords = array_diff($words, $stopwords);

        return implode(' ', $filteredWords);
    }
}
