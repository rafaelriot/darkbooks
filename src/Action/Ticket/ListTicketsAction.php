<?php

namespace App\Action\Ticket;

use App\Domain\Ticket\TicketService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListTicketsAction
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $params = $request->getQueryParams();
        $fechaInicio = $params['fecha_inicio'] ?? null;
        $fechaFin = $params['fecha_fin'] ?? null;
        $categoria = $params['categoria'] ?? null;

        $tickets = $this->ticketService->listTickets($userId, $fechaInicio, $fechaFin, $categoria);

        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $tickets
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
