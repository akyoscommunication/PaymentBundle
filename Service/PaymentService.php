<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\PaymentBundle\Entity\Payment;
use Akyos\PaymentBundle\Entity\Transaction;
use Akyos\PaymentBundle\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentService
{
	private readonly EntityManagerInterface $entityManager;
	private readonly FlashBagInterface $flashBag;
	private readonly UrlGeneratorInterface $urlGenerator;
	private readonly ContainerInterface $container;
	
	public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag, UrlGeneratorInterface $urlGenerator, ContainerInterface $container, private readonly PaymentRepository $paymentRepository)
	{
		$this->entityManager = $entityManager;
		$this->flashBag = $flashBag;
		$this->urlGenerator = $urlGenerator;
		$this->container = $container;
	}
	
	public function createTransaction(string $module, string $transactionType, string $description, float $amount, string $callbackRoute, array $callbackParams, array $options = null)
	{
		try {
			$transaction = new Transaction();
			$transaction
				->setPaymentModule($module)
				->setTransactionType($transactionType)
				->setAmount($amount)
				->setDescription($description)
				->setCallbackRoute($callbackRoute)
				->setCallbackParams($callbackParams)
			;
			
			$this->entityManager->persist($transaction);
			$transaction->setPaymentUrl('');
			$this->entityManager->flush();
			$transaction->setPaymentUrl($this->getPaymentUrl($transaction));
			$this->entityManager->flush();
			
			return $transaction;
		} catch( \Exception) {
			$this->flashBag->add('danger', 'Une erreur est survenue lors de la préparation du paiement en ligne, veuillez réessayer. Si le problème persiste, contactez l\'équipe technique.');
			return false;
		}
	}
	
	public function getPaymentUrl(Transaction $transaction): string
	{
		$successUrl = $this->urlGenerator->generate('akyos_payment_bundle_success', ['id' => $transaction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
		$errorUrl = $this->urlGenerator->generate('akyos_payment_bundle_error', ['id' => $transaction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
		$service = $this->getModuleService($transaction->getPaymentModule());
		
		if($transaction->getTransactionType() === Transaction::TRANSACTION_TYPE_UNIQUE) {
			$url = $service->getUniquePaymentUrl($transaction, $successUrl, $errorUrl);
		} elseif($transaction->getTransactionType() === Transaction::TRANSACTION_TYPE_RECURRENT) {
			$url = $service->getRecurrentPaymentUrl($transaction, $successUrl, $errorUrl);
		} else {
			throw new \Exception('Transaction type '.$transaction->getPaymentModule().' doesn\'t exists');
		}
		
		return $url;
	}
	
	public function getChargeDetails(Payment $payment): array
	{
		$service = $this->getModuleService($payment->getTransaction()->getPaymentModule());
		$paymentIntentId = json_decode($payment->getLog(), true, 512, JSON_THROW_ON_ERROR)['payment_intent'];
		/** @var array $charges */
		$charges = $service->getCharges($paymentIntentId);
		return $charges;
	}
	
	public function getModuleService(string $module): ?object
	{
		$moduleServiceName = 'Akyos\\PaymentBundle\\Service\\'.$module.'Service';
		if(class_exists($moduleServiceName)) {
			return $this->container->get($moduleServiceName);
		}
		throw new \Exception('Module '.$module.' doesn\'t exists');
	}
	
	public function getLastPayment(Transaction $transaction)
	{
		$payments = $this->paymentRepository->findBy(['transaction' => $transaction], ['createdAt' => 'DESC']);
		
		if($payments) {
			return $payments[0];
		}
		
		return false;
	}
}