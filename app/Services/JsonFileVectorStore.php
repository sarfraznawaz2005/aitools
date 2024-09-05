<?php

namespace App\Services;

use App\LLM\LlmProvider;
use Exception;

class JsonFileVectorStore
{
    use AISearchCommonTrait;

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

        foreach ($this->textSplits as $chunks) {
            foreach ($chunks as $index => $chunk) {
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
        $path = storage_path('app/notes.json');

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

        $data = [];
        $textSplits = [];
        foreach ($splits as $split) {
            $textSplits[] = $split['text'];
        }

        $chunks = array_chunk($textSplits, $this->getEmbdeddingBatchSize());

        foreach ($chunks as $chunk) {
            $embeddings = $this->llm->embed($chunk, $this->getEmbdeddingModel());

            foreach ($splits as $splitIndex => $splitItem) {
                $splits[$splitIndex]['embeddings'] = $embeddings['embeddings'][$splitIndex]['values'];
            }

            $data[] = $splits;
        }

        $this->embeddingsCache[$cacheKey] = $data;

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
        info("notes indexing file saved at: $path");

        return $data;
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
            // Gemini structure
            $queryEmbeddingValues = $queryEmbeddings['embeddings'][0]['values'];
        } elseif (isset($queryEmbeddings[0]['embedding'])) {
            // OpenAI structure
            $queryEmbeddingValues = $queryEmbeddings[0]['embedding'];
        } else {
            throw new Exception("Unknown query embeddings format.");
        }

        $iterations = 0;

        // Iterate over the main text splits array
        foreach ($this->textSplits as $mainIndex => $textItems) {
            foreach ($textItems as $index => $textItem) {
                if (isset($textItem['embeddings'])) {
                    $embeddingValues = $textItem['embeddings'];

                    $iterations++;
                    // Process embedding using the helper function
                    $this->processEmbedding($embeddingValues, $queryEmbeddingValues, $mainIndex, $index, $iterations, $results, $alreadyAdded);
                } else {
                    throw new Exception("Unknown embedding format!.");
                }
            }
        }

        return $results;
    }

}
