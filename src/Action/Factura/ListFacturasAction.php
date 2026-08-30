<?php

namespace App\Action\Factura;

use App\Domain\Factura\FacturaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListFacturasAction
{
    private FacturaService $facturaService;

    public function __construct(FacturaService $facturaService)
    {
        $this->facturaService = $facturaService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $params = $request->getQueryParams();
        $fechaInicio = $params['fecha_inicio'] ?? null;
        $fechaFin = $params['fecha_fin'] ?? null;
        $tipo = $params['tipo'] ?? null;

        $facturas = $this->facturaService->listFacturas($userId, $fechaInicio, $fechaFin, $tipo);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $facturas
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
