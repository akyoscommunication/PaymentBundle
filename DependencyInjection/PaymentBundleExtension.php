<?php

namespace Akyos\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PaymentBundleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $container->setParameter('stripe_live_key', $config['stripe_live_key']);
        $container->setParameter('stripe_live_public_key', $config['stripe_live_public_key']);
        $container->setParameter('stripe_test_key', $config['stripe_test_key']);
        $container->setParameter('stripe_test_public_key', $config['stripe_test_public_key']);
    }
}