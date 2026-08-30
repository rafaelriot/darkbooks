<?php

namespace App\Action\Inventario;

use App\Domain\Inventario\InsumoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlertasStockAction
{
    private InsumoService $insumoService;

    public function __construct(InsumoService $insumoService)
    {
        $this->insumoService = $insumoService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $alertas = $this->insumoService->getAlertasStockBajo($userId);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'total_alertas' => count($alertas),
                'insumos' => $alertas
            ]
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
