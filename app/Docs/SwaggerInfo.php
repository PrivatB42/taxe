<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API PANACEE",
 *     version="1.0.0",
 *     description="Documentation de l'API du projet PANACEE BOUTIQUE",
 *     @OA\Contact(
 *         email="support@panacee.local"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8600",
 *     description="Serveur local"
 * )
 */
class SwaggerInfo {}
