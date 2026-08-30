<?php

namespace App\Action\Herramientas;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CalculadoraPrecioAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];

        $costoInsumos = (float)($data['costo_insumos'] ?? 0);
        $margenDeseadoPct = (float)($data['margen_deseado_pct'] ?? 30); // ej 30%
        $comisionPlataformaPct = (float)($data['comision_plataforma_pct'] ?? 30); // ej 30%

        if ($costoInsumos <= 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'El costo de insumos debe ser mayor a 0'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Fórmula:
        // Precio Venta Bruto = Costo Insumos / (1 - (MargenPct/100) - (ComisionPct/100))
        $factor = 1 - ($margenDeseadoPct / 100) - ($comisionPlataformaPct / 100);
        if ($factor <= 0) {
            $factor = 0.1; // Evitar división por cero o números negativos extravagantes
        }

        $precioRecomendadoBruto = $costoInsumos / $factor;
        $comisionMonto = $precioRecomendadoBruto * ($comisionPlataformaPct / 100);
        $ingresoNetoPlataforma = $precioRecomendadoBruto - $comisionMonto;
        $gananciaNetaEst = $ingresoNetoPlataforma - $costoInsumos;

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'costo_insumos' => round($costoInsumos, 2),
                'margen_deseado_pct' => $margenDeseadoPct,
                'comision_plataforma_pct' => $comisionPlataformaPct,
                'precio_sugerido_menu' => round($precioRecomendadoBruto, 2),
                'comision_estimada' => round($comisionMonto, 2),
                'ingreso_neto_recibido' => round($ingresoNetoPlataforma, 2),
                'ganancia_neta_estimada' => round($gananciaNetaEst, 2)
            ]
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
