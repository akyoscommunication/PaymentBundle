<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\PaymentBundle\Repository\PaymentOptionsRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StripeService
{
	private PaymentOptionsRepository $paymentOptionsRepository;
	private ParameterBagInterface $parameterBag;
	
	public function __construct(ParameterBagInterface $parameterBag, PaymentOptionsRepository $paymentOptionsRepository)
	{
		$this->paymentOptionsRepository = $paymentOptionsRepository;
		$this->parameterBag = $parameterBag;
	}
	
	public function getUniquePaymentUrl(float $amount, $successUrl, $errorUrl): string
	{
		$key = $this->getKey();
		return '';
	}
	
	public function getRecurrentPaymentUrl(float $amount, $successUrl, $errorUrl): string
	{
		$key = $this->getKey();
		return '';
	}
	
	private function getKey() {
		$paymentOptions = $this->paymentOptionsRepository->findAll();
		if ($paymentOptions) {
			$paymentOptions = $paymentOptions[0];
		}
		
		if($paymentOptions && $paymentOptions->getActivateStripeLive() && $this->parameterBag->get('kernel.environment') === "prod") {
			return $this->parameterBag->get('stripe_live_key');
		}
		
		return $this->parameterBag->get('stripe_test_key');
	}
}