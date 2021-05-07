<?php

namespace Akyos\PaymentBundle\Controller;

use Akyos\PaymentBundle\Entity\Payment;
use Akyos\PaymentBundle\Entity\Transaction;
use Akyos\PaymentBundle\Service\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment-bundle", name="akyos_payment_bundle_")
 */
class PaymentController extends AbstractController
{
	/**
	 * @Route("/success/{id}", name="success", methods={"GET","POST"})
	 * @param Transaction $transaction
	 * @param Request $request
	 * @param EntityManagerInterface $entityManager
	 * @param PaymentService $paymentService
	 * @return Response
	 * @throws \Exception
	 */
	public function success(Transaction $transaction, Request $request, EntityManagerInterface $entityManager, PaymentService $paymentService): Response
	{
		try {
			// TODO => create Payment with bank datas
			$payment = new Payment();
			$payment
				->setStatus(Payment::STATUS_PAID)
				->setTransaction($transaction)
			;
			$transaction->setPaymentUrl($paymentService->getPaymentUrl($transaction));
			$entityManager->persist($payment);
			$entityManager->flush();
		} catch(\Exception $e) {
			$this->addFlash('danger', 'Le paiement a bien été pris en compte par le module bancaire, mais l\'enregistrement du paiement sur ce site n\'a pas pu aboutir. Veuillez contacter l\‘équipe technique pour vérification.');
		}
		
		return $this->redirectToRoute($transaction->getCallbackRoute(), array_merge($transaction->getCallbackParams(), [
			'status' => 'success',
		]));
	}
	
	/**
	 * @Route("/error/{id}", name="error", methods={"GET","POST"})
	 * @param Transaction $transaction
	 * @param Request $request
	 * @param EntityManagerInterface $entityManager
	 * @param PaymentService $paymentService
	 * @return Response
	 */
	public function error(Transaction $transaction, Request $request, EntityManagerInterface $entityManager, PaymentService $paymentService): Response
	{
		try {
			// TODO => log error with bank datas + renew payment link for next try
			$payment = new Payment();
			$payment
				->setStatus(Payment::STATUS_CANCELLED)
				->setTransaction($transaction)
			;
			$transaction->setPaymentUrl($paymentService->getPaymentUrl($transaction));
			$entityManager->persist($payment);
			$entityManager->flush();
		} catch(\Exception $e) {
			$this->addFlash('danger', 'Une erreur est survenue lors du paiement, puis lors de l\'enregistrement de l\'erreur. Veuillez contacter l\'équipe technique pour vérification.');
		}
		
		return $this->redirectToRoute($transaction->getCallbackRoute(), array_merge($transaction->getCallbackParams(), [
			'status' => 'error',
		]));
	}
	
	/**
	 * @Route("/redirectToCheckout/{id}", name="redirect_to_checkout", methods={"GET"})
	 * @param string $id
	 * @return Response
	 */
	public function redirectToCheckout(string $id): Response
	{
		return $this->render('@AkyosPayment/payment_options/redirectoToCheckout.html.twig', [
			'id' => $id,
			'api_key' => $this->getParameter('stripe_test_public_key')
		]);
	}
}