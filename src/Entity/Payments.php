<?php

namespace App\Entity;

use App\Repository\PaymentsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LoanRepository;
use App\Repository\CustomerRepository;

#[ORM\Entity(repositoryClass: PaymentsRepository::class)]
class Payments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'payments', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Loan $loan_id = null;

    #[ORM\Column]
    private ?float $amount_paid = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $payment_date = null;

    public function __construct()
    {
        $this->payment_date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoanId(): ?Loan
    {
        return $this->loan_id;
    }

    public function setLoanId(Loan $loan_id): static
    {
        $this->loan_id = $loan_id;

        return $this;
    }

    public function getAmountPaid(): ?float
    {
        return $this->amount_paid;
    }

    public function setAmountPaid(float $amount_paid): static
    {
        $this->amount_paid = $amount_paid;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeImmutable $payment_date): static
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    public function __toString(): string
    {
        return 'Loan ID: ' . $this->loan_id . ' Amount paid : R' . $this->amount_paid;
    }

}
