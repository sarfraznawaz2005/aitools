<?php
/*
 * Requires: yooper/php-text-analysis

    HOW TO USE:

    $llm = new GeminiProvider($model->api_key, $model->model_name, ['maxOutputTokens' => 8192, 'temperature' => 1.0]);

    $texts = [
        [
            'text' => 'Text 1',
            'source' => 'Text 1 Source',
        ],
        [
            'text' => 'Text 2',
            'source' => 'Text 2 Source',
        ],
    ];

    $searchService = JsonFileVectorStore::getInstance($llm, 'data.json', 2000);
    return $searchService->searchTexts($texts, $query);

 */

namespace App\Services;

use App\Constants;
use App\Enums\ApiKeyTypeEnum;
use App\LLM\GeminiProvider;
use App\LLM\LlmProvider;
use App\LLM\OpenAiProvider;
use Exception;
use TextAnalysis\Analysis\FreqDist;
use TextAnalysis\Documents\TokensDocument;
use TextAnalysis\Exceptions\InvalidParameterSizeException;
use TextAnalysis\Stemmers\PorterStemmer;
use TextAnalysis\Tokenizers\GeneralTokenizer;

class JsonFileVectorStore
{
    private static ?JsonFileVectorStore $instance = null;

    private array $embeddingsCache = [];
    private array $textSplits = [];
    private string $fileName;

    private function __construct(
        protected LlmProvider $llm,
        string                $fileName,
        protected int         $chunkSize,
        protected int         $maxResults)
    {
        $this->fileName = $fileName;
    }

