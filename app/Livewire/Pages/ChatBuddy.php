<?php

namespace App\Livewire\Pages;

use App\Constants;
use App\Models\Bot;
use App\Models\Conversation;
use App\Traits\InteractsWithToast;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;
use Smalot\PdfParser\Parser;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatBuddy extends Component
{
    use InteractsWithToast;

    public Conversation $conversation;

    public function chat(Conversation $conversation = null): StreamedResponse
    {
        if (is_null($conversation)) {
            return response()->stream(function () {

                sendStream("Error, conversation has been deleted!");
                sendStream("", true);

            }, 200, [
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]);
        }

        if ($conversation->bot->isDocumentBot()) {
            return $this->chatWithDocs($conversation);
        }

        return response()->stream(function () use ($conversation) {

            try {

                $prompt = $conversation->bot->prompt;
                $userQuery = $conversation->messages()->where('is_ai', false)->latest()->first();

                $latestMessage = $conversation
                    ->messages()
                    ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                    ->latest()
                    ->first();

                $latestMessages = $conversation
                    ->messages()
                    ->where('body', '!=', Constants::CHATBUDDY_LOADING_STRING)
                    ->whereNot(function ($query) {
                        $query
                            ->where('body', 'like', '%conversation history%')
                            ->orWhere('body', 'like', '%provided context%');
                    })
                    ->latest()
                    ->limit(Constants::CHATBUDDY_TOTAL_CONVERSATION_HISTORY)
                    ->get()
                    ->sortBy('id');

                $uniqueMessages = [];
                foreach ($latestMessages as $message) {

                    if ($message->id === $userQuery->id) {
                        continue;
                    }

                    $formattedMessage = ($message->is_ai ? 'ASSISTANT: ' : 'USER: ') . $message->body;

                    if (!in_array($formattedMessage, $uniqueMessages)) {
                        $uniqueMessages[] = htmlToText($formattedMessage);
                    }
                }

                $conversationHistory = implode("\n", $uniqueMessages);

                // switch to general bot temporarily in case of forced mode
                if (file_exists('forceAnswer')) {
                    $generalBotPrompt = Bot::query()->where('name', 'General')->first()->prompt;
                    $prompt = $generalBotPrompt;
                    @unlink('forceAnswer');
                }

                // add user's current question
                $conversationHistory .= "\nUSER:" . $userQuery->body;

                $prompt = makePromopt($userQuery->body, $conversationHistory, $prompt, 2);

                Log::info("\n" . str_repeat('-', 100) . "\n" . $prompt . "\n");

                $markdown = app(MarkdownRenderer::class);

                if (Constants::TEST_MODE) {
                    sleep(1);

                    $text = Constants::TEST_MESSAGE;

                    sendStream($text);

                    $latestMessage->update(['body' => $markdown->toHtml($text)]);

                    return;
                }

                $consolidatedResponse = '';
                $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

                $llm->chat($prompt, true, function ($chunk) use (&$consolidatedResponse) {
                    $consolidatedResponse .= $chunk;

                    sendStream($chunk);
                });

                //Log::info("consolidatedResponse: $consolidatedResponse");
                $latestMessage->update(['body' => $markdown->toHtml($consolidatedResponse)]);

            } catch (Exception $e) {
                Log::error(__CLASS__ . ': ' . $e->getMessage());
                $error = '<span class="text-red-600">Oops! Failed to get a response, please try again.' . ' ' . $e->getMessage() . '</span>';

                sendStream($error);

                //$latestMessage->delete();
                $latestMessage->update(['body' => $error]);
            } finally {
                sendStream("", true);
            }

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    #[Title('Chat Buddy')]
    public function render(): Application|View|Factory
    {
        if (session()->has('addBotMessage')) {
            $this->dispatch('getChatBuddyAiResponse', session('addBotMessage'));
        }

        if (session()->has('conversationsDeleted')) {
            $this->success('Conversations deleted successfully.');
        }

        return view('livewire.pages.chat-buddy');
    }

    protected function chatWithDocs(Conversation $conversation): StreamedResponse
    {
        return response()->stream(function () use ($conversation) {

            $text = '';
            $prompt = $conversation->bot->prompt;
            $userQuery = $conversation->messages()->where('is_ai', false)->latest()->first();

            $latestMessage = $conversation
                ->messages()
                ->where('body', '=', Constants::CHATBUDDY_LOADING_STRING)
                ->latest()
                ->first();

            $files = $conversation->bot->files();

            if (!$files) {
                sendStream("No files found!");
                sendStream("", true);
                return;
            }

            $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);
            $parser = new Parser();

            foreach ($files as $file) {
                if (str_contains($file, '.pdf')) {
                    $pdf = $parser->parseFile($file);
                    $text .= $pdf->getText();
                } else {
                    $text .= file_get_contents($file);
                }
            }

            // split text such that each split is not greater than 10000 byes (google limit)
            //$textSplits = str_split($text, 1000);
            $textSplits = $this->splitTextIntoChunks($text, 1000);

            $textEmbeddings = $llm->embed($textSplits, 'embedding-001');
            $queryEmbeddings = $llm->embed([$userQuery->body], 'embedding-001');

            // loops throuogh all the inputs and compare on a cosine similarity to the question and output the correct answer
            $results = [];
            for ($i = 0; $i < count($textEmbeddings['embeddings']); $i++) {
                $similarity = $this->cosineSimilarity($textEmbeddings['embeddings'][$i]['values'], $queryEmbeddings['embeddings'][0]['values']);

                $results[] = [
                    'similarity' => $similarity,
                    'index' => $i,
                    'text' => $textSplits[$i],
                ];
            }

            usort($results, function ($a, $b) {
                // orginal example was like below but in case opposte works (https://www.guywarner.dev/using-openai-to-create-a-qa-in-laravelphp-with-embedding)
                //return $a['similarity'] <=> $b['similarity'];
                return $b['similarity'] <=> $a['similarity'];
            });

            // Top 3 results
            $topResults = array_slice($results, 0, 3);

            $output = '';
            foreach ($topResults as $result) {
                $output .= $result['text'] . "\n";
                sendStream($result['text']);
            }

            $latestMessage->update(['body' => $output]);
            sendStream('', true);

        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    function splitTextIntoChunks($text, $chunkSize = 500): array
    {
        $chunks = [];
        $words = explode(" ", $text);
        $currentChunk = "";

        foreach ($words as $word) {
            if (strlen($currentChunk . " " . $word) <= $chunkSize) {
                $currentChunk .= " " . $word;
            } else {
                $chunks[] = trim($currentChunk);
                $currentChunk = $word;
            }
        }

        // Add the last chunk
        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    protected function cosineSimilarity($u, $v): float|int
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
}
