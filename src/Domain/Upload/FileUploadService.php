<?php

namespace App\Domain\Upload;

use Psr\Http\Message\UploadedFileInterface;
use Exception;

class FileUploadService
{
    private string $storagePath;

    public function __construct(array $settings)
    {
        $this->storagePath = $settings['storage']['path'];
    }

    public function upload(UploadedFileInterface $file, string $tipo): array
    {
        if (!in_array($tipo, ['tickets', 'facturas'])) {
            throw new Exception("Tipo de upload no permitido. Use 'tickets' o 'facturas'");
        }

        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir el archivo");
        }

        // Límite 5MB
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new Exception("El archivo excede el tamaño máximo permitido de 5MB");
        }

        $targetDir = $this->storagePath . '/' . $tipo;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $extension = strtolower($extension ?: 'jpg');
        
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            throw new Exception("Formato de imagen no soportado. Use JPG, PNG o WEBP");
        }

        $filename = sprintf('%s_%s_%s.%s', substr($tipo, 0, 3), time(), bin2hex(random_bytes(4)), $extension);
        $filepath = $targetDir . '/' . $filename;

        $file->moveTo($filepath);

        $relativeUrl = sprintf('/api/v1/uploads/%s/%s', $tipo, $filename);

        return [
            'filename' => $filename,
            'tipo' => $tipo,
            'url' => $relativeUrl,
            'size_kb' => round(filesize($filepath) / 1024, 2)
        ];
    }

    public function getFilePath(string $tipo, string $filename): ?string
    {
        $path = $this->storagePath . '/' . $tipo . '/' . basename($filename);
        return file_exists($path) ? $path : null;
    }
}
