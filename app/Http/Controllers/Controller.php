<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @OA\Info(
 *    title="Swagger with Laravel APIs",
 *    description="Sample APIs for praticepractice mock. | Postman - https://documenter.getpostman.com/view/2666389/2sA2r6YQVS",
 *    version="1.0.0",
 *    @OA\Contact(
 *        email="yhvreddydev@gmail.com"
 *    ),
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="sanctum"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
