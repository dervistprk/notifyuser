<?php

namespace App\Services;

use App\Repositories\MessageRepository;
use Illuminate\Support\Facades\Cache;

class MessageService
{
    public function __construct(protected MessageRepository $repository) {}

    public function processMessages(): void
    {
        $messages = $this->repository->getPendingMessages();

        foreach ($messages as $message) {
            try {
                if (strlen($message->message_content) > 160) {
                    echo "Mesaj ID $message->id karakter sınırını aştı.\n";
                    continue;
                }

                $messageId = uniqid('msg_');
                $sentAt    = now();

                Cache::put("message:{$message->id}", [
                    'message_id' => $messageId,
                    'sent_at'    => $sentAt,
                ], now()->addHours(2));

                $this->repository->markAsSent($message);
                echo "Mesaj ID $message->id başarıyla gönderildi.\n";
            } catch (\Exception $e) {
                echo "Mesaj ID $message->id gönderilemedi: {$e->getMessage()}\n";
            }
        }
    }
}
