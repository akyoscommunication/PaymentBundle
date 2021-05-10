<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\PaymentBundle\Entity\Transaction;
use Akyos\PaymentBundle\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentService
{
	private EntityManagerInterface $entityManager;
	private FlashBagInterface $flashBag;
	private UrlGeneratorInterface $urlGenerator;
	private ContainerInterface $container;
	private PaymentRepository $paymentRepository;
	
	public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag, UrlGeneratorInterface $urlGenerator, ContainerInterface $container, PaymentRepository $paymentRepository)
	{
		$this->entityManager = $entityManager;
		$this->flashBag = $flashBag;
		$this->urlGenerator = $urlGenerator;
		$this->container = $container;
		$this->paymentRepository = $paymentRepository;
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
		} catch( \Exception $e) {
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