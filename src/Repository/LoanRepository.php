<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Loan>
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    public function countLoans(): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countActiveLoans(): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.status = :status')
            ->setParameter('status', 'ACTIVE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countClosedLoans(): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.status = :status')
            ->setParameter('status', 'CLOSED')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countOverdueLoans(): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.status = :status')
            ->setParameter('status', 'OVERDUE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalLoansIssuedForMonth(int $month, ?int $year = null): int
    {
        $year ??= (int) date('Y');

        $start = new \DateTimeImmutable("$year-$month-01 00:00:00");
        $end = $start->modify('last day of this month')->setTime(23, 59, 59);

        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.created_at BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countLoansCreatedToday(): int
    {
        $start = new \DateTimeImmutable('today 00:00:00');
        $end = new \DateTimeImmutable('today 23:59:59');

        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.created_at BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumLoanPrincipal(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.amount)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumActiveLoanPrincipal(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.amount)')
            ->where('l.status = :status')
            ->setParameter('status', 'ACTIVE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumBalanceRemaining(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.balance_remaining)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumInterestAmount(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.interest_amount)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumInterestCollected(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.interest_paid_this_month)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumTotalPayback(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.total_payback)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function averageLoanAmount(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('AVG(l.amount)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function averageInterestPercent(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('AVG(l.interest_percent)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function averageLoanTermMonths(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('AVG(l.loan_term_months)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function averageBalanceRemaining(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('AVG(l.balance_remaining)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findLargestLoan(): ?Loan
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.amount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findSmallestLoan(): ?Loan
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.amount', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLatestLoan(): ?Loan
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOldestLoan(): ?Loan
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.created_at', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLoansClosingSoon(int $days): array
    {
        $date = new \DateTimeImmutable("+$days days");

        return $this->createQueryBuilder('l')
            ->where('l.closing_at <= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    public function findOverdueLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.status = :status')
            ->setParameter('status', 'OVERDUE')
            ->getQuery()
            ->getResult();
    }

    public function findActiveLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.status = :status')
            ->setParameter('status', 'ACTIVE')
            ->getQuery()
            ->getResult();
    }

    public function findClosedLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.status = :status')
            ->setParameter('status', 'CLOSED')
            ->getQuery()
            ->getResult();
    }

    public function findLoansWithExtensions(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.extension = true')
            ->getQuery()
            ->getResult();
    }

    public function countLoansWithExtensions(): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.extension = true')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLoanStatusDistribution(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.status, COUNT(l.id) as total')
            ->groupBy('l.status')
            ->getQuery()
            ->getResult();
    }

    public function getLoanTermDistribution(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.loan_term_months, COUNT(l.id) as total')
            ->groupBy('l.loan_term_months')
            ->getQuery()
            ->getResult();
    }

    public function getInterestRateDistribution(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.interest_percent, COUNT(l.id) as total')
            ->groupBy('l.interest_percent')
            ->getQuery()
            ->getResult();
    }

    public function getTotalPortfolioValue(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.balance_remaining)')
            ->where('l.status = :status')
            ->setParameter('status', 'ACTIVE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getPortfolioAtRisk(): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.balance_remaining)')
            ->where('l.status = :status')
            ->setParameter('status', 'OVERDUE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countLoansByCustomer(int $customerId): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.customer_id = :customer')
            ->setParameter('customer', $customerId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countActiveLoansByCustomer(int $customerId): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.customer_id = :customer')
            ->andWhere('l.status = :status')
            ->setParameter('customer', $customerId)
            ->setParameter('status', 'ACTIVE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countClosedLoansByCustomer(int $customerId): int
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.customer_id = :customer')
            ->andWhere('l.status = :status')
            ->setParameter('customer', $customerId)
            ->setParameter('status', 'CLOSED')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumLoanAmountByCustomer(int $customerId): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.amount)')
            ->where('l.customer_id = :customer')
            ->setParameter('customer', $customerId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumBalanceByCustomer(int $customerId): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.balance_remaining)')
            ->where('l.customer_id = :customer')
            ->setParameter('customer', $customerId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumInterestByCustomer(int $customerId): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.interest_amount)')
            ->where('l.customer_id = :customer')
            ->setParameter('customer', $customerId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumCollectedByCustomer(int $customerId): float
    {
        return (float)$this->createQueryBuilder('l')
            ->select('SUM(l.total_payback)')
            ->where('l.customer_id = :customer')
            ->setParameter('customer', $customerId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalPrincipalCollected(): float
    {
        return (float) $this->createQueryBuilder('l')
            ->select('SUM(l.total_payback - l.interest_amount)') // principal = total_payback - interest
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalPrincipalIssued(): float
    {
        return (float) $this->createQueryBuilder('l')
            ->select('SUM(l.amount)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalInterestCollected(): float
    {
        return (float) $this->createQueryBuilder('l')
            ->select('SUM(l.interest_paid_this_month)') // adjust if you track differently
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getLoansIssuedByMonth(int $month, int $year): float
    {
        $start = new \DateTimeImmutable("first day of $year-$month 00:00:00");
        $end = new \DateTimeImmutable("last day of $year-$month 23:59:59");

        return (float) $this->createQueryBuilder('l')
            ->select('SUM(l.amount)')
            ->where('l.created_at BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getInterestPaidByMonth(int $month, int $year): float
    {
        $start = new \DateTimeImmutable("first day of $year-$month 00:00:00");
        $end = new \DateTimeImmutable("last day of $year-$month 23:59:59");

        return (float) $this->createQueryBuilder('l')
            ->select('SUM(l.interest_paid_this_month)')
            ->where('l.created_at BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countLoansCreatedThisMonth(): int
    {
        $start = new \DateTimeImmutable('first day of this month 00:00:00');
        $end = new \DateTimeImmutable('last day of this month 23:59:59');

        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.created_at BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

}