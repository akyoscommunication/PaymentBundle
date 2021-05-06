<?php

namespace Akyos\PaymentBundle\Service;

use Akyos\CoreBundle\Entity\AdminAccess;
use Akyos\CoreBundle\Repository\AdminAccessRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ExtendAdminAccess
{
	private AdminAccessRepository $adminAccessRepository;
	private EntityManagerInterface $entityManager;

	public function __construct(AdminAccessRepository $adminAccessRepository, EntityManagerInterface $entityManager)
	{
		$this->adminAccessRepository = $adminAccessRepository;
		$this->entityManager = $entityManager;
	}

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