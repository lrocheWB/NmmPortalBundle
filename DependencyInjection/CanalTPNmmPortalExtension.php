<?php

namespace CanalTP\NmmPortalBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CanalTPNmmPortalExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter(
            'config.navitia',
            $config['navitia']
        );
        $container->setParameter(
            'config.navitia.url',
            $config['navitia']['url']
        );
        $container->setParameter(
            'nmm.navitia.token',
            $config['navitia']['token']
        );
        $container->setParameter(
            'nmm.tyr.url',
            $config['tyr']['url']
        );

        $loader->load('services.yml');
    }
}
