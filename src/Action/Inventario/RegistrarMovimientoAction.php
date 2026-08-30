<?php

namespace App\Action\Inventario;

use App\Domain\Inventario\InsumoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegistrarMovimientoAction
{
    private InsumoService $insumoService;

    public function __construct(InsumoService $insumoService)
    {
        $this->insumoService = $insumoService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $insumoId = (int)($args['id'] ?? 0);
        $data = $request->getParsedBody() ?? [];

        try {
            $result = $this->insumoService->registrarMovimiento($userId, $insumoId, $data);
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}
