<?php

namespace App\Services;

use App\LLM\LlmProvider;
use Exception;
use Smalot\PdfParser\Parser;

class DocumentSearchService
{
    protected LlmProvider $llm;
    protected float $similarityThreshold = 0.6;
    protected int $chunkSize = 1000;
    protected string $key = '';
    protected Parser $parser;
    protected int $maxResults;

    public function __construct(LlmProvider $llmProvider, string $key, int $chunkSize = 1000, float $similarityThreshold = 0.6, int $maxResults = 3)
    {
        $this->llm = $llmProvider;
        $this->key = $key;
        $this->chunkSize = $chunkSize;
        $this->similarityThreshold = $similarityThreshold;
        $this->maxResults = $maxResults;
        $this->parser = new Parser();
    }

    /**
     * @throws Exception
     */
    public function searchDocuments(array $files, string $query): array
    {
        $results = $this->performCosineSimilaritySearch($files, $query);

        if (empty($results)) {
            $results = $this->performTextSearch($files, $query);
        }

        return $this->getTopResults($query, $results);
    }

    /**
     * @throws Exception
     */
    protected function performCosineSimilaritySearch(array $files, string $query): array
    {
        $results = [];

        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query)], 'embedding-001');
        $textEmbeddingsArray = $this->getTextEmbeddingsFromFiles($files);

        foreach ($textEmbeddingsArray as $textEmbedding) {
            $results = array_merge($results, $this->compareEmbeddings($textEmbedding, $queryEmbeddings));
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $results;
    }

    /**
     * @throws Exception
     */
    protected function performTextSearch(array $files, string $query): array
    {
        $results = [];
        $cleanedQuery = $this->getCleanedText($query);

        foreach ($files as $file) {
            $text = $this->extractTextFromFile($file);
            $textSplits = $this->splitTextIntoChunks($this->getCleanedText($text));

            foreach ($textSplits as $index => $chunk) {
                if (stripos($chunk, $cleanedQuery) !== false) {
                    $results[] = [
                        'similarity' => 1 - (levenshtein($cleanedQuery, $chunk) / max(strlen($cleanedQuery), strlen($chunk))),
                        'index' => $index,
                        'text' => $chunk,
                        'source' => basename($file),
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
    protected function getTextEmbeddingsFromFiles(array $files): array
    {
        $textEmbeddingsArray = [];

        foreach ($files as $file) {
            $text = $this->extractTextFromFile($file);
            $textSplits = $this->splitTextIntoChunks($this->getCleanedText($text));
            $textEmbeddings = $this->getEmbeddingsOrLoadFromCache($file, $textSplits);

            $textEmbeddingsArray[] = [
                'textSplits' => $textSplits,
                'embeddings' => $textEmbeddings,
                'source' => basename($file),
            ];
        }

        return $textEmbeddingsArray;
    }

    /**
     * @throws Exception
     */
    protected function extractTextFromFile(string $file): string
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        return match (strtolower($extension)) {
            'pdf' => $this->parser->parseFile($file)->getText(),
            'txt', 'html', 'htm' => file_get_contents($file),
            default => throw new Exception("Unsupported file type: $extension"),
        };
    }

    protected function getEmbeddingsOrLoadFromCache(string $file, array $textSplits): array
    {
        $fileName = basename($file);
        $path = storage_path("app/$fileName-" . $this->key . '.json');

        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }

        $embeddings = $this->llm->embed($textSplits, 'embedding-001');
        file_put_contents($path, json_encode($embeddings));

        return $embeddings;
    }

    /**
     * @throws Exception
     */
    protected function compareEmbeddings(array $textEmbedding, array $queryEmbeddings): array
    {
        $results = [];
        $textSplits = $textEmbedding['textSplits'];

        if (count($textSplits) !== count($textEmbedding['embeddings']['embeddings'])) {
            throw new Exception("Splits and embeddings count mismatch!");
        }

        foreach ($textEmbedding['embeddings']['embeddings'] as $index => $embedding) {
            $similarity = $this->cosineSimilarity($embedding['values'], $queryEmbeddings['embeddings'][0]['values']);

            if ($similarity >= $this->similarityThreshold) {
                $results[] = [
                    'similarity' => $similarity,
                    'index' => $index,
                    'text' => $textSplits[$index],
                    'source' => $textEmbedding['source'],
                ];
            }
        }

        return $results;
    }

    protected function getTopResults(string $query, array $results): array
    {
        $topResults = array_slice($results, 0, $this->maxResults);

        $bestResult = array_reduce($topResults, function ($carry, $result) use ($query) {
            $distance = levenshtein($this->getCleanedText($query), $result['text']);
            return (!$carry || $distance < $carry['distance']) ? ['distance' => $distance, 'result' => $result] : $carry;
        });

        return $bestResult ? [$bestResult['result']] : ($topResults[0] ? [$topResults[0]] : []);
    }

    protected function splitTextIntoChunks(string $text): array
    {
        return array_map('trim', str_split($text, $this->chunkSize));
    }

    protected function cosineSimilarity(array $u, array $v): float
    {
        $dotProduct = array_sum(array_map(fn($x, $y) => $x * $y, $u, $v));
        $uLength = sqrt(array_sum(array_map(fn($x) => $x * $x, $u)));
        $vLength = sqrt(array_sum(array_map(fn($x) => $x * $x, $v)));

        return $dotProduct / ($uLength * $vLength);
    }

    protected function getCleanedText(string $text): string
    {
        $cleanedText = strip_tags($text);
        $cleanedText = preg_replace('/\s+/', ' ', $cleanedText);
        $cleanedText = preg_replace('/\r\n|\r/', "\n", $cleanedText);
        $cleanedText = preg_replace('/(\s*\n\s*){3,}/', "\n\n", $cleanedText);

        return trim($cleanedText);
    }
}
