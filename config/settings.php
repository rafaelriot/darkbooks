<?php

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true,
            'logError'            => true,
            'logErrorDetails'     => true,
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'dbname' => $_ENV['DB_NAME'] ?? 'dark_kitchen_contabilidad',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'pass' => $_ENV['DB_PASS'] ?? '',
                'charset' => 'utf8mb4'
            ],
            'jwt' => [
                'secret' => $_ENV['APP_SECRET'] ?? 'darkbooks_secret_jwt_key_default',
                'algorithm' => 'HS256',
                'expiration' => 86400 // 24 horas
            ],
            'storage' => [
                'path' => __DIR__ . '/../' . ($_ENV['STORAGE_PATH'] ?? 'storage/uploads')
            ]
        ]
    ]);
};
