<?php

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Configurar DI Container
$containerBuilder = new ContainerBuilder();
$settings = require __DIR__ . '/../config/settings.php';
$settings($containerBuilder);
$container = require __DIR__ . '/../config/container.php';
$container($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

// Cargar middlewares y rutas
$middleware = require __DIR__ . '/../config/middleware.php';
$middleware($app);

$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

$app->run();
