<?php

namespace TODOListApi\Application;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use TODOListApi\Domain\DateTimeNormalizer;
use TODOListApi\Domain\TodolistManager;

class Configurator
{
    /**
     * @param Application $app
     * @param array $configuration
     */
    public static function configureApp(Application $app, array $configuration)
    {
        self::registerServiceProviders($app, $configuration);
        self::registerServices($app, $configuration);
    }

    /**
     * @param Application $app
     * @param array $configuration
     */
    private static function registerServiceProviders(Application $app, array $configuration)
    {
        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => $configuration['db']['options'],
        ));
        $app->register(new DoctrineOrmServiceProvider());

        $app['orm.proxies_dir'] = $configuration['cache_path'] . '/doctrine/proxies';
        $app['orm.default_cache'] = 'array';
        $app['orm.em.options'] = array(
            'mappings' => array(
                array(
                    'type' => 'simple_yml',
                    'namespace' => 'TODOListApi\\Domain',
                    'path' => $configuration['doctrine_orm_path'],
                ),
            ),
        );

        $app->register(new ServiceControllerServiceProvider());
    }

    /**
     * @param Application $app
     * @param array $configuration
     */
    private static function registerServices(Application $app, array $configuration)
    {
        $app['app.todolist.manager'] = function ($app) use ($configuration) {
            return new TodolistManager(
                $app['orm.em'],
                $configuration['reporters']
            );
        };

        $app['app.todolist.serializer'] = function ($app) {
            $getSetMethodNormalizer = new GetSetMethodNormalizer();
            $getSetMethodNormalizer->setIgnoredAttributes(['availableStatuses']);

            $serializer = new Serializer(
                [new DateTimeNormalizer(), new CustomNormalizer(), $getSetMethodNormalizer],
                [new JsonEncoder(), new XmlEncoder()]
            );

            return $serializer;
        };
    }
}
