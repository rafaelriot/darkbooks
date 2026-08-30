<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class JwtMiddleware implements MiddlewareInterface
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings['jwt'];
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Token de autorización no proporcionado o inválido'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $jwtToken = $matches[1];

        try {
            $decoded = JWT::decode($jwtToken, new Key($this->settings['secret'], $this->settings['algorithm']));
            $request = $request->withAttribute('user_id', $decoded->sub)
                               ->withAttribute('user_email', $decoded->email ?? '')
                               ->withAttribute('user_rol', $decoded->rol ?? 'admin');
        } catch (\Exception $e) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Token expirado o inválido: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }
}
