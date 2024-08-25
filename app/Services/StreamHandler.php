<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class StreamHandler
{
    const string END_STREAM_SIGNAL = '<END_STREAMING_SSE>';

    private static ?StreamHandler $instance = null;

    // Private constructor to prevent direct instantiation
    private function __construct()
    {
    }

    // Static method to get the singleton instance
    public static function getInstance(): StreamHandler
    {
        if (self::$instance === null) {
            self::$instance = new StreamHandler();
        }

        return self::$instance;
    }

    public function sendStream(string $text, bool $sendCloseSignal = false): void
    {
        $output = "event: update\n";

        if (!$sendCloseSignal) {
            $encodedText = json_encode($text);
            if ($encodedText === false) {
                Log::error('JSON encoding error: ' . json_last_error_msg());
                return;
            }
            $output .= "data: " . $encodedText . "\n\n";
        } else {
            $output .= "data: " . self::END_STREAM_SIGNAL . "\n\n";
        }

        $this->writeOutput($output);
    }

    private function writeOutput(string $output): void
    {
        // Write to stdout for NativePHP
        $stream = fopen('php://stdout', 'w');
        if ($stream === false) {
            Log::error('Failed to open stdout stream.');
            return;
        }

        if (fwrite($stream, $output) === false) {
            Log::error('Failed to write to stdout stream.');
            fclose($stream);
            return;
        }

        fflush($stream);
        fclose($stream);

        // Echo for browser SSE
        echo $output;

        // Check and flush output buffers once
        if (ob_get_level() > 0) {
            ob_end_flush(); // Close and flush the output buffer
        }

        flush();
    }
}

