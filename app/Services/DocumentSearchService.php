<?php

namespace App\Services;

use App\LLM\LlmProvider;
use Exception;
use Smalot\PdfParser\Parser;

class DocumentSearchService
{
    protected Parser $parser;

    public function __construct(
        protected LlmProvider $llm,
        protected string      $fileIdentifier,
        protected int         $chunkSize = 500,
        protected float       $similarityThreshold = 0.6,
        protected int         $maxResults = 3,
    )
    {
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

        return $this->getTopResults($results);
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

        return $this->deduplicateAndAddContext($results);
    }

    /**
     * @throws Exception
     */
    protected function performTextSearch(array $files, string $query): array
    {
        $results = [];
        $cleanedQuery = $this->getCleanedText($query);

        foreach ($files as $file) {
            $textWithMetadata = $this->extractTextFromFile($file);
            $chunks = $this->splitTextIntoChunks($textWithMetadata);

            foreach ($chunks as $index => $chunk) {
                if (stripos($chunk['text'], $cleanedQuery) !== false) {
                    $results[] = [
                        'similarity' => 1 - (levenshtein($cleanedQuery, $chunk['text']) / max(strlen($cleanedQuery), strlen($chunk['text']))),
                        'index' => $index,
                        'text' => $chunk['text'],
                        'source' => basename($file),
                        'metadata' => $chunk['metadata'],
                    ];
                }
            }
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $this->deduplicateAndAddContext($results);
    }

    protected function deduplicateAndAddContext(array $results): array
    {
        $deduplicatedResults = [];
        $seenContent = [];

        foreach ($results as $result) {
            $hash = md5($result['text']);

            if (!isset($seenContent[$hash])) {
                $seenContent[$hash] = true;

                // Add context by including surrounding chunks
                $contextBefore = $this->getChunkAtIndex($result['source'], $result['index'] - 1);
                $contextAfter = $this->getChunkAtIndex($result['source'], $result['index'] + 1);

                $result['text'] = trim($contextBefore['text'] . "\n" . $result['text'] . "\n" . $contextAfter['text']);

                $result['metadata'] = array_merge(
                    $contextBefore['metadata'] ?? [],
                    $result['metadata'],
                    $contextAfter['metadata'] ?? []
                );

                $deduplicatedResults[] = $result;

                if (count($deduplicatedResults) >= $this->maxResults) {
                    break;
                }
            }
        }

        return $deduplicatedResults;
    }

    protected function getChunkAtIndex(string $source, int $index): array
    {
        $fileName = $source;
        $path = storage_path("app/$fileName-" . $this->fileIdentifier . '.json');

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            $chunks = $data['chunks'] ?? [];

            if (isset($chunks[$index])) {
                return $chunks[$index];
            }
        }

        return ['text' => '', 'metadata' => []];
    }

    protected function getEmbeddingsOrLoadFromCache(string $file, array $chunks): array
    {
        $fileName = basename($file);
        $path = storage_path("app/$fileName-" . $this->fileIdentifier . '.json');

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            return $data['embeddings'];
        }

        $textSplits = array_map(function ($chunk) {
            return trim($chunk['text']);
        }, $chunks);

        $embeddings = $this->llm->embed($textSplits, 'embedding-001');

        $data = [
            'embeddings' => $embeddings,
            'chunks' => $chunks
        ];

        file_put_contents($path, json_encode($data));

        return $embeddings;
    }


    /**
     * @throws Exception
     */
    protected function getTextEmbeddingsFromFiles(array $files): array
    {
        $textEmbeddingsArray = [];

        foreach ($files as $file) {
            $textWithMetadata = $this->extractTextFromFile($file);
            $chunks = $this->splitTextIntoChunks($textWithMetadata);
            $textEmbeddings = $this->getEmbeddingsOrLoadFromCache($file, $chunks);

            $textEmbeddingsArray[] = [
                'textSplits' => array_column($chunks, 'text'),
                'embeddings' => $textEmbeddings,
                'source' => basename($file),
                'metadata' => array_column($chunks, 'metadata'),
            ];
        }

        return $textEmbeddingsArray;
    }

    /**
     * @throws Exception
     */
    protected function extractTextFromFile(string $file): array
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        switch (strtolower($extension)) {
            case 'pdf':
                $pdf = $this->parser->parseFile($file);
                $pages = $pdf->getPages();
                $text = [];
                foreach ($pages as $pageNumber => $page) {
                    $text[] = [
                        'content' => $page->getText(),
                        'metadata' => ['page' => $pageNumber + 1]
                    ];
                }
                return $text;
            case 'txt':
            case 'md':
            case 'html':
            case 'htm':
                $content = file_get_contents($file);
                $lines = explode("\n", htmlToText($content));
                $text = [];
                foreach ($lines as $lineNumber => $line) {
                    $text[] = [
                        'content' => $line,
                        'metadata' => ['line' => $lineNumber + 1]
                    ];
                }
                return $text;
            default:
                throw new Exception("Unsupported file type: $extension");
        }
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
                    'metadata' => $textEmbedding['metadata'][$index],
                ];
            }
        }

        return $results;
    }

    protected function getTopResults(array $results): array
    {
        if (count($results) <= $this->maxResults) {
            return $results;
        }

        // return the top $maxResults
        return array_slice($results, 0, $this->maxResults);
    }

    protected function splitTextIntoChunks(array $textWithMetadata): array
    {
        $chunks = [];
        $overlap = $this->chunkSize * 0.3; // 30% overlap

        $fullText = implode("\n", array_column($textWithMetadata, 'content'));
        $totalLength = strlen($fullText);

        for ($i = 0; $i < $totalLength; $i += ($this->chunkSize - $overlap)) {
            $chunk = substr($fullText, $i, $this->chunkSize);
            $chunks[] = [
                'text' => trim($chunk),
                'metadata' => $this->getMetadataForChunk($textWithMetadata, $i, $i + strlen($chunk))
            ];
        }

        return $chunks;
    }

    protected function getMetadataForChunk(array $textWithMetadata, int $start, int $end): array
    {
        $metadata = [];
        $currentPosition = 0;

        foreach ($textWithMetadata as $item) {
            $length = strlen($item['content']);
            if ($currentPosition + $length >= $start && $currentPosition <= $end) {
                $metadata[] = $item['metadata'];
            }
            if ($currentPosition > $end) {
                break;
            }
            $currentPosition += $length + 1; // +1 for the newline
        }

        return $metadata;
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
        // Replace <br> tags with newlines
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);

        // Replace </p> tags with double newlines
        $text = preg_replace('/<\/p>/i', "\n\n", $text);

        // Remove all remaining HTML tags
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize line breaks
        $text = preg_replace('/\r\n|\r/', "\n", $text);

        // Replace any combination of more than two newlines and whitespace with two newlines
        $text = preg_replace('/(\s*\n\s*){3,}/', "\n\n", $text);

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
