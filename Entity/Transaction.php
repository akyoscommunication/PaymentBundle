<?php

namespace Akyos\PaymentBundle\Entity;

use Akyos\PaymentBundle\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
	use TimestampableEntity;
	
	final public const TRANSACTION_MODULE_STRIPE = "Stripe";
	final public const TRANSACTION_MODULE_MONETICO = "Monetico";
	final public const TRANSACTION_MODULE_PAYPAL = "Paypal";
	
	final public const TRANSACTION_TYPE_UNIQUE = "Unique";
	final public const TRANSACTION_TYPE_RECURRENT = "Abonnement";
	
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $paymentModule = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $transactionType = null;

    #[ORM\Column(type: 'float')]
    private ?float $amount = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $callbackRoute = null;

    #[ORM\Column(type: 'array', nullable: true)]
    private ?array $callbackParams = [];

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $paymentUrl = null;

    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'transaction', orphanRemoval: true)]
    private $payments;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentModule(): ?string
    {
        return $this->paymentModule;
    }

    public function setPaymentModule(string $paymentModule): self
    {
        $this->paymentModule = $paymentModule;

        return $this;
    }

    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCallbackRoute(): ?string
    {
        return $this->callbackRoute;
    }

    public function setCallbackRoute(string $callbackRoute): self
    {
        $this->callbackRoute = $callbackRoute;

        return $this;
    }

    public function getCallbackParams(): ?array
    {
        return $this->callbackParams;
    }

    public function setCallbackParams(?array $callbackParams): self
    {
        $this->callbackParams = $callbackParams;

        return $this;
    }

    public function getPaymentUrl(): ?string
    {
        return $this->paymentUrl;
    }

    public function setPaymentUrl(string $paymentUrl): self
    {
        $this->paymentUrl = $paymentUrl;

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setTransaction($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getTransaction() === $this) {
                $payment->setTransaction(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
