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

    public function __construct($llmProvider, string $key, int $chunkSize = 1000, float $similarityThreshold = 0.6, int $maxResults = 3)
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
        $results = [];

        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query)], 'embedding-001');
        $textEmbeddingsArray = $this->getTextEmbeddingsFromFiles($files);

        foreach ($textEmbeddingsArray as $textEmbedding) {
            $results = array_merge($results, $this->compareEmbeddings($textEmbedding, $queryEmbeddings));
        }

        usort($results, function ($a, $b) {
            return $a['similarity'] <=> $b['similarity'];
        });

        return $this->getTopResults($query, $results);
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
        if (str_contains($file, '.pdf')) {
            $pdf = $this->parser->parseFile($file);

            return $pdf->getText();
        }

        return file_get_contents($file);
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

        for ($i = 0; $i < count($textSplits); $i++) {
            $similarity = $this->cosineSimilarity($textEmbedding['embeddings']['embeddings'][$i]['values'], $queryEmbeddings['embeddings'][0]['values']);

            if ($similarity >= $this->similarityThreshold) {
                $results[] = [
                    'similarity' => $similarity,
                    'index' => $i,
                    'text' => $textSplits[$i],
                    'source' => $textEmbedding['source'],
                ];
            }
        }

        return $results;
    }

    protected function getTopResults(string $query, array $results): array
    {
        $topResults = array_slice($results, 0, $this->maxResults);

        $bestResult = null;
        $bestDistance = PHP_INT_MAX;

        foreach ($topResults as $result) {
            $distance = levenshtein($this->getCleanedText($query), $result['text']);

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $bestResult = $result;
            }
        }

        return $bestResult ?? ($topResults[0] ?? []);
    }

    protected function splitTextIntoChunks(string $text): array
    {
        $chunks = [];
        $currentChunk = "";
        $currentLength = 0;

        for ($i = 0; $i < strlen($text); $i++) {
            $currentChunk .= $text[$i];
            $currentLength++;

            if ($currentLength >= $this->chunkSize) {
                $chunks[] = trim($currentChunk);
                $currentChunk = "";
                $currentLength = 0;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    protected function cosineSimilarity(array $u, array $v): float
    {
        $dotProduct = 0;
        $uLength = 0;
        $vLength = 0;

        for ($i = 0; $i < count($u); $i++) {
            $dotProduct += $u[$i] * $v[$i];
            $uLength += $u[$i] * $u[$i];
            $vLength += $v[$i] * $v[$i];
        }

        $uLength = sqrt($uLength);
        $vLength = sqrt($vLength);

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
