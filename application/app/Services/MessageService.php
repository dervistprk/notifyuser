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
        $messages = $this->repository->getPendingMessages()->take(2);

        foreach ($messages as $message) {
            try {
                if (strlen($message->message_content) > 160) {
                    Log::info("Mesaj ID $message->id karakter sınırını aştı.");
                    continue;
                }

                $messageId = uniqid('msg_');
                $sentAt    = now();

                Cache::put("message:{$message->id}", [
                    'message_id' => $messageId,
                    'sent_at'    => $sentAt,
                ], now()->addHours(2));

                $this->repository->markAsSent($message);

                Log::info("Mesaj ID $message->id başarıyla gönderildi.");

                sleep(5);
            } catch (\Exception $e) {
                Log::error("Mesaj ID $message->id gönderilemedi: {$e->getMessage()}");
            }
        }
        Log::info("Kuyrukta işlenen mesajlar tamamlandı.");
    }
}
