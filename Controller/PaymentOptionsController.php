<?php

namespace Akyos\PaymentBundle\Controller;

use Akyos\PaymentBundle\Entity\PaymentOptions;
use Akyos\PaymentBundle\Form\Type\PaymentOptionsType;
use Akyos\PaymentBundle\Repository\PaymentOptionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/payment_bundle/options", name="payment_options")
 * @isGranted("options-du-bundle-de-paiement")
 */
class PaymentOptionsController extends AbstractController
{
	/**
	 * @Route("/", name="", methods={"GET", "POST"})
	 * @param PaymentOptionsRepository $paymentOptionsRepository
	 * @param Request $request
	 * @return Response
	 */
	public function index(PaymentOptionsRepository $paymentOptionsRepository, Request $request): Response
	{
		$paymentOptions = $paymentOptionsRepository->findAll();
		if (!$paymentOptions) {
			$paymentOptions = new PaymentOptions();
		} else {
			$paymentOptions = $paymentOptions[0];
		}
		
		$form = $this->createForm(PaymentOptionsType::class, $paymentOptions);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($paymentOptions);
			$entityManager->flush();
			
			return $this->redirectToRoute('payment_options');
		}
		
		return $this->render('@AkyosPayment/payment_options/new.html.twig', [
			'payment_option' => $paymentOptions,
			'form' => $form->createView(),
		]);
	}
}
