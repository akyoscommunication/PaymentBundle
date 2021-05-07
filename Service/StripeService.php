<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\PaymentBundle\Entity\Transaction;
use Akyos\PaymentBundle\Repository\PaymentOptionsRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StripeService
{
	private PaymentOptionsRepository $paymentOptionsRepository;
	private ParameterBagInterface $parameterBag;
	private HttpClientInterface $client;
	private UrlGeneratorInterface $urlGenerator;
	
	public function __construct(ParameterBagInterface $parameterBag, PaymentOptionsRepository $paymentOptionsRepository, HttpClientInterface $client, UrlGeneratorInterface $urlGenerator)
	{
		$this->paymentOptionsRepository = $paymentOptionsRepository;
		$this->parameterBag = $parameterBag;
		$this->client = $client;
		$this->urlGenerator = $urlGenerator;
	}
	
	public function getUniquePaymentUrl(Transaction $transaction, $successUrl, $errorUrl): string
	{
		Stripe::setApiKey($this->getKey());
		$checkout_session = Session::create([
			'payment_method_types' => ['card'],
			'line_items' => [[
				'price_data' => [
					'currency' => 'eur',
					'unit_amount' => $transaction->getAmount() * 100,
					'product_data' => [
						'name' => $transaction->getDescription(),
					],
				],
				'quantity' => 1,
			]],
			'mode' => 'payment',
			'success_url' => $successUrl,
			'cancel_url' => $errorUrl,
		]);
		
		return  $this->urlGenerator->generate('akyos_payment_bundle_redirect_to_checkout', ['id' => $checkout_session->id], UrlGeneratorInterface::ABSOLUTE_URL);
	}
	
	public function getRecurrentPaymentUrl(float $amount, $successUrl, $errorUrl): string
	{
		Stripe::setApiKey($this->getKey());
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