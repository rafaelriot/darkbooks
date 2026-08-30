<?php

namespace App\Domain\Venta;

use App\Infrastructure\Database;

class VentaService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function listVentas(int $userId, ?string $fechaInicio = null, ?string $fechaFin = null, ?string $plataforma = null): array
    {
        $sql = "SELECT id, usuario_id, fecha, plataforma, monto_bruto, comision_plataforma, monto_neto, iva_retenido, isr_retenido, referencia_pedido, notas, created_at FROM ventas WHERE usuario_id = ?";
        $params = [$userId];

        if ($fechaInicio) {
            $sql .= " AND fecha >= ?";
            $params[] = $fechaInicio;
        }
        if ($fechaFin) {
            $sql .= " AND fecha <= ?";
            $params[] = $fechaFin;
        }
        if ($plataforma) {
            $sql .= " AND plataforma = ?";
            $params[] = $plataforma;
        }

        $sql .= " ORDER BY fecha DESC, id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function createVenta(int $userId, array $data): array
    {
        $fecha = $data['fecha'] ?? date('Y-m-d');
        $plataforma = $data['plataforma'] ?? 'uber_eats';
        $montoBruto = (float)($data['monto_bruto'] ?? 0);
        $comision = (float)($data['comision_plataforma'] ?? 0);
        $ivaRet = (float)($data['iva_retenido'] ?? 0);
        $isrRet = (float)($data['isr_retenido'] ?? 0);
        $ref = $data['referencia_pedido'] ?? null;
        $notas = $data['notas'] ?? null;

        $id = $this->db->insert(
            "INSERT INTO ventas (usuario_id, fecha, plataforma, monto_bruto, comision_plataforma, iva_retenido, isr_retenido, referencia_pedido, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$userId, $fecha, $plataforma, $montoBruto, $comision, $ivaRet, $isrRet, $ref, $notas]
        );

        return $this->db->fetchOne("SELECT * FROM ventas WHERE id = ?", [$id]);
    }
}
