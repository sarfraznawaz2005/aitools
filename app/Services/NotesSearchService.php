<?php

namespace App\Services;

use App\LLM\LlmProvider;
use Exception;

class NotesSearchService
{
    use AISearchCommonTrait;

    private static ?NotesSearchService $instance = null;

    protected array $embeddings = [];
    private array $embeddingsCache = [];
    protected array $textSplits = [];

    private function __construct(protected LlmProvider $llm,
                                 protected string      $embdeddingModel,
                                 protected int         $embdeddingsBatchSize,
                                 protected int         $chunkSize,
                                 protected float       $similarityThreshold,
                                 protected int         $maxResults)
    {
    }

    public static function getInstance(
        LlmProvider $llm,
        string      $embdeddingModel,
        int         $embdeddingsBatchSize = 100,
        int         $chunkSize = 500,
        float       $similarityThreshold = 0.54,
        int         $maxResults = 3
    ): NotesSearchService
    {
        if (self::$instance === null) {
            self::$instance = new self($llm, $embdeddingModel, $embdeddingsBatchSize, $chunkSize, $similarityThreshold, $maxResults);
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
    protected function performCosineSimilaritySearch(array $notes, string $query): array
    {
        $results = [];

        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query, true)], $this->embdeddingModel);

        $this->setTextEmbeddingsFromTexts($notes);

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

                if ($maxScore >= $this->similarityThreshold) {
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

    /**
     * @throws Exception
     */
    protected function setTextEmbeddingsFromTexts(array $notes): void
    {
        $splits = [];
        $entries = [];

        foreach ($notes as $note) {
            $textWithMetadata = $this->getTextWithMetaData($note);
            $splits[] = $this->splitTextIntoChunks($textWithMetadata);
        }

        // Chunk the text based on $embdeddingsBatchSize
        $chunks = array_chunk($splits, $this->embdeddingsBatchSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $chunkEntry) {
                foreach ($chunkEntry as $entry) {
                    $entries[] = $entry;
                }
            }
        }

        $embeddings = $this->getEmbeddingsOrLoadFromCache($entries);

        $this->textSplits[] = $entries;
        $this->embeddings[] = $embeddings;
    }

    protected function getTextWithMetaData(array $note): array
    {
        $text = [];
        $lines = explode("\n", $note['content']);

        foreach ($lines as $lineNumber => $line) {
            $text[] = [
                'text' => $line,
                'metadata' => ['source' => $note['folder'], 'title' => $note['title'], 'line' => $lineNumber + 1]
            ];
        }

        $text = array_map(fn($item) => ['text' => $this->getCleanedText($item['text']), 'metadata' => $item['metadata']], $text);

        return array_filter($text, fn($item) => !empty(trim($item['text'])) && strlen(trim($item['text'])) > 25);
    }

    protected function getEmbeddingsOrLoadFromCache(array $texts): array
    {
        $path = storage_path('app/notes.json');
        $cacheKey = 'notes_cache_' . md5(json_encode($texts));

        if (array_key_exists($cacheKey, $this->embeddingsCache)) {
            //info("Loaded embeddings from cache for $fileName");
            return $this->embeddingsCache[$cacheKey]['embeddings'];
        }

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            //info("Loaded embeddings from file for $fileName");
            return $data['embeddings'];
        }

        $textSplits = array_map(function ($chunk) {
            return $chunk['text'];
        }, $texts);

        $embeddings = $this->llm->embed($textSplits, $this->embdeddingModel);

        $data = [
            'embeddings' => $embeddings,
            'chunks' => $texts
        ];

        $this->embeddingsCache[$cacheKey] = $data;

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        return $embeddings;
    }


    /**
     * @throws Exception
     */
    protected function compareEmbeddings(array $queryEmbeddings): array
    {
        $results = [];
        $alreadyAdded = [];

        if (count($this->textSplits) !== count($this->embeddings)) {
            throw new Exception("Splits and embeddings count mismatch!");
        }

        // Gemini structure for queryEmbeddings
        if (isset($queryEmbeddings['embeddings'])) {
            $queryEmbeddingValues = $queryEmbeddings['embeddings'][0]['values'];
        } else {
            // OpenAI structure for queryEmbeddings
            $queryEmbeddingValues = $queryEmbeddings;
        }

        foreach ($this->embeddings as $topIndex => $embeddings) {
            foreach ($embeddings as $embeddingEntry) {
                foreach ($embeddingEntry as $index => $entry) {

                    // Gemini or OpenAI
                    $embedding = $entry['values'] ?? $entry;

                    $similarity = $this->cosineSimilarity($embedding, $queryEmbeddingValues);
                    //dump($similarity);

                    if ($similarity >= $this->similarityThreshold) {
                        if (isset($this->textSplits[$topIndex][$index])) {
                            $matchedText = $this->textSplits[$topIndex][$index];
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

                    }
                }
            }
        }

        return $results;
    }
}
