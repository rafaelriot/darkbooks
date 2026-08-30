<?php

namespace App\Domain\Reporte;

use App\Infrastructure\Database;

class ReporteService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getGanancias(int $userId, string $fechaInicio, string $fechaFin): array
    {
        $sql = "SELECT 
                    SUM(total_ventas_brutas) as total_ventas_brutas,
                    SUM(total_comisiones) as total_comisiones,
                    SUM(total_ventas_netas) as total_ventas_netas,
                    SUM(total_iva_retenido) as total_iva_retenido,
                    SUM(total_isr_retenido) as total_isr_retenido,
                    SUM(total_gastos) as total_gastos,
                    SUM(ganancia_dia) as ganancia_neta
                FROM v_ganancias_periodo
                WHERE usuario_id = ? AND fecha >= ? AND fecha <= ?";

        $resumen = $this->db->fetchOne($sql, [$userId, $fechaInicio, $fechaFin]);

        // Desglose por día
        $dias = $this->db->fetchAll(
            "SELECT fecha, total_ventas_netas as ventas_netas, total_gastos as gastos, ganancia_dia as ganancia FROM v_ganancias_periodo WHERE usuario_id = ? AND fecha >= ? AND fecha <= ? ORDER BY fecha ASC",
            [$userId, $fechaInicio, $fechaFin]
        );

        // Desglose por plataforma
        $plataformas = $this->db->fetchAll(
            "SELECT plataforma, SUM(monto_bruto) as ventas_brutas, SUM(comision_plataforma) as comisiones, SUM(monto_neto) as ventas_netas, COUNT(id) as pedidos FROM ventas WHERE usuario_id = ? AND fecha >= ? AND fecha <= ? GROUP BY plataforma",
            [$userId, $fechaInicio, $fechaFin]
        );

        return [
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
            'resumen' => [
                'total_ventas_brutas' => (float)($resumen['total_ventas_brutas'] ?? 0),
                'total_comisiones_plataforma' => (float)($resumen['total_comisiones'] ?? 0),
                'total_ventas_netas' => (float)($resumen['total_ventas_netas'] ?? 0),
                'total_iva_retenido' => (float)($resumen['total_iva_retenido'] ?? 0),
                'total_isr_retenido' => (float)($resumen['total_isr_retenido'] ?? 0),
                'total_gastos' => (float)($resumen['total_gastos'] ?? 0),
                'ganancia_bruta' => (float)($resumen['ganancia_neta'] ?? 0),
                'margen_porcentaje' => ($resumen['total_ventas_brutas'] ?? 0) > 0 
                    ? round((($resumen['ganancia_neta'] ?? 0) / $resumen['total_ventas_brutas']) * 100, 2)
                    : 0.0
            ],
            'desglose_por_plataforma' => $plataformas,
            'desglose_por_dia' => $dias
        ];
    }

    public function getResumenDiario(int $userId, string $fecha): array
    {
        $resumen = $this->db->fetchOne(
            "SELECT * FROM v_ganancias_periodo WHERE usuario_id = ? AND fecha = ?",
            [$userId, $fecha]
        );

        if (!$resumen) {
            return [
                'usuario_id' => $userId,
                'fecha' => $fecha,
                'total_ventas_brutas' => 0.0,
                'total_comisiones' => 0.0,
                'total_ventas_netas' => 0.0,
                'total_gastos' => 0.0,
                'ganancia_dia' => 0.0
            ];
        }

        return $resumen;
    }

    public function getCalendario(int $userId, int $year, int $month): array
    {
        $fechaInicio = sprintf('%04d-%02d-01', $year, $month);
        $fechaFin = date('Y-m-t', strtotime($fechaInicio));

        $dias = $this->db->fetchAll(
            "SELECT fecha, total_ventas_netas as ventas, total_gastos as gastos, ganancia_dia as ganancia FROM v_ganancias_periodo WHERE usuario_id = ? AND fecha >= ? AND fecha <= ?",
            [$userId, $fechaInicio, $fechaFin]
        );

        // Mapear con semáforo de color
        $resultado = [];
        foreach ($dias as $d) {
            $ganancia = (float)$d['ganancia'];
            $color = 'rojo'; // por defecto
            if ($ganancia > 1000) {
                $color = 'verde';
            } elseif ($ganancia > 0) {
                $color = 'amarillo';
            }

            $resultado[] = [
                'fecha' => $d['fecha'],
                'ventas' => (float)$d['ventas'],
                'gastos' => (float)$d['gastos'],
                'ganancia' => $ganancia,
                'estado' => $color
            ];
        }

        return $resultado;
    }
}
