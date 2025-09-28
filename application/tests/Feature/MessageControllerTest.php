<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendPendingMessagesJob;

class MessageControllerTest extends TestCase
{
    /**
     * Tests that the receive endpoint successfully creates a message in the database
     * and dispatches a job to process pending messages.
     * Bu test, API endpoint’inin doğru çalıştığını ve job’ın kuyruğa eklendiğini doğrular.
     * @return void
     */
    public function test_receive_endpoint_creates_message_and_dispatches_job()
    {
        Queue::fake();

        $customer = Customer::factory()->create([
            'gsm' => '+905555555555',
        ]);

        $response = $this->postJson('/api/receive-message', [
            'to'              => '+905555555555',
            'message_content' => 'Merhaba!',
        ]);

        $response->assertStatus(202);
        $this->assertDatabaseHas('messages', [
            'customer_id'     => $customer->id,
            'message_content' => 'Merhaba!',
            'is_sent'         => false,
        ]);

        Queue::assertPushed(SendPendingMessagesJob::class);
    }
}
