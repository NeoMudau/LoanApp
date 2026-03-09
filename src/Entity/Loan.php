<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer_id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?float $interest_percent = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: true)]
    private ?float $interest_amount = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: true)]
    private ?float $total_payback = null;

    #[ORM\Column]
    #[ORM\JoinColumn(nullable: true)]
    private ?float $balance_remaining = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToOne(mappedBy: 'loan_id', cascade: ['persist', 'remove'])]
    private ?Payments $payments = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?Customer
    {
        return $this->customer_id;
    }

    public function setCustomerId(?Customer $customer_id): static
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getInterestPercent(): ?float
    {
        return $this->interest_percent;
    }

    public function setInterestPercent(float $interest_percent): static
    {
        $this->interest_percent = $interest_percent;

        return $this;
    }

    public function getInterestAmount(): ?float
    {
        return $this->interest_amount;
    }

    public function setInterestAmount(float $interest_amount): static
    {
        $this->interest_amount = $interest_amount;

        return $this;
    }

    public function getTotalPayback(): ?float
    {
        return $this->total_payback;
    }

    public function setTotalPayback(float $total_payback): static
    {
        $this->total_payback = $total_payback;

        return $this;
    }

    public function getBalanceRemaining(): ?float
    {
        return $this->balance_remaining;
    }

    public function setBalanceRemaining(float $balance_remaining): static
    {
        $this->balance_remaining = $balance_remaining;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPayments(): ?Payments
    {
        return $this->payments;
    }

    public function setPayments(Payments $payments): static
    {
        // set the owning side of the relation if necessary
        if ($payments->getLoanId() !== $this) {
            $payments->setLoanId($this);
        }

        $this->payments = $payments;

        return $this;
    }

    public function __toString(): string
    {
        if ($this->id === null) {
            return 'No loan';
        }

        $customerName = $this->customer_id ? $this->customer_id->getName() : 'Unknown Customer';

        return 'Customer: ' . $customerName . ' | Loan #' . $this->id . ' | Amount: R' . $this->amount . ' Intrest Amount : R' . $this->interest_amount;
    }

}
