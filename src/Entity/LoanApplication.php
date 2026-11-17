<?php

namespace App\Entity;

use App\Repository\LoanApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanApplicationRepository::class)]
class LoanApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $requestedAmount = null;

    #[ORM\Column]
    private ?int $requestedTermMonths = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $offeredAmount = null;

    #[ORM\Column(nullable: true)]
    private ?int $offeredTermMonths = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $monthlyInstalment = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $interestAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $totalPayback = null;

    #[ORM\Column(length: 30)]
    private string $status = 'SUBMITTED';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adminNotes = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $customerResponseAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $offerExpiresAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'loanApplications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestedAmount(): ?string
    {
        return $this->requestedAmount;
    }

    public function setRequestedAmount(string $requestedAmount): static
    {
        $this->requestedAmount = $requestedAmount;

        return $this;
    }

    public function getRequestedTermMonths(): ?int
    {
        return $this->requestedTermMonths;
    }

    public function setRequestedTermMonths(int $requestedTermMonths): static
    {
        $this->requestedTermMonths = $requestedTermMonths;

        return $this;
    }

    public function getOfferedAmount(): ?string
    {
        return $this->offeredAmount;
    }

    public function setOfferedAmount(?string $offeredAmount): static
    {
        $this->offeredAmount = $offeredAmount;

        return $this;
    }

    public function getOfferedTermMonths(): ?int
    {
        return $this->offeredTermMonths;
    }

    public function setOfferedTermMonths(?int $offeredTermMonths): static
    {
        $this->offeredTermMonths = $offeredTermMonths;

        return $this;
    }

    public function getMonthlyInstalment(): ?string
    {
        return $this->monthlyInstalment;
    }

    public function setMonthlyInstalment(?string $monthlyInstalment): static
    {
        $this->monthlyInstalment = $monthlyInstalment;

        return $this;
    }

    public function getInterestAmount(): ?string
    {
        return $this->interestAmount;
    }

    public function setInterestAmount(?string $interestAmount): static
    {
        $this->interestAmount = $interestAmount;

        return $this;
    }

    public function getTotalPayback(): ?string
    {
        return $this->totalPayback;
    }

    public function setTotalPayback(?string $totalPayback): static
    {
        $this->totalPayback = $totalPayback;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAdminNotes(): ?string
    {
        return $this->adminNotes;
    }

    public function setAdminNotes(?string $adminNotes): static
    {
        $this->adminNotes = $adminNotes;

        return $this;
    }

    public function getCustomerResponseAt(): ?\DateTimeImmutable
    {
        return $this->customerResponseAt;
    }

    public function setCustomerResponseAt(?\DateTimeImmutable $customerResponseAt): static
    {
        $this->customerResponseAt = $customerResponseAt;

        return $this;
    }

    public function getOfferExpiresAt(): ?\DateTimeImmutable
    {
        return $this->offerExpiresAt;
    }

    public function setOfferExpiresAt(?\DateTimeImmutable $offerExpiresAt): static
    {
        $this->offerExpiresAt = $offerExpiresAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
