<?php

namespace App\Action\Ticket;

use App\Domain\Ticket\TicketService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateTicketAction
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $userId = (int)$request->getAttribute('user_id');
        $data = $request->getParsedBody() ?? [];

        if (empty($data['total']) && empty($data['subtotal'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'El total del ticket es requerido'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $ticket = $this->ticketService->createTicket($userId, $data);
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $ticket
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}
