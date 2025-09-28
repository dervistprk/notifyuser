<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MessageService;
use App\Repositories\MessageRepository;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Illuminate\Database\Eloquent\Collection;

class MessageServiceTest extends TestCase
{
    /**
     * Tests the processMessages method to ensure that it marks pending messages as sent.
     * Bu test, MessageService içindeki processMessages() metodunun doğru şekilde çalıştığını ve mesajları işaretlediğini kontrol eder.
     * @return void
     */
    public function test_process_messages_marks_as_sent()
    {
        $message = new Message([
            'message_content' => 'Test mesaj içeriği',
            'is_sent'         => false,
        ]);

        $repository = Mockery::mock(MessageRepository::class);
        $repository->shouldReceive('getPendingMessages')->once()->andReturn(new Collection([$message]));
        $repository->shouldReceive('markAsSent')->once()->with($message);

        $service = new MessageService($repository);

        Cache::shouldReceive('put')->once();
        $service->processMessages();
    }
}
