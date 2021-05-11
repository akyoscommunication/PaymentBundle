<?php

namespace Akyos\PaymentBundle\Twig;

use Akyos\FormBundle\Controller\ContactFormFieldController;
use Akyos\PaymentBundle\Service\PaymentService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PaymentsExtension extends AbstractExtension
{
	private PaymentService $paymentService;
	
	public function __construct(PaymentService $paymentService)
	{
		$this->paymentService = $paymentService;
	}
	
	public function getFunctions(): array
	{
		return [
			new TwigFunction('getLastPayment', [$this->paymentService, 'getLastPayment']),
		];
	}
}