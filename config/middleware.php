<?php

use Slim\App;
use App\Middleware\CorsMiddleware;

return function (App $app) {
    // Middleware global de CORS
    $app->add(CorsMiddleware::class);

    // Body parsing middleware
    $app->addBodyParsingMiddleware();

    // Routing middleware
    $app->addRoutingMiddleware();

    // Error middleware
    $settings = $app->getContainer()->get('settings');
    $errorMiddleware = $app->addErrorMiddleware(
        $settings['displayErrorDetails'],
        $settings['logError'],
        $settings['logErrorDetails']
    );

    // Custom Error Renderer JSON
    $errorHandler = $errorMiddleware->getDefaultErrorHandler();
    $errorHandler->forceContentType('application/json');
};
