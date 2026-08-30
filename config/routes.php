<?php

use Slim\Routing\RouteCollectorProxy;
use App\Middleware\JwtMiddleware;

return function (\Slim\App $app) {

    // --- Base & Health Check ---
    $app->get('/', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'DarkBooks API Server is running',
            'version' => '1.0.0'
        ], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/api/v1[/]', function ($request, $response) {
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'DarkBooks API v1 is active',
            'version' => '1.0.0',
            'endpoints' => [
                'login' => 'POST /api/v1/auth/login',
                'register' => 'POST /api/v1/auth/register',
                'ventas' => 'GET /api/v1/ventas',
                'tickets' => 'GET /api/v1/tickets',
                'facturas' => 'GET /api/v1/facturas',
                'insumos' => 'GET /api/v1/insumos',
                'reportes' => 'GET /api/v1/reportes/resumen-diario'
            ]
        ], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // --- Auth (Rutas Públicas) ---
    $app->post('/api/v1/auth/login',    \App\Action\Auth\LoginAction::class);
    $app->post('/api/v1/auth/register', \App\Action\Auth\RegisterAction::class);

    // --- Rutas Protegidas ---
    $app->group('/api/v1', function (RouteCollectorProxy $group) {

        // Ventas
        $group->get('/ventas',          \App\Action\Venta\ListVentasAction::class);
        $group->post('/ventas',         \App\Action\Venta\CreateVentaAction::class);

        // Tickets de Compra
        $group->get('/tickets',         \App\Action\Ticket\ListTicketsAction::class);
        $group->post('/tickets',        \App\Action\Ticket\CreateTicketAction::class);

        // Facturas
        $group->get('/facturas',        \App\Action\Factura\ListFacturasAction::class);
        $group->post('/facturas',       \App\Action\Factura\CreateFacturaAction::class);

        // Inventario — Insumos
        $group->get('/insumos',              \App\Action\Inventario\ListInsumosAction::class);
        $group->post('/insumos',             \App\Action\Inventario\CreateInsumoAction::class);
        $group->post('/insumos/{id}/movimientos', \App\Action\Inventario\RegistrarMovimientoAction::class);
        $group->get('/inventario/alertas',   \App\Action\Inventario\AlertasStockAction::class);

        // Uploads
        $group->post('/uploads/{tipo}',            \App\Action\Upload\UploadFileAction::class);
        $group->get('/uploads/{tipo}/{filename}',  \App\Action\Upload\GetFileAction::class);

        // Reportes & Analytics
        $group->get('/reportes/ganancias',      \App\Action\Reporte\GananciasAction::class);
        $group->get('/reportes/resumen-diario',  \App\Action\Reporte\ResumenDiarioAction::class);
        $group->get('/reportes/calendario',      \App\Action\Reporte\CalendarioAction::class);

        // Herramientas
        $group->post('/herramientas/calculadora-precio', \App\Action\Herramientas\CalculadoraPrecioAction::class);

    })->add(JwtMiddleware::class);
};
