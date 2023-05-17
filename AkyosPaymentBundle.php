<?php

namespace Akyos\PaymentBundle;

use Akyos\PaymentBundle\DependencyInjection\PaymentBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AkyosPaymentBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new PaymentBundleExtension();
        }
        return $this->extension;
    }
}
