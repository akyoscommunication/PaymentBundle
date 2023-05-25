<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\CmsBundle\Entity\AdminAccess;
use Akyos\CmsBundle\Repository\AdminAccessRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ExtendAdminAccess
{
	public function __construct(
		private readonly AdminAccessRepository $adminAccessRepository,
		private readonly EntityManagerInterface $entityManager,
	) {}

	public function setDefaults()
	{
		if (!$this->adminAccessRepository->findOneByName("Options du bundle de paiement")) {
			$adminAccess = new AdminAccess();
			$adminAccess
				->setName('Options du bundle de paiement')
				->setRoles([])
				->setIsLocked(true);
			$this->entityManager->persist($adminAccess);
			$this->entityManager->flush();
		}

		return new Response('true');

	}
}
