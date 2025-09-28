<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Message;
use App\Jobs\SendPendingMessagesJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class MessageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/receive-message",
     *     summary="Yeni mesaj al ve kuyruğa gönder",
     *     tags={"Messages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"to","message_content"},
     *             @OA\Property(property="to", type="string", example="905551112233"),
     *             @OA\Property(property="message_content", type="string", example="Merhaba, bu bir test mesajıdır.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Mesaj kuyruğa alındı",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Accepted"),
     *             @OA\Property(property="message_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Müşteri bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Doğrulama hatası"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/test-api-connection",
     *     summary="API bağlantısını test et",
     *     tags={"Messages"},
         * @OA\Parameter(
         * name="test",
         * in="query",
         * required=false,
         * description="true gönderilirse bağlantı başarılı döner, false veya eksikse başarısız döner",
         * @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bağlantı başarılı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="test, connected!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=301,
     *         description="Parametre eksik",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Please send test key as a parameter.")
     *         )
     *     )
     * )
     */
    public function test(Request $request)
    {
        $test = filter_var($request->query('test'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($test === true) {
            return response()->json([
                'success' => true,
                'message' => 'test, connected!',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Please send test value true as a parameter.',
        ], 301);
    }

    /**
     * @OA\Get(
     *     path="/api/get-sent-messages",
     *     summary="Gönderilmiş mesajları listele",
     *     tags={"Messages"},
     *     @OA\Response(
     *         response=200,
     *         description="Gönderilmiş mesajlar başarıyla listelendi",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="message_content", type="string", example="Merhaba!"),
     *                 @OA\Property(property="sent_at", type="string", format="date-time", example="2025-09-28T12:34:56"),
     *                 @OA\Property(property="customer", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Ahmet Yılmaz"),
     *                     @OA\Property(property="gsm", type="string", example="905551112233")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getSentMessages()
    {
        $messages = Message::with('customer')->where('is_sent', true)->get();
        return response()->json($messages, 200);
    }
}
