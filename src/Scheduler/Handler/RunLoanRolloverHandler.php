<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\RunLoanRollover;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class RunLoanRolloverHandler
{
    public function __construct(
        private readonly LoanRepository $loanRepository,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(RunLoanRollover $message): void
    {
        $loans = $this->loanRepository->createQueryBuilder('l')
            ->where('l.status IN (:statuses)')
            ->setParameter('statuses', ['ACTIVE', 'OVERDUE'])
            ->getQuery()
            ->getResult();

        $this->logger->info(sprintf('[LoanRollover] Processing %d loans', count($loans)));

        foreach ($loans as $loan) {
            $result = $loan->rolloverMonth();

            if ($result['skipped'] ?? false) {
                continue;
            }

            $this->logger->info(sprintf(
                '[LoanRollover] Loan #%d | Case: %s | Paid: R%s | New Balance: R%s',
                $result['loan_id'],
                $result['case'],
                $result['paid'],
                $loan->getBalanceRemaining(),
            ));

            if ($result['refund'] > 0) {
                $this->logger->warning(sprintf(
                    '[LoanRollover] Loan #%d has REFUND due: R%s',
                    $result['loan_id'],
                    $result['refund']
                ));
            }
        }

        $this->em->flush();
        $this->logger->info('[LoanRollover] Rollover complete.');
    }
}