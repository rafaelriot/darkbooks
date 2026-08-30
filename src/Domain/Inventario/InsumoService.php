<?php

namespace App\Domain\Inventario;

use App\Infrastructure\Database;
use Exception;

class InsumoService
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function listInsumos(int $userId, ?string $categoria = null, ?bool $stockBajo = false): array
    {
        $sql = "SELECT * FROM insumos WHERE usuario_id = ? AND activo = 1";
        $params = [$userId];

        if ($categoria) {
            $sql .= " AND categoria = ?";
            $params[] = $categoria;
        }

        if ($stockBajo) {
            $sql .= " AND stock_actual <= stock_minimo";
        }

        $sql .= " ORDER BY nombre ASC";
        return $this->db->fetchAll($sql, $params);
    }

    public function createInsumo(int $userId, array $data): array
    {
        $nombre = trim($data['nombre'] ?? '');
        $unidad = $data['unidad_medida'] ?? 'kg';
        $stockActual = (float)($data['stock_actual'] ?? 0);
        $stockMinimo = (float)($data['stock_minimo'] ?? 0);
        $costoPromedio = (float)($data['costo_unitario_promedio'] ?? 0);
        $categoria = $data['categoria'] ?? 'proteinas';

        if (empty($nombre)) {
            throw new Exception("El nombre del insumo es obligatorio");
        }

        $id = $this->db->insert(
            "INSERT INTO insumos (usuario_id, nombre, unidad_medida, stock_actual, stock_minimo, costo_unitario_promedio, categoria) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$userId, $nombre, $unidad, $stockActual, $stockMinimo, $costoPromedio, $categoria]
        );

        return $this->db->fetchOne("SELECT * FROM insumos WHERE id = ?", [$id]);
    }

    public function registrarMovimiento(int $userId, int $insumoId, array $data): array
    {
        $insumo = $this->db->fetchOne("SELECT * FROM insumos WHERE id = ? AND usuario_id = ?", [$insumoId, $userId]);
        if (!$insumo) {
            throw new Exception("Insumo no encontrado");
        }

        $tipo = $data['tipo_movimiento'] ?? 'entrada';
        $cantidad = (float)($data['cantidad'] ?? 0);
        $costoUnitario = isset($data['costo_unitario']) ? (float)$data['costo_unitario'] : null;
        $ticketId = isset($data['ticket_compra_id']) ? (int)$data['ticket_compra_id'] : null;
        $notas = $data['notas'] ?? null;

        if ($cantidad <= 0) {
            throw new Exception("La cantidad debe ser mayor a cero");
        }

        $stockAnterior = (float)$insumo['stock_actual'];
        $costoAnterior = (float)$insumo['costo_unitario_promedio'];
        $nuevoStock = $stockAnterior;
        $nuevoCostoPromedio = $costoAnterior;

        if ($tipo === 'entrada' || $tipo === 'ajuste_positivo') {
            $nuevoStock = $stockAnterior + $cantidad;
            if ($tipo === 'entrada' && $costoUnitario !== null && $costoUnitario > 0) {
                // Cálculo de costo promedio ponderado
                $totalCostoAnterior = $stockAnterior * $costoAnterior;
                $totalCostoNuevo = $cantidad * $costoUnitario;
                $nuevoCostoPromedio = ($nuevoStock > 0) ? ($totalCostoAnterior + $totalCostoNuevo) / $nuevoStock : $costoUnitario;
            }
        } elseif ($tipo === 'salida' || $tipo === 'merma' || $tipo === 'ajuste_negativo') {
            $nuevoStock = max(0, $stockAnterior - $cantidad);
        }

        // Actualizar insumo
        $this->db->execute(
            "UPDATE insumos SET stock_actual = ?, costo_unitario_promedio = ? WHERE id = ?",
            [$nuevoStock, $nuevoCostoPromedio, $insumoId]
        );

        // Registrar movimiento
        $movId = $this->db->insert(
            "INSERT INTO movimientos_inventario (insumo_id, usuario_id, tipo_movimiento, cantidad, costo_unitario, stock_resultante, ticket_compra_id, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$insumoId, $userId, $tipo, $cantidad, $costoUnitario, $nuevoStock, $ticketId, $notas]
        );

        return [
            'movimiento_id' => (int)$movId,
            'insumo_id' => $insumoId,
            'insumo_nombre' => $insumo['nombre'],
            'tipo_movimiento' => $tipo,
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_resultante' => $nuevoStock,
            'costo_promedio_nuevo' => round($nuevoCostoPromedio, 2)
        ];
    }

    public function getAlertasStockBajo(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM v_alertas_stock_bajo WHERE usuario_id = ?",
            [$userId]
        );
    }
}
