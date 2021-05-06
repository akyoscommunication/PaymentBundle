<?php

namespace Akyos\PaymentBundle\Entity;

use Akyos\PaymentBundle\Repository\PaymentOptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentOptionsRepository::class)
 */
class PaymentOptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activateStripeLive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivateStripeLive(): ?bool
    {
        return $this->activateStripeLive;
    }

    public function setActivateStripeLive(?bool $activateStripeLive): self
    {
        $this->activateStripeLive = $activateStripeLive;

        return $this;
    }
}
