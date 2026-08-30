<?php

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use App\Infrastructure\Database;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get('settings')['db'];
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $settings['host'],
                $settings['port'],
                $settings['dbname'],
                $settings['charset']
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            return new PDO($dsn, $settings['user'], $settings['pass'], $options);
        },
        Database::class => function (ContainerInterface $c) {
            return new Database($c->get(PDO::class));
        }
    ]);
};
