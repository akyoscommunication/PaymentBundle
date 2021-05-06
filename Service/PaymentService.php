<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\PaymentBundle\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentService
{
	private EntityManagerInterface $entityManager;
	private FlashBagInterface $flashBag;
	private UrlGeneratorInterface $urlGenerator;
	
	public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag, UrlGeneratorInterface $urlGenerator)
	{
		$this->entityManager = $entityManager;
		$this->flashBag = $flashBag;
		$this->urlGenerator = $urlGenerator;
	}
	
	public function createTransaction(string $module, string $transactionType, float $amount, string $callbackRoute, array $callbackParams, array $options = null)
	{
		try {
			$transaction = new Transaction();
			$transaction
				->setPaymentModule($module)
				->setTransactionType($transactionType)
				->setAmount($amount)
				->setCallbackRoute($callbackRoute)
				->setCallbackParams($callbackParams)
			;
			
			$transaction->setPaymentUrl($this->getPaymentUrl($transaction));
			
			$this->entityManager->persist($transaction);
			$this->entityManager->flush();
			
			return $transaction;
		} catch( \Exception $e) {
			$this->flashBag->add('danger', 'Une erreur est survenue lors de la préparation du paiement en ligne, veuillez réessayer. Si le problème persiste, contactez l\'équipe technique.');
			return false;
		}
	}
	
	public function getPaymentUrl(Transaction $transaction): string
	{
		$successUrl = $this->urlGenerator->generate('akyos_payment_bundle_success', ['id' => $transaction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
		$errorUrl = $this->urlGenerator->generate('akyos_payment_bundle_error', ['id' => $transaction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
		
		$moduleServiceName = $transaction->getPaymentModule().'Service';
		if(class_exists($moduleServiceName)) {
			if($transaction->getTransactionType() === Transaction::TRANSACTION_TYPE_UNIQUE) {
				$url = $moduleServiceName::getUniquePaymentUrl($transaction->getAmount(), $successUrl, $errorUrl);
			} elseif($transaction->getTransactionType() === Transaction::TRANSACTION_TYPE_RECURRENT) {
				$url = $moduleServiceName::getRecurrentPaymentUrl($transaction->getAmount(), $successUrl, $errorUrl);
			} else {
				throw new \Exception('Transaction type '.$transaction->getPaymentModule().' doesn\'t exists');
			}
			return $url;
		}
		
		throw new \Exception('Module '.$transaction->getPaymentModule().' doesn\'t exists');
	}
}