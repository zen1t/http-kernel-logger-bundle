<?php

namespace Vesax\HttpKernelLoggerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class VesaxHttpKernelLoggerExtension extends Extension
{
    private static $types = [
        'response' => 'Vesax\HttpKernelLoggerBundle\EventListener\ResponseListener',
        'request' => 'Vesax\HttpKernelLoggerBundle\EventListener\RequestListener',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['zones'] as $rule => $zoneOptions) {
            $type = self::$types[$zoneOptions['type']];
            $zoneLoggerDefinition = new Definition($type, [
                new Reference('logger'),
                new Reference('vesax.http_kernel_logger.formatter'),
                '|'.$rule.'|',
            ]);

            $zoneLoggerDefinition->addTag('kernel.event_subscriber');
            $zoneLoggerDefinition->addTag('monolog.logger',
                ['channel' => $zoneOptions['channel']]);

            $container->setDefinition('vesax.http_kernel_logger.loggers.'.md5($rule),
                $zoneLoggerDefinition);
        }

        $loader = new Loader\YamlFileLoader($container,
            new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
