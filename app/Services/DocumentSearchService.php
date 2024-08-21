<?php

namespace App\Services;

use App\LLM\LlmProvider;
use Exception;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class DocumentSearchService
{
    protected Parser $parser;
    protected array $embeddings = [];
    protected array $textSplits = [];

    public function __construct(
        protected LlmProvider $llm,
        protected string      $fileIdentifier,
        protected int         $chunkSize = 500,
        protected int         $embdeddingsBatchSize = 100,
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

        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query, true)], 'embedding-001');

        $this->setTextEmbeddingsFromFiles($files);

        $results = array_merge($results, $this->compareEmbeddings($queryEmbeddings));

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $results;
    }

    /**
     * @throws Exception
     */
    protected function performTextSearch(array $files, string $query): array
    {
        $results = [];
        $cleanedQuery = $this->getCleanedText($query, true);

        foreach ($files as $file) {
            $textWithMetadata = $this->extractTextFromFile($file);
            $chunks = $this->splitTextIntoChunks($textWithMetadata);

            foreach ($chunks as $index => $chunk) {
                $exactMatchScore = $this->calculateExactMatchScore($cleanedQuery, $chunk['text']);
                $fuzzyMatchScore = $this->calculateFuzzyMatchScore($cleanedQuery, $chunk['text']);

                $maxScore = max($exactMatchScore, $fuzzyMatchScore);

                if ($maxScore >= $this->similarityThreshold) {
                    $results[] = [
                        'similarity' => $maxScore,
                        'index' => $index,
                        'matchedChunk' => $chunk,
                    ];
                }
            }
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $results;
    }

    protected function calculateExactMatchScore(string $query, string $text): float
    {
        if (stripos($text, $query) !== false) {
            return $this->similarityThreshold;
        }

        return 0.0;
    }

    protected function calculateFuzzyMatchScore(string $query, string $text): float
    {
        $distance = levenshtein($query, $text);
        $maxLength = max(strlen($query), strlen($text));

        if ($maxLength === 0) {
            return $this->similarityThreshold;
        }

        return 1 - ($distance / $maxLength);
    }

    public function isEmbdeddingDone(string $file, string $fileIdentifier): bool
    {
        $fileName = basename($file);
        $path = storage_path("app/$fileName-" . $fileIdentifier . '.json');

        return file_exists($path);
    }

    protected function getEmbeddingsOrLoadFromCache(string $file, array $chunks): array
    {
        $fileName = basename($file);
        $path = storage_path("app/$fileName-" . $this->fileIdentifier . '.json');

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            //Log::info("Loaded embeddings from cache for $fileName");
            return $data['embeddings'];
        }

        $textSplits = array_map(function ($chunk) {
            return trim($chunk['text']);
        }, $chunks);

        $textSplits = array_filter($textSplits);

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
    protected function setTextEmbeddingsFromFiles(array $files): void
    {
        foreach ($files as $file) {

            // already set
            if (isset($this->textSplits[$file]) && $this->textSplits[$file]) {
                continue;
            }

            $textWithMetadata = $this->extractTextFromFile($file);
            $chunks = $this->splitTextIntoChunks($textWithMetadata);

            // Chunk the text based on $embdeddingsBatchSize
            $chunkedTextArray = array_chunk($chunks, $this->embdeddingsBatchSize);

            $chunkedEmbeddings = [];
            $chunkedTextSplits = [];
            foreach ($chunkedTextArray as $chunkIndex => $chunkedText) {
                $embeddings = $this->getEmbeddingsOrLoadFromCache($file, $chunkedText);
                $chunkedEmbeddings[$chunkIndex] = $embeddings;
                $chunkedTextSplits[$chunkIndex] = $chunkedText;
            }

            $this->textSplits[$file] = $chunkedTextSplits;
            $this->embeddings[$file] = $chunkedEmbeddings;
        }
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
                        'text' => $this->getCleanedText($page->getText()),
                        'metadata' => ['source' => basename($file), 'page' => $pageNumber + 1]
                    ];
                }

                return $text;
            case 'txt':
            case 'md':
            case 'html':
            case 'htm':
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                $text = [];

                foreach ($lines as $lineNumber => $line) {
                    $text[] = [
                        'text' => $this->getCleanedText($line),
                        'metadata' => ['source' => basename($file), 'line' => $lineNumber + 1]
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
    protected function compareEmbeddings(array $queryEmbeddings): array
    {
        $results = [];
        $alreadyAdded = [];

        if (count($this->textSplits) !== count($this->embeddings)) {
            throw new Exception("Splits and embeddings count mismatch!");
        }

        foreach ($this->embeddings as $file => $fileEmbeddings) {
            foreach ($fileEmbeddings as $mainIndex => $embeddings) {

                foreach ($embeddings['embeddings'] as $index => $embedding) {
                    $similarity = $this->cosineSimilarity($embedding['values'], $queryEmbeddings['embeddings'][0]['values']);

                    if ($similarity >= $this->similarityThreshold) {

                        if (isset($this->textSplits[$file][$mainIndex][$index])) {
                            $matchedText = $this->textSplits[$file][$mainIndex][$index];
                            $hash = md5($matchedText['text']);

                            if (!isset($alreadyAdded[$hash])) {
                                $alreadyAdded[$hash] = true;

                                //Log::info("TEXT@$index:" . $matchedText['text']);

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
        $overlapPercentage = 30; // 30% overlap, adjust as needed
        $overlapSize = max(1, (int)($this->chunkSize * ($overlapPercentage / 100)));

        $fullText = implode("\n", array_column($textWithMetadata, 'text'));
        $totalLength = strlen($fullText);

        $chunkStart = 0;
        while ($chunkStart < $totalLength) {
            $chunkEnd = min($chunkStart + $this->chunkSize, $totalLength);
            $chunk = substr($fullText, $chunkStart, $chunkEnd - $chunkStart);

            $chunks[] = [
                'text' => trim($chunk),
                'metadata' => $this->getMetadataForChunk($textWithMetadata, $chunkStart, $chunkEnd - 1)
            ];

            if ($chunkEnd == $totalLength) {
                break;
            }

            $chunkStart += max(1, $this->chunkSize - $overlapSize);
        }

        return $chunks;
    }

    protected function getMetadataForChunk(array $textWithMetadata, int $start, int $end): array
    {
        $metadata = [];
        $currentPosition = 0;

        foreach ($textWithMetadata as $item) {
            $length = strlen($item['text']);

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

    protected function getCleanedText(string $text, bool $removeStopWords = false): string
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

        // remove punctuation symbols
        $text = preg_replace('/[^\w\s\-_.&*$@]/', '', $text);

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