    public static function getInstance(
        LlmProvider $llm,
        string      $fileName,
        int         $chunkSize = 1000,
        int         $maxResults = 3
    ): JsonFileVectorStore
    {
        if (self::$instance === null) {
            self::$instance = new self($llm, $fileName, $chunkSize, $maxResults);
        }

        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function searchTexts(array $notes, string $query): array
    {
        // full semantic search
        $results = $this->performLLMSemanticSearch($notes, $query);

        if (!empty($results)) {
            if (app()->environment('local')) {
                info('Resutls found via semantic search');
            }

            return $this->getTopResults($results);
        }

        // partial semantic search
        $results = $this->performTFIDFSearch($notes, $query);

        if (!empty($results)) {
            if (app()->environment('local')) {
                info('Resutls found via partial semantic search');
            }

            return $this->getTopResults($results);
        }

        // direct text search
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
    protected function performLLMSemanticSearch(array $texts, string $query): array
    {
        $results = [];

        $queryEmbeddings = $this->llm->embed(
            [$this->getCleanedText($query, true)],
            $this->getEmbdeddingModel(),
        );

        $this->setTextEmbeddingsFromTexts($texts);

        $results = array_merge($results, $this->compareEmbeddings($queryEmbeddings));

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $results;
    }

    protected function performTFIDFSearch(array $texts, string $query): array
    {
        $texts = array_map(function ($doc) {
            return $doc['text'];
        }, $texts);

        $tokenizedDocs = array_map(function ($doc) {
            $cleanedText = $this->getCleanedText($doc, true);

            return new TokensDocument(explode(' ', $cleanedText));
        }, $texts);

        // Clean and tokenize the query
        $cleanedQuery = $this->getCleanedText($query, true);
        $tokenizedQuery = new TokensDocument(explode(' ', $cleanedQuery));

        $totalDocs = count($tokenizedDocs);
        $docDF = $this->calculateDF($tokenizedDocs); // Document Frequencies

        $docVectors = [];
        foreach ($tokenizedDocs as $doc) {
            $tf = $this->calculateTF($doc->getDocumentData());
            $docVectors[] = $this->calculateTFIDF($tf, $docDF, $totalDocs);
        }

        $queryTF = $this->calculateTF($tokenizedQuery->getDocumentData());
        $queryVector = $this->calculateTFIDF($queryTF, $docDF, $totalDocs);

        $rankedResults = [];
        foreach ($docVectors as $key => $docVector) {
            $similarity = $this->cosineSimilarity($docVector, $queryVector);

            $rankedResults[] = [
                'similarity' => $similarity,
                'index' => $key,
                'matchedChunk' => ['text' => $this->getCleanedText($texts[$key]), 'metadata' => $this->textSplits[$key]['metadata']],
            ];
        }

        usort($rankedResults, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Only return results with similarity > 0
        return array_filter($rankedResults, fn($item) => $item['similarity'] > 0);
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

    protected function calculateTF($tokens): array
    {
        try {
            $freqDist = new FreqDist($tokens);
        } catch (InvalidParameterSizeException $e) {
            return $tokens;
        }

        return $freqDist->getKeyValuesByFrequency();
    }

    protected function calculateDF($documents): array
    {
        $df = [];

        foreach ($documents as $tokens) {
            $uniqueTokens = array_unique($tokens->getDocumentData());
            foreach ($uniqueTokens as $token) {
                if (!isset($df[$token])) {
                    $df[$token] = 0;
                }
                $df[$token]++;
            }
        }

        return $df;
    }

    protected function calculateTFIDF($tf, $df, $totalDocs): array
    {
        $tfidf = [];

        foreach ($tf as $term => $count) {
            $idf = log($totalDocs / ($df[$term] ?? 1)); // Inverse Document Frequency
            $tfidf[$term] = $count * $idf;
        }

        return $tfidf;
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
        $path = storage_path('app/' . $this->fileName);

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

            $embeddings = $embeddings['embeddings'] ?? $embeddings; // gemini or openai

            foreach ($embeddings as $embeddingIndex => $embedding) {
                // Map the embedding back to the correct split in the original $splits array
                if (isset($splits[$embeddingIndex])) {
                    $splits[$embeddingIndex]['embeddings'] = $embedding['embedding'] ?? $embedding['values'];
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
        } elseif (isset($queryEmbeddings['embedding']['values'])) {
            $queryEmbeddingValues = $queryEmbeddings['embedding']['values'];
        } elseif (isset($queryEmbeddings[0]['embedding'])) {
            $queryEmbeddingValues = $queryEmbeddings[0]['embedding'];
        } else {
            throw new Exception("Unknown query embeddings format.");
        }

        $iterations = 0;

        foreach ($this->textSplits as $index => $split) {
            if (isset($split['embeddings'])) {
                $embeddingValues = $split['embeddings'];

                // Increment iterations
                $iterations++;

                // Calculate cosine similarity
                $similarity = $this->cosineSimilarity($embeddingValues, $queryEmbeddingValues);

                // Log similarity and iteration for debugging (optional)
                //info("Iteration #: $iterations, Similarity: $similarity");

                if ($similarity >= $this->getSimiliarityThreashold()) {
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
                } else {
                    //info("NOT FOUND at #: $iterations, Similarity: $similarity");
                }
            } else {
                throw new Exception("Unknown embedding format!.");
            }
        }

        return $results;
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

    protected function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        foreach ($vecA as $key => $valueA) {
            $valueB = $vecB[$key] ?? 0;
            $dotProduct += $valueA * $valueB;
            $magnitudeA += $valueA * $valueA;
            $magnitudeB += $valueB * $valueB;
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA * $magnitudeB == 0) {
            return 0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
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
        $text = strtolower(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5)));

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

        // Tokenize text
        $tokenizer = new GeneralTokenizer();
        $tokens = $tokenizer->tokenize($text);

        if ($removeStopWords) {
            $text = implode(' ', $tokens); // Join tokens to remove stopwords
            $text = $this->removeStopwords($text); // Use your custom stopword removal function
            $tokens = explode(' ', $text); // Tokenize again
        }

        // Apply stemming
        $stemmer = new PorterStemmer();
        $tokens = array_map([$stemmer, 'stem'], $tokens);

        return implode(' ', $tokens);
    }

    protected function removeStopwords(string $text): string
    {
        $stopWords = [
            'the', 'a', 'an', 'and', 'but', 'if', 'or', 'because', 'as', 'until', 'while', 'of', 'at',
            'by', 'for', 'with', 'about', 'against', 'between', 'into', 'through', 'during', 'before',
            'after', 'above', 'below', 'to', 'from', 'up', 'down', 'in', 'out', 'on', 'off', 'over',
            'under', 'again', 'further', 'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how',
            'all', 'any', 'both', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor',
            'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', 'can', 'will', 'just', 'don',
            'should', 'now', 'what', 'is', 'am', 'are', 'was', 'were', 'be', 'been', 'being', 'has',
            'have', 'had', 'do', 'does', 'did', 'having', 'he', 'she', 'it', 'they', 'them', 'his',
            'her', 'its', 'their', 'my', 'your', 'our', 'we', 'you', 'who', 'whom', 'which', 'this',
            'that', 'these', 'those', 'I', 'me', 'mine', 'yours', 'ours', 'himself', 'herself', 'itself',
            'themselves', 'aren\'t', 'can\'t', 'cannot', 'could', 'couldn\'t', 'didn\'t', 'doesn\'t',
            'doing', 'don\'t', 'hadn\'t', 'hasn\'t', 'haven\'t', 'he\'d', 'he\'ll', 'he\'s', 'here\'s',
            'hers', 'him', 'how\'s', 'i', 'i\'d', 'i\'ll', 'i\'m', 'i\'ve', 'isn\'t', 'it\'s', 'let\'s',
            'mustn\'t', 'myself', 'ought', 'ourselves', 'she\'d', 'she\'ll', 'she\'s', 'shouldn\'t',
            'that\'s', 'theirs', 'there\'s', 'they\'d', 'they\'ll', 'they\'re', 'they\'ve', 'wasn\'t',
            'we\'d', 'we\'ll', 'we\'re', 'we\'ve', 'weren\'t', 'what\'s', 'when\'s', 'where\'s',
            'who\'s', 'why\'s', 'won\'t', 'would', 'wouldn\'t', 'you\'d', 'you\'ll', 'you\'re',
            'you\'ve', 'yourself', 'yourselves'
        ];

        $words = explode(' ', $text);
        $filteredWords = array_diff($words, $stopWords);

        return implode(' ', $filteredWords);
    }
}
