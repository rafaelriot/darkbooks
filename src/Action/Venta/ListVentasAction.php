<?php

namespace App\Action\Venta;

use App\Domain\Venta\VentaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListVentasAction
{
    private VentaService $ventaService;

    public function __construct(VentaService $ventaService)
    {
        $this->ventaService = $ventaService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $params = $request->getQueryParams();
        $fechaInicio = $params['fecha_inicio'] ?? null;
        $fechaFin = $params['fecha_fin'] ?? null;
        $plataforma = $params['plataforma'] ?? null;

        $ventas = $this->ventaService->listVentas($userId, $fechaInicio, $fechaFin, $plataforma);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $ventas
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
