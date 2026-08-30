<?php

namespace App\Action\Reporte;

use App\Domain\Reporte\ReporteService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GananciasAction
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
        $fechaInicio = $params['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $params['fecha_fin'] ?? date('Y-m-t');

        $data = $this->reporteService->getGanancias($userId, $fechaInicio, $fechaFin);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
