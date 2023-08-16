<?php

namespace Akyos\PaymentBundle\Controller;

use Akyos\PaymentBundle\Entity\Transaction;
use Akyos\PaymentBundle\Repository\PaymentOptionsRepository;
use Akyos\PaymentBundle\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/payment-bundle', name: 'akyos_payment_bundle_')]
class PaymentController extends AbstractController
{
	#[Route(path: '/success/{id}', name: 'success', methods: ['GET', 'POST'])]
	public function success(Transaction $transaction, Request $request, PaymentService $paymentService): Response
	{
		$service = $paymentService->getModuleService($transaction->getPaymentModule());
		if(!$service->success($transaction, $request)) {
			$this->addFlash('danger', 'Le paiement a bien été pris en compte par le module bancaire, mais l\'enregistrement du paiement sur ce site n\'a pas pu aboutir. Veuillez contacter l\‘équipe technique pour vérification.');
		}

		return $this->redirectToRoute($transaction->getCallbackRoute(), array_merge($transaction->getCallbackParams(), [
			'status' => 'success',
			'transaction' => $transaction->getId(),
		]));
	}

	#[Route(path: '/error/{id}', name: 'error', methods: ['GET', 'POST'])]
	public function error(Transaction $transaction, Request $request, PaymentService $paymentService): Response
	{
		$service = $paymentService->getModuleService($transaction->getPaymentModule());
		if(!$service->error($transaction, $request)) {
			$this->addFlash('danger', 'Une erreur est survenue lors du paiement, puis lors de l\'enregistrement de l\'erreur. Veuillez contacter l\'équipe technique pour vérification.');
		}

		return $this->redirectToRoute($transaction->getCallbackRoute(), array_merge($transaction->getCallbackParams(), [
			'status' => 'error',
			'transaction' => $transaction->getId(),
		]));
	}

	#[Route(path: '/redirectToCheckout/{id}', name: 'redirect_to_checkout', methods: ['GET'])]
	public function redirectToCheckout(string $id, PaymentOptionsRepository $paymentOptionsRepository): Response
	{
		$paymentOptions = $paymentOptionsRepository->findAll();
		if ($paymentOptions) {
			$paymentOptions = $paymentOptions[0];
		}

		$apiKey = $this->getParameter('stripe_test_public_key');
		if($paymentOptions && $paymentOptions->getActivateStripeLive() && $this->getParameter('kernel.environment') === "prod") {
			$apiKey = $this->getParameter('stripe_live_public_key');
		}

		return $this->render('@AkyosPayment/payment_options/redirectoToCheckout.html.twig', [
			'id' => $id,
			'api_key' => $apiKey,
		]);
	}
}
