<?php

namespace App\Services;

use App\LLM\OpenAiProvider;
use Exception;

trait AISearchCommonTrait
{
    protected function getSimiliarityThreashold(): float
    {
        // because there is difference in the cosine similarity values between OpenAI and Gemini
        if ($this->llm instanceof OpenAiProvider) {
            return 0.75;
        } else {
            return 0.6;
        }
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

    protected function getTopResults(array $results): array
    {
        return array_slice($results, 0, $this->maxResults);
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
