<?php

namespace Akyos\PaymentBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class   ExtendSidebar
{
	private $router;
	private $security;

	public function __construct(UrlGeneratorInterface $router, Security $security)
	{
		$this->router = $router;
		$this->security = $security;
	}

	public function getTemplate($route)
	{
		$template = '';
		return new Response($template);
	}
	
	public function getOptionsTemplate($route)
	{
		$template = '';
		if ($this->security->isGranted('options-du-bundle-de-paiement')) {
			$template = '<li class="' . (strpos($route, "payment_options") !== false ? "active" : "") . '"><a href="' . $this->router->generate('payment_options') . '">Gestion des paiements</a></li>';
		}
		return new Response($template);
	}
}