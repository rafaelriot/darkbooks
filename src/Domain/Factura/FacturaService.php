<?php

namespace App\Domain\Factura;

use App\Infrastructure\Database;

class FacturaService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function listFacturas(int $userId, ?string $fechaInicio = null, ?string $fechaFin = null, ?string $tipo = null): array
    {
        $sql = "SELECT * FROM facturas WHERE usuario_id = ?";
        $params = [$userId];

        if ($fechaInicio) {
            $sql .= " AND fecha >= ?";
            $params[] = $fechaInicio;
        }
        if ($fechaFin) {
            $sql .= " AND fecha <= ?";
            $params[] = $fechaFin;
        }
        if ($tipo) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
        }

        $sql .= " ORDER BY fecha DESC, id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function createFactura(int $userId, array $data): array
    {
        $fecha = $data['fecha'] ?? date('Y-m-d');
        $tipo = $data['tipo'] ?? 'egreso';
        $rfcEmisor = $data['rfc_emisor'] ?? null;
        $razonSocial = $data['razon_social_emisor'] ?? null;
        $uuid = $data['uuid_cfdi'] ?? null;
        $folio = $data['folio'] ?? null;
        $subtotal = (float)($data['subtotal'] ?? $data['total'] ?? 0);
        $iva = (float)($data['iva'] ?? 0);
        $total = (float)($data['total'] ?? ($subtotal + $iva));
        $categoria = $data['categoria'] ?? 'insumos';
        $concepto = $data['concepto'] ?? null;
        $fotoUrl = $data['foto_url'] ?? null;

        $id = $this->db->insert(
            "INSERT INTO facturas (usuario_id, fecha, tipo, rfc_emisor, razon_social_emisor, uuid_cfdi, folio, subtotal, iva, total, categoria, concepto, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$userId, $fecha, $tipo, $rfcEmisor, $razonSocial, $uuid, $folio, $subtotal, $iva, $total, $categoria, $concepto, $fotoUrl]
        );

        return $this->db->fetchOne("SELECT * FROM facturas WHERE id = ?", [$id]);
    }
}
