<?php

namespace App\Services;

use App\LLM\LlmProvider;
use Exception;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Parser;

class DocumentSearchService
{
    use AISearchCommonTrait;

    private static ?DocumentSearchService $instance = null;

    protected Parser $parser;
    protected array $embeddings = [];
    private array $embeddingsCache = [];
    protected array $textSplits = [];

    private function __construct(protected LlmProvider $llm,
                                 protected string      $fileIdentifier,
                                 protected int         $chunkSize,
                                 protected int         $maxResults)
    {
        $config = new Config();
        $config->setRetainImageContent(false);

        $this->parser = new Parser([], $config);
    }

    public static function getInstance(
        LlmProvider $llm,
        string      $fileIdentifier,
        int         $chunkSize = 1000,
        int         $maxResults = 2
    ): DocumentSearchService
    {
        if (self::$instance === null) {
            self::$instance = new self($llm, $fileIdentifier, $chunkSize, $maxResults);
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

        $queryEmbeddings = $this->llm->embed([$this->getCleanedText($query, true)], $this->getEmbdeddingModel());

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

                    if ($maxScore >= $this->getSimiliarityThreashold()) {
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
            'similarity' => $this->getSimiliarityThreashold(),
            'index' => 0,
            'matchedChunk' => ['text' => $llmResult, 'metadata' => []],
        ]];
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

        $embeddings = $this->llm->embed($textSplits, $this->getEmbdeddingModel());

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

            $chunkedTextArray = array_chunk($chunks, $this->getEmbdeddingBatchSize());

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
                        'text' => $page->getText(),
                        'metadata' => ['source' => basename($file), 'page' => $pageNumber + 1]
                    ];
                }

                //file_put_contents('pdf_text', json_encode($text, JSON_PRETTY_PRINT));

                $text = array_map(function ($item) {
                    $item['text'] = $this->getCleanedText($item['text']);
                    return $item;
                }, $text);

                return array_filter($text, fn($item) => !empty(trim($item['text'])) && strlen(trim($item['text'])) > 10);
            case 'txt':
            case 'md':
            case 'html':
            case 'htm':
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                $text = [];

                foreach ($lines as $lineNumber => $line) {
                    $text[] = [
                        'text' => $line,
                        'metadata' => ['source' => basename($file), 'line' => $lineNumber + 1]
                    ];
                }

                $text = array_map(function ($item) {
                    $item['text'] = $this->getCleanedText($item['text']);
                    return $item;
                }, $text);

                return array_filter($text, fn($item) => !empty(trim($item['text'])) && strlen(trim($item['text'])) > 10);
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

        foreach ($this->embeddings as $fileEmbeddings) {
            foreach ($fileEmbeddings as $mainIndex => $embeddings) {
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
        }

        return $results;
    }
}
