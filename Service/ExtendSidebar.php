<?php

namespace Akyos\PaymentBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class   ExtendSidebar
{
	public function __construct(
		private readonly UrlGeneratorInterface $router,
		private readonly Security $security,
	) {}

	public function getTemplate($route)
	{
		$template = '';
		return new Response($template);
	}
	
	public function getOptionsTemplate($route)
	{
		$template = '';
		if ($this->security->isGranted('options-du-bundle-de-paiement')) {
			$template = '<li class="' . (str_contains((string) $route, "payment_options") ? "active" : "") . '"><a href="' . $this->router->generate('payment_options') . '">Gestion des paiements</a></li>';
		}
		return new Response($template);
	}
}