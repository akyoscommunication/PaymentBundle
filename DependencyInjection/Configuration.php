<?php

namespace Akyos\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('payment_bundle');

        $treeBuilder
            ->getRootNode()
                ->children()
				->scalarNode('stripe_live_key')
					->defaultValue('sk_live_22')
				->end()
				->scalarNode('stripe_test_key')
					->defaultValue('sk_test_51Hw3vEG3bQquurNm3XDfRFSkHmFi7aWG7E46GTvWYiPfLhY8ao2qQLHJDU9mZmjzIz9RMXByiUhT1FClpLZ0SDcd00hTEN9p7V')
				->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
