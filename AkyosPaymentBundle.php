<?php

namespace Akyos\PaymentBundle;

use Akyos\PaymentBundle\DependencyInjection\PaymentBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AkyosPaymentBundle extends Bundle
{
    public function getContainerExtension(): PaymentBundleExtension
    {
        return new PaymentBundleExtension();
    }
}
