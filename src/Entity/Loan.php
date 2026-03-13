<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

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

    #[ORM\OneToMany(mappedBy: 'loan_id', targetEntity: Payments::class)]
    private Collection $payments;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $closing_at = null;

    #[ORM\Column]
    private ?bool $extension = null;

    #[ORM\Column]
    private ?int $loan_term_months = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'ACTIVE';

    #[ORM\Column(nullable: true)]
    private ?float $interest_paid_this_month = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $last_rollover_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastRolloverAt(): ?\DateTimeImmutable
    {
        return $this->last_rollover_at;
    }

    public function setLastRolloverAt(\DateTimeImmutable $last_rollover_at): static
    {
        $this->last_rollover_at = $last_rollover_at;
        return $this;
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

    public function getInterestPaidThisMonth(): ?float
    {
        return $this->interest_paid_this_month;
    }

    public function setInterestPaidThisMonth(float $amount): static
    {
        $this->interest_paid_this_month = $amount;
        return $this;
    }

    /**
     * @return Collection<int, Payments>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payments $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setLoanId($this);
        }

        return $this;
    }

    public function removePayment(Payments $payment): static
    {
        $this->payments->removeElement($payment);
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getClosingAt(): ?\DateTimeImmutable
    {
        return $this->closing_at;
    }

    public function setClosingAt(\DateTimeImmutable $closing_at): static
    {
        $this->closing_at = $closing_at;

        return $this;
    }

    public function isExtension(): ?bool
    {
        return $this->extension;
    }

    public function setExtension(bool $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getLoanTermMonths(): ?int
    {
        return $this->loan_term_months;
    }

    public function setLoanTermMonths(int $loan_term_months): static
    {
        $this->loan_term_months = $loan_term_months;

        return $this;
    }

    public function calculateClosingDate(): void
    {
        if ($this->created_at && $this->loan_term_months) {
            $this->closing_at = $this->created_at->modify("+{$this->loan_term_months} months");
        }
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function updateStatus(): void
    {
        $today = new \DateTimeImmutable();

        if ($this->balance_remaining <= 0) {
            $this->status = 'CLOSED';
            return;
        }

        if ($this->closing_at && $this->closing_at < $today) {
            $this->status = 'OVERDUE';
            return;
        }

        $this->status = 'ACTIVE';
    }

    public function applyPayment(float $paidAmount): float
    {
        $refund = 0;

        // Track running total of all payments ever made
        $this->total_payback = ($this->total_payback ?? 0) + $paidAmount;

        // Only immediate action: full payoff closes the loan right away
        if ($this->balance_remaining !== null && $paidAmount >= $this->balance_remaining) {
            $refund = round($paidAmount - $this->balance_remaining, 2);
            $this->amount = 0;
            $this->balance_remaining = 0;
            $this->interest_amount = 0;
            $this->status = 'CLOSED';
        }

        $this->updated_at = new \DateTimeImmutable();
        return $refund;
    }

    public function rolloverMonth(): array
    {
        // Skip closed loans
        if ($this->status === 'CLOSED') {
            return ['skipped' => true];
        }

        // Prevent running twice in the same month
        if ($this->last_rollover_at !== null) {
            $sameMonth = $this->last_rollover_at->format('Y-m') === (new \DateTimeImmutable())->format('Y-m');
            if ($sameMonth) {
                return ['skipped' => true, 'reason' => 'already_rolled_this_month'];
            }
        }

        // Sum payments made since the last rollover (or since loan was created)
        $periodStart = $this->updated_at ?? $this->created_at;
        $totalPaidThisPeriod = 0.0;

        foreach ($this->payments as $payment) {
            if ($payment->getPaymentDate() >= $periodStart) {
                $totalPaidThisPeriod += $payment->getAmountPaid();
            }
        }

        $monthlyInterest = round($this->amount * ($this->interest_percent / 100), 2);
        $result = [
            'loan_id'        => $this->id,
            'principal'      => $this->amount,
            'interest'       => $monthlyInterest,
            'paid'           => $totalPaidThisPeriod,
            'refund'         => 0,
            'case'           => null,
        ];

        if ($totalPaidThisPeriod < $monthlyInterest) {
            // ── CASE 1: Paid less than interest ──────────────────────────────
            // Shortfall gets added to principal, recalculate everything
            $shortfall = round($monthlyInterest - $totalPaidThisPeriod, 2);
            $this->amount = round($this->amount + $shortfall, 2);
            $this->interest_amount = round($this->amount * ($this->interest_percent / 100), 2);
            $this->balance_remaining = round($this->amount + $this->interest_amount, 2);
            $this->extendLoan(30);
            $result['case'] = 'shortfall';
            $result['shortfall_added'] = $shortfall;

        } elseif ($totalPaidThisPeriod == $monthlyInterest) {
            // ── CASE 2: Paid exactly the interest ────────────────────────────
            // Principal untouched, just extend
            $this->interest_amount = $monthlyInterest;
            $this->balance_remaining = round($this->amount + $this->interest_amount, 2);
            $this->extendLoan(30);
            $result['case'] = 'interest_only';

        } else {
            // ── CASE 3: Paid more than interest ──────────────────────────────
            $extra = round($totalPaidThisPeriod - $monthlyInterest, 2);
            $newPrincipal = round($this->amount - $extra, 2);

            if ($newPrincipal <= 0) {
                // Loan fully cleared
                $refund = round(abs($newPrincipal), 2);
                $this->amount = 0;
                $this->balance_remaining = 0;
                $this->interest_amount = 0;
                $this->status = 'CLOSED';
                $result['case'] = 'closed';
                $result['refund'] = $refund;
            } else {
                // Reduced principal, new interest calculated, extend
                $this->amount = $newPrincipal;
                $this->interest_amount = round($newPrincipal * ($this->interest_percent / 100), 2);
                $this->balance_remaining = round($newPrincipal + $this->interest_amount, 2);
                $this->extendLoan(30);
                $result['case'] = 'partial_principal';
            }
        }

        $this->updated_at = new \DateTimeImmutable();
        $this->updateStatus();

        // At the very end, before return $result:
        $this->last_rollover_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();

        return $result;
    }

    private function extendLoan(int $days): void
    {
        $this->closing_at = $this->closing_at
            ? $this->closing_at->modify("+{$days} days")
            : (new \DateTimeImmutable())->modify("+{$days} days");
    }

}