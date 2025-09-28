<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="NotifyUser API",
 *     version="1.0.0",
 *     description="Mesaj gönderim sistemi için API dokümantasyonu",
 *     @OA\Contact(
 *         email="dervis@admin.com"
 *     )
 * )
 */

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
