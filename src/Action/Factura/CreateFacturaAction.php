<?php

namespace App\Action\Factura;

use App\Domain\Factura\FacturaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateFacturaAction
{
    private FacturaService $facturaService;

    public function __construct(FacturaService $facturaService)
    {
        $this->facturaService = $facturaService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $data = $request->getParsedBody() ?? [];

        if (empty($data['total']) && empty($data['subtotal'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'El total de la factura es requerido'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $factura = $this->facturaService->createFactura($userId, $data);
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $factura
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
