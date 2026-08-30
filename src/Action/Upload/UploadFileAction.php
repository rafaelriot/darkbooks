<?php

namespace App\Action\Upload;

use App\Domain\Upload\FileUploadService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UploadFileAction
{
    private FileUploadService $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $tipo = $args['tipo'] ?? 'tickets';
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles['file'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'No se ha adjuntado ningún archivo (campo file)'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $result = $this->uploadService->upload($uploadedFiles['file'], $tipo);
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result
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
