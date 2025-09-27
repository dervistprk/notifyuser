<?php

namespace App\Repositories;

use App\Models\Message;

class MessageRepository
{
    public function getPendingMessages(): \Illuminate\Database\Eloquent\Collection|array
    {
        return Message::where('is_sent', false)->with('customer')->get();
    }

    public function markAsSent(Message $message): void
    {
        $message->update(['is_sent' => true]);
    }
}
