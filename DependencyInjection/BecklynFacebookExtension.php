<?php

namespace Becklyn\FacebookBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Loads and manages the bundle configuration
 */
class BecklynFacebookExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if ($config["add_p3p_headers"])
        {
            $container
                ->register('kernel.listener.becklyn_facebook', 'Becklyn\FacebookBundle\Listener\IeIframeCookieListener')
                ->addTag('kernel.event_listener', array('event' => 'kernel.response', 'method' => 'onKernelResponse'));
        }
    }
}