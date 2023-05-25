<?php

namespace Akyos\PaymentBundle\Twig;

use Akyos\FormBundle\Controller\ContactFormFieldController;
use Akyos\PaymentBundle\Service\PaymentService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PaymentsExtension extends AbstractExtension
{
	public function __construct(private readonly PaymentService $paymentService)
 {
 }
	
	public function getFunctions(): array
	{
		return [
			new TwigFunction('getLastPayment', $this->paymentService->getLastPayment(...)),
		];
	}
}