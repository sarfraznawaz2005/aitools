<?php

namespace App\Services;

use App\LLM\LlmProvider;
use Exception;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Parser;

class DocumentSearchService
{
    private static ?DocumentSearchService $instance = null;

    protected Parser $parser;
    protected array $embeddings = [];
    private array $embeddingsCache = [];
    protected array $textSplits = [];

    private function __construct(protected LlmProvider $llm,
                                 protected string      $fileIdentifier,
                                 protected string      $embdeddingModel,
                                 protected int         $embdeddingsBatchSize = 100,
                                 protected int         $chunkSize = 500,
                                 protected float       $similarityThreshold = 0.6,
                                 protected int         $maxResults = 3)
    {
        $config = new Config();
        $config->setRetainImageContent(false);

        $this->parser = new Parser([], $config);
    }

    public static function getInstance(
        LlmProvider $llm,
        string      $fileIdentifier,
        string      $embdeddingModel,
        int         $embdeddingsBatchSize = 100,
        int         $chunkSize = 500,
        float       $similarityThreshold = 0.6,
        int         $maxResults = 3
    ): DocumentSearchService
    {
        if (self::$instance === null) {
            self::$instance = new self($llm, $fileIdentifier, $embdeddingModel, $embdeddingsBatchSize, $chunkSize, $similarityThreshold, $maxResults);
        }

        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function searchDocuments(array $files, string $query): array
    {
        $results = $this->performCosineSimilaritySearch($files, $query);

        if (!empty($results)) {
            if (app()->environment('local')) {
                info('Resutls found via cosine similarity');
            }

            return $this->getTopResults($results);
        }

        $results = $this->performTextSearch($files, $query);

        if (!empty($results)) {
            if (app()->environment('local')) {
                info('Resutls found via text search');
            }

            return $this->getTopResults($results);
        }

        if (app()->environment('local')) {
            info('No results found, giving suggested topics');
        }

        return $this->getListOfIdeas($files);
    }

    /**
     * @throws Exception
     */
    protected function performCosineSimilaritySearch(array $files, string $query): array
    {
        $results = [];

        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query, true)], $this->embdeddingModel);

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
            foreach ($this->textSplits[$file] as $chunks) {
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
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $results;
    }

    protected function getListOfIdeas(array $files): array
    {
        $prompt = <<<EOF
        Please convert following piece of text into brief list of topics user can ask questions about.
        Do not mention anything else except for providing list of topics in following format:

        - TOPIC 1
        - TOPIC 2
        - TOPIC 3

        {{TEXT}}
        EOF;

        $result = '';
        foreach ($files as $file) {
            foreach ($this->textSplits[$file] as $chunks) {
                foreach ($chunks as $chunk) {
                    $result .= $chunk['text'] . "\n";
                }
            }
        }

        $prompt = str_replace('{{TEXT}}', $this->getCleanedText($result), $prompt);

        $llmResult = $this->llm->chat($prompt);

        return [[
            'similarity' => $this->similarityThreshold,
            'index' => 0,
            'matchedChunk' => ['text' => $llmResult, 'metadata' => []],
        ]];
    }

    protected function calculateExactMatchScore(string $query, string $text): float
    {
        return stripos($text, $query) !== false ? $this->similarityThreshold : 0.0;
    }

    protected function calculateFuzzyMatchScore(string $query, string $text): float
    {
        $distance = levenshtein($query, $text);
        $maxLength = max(strlen($query), strlen($text));

        return $maxLength === 0 ? $this->similarityThreshold : 1 - ($distance / $maxLength);
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
        $cacheKey = "$fileName-" . $this->fileIdentifier;

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
            return trim($chunk['text']);
        }, $chunks);

        $textSplits = array_filter($textSplits);

        $embeddings = $this->llm->embed($textSplits, $this->embdeddingModel);

        $data = [
            'embeddings' => $embeddings,
            'chunks' => $chunks
        ];

        $this->embeddingsCache[$cacheKey] = $data;

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

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
                usleep(100000); // sleep for 100ms to avoid rate limiting
                $embeddings = $this->getEmbeddingsOrLoadFromCache($file, $chunkedText);
                $chunkedEmbeddings[$chunkIndex] = $embeddings;
                $chunkedTextSplits[$chunkIndex] = $chunkedText;
            }

            $this->textSplits[$file] = $chunkedTextSplits;
            $this->embeddings[$file] = $chunkedEmbeddings;

            // Free memory after processing each file
            unset($textWithMetadata, $chunks, $chunkedTextArray, $chunkedEmbeddings, $chunkedTextSplits);
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

                $text = [];
                $pdf = $this->parser->parseFile($file);
                $pages = $pdf->getPages();

                foreach ($pages as $pageNumber => $page) {
                    $text[] = [
                        'text' => $this->getCleanedText($page->getText()),
                        'metadata' => ['source' => basename($file), 'page' => $pageNumber + 1]
                    ];
                }

                //file_put_contents('pdf_text', json_encode($text, JSON_PRETTY_PRINT));

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

                if (isset($embeddings['embeddings'])) {
                    // Gemini structure: 'embeddings' => array of arrays with 'values'
                    $embeddingValues = array_column($embeddings['embeddings'], 'values');
                } else {
                    // OpenAI structure: direct array of embedding values
                    $embeddingValues = [$embeddings];
                }

                foreach ($embeddingValues as $index => $embedding) {
                    // Gemini structure for queryEmbeddings
                    if (isset($queryEmbeddings['embeddings'])) {
                        $queryEmbeddingValues = $queryEmbeddings['embeddings'][0]['values'];
                    } else {
                        // OpenAI structure for queryEmbeddings
                        $queryEmbeddingValues = $queryEmbeddings;
                    }

                    $similarity = $this->cosineSimilarity($embedding, $queryEmbeddingValues);

                    if ($similarity >= $this->similarityThreshold) {

                        if (isset($this->textSplits[$file][$mainIndex][$index])) {
                            $matchedText = $this->textSplits[$file][$mainIndex][$index];
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

    protected function getTopResults(array $results): array
    {
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
                '/[^\w\s\-$%_.\/ ]/',            // Allow only letters, numbers, $, -, _, %, /, ., and space
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
