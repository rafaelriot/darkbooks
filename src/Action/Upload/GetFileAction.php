<?php

namespace App\Action\Upload;

use App\Domain\Upload\FileUploadService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Stream;

class GetFileAction
{
    private FileUploadService $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $tipo = $args['tipo'] ?? 'tickets';
        $filename = $args['filename'] ?? '';

        $path = $this->uploadService->getFilePath($tipo, $filename);
        if (!$path) {
            $response->getBody()->write(json_encode(['error' => 'Archivo no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $mimeType = mime_content_type($path) ?: 'image/jpeg';
        $stream = new Stream(fopen($path, 'rb'));

        return $response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Content-Length', (string)filesize($path))
            ->withBody($stream);
    }
}
