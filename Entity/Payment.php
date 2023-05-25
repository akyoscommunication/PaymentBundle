<?php

namespace Akyos\PaymentBundle\Entity;

use Akyos\PaymentBundle\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
	use TimestampableEntity;
	
	final public const STATUS_PAID = 'Payé';
	final public const STATUS_CANCELLED = 'Annulé';
	
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: Transaction::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?\Akyos\PaymentBundle\Entity\Transaction $transaction = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $token = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $log = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }

    public function setLog(?string $log): self
    {
        $this->log = $log;

        return $this;
    }
}
