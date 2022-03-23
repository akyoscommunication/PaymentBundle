<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\PaymentBundle\Entity\Payment;
use Akyos\PaymentBundle\Entity\Transaction;
use Akyos\PaymentBundle\Repository\PaymentOptionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Charge;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
	private PaymentOptionsRepository $paymentOptionsRepository;
	private ParameterBagInterface $parameterBag;
	private UrlGeneratorInterface $urlGenerator;
	private EntityManagerInterface $entityManager;
	
	public function __construct(ParameterBagInterface $parameterBag, PaymentOptionsRepository $paymentOptionsRepository, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager)
	{
		$this->paymentOptionsRepository = $paymentOptionsRepository;
		$this->parameterBag = $parameterBag;
		$this->urlGenerator = $urlGenerator;
		$this->entityManager = $entityManager;
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
			'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
			'cancel_url' => $errorUrl.'?session_id={CHECKOUT_SESSION_ID}',
		]);
		
		return  $this->urlGenerator->generate('akyos_payment_bundle_redirect_to_checkout', ['id' => $checkout_session->id], UrlGeneratorInterface::ABSOLUTE_URL);
	}
	
	public function getRecurrentPaymentUrl(float $amount, $successUrl, $errorUrl): string
	{
		// TODO => Recurrent payments
		Stripe::setApiKey($this->getKey());
		return '';
	}
	
	public function getCharges(String $paymentIntentId): array
	{
		Stripe::setApiKey($this->getKey());
		$paymentIntent = PaymentIntent::retrieve($paymentIntentId);
		$charges = [];
        if($paymentIntent->charges->total_count === 0) {
            return [$paymentIntent];
        }
        if ($paymentIntent->charges->data) {
			foreach ($paymentIntent->charges->data as $data) {
				$charge = Charge::retrieve($data->id);
				if($charge) {
					$charges[] = $charge;
				}
			}
		}
		return $charges;
	}
	
	public function success(Transaction $transaction, Request $request): string
	{
		try {
			Stripe::setApiKey($this->getKey());
			$sessionId = $request->get('session_id');
			$checkoutSession = Session::retrieve($sessionId);
		
			$payment = new Payment();
			$payment
				->setTransaction($transaction)
				->setStatus(Payment::STATUS_PAID)
				->setToken($sessionId)
				->setLog($checkoutSession->toJSON())
			;
			
			$this->entityManager->persist($payment);
			$this->entityManager->flush();
			
			return true;
		} catch(\Exception $e) {
			return false;
		}
	}
	
	public function error(Transaction $transaction, Request $request): string
	{
		try {
			Stripe::setApiKey($this->getKey());
			$sessionId = $request->get('session_id');
			$checkoutSession = Session::retrieve($sessionId);
			
			$payment = new Payment();
			$payment
				->setTransaction($transaction)
				->setStatus(Payment::STATUS_CANCELLED)
				->setToken($sessionId)
				->setLog($checkoutSession->toJSON())
			;
			
			$this->entityManager->persist($payment);
			$this->entityManager->flush();
			
			return true;
		} catch(\Exception $e) {
			return false;
		}
	}
	
	private function getKey() {
		$paymentOptions = $this->paymentOptionsRepository->findAll();
		if ($paymentOptions) {
			$paymentOptions = $paymentOptions[0];
		}
		
		if($paymentOptions && $paymentOptions->getActivateStripeLive() && $this->parameterBag->get('kernel.environment') === "prod") {
			return $this->parameterBag->get('stripe_live_key');
		}
		
		return $this->parameterBag->get('stripe_live_key');
	}
}