<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Message;
use App\Jobs\SendPendingMessagesJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function receive(Request $request)
    {
        try {
            $validated = $request->validate([
                'to'              => 'required|string',
                'message_content' => 'required|string|max:160',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }

        $customer = Customer::where('gsm', $validated['to'])->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.',
            ], 404);
        }

        $message = Message::create([
            'customer_id'     => $customer->id,
            'message_content' => $validated['message_content'],
            'is_sent'         => false,
        ]);

        SendPendingMessagesJob::dispatch();
        Log::info("$message->id ID numaralı mesaj API üzerinden veritabanına eklendi ve müşteriye gönderimi sağlandı.");

        return response()->json([
            'message'    => 'Accepted',
            'message_id' => $message->id,
        ], 202);
    }

    public function test(Request $request)
    {
        if ($request->test) {
            return response()->json([
                'success' => true,
                'message' => 'test, connected!',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Please send test key as a parameter.',
        ], 301);
    }
}
