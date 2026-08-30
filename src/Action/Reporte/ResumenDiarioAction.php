<?php

namespace App\Action\Reporte;

use App\Domain\Reporte\ReporteService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ResumenDiarioAction
{
    private ReporteService $reporteService;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $params = $request->getQueryParams();
        $fecha = $params['fecha'] ?? date('Y-m-d');

        $data = $this->reporteService->getResumenDiario($userId, $fecha);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
