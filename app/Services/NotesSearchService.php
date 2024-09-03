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
                                 protected int         $chunkSize,
                                 protected int         $maxResults)
    {
    }

    public static function getInstance(
        LlmProvider $llm,
        int         $chunkSize = 1000,
        int         $maxResults = 2
    ): NotesSearchService
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
    protected function performCosineSimilaritySearch(array $notes, string $query): array
    {
        $results = [];

        info('embedding query');
        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query, true)], $this->getEmbdeddingModel());

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

    /**
     * @throws Exception
     */
    protected function setTextEmbeddingsFromTexts(array $notes): void
    {
        $splits = [];
        $entries = [];
        info('setTextEmbeddingsFromTexts');
        foreach ($notes as $note) {
            $textWithMetadata = $this->getTextWithMetaData($note);
            $splits[] = $this->splitTextIntoChunks($textWithMetadata);
        }

        $chunks = array_chunk($splits, $this->getEmbdeddingBatchSize());

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

//        $cacheKey = 'notes_cache_' . md5(json_encode($texts));
//        if (array_key_exists($cacheKey, $this->embeddingsCache)) {
//            //info("Loaded embeddings from cache for $fileName");
//            return $this->embeddingsCache[$cacheKey]['embeddings'];
//        }

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            info("Loaded embeddings from file for $path");
            return $data['embeddings'];
        }

        $textSplits = array_map(function ($chunk) {
            return $chunk['text'];
        }, $texts);

        $embeddings = $this->llm->embed($textSplits, $this->getEmbdeddingModel());

        $data = [
            'embeddings' => $embeddings,
            'chunks' => $texts
        ];

        //$this->embeddingsCache[$cacheKey] = $data;

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
        info("file saved at: $path");

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
        info('compareEmbeddings');
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

        foreach ($this->embeddings as $mainIndex => $embeddings) {
            foreach ($embeddings as $index => $embedding) {
                // Check the structure and handle accordingly
                if (isset($embedding['embedding'])) {
                    // OpenAI structure
                    $embeddingValues = $embedding['embedding'];
                    $iterations++;
                    $this->processEmbedding($embeddingValues, $queryEmbeddingValues, $mainIndex, $index, $iterations, $results, $alreadyAdded);
                } elseif (isset($embedding['values'])) {
                    // Gemini structure, single embedding with 'values' key
                    $embeddingValues = $embedding['values'];
                    $iterations++;
                    $this->processEmbedding($embeddingValues, $queryEmbeddingValues, $mainIndex, $index, $iterations, $results, $alreadyAdded);
                } elseif (isset($embedding[0]['values'])) {
                    // Gemini structure, multiple embeddings within an array
                    foreach ($embedding as $subIndex => $subEmbedding) {
                        $embeddingValues = $subEmbedding['values'];
                        $iterations++;
                        $this->processEmbedding($embeddingValues, $queryEmbeddingValues, $mainIndex, (int)$subIndex, $iterations, $results, $alreadyAdded);
                    }
                } else {
                    throw new Exception("Unknown embedding format.");
                }
            }
        }

        return $results;
    }
}
