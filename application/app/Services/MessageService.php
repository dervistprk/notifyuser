<?php

namespace App\Services;

use App\Repositories\MessageRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MessageService
{
    public function __construct(protected MessageRepository $repository) {}

    public function processMessages(): void
    {
        $messages = $this->repository->getPendingMessages();

        foreach ($messages as $message) {
            if (strlen($message->message_content) > 160) {
                Log::info("Mesaj ID {$message->id} karakter sınırını aştı.");
                echo "Mesaj ID $message->id karakter sınırını aştı.\n";
                continue;
            }

            $messageId = uniqid('msg_');
            $sentAt    = now();

            Cache::put("message:$message->id", [
                'message_id' => $messageId,
                'sent_at'    => $sentAt,
            ], now()->addHours(1));

            $this->repository->markAsSent($message);
        }
    }
}
