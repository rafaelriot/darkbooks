<?php

namespace App\Domain\Ticket;

use App\Infrastructure\Database;

class TicketService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function listTickets(int $userId, ?string $fechaInicio = null, ?string $fechaFin = null, ?string $categoria = null): array
    {
        $sql = "SELECT * FROM tickets_compra WHERE usuario_id = ?";
        $params = [$userId];

        if ($fechaInicio) {
            $sql .= " AND fecha >= ?";
            $params[] = $fechaInicio;
        }
        if ($fechaFin) {
            $sql .= " AND fecha <= ?";
            $params[] = $fechaFin;
        }
        if ($categoria) {
            $sql .= " AND categoria = ?";
            $params[] = $categoria;
        }

        $sql .= " ORDER BY fecha DESC, id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function createTicket(int $userId, array $data): array
    {
        $fecha = $data['fecha'] ?? date('Y-m-d');
        $proveedor = $data['proveedor'] ?? 'Proveedor General';
        $numTicket = $data['numero_ticket'] ?? null;
        $subtotal = (float)($data['subtotal'] ?? $data['total'] ?? 0);
        $iva = (float)($data['iva'] ?? 0);
        $total = (float)($data['total'] ?? ($subtotal + $iva));
        $categoria = $data['categoria'] ?? 'insumos_alimentos';
        $desc = $data['descripcion'] ?? null;
        $fotoUrl = $data['foto_url'] ?? null;

        $id = $this->db->insert(
            "INSERT INTO tickets_compra (usuario_id, fecha, proveedor, numero_ticket, subtotal, iva, total, categoria, descripcion, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$userId, $fecha, $proveedor, $numTicket, $subtotal, $iva, $total, $categoria, $desc, $fotoUrl]
        );

        return $this->db->fetchOne("SELECT * FROM tickets_compra WHERE id = ?", [$id]);
    }
}
