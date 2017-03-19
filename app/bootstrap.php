<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

/* Silex setup */

$paramFilepath = realpath(__DIR__.'/parameters.yml');
if (!file_exists($paramFilepath)) {
    throw new \RuntimeException('No parameters.yml');
}

$parameters = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($paramFilepath));

$configFilepath = realpath(__DIR__.'/config.yml');
$configAsAString = file_get_contents($configFilepath);

$configAsAString = \TODOListApi\Application\ParametersConfigHandler::replaceParametersInConfig(
    $parameters['parameters'],
    $configAsAString
);

$configuration = \Symfony\Component\Yaml\Yaml::parse($configAsAString);
$configuration['cache_path'] = realpath(__DIR__.'/cache/');
$configuration['doctrine_orm_path'] = realpath(__DIR__.'/../resources/config/doctrine/');

\TODOListApi\Application\Configurator::configureApp($app, $configuration);

if (isset($parameters['parameters']['environment']) && ('dev' === $parameters['parameters']['environment'])) {
    $app['debug'] = true;
}

return $app;