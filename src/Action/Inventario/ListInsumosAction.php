<?php

namespace App\Action\Inventario;

use App\Domain\Inventario\InsumoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListInsumosAction
{
    private InsumoService $insumoService;

    public function __construct(InsumoService $insumoService)
    {
        $this->insumoService = $insumoService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $params = $request->getQueryParams();
        $categoria = $params['categoria'] ?? null;
        $stockBajo = isset($params['stock_bajo']) && $params['stock_bajo'] == '1';

        $insumos = $this->insumoService->listInsumos($userId, $categoria, $stockBajo);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $insumos
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
